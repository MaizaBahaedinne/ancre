@extends('adminlte::page')

@section('title', 'Vitrine - Services')

@section('content_header')
    @include('admin.vitrine._header')
@stop

@section('content')
    @include('admin.vitrine._alerts')

    <div class="card h-100 mb-4">
        <div class="card-header"><strong>Services</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vitrine.services.store') }}" class="row g-2 mb-3">
                @csrf
                <div class="col-md-5"><input type="text" name="title" class="form-control" placeholder="Titre" required></div>
                <div class="col-md-3"><input type="text" name="icon" class="form-control" placeholder="fa-solid fa-star"></div>
                <div class="col-md-2"><input type="number" name="sort_order" class="form-control" placeholder="Ordre" min="0" max="999"></div>
                <div class="col-md-2 d-flex align-items-center"><label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked> Actif</label></div>
                <div class="col-12"><textarea name="description" rows="2" class="form-control" placeholder="Description"></textarea></div>
                <div class="col-12"><button class="btn btn-success btn-sm" type="submit">Ajouter service</button></div>
            </form>

            @foreach($services as $service)
                <div class="border rounded p-2 mb-2">
                    <form method="POST" action="{{ route('admin.vitrine.services.update', $service) }}" class="row g-2">
                        @csrf
                        @method('PUT')
                        <div class="col-md-5"><input type="text" name="title" class="form-control form-control-sm" value="{{ $service->title }}" required></div>
                        <div class="col-md-3"><input type="text" name="icon" class="form-control form-control-sm" value="{{ $service->icon }}"></div>
                        <div class="col-md-2"><input type="number" name="sort_order" class="form-control form-control-sm" value="{{ $service->sort_order }}" min="0" max="999"></div>
                        <div class="col-md-2 d-flex align-items-center"><label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }}> Actif</label></div>
                        <div class="col-12"><textarea name="description" rows="2" class="form-control form-control-sm">{{ $service->description }}</textarea></div>
                        <div class="col-12"><button class="btn btn-primary btn-sm" type="submit">Maj</button></div>
                    </form>
                    <form method="POST" action="{{ route('admin.vitrine.services.destroy', $service) }}" class="mt-2" onsubmit="return confirm('Supprimer ce service ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" type="submit">Supprimer</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@stop
