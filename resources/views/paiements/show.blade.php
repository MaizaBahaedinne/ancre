@extends('adminlte::page')
@section('title', 'Detail Paiement')
@section('content_header')<h1 class="m-0">Detail paiement</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<dl class="row mb-0">
<dt class="col-sm-4">Reference</dt><dd class="col-sm-8">{{ $paiement->reference ?: ('PAY-' . str_pad((string) $paiement->id, 6, '0', STR_PAD_LEFT)) }}</dd>
<dt class="col-sm-4">Enfant</dt><dd class="col-sm-8">{{ $paiement->enfant?->nom }} {{ $paiement->enfant?->prenom }}</dd>
<dt class="col-sm-4">Parent</dt><dd class="col-sm-8">{{ $paiement->enfant?->parent?->nom }} {{ $paiement->enfant?->parent?->prenom }}</dd>
<dt class="col-sm-4">Montant</dt><dd class="col-sm-8">{{ number_format((float)$paiement->montant, 2, ',', ' ') }} TND</dd>
<dt class="col-sm-4">Date</dt><dd class="col-sm-8">{{ optional($paiement->date_paiement)->format('d/m/Y') }}</dd>
<dt class="col-sm-4">Periode</dt><dd class="col-sm-8">{{ $paiement->mois }}/{{ $paiement->annee }}</dd>
<dt class="col-sm-4">Mode paiement</dt><dd class="col-sm-8">{{ $paiement->mode_paiement }}</dd>
<dt class="col-sm-4">Statut</dt><dd class="col-sm-8">{{ $paiement->statut }}</dd>
<dt class="col-sm-4">Commentaire</dt><dd class="col-sm-8">{{ $paiement->commentaire ?: '-' }}</dd>
</dl>
</div><div class="card-footer">
<a href="{{ route('paiements.receipt', $paiement) }}" class="btn btn-secondary">Telecharger recu PDF</a>
<a href="{{ route('paiements.edit', $paiement) }}" class="btn btn-warning">Modifier</a>
<a href="{{ route('paiements.index') }}" class="btn btn-secondary">Retour</a>
</div></div>
@stop
