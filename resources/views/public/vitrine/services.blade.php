@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | Services')

@section('content')
    @php
        $heroImage = $page?->hero_image ? asset('storage/'.$page->hero_image) : 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=1800&q=80';
    @endphp
    <main>
        <section class="hero-shell" style="background: linear-gradient(110deg, rgba(15, 41, 66, 0.83), rgba(15, 41, 66, 0.56)), url('{{ $heroImage }}') center/cover no-repeat;">
            <div class="hero-content">
                <span class="hero-kicker"><i class="fa-solid fa-shield-heart"></i> Services</span>
                <h1>{{ $page?->hero_title ?: 'Nos services' }}</h1>
                <p class="hero-lead">{{ $page?->hero_subtitle ?: 'Un accompagnement complet pour vos enfants.' }}</p>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap">
                <p class="section-subtitle">{!! nl2br(e($page?->content ?: '')) !!}</p>
                <div class="grid-3">
                    @forelse($services as $service)
                        <article class="card">
                            <span class="feature-icon"><i class="{{ $service->icon ?: 'fa-solid fa-check' }}"></i></span>
                            <h3>{{ $service->title }}</h3>
                            <p class="text-muted">{{ $service->description }}</p>
                        </article>
                    @empty
                        <article class="card"><p class="text-muted">Aucun service actif.</p></article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="section section-band" style="padding-top:0;">
            <div class="wrap grid-2">
                <article class="card">
                    <h2 class="section-title" style="margin-top:0;">Horaires d'accueil</h2>
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

                <aside class="image-card">
                    <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1400&q=80" alt="Service educatif en classe">
                </aside>
            </div>
        </section>
    </main>
@endsection
