@extends('adminlte::page')

@section('title', 'Annees scolaires')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Annees scolaires</h1>
    <a href="{{ route('academic-years.create') }}" class="btn btn-primary">Ajouter une annee scolaire</a>
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
                <thead>
                    <tr>
                        <th>Libelle</th>
                        <th>Debut</th>
                        <th>Fin</th>
                        <th>Frais annuel</th>
                        <th>Statut</th>
                        <th>Periodes</th>
                        <th>Classes</th>
                        <th class="no-sort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($academicYears as $academicYear)
                        <tr>
                            <td>{{ $academicYear->label }}</td>
                            <td>{{ optional($academicYear->start_date)->format('d/m/Y') }}</td>
                            <td>{{ optional($academicYear->end_date)->format('d/m/Y') }}</td>
                            <td>{{ number_format((float) $academicYear->registration_fee, 2, ',', ' ') }} TND</td>
                            <td><span class="badge bg-{{ $academicYear->is_active ? 'success' : 'secondary' }}">{{ $academicYear->is_active ? 'Active' : 'Archivee' }}</span></td>
                            <td>{{ $academicYear->periods_count }}</td>
                            <td>{{ $academicYear->school_classes_count }}</td>
                            <td>
                                @canany(['academic-years.view', 'academic-years.update', 'academic-years.delete'])
                                    <div class="modern-action-group">
                                        @can('academic-years.view')
                                            <a href="{{ route('academic-years.show', $academicYear) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Voir</span></a>
                                        @endcan
                                        @can('academic-years.update')
                                            <a href="{{ route('academic-years.edit', $academicYear) }}" class="modern-action-btn is-edit"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                        @endcan
                                        @can('academic-years.delete')
                                            <form method="POST" action="{{ route('academic-years.destroy', $academicYear) }}" class="modern-inline-form" onsubmit="return confirm('Supprimer cette annee scolaire ?')">
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
                        <tr><td colspan="8" class="text-center">Aucune annee scolaire.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop