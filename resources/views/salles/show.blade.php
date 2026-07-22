@extends('adminlte::page')
@section('title', 'Detail Salle')
@section('content_header')<h1 class="m-0">Detail salle</h1>@stop

@section('content')
<div class="card"><div class="card-body">
<dl class="row mb-0">
<dt class="col-sm-4">Nom</dt><dd class="col-sm-8">{{ $salle->nom }}</dd>
<dt class="col-sm-4">Etage</dt><dd class="col-sm-8">{{ $salle->etage }}</dd>
<dt class="col-sm-4">Capacite</dt><dd class="col-sm-8">{{ $salle->capacite }}</dd>
<dt class="col-sm-4">Equipements</dt><dd class="col-sm-8">{{ collect($salle->equipements ?? [])->map(fn ($item) => \App\Models\Salle::EQUIPEMENT_OPTIONS[$item] ?? $item)->join(', ') ?: '-' }}</dd>
<dt class="col-sm-4">Statut</dt><dd class="col-sm-8">{{ \App\Models\Salle::STATUT_OPTIONS[$salle->statut] ?? $salle->statut }}</dd>
<dt class="col-sm-4">Responsable</dt><dd class="col-sm-8">{{ $salle->responsablePersonnel ? $salle->responsablePersonnel->prenom.' '.$salle->responsablePersonnel->nom.' - '.$salle->responsablePersonnel->fonction : '-' }}</dd>
</dl>
</div></div>

<div class="card">
    <div class="card-header"><h3 class="card-title mb-0">Activites reservees</h3></div>
    <div class="card-body modern-table-card">
        <div class="table-responsive">
            <table class="table table-striped table-bordered js-data-table nowrap">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Date</th>
                        <th>Debut</th>
                        <th>Fin</th>
                        <th>Responsable</th>
                        <th class="no-sort">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salle->activites as $activite)
                        <tr>
                            <td>{{ $activite->titre }}</td>
                            <td>{{ optional($activite->date)->format('d/m/Y') }}</td>
                            <td>{{ $activite->heure_debut ?: $activite->heure ?: '-' }}</td>
                            <td>{{ $activite->heure_fin ?: '-' }}</td>
                            <td>{{ $activite->responsablePersonnel ? $activite->responsablePersonnel->prenom.' '.$activite->responsablePersonnel->nom : ($activite->responsable ?: '-') }}</td>
                            <td>
                                <a href="{{ route('activites.show', $activite) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Voir</span></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">Aucune activite liee.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('salles.edit', $salle) }}" class="btn btn-warning">Modifier</a>
    <a href="{{ route('salles.index') }}" class="btn btn-secondary">Retour</a>
</div>
@stop
