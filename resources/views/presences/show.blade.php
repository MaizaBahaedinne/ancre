@extends('adminlte::page')

@section('title', 'Detail Presence')

@section('content_header')
<h1 class="m-0">Detail presence</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-4">Date</dt>
            <dd class="col-sm-8">{{ optional($presence->date)->format('d/m/Y') }}</dd>

            <dt class="col-sm-4">Enfant</dt>
            <dd class="col-sm-8">{{ $presence->enfant?->nom }} {{ $presence->enfant?->prenom }}</dd>

            <dt class="col-sm-4">Parent</dt>
            <dd class="col-sm-8">{{ $presence->enfant?->parent?->nom }} {{ $presence->enfant?->parent?->prenom }}</dd>

            <dt class="col-sm-4">Heure arrivee</dt>
            <dd class="col-sm-8">{{ $presence->heure_arrivee ?: '-' }}</dd>

            <dt class="col-sm-4">Heure depart</dt>
            <dd class="col-sm-8">{{ $presence->heure_depart ?: '-' }}</dd>

            <dt class="col-sm-4">Personne depot</dt>
            <dd class="col-sm-8">{{ $presence->personne_depot ?: '-' }}</dd>

            <dt class="col-sm-4">Personne retrait</dt>
            <dd class="col-sm-8">{{ $presence->personne_retrait ?: '-' }}</dd>

            <dt class="col-sm-4">Remarque</dt>
            <dd class="col-sm-8">{{ $presence->remarque ?: '-' }}</dd>
        </dl>
    </div>
    <div class="card-footer">
        <a href="{{ route('presences.edit', $presence) }}" class="btn btn-warning">Modifier</a>
        <a href="{{ route('presences.index') }}" class="btn btn-secondary">Retour</a>
    </div>
</div>
@stop
