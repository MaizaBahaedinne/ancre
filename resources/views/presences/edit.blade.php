@extends('adminlte::page')

@section('title', 'Modifier Presence')

@section('content_header')
<h1 class="m-0">Modifier presence</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('presences.update', $presence) }}">
            @csrf
            @method('PUT')
            @include('presences.partials.form', ['presence' => $presence])
            <button class="btn btn-primary">Mettre a jour</button>
            <a href="{{ route('presences.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>
@stop
