@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | A propos')
@section('meta_description', 'Decouvrez la mission de la Garderie Ancre des Elites: ecoute, respect du rythme, bienveillance, autonomie et partenariat avec les familles a Sfax.')

@section('content')
    @php
        $heroImage = $page?->hero_image ? asset('storage/'.$page->hero_image) : 'https://images.unsplash.com/photo-1526634332515-d56c5fd16991?auto=format&fit=crop&w=1800&q=80';
        $aboutImage = file_exists(public_path('images/about-child-tunisie.jpg'))
            ? asset('images/about-child-tunisie.jpg')
            : 'https://images.unsplash.com/photo-1519345182560-3f2917c472ef?auto=format&fit=crop&w=1400&q=80';
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
                <h2 class="section-title">Mission, vision et valeurs</h2>
                <p class="section-subtitle">Nos engagements definissent notre maniere d accueillir, d encadrer et d accompagner chaque enfant.</p>

                <div class="reveal" style="display:grid;grid-template-columns:1fr;gap:1rem;margin-top:1.2rem;">
                    <article class="panel">
                        <span class="icon-chip"><i class="fa-solid fa-bullseye"></i></span>
                        <h3>Mission</h3>
                        <p class="muted">{!! nl2br(e($page?->mission ?: 'Accompagner les enfants dans leur developpement social, affectif et cognitif avec une approche pedagogique positive.')) !!}</p>
                    </article>
                    <article class="panel">
                        <span class="icon-chip"><i class="fa-solid fa-eye"></i></span>
                        <h3>Vision</h3>
                        <p class="muted">{!! nl2br(e($page?->vision ?: 'Devenir une reference locale de la petite enfance grace a un cadre moderne, bienveillant et stimulant.')) !!}</p>
                    </article>
                    <article class="panel">
                        <span class="icon-chip"><i class="fa-solid fa-heart"></i></span>
                        <h3>Valeurs</h3>
                        <p class="muted">{!! nl2br(e($page?->valeurs ?: 'Respect, securite, ecoute active, autonomie, cooperation avec les parents et recherche continue de qualite.')) !!}</p>
                    </article>
                </div>
            </div>
        </section>
    </main>
@endsection
