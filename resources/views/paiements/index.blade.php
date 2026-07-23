@extends('adminlte::page')

@section('title', 'Paiements')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">Paiements</h1>
    <a href="{{ route('paiements.create') }}" class="btn btn-primary">Ajouter paiement</a>
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
                <strong>Affichage:</strong> Archives des paiements avant {{ $today->format('m/Y') }}
            @else
                <strong>Affichage:</strong> Paiements du mois en cours ({{ $today->format('m/Y') }})
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('paiements.index') }}" class="btn btn-{{ $isArchiveScope ? 'outline-primary' : 'primary' }} btn-sm">Mois en cours</a>
            <a href="{{ route('paiements.index', ['scope' => 'archive']) }}" class="btn btn-{{ $isArchiveScope ? 'primary' : 'outline-primary' }} btn-sm">Voir archives</a>
        </div>
    </div>
</div>

<div class="alert alert-warning">
    Paiements en retard{{ $isArchiveScope ? ' dans les archives' : ' ce mois-ci' }}: <strong>{{ $impayesCount }}</strong>
</div>

@if($isArchiveScope)
<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="scope" value="archive">
            <div class="col-md-3">
                <label>Enfant</label>
                <select name="enfant_id" class="form-control">
                    <option value="">Tous les enfants</option>
                    @foreach($enfants as $enfant)
                        <option value="{{ $enfant->id }}" @selected((string) $enfantId === (string) $enfant->id)>{{ $enfant->nom }} {{ $enfant->prenom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Mois</label>
                <input type="number" min="1" max="12" name="mois" class="form-control" value="{{ request('mois') }}">
            </div>
            <div class="col-md-2">
                <label>Annee</label>
                <input type="number" min="2000" max="2100" name="annee" class="form-control" value="{{ request('annee') }}">
            </div>
            <div class="col-md-2">
                <label>Statut</label>
                <select name="statut" class="form-control">
                    <option value="">Tous</option>
                    @foreach(['Paye', 'En retard', 'Partiel'] as $paymentStatus)
                        <option value="{{ $paymentStatus }}" @selected($statut === $paymentStatus)>{{ $paymentStatus }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary">Filtrer archives</button>
                <a href="{{ route('paiements.index', ['scope' => 'archive']) }}" class="btn btn-outline-secondary">Reinitialiser</a>
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
                    <th>Reference</th>
                    <th>Enfant</th>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Mois/Annee</th>
                    <th>Mode</th>
                    <th>Statut</th>
                    <th width="280" class="no-sort">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($paiements as $paiement)
                    <tr>
                        <td>{{ $paiement->reference ?: ('PAY-' . str_pad((string) $paiement->id, 6, '0', STR_PAD_LEFT)) }}</td>
                        <td>{{ $paiement->enfant?->nom }} {{ $paiement->enfant?->prenom }}</td>
                        <td>{{ number_format((float)$paiement->montant, 2, ',', ' ') }} TND</td>
                        <td>{{ optional($paiement->date_paiement)->format('d/m/Y') }}</td>
                        <td>{{ $paiement->mois }}/{{ $paiement->annee }}</td>
                        <td>{{ $paiement->mode_paiement }}</td>
                        <td><span class="badge badge-{{ $paiement->statut === 'Paye' ? 'success' : ($paiement->statut === 'En retard' ? 'danger' : 'warning') }}">{{ $paiement->statut }}</span></td>
                        <td>
                            @canany(['payments.view', 'payments.update', 'payments.delete'])
                                <div class="modern-action-group">
                                    @can('payments.view')
                                        <a href="{{ route('paiements.show', $paiement) }}" class="modern-action-btn is-view"><i class="fa-solid fa-eye"></i><span>Voir</span></a>
                                        <a href="{{ route('paiements.receipt', $paiement) }}" class="modern-action-btn is-print"><i class="fa-solid fa-file-pdf"></i><span>Recu</span></a>
                                    @endcan
                                    @can('payments.update')
                                        <a href="{{ route('paiements.edit', $paiement) }}" class="modern-action-btn is-edit"><i class="fa-solid fa-pen"></i><span>Modifier</span></a>
                                    @endcan
                                    @can('payments.delete')
                                        <form method="POST" action="{{ route('paiements.destroy', $paiement) }}" class="modern-inline-form" onsubmit="return confirm('Supprimer ce paiement ?')">
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
                        <td colspan="8" class="text-center">
                            {{ $isArchiveScope ? 'Aucun paiement archive pour ce filtre.' : 'Aucun paiement enregistre pour le mois en cours.' }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
