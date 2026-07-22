@extends('adminlte::page')

@section('title', 'Modifier utilisateur')

@section('content_header')
<h1 class="m-0">Modifier utilisateur</h1>
@stop

@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')
        @include('admin.users.partials.form', ['user' => $user])
        <button class="btn btn-primary">Mettre a jour</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div></div>
@stop