@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | Accueil')
@section('meta_description', 'Garderie Ancre des Elites a Tunis: cadre securise, equipe bienveillante, activites educatives et accompagnement des familles. Demandez une visite.')

@section('content')
    @php
        $pageMeta = is_array($page?->meta ?? null) ? $page->meta : [];
        $vitrineImage1 = asset('images/vitrine/vitrine-01.jpg');
        $vitrineImage2 = asset('images/vitrine/vitrine-02.jpg');
        $vitrineImage3 = asset('images/vitrine/vitrine-03.jpg');
        $vitrineImage4 = asset('images/vitrine/vitrine-04.jpg');
        $vitrineImage5 = asset('images/vitrine/vitrine-05.jpg');

        $heroImage = $page?->hero_image ? asset('storage/'.$page->hero_image) : $vitrineImage1;
        $heroImagesDefault = [
            $heroImage,
            $vitrineImage2,
            $vitrineImage3,
            $vitrineImage4,
            $vitrineImage5,
        ];

        $heroImages = [];
        if (!empty($pageMeta['hero_images']) && is_array($pageMeta['hero_images'])) {
            foreach ($pageMeta['hero_images'] as $rawImage) {
                if (!is_string($rawImage) || trim($rawImage) === '') {
                    continue;
                }

                $rawImage = trim($rawImage);
                $heroImages[] = \Illuminate\Support\Str::startsWith($rawImage, 'http') ? $rawImage : asset(ltrim($rawImage, '/'));
            }
        }
        if (empty($heroImages)) {
            $heroImages = $heroImagesDefault;
        }
        $heroImages = array_slice($heroImages, 0, 6);
        $heroImageStepSeconds = max(4, min(12, (int) ($pageMeta['home_animation_duration_seconds'] ?? 6)));
        $heroCycleDurationSeconds = max(12, count($heroImages) * $heroImageStepSeconds);

        $heroBadgeText = $pageMeta['hero_badge_text'] ?? 'Garderie de confiance a Sfax';

        $aboutSnippet = $aboutPage?->content
            ? \Illuminate\Support\Str::limit(strip_tags($aboutPage->content), 240)
            : 'Nous accompagnons chaque enfant avec une approche pedagogique moderne, bienveillante et centree sur son rythme.';

        $aboutImageUrl = $pageMeta['about_image_url'] ?? null;
        if (is_string($aboutImageUrl) && trim($aboutImageUrl) !== '') {
            $candidate = trim($aboutImageUrl);
            if (\Illuminate\Support\Str::startsWith($candidate, 'http')) {
                $aboutImage = $candidate;
            } else {
                $relativePath = ltrim($candidate, '/');
                $aboutImage = file_exists(public_path($relativePath))
                    ? asset($relativePath)
                    : $vitrineImage5;
            }
        } else {
            $aboutImage = $vitrineImage5;
        }

        $aboutHighlights = [
            'Encadrement securise et bienveillant.',
            'Programme d eveil adapte a chaque enfant.',
            'Communication continue avec les parents.',
        ];
        if (!empty($pageMeta['about_highlights']) && is_array($pageMeta['about_highlights'])) {
            $aboutHighlights = array_values(array_filter($pageMeta['about_highlights'], fn ($item) => is_string($item) && trim($item) !== ''));
            if (empty($aboutHighlights)) {
                $aboutHighlights = [
                    'Encadrement securise et bienveillant.',
                    'Programme d eveil adapte a chaque enfant.',
                    'Communication continue avec les parents.',
                ];
            }
        }

        $inscriptionUrl = $settings?->parent_space_url ?: route('login');
        $vitrineContactUrl = \Illuminate\Support\Facades\Route::has('vitrine.contact') ? route('vitrine.contact') : url('/contact');
        $vitrineServicesUrl = \Illuminate\Support\Facades\Route::has('vitrine.services') ? route('vitrine.services') : url('/services');
        $vitrineActivitiesUrl = \Illuminate\Support\Facades\Route::has('vitrine.activities') ? route('vitrine.activities') : url('/activites');
        $vitrineNewsletterSubmitUrl = \Illuminate\Support\Facades\Route::has('vitrine.newsletter.subscribe') ? route('vitrine.newsletter.subscribe') : url('/newsletter/subscribe');
        $vitrineVisitSubmitUrl = \Illuminate\Support\Facades\Route::has('vitrine.visit-request.submit') ? route('vitrine.visit-request.submit') : url('/visit-request');

        $showBlogSection = (bool) ($pageMeta['home_show_blog_section'] ?? true);
        $showTestimonialsSection = (bool) ($pageMeta['home_show_testimonials_section'] ?? true);

        $statsMode = ($pageMeta['home_stats_mode'] ?? 'auto') === 'manual' ? 'manual' : 'auto';
        $autoStats = is_array($statsAuto ?? null) ? $statsAuto : [
            'services' => ($services ?? collect())->count(),
            'parents' => 0,
            'staff' => ($professionals ?? collect())->count(),
            'activities' => ($activitiesFeatured ?? collect())->count(),
        ];

        $statsDisplay = [
            'services' => (int) ($autoStats['services'] ?? 0),
            'parents' => (int) ($autoStats['parents'] ?? 0),
            'staff' => (int) ($autoStats['staff'] ?? 0),
            'activities' => (int) ($autoStats['activities'] ?? 0),
        ];

        if ($statsMode === 'manual') {
            $statsDisplay['services'] = (int) ($pageMeta['home_manual_services_count'] ?? $statsDisplay['services']);
            $statsDisplay['parents'] = (int) ($pageMeta['home_manual_parents_count'] ?? $statsDisplay['parents']);
            $statsDisplay['staff'] = (int) ($pageMeta['home_manual_staff_count'] ?? $statsDisplay['staff']);
            $statsDisplay['activities'] = (int) ($pageMeta['home_manual_activities_count'] ?? $statsDisplay['activities']);
        }

        $figures = [
            'children' => (int) ($pageMeta['home_stat_children_count'] ?? ($pageMeta['home_manual_parents_count'] ?? 100)),
            'educators' => (int) ($pageMeta['home_stat_educators_count'] ?? ($pageMeta['home_manual_staff_count'] ?? 10)),
            'experience' => (int) ($pageMeta['home_stat_experience_years'] ?? 5),
            'activities' => (int) ($pageMeta['home_stat_activities_count'] ?? ($pageMeta['home_manual_activities_count'] ?? 20)),
        ];
    @endphp

    <main>
        <section class="hero" style="--hero-cycle-duration: {{ $heroCycleDurationSeconds }}s;">
            <div class="hero-media" aria-hidden="true">
                @if(!empty($heroImages[0]))
                    <img
                        src="{{ $heroImages[0] }}"
                        alt=""
                        class="hero-lcp-image"
                        fetchpriority="high"
                        decoding="async"
                        loading="eager"
                        referrerpolicy="no-referrer"
                    >
                @endif
                @foreach(array_slice($heroImages, 1, 2) as $index => $image)
                    <span class="hero-slide" style="background-image:url(&quot;{{ $image }}&quot;);animation-delay:{{ ($index + 1) * $heroImageStepSeconds }}s;"></span>
                @endforeach
            </div>
            <div class="hero-content hero-grid">
                <div class="hero-copy">
                    <span class="hero-badge"><i class="fa-solid fa-seedling"></i> {{ $heroBadgeText }}</span>
                    <h1>{{ $settings?->hero_title ?: ($page?->hero_title ?: 'Ancre des Elites, une garderie ou votre enfant grandit en confiance') }}</h1>
                    <p class="hero-lead">{{ $settings?->hero_subtitle ?: ($page?->hero_subtitle ?: 'Chaque journee est pensee pour son bien-etre, son eveil et son autonomie dans un cadre securise et bienveillant.') }}</p>

                    <div class="hero-actions">
                        <a href="{{ $inscriptionUrl }}" class="btn-hero-alt"><i class="fa-solid fa-user-plus"></i> Inscrire mon enfant</a>
                        <a href="{{ $vitrineContactUrl }}" class="btn-hero"><i class="fa-solid fa-phone-volume"></i> Nous contacter</a>
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

                    <form method="POST" action="{{ $vitrineVisitSubmitUrl }}" class="form-grid">
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
                <h2 class="section-title title-center">Pourquoi les parents nous choisissent</h2>
                <div class="grid-4 reveal" style="margin-top:0;">
                    <article class="panel feature-card">
                        <span class="icon-chip"><i class="fa-solid fa-shield-heart"></i></span>
                        <h3>Securite</h3>
                        <p class="muted">Environnement controle, propre et adapte aux tout-petits.</p>
                    </article>
                    <article class="panel feature-card">
                        <span class="icon-chip"><i class="fa-solid fa-user-group"></i></span>
                        <h3>Equipe a l'ecoute</h3>
                        <p class="muted">Professionnels bienveillants formes a la petite enfance.</p>
                    </article>
                    <article class="panel feature-card">
                        <span class="icon-chip"><i class="fa-solid fa-palette"></i></span>
                        <h3>Activites d'eveil</h3>
                        <p class="muted">Programme pedagogique adapte a chaque tranche d'age.</p>
                    </article>
                    <article class="panel feature-card">
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
                <div class="grid-2 reveal">
                    <aside class="media-frame" style="min-height:280px;">
                        <img src="{{ $vitrineImage5 }}" alt="Enfant tunisien" loading="lazy">
                    </aside>
                    <article class="panel" style="display:grid;gap:0.7rem;align-content:start;">
                        <p class="muted" style="font-size:1.02rem;">{{ $aboutSnippet }}</p>
                        <div class="muted" style="display:grid;gap:0.45rem;">
                            @foreach($aboutHighlights as $highlight)
                                <span><i class="fa-solid fa-circle-check" style="color:#2d6f85;"></i> {{ $highlight }}</span>
                            @endforeach
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="wrap">
                <h2 class="section-title title-center">Nos chiffres cles</h2>
                <div class="grid-4 stats-grid reveal">
                    <article class="panel">
                        <span class="stat-icon"><i class="fa-solid fa-child-reaching"></i></span>
                        <div class="stat-value">+{{ $figures['children'] }}</div>
                        <p class="muted"><strong>enfants accompagnes</strong></p>
                        <p class="muted">Chaque annee, nous contribuons au bien-etre et a l epanouissement des enfants.</p>
                    </article>
                    <article class="panel">
                        <span class="stat-icon"><i class="fa-solid fa-chalkboard-user"></i></span>
                        <div class="stat-value">+{{ $figures['educators'] }}</div>
                        <p class="muted"><strong>educateurs passionnes</strong></p>
                        <p class="muted">Une equipe qualifiee et attentive pour encadrer les enfants au quotidien.</p>
                    </article>
                    <article class="panel">
                        <span class="stat-icon"><i class="fa-solid fa-calendar-check"></i></span>
                        <div class="stat-value">+{{ $figures['experience'] }}</div>
                        <p class="muted"><strong>annees d experience</strong></p>
                        <p class="muted">Un savoir-faire reconnu dans l accompagnement scolaire et educatif.</p>
                    </article>
                    <article class="panel">
                        <span class="stat-icon"><i class="fa-solid fa-puzzle-piece"></i></span>
                        <div class="stat-value">+{{ $figures['activities'] }}</div>
                        <p class="muted"><strong>activites proposees</strong></p>
                        <p class="muted">Des activites variees pour apprendre, creer, bouger et grandir.</p>
                    </article>
                </div>
            </div>
        </section>

        @if($showTestimonialsSection)
            <section class="section section-warm">
                <div class="wrap">
                    <h2 class="section-title title-center">Parents heureux</h2>
                    <div class="testimonials-strip reveal" data-testimonial-strip>
                        @forelse(($testimonials ?? collect()) as $testimonial)
                            @php
                                $parentPhoto = !empty($testimonial->parent_photo_url)
                                    ? $testimonial->parent_photo_url
                                    : 'https://ui-avatars.com/api/?name='.urlencode($testimonial->parent_name ?: 'Parent').'&background=2d6f85&color=ffffff&size=128';
                            @endphp
                            <article class="panel testimonial-item">
                                <div style="display:flex;align-items:center;gap:0.7rem;margin-bottom:0.65rem;">
                                    <img src="{{ $parentPhoto }}" alt="{{ $testimonial->parent_name }}" style="width:44px;height:44px;border-radius:999px;object-fit:cover;border:2px solid #d9e4ec;">
                                    <strong>{{ $testimonial->parent_name }}</strong>
                                </div>
                                <div class="stars">
                                    @for($i = 0; $i < ((int)($testimonial->rating ?? 5)); $i++)
                                        <i class="fa-solid fa-star"></i>
                                    @endfor
                                </div>
                                <p class="muted">"{{ $testimonial->content }}"</p>
                                <strong>- {{ $testimonial->child_name ? 'Parent de '.$testimonial->child_name : 'Parent' }}</strong>
                            </article>
                        @empty
                            <article class="panel testimonial-item">
                                <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                                <p class="muted">"Une equipe tres professionnelle, ma fille adore venir tous les matins."</p>
                                <strong>- Parent de Lina</strong>
                            </article>
                        @endforelse
                    </div>
                </div>
            </section>
        @endif

        <section class="section">
            <div class="wrap">
                <h2 class="section-title title-center">Nos services</h2>
                <div class="grid-4 reveal">
                    @forelse(($servicesFeatured ?? collect()) as $service)
                        <article class="panel">
                            @if($service->thumbnail)
                                <div style="height:160px;border-radius:14px;overflow:hidden;margin-bottom:0.7rem;background:#edf3f7;">
                                    <img src="{{ \Illuminate\Support\Str::startsWith($service->thumbnail, ['http://', 'https://']) ? $service->thumbnail : asset('storage/'.$service->thumbnail) }}" alt="{{ $service->title }}" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                            @else
                                <span class="icon-chip"><i class="{{ $service->icon ?: 'fa-solid fa-star' }}"></i></span>
                            @endif
                            <h3>{{ $service->title }}</h3>
                            <p class="muted">{{ \Illuminate\Support\Str::limit($service->description, 110) }}</p>
                        </article>
                    @empty
                        <article class="panel"><p class="muted">Aucun service disponible.</p></article>
                    @endforelse
                </div>
                <a href="{{ $vitrineServicesUrl }}" class="btn-parent" style="display:inline-flex;margin-top:1rem;">Lire plus</a>
            </div>
        </section>

        <section class="section section-warm">
            <div class="wrap">
                <h2 class="section-title title-center">Nos activites</h2>
                <div class="social-grid reveal">
                    @forelse(($activitiesFeatured ?? collect()) as $post)
                        <a href="{{ $post->post_url }}" target="_blank" rel="noopener" class="social-card">
                            <div class="social-thumb">
                                @if($post->thumbnail_path)
                                    <img src="{{ asset('storage/'.$post->thumbnail_path) }}" alt="{{ $post->platform }}">
                                @elseif($post->thumbnail_url)
                                    <img src="{{ $post->thumbnail_url }}" alt="{{ $post->platform }}">
                                @else
                                    <img src="{{ $vitrineImage3 }}" alt="Publication activite">
                                @endif
                            </div>
                            <div class="social-meta"><strong>{{ ucfirst($post->platform) }}</strong> - {{ $post->caption ?: 'Voir la publication' }}</div>
                        </a>
                    @empty
                        <article class="panel"><p class="muted">Aucune activite disponible.</p></article>
                    @endforelse
                </div>
                <a href="{{ $vitrineActivitiesUrl }}" class="btn-parent" style="display:inline-flex;margin-top:1rem;">Lire plus</a>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap">
                <h2 class="section-title">Nos packages</h2>
                <p class="section-subtitle">Des formules adaptees aux besoins de votre famille.</p>
                <div class="grid-3 reveal" style="margin-top:1.5rem;">
                    @forelse(($packages ?? collect())->where('is_active', true) as $package)
                        <article class="panel">
                            <h3>{{ $package->name }}</h3>
                            <p class="muted">{{ $package->description }}</p>
                            @if($package->features)
                                <div class="muted" style="font-size:0.9rem;margin-top:0.7rem;">
                                    @foreach(is_string($package->features) ? explode(',', $package->features) : $package->features as $feature)
                                        @if(trim($feature))
                                            <div style="margin:0.4rem 0;"><i class="fa-solid fa-check" style="color:#2d6f85;margin-right:0.4rem;"></i>{{ trim($feature) }}</div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @empty
                        <article class="panel"><p class="muted">Aucun package disponible.</p></article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="section">
            <div class="wrap">
                <h2 class="section-title">Horaires d'accueil</h2>
                <p class="section-subtitle">Ouvert du lundi au vendredi pour accueillir votre enfant.</p>
                <div class="grid-2 reveal" style="margin-top:1.5rem;">
                    <article class="panel">
                        <h3><i class="fa-solid fa-calendar-days" style="margin-right:0.5rem;"></i>Jours d'ouverture</h3>
                        <p class="muted">
                            <strong>Lundi au vendredi</strong><br>
                            Nous sommes fermes les samedis, dimanches et jours feries.
                        </p>
                    </article>
                    <article class="panel">
                        <h3><i class="fa-solid fa-clock" style="margin-right:0.5rem;"></i>Horaires</h3>
                        <p class="muted">
                            <strong>8h00 - 17h30</strong><br>
                            Accueil flexible en fonction de vos besoins.<br>
                            @if($settings?->phone)
                                <small>Contactez-nous: <strong>{{ $settings->phone }}</strong></small>
                            @endif
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section section-warm">
            <div class="wrap">
                <h2 class="section-title">Notre Equipe</h2>
                <p class="section-subtitle">Des professionnels dedies au bien-etre de vos enfants.</p>
                <div class="grid-4 reveal" style="margin-top:1.5rem;">
                    @forelse(($professionals ?? collect()) as $member)
                        @php
                            $memberPhoto = $member->photo
                                ? (\Illuminate\Support\Str::startsWith($member->photo, ['http://', 'https://']) ? $member->photo : asset('storage/'.$member->photo))
                                : $vitrineImage4;
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

        <section class="section newsletter-band">
            <div class="wrap">
                <h2 class="section-title title-center" style="margin-bottom:0.5rem;">Restez informes</h2>
                <p class="section-subtitle" style="margin:0 auto 1rem;text-align:center;">Inscrivez-vous a notre newsletter pour recevoir nos actualites et les prochaines activites.</p>
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
                <form action="{{ $vitrineNewsletterSubmitUrl }}" method="POST" style="display:flex;gap:0.6rem;justify-content:center;flex-wrap:wrap;">
                    @csrf
                    <input type="email" name="newsletter_email" placeholder="Votre email" style="min-width:280px;max-width:420px;border:1px solid #d6e1ea;border-radius:999px;padding:0.7rem 1rem;">
                    <button type="submit" class="btn-parent" style="border:0;cursor:pointer;">S'abonner</button>
                </form>
            </div>
        </section>

        <section class="section">
            <div class="wrap">
                <h2 class="section-title">Questions frequentes</h2>
                <div class="grid-2 reveal">
                    @forelse(($faqs ?? collect()) as $faq)
                        <article class="panel">
                            <h3>{{ $faq->question }}</h3>
                            <p class="muted">{{ $faq->answer }}</p>
                        </article>
                    @empty
                        <article class="panel"><p class="muted">La FAQ sera bientot disponible. Vous pouvez l'ajouter depuis la plateforme d'administration.</p></article>
                    @endforelse
                </div>
            </div>
        </section>

    </main>
@endsection
