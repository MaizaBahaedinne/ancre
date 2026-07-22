@extends('adminlte::page')

@section('title', 'Nouvel utilisateur')

@section('content_header')
<h1 class="m-0">Creer un utilisateur</h1>
@stop

@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        @include('admin.users.partials.form', ['user' => null])
        <button class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div></div>
@stop