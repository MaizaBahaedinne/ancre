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
            <div class="d-grid gap-3">
                @foreach($pages as $page)
                    <details class="card" {{ $loop->first ? 'open' : '' }}>
                        <summary class="card-header" style="cursor:pointer;list-style:none;">
                            <h3 class="card-title m-0">{{ strtoupper($page->slug) }} - {{ $page->title }}</h3>
                        </summary>
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

                                    @if($page->slug === 'home')
                                        @php
                                            $homeMeta = is_array($page->meta ?? null) ? $page->meta : [];
                                            $homeHeroImages = $homeMeta['hero_images'] ?? [];
                                            $homeHighlights = $homeMeta['about_highlights'] ?? [];
                                        @endphp
                                        <div class="col-12">
                                            <div class="card border-0 bg-light">
                                                <div class="card-body">
                                                    <h5 class="mb-3">Parametres Accueil faciles</h5>
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Texte badge hero</label>
                                                            <input type="text" name="home_hero_badge_text" class="form-control" value="{{ old('home_hero_badge_text', $homeMeta['hero_badge_text'] ?? '') }}" placeholder="Ex: Garderie de confiance a Sfax">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Image section A propos (URL ou chemin local)</label>
                                                            <input type="text" name="home_about_image_url" class="form-control" value="{{ old('home_about_image_url', $homeMeta['about_image_url'] ?? '') }}" placeholder="Ex: images/about-child-tunisie.jpg">
                                                        </div>

                                                        <div class="col-12"><hr class="my-1"></div>
                                                        <div class="col-12"><strong>Images hero (max 6)</strong></div>

                                                        @for($i = 0; $i < 6; $i++)
                                                            <div class="col-md-6">
                                                                <label class="form-label">Image hero {{ $i + 1 }}</label>
                                                                <input type="text" name="home_hero_image_{{ $i + 1 }}" class="form-control" value="{{ old('home_hero_image_'.($i + 1), $homeHeroImages[$i] ?? '') }}" placeholder="URL ou chemin local">
                                                            </div>
                                                        @endfor

                                                        <div class="col-12"><hr class="my-1"></div>
                                                        <div class="col-12"><strong>Points forts A propos</strong></div>

                                                        @for($i = 0; $i < 4; $i++)
                                                            <div class="col-md-6">
                                                                <label class="form-label">Point fort {{ $i + 1 }}</label>
                                                                <input type="text" name="home_about_highlight_{{ $i + 1 }}" class="form-control" value="{{ old('home_about_highlight_'.($i + 1), $homeHighlights[$i] ?? '') }}" placeholder="Texte court">
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($page->slug === 'about')
                                        <div class="col-12">
                                            <label class="form-label">Mission</label>
                                            <textarea name="mission" rows="3" class="form-control">{{ old('mission', $page->mission) }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Vision</label>
                                            <textarea name="vision" rows="3" class="form-control">{{ old('vision', $page->vision) }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Valeurs</label>
                                            <textarea name="valeurs" rows="3" class="form-control">{{ old('valeurs', $page->valeurs) }}</textarea>
                                        </div>
                                    @endif

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-sm">Enregistrer la page</button>
                                    </div>
                                </form>
                        </div>
                    </details>
                @endforeach
            </div>
        </div>
    </div>
@stop
