@extends('adminlte::page')

@section('title', 'Packages')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Packages</h1>
    <a href="{{ route('packages.create') }}" class="btn btn-primary">Nouveau package</a>
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
                    <th>Scolarite</th>
                    <th>Dejeuner</th>
                    <th>Activite</th>
                    <th>Total mensuel</th>
                    <th>Statut</th>
                    <th width="210" class="no-sort">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($packages as $package)
                    <tr>
                        <td>{{ $package->nom }}</td>
                        <td>{{ number_format((float) $package->frais_scolarite, 2, ',', ' ') }} TND</td>
                        <td>{{ number_format((float) $package->frais_dejeuner, 2, ',', ' ') }} TND</td>
                        <td>{{ number_format((float) $package->frais_activite, 2, ',', ' ') }} TND</td>
                        <td><strong>{{ number_format((float) $package->total_mensuel, 2, ',', ' ') }} TND</strong></td>
                        <td>
                            <span class="badge badge-{{ $package->is_active ? 'success' : 'secondary' }}">
                                {{ $package->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td>
                            <div class="modern-action-group">
                                <a href="{{ route('packages.show', $package) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Voir</span></a>
                                <a href="{{ route('packages.edit', $package) }}" class="modern-action-btn is-edit"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                <form method="POST" action="{{ route('packages.destroy', $package) }}" class="modern-inline-form" onsubmit="return confirm('Supprimer ce package ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="modern-action-btn is-delete" type="submit"><i class="fa-solid fa-trash"></i><span>Supprimer</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Aucun package.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop