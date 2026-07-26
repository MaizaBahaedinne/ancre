@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | Activites')
@section('meta_description', 'Activites educatives et moments forts de la Garderie Ancre des Elites a Sfax: eveil, creativite, motricite et socialisation.')

@section('content')
    @php
        $heroImage = $page?->hero_image ? asset('storage/'.$page->hero_image) : 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=1800&q=80';
    @endphp
    <main>
        <section class="hero hero-subpage">
            <div class="hero-media" aria-hidden="true">
                <span class="hero-slide" style="background-image:url('{{ $heroImage }}');animation:none;opacity:1;transform:scale(1.04);"></span>
            </div>
            <div class="hero-content">
                <span class="hero-badge"><i class="fa-solid fa-camera-retro"></i> Activites</span>
                <h1>{{ $page?->hero_title ?: 'Nos activites au quotidien' }}</h1>
                <p class="hero-lead">{{ $page?->hero_subtitle ?: 'Retrouvez les moments forts partages sur Facebook, Instagram et TikTok.' }}</p>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap">
                <p class="section-subtitle">{!! nl2br(e($page?->content ?: '')) !!}</p>
                <div class="social-grid reveal">
                    @forelse($socialPosts as $post)
                        <a href="{{ $post->post_url }}" target="_blank" rel="noopener" class="social-card">
                            <div class="social-thumb">
                                @if($post->thumbnail_path)
                                    <img src="{{ asset('storage/'.$post->thumbnail_path) }}" alt="{{ $post->platform }}">
                                @elseif($post->thumbnail_url)
                                    <img src="{{ $post->thumbnail_url }}" alt="{{ $post->platform }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1472162072942-cd5147eb3902?auto=format&fit=crop&w=1000&q=80" alt="Activite garderie">
                                @endif
                            </div>
                            <div class="social-meta">
                                <strong>{{ ucfirst($post->platform) }}</strong><br>
                                {{ $post->caption ?: 'Voir la publication' }}
                            </div>
                        </a>
                    @empty
                        <article class="panel"><p class="muted">Aucune publication sociale active.</p></article>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
@endsection
