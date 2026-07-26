@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | Accueil')

@section('content')
    @php
        $heroImage = $page?->hero_image ? asset('storage/'.$page->hero_image) : 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=1800&q=80';

        $aboutSnippet = $page?->content
            ? \Illuminate\Support\Str::limit(strip_tags($page->content), 240)
            : 'Nous accompagnons chaque enfant avec une approche pedagogique moderne, bienveillante et centree sur son rythme.';
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
                <h2 class="section-title">About Us</h2>
                <div class="grid-2">
                    <article class="panel">
                        <p class="muted">{{ $aboutSnippet }}</p>
                        <a href="{{ route('vitrine.about') }}" class="btn-parent" style="display:inline-flex;margin-top:0.8rem;">Read More</a>
                    </article>
                    <aside class="media-frame">
                        <img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1400&q=80" alt="About Ancre Des Elites" loading="lazy">
                    </aside>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="wrap">
                <h2 class="section-title">Nos services</h2>
                <div class="grid-4 reveal">
                    @forelse(($servicesFeatured ?? collect()) as $service)
                        <article class="panel">
                            <span class="icon-chip"><i class="{{ $service->icon ?: 'fa-solid fa-star' }}"></i></span>
                            <h3>{{ $service->title }}</h3>
                            <p class="muted">{{ \Illuminate\Support\Str::limit($service->description, 110) }}</p>
                        </article>
                    @empty
                        <article class="panel"><p class="muted">Aucun service disponible.</p></article>
                    @endforelse
                </div>
                <a href="{{ route('vitrine.services') }}" class="btn-parent" style="display:inline-flex;margin-top:1rem;">Read More</a>
            </div>
        </section>

        <section class="section section-warm">
            <div class="wrap">
                <h2 class="section-title">Nos activites</h2>
                <div class="social-grid reveal">
                    @forelse(($activitiesFeatured ?? collect()) as $post)
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
                        <article class="panel"><p class="muted">Aucune activite disponible.</p></article>
                    @endforelse
                </div>
                <a href="{{ route('vitrine.activities') }}" class="btn-parent" style="display:inline-flex;margin-top:1rem;">Read More</a>
            </div>
        </section>

        <section class="section">
            <div class="wrap">
                <h2 class="section-title">Statestique</h2>
                <div class="grid-4 stats-grid reveal">
                    <article class="panel">
                        <div class="stat-value">{{ ($services ?? collect())->count() > 0 ? ($services ?? collect())->count() : '6+' }}</div>
                        <p class="muted">Programmes et ateliers</p>
                    </article>
                    <article class="panel">
                        <div class="stat-value">{{ ($schedules ?? collect())->where('is_closed', false)->count() > 0 ? ($schedules ?? collect())->where('is_closed', false)->count() : '7j/7' }}</div>
                        <p class="muted">Jours d accueil</p>
                    </article>
                    <article class="panel">
                        <div class="stat-value">{{ ($professionals ?? collect())->count() > 0 ? ($professionals ?? collect())->count() : '12+' }}</div>
                        <p class="muted">Professionnels</p>
                    </article>
                    <article class="panel">
                        <div class="stat-value">98%</div>
                        <p class="muted">Parents satisfaits</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap">
                <h2 class="section-title">Meet Our Professional</h2>
                <div class="grid-4 reveal">
                    @forelse(($professionals ?? collect()) as $member)
                        @php
                            $memberPhoto = $member->photo
                                ? (\Illuminate\Support\Str::startsWith($member->photo, ['http://', 'https://']) ? $member->photo : asset('storage/'.$member->photo))
                                : 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=700&q=80';
                            $memberName = trim(($member->prenom ?? '').' '.($member->nom ?? '')) ?: 'Professionnel';
                        @endphp
                        <article class="panel">
                            <div style="height:180px;border-radius:14px;overflow:hidden;margin-bottom:0.7rem;">
                                <img src="{{ $memberPhoto }}" alt="{{ $memberName }}" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            <h3>{{ $memberName }}</h3>
                            <p class="muted">{{ $member->fonction ?: 'Educateur / Educatrice' }}</p>
                        </article>
                    @empty
                        <article class="panel"><p class="muted">Equipe en cours de publication.</p></article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="section">
            <div class="wrap">
                <h2 class="section-title">Blog</h2>
                <div class="grid-3 reveal">
                    @forelse(($blogPosts ?? collect()) as $post)
                        <article class="panel">
                            <div style="height:190px;border-radius:14px;overflow:hidden;margin-bottom:0.7rem;background:#edf3f7;">
                                @if($post->thumbnail_path)
                                    <img src="{{ asset('storage/'.$post->thumbnail_path) }}" alt="{{ $post->platform }}" style="width:100%;height:100%;object-fit:cover;">
                                @elseif($post->thumbnail_url)
                                    <img src="{{ $post->thumbnail_url }}" alt="{{ $post->platform }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=1000&q=80" alt="Article" style="width:100%;height:100%;object-fit:cover;">
                                @endif
                            </div>
                            <h3>{{ ucfirst($post->platform) }} - Article</h3>
                            <p class="muted">{{ \Illuminate\Support\Str::limit($post->caption ?: 'Retrouvez les dernieres informations de la garderie.', 130) }}</p>
                        </article>
                    @empty
                        <article class="panel"><p class="muted">Aucun article disponible.</p></article>
                    @endforelse
                </div>
                <a href="{{ route('vitrine.activities') }}" class="btn-parent" style="display:inline-flex;margin-top:1rem;">Read More</a>
            </div>
        </section>

        <section class="section section-warm">
            <div class="wrap">
                <h2 class="section-title">Happy Parents Our Testimonials</h2>
                <div class="grid-3 reveal">
                    <article class="panel">
                        <p class="muted">"Une equipe tres professionnelle, ma fille adore venir tous les matins."</p>
                        <strong>- Parent de Lina</strong>
                    </article>
                    <article class="panel">
                        <p class="muted">"Excellente communication avec les parents et progression visible de notre enfant."</p>
                        <strong>- Parent de Youssef</strong>
                    </article>
                    <article class="panel">
                        <p class="muted">"Cadre propre, activites variees et personnel bienveillant. Je recommande vivement."</p>
                        <strong>- Parent de Mariem</strong>
                    </article>
                </div>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap">
                <article class="panel" style="text-align:center;max-width:860px;margin:0 auto;">
                    <h2 class="section-title" style="margin-bottom:0.5rem;">News Letter</h2>
                    <p class="section-subtitle" style="margin:0 auto 1rem;">Recevez nos actualites, activites et conseils pour les parents.</p>
                    <form action="{{ route('vitrine.contact') }}" method="GET" style="display:flex;gap:0.6rem;justify-content:center;flex-wrap:wrap;">
                        <input type="email" name="newsletter_email" placeholder="Votre email" style="min-width:280px;max-width:420px;border:1px solid #d6e1ea;border-radius:999px;padding:0.7rem 1rem;">
                        <button type="submit" class="btn-parent" style="border:0;cursor:pointer;">S'abonner</button>
                    </form>
                </article>
            </div>
        </section>
    </main>
@endsection
