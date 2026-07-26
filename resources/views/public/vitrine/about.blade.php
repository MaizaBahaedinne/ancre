@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | A propos')

@section('content')
    @php
        $heroImage = $page?->hero_image ? asset('storage/'.$page->hero_image) : 'https://images.unsplash.com/photo-1526634332515-d56c5fd16991?auto=format&fit=crop&w=1800&q=80';
    @endphp
    <main>
        <section class="hero-shell" style="background: linear-gradient(110deg, rgba(15, 41, 66, 0.83), rgba(15, 41, 66, 0.56)), url('{{ $heroImage }}') center/cover no-repeat;">
            <div class="hero-content">
                <span class="hero-kicker"><i class="fa-solid fa-child-reaching"></i> A propos</span>
                <h1>{{ $page?->hero_title ?: 'Qui sommes-nous ?' }}</h1>
                <p class="hero-lead">{{ $page?->hero_subtitle ?: 'Une equipe engagee pour le bien-etre et l\'evolution des enfants.' }}</p>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap grid-2">
                <article class="card">
                    <h2 class="section-title" style="margin-top:0;">{{ $page?->title ?: 'Notre histoire' }}</h2>
                    <div class="text-muted">{!! nl2br(e($page?->content ?: 'Contenu a completer depuis la plateforme admin.')) !!}</div>
                </article>

                <aside class="image-card">
                    <img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1400&q=80" alt="Equipe educative et enfants">
                </aside>
            </div>
        </section>

        <section class="section section-band">
            <div class="wrap">
                <h2 class="section-title">Mission, vision et valeurs</h2>
                <p class="section-subtitle">Nos principes guident chaque decision pedagogique et chaque interaction avec les enfants et les familles.</p>

                <div class="mv-grid stagger">
                    <article class="card">
                        <span class="feature-icon"><i class="fa-solid fa-bullseye"></i></span>
                        <h3>Mission</h3>
                        <p class="text-muted">Accompagner chaque enfant dans son developpement affectif, social et cognitif avec des activites adaptees a son rythme.</p>
                    </article>
                    <article class="card">
                        <span class="feature-icon"><i class="fa-solid fa-eye"></i></span>
                        <h3>Vision</h3>
                        <p class="text-muted">Devenir une reference locale de l'education prescolaire moderne, inclusive et bienveillante.</p>
                    </article>
                    <article class="card">
                        <span class="feature-icon"><i class="fa-solid fa-heart"></i></span>
                        <h3>Valeurs</h3>
                        <p class="text-muted">Respect, securite, ecoute active, autonomie, collaboration avec les parents et excellence educative.</p>
                    </article>
                </div>
            </div>
        </section>
    </main>
@endsection
