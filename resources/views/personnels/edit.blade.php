@extends('adminlte::page')
@section('title', 'Modifier Personnel')
@section('content_header')<h1 class="m-0">Modifier employe</h1>@stop
@section('content')
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('personnels.update', $personnel) }}" enctype="multipart/form-data">
@csrf
@method('PUT')
@include('personnels.partials.form', ['personnel' => $personnel])
<button class="btn btn-primary">Mettre a jour</button>
<a href="{{ route('personnels.index') }}" class="btn btn-secondary">Annuler</a>
</form>
</div></div>
@stop
