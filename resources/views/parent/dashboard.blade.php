@extends('adminlte::page')

@section('title', 'Mon espace parent')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="m-0">Mon espace parent</h1>
        <small class="text-muted">Suivi des incidents, des enfants et des informations partagees</small>
    </div>
    <span class="badge badge-info p-2">{{ $childrenCount ?? 0 }} enfant(s)</span>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-lg-4 col-12">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $childrenCount ?? 0 }}</h3>
                <p>Mes enfants</p>
            </div>
            <div class="icon"><i class="fas fa-children"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-12">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $notifiedIncidentsCount ?? 0 }}</h3>
                <p>Tickets incidents visibles</p>
            </div>
            <div class="icon"><i class="fas fa-triangle-exclamation"></i></div>
        </div>
    </div>
    <div class="col-lg-4 col-12">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $parent?->user?->email ? 'OK' : '-' }}</h3>
                <p>Compte parent</p>
            </div>
            <div class="icon"><i class="fas fa-user-shield"></i></div>
        </div>
    </div>
</div>

<div class="card card-outline card-primary">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="h5 mb-1">Inscription aux activites</h3>
            <p class="text-muted mb-0">Inscrivez vos enfants aux activites et suivez la validation selon paiement et capacite.</p>
        </div>
        <a href="{{ route('parent.activites.index') }}" class="btn btn-primary">Voir les activites</a>
    </div>
</div>

<div class="card card-outline card-danger">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Tickets d'incidents de mes enfants</h3>
        <span class="text-muted small">Affiches uniquement si "Informer le parent" est active</span>
    </div>
    <div class="card-body">
        @if(($incidentTickets ?? collect())->isEmpty())
            <div class="alert alert-info mb-0">Aucun ticket incident a afficher pour le moment.</div>
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
                            <div class="text-muted small mb-3">
                                Date: {{ optional($incident->date)->format('d/m/Y') }}<br>
                                Personnel responsable: {{ $incident->responsablePersonnel ? $incident->responsablePersonnel->prenom.' '.$incident->responsablePersonnel->nom : '-' }}
                            </div>
                            <div class="modern-richtext-output mb-3">{!! \Illuminate\Support\Str::limit(strip_tags($incident->description), 140) !!}</div>
                            <a href="{{ route('parent.incidents.show', $incident) }}" class="btn btn-sm btn-primary">Voir ticket complet</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@stop