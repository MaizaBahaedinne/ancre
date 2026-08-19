@extends('adminlte::page')

@section('title', 'Vitrine - Parametres')

@section('content_header')
    @include('admin.vitrine._header')
@stop

@section('content')
    @include('admin.vitrine._alerts')

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

                <div class="col-12"><hr class="my-2"></div>

                <div class="col-12">
                    <h6 class="mb-1">Countdown site web</h6>
                    <p class="text-muted mb-0">Activez et configurez le compte a rebours affiche sur la page d'accueil vitrine.</p>
                </div>

                <div class="col-md-3">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="countdown_enabled" name="countdown_enabled" value="1" @checked(old('countdown_enabled', $settings->countdown_enabled))>
                        <label class="form-check-label" for="countdown_enabled">Activer le countdown</label>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Date cible</label>
                    <input type="datetime-local" name="countdown_target_at" class="form-control" value="{{ old('countdown_target_at', optional($settings->countdown_target_at)->format('Y-m-d\\TH:i')) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fuseau horaire</label>
                    <input type="text" name="countdown_timezone" class="form-control" value="{{ old('countdown_timezone', $settings->countdown_timezone ?: 'Africa/Tunis') }}" placeholder="Africa/Tunis">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Titre</label>
                    <input type="text" name="countdown_title" class="form-control" value="{{ old('countdown_title', $settings->countdown_title) }}" placeholder="Ouverture des inscriptions">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sous-titre</label>
                    <input type="text" name="countdown_subtitle" class="form-control" value="{{ old('countdown_subtitle', $settings->countdown_subtitle) }}" placeholder="Le nouveau portail est bientot disponible.">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Message fin de countdown</label>
                    <input type="text" name="countdown_expired_label" class="form-control" value="{{ old('countdown_expired_label', $settings->countdown_expired_label) }}" placeholder="Le lancement est en ligne.">
                </div>

                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Enregistrer les parametres</button>
                </div>
            </form>
        </div>
    </div>
@stop
