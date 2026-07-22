<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActiviteRequest;
use App\Http\Requests\UpdateActiviteRequest;
use App\Models\Activite;
use App\Models\ActivityRegistration;
use App\Models\Enfant;
use App\Models\ParentModel;
use App\Models\Personnel;
use App\Models\Salle;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;

class ActiviteController extends Controller
{
    private const MAX_RECURRENCE_OCCURRENCES = 500;

    private const RECURRENCE_OPTIONS = [
        'journalier' => 'Journaliere',
        'hebdomadaire' => 'Hebdomadaire',
        'mensuelle' => 'Mensuelle',
        'trimestrielle' => 'Trimestrielle',
        'semestrielle' => 'Semestrielle',
        'annuelle' => 'Annuelle',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $educatorPersonnel = $this->currentEducatorPersonnel();
        $isRestrictedEducator = $this->isRestrictedEducator();

        $activites = $this->scopedActivitiesQuery($educatorPersonnel)
            ->with(['responsablePersonnel', 'salle'])
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->orderBy('heure')
            ->get();

        $calendarEvents = $this->buildCalendarEvents($activites);

        return view('activites.index', [
            'activites' => $activites,
            'calendarEvents' => $calendarEvents,
            'isRestrictedEducator' => $isRestrictedEducator,
            'educatorPersonnel' => $educatorPersonnel,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $responsables = $this->responsables();
        $salles = $this->salles();
        $recurrenceOptions = self::RECURRENCE_OPTIONS;

        return view('activites.create', compact('responsables', 'salles', 'recurrenceOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActiviteRequest $request): RedirectResponse
    {
        Activite::create($this->activityPayload($request->validated()));

        return redirect()->route('activites.index')->with('success', 'Activite ajoutee avec succes.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Activite $activite): View
    {
        $this->ensureActivityAccess($activite);
        $activite->load([
            'responsablePersonnel',
            'salle',
            'registrations.enfant',
            'registrations.parent',
        ]);
        $activityEndAt = $activite->endsAt();
        $participationCutoffAt = $activite->participationCutoffAt();

        if ($this->isParentUser()) {
            $parentChildIds = $this->currentParent()?->enfants()->pluck('id')->all() ?? [];

            $activite->setRelation(
                'registrations',
                $activite->registrations
                    ->whereIn('enfant_id', $parentChildIds)
                    ->values()
            );
        }

        $validatedRegistrations = $activite->registrations
            ->where('status', ActivityRegistration::STATUS_VALIDATED)
            ->count();
        $registeredChildIds = $activite->registrations->pluck('enfant_id')->filter()->all();

        return view('activites.show', [
            'activite' => $activite,
            'availableChildren' => $this->isParentUser()
                ? collect()
                : Enfant::query()
                    ->with('parent')
                    ->when(! empty($registeredChildIds), fn ($query) => $query->whereNotIn('id', $registeredChildIds))
                    ->orderBy('prenom')
                    ->orderBy('nom')
                    ->get(),
            'canAddParticipants' => ! $activityEndAt || now()->lt($activityEndAt),
            'canManageParticipation' => ! $participationCutoffAt || now()->lte($participationCutoffAt),
            'activityEndAt' => $activityEndAt,
            'participationCutoffAt' => $participationCutoffAt,
            'paymentMethodOptions' => ActivityRegistration::PAYMENT_METHOD_OPTIONS,
            'validatedRegistrations' => $validatedRegistrations,
            'hasRegistrableChildren' => $this->isParentUser() ? false : Enfant::query()
                ->when(! empty($registeredChildIds), fn ($query) => $query->whereNotIn('id', $registeredChildIds))
                ->exists(),
            'registrationStatusOptions' => ActivityRegistration::STATUS_OPTIONS,
            'participationOptions' => ActivityRegistration::PARTICIPATION_OPTIONS,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activite $activite): View
    {
        $this->ensureActivityAccess($activite);
        $responsables = $this->responsables();
        $salles = $this->salles();
        $recurrenceOptions = self::RECURRENCE_OPTIONS;

        return view('activites.edit', compact('activite', 'responsables', 'salles', 'recurrenceOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActiviteRequest $request, Activite $activite): RedirectResponse
    {
        $this->ensureActivityAccess($activite);
        $activite->update($this->activityPayload($request->validated()));

        return redirect()->route('activites.index')->with('success', 'Activite mise a jour avec succes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activite $activite): RedirectResponse
    {
        $this->ensureActivityAccess($activite);
        $activite->delete();

        return redirect()->route('activites.index')->with('success', 'Activite supprimee avec succes.');
    }

    private function responsables()
    {
        $educatorPersonnel = $this->currentEducatorPersonnel();

        if ($this->isRestrictedEducator()) {
            return $educatorPersonnel ? collect([$educatorPersonnel]) : collect();
        }

        return Personnel::query()
            ->orderBy('fonction')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
    }

    private function scopedActivitiesQuery(?Personnel $educatorPersonnel = null): Builder
    {
        $query = Activite::query();

        if ($this->isParentUser()) {
            $parentChildIds = $this->currentParent()?->enfants?->pluck('id')->all() ?? [];

            if (empty($parentChildIds)) {
                return $query->whereRaw('1 = 0');
            }

            return $query->whereHas('registrations', function ($registrationScope) use ($parentChildIds) {
                $registrationScope->whereIn('enfant_id', $parentChildIds);
            });
        }

        if (! $this->isRestrictedEducator()) {
            return $query;
        }

        if (! $educatorPersonnel) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('responsable_personnel_id', $educatorPersonnel->id);
    }

    private function ensureActivityAccess(Activite $activite): void
    {
        if ($this->isParentUser()) {
            $parentChildIds = $this->currentParent()?->enfants?->pluck('id')->all() ?? [];

            $canAccess = ! empty($parentChildIds)
                && $activite->registrations()
                    ->whereIn('enfant_id', $parentChildIds)
                    ->exists();

            abort_unless($canAccess, 403);

            return;
        }

        if (! $this->isRestrictedEducator()) {
            return;
        }

        $educatorPersonnel = $this->currentEducatorPersonnel();

        abort_unless(
            $educatorPersonnel && (int) $activite->responsable_personnel_id === (int) $educatorPersonnel->id,
            403
        );
    }

    private function currentEducatorPersonnel(): ?Personnel
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        return Personnel::query()
            ->where('user_id', $user->id)
            ->first();
    }

    private function isRestrictedEducator(): bool
    {
        return (bool) auth()->user()?->hasRole('Educateur');
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
            ->with('enfants')
            ->first();
    }

    private function activityPayload(array $data): array
    {
        $personnel = Personnel::find($data['responsable_personnel_id']);

        $data['responsable'] = $personnel ? trim($personnel->prenom.' '.$personnel->nom) : ($data['responsable'] ?? null);
        $data['heure'] = $data['heure_debut'] ?? $data['heure'] ?? null;

        if (empty($data['recurrence'])) {
            $data['date_fin_recurrence'] = null;
            $data['recurrence_jours'] = null;
            $data['recurrence_jour_mois'] = null;
            $data['recurrence_date_annuelle'] = null;
        }

        if (($data['recurrence'] ?? null) !== 'hebdomadaire') {
            $data['recurrence_jours'] = null;
        }

        if (!in_array(($data['recurrence'] ?? null), ['mensuelle', 'trimestrielle', 'semestrielle'], true)) {
            $data['recurrence_jour_mois'] = null;
        }

        if (($data['recurrence'] ?? null) !== 'annuelle') {
            $data['recurrence_date_annuelle'] = null;
        }

        return $data;
    }

    private function salles()
    {
        return Salle::query()
            ->where('statut', Salle::STATUT_DISPONIBLE)
            ->orderBy('nom')
            ->get();
    }

    private function buildCalendarEvents($activites): array
    {
        $events = [];

        foreach ($activites as $activite) {
            $startTime = $activite->heure_debut ?: $activite->heure;
            $occurrenceDates = $this->recurrenceDates($activite);

            foreach ($occurrenceDates as $occurrenceDate) {
                $date = $occurrenceDate->format('Y-m-d');
                $dateTime = $startTime ? $date.'T'.substr((string) $startTime, 0, 5) : $date;
                $endDateTime = $activite->heure_fin ? $date.'T'.substr((string) $activite->heure_fin, 0, 5) : null;

                $events[] = [
                    'title' => $activite->titre,
                    'start' => $dateTime,
                    'end' => $endDateTime,
                    'allDay' => empty($startTime),
                    'url' => route('activites.show', $activite),
                    'extendedProps' => [
                        'responsable' => $activite->responsable,
                        'heure' => $startTime,
                        'salle' => $activite->salle?->nom,
                    ],
                ];
            }
        }

        return $events;
    }

    private function recurrenceDates(Activite $activite): array
    {
        if (! $activite->date) {
            return [];
        }

        $startDate = $activite->date->copy()->startOfDay();
        $endDate = $activite->date_fin_recurrence
            ? $activite->date_fin_recurrence->copy()->startOfDay()
            : $startDate->copy();

        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy();
        }

        $recurrence = $activite->recurrence;

        if (empty($recurrence)) {
            return [$startDate];
        }

        return match ($recurrence) {
            'journalier' => $this->buildDailyOccurrences($startDate, $endDate),
            'hebdomadaire' => $this->buildWeeklyOccurrences($startDate, $endDate, $activite->recurrence_jours ?? []),
            'mensuelle' => $this->buildMonthlyOccurrences($startDate, $endDate, 1, $activite->recurrence_jour_mois),
            'trimestrielle' => $this->buildMonthlyOccurrences($startDate, $endDate, 3, $activite->recurrence_jour_mois),
            'semestrielle' => $this->buildMonthlyOccurrences($startDate, $endDate, 6, $activite->recurrence_jour_mois),
            'annuelle' => $this->buildYearlyOccurrences($startDate, $endDate, $activite->recurrence_date_annuelle),
            default => [$startDate],
        };
    }

    private function buildDailyOccurrences(Carbon $startDate, Carbon $endDate): array
    {
        $dates = [];
        $cursor = $startDate->copy();

        while ($cursor->lte($endDate) && count($dates) < self::MAX_RECURRENCE_OCCURRENCES) {
            $dates[] = $cursor->copy();
            $cursor->addDay();
        }

        return $dates;
    }

    private function buildWeeklyOccurrences(Carbon $startDate, Carbon $endDate, array $dayValues): array
    {
        $daysMap = [
            'lundi' => 1,
            'mardi' => 2,
            'mercredi' => 3,
            'jeudi' => 4,
            'vendredi' => 5,
            'samedi' => 6,
            'dimanche' => 7,
        ];

        $selectedDays = collect($dayValues)
            ->map(fn ($day) => $daysMap[$day] ?? null)
            ->filter()
            ->values()
            ->all();

        if (empty($selectedDays)) {
            $selectedDays = [$startDate->dayOfWeekIso];
        }

        $dates = [];
        $cursor = $startDate->copy();

        while ($cursor->lte($endDate) && count($dates) < self::MAX_RECURRENCE_OCCURRENCES) {
            if (in_array($cursor->dayOfWeekIso, $selectedDays, true)) {
                $dates[] = $cursor->copy();
            }

            $cursor->addDay();
        }

        return $dates;
    }

    private function buildMonthlyOccurrences(Carbon $startDate, Carbon $endDate, int $monthsStep, mixed $dayOfMonth): array
    {
        $targetDay = (int) ($dayOfMonth ?: $startDate->day);
        $targetDay = max(1, min(31, $targetDay));

        $dates = [];
        $cursor = Carbon::create($startDate->year, $startDate->month, 1)->startOfDay();

        while ($cursor->lte($endDate) && count($dates) < self::MAX_RECURRENCE_OCCURRENCES) {
            $monthEndDay = $cursor->copy()->endOfMonth()->day;
            $effectiveDay = min($targetDay, $monthEndDay);
            $candidate = $cursor->copy()->day($effectiveDay);

            if ($candidate->betweenIncluded($startDate, $endDate)) {
                $dates[] = $candidate;
            }

            $cursor->addMonthsNoOverflow($monthsStep)->startOfMonth();
        }

        return $dates;
    }

    private function buildYearlyOccurrences(Carbon $startDate, Carbon $endDate, ?Carbon $annualDate): array
    {
        $reference = $annualDate?->copy() ?: $startDate->copy();
        $month = $reference->month;
        $day = $reference->day;

        $dates = [];

        for ($year = $startDate->year; $year <= $endDate->year && count($dates) < self::MAX_RECURRENCE_OCCURRENCES; $year++) {
            $monthEndDay = Carbon::create($year, $month, 1)->endOfMonth()->day;
            $effectiveDay = min($day, $monthEndDay);
            $candidate = Carbon::create($year, $month, $effectiveDay)->startOfDay();

            if ($candidate->betweenIncluded($startDate, $endDate)) {
                $dates[] = $candidate;
            }
        }

        return $dates;
    }
}
