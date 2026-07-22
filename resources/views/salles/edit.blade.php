@extends('adminlte::page')

@section('title', 'Modifier Salle')
@section('content_header')<h1 class="m-0">Modifier salle</h1>@stop

@section('content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('salles.update', $salle) }}">
@csrf
@method('PUT')
@include('salles.partials.form', ['salle' => $salle])
<button class="btn btn-primary">Mettre a jour</button>
<a href="{{ route('salles.index') }}" class="btn btn-secondary">Annuler</a>
</form>
</div></div>
@stop
