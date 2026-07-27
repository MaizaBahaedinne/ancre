@extends('adminlte::page')

@section('title', 'Vitrine - Temoignages')

@section('content_header')
    @include('admin.vitrine._header')
@stop

@section('content')
    @include('admin.vitrine._alerts')

    <div class="card mb-4">
        <div class="card-header"><strong>Ajouter un temoignage</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vitrine.testimonials.store') }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Parent</label>
                    <select name="parent_id" class="form-control" required>
                        <option value="">-- Selectionner un parent --</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}">{{ trim(($parent->prenom ?? '') . ' ' . ($parent->nom ?? '')) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Note</label>
                    <div style="display:flex;gap:0.3rem;align-items:center;">
                        @for($i = 1; $i <= 5; $i++)
                            <label style="cursor:pointer;margin:0;" title="{{ $i }} etoile(s)">
                                <input type="radio" name="rating" value="{{ $i }}" {{ $i === 5 ? 'checked' : '' }} style="margin-right:0.2rem;">
                                <i class="fa-solid fa-star" style="color:#e0a63f;"></i>
                            </label>
                        @endfor
                    </div>
                </div>
                <div class="col-md-2"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" min="0" max="999" value="0"></div>
                <div class="col-12"><label class="form-label">Contenu du temoignage</label><textarea name="content" rows="3" class="form-control" required></textarea></div>
                <div class="col-md-3 d-flex align-items-end"><label class="form-check"><input type="checkbox" class="form-check-input" name="is_published" value="1" checked> Publie</label></div>
                <div class="col-12"><button class="btn btn-success btn-sm" type="submit">Ajouter</button></div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Temoignages existants</strong></div>
        <div class="card-body">
            @foreach($testimonials as $testimonial)
                <div class="border rounded p-2 mb-2">
                    <form method="POST" action="{{ route('admin.vitrine.testimonials.update', $testimonial) }}" class="row g-2">
                        @csrf
                        @method('PUT')
                        <div class="col-md-4">
                            <select name="parent_id" class="form-control form-control-sm">
                                <option value="">-- Sans parent --</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}" {{ $testimonial->parent_id === $parent->id ? 'selected' : '' }}>
                                        {{ trim(($parent->prenom ?? '') . ' ' . ($parent->nom ?? '')) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div style="display:flex;gap:0.2rem;">
                                @for($i = 1; $i <= 5; $i++)
                                    <label style="cursor:pointer;margin:0;font-size:0.9rem;" title="{{ $i }} etoile(s)">
                                        <input type="radio" name="rating" value="{{ $i }}" {{ $testimonial->rating === $i ? 'checked' : '' }} style="margin:0;">
                                        <i class="fa-solid fa-star" style="color:#e0a63f;"></i>
                                    </label>
                                @endfor
                            </div>
                        </div>
                        <div class="col-md-2"><input type="number" name="sort_order" class="form-control form-control-sm" min="0" max="999" value="{{ $testimonial->sort_order }}"></div>
                        <div class="col-12"><textarea name="content" rows="2" class="form-control form-control-sm" required>{{ $testimonial->content }}</textarea></div>
                        <div class="col-md-3 d-flex align-items-center"><label class="form-check"><input type="checkbox" class="form-check-input" name="is_published" value="1" {{ $testimonial->is_published ? 'checked' : '' }}> Publie</label></div>
                        <div class="col-12"><button class="btn btn-primary btn-sm" type="submit">Mettre a jour</button></div>
                    </form>
                    <form method="POST" action="{{ route('admin.vitrine.testimonials.destroy', $testimonial) }}" class="mt-2" onsubmit="return confirm('Supprimer ce temoignage ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" type="submit">Supprimer</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@stop
