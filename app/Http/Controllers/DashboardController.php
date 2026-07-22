<?php

namespace App\Http\Controllers;

use App\Models\Activite;
use App\Models\Enfant;
use App\Models\Incident;
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
        $totalEnfants = Enfant::query()->count();

        $presentToday = Presence::query()
            ->whereDate('date', $today)
            ->distinct('enfant_id')
            ->count('enfant_id');

        $absentToday = max($totalEnfants - $presentToday, 0);
        $presenceRateToday = $totalEnfants > 0
            ? round(($presentToday / $totalEnfants) * 100, 1)
            : 0.0;

        $genderCounts = [
            'F' => Enfant::query()->where('sexe', 'F')->count(),
            'M' => Enfant::query()->where('sexe', 'M')->count(),
        ];

        $avgAgeYears = round((float) (Enfant::query()->selectRaw('AVG(TIMESTAMPDIFF(YEAR, date_naissance, CURDATE())) as avg_age')->value('avg_age') ?? 0), 1);

        $ageBandCounts = [
            '2-3 ans' => Enfant::query()->whereBetween('date_naissance', [$today->copy()->subYears(3), $today->copy()->subYears(2)])->count(),
            '4-5 ans' => Enfant::query()->whereBetween('date_naissance', [$today->copy()->subYears(5), $today->copy()->subYears(4)])->count(),
            '6+ ans' => Enfant::query()->whereDate('date_naissance', '<', $today->copy()->subYears(6))->count(),
        ];

        $childrenBySchool = School::query()
            ->leftJoin('school_classes', 'schools.id', '=', 'school_classes.school_id')
            ->leftJoin('enfants', 'school_classes.id', '=', 'enfants.school_class_id')
            ->groupBy('schools.id', 'schools.name')
            ->orderByDesc('children_count')
            ->get([
                'schools.name',
                \DB::raw('COUNT(enfants.id) as children_count'),
            ]);

        $childrenByLevel = SchoolClass::query()
            ->leftJoin('enfants', 'school_classes.id', '=', 'enfants.school_class_id')
            ->selectRaw("COALESCE(NULLIF(school_classes.level, ''), 'Non defini') as level_label")
            ->selectRaw('COUNT(enfants.id) as children_count')
            ->groupBy('level_label')
            ->orderByDesc('children_count')
            ->get();

        $schoolOccupancy = School::query()
            ->leftJoin('school_classes', 'schools.id', '=', 'school_classes.school_id')
            ->leftJoin('enfants', 'school_classes.id', '=', 'enfants.school_class_id')
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

        $totalSchools = School::query()->count();
        $activeClasses = SchoolClass::query()->where('is_active', true)->count();
        $childrenWithoutClass = Enfant::query()->whereNull('school_class_id')->count();

        $presenceTrend = Presence::query()
            ->whereDate('date', '>=', $today->copy()->subDays(6))
            ->selectRaw('DATE(date) as date_key')
            ->selectRaw('COUNT(DISTINCT enfant_id) as present_count')
            ->groupBy('date_key')
            ->orderBy('date_key')
            ->get()
            ->keyBy('date_key');

        $presenceTrendLabels = [];
        $presenceTrendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $today->copy()->subDays($i);
            $key = $day->toDateString();
            $presenceTrendLabels[] = $day->format('d/m');
            $presenceTrendData[] = (int) ($presenceTrend[$key]->present_count ?? 0);
        }

        $incidentStatusCounts = [
            'Ouvert' => Incident::query()->where('workflow_status', Incident::WORKFLOW_OPEN)->count(),
            'Pris en charge' => Incident::query()->where('workflow_status', Incident::WORKFLOW_TAKEN)->count(),
            'En cours' => Incident::query()->where('workflow_status', Incident::WORKFLOW_IN_PROGRESS)->count(),
            'En attente' => Incident::query()->where('workflow_status', Incident::WORKFLOW_WAITING)->count(),
            'Cloture' => Incident::query()->where('workflow_status', Incident::WORKFLOW_CLOSED)->count(),
        ];

        $openIncidents = Incident::query()
            ->where(function ($query) {
                $query->whereNull('workflow_status')
                    ->orWhere('workflow_status', '!=', Incident::WORKFLOW_CLOSED);
            })
            ->count();
        $closedIncidents30d = Incident::query()
            ->where('workflow_status', Incident::WORKFLOW_CLOSED)
            ->whereDate('date', '>=', $today->copy()->subDays(30))
            ->count();

        $avgTakeoverMinutes = round((float) (Incident::query()
            ->whereNotNull('opened_at')
            ->whereNotNull('taken_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, opened_at, taken_at)) as avg_takeover')
            ->value('avg_takeover') ?? 0), 1);

        $avgResolutionMinutes = round((float) (Incident::query()
            ->whereNotNull('opened_at')
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, opened_at, resolved_at)) as avg_resolution')
            ->value('avg_resolution') ?? 0), 1);

        $incidentsByType = Incident::query()
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

        return view('dashboard', compact(
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
            'roomLiveLoad'
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
