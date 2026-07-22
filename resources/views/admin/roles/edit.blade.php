@extends('adminlte::page')

@section('title', 'Modifier role')

@section('content_header')
<h1 class="m-0">Modifier role</h1>
@stop

@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
        @csrf
        @method('PUT')
        @include('admin.roles.partials.form', ['role' => $role])
        <button class="btn btn-primary">Mettre a jour</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div></div>
@stop