@extends('adminlte::page')

@section('title', 'Salles')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Salles</h1>
    <a href="{{ route('salles.create') }}" class="btn btn-primary">Ajouter salle</a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-body modern-table-card">
        <div class="table-responsive">
            <table class="table table-striped table-bordered js-data-table nowrap">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Etage</th>
                        <th>Capacite</th>
                        <th>Equipements</th>
                        <th>Statut</th>
                        <th>Responsable</th>
                        <th>Activites</th>
                        <th class="no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salles as $salle)
                        <tr>
                            <td>{{ $salle->nom }}</td>
                            <td>{{ $salle->etage }}</td>
                            <td>{{ $salle->capacite }}</td>
                            <td>{{ collect($salle->equipements ?? [])->map(fn ($item) => \App\Models\Salle::EQUIPEMENT_OPTIONS[$item] ?? $item)->join(', ') ?: '-' }}</td>
                            <td><span class="badge bg-{{ $salle->statut === 'disponible' ? 'success' : ($salle->statut === 'reservee' ? 'warning text-dark' : 'secondary') }}">{{ \App\Models\Salle::STATUT_OPTIONS[$salle->statut] ?? $salle->statut }}</span></td>
                            <td>{{ $salle->responsablePersonnel ? $salle->responsablePersonnel->prenom.' '.$salle->responsablePersonnel->nom : '-' }}</td>
                            <td>{{ $salle->activites_count }}</td>
                            <td>
                                @canany(['rooms.view', 'rooms.update', 'rooms.delete'])
                                    <div class="modern-action-group">
                                        @can('rooms.view')
                                            <a href="{{ route('salles.show', $salle) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Voir</span></a>
                                        @endcan
                                        @can('rooms.update')
                                            <a href="{{ route('salles.edit', $salle) }}" class="modern-action-btn is-edit"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                        @endcan
                                        @can('rooms.delete')
                                            <form method="POST" action="{{ route('salles.destroy', $salle) }}" class="modern-inline-form" onsubmit="return confirm('Supprimer cette salle ?')">
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
                        <tr>
                            <td colspan="8" class="text-center">Aucune salle.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
