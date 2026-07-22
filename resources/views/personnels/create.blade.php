@extends('adminlte::page')
@section('title', 'Nouveau Personnel')
@section('content_header')<h1 class="m-0">Ajouter employe</h1>@stop
@section('content')
<div class="card"><div class="card-body">
@if (session('temporary_password'))
<div class="alert alert-info">Compte utilisateur cree. Mot de passe temporaire: <strong>{{ session('temporary_password') }}</strong></div>
@endif
<form method="POST" action="{{ route('personnels.store') }}" enctype="multipart/form-data">
@csrf
@include('personnels.partials.form', ['personnel' => null])
<button class="btn btn-primary">Enregistrer</button>
<a href="{{ route('personnels.index') }}" class="btn btn-secondary">Annuler</a>
</form>
</div></div>
@stop
