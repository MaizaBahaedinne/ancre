@extends('adminlte::page')

@section('title', 'Nouveau Sujet')

@section('content_header')
<h1 class="m-0">Nouveau sujet</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('demandes-sujets.store') }}">
            @csrf
            @include('request-subjects.partials.form')
            <button class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('demandes-sujets.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>
@stop