@extends('adminlte::page')
@section('title', 'Modifier Activite')
@section('content_header')<h1 class="m-0">Modifier activite</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('activites.update', $activite) }}">
@csrf
@method('PUT')
@include('activites.partials.form', ['activite' => $activite])
<button class="btn btn-primary">Mettre a jour</button>
<a href="{{ route('activites.index') }}" class="btn btn-secondary">Annuler</a>
</form>
</div></div>
@stop
