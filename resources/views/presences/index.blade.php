@extends('adminlte::page')

@section('title', 'Presences')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Presences</h1>
    <a href="{{ route('presences.create') }}" class="btn btn-primary">Enregistrer presence</a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card card-outline card-info">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            @if($isArchiveScope)
                <strong>Affichage:</strong> Archives des presences (avant {{ $today->format('d/m/Y') }})
            @else
                <strong>Affichage:</strong> Presences d'aujourd'hui ({{ $today->format('d/m/Y') }})
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('presences.index') }}" class="btn btn-{{ $isArchiveScope ? 'outline-primary' : 'primary' }} btn-sm">Aujourd'hui</a>
            <a href="{{ route('presences.index', ['scope' => 'archive']) }}" class="btn btn-{{ $isArchiveScope ? 'primary' : 'outline-primary' }} btn-sm">Voir archives</a>
        </div>
    </div>
</div>

@if($isArchiveScope)
<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="scope" value="archive">
            <div class="col-md-3">
                <label>Date</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-2">
                <label>Mois</label>
                <input type="number" min="1" max="12" name="mois" class="form-control" value="{{ request('mois') }}">
            </div>
            <div class="col-md-2">
                <label>Annee</label>
                <input type="number" min="2000" max="2100" name="annee" class="form-control" value="{{ request('annee') }}">
            </div>
            <div class="col-md-5 d-flex gap-2">
                <button class="btn btn-primary">Filtrer archives</button>
                <a href="{{ route('presences.index', ['scope' => 'archive']) }}" class="btn btn-outline-secondary">Reinitialiser</a>
            </div>
        </form>
    </div>
</div>
@endif

<div class="card">
    <div class="card-body modern-table-card">
        <div class="table-responsive">
            <table class="table table-striped table-bordered js-data-table nowrap">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Enfant</th>
                    <th>Arrivee</th>
                    <th>Depart</th>
                    <th>Depot</th>
                    <th>Retrait</th>
                    <th width="210" class="no-sort">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($presences as $presence)
                    <tr>
                        <td>{{ optional($presence->date)->format('d/m/Y') }}</td>
                        <td>{{ $presence->enfant?->nom }} {{ $presence->enfant?->prenom }}</td>
                        <td>{{ $presence->heure_arrivee ?: '-' }}</td>
                        <td>{{ $presence->heure_depart ?: '-' }}</td>
                        <td>{{ $presence->personne_depot ?: '-' }}</td>
                        <td>{{ $presence->personne_retrait ?: '-' }}</td>
                        <td>
                            <div class="modern-action-group">
                                <a href="{{ route('presences.show', $presence) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Voir</span></a>
                                <a href="{{ route('presences.edit', $presence) }}" class="modern-action-btn is-edit"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                <form method="POST" action="{{ route('presences.destroy', $presence) }}" class="modern-inline-form" onsubmit="return confirm('Supprimer cette presence ?')">
                                @csrf
                                @method('DELETE')
                                    <button class="modern-action-btn is-delete" type="submit"><i class="fa-solid fa-trash"></i><span>Supprimer</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            {{ $isArchiveScope ? 'Aucune presence archivee pour ce filtre.' : 'Aucune presence enregistree pour aujourd\'hui.' }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
