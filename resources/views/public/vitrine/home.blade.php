@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | Accueil')
@section('meta_description', 'Garderie Ancre des Elites a Tunis: cadre securise, equipe bienveillante, activites educatives et accompagnement des familles. Demandez une visite.')

@section('content')
    @php
        $heroImage = $page?->hero_image ? asset('storage/'.$page->hero_image) : 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=1800&q=80';

        $aboutSnippet = $aboutPage?->content
            ? \Illuminate\Support\Str::limit(strip_tags($aboutPage->content), 240)
            : 'Nous accompagnons chaque enfant avec une approche pedagogique moderne, bienveillante et centree sur son rythme.';

        $inscriptionUrl = $settings?->parent_space_url ?: route('login');
    @endphp
    <main>
        <section class="hero" style="--hero-image: url('{{ $heroImage }}');">
            <div class="hero-content hero-grid">
                <div class="hero-copy">
                    <span class="hero-badge"><i class="fa-solid fa-seedling"></i> Garderie de confiance a Tunis</span>
                    <h1>{{ $settings?->hero_title ?: ($page?->hero_title ?: 'Ancre des Elites, une garderie ou votre enfant grandit en confiance') }}</h1>
                    <p class="hero-lead">{{ $settings?->hero_subtitle ?: ($page?->hero_subtitle ?: 'Chaque journee est pensee pour son bien-etre, son eveil et son autonomie dans un cadre securise et bienveillant.') }}</p>
                    <div class="hero-actions">
                        <a href="{{ $inscriptionUrl }}" class="btn-hero-alt"><i class="fa-solid fa-user-plus"></i> Inscrire mon enfant</a>
                        <a href="{{ route('vitrine.contact') }}" class="btn-hero" style="background:#fff9ef;"><i class="fa-solid fa-phone-volume"></i> Nous contacter</a>
                    </div>
                </div>

                <aside class="hero-visit-card">
                    <h2 class="hero-visit-title">Demander une visite</h2>
                    <p class="hero-visit-sub">Remplissez ce formulaire rapide, notre equipe vous rappelle sous 24h.</p>

                    @if(session('visit_success'))
                        <div class="alert alert-ok" style="margin-bottom:0.6rem;">{{ session('visit_success') }}</div>
                    @endif

                    @if($errors->visitRequest->any())
                        <div class="alert alert-err" style="margin-bottom:0.6rem;">
                            <ul style="margin:0; padding-left:1.1rem;">
                                @foreach($errors->visitRequest->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('vitrine.visit-request.submit') }}" class="form-grid">
                        @csrf
                        <div class="hero-visit-grid">
                            <div>
                                <label for="visit_full_name">Nom complet</label>
                                <input id="visit_full_name" type="text" name="full_name" value="{{ old('full_name') }}" required>
                            </div>
                            <div>
                                <label for="visit_phone">Telephone</label>
                                <input id="visit_phone" type="text" name="phone" value="{{ old('phone') }}" required>
                            </div>
                            <div>
                                <label for="visit_email">Email (optionnel)</label>
                                <input id="visit_email" type="email" name="email" value="{{ old('email') }}">
                            </div>
                            <div>
                                <label for="visit_child_age_group">Age de l'enfant</label>
                                <input id="visit_child_age_group" type="text" name="child_age_group" value="{{ old('child_age_group') }}" placeholder="Ex: 3 ans">
                            </div>
                        </div>
                        <div>
                            <label for="visit_message">Message (optionnel)</label>
                            <textarea id="visit_message" name="message" rows="2">{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="btn-visit-hero">Verifier les disponibilites</button>
                    </form>
                </aside>
            </div>
        </section>

        <section class="section" style="padding:1.3rem 0 0.8rem;">
            <div class="wrap">
                <div class="grid-4 reveal" style="margin-top:0;">
                    <article class="panel">
                        <span class="icon-chip"><i class="fa-solid fa-shield-heart"></i></span>
                        <h3>Securite</h3>
                        <p class="muted">Environnement controle, propre et adapte aux tout-petits.</p>
                    </article>
                    <article class="panel">
                        <span class="icon-chip"><i class="fa-solid fa-user-group"></i></span>
                        <h3>Equipe a l'ecoute</h3>
                        <p class="muted">Professionnels bienveillants formes a la petite enfance.</p>
                    </article>
                    <article class="panel">
                        <span class="icon-chip"><i class="fa-solid fa-palette"></i></span>
                        <h3>Activites d'eveil</h3>
                        <p class="muted">Programme pedagogique adapte a chaque tranche d'age.</p>
                    </article>
                    <article class="panel">
                        <span class="icon-chip"><i class="fa-solid fa-comments"></i></span>
                        <h3>Suivi parent</h3>
                        <p class="muted">Communication claire et continue avec les familles.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap">
                <h2 class="section-title">A propos de nous</h2>
                <div class="grid-2">
                    <article class="panel">
                        <p class="muted">{{ $aboutSnippet }}</p>
                        <a href="{{ route('vitrine.about') }}" class="btn-parent" style="display:inline-flex;margin-top:0.8rem;">Lire plus</a>
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
                <a href="{{ route('vitrine.services') }}" class="btn-parent" style="display:inline-flex;margin-top:1rem;">Lire plus</a>
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
                <a href="{{ route('vitrine.activities') }}" class="btn-parent" style="display:inline-flex;margin-top:1rem;">Lire plus</a>
            </div>
        </section>

        <section class="section">
            <div class="wrap">
                <h2 class="section-title">Nos chiffres cles</h2>
                <div class="grid-4 stats-grid reveal">
                    <article class="panel">
                        <div class="stat-value">{{ ($services ?? collect())->count() > 0 ? ($services ?? collect())->count() : '6+' }}</div>
                        <p class="muted">Programmes et ateliers</p>
                    </article>
                    <article class="panel">
                        <div class="stat-value">{{ ($schedules ?? collect())->where('is_closed', false)->count() > 0 ? ($schedules ?? collect())->where('is_closed', false)->count() : '7j/7' }}</div>
                        <p class="muted">Jours d'accueil organises</p>
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
                <h2 class="section-title">Notre equipe professionnelle</h2>
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
                <h2 class="section-title">Blog & actualites</h2>
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
                            <h3>{{ ucfirst($post->platform) }} - Actualite</h3>
                            <p class="muted">{{ \Illuminate\Support\Str::limit($post->caption ?: 'Retrouvez les dernieres informations de la garderie.', 130) }}</p>
                        </article>
                    @empty
                        <article class="panel"><p class="muted">Aucun article disponible.</p></article>
                    @endforelse
                </div>
                <a href="{{ route('vitrine.activities') }}" class="btn-parent" style="display:inline-flex;margin-top:1rem;">Lire plus</a>
            </div>
        </section>

        <section class="section section-warm">
            <div class="wrap">
                <h2 class="section-title">Parents heureux - nos temoignages</h2>
                <div class="grid-3 reveal">
                    @forelse(($testimonials ?? collect())->take(3) as $testimonial)
                        <article class="panel">
                            <p class="muted">"{{ $testimonial->content }}"</p>
                            <strong>- {{ $testimonial->parent_name }}{{ $testimonial->child_name ? ' (Parent de '.$testimonial->child_name.')' : '' }}</strong>
                        </article>
                    @empty
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
                    @endforelse
                </div>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap">
                <article class="panel" style="text-align:center;max-width:860px;margin:0 auto;">
                    <h2 class="section-title" style="margin-bottom:0.5rem;">Newsletter</h2>
                    <p class="section-subtitle" style="margin:0 auto 1rem;">Recevez nos actualites, nos conseils parents et les prochaines activites de la garderie.</p>
                    @if(session('newsletter_success'))
                        <div class="alert alert-ok" style="max-width:620px;margin:0 auto 1rem;">{{ session('newsletter_success') }}</div>
                    @endif
                    @if($errors->newsletter->any())
                        <div class="alert alert-err" style="max-width:620px;margin:0 auto 1rem;">
                            <ul style="margin:0; padding-left:1.2rem; text-align:left;">
                                @foreach($errors->newsletter->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('vitrine.newsletter.subscribe') }}" method="POST" style="display:flex;gap:0.6rem;justify-content:center;flex-wrap:wrap;">
                        @csrf
                        <input type="email" name="newsletter_email" placeholder="Votre email" style="min-width:280px;max-width:420px;border:1px solid #d6e1ea;border-radius:999px;padding:0.7rem 1rem;">
                        <button type="submit" class="btn-parent" style="border:0;cursor:pointer;">S'abonner</button>
                    </form>
                </article>
            </div>
        </section>

        <section class="section">
            <div class="wrap">
                <h2 class="section-title">Questions frequentes</h2>
                <div class="grid-2 reveal">
                    <article class="panel">
                        <h3>Quels ages accueillez-vous ?</h3>
                        <p class="muted">Nous accueillons les enfants selon les tranches d'age definies par la structure, avec un accompagnement adapte a chaque etape.</p>
                    </article>
                    <article class="panel">
                        <h3>Comment se passe l'inscription ?</h3>
                        <p class="muted">Vous demandez d'abord une visite, puis nous vous accompagnons pour la preinscription et le dossier final.</p>
                    </article>
                    <article class="panel">
                        <h3>Comment suivez-vous l'evolution de l'enfant ?</h3>
                        <p class="muted">Notre equipe partage regulierement les observations et l'evolution pedagogique avec les parents.</p>
                    </article>
                    <article class="panel">
                        <h3>Quand recevez-vous une reponse ?</h3>
                        <p class="muted">Nous faisons notre maximum pour repondre a chaque demande dans un delai de 24h.</p>
                    </article>
                </div>
            </div>
        </section>
    </main>
@endsection
