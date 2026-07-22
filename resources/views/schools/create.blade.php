@extends('adminlte::page')

@section('title', 'Nouvelle ecole')
@section('content_header')<h1 class="m-0">Ajouter une ecole</h1>@stop

@section('content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('schools.store') }}">
@csrf
@include('schools.partials.form', ['school' => null])
<button class="btn btn-primary">Enregistrer</button>
<a href="{{ route('schools.index') }}" class="btn btn-secondary">Annuler</a>
</form>
</div></div>
@stop