@extends('adminlte::page')
@section('title', 'Modifier Incident')
@section('content_header')<h1 class="m-0">Modifier incident</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('incidents.update', $incident) }}" enctype="multipart/form-data">
@csrf
@method('PUT')
@include('incidents.partials.form', ['incident' => $incident])
<button class="btn btn-primary">Mettre a jour</button>
<a href="{{ route('incidents.index') }}" class="btn btn-secondary">Annuler</a>
</form>
</div></div>
@stop
