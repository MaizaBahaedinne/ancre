@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | A propos')
@section('meta_description', 'Decouvrez la mission de la Garderie Ancre des Elites: ecoute, respect du rythme, bienveillance, autonomie et partenariat avec les familles a Sfax.')

@section('content')
    @php
        $heroImage = $page?->hero_image ? asset('storage/'.$page->hero_image) : 'https://images.unsplash.com/photo-1526634332515-d56c5fd16991?auto=format&fit=crop&w=1800&q=80';
        $aboutImage = file_exists(public_path('images/about-child-tunisie.jpg'))
            ? asset('images/about-child-tunisie.jpg')
            : 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1400&q=80';
    @endphp
    <main>
        <section class="hero hero-subpage">
            <div class="hero-media" aria-hidden="true">
                <span class="hero-slide" style="background-image:url('{{ $heroImage }}');animation:none;opacity:1;transform:scale(1.04);"></span>
            </div>
            <div class="hero-content">
                <span class="hero-badge"><i class="fa-solid fa-children"></i> A propos</span>
                <h1>{{ $page?->hero_title ?: 'Une equipe a l ecoute de chaque enfant' }}</h1>
                <p class="hero-lead">{{ $page?->hero_subtitle ?: 'Nous creons un cadre affectif, educatif et securisant ou l enfant evolue avec confiance, joie et autonomie.' }}</p>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap grid-2">
                <article class="panel">
                    <h2 class="section-title" style="margin-top:0;">{{ $page?->title ?: 'Notre histoire' }}</h2>
                    <div class="muted">{!! nl2br(e($page?->content ?: 'Contenu a completer depuis la plateforme admin.')) !!}</div>
                </article>

                <aside class="media-frame">
                    <img src="{{ $aboutImage }}" alt="Enfant tunisien">
                </aside>
            </div>
        </section>

        <section class="section section-warm">
            <div class="wrap">
                <div class="grid-2">
                    <article class="panel">
                        <span class="icon-chip" style="font-size:2rem;"><i class="fa-solid fa-bullseye"></i></span>
                        <h2 class="section-title" style="margin-top:1rem;">Mission</h2>
                        <p class="muted">{!! nl2br(e($page?->mission ?: 'Accompagner les enfants dans leur developpement social, affectif et cognitif avec une approche pedagogique positive.')) !!}</p>
                    </article>
                    <article class="panel">
                        <span class="icon-chip" style="font-size:2rem;"><i class="fa-solid fa-eye"></i></span>
                        <h2 class="section-title" style="margin-top:1rem;">Vision</h2>
                        <p class="muted">{!! nl2br(e($page?->vision ?: 'Devenir une reference locale de la petite enfance grace a un cadre moderne, bienveillant et stimulant.')) !!}</p>
                    </article>
                </div>
                <div style="margin-top:2rem;">
                    <article class="panel">
                        <span class="icon-chip" style="font-size:2rem;"><i class="fa-solid fa-heart"></i></span>
                        <h2 class="section-title" style="margin-top:1rem;">Valeurs</h2>
                        <p class="muted">{!! nl2br(e($page?->valeurs ?: 'Respect, securite, ecoute active, autonomie, cooperation avec les parents et recherche continue de qualite.')) !!}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap">
                <h2 class="section-title">Notre Equipe</h2>
                <p class="section-subtitle">Une equipe passionnee et dedieea au bien-etre de vos enfants.</p>
                
                <div class="grid-3" style="margin-top:2rem;gap:1.5rem;">
                    <article class="panel" style="text-align:center;">
                        <div style="width:120px;height:120px;background:#f0f0f0;border-radius:50%;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;">
                            <i class="fa-solid fa-user" style="font-size:2rem;color:#999;"></i>
                        </div>
                        <h3>Directrice</h3>
                        <p class="muted">Accueil et coordination pedagogique</p>
                    </article>
                    <article class="panel" style="text-align:center;">
                        <div style="width:120px;height:120px;background:#f0f0f0;border-radius:50%;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;">
                            <i class="fa-solid fa-user" style="font-size:2rem;color:#999;"></i>
                        </div>
                        <h3>Educatrices</h3>
                        <p class="muted">Accompagnement et eveil des enfants</p>
                    </article>
                    <article class="panel" style="text-align:center;">
                        <div style="width:120px;height:120px;background:#f0f0f0;border-radius:50%;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;">
                            <i class="fa-solid fa-user" style="font-size:2rem;color:#999;"></i>
                        </div>
                        <h3>Personnel de soutien</h3>
                        <p class="muted">Hygiene et securite</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section section-warm">
            <div class="wrap">
                <div style="max-width:600px;margin:0 auto;text-align:center;">
                    <h2 class="section-title">Restez informes</h2>
                    <p class="section-subtitle">Inscrivez-vous a notre newsletter pour recevoir les dernieres actualites et evenements.</p>
                    
                    <form style="display:flex;gap:0.5rem;margin-top:1.5rem;" action="{{ route('vitrine.newsletter.subscribe') }}" method="POST">
                        @csrf
                        <input type="email" name="email" placeholder="Votre adresse email" required style="flex:1;padding:0.75rem;border:1px solid var(--line);border-radius:8px;font-size:0.95rem;">
                        <button type="submit" class="btn btn-primary">S'abonner</button>
                    </form>
                    <p class="muted" style="font-size:0.85rem;margin-top:0.75rem;">Nous respectons votre vie privee. Vous pouvez vous desabonner a tout moment.</p>
                </div>
            </div>
        </section>
    </main>
@endsection
