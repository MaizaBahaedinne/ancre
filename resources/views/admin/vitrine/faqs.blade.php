@extends('adminlte::page')

@section('title', 'Vitrine - FAQ')

@section('content_header')
    @include('admin.vitrine._header')
@stop

@section('content')
    @include('admin.vitrine._alerts')

    <div class="card mb-4">
        <div class="card-header"><strong>Ajouter une question frequente</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vitrine.faqs.store') }}" class="row g-3">
                @csrf
                <div class="col-md-8"><label class="form-label">Question</label><input type="text" name="question" class="form-control" required></div>
                <div class="col-md-2"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" min="0" max="999" value="0"></div>
                <div class="col-md-2 d-flex align-items-end"><label class="form-check"><input type="checkbox" class="form-check-input" name="is_active" value="1" checked> Active</label></div>
                <div class="col-12"><label class="form-label">Reponse</label><textarea name="answer" rows="3" class="form-control" required></textarea></div>
                <div class="col-12"><button class="btn btn-success btn-sm" type="submit">Ajouter</button></div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Questions existantes</strong></div>
        <div class="card-body">
            @foreach($faqs as $faq)
                <div class="border rounded p-2 mb-2">
                    <form method="POST" action="{{ route('admin.vitrine.faqs.update', $faq) }}" class="row g-2">
                        @csrf
                        @method('PUT')
                        <div class="col-md-8"><input type="text" name="question" class="form-control form-control-sm" value="{{ $faq->question }}" required></div>
                        <div class="col-md-2"><input type="number" name="sort_order" class="form-control form-control-sm" min="0" max="999" value="{{ $faq->sort_order }}"></div>
                        <div class="col-md-2 d-flex align-items-center"><label class="form-check"><input type="checkbox" class="form-check-input" name="is_active" value="1" {{ $faq->is_active ? 'checked' : '' }}> Active</label></div>
                        <div class="col-12"><textarea name="answer" rows="2" class="form-control form-control-sm" required>{{ $faq->answer }}</textarea></div>
                        <div class="col-12"><button class="btn btn-primary btn-sm" type="submit">Mettre a jour</button></div>
                    </form>
                    <form method="POST" action="{{ route('admin.vitrine.faqs.destroy', $faq) }}" class="mt-2" onsubmit="return confirm('Supprimer cette FAQ ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" type="submit">Supprimer</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@stop
