@extends('adminlte::page')
@section('title', 'Nouveau Incident')
@section('content_header')<h1 class="m-0">Declarer incident</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('incidents.store') }}" enctype="multipart/form-data">
@csrf
@include('incidents.partials.form', ['incident' => null])
<button class="btn btn-primary">Enregistrer</button>
<a href="{{ route('incidents.index') }}" class="btn btn-secondary">Annuler</a>
</form>
</div></div>
@stop
