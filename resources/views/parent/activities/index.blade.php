@extends('adminlte::page')

@section('title', 'Activites disponibles')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="m-0">Activites disponibles</h1>
        <small class="text-muted">Inscrivez vos enfants aux activites de la garderie</small>
    </div>
    <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-secondary">Retour espace parent</a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-3">
    @forelse($activites as $activite)
        @php
            $myRegs = ($myRegistrationsByActivity[$activite->id] ?? collect());
            $isRegistered = $myRegs->isNotEmpty();
            $capacityLabel = $activite->capacite ? ($activite->validated_registrations_count.' / '.$activite->capacite) : ($activite->validated_registrations_count.' / Illimite');
        @endphp
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">{{ $activite->titre }}</h3>
                    <span class="badge badge-{{ $isRegistered ? 'success' : 'secondary' }}">{{ $isRegistered ? 'Inscription enregistree' : 'A inscrire' }}</span>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Date:</strong> {{ optional($activite->date)->format('d/m/Y') ?: '-' }}</p>
                    <p class="mb-2"><strong>Heure:</strong> {{ $activite->heure_debut ?: $activite->heure ?: '-' }} @if($activite->heure_fin) - {{ $activite->heure_fin }} @endif</p>
                    <p class="mb-2"><strong>Salle:</strong> {{ $activite->salle?->nom ?: '-' }}</p>
                    <p class="mb-2"><strong>Frais:</strong> {{ $activite->frais_participation !== null ? number_format((float) $activite->frais_participation, 2, ',', ' ').' TND' : 'Aucun frais' }}</p>
                    <p class="mb-0"><strong>Capacite validee:</strong> {{ $capacityLabel }}</p>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="{{ route('parent.activites.show', $activite) }}" class="btn btn-primary btn-sm">Inscrire un enfant</a>
                    <a href="{{ route('parent.activites.show', $activite) }}" class="btn btn-outline-secondary btn-sm">Voir details</a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info mb-0">Aucune activite disponible pour le moment.</div>
        </div>
    @endforelse
</div>
@stop
