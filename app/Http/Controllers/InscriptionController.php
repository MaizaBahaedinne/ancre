<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInscriptionRequest;
use App\Http\Requests\UpdateInscriptionRequest;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;
use App\Models\Enfant;
use App\Models\EnfantEvaluation;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Package;
use App\Models\ParentModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $activeAcademicYear = $this->activeAcademicYear();
        $parent = $this->currentParent();
        $parentChildIds = $this->isParentUser()
            ? ($parent?->enfants()->pluck('id')->all() ?? [])
            : [];
        $scope = request('scope', 'current');
        $isArchiveScope = $scope === 'archive';
        $enfantId = request('enfant_id');
        $annee = $isArchiveScope ? request('annee_scolaire') : $activeAcademicYear?->label;

        $inscriptionsQuery = Inscription::with(['enfant', 'package'])
            ->when($this->isParentUser(), function ($query) use ($parentChildIds) {
                if (empty($parentChildIds)) {
                    return $query->whereRaw('1 = 0');
                }

                return $query->whereIn('enfant_id', $parentChildIds);
            })
            ->when($enfantId, fn ($query, $value) => $query->where('enfant_id', $value))
            ->orderByDesc('date_inscription');

        if ($isArchiveScope) {
            if ($activeAcademicYear?->label) {
                $inscriptionsQuery->where('annee_scolaire', '!=', $activeAcademicYear->label);
            }

            $inscriptionsQuery->when($annee, fn ($query, $value) => $query->where('annee_scolaire', $value));
        } elseif ($activeAcademicYear?->label) {
            $inscriptionsQuery->where('annee_scolaire', $activeAcademicYear->label);
        }

        $inscriptions = $inscriptionsQuery->get();

        $enfants = Enfant::orderBy('nom')->orderBy('prenom')->get();
        $academicYears = AcademicYear::query()
            ->orderByDesc('start_date')
            ->get();

        return view('inscriptions.index', compact(
            'inscriptions',
            'enfants',
            'enfantId',
            'annee',
            'scope',
            'isArchiveScope',
            'activeAcademicYear',
            'academicYears'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $activeAcademicYear = $this->activeAcademicYear();
        $enfants = $this->availableChildrenForAcademicYear($activeAcademicYear?->label);
        $packages = $this->availablePackages();
        $annualRegistrationFee = (float) ($activeAcademicYear?->registration_fee ?? 0);

        return view('inscriptions.create', compact('enfants', 'packages', 'activeAcademicYear', 'annualRegistrationFee'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInscriptionRequest $request): RedirectResponse
    {
        $activeAcademicYear = $this->activeAcademicYear();

        if (! $activeAcademicYear) {
            return back()->withErrors([
                'annee_scolaire' => 'Aucune annee scolaire active n\'est definie.',
            ])->withInput();
        }

        $validated = $request->validated();

        if ($this->isParentUser()) {
            $canUseChild = $this->allowedChildrenQuery()
                ->whereKey($validated['enfant_id'])
                ->exists();

            abort_unless($canUseChild, 403);
        }

        $alreadyInscribed = Inscription::query()
            ->where('enfant_id', $validated['enfant_id'])
            ->where('annee_scolaire', $activeAcademicYear->label)
            ->exists();

        if ($alreadyInscribed) {
            return back()->withErrors([
                'enfant_id' => 'Cet enfant est deja inscrit pour l\'annee scolaire en cours.',
            ])->withInput();
        }

        $package = Package::query()->findOrFail($validated['package_id']);
        $annualRegistrationFee = (float) $activeAcademicYear->registration_fee;
        $packageMonthlyTotal = (float) $package->total_mensuel;

        Inscription::create([
            ...$validated,
            'annee_scolaire' => $activeAcademicYear->label,
            'annual_registration_fee' => $annualRegistrationFee,
            'package_monthly_total' => $packageMonthlyTotal,
            'total_amount' => $packageMonthlyTotal + $annualRegistrationFee,
        ]);

        return redirect()
            ->route('inscriptions.index')
            ->with('success', 'Inscription enregistree avec succes.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inscription $inscription): View
    {
        $this->ensureParentCanAccessInscription($inscription);

        $inscription->load([
            'enfant.parent',
            'enfant.schoolClass',
            'enfant.evaluations.grades.subject',
            'enfant.paiements' => fn ($query) => $query
                ->orderByDesc('annee')
                ->orderByDesc('mois')
                ->orderByDesc('date_paiement')
                ->orderByDesc('id'),
            'package',
        ]);

        $monthlyPaymentHistory = $this->monthlyPaymentHistory($inscription);
        $activeAcademicYearLabel = $this->activeAcademicYear()?->label;
        $paidMonthsCount = $monthlyPaymentHistory->where('status', 'Paye')->count();
        $lateMonthsCount = $monthlyPaymentHistory->whereIn('status', ['En retard', 'Non enregistre'])->count();
        $trackedMonthsCount = $monthlyPaymentHistory->count();
        $paidAmountTotal = (float) $monthlyPaymentHistory->sum('paid_amount');
        $expectedAmountTotal = (float) $monthlyPaymentHistory->sum('expected_total');

        $evaluationAcademicYear = $this->resolveAcademicYearByLabel($inscription->annee_scolaire);

        $currentLevel = $inscription->enfant?->schoolClass?->level ?: $inscription->enfant?->classe;
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
        if ($evaluationAcademicYear && $inscription->enfant) {
            $activeYearEvaluations = $inscription->enfant->evaluations
                ->where('academic_year_id', $evaluationAcademicYear->id)
                ->keyBy('trimester');
        }

        $trimesterStatuses = collect(EnfantEvaluation::TRIMESTER_OPTIONS)
            ->mapWithKeys(fn ($trimester) => [$trimester => $activeYearEvaluations->has($trimester)]);

        return view('inscriptions.show', compact(
            'inscription',
            'monthlyPaymentHistory',
            'activeAcademicYearLabel',
            'paidMonthsCount',
            'lateMonthsCount',
            'trackedMonthsCount',
            'paidAmountTotal',
            'expectedAmountTotal',
            'evaluationAcademicYear',
            'currentLevel',
            'subjectCatalog',
            'activeYearEvaluations',
            'trimesterStatuses'
        ));
    }

    public function storeQuickPayment(Request $request, Inscription $inscription): RedirectResponse
    {
        $this->ensureParentCanAccessInscription($inscription);

        $inscription->load([
            'enfant.paiements' => fn ($query) => $query
                ->orderByDesc('annee')
                ->orderByDesc('mois')
                ->orderByDesc('date_paiement')
                ->orderByDesc('id'),
        ]);

        $validated = $request->validate([
            'quick_payment_modal' => ['nullable', 'string'],
            'mois' => ['required', 'integer', 'between:1,12'],
            'annee' => ['required', 'integer', 'min:2000', 'max:2100'],
            'montant' => ['required', 'numeric', 'gt:0'],
            'date_paiement' => ['required', 'date'],
            'mode_paiement' => ['required', 'in:Especes,Carte,Virement,Cheque'],
            'commentaire' => ['nullable', 'string'],
        ]);

        $historyItem = $this->monthlyPaymentHistory($inscription)
            ->firstWhere('month_key', sprintf('%04d-%02d', (int) $validated['annee'], (int) $validated['mois']));

        if (! $historyItem) {
            return back()
                ->withErrors(['montant' => 'Le mois selectionne ne fait pas partie de cet historique d\'inscription.'])
                ->withInput();
        }

        $remainingBalance = (float) ($historyItem['balance'] ?? 0);

        if ($remainingBalance <= 0) {
            return back()
                ->withErrors(['montant' => 'Ce mois est deja integralement regle.'])
                ->withInput($request->all() + [
                    'remaining_balance' => $remainingBalance,
                    'month_label' => $historyItem['month_label'],
                ]);
        }

        if ((float) $validated['montant'] > $remainingBalance) {
            return back()
                ->withErrors(['montant' => 'Le montant ne peut pas depasser le reste a regler pour ce mois.'])
                ->withInput($request->all() + [
                    'remaining_balance' => $remainingBalance,
                    'month_label' => $historyItem['month_label'],
                ]);
        }

        $newPaidTotal = (float) ($historyItem['paid_amount'] ?? 0) + (float) $validated['montant'];
        $expectedTotal = (float) ($historyItem['expected_total'] ?? 0);

        $paiement = Paiement::create([
            'enfant_id' => $inscription->enfant_id,
            'montant' => $validated['montant'],
            'date_paiement' => $validated['date_paiement'],
            'mois' => $validated['mois'],
            'annee' => $validated['annee'],
            'mode_paiement' => $validated['mode_paiement'],
            'statut' => $newPaidTotal >= $expectedTotal ? 'Paye' : 'Partiel',
            'commentaire' => $validated['commentaire'] ?? null,
        ]);

        if (empty($paiement->reference)) {
            $paiement->update([
                'reference' => sprintf('PAY-%s-%06d', $paiement->annee ?: now()->format('Y'), $paiement->id),
            ]);
        }

        return redirect()
            ->route('inscriptions.show', $inscription)
            ->with('success', 'Paiement ajoute avec succes pour '.$historyItem['month_label'].'.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inscription $inscription): View
    {
        $this->ensureParentCanAccessInscription($inscription);

        $enfants = $this->allowedChildrenQuery()
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
        $packages = $this->availablePackages($inscription->package_id);
        $activeAcademicYear = AcademicYear::query()->where('label', $inscription->annee_scolaire)->first();
        $annualRegistrationFee = (float) ($inscription->annual_registration_fee ?? $activeAcademicYear?->registration_fee ?? 0);

        return view('inscriptions.edit', compact('inscription', 'enfants', 'packages', 'activeAcademicYear', 'annualRegistrationFee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInscriptionRequest $request, Inscription $inscription): RedirectResponse
    {
        $this->ensureParentCanAccessInscription($inscription);

        $validated = $request->validated();

        if ($this->isParentUser()) {
            $canUseChild = $this->allowedChildrenQuery()
                ->whereKey($validated['enfant_id'])
                ->exists();

            abort_unless($canUseChild, 403);
        }

        $package = Package::query()->findOrFail($validated['package_id']);
        $annualRegistrationFee = (float) ($inscription->annual_registration_fee ?? 0);
        $packageMonthlyTotal = (float) $package->total_mensuel;

        $inscription->update([
            ...$validated,
            'annual_registration_fee' => $annualRegistrationFee,
            'package_monthly_total' => $packageMonthlyTotal,
            'total_amount' => $packageMonthlyTotal + $annualRegistrationFee,
        ]);

        return redirect()
            ->route('inscriptions.index')
            ->with('success', 'Inscription mise a jour avec succes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inscription $inscription): RedirectResponse
    {
        $this->ensureParentCanAccessInscription($inscription);

        $inscription->delete();

        return redirect()
            ->route('inscriptions.index')
            ->with('success', 'Inscription supprimee avec succes.');
    }

    private function availablePackages(?int $selectedPackageId = null)
    {
        return Package::query()
            ->where(function ($query) use ($selectedPackageId) {
                $query->where('is_active', true);

                if ($selectedPackageId) {
                    $query->orWhere('id', $selectedPackageId);
                }
            })
            ->orderByDesc('is_active')
            ->orderBy('nom')
            ->get();
    }

    private function activeAcademicYear(): ?AcademicYear
    {
        return AcademicYear::query()
            ->active()
            ->orderByDesc('start_date')
            ->first();
    }

    private function availableChildrenForAcademicYear(?string $academicYearLabel)
    {
        return $this->allowedChildrenQuery()
            ->when($academicYearLabel, function ($query, $label) {
                $query->whereDoesntHave('inscriptions', function ($subQuery) use ($label) {
                    $subQuery->where('annee_scolaire', $label);
                });
            })
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
    }

    private function monthlyPaymentHistory(Inscription $inscription): Collection
    {
        $startMonth = ($inscription->date_inscription ? $inscription->date_inscription->copy() : now())->startOfMonth();
        $endMonth = now()->startOfMonth();

        if ($startMonth->greaterThan($endMonth)) {
            $endMonth = $startMonth->copy();
        }

        $paymentsByMonth = ($inscription->enfant?->paiements ?? collect())
            ->filter(function ($paiement) use ($startMonth) {
                $paymentMonth = Carbon::create((int) $paiement->annee, (int) $paiement->mois, 1)->startOfMonth();

                return $paymentMonth->greaterThanOrEqualTo($startMonth);
            })
            ->groupBy(fn ($paiement) => sprintf('%04d-%02d', (int) $paiement->annee, (int) $paiement->mois));

        $history = collect();
        $cursor = $startMonth->copy();

        while ($cursor->lessThanOrEqualTo($endMonth)) {
            $monthKey = $cursor->format('Y-m');
            $monthPayments = $paymentsByMonth->get($monthKey, collect())->sortByDesc(function ($paiement) {
                return sprintf('%s-%010d', optional($paiement->date_paiement)->format('Ymd') ?: '00000000', $paiement->id);
            })->values();

            $expectedRegistrationFee = $cursor->equalTo($startMonth)
                ? (float) $inscription->resolved_annual_registration_fee
                : 0.0;
            $expectedMonthlyTotal = (float) $inscription->resolved_package_monthly_total;
            $expectedTotal = $expectedMonthlyTotal + $expectedRegistrationFee;
            $paidAmount = (float) $monthPayments->sum('montant');
            $latestPayment = $monthPayments->first();

            $status = 'Non enregistre';

            if ($monthPayments->isNotEmpty()) {
                $status = $paidAmount >= $expectedTotal && $expectedTotal > 0
                    ? 'Paye'
                    : ($latestPayment->statut ?: 'Partiel');
            }

            $history->push([
                'month_key' => $monthKey,
                'month_label' => ucfirst($cursor->copy()->locale('fr')->translatedFormat('F Y')),
                'expected_monthly_total' => $expectedMonthlyTotal,
                'expected_registration_fee' => $expectedRegistrationFee,
                'expected_total' => $expectedTotal,
                'paid_amount' => $paidAmount,
                'balance' => max($expectedTotal - $paidAmount, 0),
                'status' => $status,
                'payments' => $monthPayments,
                'latest_payment' => $latestPayment,
            ]);

            $cursor->addMonth();
        }

        return $history->sortByDesc('month_key')->values();
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

        return ParentModel::query()->where('user_id', $userId)->first();
    }

    private function allowedChildrenQuery()
    {
        $query = Enfant::query();

        if (! $this->isParentUser()) {
            return $query;
        }

        $parent = $this->currentParent();

        if (! $parent) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($scope) use ($parent) {
            $scope->where('parent_id', $parent->id)
                ->orWhereHas('familyRelations', function ($relationScope) use ($parent) {
                    $relationScope->where('parent_id', $parent->id);
                });
        });
    }

    private function ensureParentCanAccessInscription(Inscription $inscription): void
    {
        if (! $this->isParentUser()) {
            return;
        }

        $canAccess = $this->allowedChildrenQuery()
            ->whereKey($inscription->enfant_id)
            ->exists();

        abort_unless($canAccess, 403);
    }

    private function normalizeLevelLabel(?string $level): string
    {
        $value = Str::ascii((string) $level);
        $value = mb_strtolower($value, 'UTF-8');

        return preg_replace('/\s+/', ' ', trim($value)) ?: '';
    }

    private function resolveAcademicYearByLabel(?string $label): ?AcademicYear
    {
        $rawLabel = trim((string) $label);

        if ($rawLabel === '') {
            return null;
        }

        $exact = AcademicYear::query()->where('label', $rawLabel)->first();

        if ($exact) {
            return $exact;
        }

        $normalizedTarget = $this->normalizeLevelLabel($rawLabel);
        $academicYears = AcademicYear::query()->orderByDesc('start_date')->get();

        $normalizedMatch = $academicYears->first(function (AcademicYear $academicYear) use ($normalizedTarget) {
            return $this->normalizeLevelLabel($academicYear->label) === $normalizedTarget;
        });

        if ($normalizedMatch) {
            return $normalizedMatch;
        }

        preg_match_all('/\d{4}/', $rawLabel, $matches);
        $yearTokens = $matches[0] ?? [];

        if (! empty($yearTokens)) {
            $tokenMatch = $academicYears->first(function (AcademicYear $academicYear) use ($yearTokens) {
                $normalizedLabel = $this->normalizeLevelLabel($academicYear->label);

                foreach ($yearTokens as $token) {
                    if (! str_contains($normalizedLabel, $token)) {
                        return false;
                    }
                }

                return true;
            });

            if ($tokenMatch) {
                return $tokenMatch;
            }
        }

        return null;
    }
}
