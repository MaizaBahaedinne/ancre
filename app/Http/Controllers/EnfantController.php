<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnfantRequest;
use App\Http\Requests\UpdateEnfantRequest;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;
use App\Models\Enfant;
use App\Models\EnfantEvaluation;
use App\Models\EnfantParentRelation;
use App\Models\Inscription;
use App\Models\Package;
use App\Models\ParentModel;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EnfantController extends Controller
{
    private const ALLERGIE_OPTIONS = [
        'Arachides',
        'Fruits a coque',
        'Lait',
        'Oeufs',
        'Gluten',
        'Soja',
        'Sesame',
        'Poisson',
        'Fruits de mer',
        'Fraises',
        'Chocolat',
        'Pollen',
        'Poussiere',
        'Acariens',
        'Poils d\'animaux',
        'Piqures d\'insectes',
        'Soleil',
        'Froid',
        'Medicaments',
        'Latex',
        'Produits cosmetiques',
        'Lessive',
    ];

    private const RELATION_OPTIONS = [
        'Pere' => 'Pere',
        'Mere' => 'Mere',
        'Grand pere' => 'Grand pere',
        'Grand mere' => 'Grand mere',
        'Frere' => 'Frere',
        'Soeur' => 'Soeur',
        'Tuteur' => 'Tuteur',
        'Autre' => 'Autre',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $search = request('search');
        $classe = request('classe');
        $parent = $this->currentParent();

        $baseQuery = Enfant::query()
            ->with(['parent.user', 'familyRelations.parent.user', 'schoolClass.school', 'schoolClass.academicYear'])
            ->when($this->isParentUser(), function ($query) use ($parent) {
                if (! $parent) {
                    return $query->whereRaw('1 = 0');
                }

                return $query->where(function ($childScope) use ($parent) {
                    $childScope->where('parent_id', $parent->id)
                        ->orWhereHas('familyRelations', function ($relationScope) use ($parent) {
                            $relationScope->where('parent_id', $parent->id);
                        });
                });
            })
            ->when($search, function ($query, $searchValue) {
                $query->where(function ($searchScope) use ($searchValue) {
                    $searchScope->where('nom', 'like', "%{$searchValue}%")
                        ->orWhere('prenom', 'like', "%{$searchValue}%");
                });
            })
            ->when($classe, fn ($query, $classeValue) => $query->where('classe', $classeValue));

        $statsQuery = clone $baseQuery;

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'with_parent_user' => (clone $statsQuery)
                ->where(function ($query) {
                    $query->whereHas('parent.user')
                        ->orWhereHas('familyRelations.parent.user');
                })
                ->count(),
            'without_parent_user' => (clone $statsQuery)
                ->where(function ($query) {
                    $query->whereDoesntHave('parent.user')
                        ->whereDoesntHave('familyRelations.parent.user');
                })
                ->count(),
            'with_allergie' => (clone $statsQuery)->where('has_allergie', true)->count(),
        ];

        $enfants = $baseQuery
            ->latest()
            ->get();

        $classes = SchoolClass::query()
            ->with(['school', 'academicYear'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('enfants.index', compact('enfants', 'search', 'classe', 'classes', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $parents = ParentModel::orderBy('nom')->orderBy('prenom')->get();
        $relationOptions = self::RELATION_OPTIONS;
        $allergieOptions = self::ALLERGIE_OPTIONS;
        $schoolClasses = $this->schoolClasses();
        $activeAcademicYear = AcademicYear::query()->where('is_active', true)->first();

        return view('enfants.create', compact('parents', 'relationOptions', 'allergieOptions', 'schoolClasses', 'activeAcademicYear'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEnfantRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['relations']);
        $data = $this->syncClassLabel($data);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('enfants', 'public');
        }

        $enfant = Enfant::create($data);
        $this->syncFamilyRelations($enfant, $request->input('relations', []));

        return redirect()
            ->route('enfants.index')
            ->with('success', 'Enfant ajoute avec succes.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Enfant $enfant): View
    {
        $this->ensureParentCanAccessChild($enfant);

        $enfant->load([
            'parent',
            'familyRelations.parent',
            'activityParticipations.activite',
            'inscriptions',
            'schoolClass',
            'evaluations.grades.subject',
        ]);

        $today = Carbon::today();
        $startMonth = $today->copy()->startOfMonth();
        $startSchool = $enfant->inscriptions->sortBy('date_inscription')->first()?->date_inscription;
        $absenceStart = $startSchool && $startSchool->greaterThan($startMonth) ? $startSchool->copy() : $startMonth;

        $presenceMonth = $enfant->presences()
            ->whereBetween('date', [$absenceStart->toDateString(), $today->toDateString()])
            ->count();

        $schoolDays = 0;
        $cursor = $absenceStart->copy();
        while ($cursor->lessThanOrEqualTo($today)) {
            if (! in_array($cursor->dayOfWeekIso, [6, 7], true)) {
                $schoolDays++;
            }
            $cursor->addDay();
        }

        $absenceMonth = max($schoolDays - $presenceMonth, 0);

        $paiementTotal = (float) $enfant->paiements()->sum('montant');
        $paiementCount = $enfant->paiements()->count();
        $paiementRetardCount = $enfant->paiements()->where('statut', 'En retard')->count();

        $activityParticipationCount = $enfant->activityParticipations()->where('statut', 'Present')->count();
        $activityAbsenceCount = $enfant->activityParticipations()->where('statut', 'Absent')->count();
        $recentActivityParticipations = $enfant->activityParticipations()
            ->with('activite')
            ->latest()
            ->take(8)
            ->get();

        $activeAcademicYear = AcademicYear::query()->active()->first();
        $currentYearInscription = $activeAcademicYear
            ? $enfant->inscriptions->firstWhere('annee_scolaire', $activeAcademicYear->label)
            : null;
        $availablePackages = Package::query()
            ->where('is_active', true)
            ->orderBy('nom')
            ->get();

        $currentLevel = $enfant->schoolClass?->level ?: $enfant->classe;
        $subjectCatalog = AcademicSubject::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($currentLevel) {
            $normalizedCurrentLevel = $this->normalizeLevelLabel($currentLevel);

            $filteredByLevel = $subjectCatalog->filter(
                fn (AcademicSubject $subject) => $this->normalizeLevelLabel($subject->level) === $normalizedCurrentLevel
            )->values();

            if ($filteredByLevel->isEmpty() && preg_match('/\d+/', $normalizedCurrentLevel, $matches)) {
                $targetYear = $matches[0];

                $filteredByLevel = $subjectCatalog->filter(
                    fn (AcademicSubject $subject) => preg_match('/\d+/', $this->normalizeLevelLabel($subject->level), $m) && ($m[0] ?? null) === $targetYear
                )->values();
            }

            $subjectCatalog = $filteredByLevel;
        }

        $activeYearEvaluations = collect();
        if ($activeAcademicYear) {
            $activeYearEvaluations = $enfant->evaluations
                ->where('academic_year_id', $activeAcademicYear->id)
                ->keyBy('trimester');
        }

        $trimesterStatuses = collect(EnfantEvaluation::TRIMESTER_OPTIONS)
            ->mapWithKeys(fn ($trimester) => [$trimester => $activeYearEvaluations->has($trimester)]);

        return view('enfants.show', compact(
            'enfant',
            'presenceMonth',
            'absenceMonth',
            'paiementTotal',
            'paiementCount',
            'paiementRetardCount',
            'activityParticipationCount',
            'activityAbsenceCount',
            'recentActivityParticipations',
            'activeAcademicYear',
            'currentYearInscription',
            'availablePackages',
            'currentLevel',
            'subjectCatalog',
            'activeYearEvaluations',
            'trimesterStatuses'
        ));
    }

    private function normalizeLevelLabel(?string $level): string
    {
        $value = Str::ascii((string) $level);
        $value = mb_strtolower($value, 'UTF-8');

        return preg_replace('/\s+/', ' ', trim($value)) ?: '';
    }

    public function storeCurrentYearInscription(Request $request, Enfant $enfant): RedirectResponse
    {
        $this->ensureParentCanAccessChild($enfant);

        $activeAcademicYear = AcademicYear::query()->active()->first();

        if (! $activeAcademicYear) {
            return back()->withErrors([
                'quick_inscription' => 'Aucune annee scolaire active n\'est definie.',
            ])->withInput();
        }

        $validated = $request->validate([
            'quick_inscription_modal' => ['nullable', 'string'],
            'package_id' => ['required', 'exists:packages,id'],
            'date_inscription' => ['required', 'date'],
            'type_garde' => ['required', 'in:Matin,Apres-midi,Journee complete'],
            'statut' => ['required', 'in:Active,Renouvelee,Suspendue,Annulee'],
        ]);

        $alreadyInscribed = Inscription::query()
            ->where('enfant_id', $enfant->id)
            ->where('annee_scolaire', $activeAcademicYear->label)
            ->exists();

        if ($alreadyInscribed) {
            return back()->withErrors([
                'quick_inscription' => 'Cet enfant est deja inscrit pour l\'annee scolaire en cours.',
            ])->withInput();
        }

        $package = Package::query()
            ->where('is_active', true)
            ->find($validated['package_id']);

        if (! $package) {
            return back()->withErrors([
                'package_id' => 'Le package selectionne est inactif.',
            ])->withInput();
        }

        $annualRegistrationFee = (float) ($activeAcademicYear->registration_fee ?? 0);
        $packageMonthlyTotal = (float) $package->total_mensuel;

        Inscription::create([
            'enfant_id' => $enfant->id,
            'package_id' => $package->id,
            'annee_scolaire' => $activeAcademicYear->label,
            'date_inscription' => $validated['date_inscription'],
            'type_garde' => $validated['type_garde'],
            'statut' => $validated['statut'],
            'annual_registration_fee' => $annualRegistrationFee,
            'package_monthly_total' => $packageMonthlyTotal,
            'total_amount' => $annualRegistrationFee + $packageMonthlyTotal,
        ]);

        return redirect()
            ->route('enfants.show', $enfant)
            ->with('success', 'Inscription creee pour l\'annee scolaire en cours.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Enfant $enfant): View
    {
        $this->ensureParentCanAccessChild($enfant);

        $enfant->load('familyRelations');
        $parents = ParentModel::orderBy('nom')->orderBy('prenom')->get();
        $relationOptions = self::RELATION_OPTIONS;
        $allergieOptions = self::ALLERGIE_OPTIONS;
        $schoolClasses = $this->schoolClasses();
        $activeAcademicYear = AcademicYear::query()->where('is_active', true)->first();
        $existingRelations = $enfant->familyRelations
            ->mapWithKeys(fn ($relation) => [$relation->relation => $relation->parent_id])
            ->toArray();

        return view('enfants.edit', compact('enfant', 'parents', 'relationOptions', 'existingRelations', 'allergieOptions', 'schoolClasses', 'activeAcademicYear'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEnfantRequest $request, Enfant $enfant): RedirectResponse
    {
        $this->ensureParentCanAccessChild($enfant);

        $data = $request->validated();
        unset($data['relations']);
        $data = $this->syncClassLabel($data);

        if ($request->hasFile('photo')) {
            if ($enfant->photo && Storage::disk('public')->exists($enfant->photo)) {
                Storage::disk('public')->delete($enfant->photo);
            }
            $data['photo'] = $request->file('photo')->store('enfants', 'public');
        }

        $enfant->update($data);
        $this->syncFamilyRelations($enfant, $request->input('relations', []));

        return redirect()
            ->route('enfants.index')
            ->with('success', 'Enfant mis a jour avec succes.');
    }

    public function uploadPhoto(Request $request, Enfant $enfant): RedirectResponse
    {
        $this->ensureParentCanAccessChild($enfant);

        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($enfant->photo && Storage::disk('public')->exists($enfant->photo)) {
            Storage::disk('public')->delete($enfant->photo);
        }

        $path = $request->file('photo')->store('enfants', 'public');
        $enfant->update(['photo' => $path]);

        return redirect()
            ->route('enfants.show', $enfant)
            ->with('success', 'Photo de l\'enfant mise a jour avec succes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enfant $enfant): RedirectResponse
    {
        $this->ensureParentCanAccessChild($enfant);

        if ($enfant->photo && Storage::disk('public')->exists($enfant->photo)) {
            Storage::disk('public')->delete($enfant->photo);
        }

        $enfant->delete();

        return redirect()
            ->route('enfants.index')
            ->with('success', 'Enfant supprime avec succes.');
    }

    private function syncFamilyRelations(Enfant $enfant, array $relations): void
    {
        EnfantParentRelation::query()->where('enfant_id', $enfant->id)->delete();

        $selectedParentIds = [];

        foreach (self::RELATION_OPTIONS as $key => $label) {
            $parentId = $relations[$key] ?? null;

            if (! $parentId) {
                continue;
            }

            if (in_array((int) $parentId, $selectedParentIds, true)) {
                continue;
            }

            $selectedParentIds[] = (int) $parentId;

            EnfantParentRelation::create([
                'enfant_id' => $enfant->id,
                'parent_id' => (int) $parentId,
                'relation' => $label,
            ]);
        }

        if ($enfant->parent_id && ! in_array((int) $enfant->parent_id, $selectedParentIds, true)) {
            EnfantParentRelation::create([
                'enfant_id' => $enfant->id,
                'parent_id' => (int) $enfant->parent_id,
                'relation' => 'Parent principal',
            ]);
        }
    }

    private function schoolClasses()
    {
        return SchoolClass::query()
            ->with(['school', 'academicYear'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function syncClassLabel(array $data): array
    {
        $schoolClass = ! empty($data['school_class_id']) ? SchoolClass::query()->with('school')->find($data['school_class_id']) : null;

        if ($schoolClass) {
            $data['classe'] = $schoolClass->name;
        }

        return $data;
    }

    private function isParentUser(): bool
    {
        return (bool) auth()->user()?->hasRole('Parent');
    }

    private function currentParent(): ?ParentModel
    {
        $userId = auth()->id();

        if (! $userId) {
            return null;
        }

        return ParentModel::query()
            ->where('user_id', $userId)
            ->first();
    }

    private function ensureParentCanAccessChild(Enfant $enfant): void
    {
        if (! $this->isParentUser()) {
            return;
        }

        $parent = $this->currentParent();

        abort_unless($parent, 403);

        $isOwner = (int) $enfant->parent_id === (int) $parent->id;
        $isRelated = $enfant->familyRelations()
            ->where('parent_id', $parent->id)
            ->exists();

        abort_unless($isOwner || $isRelated, 403);
    }
}
