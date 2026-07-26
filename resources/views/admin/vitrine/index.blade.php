@extends('adminlte::page')

@section('title', 'Gestion Vitrine')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1 class="m-0">Gestion du site vitrine</h1>
        <a href="{{ route('vitrine.home') }}" class="btn btn-outline-primary" target="_blank" rel="noopener">Voir la vitrine</a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Veuillez corriger les erreurs:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header"><strong>Parametres globaux</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vitrine.settings.update') }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-4">
                    <label class="form-label">Nom du site</label>
                    <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings->site_name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Slogan</label>
                    <input type="text" name="tagline" class="form-control" value="{{ old('tagline', $settings->tagline) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lien espace parent</label>
                    <input type="text" name="parent_space_url" class="form-control" value="{{ old('parent_space_url', $settings->parent_space_url) }}" placeholder="/login">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Titre hero (Accueil)</label>
                    <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title', $settings->hero_title) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sous-titre hero (Accueil)</label>
                    <input type="text" name="hero_subtitle" class="form-control" value="{{ old('hero_subtitle', $settings->hero_subtitle) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $settings->address) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Telephone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $settings->phone) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $settings->email) }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">URL Google Maps embed</label>
                    <input type="text" name="map_embed_url" class="form-control" value="{{ old('map_embed_url', $settings->map_embed_url) }}" placeholder="https://www.google.com/maps/embed?...">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Facebook</label>
                    <input type="text" name="facebook_url" class="form-control" value="{{ old('facebook_url', $settings->facebook_url) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Instagram</label>
                    <input type="text" name="instagram_url" class="form-control" value="{{ old('instagram_url', $settings->instagram_url) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">TikTok</label>
                    <input type="text" name="tiktok_url" class="form-control" value="{{ old('tiktok_url', $settings->tiktok_url) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">YouTube</label>
                    <input type="text" name="youtube_url" class="form-control" value="{{ old('youtube_url', $settings->youtube_url) }}">
                </div>

                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Enregistrer les parametres</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>Pages vitrine (Accueil, A propos, Services, Activites, Contact)</strong></div>
        <div class="card-body">
            <div class="accordion" id="vitrinePagesAccordion">
                @foreach($pages as $page)
                    <div class="accordion-item mb-3 border rounded">
                        <h2 class="accordion-header" id="heading-page-{{ $page->id }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-page-{{ $page->id }}" aria-expanded="false" aria-controls="collapse-page-{{ $page->id }}">
                                {{ strtoupper($page->slug) }} - {{ $page->title }}
                            </button>
                        </h2>
                        <div id="collapse-page-{{ $page->id }}" class="accordion-collapse collapse" aria-labelledby="heading-page-{{ $page->id }}" data-bs-parent="#vitrinePagesAccordion">
                            <div class="accordion-body">
                                <form method="POST" action="{{ route('admin.vitrine.pages.update', $page) }}" class="row g-3">
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

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
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
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
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
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Publications sociales (Facebook / Instagram / TikTok)</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vitrine.social-posts.store') }}" class="row g-2 mb-3">
                @csrf
                <div class="col-md-2"><input type="text" name="platform" class="form-control" placeholder="facebook" required></div>
                <div class="col-md-4"><input type="text" name="post_url" class="form-control" placeholder="URL publication" required></div>
                <div class="col-md-3"><input type="text" name="thumbnail_url" class="form-control" placeholder="URL miniature"></div>
                <div class="col-md-1"><input type="number" name="sort_order" class="form-control" placeholder="#" min="0" max="999"></div>
                <div class="col-md-2 d-flex align-items-center"><label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked> Actif</label></div>
                <div class="col-12"><textarea name="caption" rows="2" class="form-control" placeholder="Legende"></textarea></div>
                <div class="col-12"><button class="btn btn-success btn-sm" type="submit">Ajouter publication</button></div>
            </form>

            @foreach($socialPosts as $post)
                <div class="border rounded p-2 mb-2">
                    <form method="POST" action="{{ route('admin.vitrine.social-posts.update', $post) }}" class="row g-2">
                        @csrf
                        @method('PUT')
                        <div class="col-md-2"><input type="text" name="platform" class="form-control form-control-sm" value="{{ $post->platform }}" required></div>
                        <div class="col-md-4"><input type="text" name="post_url" class="form-control form-control-sm" value="{{ $post->post_url }}" required></div>
                        <div class="col-md-3"><input type="text" name="thumbnail_url" class="form-control form-control-sm" value="{{ $post->thumbnail_url }}"></div>
                        <div class="col-md-1"><input type="number" name="sort_order" class="form-control form-control-sm" value="{{ $post->sort_order }}" min="0" max="999"></div>
                        <div class="col-md-2 d-flex align-items-center"><label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $post->is_active ? 'checked' : '' }}> Actif</label></div>
                        <div class="col-12"><textarea name="caption" rows="2" class="form-control form-control-sm">{{ $post->caption }}</textarea></div>
                        <div class="col-12"><button class="btn btn-primary btn-sm" type="submit">Maj</button></div>
                    </form>
                    <div class="d-flex gap-2 mt-2">
                        <form method="POST" action="{{ route('admin.vitrine.social-posts.destroy', $post) }}" onsubmit="return confirm('Supprimer cette publication ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm" type="submit">Supprimer</button>
                        </form>
                        <a href="{{ $post->post_url }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">Voir</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop
