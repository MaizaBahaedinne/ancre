@extends('public.vitrine.layout')

@section('title', ($settings?->site_name ?: 'Ancre Des Elites').' | Blog & actualites')
@section('meta_description', 'Actualites et conseils de la garderie Ancre des Elites.')

@section('content')
    <main>
        <section class="section section-soft">
            <div class="wrap">
                <h1 class="section-title title-center">Blog & actualites</h1>
                <p class="section-subtitle" style="text-align:center;margin:0 auto;">Retrouvez nos conseils, annonces et informations pour les parents.</p>
                <div class="grid-3 reveal" style="margin-top:1.4rem;">
                    @forelse($posts as $post)
                        <article class="panel">
                            <div style="height:190px;border-radius:14px;overflow:hidden;margin-bottom:0.7rem;background:#edf3f7;">
                                <img src="{{ $post->cover_url ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=1000&q=80' }}" alt="{{ $post->title }}" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            <h3>{{ $post->title }}</h3>
                            <p class="muted">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content ?: ''), 140) }}</p>
                        </article>
                    @empty
                        <article class="panel"><p class="muted">Aucun article publie pour le moment.</p></article>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
@endsection
