@extends('adminlte::page')

@section('title', 'Ticket Incident')

@section('content_header')
<h1 class="m-0">Ticket incident</h1>
@stop

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                    <span class="badge bg-{{ $incident->workflowBadgeClass() }} px-3 py-2">{{ \App\Models\Incident::WORKFLOW_OPTIONS[$incident->workflow_status] ?? ucfirst($incident->workflow_status) }}</span>
                    <span class="text-muted">Visible au parent</span>
                </div>
                <h2 class="h4 mb-1">{{ $incident->type_incident }}</h2>
                <div class="text-muted">{{ $incident->enfant?->prenom }} {{ $incident->enfant?->nom }} · {{ optional($incident->date)->format('d/m/Y') }}</div>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Retour au tableau de bord</a>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="border rounded-4 p-3 h-100">
                    <div class="text-muted small">Date d'ouverture</div>
                    <div class="fw-bold">{{ optional($incident->opened_at)->format('d/m/Y H:i') ?: '-' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded-4 p-3 h-100">
                    <div class="text-muted small">Date et heure de resolution</div>
                    <div class="fw-bold">{{ optional($incident->resolved_at)->format('d/m/Y H:i') ?: '-' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded-4 p-3 h-100">
                    <div class="text-muted small">Personnel responsable</div>
                    <div class="fw-bold">{{ $incident->responsablePersonnel ? $incident->responsablePersonnel->prenom.' '.$incident->responsablePersonnel->nom.' - '.$incident->responsablePersonnel->fonction : '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title mb-0">Details du ticket</h3></div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-4">Description</dt><dd class="col-sm-8 modern-richtext-output">{!! $incident->description !!}</dd>
            <dt class="col-sm-4">Action realisee</dt><dd class="col-sm-8 modern-richtext-output">{!! $incident->action_realisee ?: '<p>-</p>' !!}</dd>
            <dt class="col-sm-4">Temps de prise en charge</dt><dd class="col-sm-8">{{ $incident->open_to_taken_minutes !== null ? $incident->open_to_taken_minutes.' min' : '-' }}</dd>
            <dt class="col-sm-4">Temps pour la resolution</dt><dd class="col-sm-8">{{ $incident->open_to_resolved_minutes !== null ? $incident->open_to_resolved_minutes.' min' : '-' }}</dd>
        </dl>
    </div>
</div>
@stop