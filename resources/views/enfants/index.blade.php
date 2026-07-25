@extends('adminlte::page')

@section('title', 'Enfants')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Enfants</h1>
        <a href="{{ route('enfants.create') }}" class="btn btn-primary">Ajouter un enfant</a>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Enfants</div><div class="display-6 fw-semibold">{{ $stats['total'] ?? 0 }}</div></div></div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Inscrits (annee en cours)</div><div class="display-6 fw-semibold">{{ $stats['inscribed_current_year'] ?? 0 }}</div></div></div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Non inscrits (annee en cours)</div><div class="display-6 fw-semibold">{{ $stats['not_inscribed_current_year'] ?? 0 }}</div></div></div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Avec allergie</div><div class="display-6 fw-semibold">{{ $stats['with_allergie'] ?? 0 }}</div></div></div>
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
                            <th>Classe</th>
                            <th>Ecole</th>
                            <th>Parent</th>
                            <th>Inscription annee en cours</th>
                            <th>Date naissance</th>
                            <th width="220" class="no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enfants as $enfant)
                            <tr>
                                
                                <td>{{ $enfant->nom }}</td>
                                <td>{{ $enfant->prenom }}</td>
                                <td>{{ $enfant->schoolClass?->name ?: ($enfant->classe ?: '-') }}</td>
                                <td>{{ $enfant->schoolClass?->school?->name ?: '-' }}</td>
                                <td>
                                    @if($enfant->parent)
                                        <a href="{{ route('parents.show', $enfant->parent) }}">
                                            {{ $enfant->parent->nom }} {{ $enfant->parent->prenom }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($activeAcademicYearLabel && $enfant->is_inscribed_current_year)
                                        <span class="badge bg-success">Oui</span>
                                    @elseif($activeAcademicYearLabel)
                                        <span class="badge bg-secondary">Non</span>
                                    @else
                                        <span class="badge bg-warning text-dark">N/A</span>
                                    @endif
                                </td>
                                <td>{{ optional($enfant->date_naissance)->format('d/m/Y') }}</td>
                                <td>
                                    @canany(['children.view', 'children.update', 'children.delete'])
                                        <div class="modern-action-group">
                                            @can('children.view')
                                                <a href="{{ route('enfants.show', $enfant) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Voir</span></a>
                                            @endcan
                                            @can('children.update')
                                                <a href="{{ route('enfants.edit', $enfant) }}" class="modern-action-btn is-edit"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                            @endcan
                                            @can('children.delete')
                                                <form action="{{ route('enfants.destroy', $enfant) }}" method="POST" class="modern-inline-form" onsubmit="return confirm('Confirmer la suppression ?')">
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
                                <td colspan="8" class="text-center">Aucun enfant trouve.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
