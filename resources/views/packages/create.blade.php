@extends('adminlte::page')

@section('title', 'Nouveau Package')

@section('content_header')
<h1 class="m-0">Nouveau package</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('packages.store') }}">
            @csrf
            @include('packages.partials.form', ['package' => null])
            <button class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('packages.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>
@stop