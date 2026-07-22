@extends('adminlte::page')
@section('title', 'Nouvelle Activite')
@section('content_header')<h1 class="m-0">Ajouter activite</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('activites.store') }}">
@csrf
@include('activites.partials.form', ['activite' => null])
<button class="btn btn-primary">Enregistrer</button>
<a href="{{ route('activites.index') }}" class="btn btn-secondary">Annuler</a>
</form>
</div></div>
@stop
