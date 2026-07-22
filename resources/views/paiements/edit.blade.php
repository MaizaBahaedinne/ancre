@extends('adminlte::page')
@section('title', 'Modifier Paiement')
@section('content_header')<h1 class="m-0">Modifier paiement</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('paiements.update', $paiement) }}">
@csrf
@method('PUT')
@include('paiements.partials.form', ['paiement' => $paiement])
<button class="btn btn-primary">Mettre a jour</button>
<a href="{{ route('paiements.index') }}" class="btn btn-secondary">Annuler</a>
</form>
</div></div>
@stop
