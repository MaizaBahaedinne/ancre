@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | Accueil')

@section('content')
    @php
        $heroImage = $page?->hero_image ? asset('storage/'.$page->hero_image) : 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=1800&q=80';
    @endphp
    <main>
        <section class="hero" style="--hero-image: url('{{ $heroImage }}');">
            <div class="hero-content">
                <span class="hero-badge"><i class="fa-solid fa-seedling"></i> Garderie moderne et bienveillante</span>
                <h1>{{ $settings?->hero_title ?: ($page?->hero_title ?: 'Une enfance epanouie commence ici') }}</h1>
                <p class="hero-lead">{{ $settings?->hero_subtitle ?: ($page?->hero_subtitle ?: 'Un environnement securise, des educateurs passionnes et des activites pensees pour chaque age.') }}</p>
                <div class="hero-actions">
                    <a href="{{ route('vitrine.services') }}" class="btn-hero"><i class="fa-solid fa-graduation-cap"></i> Voir nos services</a>
                    <a href="{{ route('vitrine.contact') }}" class="btn-hero-alt"><i class="fa-solid fa-phone-volume"></i> Prendre contact</a>
                </div>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap">
                <h2 class="section-title">Services phares</h2>
                <p class="section-subtitle">{{ $page?->content ?: 'Decouvrez nos services d accueil, d apprentissage et d accompagnement au quotidien.' }}</p>
                <div class="grid-3 reveal">
                    @forelse($services as $service)
                        <article class="panel">
                            <span class="icon-chip"><i class="{{ $service->icon ?: 'fa-solid fa-star' }}"></i></span>
                            <h3>{{ $service->title }}</h3>
                            <p class="muted">{{ $service->description }}</p>
                        </article>
                    @empty
                        <article class="panel"><p class="muted">Aucun service publie pour le moment.</p></article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="section">
            <div class="wrap">
                <h2 class="section-title">Des chiffres qui rassurent les familles</h2>
                <p class="section-subtitle">Une equipe engagee, des programmes varies et un cadre de confiance pour chaque enfant.</p>
                <div class="grid-4 stats-grid reveal">
                    <article class="panel">
                        <div class="stat-value">{{ $services->count() > 0 ? $services->count() : '6+' }}</div>
                        <p class="muted">Programmes et ateliers</p>
                    </article>
                    <article class="panel">
                        <div class="stat-value">{{ $schedules->where('is_closed', false)->count() > 0 ? $schedules->where('is_closed', false)->count() : '7j/7' }}</div>
                        <p class="muted">Jours d accueil organises</p>
                    </article>
                    <article class="panel">
                        <div class="stat-value">12+</div>
                        <p class="muted">Professionnels petite enfance</p>
                    </article>
                    <article class="panel">
                        <div class="stat-value">98%</div>
                        <p class="muted">Satisfaction des familles</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section section-warm">
            <div class="wrap grid-2">
                <article class="panel">
                    <h2 class="section-title" style="margin-top:0;">Horaires et informations</h2>
                    <table class="schedule">
                        <tbody>
                            @forelse($schedules as $slot)
                                <tr>
                                    <td>{{ $slot->day_label }}</td>
                                    <td>{{ $slot->is_closed ? 'Ferme' : trim(($slot->open_at ?: '-') . ' - ' . ($slot->close_at ?: '-')) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2">Horaires non configures.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <p class="muted"><i class="fa-solid fa-location-dot"></i> {{ $settings?->address ?: 'Adresse a definir' }}</p>
                    <p class="muted"><i class="fa-solid fa-phone"></i> {{ $settings?->phone ?: 'Telephone a definir' }}</p>
                </article>

                <aside class="media-frame">
                    <img src="https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=1400&q=80" alt="Enfants en activite" loading="lazy">
                </aside>
            </div>
        </section>

        <section class="section">
            <div class="wrap">
                <h2 class="section-title">Dernieres activites</h2>
                <p class="section-subtitle">Suivez nos publications et la vie de la garderie au quotidien.</p>
                <div class="social-grid reveal">
                    @forelse($socialPosts as $post)
                        <a href="{{ $post->post_url }}" target="_blank" rel="noopener" class="social-card">
                            <div class="social-thumb">
                                @if($post->thumbnail_path)
                                    <img src="{{ asset('storage/'.$post->thumbnail_path) }}" alt="{{ $post->platform }}">
                                @elseif($post->thumbnail_url)
                                    <img src="{{ $post->thumbnail_url }}" alt="{{ $post->platform }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1526634332515-d56c5fd16991?auto=format&fit=crop&w=1000&q=80" alt="Publication activite">
                                @endif
                            </div>
                            <div class="social-meta"><strong>{{ ucfirst($post->platform) }}</strong> - {{ $post->caption ?: 'Voir la publication' }}</div>
                        </a>
                    @empty
                        <article class="panel"><p class="muted">Aucune publication sociale pour le moment.</p></article>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
@endsection
