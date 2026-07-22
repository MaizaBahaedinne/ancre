@extends('adminlte::page')

@section('title', 'Modifier annee scolaire')
@section('content_header')<h1 class="m-0">Modifier annee scolaire</h1>@stop

@section('content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('academic-years.update', $academicYear) }}">
@csrf
@method('PUT')
@include('academic-years.partials.form', ['academicYear' => $academicYear])
<button class="btn btn-primary">Mettre a jour</button>
<a href="{{ route('academic-years.index') }}" class="btn btn-secondary">Annuler</a>
</form>
</div></div>
@stop