@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | Contact')

@section('content')
    @php
        $heroImage = $page?->hero_image ? asset('storage/'.$page->hero_image) : 'https://images.unsplash.com/photo-1527525443983-6e60c75fff46?auto=format&fit=crop&w=1800&q=80';
    @endphp
    <main>
        <section class="hero-shell" style="background: linear-gradient(110deg, rgba(15, 41, 66, 0.83), rgba(15, 41, 66, 0.56)), url('{{ $heroImage }}') center/cover no-repeat;">
            <div class="hero-content">
                <span class="hero-kicker"><i class="fa-solid fa-map-location-dot"></i> Contact</span>
                <h1>{{ $page?->hero_title ?: 'Contact et localisation' }}</h1>
                <p class="hero-lead">{{ $page?->hero_subtitle ?: 'Venez nous rendre visite ou contactez-nous directement.' }}</p>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap contact-grid">
                <article class="card">
                    <h2 class="section-title" style="margin-top:0;">Informations</h2>
                    <p class="text-muted"><i class="fa-solid fa-location-dot"></i> {{ $settings?->address ?: 'Adresse a definir' }}</p>
                    <p class="text-muted"><i class="fa-solid fa-phone"></i> {{ $settings?->phone ?: 'Telephone a definir' }}</p>
                    <p class="text-muted"><i class="fa-solid fa-envelope"></i> {{ $settings?->email ?: 'Email a definir' }}</p>
                    <div class="text-muted">{!! nl2br(e($page?->content ?: '')) !!}</div>
                </article>

                <article class="card">
                    <h2 class="section-title" style="margin-top:0;">Horaires</h2>
                    <table class="schedule">
                        <tbody>
                            @forelse($schedules as $slot)
                                <tr>
                                    <td>{{ $slot->day_label }}</td>
                                    <td>{{ $slot->is_closed ? 'Ferme' : trim(($slot->open_at ?: '-').' - '.($slot->close_at ?: '-')) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2">Horaires non disponibles.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </article>
            </div>
        </section>

        <section class="section section-band" style="padding-top:0;">
            <div class="wrap">
                <article class="card">
                    <h2 class="section-title" style="margin-top:0;">Localisation</h2>
                    @if(!empty($settings?->map_embed_url))
                        <iframe
                            src="{{ $settings->map_embed_url }}"
                            width="100%"
                            height="380"
                            style="border:0;border-radius:16px;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Carte de localisation"
                        ></iframe>
                    @else
                        <p class="text-muted">Carte non configuree. Ajoutez l'URL Maps embed depuis la plateforme admin.</p>
                    @endif
                </article>
            </div>
        </section>
    </main>
@endsection
