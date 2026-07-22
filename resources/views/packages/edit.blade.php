@extends('adminlte::page')

@section('title', 'Modifier Package')

@section('content_header')
<h1 class="m-0">Modifier package</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('packages.update', $package) }}">
            @csrf
            @method('PUT')
            @include('packages.partials.form', ['package' => $package])
            <button class="btn btn-primary">Mettre a jour</button>
            <a href="{{ route('packages.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>
@stop