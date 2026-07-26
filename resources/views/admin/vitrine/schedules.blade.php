@extends('adminlte::page')

@section('title', 'Vitrine - Horaires')

@section('content_header')
    @include('admin.vitrine._header')
@stop

@section('content')
    @include('admin.vitrine._alerts')

    <div class="card h-100 mb-4">
        <div class="card-header"><strong>Horaires</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vitrine.schedules.store') }}" class="row g-2 mb-3">
                @csrf
                <div class="col-md-4"><input type="text" name="day_label" class="form-control" placeholder="Jour" required></div>
                <div class="col-md-2"><input type="text" name="open_at" class="form-control" placeholder="08:00"></div>
                <div class="col-md-2"><input type="text" name="close_at" class="form-control" placeholder="18:00"></div>
                <div class="col-md-2"><input type="number" name="sort_order" class="form-control" placeholder="Ordre" min="0" max="999"></div>
                <div class="col-md-2 d-flex align-items-center"><label class="form-check"><input class="form-check-input" type="checkbox" name="is_closed" value="1"> Ferme</label></div>
                <div class="col-md-2 d-flex align-items-center"><label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked> Actif</label></div>
                <div class="col-12"><button class="btn btn-success btn-sm" type="submit">Ajouter horaire</button></div>
            </form>

            @foreach($schedules as $schedule)
                <div class="border rounded p-2 mb-2">
                    <form method="POST" action="{{ route('admin.vitrine.schedules.update', $schedule) }}" class="row g-2">
                        @csrf
                        @method('PUT')
                        <div class="col-md-4"><input type="text" name="day_label" class="form-control form-control-sm" value="{{ $schedule->day_label }}" required></div>
                        <div class="col-md-2"><input type="text" name="open_at" class="form-control form-control-sm" value="{{ $schedule->open_at }}"></div>
                        <div class="col-md-2"><input type="text" name="close_at" class="form-control form-control-sm" value="{{ $schedule->close_at }}"></div>
                        <div class="col-md-2"><input type="number" name="sort_order" class="form-control form-control-sm" value="{{ $schedule->sort_order }}" min="0" max="999"></div>
                        <div class="col-md-2 d-flex align-items-center"><label class="form-check"><input class="form-check-input" type="checkbox" name="is_closed" value="1" {{ $schedule->is_closed ? 'checked' : '' }}> Ferme</label></div>
                        <div class="col-md-2 d-flex align-items-center"><label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $schedule->is_active ? 'checked' : '' }}> Actif</label></div>
                        <div class="col-12"><button class="btn btn-primary btn-sm" type="submit">Maj</button></div>
                    </form>
                    <form method="POST" action="{{ route('admin.vitrine.schedules.destroy', $schedule) }}" class="mt-2" onsubmit="return confirm('Supprimer cet horaire ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" type="submit">Supprimer</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@stop
