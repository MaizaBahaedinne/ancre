@extends('adminlte::page')

@section('title', 'Incidents')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="m-0">{{ ($scope ?? 'open') === 'closed' ? 'Tickets clotures' : 'Tickets incidents actifs' }}</h1>
        <small class="text-muted">{{ ($scope ?? 'open') === 'closed' ? 'Archive des incidents resolus' : 'Affichage optimise sans les tickets clotures' }}</small>
    </div>
    <a href="{{ route('incidents.create') }}" class="btn btn-primary">Declarer incident</a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Tickets actifs</div>
                <div class="display-6 fw-semibold">{{ $stats['active'] ?? 0 }}</div>
                <a href="{{ route('incidents.index', array_filter(['scope' => 'open', 'enfant_id' => $enfantId ?? null])) }}" class="small">Voir les tickets actifs</a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">En cours</div>
                <div class="display-6 fw-semibold">{{ $stats['in_progress'] ?? 0 }}</div>
                <div class="small text-muted">Suivi actuellement en traitement</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">En attente</div>
                <div class="display-6 fw-semibold">{{ $stats['waiting'] ?? 0 }}</div>
                <div class="small text-muted">En attente d'action ou de retour</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <a href="{{ route('incidents.index', array_filter(['scope' => 'closed', 'enfant_id' => $enfantId ?? null])) }}" class="text-decoration-none text-reset d-block h-100">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Tickets clotures</div>
                    <div class="display-6 fw-semibold">{{ $stats['closed'] ?? 0 }}</div>
                    <div class="small">Ouvrir l'archive des tickets clotures</div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body modern-table-card">
        <div class="table-responsive">
            <table class="table table-striped table-bordered js-data-table nowrap">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Enfant</th>
                        <th>Type</th>
                        <th>Statut ticket</th>
                        <th>Personne en charge</th>
                        <th>Parent informe</th>
                        <th>Description</th>
                        <th width="210" class="no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incidents as $incident)
                        <tr>
                            <td>{{ optional($incident->date)->format('d/m/Y') }}</td>
                            <td>{{ $incident->enfant?->nom }} {{ $incident->enfant?->prenom }}</td>
                            <td>{{ $incident->type_incident }}</td>
                            <td><span class="badge bg-{{ $incident->workflowBadgeClass() }}">{{ \App\Models\Incident::WORKFLOW_OPTIONS[$incident->workflow_status] ?? ucfirst($incident->workflow_status) }}</span></td>
                            <td>{{ $incident->responsablePersonnel ? $incident->responsablePersonnel->prenom.' '.$incident->responsablePersonnel->nom : '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $incident->notify_parent ? 'success' : 'secondary' }}">
                                    {{ $incident->parentNotificationLabel() }}
                                </span>
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit(strip_tags($incident->description), 60) }}</td>
                            <td>
                                @canany(['incidents.view', 'incidents.update', 'incidents.delete'])
                                    <div class="modern-action-group">
                                        @can('incidents.view')
                                            <a href="{{ route('incidents.show', $incident) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Voir</span></a>
                                        @endcan
                                        @can('incidents.update')
                                            <a href="{{ route('incidents.edit', $incident) }}" class="modern-action-btn is-edit"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                        @endcan
                                        @can('incidents.delete')
                                            <form method="POST" action="{{ route('incidents.destroy', $incident) }}" class="modern-inline-form" onsubmit="return confirm('Supprimer cet incident ?')">
                                            @csrf
                                            @method('DELETE')
                                                <button class="modern-action-btn is-delete" type="submit"><i class="fa-solid fa-trash"></i><span>Supprimer</span></button>
                                            </form>
                                        @endcan
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endcanany
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">Aucun incident.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
