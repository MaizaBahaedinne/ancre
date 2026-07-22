@extends('adminlte::page')

@section('title', 'Modifier Sujet')

@section('content_header')
<h1 class="m-0">Modifier sujet</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('demandes-sujets.update', $subject) }}">
            @csrf
            @method('PUT')
            @include('request-subjects.partials.form', ['subject' => $subject])
            <button class="btn btn-primary">Mettre a jour</button>
            <a href="{{ route('demandes-sujets.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>
@stop