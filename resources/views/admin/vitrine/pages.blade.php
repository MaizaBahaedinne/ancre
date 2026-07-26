@extends('adminlte::page')

@section('title', 'Vitrine - Pages')

@section('content_header')
    @include('admin.vitrine._header')
@stop

@section('content')
    @include('admin.vitrine._alerts')

    <div class="card mb-4">
        <div class="card-header"><strong>Pages vitrine (Accueil, A propos, Services, Activites, Contact)</strong></div>
        <div class="card-body">
            <div id="vitrinePagesAccordion">
                @foreach($pages as $page)
                    <div class="card mb-3">
                        <div class="card-header" id="heading-page-{{ $page->id }}">
                            <h3 class="card-title m-0">
                                <a href="#collapse-page-{{ $page->id }}" data-toggle="collapse" aria-expanded="false" aria-controls="collapse-page-{{ $page->id }}" class="d-block text-dark">
                                    {{ strtoupper($page->slug) }} - {{ $page->title }}
                                </a>
                            </h3>
                        </div>
                        <div id="collapse-page-{{ $page->id }}" class="collapse" aria-labelledby="heading-page-{{ $page->id }}" data-parent="#vitrinePagesAccordion">
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.vitrine.pages.update', $page) }}" class="row g-3" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="col-md-4">
                                        <label class="form-label">Titre menu</label>
                                        <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Ordre</label>
                                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $page->sort_order) }}" min="0" max="999">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_published" value="1" id="published-{{ $page->id }}" {{ old('is_published', $page->is_published) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="published-{{ $page->id }}">Page publiee</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Titre hero</label>
                                        <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title', $page->hero_title) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Sous-titre hero</label>
                                        <input type="text" name="hero_subtitle" class="form-control" value="{{ old('hero_subtitle', $page->hero_subtitle) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Image hero (locale)</label>
                                        <input type="file" name="hero_image" class="form-control" accept="image/*">
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_hero_image" value="1" id="remove-hero-{{ $page->id }}">
                                            <label class="form-check-label" for="remove-hero-{{ $page->id }}">Supprimer l'image hero actuelle</label>
                                        </div>
                                    </div>
                                    @if($page->hero_image)
                                        <div class="col-12">
                                            <img src="{{ asset('storage/'.$page->hero_image) }}" alt="Hero {{ $page->title }}" class="img-fluid rounded border" style="max-height:200px;object-fit:cover;">
                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <label class="form-label">Contenu principal</label>
                                        <textarea name="content" rows="4" class="form-control">{{ old('content', $page->content) }}</textarea>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-sm">Enregistrer la page</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@stop
