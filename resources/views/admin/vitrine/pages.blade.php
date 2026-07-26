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
            @php
                $pagesList = $pages->values();
            @endphp

            <ul class="nav nav-tabs" id="vitrinePagesTabs" role="tablist">
                @foreach($pagesList as $index => $page)
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link {{ $index === 0 ? 'active' : '' }}"
                            id="tab-{{ $page->slug }}"
                            data-bs-toggle="tab"
                            data-bs-target="#pane-{{ $page->slug }}"
                            type="button"
                            role="tab"
                            aria-controls="pane-{{ $page->slug }}"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                        >
                            {{ strtoupper($page->slug) }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content border border-top-0 p-3 bg-white" id="vitrinePagesTabsContent">
                @foreach($pagesList as $index => $page)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="pane-{{ $page->slug }}" role="tabpanel" aria-labelledby="tab-{{ $page->slug }}">
                        <h4 class="mb-3">{{ strtoupper($page->slug) }} - {{ $page->title }}</h4>

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
                                            <h5 class="mb-3">Parametres d affichage Accueil</h5>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Texte badge hero</label>
                                                    <input type="text" name="home_hero_badge_text" class="form-control" value="{{ old('home_hero_badge_text', $homeMeta['hero_badge_text'] ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Image section A propos (URL ou chemin local)</label>
                                                    <input type="text" name="home_about_image_url" class="form-control" value="{{ old('home_about_image_url', $homeMeta['about_image_url'] ?? '') }}" placeholder="images/about-child-tunisie.jpg">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label">Nombre de services affiches</label>
                                                    <input type="number" name="home_services_count" class="form-control" min="1" max="12" value="{{ old('home_services_count', $homeMeta['home_services_count'] ?? 4) }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Nombre d activites affichees</label>
                                                    <input type="number" name="home_activities_count" class="form-control" min="1" max="12" value="{{ old('home_activities_count', $homeMeta['home_activities_count'] ?? 4) }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Nombre de blogs affiches</label>
                                                    <input type="number" name="home_blog_count" class="form-control" min="1" max="12" value="{{ old('home_blog_count', $homeMeta['home_blog_count'] ?? 3) }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Nombre de temoignages</label>
                                                    <input type="number" name="home_testimonials_count" class="form-control" min="1" max="20" value="{{ old('home_testimonials_count', $homeMeta['home_testimonials_count'] ?? 10) }}">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Duree de transition hero (secondes)</label>
                                                    <input type="number" name="home_animation_duration_seconds" class="form-control" min="2" max="12" value="{{ old('home_animation_duration_seconds', $homeMeta['home_animation_duration_seconds'] ?? 4) }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Mode des chiffres</label>
                                                    <select name="home_stats_mode" class="form-select">
                                                        <option value="auto" {{ old('home_stats_mode', $homeMeta['home_stats_mode'] ?? 'auto') === 'auto' ? 'selected' : '' }}>Automatique (base)</option>
                                                        <option value="manual" {{ old('home_stats_mode', $homeMeta['home_stats_mode'] ?? 'auto') === 'manual' ? 'selected' : '' }}>Manuel</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 d-flex align-items-end">
                                                    <div class="d-flex gap-3">
                                                        <label class="form-check mb-0"><input type="checkbox" class="form-check-input" name="home_show_blog_section" value="1" {{ old('home_show_blog_section', $homeMeta['home_show_blog_section'] ?? true) ? 'checked' : '' }}> Afficher section Blog</label>
                                                        <label class="form-check mb-0"><input type="checkbox" class="form-check-input" name="home_show_testimonials_section" value="1" {{ old('home_show_testimonials_section', $homeMeta['home_show_testimonials_section'] ?? true) ? 'checked' : '' }}> Afficher Temoignages</label>
                                                    </div>
                                                </div>

                                                <div class="col-12"><hr class="my-1"></div>
                                                <div class="col-12"><strong>Chiffres manuels (utilises si mode = Manuel)</strong></div>

                                                <div class="col-md-3">
                                                    <label class="form-label">Services</label>
                                                    <input type="number" name="home_manual_services_count" class="form-control" min="0" value="{{ old('home_manual_services_count', $homeMeta['home_manual_services_count'] ?? '') }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Parents</label>
                                                    <input type="number" name="home_manual_parents_count" class="form-control" min="0" value="{{ old('home_manual_parents_count', $homeMeta['home_manual_parents_count'] ?? '') }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Staff</label>
                                                    <input type="number" name="home_manual_staff_count" class="form-control" min="0" value="{{ old('home_manual_staff_count', $homeMeta['home_manual_staff_count'] ?? '') }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Activites</label>
                                                    <input type="number" name="home_manual_activities_count" class="form-control" min="0" value="{{ old('home_manual_activities_count', $homeMeta['home_manual_activities_count'] ?? '') }}">
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
                @endforeach
            </div>
        </div>
    </div>
@stop
