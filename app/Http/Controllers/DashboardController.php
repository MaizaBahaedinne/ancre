<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Activite;
use App\Models\Enfant;
use App\Models\Incident;
use App\Models\Inscription;
use App\Models\ParentModel;
use App\Models\Presence;
use App\Models\Salle;
use App\Models\School;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        if ($request->user()?->hasRole('Parent')) {
            return $this->parentDashboard($request);
        }

        $today = Carbon::today();
        $now = Carbon::now();
        $activeAcademicYear = AcademicYear::query()->active()->first();
        $activeAcademicYearLabel = $activeAcademicYear?->label;
        $activeAcademicYearStart = $activeAcademicYear?->start_date?->copy()?->startOfDay();
        $activeAcademicYearEnd = $activeAcademicYear?->end_date?->copy()?->endOfDay();

        $isWithinActiveAcademicPeriod = $activeAcademicYearStart && $activeAcademicYearEnd
            ? $today->betweenIncluded($activeAcademicYearStart->copy()->startOfDay(), $activeAcademicYearEnd->copy()->endOfDay())
            : true;

        $currentYearChildrenQuery = Enfant::query()
            ->when($activeAcademicYearLabel, function ($query, $yearLabel) {
                $query->whereHas('inscriptions', fn ($inscriptions) => $inscriptions->where('annee_scolaire', $yearLabel));
            });

        $currentYearInscriptionChildrenIds = Inscription::query()
            ->when($activeAcademicYearLabel, fn ($query, $yearLabel) => $query->where('annee_scolaire', $yearLabel))
            ->distinct()
            ->pluck('enfant_id');

        $totalEnfants = (clone $currentYearChildrenQuery)->count();

        $presentToday = Presence::query()
            ->whereDate('date', $today)
            ->when($activeAcademicYearLabel, function ($query, $yearLabel) {
                $query->whereHas('enfant.inscriptions', fn ($inscriptions) => $inscriptions->where('annee_scolaire', $yearLabel));
            })
            ->distinct('enfant_id')
            ->count('enfant_id');

        if (! $isWithinActiveAcademicPeriod) {
            $presentToday = 0;
        }

        $absentToday = max($totalEnfants - $presentToday, 0);
        $presenceRateToday = $totalEnfants > 0
            ? round(($presentToday / $totalEnfants) * 100, 1)
            : 0.0;

        $genderCounts = [
            'F' => (clone $currentYearChildrenQuery)->where('sexe', 'F')->count(),
            'M' => (clone $currentYearChildrenQuery)->where('sexe', 'M')->count(),
        ];

        $avgAgeYears = round((float) ((clone $currentYearChildrenQuery)
            ->selectRaw('AVG(TIMESTAMPDIFF(YEAR, date_naissance, CURDATE())) as avg_age')
            ->value('avg_age') ?? 0), 1);

        $ageBandCounts = [
            '2-3 ans' => (clone $currentYearChildrenQuery)->whereBetween('date_naissance', [$today->copy()->subYears(3), $today->copy()->subYears(2)])->count(),
            '4-5 ans' => (clone $currentYearChildrenQuery)->whereBetween('date_naissance', [$today->copy()->subYears(5), $today->copy()->subYears(4)])->count(),
            '6+ ans' => (clone $currentYearChildrenQuery)->whereDate('date_naissance', '<', $today->copy()->subYears(6))->count(),
        ];

        $childrenBySchool = School::query()
            ->leftJoin('school_classes', 'schools.id', '=', 'school_classes.school_id')
            ->leftJoin('enfants', 'school_classes.id', '=', 'enfants.school_class_id')
            ->when($activeAcademicYearLabel, function ($query) use ($currentYearInscriptionChildrenIds) {
                $query->whereIn('enfants.id', $currentYearInscriptionChildrenIds);
            })
            ->groupBy('schools.id', 'schools.name')
            ->orderByDesc('children_count')
            ->get([
                'schools.name',
                \DB::raw('COUNT(enfants.id) as children_count'),
            ]);

        $childrenByLevel = SchoolClass::query()
            ->leftJoin('enfants', 'school_classes.id', '=', 'enfants.school_class_id')
            ->when($activeAcademicYearLabel, function ($query) use ($currentYearInscriptionChildrenIds) {
                $query->whereIn('enfants.id', $currentYearInscriptionChildrenIds);
            })
            ->selectRaw("COALESCE(NULLIF(school_classes.level, ''), 'Non defini') as level_label")
            ->selectRaw('COUNT(enfants.id) as children_count')
            ->groupBy('level_label')
            ->orderByDesc('children_count')
            ->get();

        $schoolOccupancy = School::query()
            ->leftJoin('school_classes', 'schools.id', '=', 'school_classes.school_id')
            ->leftJoin('enfants', 'school_classes.id', '=', 'enfants.school_class_id')
            ->when($activeAcademicYearLabel, function ($query) use ($currentYearInscriptionChildrenIds) {
                $query->whereIn('enfants.id', $currentYearInscriptionChildrenIds);
            })
            ->groupBy('schools.id', 'schools.name')
            ->get([
                'schools.name',
                \DB::raw('COUNT(DISTINCT school_classes.id) as classes_count'),
                \DB::raw('SUM(COALESCE(school_classes.capacity, 0)) as total_capacity'),
                \DB::raw('COUNT(enfants.id) as children_count'),
            ])
            ->map(function ($row) {
                $capacity = (int) ($row->total_capacity ?? 0);
                $children = (int) ($row->children_count ?? 0);

                return [
                    'name' => $row->name,
                    'classes_count' => (int) ($row->classes_count ?? 0),
                    'total_capacity' => $capacity,
                    'children_count' => $children,
                    'occupancy_rate' => $capacity > 0 ? round(($children / $capacity) * 100, 1) : null,
                ];
            })
            ->sortByDesc('children_count')
            ->values();

        $totalSchools = School::query()
            ->when($activeAcademicYearLabel, function ($query) use ($currentYearInscriptionChildrenIds) {
                $query->whereHas('classes.enfants', fn ($children) => $children->whereIn('enfants.id', $currentYearInscriptionChildrenIds));
            })
            ->count();

        $activeClasses = SchoolClass::query()
            ->where('is_active', true)
            ->when($activeAcademicYearLabel, function ($query) use ($currentYearInscriptionChildrenIds) {
                $query->whereHas('enfants', fn ($children) => $children->whereIn('enfants.id', $currentYearInscriptionChildrenIds));
            })
            ->count();

        $childrenWithoutClass = (clone $currentYearChildrenQuery)
            ->when($activeAcademicYearLabel, function ($query) {
                $query->where(function ($inner) {
                    $inner->whereNull('school_class_id')
                        ->orWhereDoesntHave('schoolClass');
                });
            }, fn ($query) => $query->whereNull('school_class_id'))
            ->count();

        $presenceTrend = Presence::query()
            ->whereDate('date', '>=', $today->copy()->subDays(6))
            ->when($activeAcademicYearLabel, function ($query, $yearLabel) {
                $query->whereHas('enfant.inscriptions', fn ($inscriptions) => $inscriptions->where('annee_scolaire', $yearLabel));
            })
            ->selectRaw('DATE(date) as date_key')
            ->selectRaw('COUNT(DISTINCT enfant_id) as present_count')
            ->groupBy('date_key')
            ->orderBy('date_key')
            ->get()
            ->keyBy('date_key');

        if (! $isWithinActiveAcademicPeriod) {
            $presenceTrend = collect();
        }

        $presenceTrendLabels = [];
        $presenceTrendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $today->copy()->subDays($i);
            $key = $day->toDateString();
            $presenceTrendLabels[] = $day->format('d/m');
            $presenceTrendData[] = (int) ($presenceTrend[$key]->present_count ?? 0);
        }

        $incidentStatusCounts = [
            'Ouvert' => Incident::query()->where('workflow_status', Incident::WORKFLOW_OPEN)
                ->when($activeAcademicYearStart && $activeAcademicYearEnd, fn ($query) => $query->whereBetween('date', [$activeAcademicYearStart, $activeAcademicYearEnd]))
                ->count(),
            'Pris en charge' => Incident::query()->where('workflow_status', Incident::WORKFLOW_TAKEN)
                ->when($activeAcademicYearStart && $activeAcademicYearEnd, fn ($query) => $query->whereBetween('date', [$activeAcademicYearStart, $activeAcademicYearEnd]))
                ->count(),
            'En cours' => Incident::query()->where('workflow_status', Incident::WORKFLOW_IN_PROGRESS)
                ->when($activeAcademicYearStart && $activeAcademicYearEnd, fn ($query) => $query->whereBetween('date', [$activeAcademicYearStart, $activeAcademicYearEnd]))
                ->count(),
            'En attente' => Incident::query()->where('workflow_status', Incident::WORKFLOW_WAITING)
                ->when($activeAcademicYearStart && $activeAcademicYearEnd, fn ($query) => $query->whereBetween('date', [$activeAcademicYearStart, $activeAcademicYearEnd]))
                ->count(),
            'Cloture' => Incident::query()->where('workflow_status', Incident::WORKFLOW_CLOSED)
                ->when($activeAcademicYearStart && $activeAcademicYearEnd, fn ($query) => $query->whereBetween('date', [$activeAcademicYearStart, $activeAcademicYearEnd]))
                ->count(),
        ];

        $openIncidents = Incident::query()
            ->when($activeAcademicYearStart && $activeAcademicYearEnd, fn ($query) => $query->whereBetween('date', [$activeAcademicYearStart, $activeAcademicYearEnd]))
            ->where(function ($query) {
                $query->whereNull('workflow_status')
                    ->orWhere('workflow_status', '!=', Incident::WORKFLOW_CLOSED);
            })
            ->count();

        $closedIncidents30d = Incident::query()
            ->where('workflow_status', Incident::WORKFLOW_CLOSED)
            ->whereDate('date', '>=', $today->copy()->subDays(30))
            ->when($activeAcademicYearStart && $activeAcademicYearEnd, fn ($query) => $query->whereBetween('date', [$activeAcademicYearStart, $activeAcademicYearEnd]))
            ->count();

        $avgTakeoverMinutes = round((float) (Incident::query()
            ->when($activeAcademicYearStart && $activeAcademicYearEnd, fn ($query) => $query->whereBetween('date', [$activeAcademicYearStart, $activeAcademicYearEnd]))
            ->whereNotNull('opened_at')
            ->whereNotNull('taken_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, opened_at, taken_at)) as avg_takeover')
            ->value('avg_takeover') ?? 0), 1);

        $avgResolutionMinutes = round((float) (Incident::query()
            ->when($activeAcademicYearStart && $activeAcademicYearEnd, fn ($query) => $query->whereBetween('date', [$activeAcademicYearStart, $activeAcademicYearEnd]))
            ->whereNotNull('opened_at')
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, opened_at, resolved_at)) as avg_resolution')
            ->value('avg_resolution') ?? 0), 1);

        $incidentsByType = Incident::query()
            ->when($activeAcademicYearStart && $activeAcademicYearEnd, fn ($query) => $query->whereBetween('date', [$activeAcademicYearStart, $activeAcademicYearEnd]))
            ->select('type_incident')
            ->selectRaw('COUNT(*) as incidents_count')
            ->groupBy('type_incident')
            ->orderByDesc('incidents_count')
            ->limit(6)
            ->get();

        $todayActivities = Activite::query()
            ->with(['salle'])
            ->withCount('participations')
            ->whereDate('date', $today)
            ->when($activeAcademicYearStart && $activeAcademicYearEnd, fn ($query) => $query->whereBetween('date', [$activeAcademicYearStart, $activeAcademicYearEnd]))
            ->get();

        $inProgressActivities = $todayActivities->filter(function (Activite $activite) use ($now): bool {
            $start = $activite->heure_debut ?: $activite->heure;
            $end = $activite->heure_fin;

            if (! $start) {
                return false;
            }

            $startAt = $now->copy()->setTimeFromTimeString(strlen($start) === 5 ? $start.':00' : $start);

            if ($end) {
                $endAt = $now->copy()->setTimeFromTimeString(strlen($end) === 5 ? $end.':00' : $end);
            } else {
                $endAt = $startAt->copy()->addHour();
            }

            return $now->between($startAt, $endAt);
        })->values();

        $completedActivities = $todayActivities->filter(function (Activite $activite) use ($now): bool {
            $end = $activite->heure_fin;

            if (! $end) {
                return false;
            }

            $endAt = $now->copy()->setTimeFromTimeString(strlen($end) === 5 ? $end.':00' : $end);

            return $endAt->lt($now);
        })->count();

        $activitiesWithCapacity = $todayActivities->filter(fn (Activite $activite) => (int) ($activite->capacite ?? 0) > 0);
        $avgParticipationRate = $activitiesWithCapacity->isNotEmpty()
            ? round($activitiesWithCapacity->avg(fn (Activite $activite) => ($activite->participations_count / max((int) $activite->capacite, 1)) * 100), 1)
            : 0.0;

        $totalRooms = Salle::query()->count();
        $occupiedRoomIds = $inProgressActivities->pluck('salle_id')->filter()->unique()->values();
        $roomsOccupiedNow = $occupiedRoomIds->count();
        $roomOccupancyRate = $totalRooms > 0
            ? round(($roomsOccupiedNow / $totalRooms) * 100, 1)
            : 0.0;

        $roomsInMaintenance = Salle::query()->where('statut', Salle::STATUT_MAINTENANCE)->count();
        $roomsUnavailable = Salle::query()->where('statut', Salle::STATUT_INDISPONIBLE)->count();
        $roomsReserved = Salle::query()->where('statut', Salle::STATUT_RESERVEE)->count();

        $roomLiveLoad = Salle::query()
            ->get(['id', 'nom', 'capacite'])
            ->map(function (Salle $salle) use ($inProgressActivities) {
                /** @var Collection<int, Activite> $activitiesInRoom */
                $activitiesInRoom = $inProgressActivities->where('salle_id', $salle->id);
                $participants = (int) $activitiesInRoom->sum('participations_count');
                $capacity = max((int) ($salle->capacite ?? 0), 1);

                return [
                    'name' => $salle->nom,
                    'participants' => $participants,
                    'capacity' => (int) ($salle->capacite ?? 0),
                    'load_rate' => round(($participants / $capacity) * 100, 1),
                    'is_overloaded' => $participants > (int) ($salle->capacite ?? 0) && (int) ($salle->capacite ?? 0) > 0,
                ];
            })
            ->sortByDesc('participants')
            ->values();

        $archivedYearStats = AcademicYear::query()
            ->when($activeAcademicYear?->id, fn ($query, $id) => $query->whereKeyNot($id))
            ->orderByDesc('start_date')
            ->get()
            ->map(function (AcademicYear $year) {
                $childrenCount = Inscription::query()
                    ->where('annee_scolaire', $year->label)
                    ->distinct('enfant_id')
                    ->count('enfant_id');

                $inscriptionsCount = Inscription::query()
                    ->where('annee_scolaire', $year->label)
                    ->count();

                $presencesCount = Presence::query()
                    ->whereBetween('date', [$year->start_date, $year->end_date])
                    ->count();

                $incidentsCount = Incident::query()
                    ->whereBetween('date', [$year->start_date, $year->end_date])
                    ->count();

                $activitiesCount = Activite::query()
                    ->whereBetween('date', [$year->start_date, $year->end_date])
                    ->count();

                return [
                    'label' => $year->label,
                    'period' => $year->start_date?->format('d/m/Y').' - '.$year->end_date?->format('d/m/Y'),
                    'children_count' => $childrenCount,
                    'inscriptions_count' => $inscriptionsCount,
                    'presences_count' => $presencesCount,
                    'incidents_count' => $incidentsCount,
                    'activities_count' => $activitiesCount,
                ];
            })
            ->values();

        return view('dashboard', compact(
            'activeAcademicYear',
            'activeAcademicYearLabel',
            'totalEnfants',
            'presentToday',
            'absentToday',
            'presenceRateToday',
            'genderCounts',
            'avgAgeYears',
            'ageBandCounts',
            'childrenBySchool',
            'childrenByLevel',
            'totalSchools',
            'activeClasses',
            'childrenWithoutClass',
            'schoolOccupancy',
            'presenceTrendLabels',
            'presenceTrendData',
            'incidentStatusCounts',
            'openIncidents',
            'closedIncidents30d',
            'avgTakeoverMinutes',
            'avgResolutionMinutes',
            'incidentsByType',
            'todayActivities',
            'inProgressActivities',
            'completedActivities',
            'avgParticipationRate',
            'totalRooms',
            'roomsOccupiedNow',
            'roomOccupancyRate',
            'roomsInMaintenance',
            'roomsUnavailable',
            'roomsReserved',
            'roomLiveLoad',
            'archivedYearStats'
        ));
    }

    private function parentDashboard(Request $request): View
    {
        $parent = ParentModel::query()
            ->with(['enfants.incidents' => fn ($query) => $query->orderByDesc('date')->orderByDesc('created_at')])
            ->where('user_id', $request->user()->id)
            ->first();

        $incidentTickets = collect();

        if ($parent) {
            $childrenIds = $parent->enfants->pluck('id')->all();

            $incidentTickets = Incident::query()
                ->with(['enfant', 'responsablePersonnel'])
                ->where('notify_parent', true)
                ->whereIn('enfant_id', $childrenIds)
                ->orderByDesc('date')
                ->orderByDesc('opened_at')
                ->get();
        }

        return view('parent.dashboard', [
            'parent' => $parent,
            'incidentTickets' => $incidentTickets,
            'childrenCount' => $parent?->enfants->count() ?? 0,
            'notifiedIncidentsCount' => $incidentTickets->count(),
        ]);
    }
}
