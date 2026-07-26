@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | Accueil')

@section('content')
    @php
        $heroImage = $page?->hero_image ? asset('storage/'.$page->hero_image) : 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=1800&q=80';
    @endphp
    <main>
        <section class="hero-shell" style="background: linear-gradient(110deg, rgba(15, 41, 66, 0.84), rgba(15, 41, 66, 0.54)), url('{{ $heroImage }}') center/cover no-repeat;">
            <div class="hero-content">
                <span class="hero-kicker"><i class="fa-solid fa-star"></i> Garderie professionnelle</span>
                <h1>{{ $settings?->hero_title ?: ($page?->hero_title ?: 'Bienvenue a Ancre Des Elites') }}</h1>
                <p class="hero-lead">{{ $settings?->hero_subtitle ?: ($page?->hero_subtitle ?: 'Une approche educative moderne dans un cadre securise.') }}</p>
                <div class="hero-actions">
                    <a href="{{ route('vitrine.services') }}" class="btn-hero"><i class="fa-solid fa-graduation-cap"></i> Decouvrir nos services</a>
                    <a href="{{ route('vitrine.contact') }}" class="btn-hero-alt"><i class="fa-solid fa-phone-volume"></i> Nous contacter</a>
                </div>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap">
                <h2 class="section-title">Services phares</h2>
                <p class="section-subtitle">{{ $page?->content }}</p>
                <div class="grid-3 stagger">
                    @forelse($services as $service)
                        <article class="card">
                            <span class="feature-icon"><i class="{{ $service->icon ?: 'fa-solid fa-star' }}"></i></span>
                            <h3>{{ $service->title }}</h3>
                            <p class="text-muted">{{ $service->description }}</p>
                        </article>
                    @empty
                        <article class="card"><p class="text-muted">Aucun service publie pour le moment.</p></article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="section">
            <div class="wrap">
                <h2 class="section-title">Une garderie qui evolue avec vos enfants</h2>
                <p class="section-subtitle">Nos chiffres cles illustrent notre engagement quotidien pour un environnement sain, pedagogique et securise.</p>
                <div class="stats-grid stagger">
                    <article class="stat-card">
                        <div class="stat-number">{{ $services->count() > 0 ? $services->count() : '6+' }}</div>
                        <div class="text-muted">Programmes et services educatifs</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-number">{{ $schedules->where('is_closed', false)->count() > 0 ? $schedules->where('is_closed', false)->count() : '7j/7' }}</div>
                        <div class="text-muted">Jours d'accueil planifies</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-number">12+</div>
                        <div class="text-muted">Professionnels de la petite enfance</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-number">98%</div>
                        <div class="text-muted">Satisfaction des familles accompagnees</div>
                    </article>
                </div>
            </div>
        </section>

        <section class="section section-band">
            <div class="wrap grid-2">
                <article class="card">
                    <h2 class="section-title" style="margin-top:0;">Horaires et infos</h2>
                    <table class="schedule">
                        <tbody>
                            @forelse($schedules as $slot)
                                <tr>
                                    <td>{{ $slot->day_label }}</td>
                                    <td>{{ $slot->is_closed ? 'Ferme' : trim(($slot->open_at ?: '-').' - '.($slot->close_at ?: '-')) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2">Horaires non configures.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <p class="text-muted"><i class="fa-solid fa-location-dot"></i> {{ $settings?->address ?: 'Adresse a definir' }}</p>
                    <p class="text-muted"><i class="fa-solid fa-phone"></i> {{ $settings?->phone ?: 'Telephone a definir' }}</p>
                </article>

                <aside class="image-card">
                    <img src="https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=1400&q=80" alt="Enfants en activite a la garderie" loading="lazy">
                </aside>
            </div>
        </section>

        <section class="section">
            <div class="wrap">
                <h2 class="section-title">Dernieres activites</h2>
                <p class="section-subtitle">Retrouvez nos publications Facebook, Instagram et TikTok.</p>
                <div class="social-list stagger">
                    @forelse($socialPosts as $post)
                        <a href="{{ $post->post_url }}" target="_blank" rel="noopener" class="social-item">
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
                        <div class="card"><p class="text-muted">Aucune publication sociale pour le moment.</p></div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
@endsection
