@extends('adminlte::page')

@section('title', 'Parents')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Parents</h1>
        <a href="{{ route('parents.create') }}" class="btn btn-primary">Ajouter un parent</a>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Parents</div><div class="display-6 fw-semibold">{{ $stats['total'] ?? 0 }}</div></div></div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Inscrits (annee en cours)</div><div class="display-6 fw-semibold">{{ $stats['inscribed_current_year'] ?? 0 }}</div></div></div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Non inscrits (annee en cours)</div><div class="display-6 fw-semibold">{{ $stats['not_inscribed_current_year'] ?? 0 }}</div></div></div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Comptes verifies</div><div class="display-6 fw-semibold">{{ $stats['verified_accounts'] ?? 0 }}</div><div class="small text-muted">Non verifies: {{ $stats['not_verified_accounts'] ?? 0 }}</div></div></div>
        </div>
    </div>

    <div class="alert alert-light border d-flex align-items-center gap-2" role="alert">
        <i class="fa-solid fa-calendar-check text-primary"></i>
        <span>
            @if($activeAcademicYearLabel)
                Annee scolaire en cours: <strong>{{ $activeAcademicYearLabel }}</strong>
            @else
                Aucune annee scolaire active n'est definie.
            @endif
        </span>
    </div>

    <div class="card">
        <div class="card-body modern-table-card">
            <div class="table-responsive">
                <table class="table table-striped table-bordered js-data-table nowrap">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prenom</th>
                            <th>Telephone</th>
                            <th>Email</th>
                            <th>Urgence</th>
                            <th>Inscription annee en cours</th>
                            <th>Compte verifie</th>
                            <th width="200" class="no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parents as $parent)
                            <tr>
                                <td>{{ $parent->nom }}</td>
                                <td>{{ $parent->prenom }}</td>
                                <td>{{ $parent->telephone }}</td>
                                <td>{{ $parent->email }}</td>
                                <td>{{ $parent->contact_urgence }}</td>
                                <td>
                                    @if($activeAcademicYearLabel && $parent->is_inscribed_current_year)
                                        <span class="badge bg-success">Oui</span>
                                    @elseif($activeAcademicYearLabel)
                                        <span class="badge bg-secondary">Non</span>
                                    @else
                                        <span class="badge bg-warning text-dark">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($parent->is_verified_account)
                                        <span class="badge bg-success">Oui</span>
                                    @else
                                        <span class="badge bg-secondary">Non</span>
                                    @endif
                                </td>
                                <td>
                                    @canany(['parents.view', 'parents.update', 'parents.delete'])
                                        <div class="modern-action-group">
                                            @can('parents.view')
                                                <a href="{{ route('parents.show', $parent) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Voir</span></a>
                                            @endcan
                                            @can('parents.update')
                                                <a href="{{ route('parents.edit', $parent) }}" class="modern-action-btn is-edit"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                            @endcan
                                            @can('parents.delete')
                                                <form action="{{ route('parents.destroy', $parent) }}" method="POST" class="modern-inline-form" onsubmit="return confirm('Confirmer la suppression ?')">
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
                                <td colspan="8" class="text-center">Aucun parent trouve.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
