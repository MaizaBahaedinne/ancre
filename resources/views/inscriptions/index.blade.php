@extends('adminlte::page')

@section('title', 'Inscriptions')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Inscriptions</h1>
    <a href="{{ route('inscriptions.create') }}" class="btn btn-primary">Nouvelle inscription</a>
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
                <strong>Affichage:</strong>
                Archives des inscriptions
                @if($activeAcademicYear)
                    (hors annee en cours: {{ $activeAcademicYear->label }})
                @endif
            @else
                <strong>Affichage:</strong>
                @if($activeAcademicYear)
                    Inscriptions de l'annee en cours ({{ $activeAcademicYear->label }})
                @else
                    Inscriptions sans annee scolaire active definie
                @endif
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inscriptions.index') }}" class="btn btn-{{ $isArchiveScope ? 'outline-primary' : 'primary' }} btn-sm">Annee en cours</a>
            <a href="{{ route('inscriptions.index', ['scope' => 'archive']) }}" class="btn btn-{{ $isArchiveScope ? 'primary' : 'outline-primary' }} btn-sm">Voir archives</a>
        </div>
    </div>
</div>

@if($isArchiveScope)
<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="scope" value="archive">
            <div class="col-md-4">
                <label>Enfant</label>
                <select name="enfant_id" class="form-control">
                    <option value="">Tous les enfants</option>
                    @foreach($enfants as $enfant)
                        <option value="{{ $enfant->id }}" @selected((string) $enfantId === (string) $enfant->id)>{{ $enfant->nom }} {{ $enfant->prenom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Annee scolaire archivee</label>
                <select name="annee_scolaire" class="form-control">
                    <option value="">Toutes les annees archivees</option>
                    @foreach($academicYears as $academicYear)
                        @continue($activeAcademicYear && $academicYear->label === $activeAcademicYear->label)
                        <option value="{{ $academicYear->label }}" @selected($annee === $academicYear->label)>{{ $academicYear->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-primary">Filtrer archives</button>
                <a href="{{ route('inscriptions.index', ['scope' => 'archive']) }}" class="btn btn-outline-secondary">Reinitialiser</a>
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
                    <th>Enfant</th>
                    <th>Package</th>
                    <th>Total package</th>
                    <th>Frais annuel</th>
                    <th>Total inscription</th>
                    <th>Annee scolaire</th>
                    <th>Date inscription</th>
                    <th>Type garde</th>
                    <th>Statut</th>
                    <th width="210" class="no-sort">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($inscriptions as $inscription)
                    <tr>
                        <td>{{ $inscription->enfant?->nom }} {{ $inscription->enfant?->prenom }}</td>
                        <td>{{ $inscription->package?->nom ?: '-' }}</td>
                        <td>{{ number_format((float) $inscription->resolved_package_monthly_total, 2, ',', ' ') }} TND</td>
                        <td>{{ number_format((float) $inscription->resolved_annual_registration_fee, 2, ',', ' ') }} TND</td>
                        <td>{{ number_format((float) $inscription->resolved_total_amount, 2, ',', ' ') }} TND</td>
                        <td>{{ $inscription->annee_scolaire }}</td>
                        <td>{{ optional($inscription->date_inscription)->format('d/m/Y') }}</td>
                        <td>{{ $inscription->type_garde }}</td>
                        <td>{{ $inscription->statut }}</td>
                        <td>
                            <div class="modern-action-group">
                                <a href="{{ route('inscriptions.show', $inscription) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Voir</span></a>
                                <a href="{{ route('inscriptions.edit', $inscription) }}" class="modern-action-btn is-edit"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                <form method="POST" action="{{ route('inscriptions.destroy', $inscription) }}" class="modern-inline-form" onsubmit="return confirm('Supprimer cette inscription ?')">
                                @csrf
                                @method('DELETE')
                                    <button class="modern-action-btn is-delete" type="submit"><i class="fa-solid fa-trash"></i><span>Supprimer</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">
                            {{ $isArchiveScope ? 'Aucune inscription archivee pour ce filtre.' : 'Aucune inscription pour l\'annee scolaire en cours.' }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
