@extends('adminlte::page')

@section('title', 'Detail Package')

@section('content_header')
<h1 class="m-0">Detail package</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-4">Nom</dt>
            <dd class="col-sm-8">{{ $package->nom }}</dd>

            <dt class="col-sm-4">Frais scolarite</dt>
            <dd class="col-sm-8">{{ number_format((float) $package->frais_scolarite, 2, ',', ' ') }} TND</dd>

            <dt class="col-sm-4">Frais dejeuner</dt>
            <dd class="col-sm-8">{{ number_format((float) $package->frais_dejeuner, 2, ',', ' ') }} TND</dd>

            <dt class="col-sm-4">Frais activite</dt>
            <dd class="col-sm-8">{{ number_format((float) $package->frais_activite, 2, ',', ' ') }} TND</dd>

            <dt class="col-sm-4">Total mensuel</dt>
            <dd class="col-sm-8"><strong>{{ number_format((float) $package->total_mensuel, 2, ',', ' ') }} TND</strong></dd>

            <dt class="col-sm-4">Statut</dt>
            <dd class="col-sm-8">{{ $package->is_active ? 'Actif' : 'Inactif' }}</dd>
        </dl>
    </div>
    <div class="card-footer">
        <a href="{{ route('packages.edit', $package) }}" class="btn btn-warning">Modifier</a>
        <a href="{{ route('packages.index') }}" class="btn btn-secondary">Retour</a>
    </div>
</div>
@stop