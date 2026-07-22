@extends('adminlte::page')

@section('title', 'Nouvelle Salle')
@section('content_header')<h1 class="m-0">Ajouter salle</h1>@stop

@section('content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('salles.store') }}">
@csrf
@include('salles.partials.form', ['salle' => null])
<button class="btn btn-primary">Enregistrer</button>
<a href="{{ route('salles.index') }}" class="btn btn-secondary">Annuler</a>
</form>
</div></div>
@stop
