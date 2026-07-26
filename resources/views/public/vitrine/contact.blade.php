@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | Contact')
@section('meta_description', 'Contactez la Garderie Ancre des Elites a Sfax pour demander une visite, obtenir des informations et lancer l inscription de votre enfant.')

@section('content')
    @php
        $heroImage = $page?->hero_image ? asset('storage/'.$page->hero_image) : 'https://images.unsplash.com/photo-1527525443983-6e60c75fff46?auto=format&fit=crop&w=1800&q=80';
    @endphp
    <main>
        <section class="hero">
            <div class="hero-media" aria-hidden="true">
                <span class="hero-slide" style="background-image:url('{{ $heroImage }}');animation:none;opacity:1;transform:scale(1.04);"></span>
            </div>
            <div class="hero-content">
                <span class="hero-badge"><i class="fa-solid fa-map-location-dot"></i> Contact</span>
                <h1>{{ $page?->hero_title ?: 'Contact et localisation' }}</h1>
                <p class="hero-lead">{{ $page?->hero_subtitle ?: 'Notre equipe est disponible pour repondre a vos questions rapidement.' }}</p>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap" style="display:grid;gap:1rem;grid-template-columns:1.25fr 1fr;">
                <article class="panel">
                    <h2 class="section-title" style="margin-top:0;">Formulaire de contact</h2>
                    <p class="muted">Nous vous repondons en general sous 24h pour organiser une visite ou vous accompagner dans l'inscription.</p>

                    @if(session('contact_success'))
                        <div class="alert alert-ok">{{ session('contact_success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-err">
                            <strong>Merci de corriger les champs suivants:</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('vitrine.contact.submit') }}" class="form-grid">
                        @csrf
                        <div class="form-row">
                            <div>
                                <label for="full_name">Nom complet</label>
                                <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                            </div>
                            <div>
                                <label for="phone">Telephone</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div>
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            <div>
                                <label for="subject">Sujet</label>
                                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required>
                            </div>
                        </div>
                        <div>
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="btn-submit"><i class="fa-solid fa-paper-plane"></i> Envoyer le message</button>
                    </form>
                </article>

                <article class="panel">
                    <h2 class="section-title" style="margin-top:0;">Informations</h2>
                    <p class="muted"><i class="fa-solid fa-location-dot"></i> {{ $settings?->address ?: 'Adresse a definir' }}</p>
                    <p class="muted"><i class="fa-solid fa-phone"></i> {{ $settings?->phone ?: 'Telephone a definir' }}</p>
                    <p class="muted"><i class="fa-solid fa-envelope"></i> {{ $settings?->email ?: 'Email a definir' }}</p>
                    <div class="muted">{!! nl2br(e($page?->content ?: '')) !!}</div>

                    <h3 style="margin-top:1rem;">Horaires</h3>
                    <table class="schedule">
                        <tbody>
                            @forelse($schedules as $slot)
                                <tr>
                                    <td>{{ $slot->day_label }}</td>
                                    <td>{{ $slot->is_closed ? 'Ferme' : trim(($slot->open_at ?: '-') . ' - ' . ($slot->close_at ?: '-')) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2">Horaires non disponibles.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </article>
            </div>
        </section>

        <section class="section section-warm" style="padding-top:0;">
            <div class="wrap">
                <article class="panel">
                    <h2 class="section-title" style="margin-top:0;">Localisation</h2>
                    @if(!empty($settings?->map_embed_url))
                        <iframe
                            src="{{ $settings->map_embed_url }}"
                            width="100%"
                            height="400"
                            style="border:0;border-radius:16px;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Carte de localisation"
                        ></iframe>
                    @else
                        <p class="muted">Carte non configuree. Ajoutez l URL Maps embed depuis la plateforme admin.</p>
                    @endif
                </article>
            </div>
        </section>
    </main>
@endsection
