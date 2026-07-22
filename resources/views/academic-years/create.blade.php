@extends('adminlte::page')

@section('title', 'Nouvelle annee scolaire')
@section('content_header')<h1 class="m-0">Ajouter une annee scolaire</h1>@stop

@section('content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('academic-years.store') }}">
@csrf
@include('academic-years.partials.form', ['academicYear' => null])
<button class="btn btn-primary">Enregistrer</button>
<a href="{{ route('academic-years.index') }}" class="btn btn-secondary">Annuler</a>
</form>
</div></div>
@stop