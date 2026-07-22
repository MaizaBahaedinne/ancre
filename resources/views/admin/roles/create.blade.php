@extends('adminlte::page')

@section('title', 'Nouveau role')

@section('content_header')
<h1 class="m-0">Creer un role</h1>
@stop

@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        @include('admin.roles.partials.form', ['role' => null])
        <button class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div></div>
@stop