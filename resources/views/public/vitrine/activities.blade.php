@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | Activites')

@section('content')
    <main>
        <section class="hero-shell" style="background: linear-gradient(110deg, rgba(15, 41, 66, 0.83), rgba(15, 41, 66, 0.56)), url('https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=1800&q=80') center/cover no-repeat;">
            <div class="hero-content">
                <span class="hero-kicker"><i class="fa-solid fa-camera-retro"></i> Activites</span>
                <h1>{{ $page?->hero_title ?: 'Nos activites au quotidien' }}</h1>
                <p class="hero-lead">{{ $page?->hero_subtitle ?: 'Publications Facebook, Instagram et TikTok.' }}</p>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap">
                <p class="section-subtitle">{!! nl2br(e($page?->content ?: '')) !!}</p>
                <div class="social-list">
                    @forelse($socialPosts as $post)
                        <a href="{{ $post->post_url }}" target="_blank" rel="noopener" class="social-item">
                            <div class="social-thumb">
                                @if($post->thumbnail_url)
                                    <img src="{{ $post->thumbnail_url }}" alt="{{ $post->platform }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1472162072942-cd5147eb3902?auto=format&fit=crop&w=1000&q=80" alt="Activite garderie">
                                @endif
                            </div>
                            <div class="social-meta">
                                <strong>{{ ucfirst($post->platform) }}</strong>
                                <div>{{ $post->caption ?: 'Voir la publication' }}</div>
                            </div>
                        </a>
                    @empty
                        <article class="card"><p class="text-muted">Aucune publication sociale active.</p></article>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
@endsection
