@extends('public.vitrine.layout')

@section('title', ($post->title ?? 'Article').' | '.($settings?->site_name ?: 'Ancre Des Elites'))
@section('meta_description', $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content ?: ''), 150))

@section('content')
    @php
        $heroImage = $post->cover_url ?: 'https://images.unsplash.com/photo-1453733190371-0a9bedd82893?auto=format&fit=crop&w=1800&q=80';
        $likes = (int) ($reactionCounts['like'] ?? 0);
        $loves = (int) ($reactionCounts['love'] ?? 0);
        $claps = (int) ($reactionCounts['clap'] ?? 0);
    @endphp

    <main>
        <section class="hero hero-subpage">
            <div class="hero-media" aria-hidden="true">
                <span class="hero-slide" style="background-image:url('{{ $heroImage }}');animation:none;opacity:1;transform:scale(1.04);"></span>
            </div>
            <div class="hero-content">
                <span class="hero-badge"><i class="fa-solid fa-newspaper"></i> Actualites</span>
                <h1>{{ $post->title }}</h1>
                <p class="hero-lead">{{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : 'Publication recente' }}</p>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap grid-2" style="align-items:start;">
                <article class="panel">
                    @if(session('blog_success'))
                        <div class="alert alert-ok" style="margin-bottom:1rem;">{{ session('blog_success') }}</div>
                    @endif

                    <p class="muted" style="margin-top:0;">{{ $post->excerpt }}</p>
                    <div class="muted" style="color:var(--ink-700);">{!! nl2br(e($post->content ?? '')) !!}</div>

                    <hr style="margin:1.2rem 0;border:none;border-top:1px solid #d9e4ec;">

                    <h3 style="margin-top:0;">Reactions des parents</h3>
                    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center;">
                        <span class="pill" style="padding:0.4rem 0.65rem;border-radius:999px;background:#edf6ff;">👍 {{ $likes }}</span>
                        <span class="pill" style="padding:0.4rem 0.65rem;border-radius:999px;background:#fff0f4;">❤️ {{ $loves }}</span>
                        <span class="pill" style="padding:0.4rem 0.65rem;border-radius:999px;background:#f2f6ea;">👏 {{ $claps }}</span>
                    </div>

                    @auth
                        @if(auth()->user()->hasRole('Parent'))
                            <form method="POST" action="{{ route('vitrine.blog.reactions.store', $post->id) }}" class="form-row" style="margin-top:0.8rem;">
                                @csrf
                                <div>
                                    <label class="form-label">Ma reaction</label>
                                    <select name="reaction" class="form-control" required>
                                        <option value="like" {{ ($myReaction ?? '') === 'like' ? 'selected' : '' }}>👍 J aime</option>
                                        <option value="love" {{ ($myReaction ?? '') === 'love' ? 'selected' : '' }}>❤️ J adore</option>
                                        <option value="clap" {{ ($myReaction ?? '') === 'clap' ? 'selected' : '' }}>👏 Bravo</option>
                                    </select>
                                </div>
                                <div style="align-self:end;">
                                    <button class="btn-parent" type="submit">Envoyer</button>
                                </div>
                            </form>
                        @endif
                    @else
                        <p class="muted" style="margin-top:0.8rem;">Connectez-vous en compte parent pour reagir.</p>
                    @endauth
                </article>

                <aside class="panel">
                    <h3 style="margin-top:0;">Commentaires des parents</h3>

                    @auth
                        @if(auth()->user()->hasRole('Parent'))
                            <form method="POST" action="{{ route('vitrine.blog.comments.store', $post->id) }}" style="margin-bottom:1rem;">
                                @csrf
                                <label class="form-label">Ajouter un commentaire</label>
                                <textarea name="content" rows="4" class="form-control" required>{{ old('content') }}</textarea>
                                @if($errors->blogComment->has('content'))
                                    <div class="alert alert-err" style="margin-top:0.55rem;">{{ $errors->blogComment->first('content') }}</div>
                                @endif
                                <button class="btn-parent" type="submit" style="margin-top:0.6rem;">Publier</button>
                            </form>
                        @else
                            <p class="muted">Cette section est reservee aux comptes parents.</p>
                        @endif
                    @else
                        <p class="muted">Connectez-vous en compte parent pour commenter.</p>
                    @endauth

                    <div style="display:grid;gap:0.65rem;">
                        @forelse($comments as $comment)
                            <article style="border:1px solid #d9e4ec;border-radius:12px;padding:0.7rem;background:#fff;">
                                <strong>{{ $comment->user?->name ?: 'Parent' }}</strong>
                                <p class="muted" style="margin:0.3rem 0 0;">{{ $comment->content }}</p>
                                <small class="muted">{{ $comment->created_at?->format('d/m/Y H:i') }}</small>
                            </article>
                        @empty
                            <p class="muted">Aucun commentaire pour le moment.</p>
                        @endforelse
                    </div>
                </aside>
            </div>
        </section>
    </main>
@endsection
