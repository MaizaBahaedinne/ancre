@extends('adminlte::page')
@section('title', 'Nouveau Paiement')
@section('content_header')<h1 class="m-0">Ajouter paiement</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('paiements.store') }}">
@csrf
@include('paiements.partials.form', ['paiement' => null])
<button class="btn btn-primary">Enregistrer</button>
<a href="{{ route('paiements.index') }}" class="btn btn-secondary">Annuler</a>
</form>
</div></div>
@stop
