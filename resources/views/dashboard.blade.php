@extends('adminlte::page')

@section('title', 'Dashboard')

@section('css')
<style>
    .dashboard-grid {
        --bs-gutter-y: 1rem;
    }

    .dashboard-kpi-card {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 124px;
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 18px;
        background: #fff;
        padding: 1rem;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.05);
    }

    .dashboard-kpi-card .kpi-label {
        margin: 0;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #64748b;
    }

    .dashboard-kpi-card .kpi-value {
        margin: 0.35rem 0 0;
        font-size: 1.9rem;
        line-height: 1;
        font-weight: 800;
        color: #0f172a;
    }

    .dashboard-kpi-card .kpi-meta {
        margin-top: 0.5rem;
        font-size: 0.86rem;
        color: #475569;
    }

    .dashboard-kpi-icon {
        position: absolute;
        top: 0.85rem;
        right: 0.9rem;
        width: 2rem;
        height: 2rem;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(148, 163, 184, 0.15);
        color: #0f172a;
    }

    .dashboard-card {
        border-radius: 18px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .dashboard-card .card-header {
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    }

    .dashboard-section-title {
        margin: 0.2rem 0 0.7rem;
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
    }

    .chart-wrap {
        position: relative;
        height: 260px;
    }

    .chart-wrap.chart-wrap-sm {
        height: 220px;
    }

    .chart-wrap canvas {
        width: 100% !important;
        height: 100% !important;
    }

    @media (max-width: 991.98px) {
        .chart-wrap,
        .chart-wrap.chart-wrap-sm {
            height: 230px;
        }
    }

    @media (max-width: 575.98px) {
        .dashboard-kpi-card {
            min-height: 112px;
            padding: 0.85rem;
        }

        .dashboard-kpi-card .kpi-value {
            font-size: 1.55rem;
        }
    }
</style>
@stop

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0">Tableau de Bord</h1>
            <small class="text-muted">Pilotage operationnel en temps reel</small>
        </div>
        <span class="badge badge-primary p-2">
            Roles: {{ auth()->user()->getRoleNames()->join(', ') ?: 'Aucun role' }}
        </span>
    </div>
@stop

@section('content')
    @if(isset($incidentTickets))
        <div class="card card-outline card-danger">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Tickets d'incidents a consulter</h3>
                <span class="badge badge-danger p-2">{{ $notifiedIncidentsCount ?? 0 }} ticket(s)</span>
            </div>
            <div class="card-body">
                @if(($incidentTickets ?? collect())->isEmpty())
                    <div class="alert alert-info mb-0">Aucun incident a afficher pour le moment.</div>
                @else
                    <div class="row g-3">
                        @foreach($incidentTickets as $incident)
                            <div class="col-12 col-lg-6">
                                <div class="border rounded-4 p-3 h-100 bg-white">
                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                        <div>
                                            <div class="fw-bold">{{ $incident->type_incident }}</div>
                                            <div class="text-muted small">{{ $incident->enfant?->prenom }} {{ $incident->enfant?->nom }}</div>
                                        </div>
                                        <span class="badge bg-{{ $incident->workflowBadgeClass() }}">{{ \App\Models\Incident::WORKFLOW_OPTIONS[$incident->workflow_status] ?? ucfirst($incident->workflow_status) }}</span>
                                    </div>
                                    <div class="small text-muted mb-3">
                                        Date: {{ optional($incident->date)->format('d/m/Y') }}<br>
                                        Responsable: {{ $incident->responsablePersonnel ? $incident->responsablePersonnel->prenom.' '.$incident->responsablePersonnel->nom : '-' }}
                                    </div>
                                    <div class="modern-richtext-output mb-3">{!! \Illuminate\Support\Str::limit(strip_tags($incident->description), 140) !!}</div>
                                    <a href="{{ route('parent.incidents.show', $incident) }}" class="btn btn-sm btn-primary">Voir ticket</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @else
        <p class="dashboard-section-title">Synthese instantanee</p>
        <div class="row dashboard-grid g-3 mb-1">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="dashboard-kpi-card h-100">
                    <span class="dashboard-kpi-icon"><i class="fas fa-children"></i></span>
                    <p class="kpi-label">Enfants inscrits</p>
                    <p class="kpi-value">{{ $totalEnfants }}</p>
                    <div class="kpi-meta">Population active</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="dashboard-kpi-card h-100">
                    <span class="dashboard-kpi-icon"><i class="fas fa-calendar-check"></i></span>
                    <p class="kpi-label">Taux de presence</p>
                    <p class="kpi-value">{{ $presenceRateToday }}%</p>
                    <div class="kpi-meta">Aujourd'hui: {{ $presentToday }} presents</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="dashboard-kpi-card h-100">
                    <span class="dashboard-kpi-icon"><i class="fas fa-door-open"></i></span>
                    <p class="kpi-label">Occupation des salles</p>
                    <p class="kpi-value">{{ $roomsOccupiedNow }}/{{ $totalRooms }}</p>
                    <div class="kpi-meta">En temps reel</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="dashboard-kpi-card h-100">
                    <span class="dashboard-kpi-icon"><i class="fas fa-triangle-exclamation"></i></span>
                    <p class="kpi-label">Incidents ouverts</p>
                    <p class="kpi-value">{{ $openIncidents }}</p>
                    <div class="kpi-meta">A suivre immediatement</div>
                </div>
            </div>
        </div>

        <div class="row dashboard-grid g-3 mb-3">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="dashboard-kpi-card h-100">
                    <span class="dashboard-kpi-icon"><i class="fas fa-school"></i></span>
                    <p class="kpi-label">Ecoles</p>
                    <p class="kpi-value">{{ $totalSchools }}</p>
                    <div class="kpi-meta">Etablissements suivis</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="dashboard-kpi-card h-100">
                    <span class="dashboard-kpi-icon"><i class="fas fa-layer-group"></i></span>
                    <p class="kpi-label">Classes actives</p>
                    <p class="kpi-value">{{ $activeClasses }}</p>
                    <div class="kpi-meta">Niveaux en operation</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="dashboard-kpi-card h-100">
                    <span class="dashboard-kpi-icon"><i class="fas fa-hourglass-half"></i></span>
                    <p class="kpi-label">Age moyen</p>
                    <p class="kpi-value">{{ $avgAgeYears }} ans</p>
                    <div class="kpi-meta">Moyenne de la structure</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="dashboard-kpi-card h-100">
                    <span class="dashboard-kpi-icon"><i class="fas fa-user-slash"></i></span>
                    <p class="kpi-label">Enfants sans classe</p>
                    <p class="kpi-value">{{ $childrenWithoutClass }}</p>
                    <div class="kpi-meta">A regulariser</div>
                </div>
            </div>
        </div>

        <p class="dashboard-section-title">Enfants et presence</p>
        <div class="row dashboard-grid g-3">
            <div class="col-lg-4 col-12">
                <div class="card card-outline card-primary dashboard-card h-100">
                    <div class="card-header"><h3 class="card-title mb-0">Repartition par sexe</h3></div>
                    <div class="card-body"><div class="chart-wrap chart-wrap-sm"><canvas id="genderChart"></canvas></div></div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="card card-outline card-info dashboard-card h-100">
                    <div class="card-header"><h3 class="card-title mb-0">Tranches d'age</h3></div>
                    <div class="card-body"><div class="chart-wrap chart-wrap-sm"><canvas id="ageBandChart"></canvas></div></div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="card card-outline card-success dashboard-card h-100">
                    <div class="card-header"><h3 class="card-title mb-0">Presence sur 7 jours</h3></div>
                    <div class="card-body"><div class="chart-wrap chart-wrap-sm"><canvas id="presenceTrendChart"></canvas></div></div>
                </div>
            </div>
        </div>

        <div class="row dashboard-grid g-3">
            <div class="col-lg-8 col-12">
                <div class="card card-outline card-primary dashboard-card h-100">
                    <div class="card-header"><h3 class="card-title mb-0">Cartographie des enfants par ecole</h3></div>
                    <div class="card-body"><div class="chart-wrap"><canvas id="childrenBySchoolChart"></canvas></div></div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="card card-outline card-secondary dashboard-card h-100">
                    <div class="card-header"><h3 class="card-title mb-0">Par niveau</h3></div>
                    <div class="card-body"><div class="chart-wrap"><canvas id="childrenByLevelChart"></canvas></div></div>
                </div>
            </div>
        </div>

        <p class="dashboard-section-title">Salles et ecoles</p>
        <div class="row dashboard-grid g-3">
            <div class="col-lg-6 col-12">
                <div class="card card-outline card-warning dashboard-card h-100">
                    <div class="card-header"><h3 class="card-title mb-0">Salles - occupation temps reel</h3></div>
                    <div class="card-body p-0">
                        <div class="p-3 border-bottom bg-light">
                            <strong>Taux d'occupation: {{ $roomOccupancyRate }}%</strong>
                            <span class="ml-3">Maintenance: {{ $roomsInMaintenance }}</span>
                            <span class="ml-3">Indisponibles: {{ $roomsUnavailable }}</span>
                            <span class="ml-3">Reservees: {{ $roomsReserved }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Salle</th>
                                        <th>Charge</th>
                                        <th>Taux</th>
                                        <th>Etat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($roomLiveLoad as $room)
                                        <tr>
                                            <td>{{ $room['name'] }}</td>
                                            <td>{{ $room['participants'] }} / {{ $room['capacity'] }}</td>
                                            <td>{{ $room['load_rate'] }}%</td>
                                            <td>
                                                @if($room['is_overloaded'])
                                                    <span class="badge badge-danger">Surcharge</span>
                                                @elseif($room['participants'] > 0)
                                                    <span class="badge badge-success">Occupee</span>
                                                @else
                                                    <span class="badge badge-secondary">Libre</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center">Aucune salle configuree.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="card card-outline card-info dashboard-card h-100">
                    <div class="card-header"><h3 class="card-title mb-0">Ecoles - capacite et occupation</h3></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Ecole</th>
                                        <th>Classes</th>
                                        <th>Enfants</th>
                                        <th>Capacite</th>
                                        <th>Occupation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($schoolOccupancy as $school)
                                        <tr>
                                            <td>{{ $school['name'] }}</td>
                                            <td>{{ $school['classes_count'] }}</td>
                                            <td>{{ $school['children_count'] }}</td>
                                            <td>{{ $school['total_capacity'] }}</td>
                                            <td>
                                                @if(is_null($school['occupancy_rate']))
                                                    -
                                                @else
                                                    {{ $school['occupancy_rate'] }}%
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center">Aucune ecole disponible.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p class="dashboard-section-title">Incidents et activites</p>
        <div class="row dashboard-grid g-3">
            <div class="col-lg-8 col-12">
                <div class="card card-outline card-danger dashboard-card h-100">
                    <div class="card-header"><h3 class="card-title mb-0">Incidents par statut</h3></div>
                    <div class="card-body"><div class="chart-wrap chart-wrap-sm"><canvas id="incidentStatusChart"></canvas></div></div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="card card-outline card-danger dashboard-card h-100">
                    <div class="card-header"><h3 class="card-title mb-0">Delais incidents</h3></div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Prise en charge moyenne:</strong> {{ $avgTakeoverMinutes }} min</p>
                        <p class="mb-2"><strong>Resolution moyenne:</strong> {{ $avgResolutionMinutes }} min</p>
                        <p class="mb-0"><strong>Clotures (30j):</strong> {{ $closedIncidents30d }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row dashboard-grid g-3">
            <div class="col-lg-6 col-12">
                <div class="card card-outline card-secondary dashboard-card h-100">
                    <div class="card-header"><h3 class="card-title mb-0">Incidents par type</h3></div>
                    <div class="card-body"><div class="chart-wrap chart-wrap-sm"><canvas id="incidentTypeChart"></canvas></div></div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="card card-outline card-success dashboard-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Activites du jour</h3>
                        <span class="badge badge-success">{{ $inProgressActivities->count() }} en cours</span>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Total:</strong> {{ $todayActivities->count() }} | <strong>Terminees:</strong> {{ $completedActivities }} | <strong>Participation moyenne:</strong> {{ $avgParticipationRate }}%</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Activite</th>
                                        <th>Salle</th>
                                        <th>Horaire</th>
                                        <th>Participants</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($todayActivities as $activity)
                                        <tr>
                                            <td>{{ $activity->titre }}</td>
                                            <td>{{ $activity->salle?->nom ?? '-' }}</td>
                                            <td>{{ $activity->heure_debut ?? $activity->heure ?? '-' }} - {{ $activity->heure_fin ?? '-' }}</td>
                                            <td>{{ $activity->participations_count }}@if($activity->capacite) / {{ $activity->capacite }} @endif</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center">Aucune activite aujourd'hui.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const defaultAnimation = {
        duration: 1200,
        easing: 'easeOutQuart',
    };

    const staggeredAnimation = {
        duration: 1200,
        easing: 'easeOutCubic',
        delay(context) {
            return context.type === 'data' ? context.dataIndex * 70 : 0;
        },
    };

    const withBaseOptions = (extra = {}) => ({
        responsive: true,
        maintainAspectRatio: false,
        animation: defaultAnimation,
        ...extra,
    });

    const makeChart = (id, config) => {
        const canvas = document.getElementById(id);
        if (canvas) {
            new Chart(canvas, config);
        }
    };

    makeChart('genderChart', {
        type: 'doughnut',
        data: {
            labels: ['Filles', 'Garcons'],
            datasets: [{
                data: [{{ $genderCounts['F'] ?? 0 }}, {{ $genderCounts['M'] ?? 0 }}],
                backgroundColor: ['#ec4899', '#3b82f6'],
            }],
        },
        options: withBaseOptions()
    });

    makeChart('ageBandChart', {
        type: 'bar',
        data: {
            labels: @json(array_keys($ageBandCounts ?? [])),
            datasets: [{
                label: 'Enfants',
                data: @json(array_values($ageBandCounts ?? [])),
                backgroundColor: '#14b8a6',
            }],
        },
        options: withBaseOptions({
            animation: staggeredAnimation,
            plugins: { legend: { display: false } },
        })
    });

    makeChart('presenceTrendChart', {
        type: 'line',
        data: {
            labels: @json($presenceTrendLabels ?? []),
            datasets: [{
                label: 'Presents',
                data: @json($presenceTrendData ?? []),
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22, 163, 74, 0.15)',
                fill: true,
                tension: 0.3,
            }],
        },
        options: withBaseOptions({ plugins: { legend: { display: false } } })
    });

    makeChart('childrenBySchoolChart', {
        type: 'bar',
        data: {
            labels: @json(($childrenBySchool ?? collect())->pluck('name')->all()),
            datasets: [{
                label: 'Enfants',
                data: @json(($childrenBySchool ?? collect())->pluck('children_count')->map(fn($v) => (int) $v)->all()),
                backgroundColor: '#0ea5e9',
            }],
        },
        options: withBaseOptions({
            animation: staggeredAnimation,
            plugins: { legend: { display: false } },
            scales: { x: { ticks: { maxRotation: 45, minRotation: 25 } } }
        })
    });

    makeChart('childrenByLevelChart', {
        type: 'bar',
        data: {
            labels: @json(($childrenByLevel ?? collect())->pluck('level_label')->all()),
            datasets: [{
                label: 'Enfants',
                data: @json(($childrenByLevel ?? collect())->pluck('children_count')->map(fn($v) => (int) $v)->all()),
                backgroundColor: '#6366f1',
            }],
        },
        options: withBaseOptions({
            animation: staggeredAnimation,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
        })
    });

    makeChart('incidentStatusChart', {
        type: 'bar',
        data: {
            labels: @json(array_keys($incidentStatusCounts ?? [])),
            datasets: [{
                label: 'Incidents',
                data: @json(array_values($incidentStatusCounts ?? [])),
                backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#64748b', '#22c55e'],
            }],
        },
        options: withBaseOptions({
            animation: staggeredAnimation,
            plugins: { legend: { display: false } },
        })
    });

    makeChart('incidentTypeChart', {
        type: 'pie',
        data: {
            labels: @json(($incidentsByType ?? collect())->pluck('type_incident')->all()),
            datasets: [{
                data: @json(($incidentsByType ?? collect())->pluck('incidents_count')->map(fn($v) => (int) $v)->all()),
                backgroundColor: ['#ef4444', '#f97316', '#f59e0b', '#84cc16', '#14b8a6', '#3b82f6'],
            }],
        },
        options: withBaseOptions()
    });
</script>
@stop
