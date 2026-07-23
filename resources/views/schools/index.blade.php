@extends('adminlte::page')

@section('title', 'Ecoles')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Ecoles</h1>
    <a href="{{ route('schools.create') }}" class="btn btn-primary">Ajouter une ecole</a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body modern-table-card">
        <div class="table-responsive">
            <table class="table table-striped table-bordered js-data-table nowrap">
                <thead><tr><th>Nom</th><th>Adresse</th><th>Telephone</th><th>Directeur</th><th>Contact directeur</th><th>Classes</th><th class="no-sort">Actions</th></tr></thead>
                <tbody>
                @forelse($schools as $school)
                    <tr>
                        <td>{{ $school->name }}</td>
                        <td>{{ collect([$school->address_route, $school->address_street, $school->address_postal_code, $school->address_city])->filter()->join(', ') ?: ($school->city ?: '-') }}</td>
                        <td>{{ $school->phone ?: '-' }}</td>
                        <td>{{ $school->director_name ?: '-' }}</td>
                        <td>{{ $school->director_contact ?: '-' }}</td>
                        <td>{{ $school->classes_count }}</td>
                        <td>
                            @canany(['schools.view', 'schools.update', 'schools.delete'])
                                <div class="modern-action-group">
                                    @can('schools.view')
                                        <a href="{{ route('schools.show', $school) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Voir</span></a>
                                    @endcan
                                    @can('schools.update')
                                        <a href="{{ route('schools.edit', $school) }}" class="modern-action-btn is-edit"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                    @endcan
                                    @can('schools.delete')
                                        <form method="POST" action="{{ route('schools.destroy', $school) }}" class="modern-inline-form" onsubmit="return confirm('Supprimer cette ecole ?')">@csrf @method('DELETE')<button class="modern-action-btn is-delete" type="submit"><i class="fa-solid fa-trash"></i><span>Supprimer</span></button></form>
                                    @endcan
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endcanany
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">Aucune ecole.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop