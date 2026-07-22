@extends('adminlte::page')

@section('title', 'Modifier Inscription')

@section('content_header')
<h1 class="m-0">Modifier inscription</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('inscriptions.update', $inscription) }}">
            @csrf
            @method('PUT')
            @include('inscriptions.partials.form', ['inscription' => $inscription])
            <button class="btn btn-primary">Mettre a jour</button>
            <a href="{{ route('inscriptions.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>
@stop
