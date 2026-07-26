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
                                        <div class="col-12">
                                            <label class="form-label">Parametres avances Accueil (JSON)</label>
                                            @php
                                                $metaJson = old('meta_json', json_encode($page->meta ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                                            @endphp
                                            <textarea name="meta_json" rows="13" class="form-control" placeholder='{"hero_badge_text":"Garderie de confiance a Sfax","about_image_url":"https://...","hero_images":["https://...","https://..."],"about_highlights":["Encadrement securise","Programme d eveil adapte","Communication continue"]}'>{{ $metaJson }}</textarea>
                                            <small class="text-muted">Clés supportees: <strong>hero_badge_text</strong>, <strong>about_image_url</strong>, <strong>hero_images</strong> (tableau de 6 URLs max), <strong>about_highlights</strong> (tableau).</small>
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
