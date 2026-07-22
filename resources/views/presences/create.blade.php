@extends('adminlte::page')

@section('title', 'Nouvelle Presence')

@section('content_header')
<h1 class="m-0">Enregistrer presence</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('presences.store') }}">
            @csrf
            @include('presences.partials.form', ['presence' => null])
            <button class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('presences.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>
@stop
