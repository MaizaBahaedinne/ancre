@extends('adminlte::page')

@section('title', 'Nouvelle Inscription')

@section('content_header')
<h1 class="m-0">Nouvelle inscription</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('inscriptions.store') }}">
            @csrf
            @include('inscriptions.partials.form', ['inscription' => null])
            <button class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('inscriptions.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>
@stop
