@extends('public.vitrine.layout')

@section('title', ($post->title ?? 'Article').' | '.($settings?->site_name ?: 'Ancre Des Elites'))
@section('meta_description', $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content ?: ''), 150))

@section('content')
    @php
        $heroImage = $post->cover_url ?: asset('images/vitrine/vitrine-04.jpg');
        $likes = (int) ($reactionCounts['like'] ?? 0);
        $loves = (int) ($reactionCounts['love'] ?? 0);
        $claps = (int) ($reactionCounts['clap'] ?? 0);
        $publishedBy = $settings?->site_name ?: 'Administration';

        $excerptText = trim((string) ($post->excerpt ?? ''));
        $excerptNeedsToggle = \Illuminate\Support\Str::length($excerptText) > 260;
        $excerptPreview = $excerptNeedsToggle ? \Illuminate\Support\Str::limit($excerptText, 260) : $excerptText;

        $contentText = trim((string) ($post->content ?? ''));
        $contentNeedsToggle = \Illuminate\Support\Str::length($contentText) > 1200;
        $contentPreview = $contentNeedsToggle ? \Illuminate\Support\Str::limit($contentText, 1200) : $contentText;
    @endphp

    <main>
        <section class="hero hero-subpage">
            <div class="hero-media" aria-hidden="true">
                <span class="hero-slide" style="background-image:url('{{ $heroImage }}');animation:none;opacity:1;transform:scale(1.04);"></span>
            </div>
            <div class="hero-content">
                <span class="hero-badge"><i class="fa-solid fa-newspaper"></i> Actualites</span>
                <h1 style="overflow-wrap:anywhere;word-break:break-word;line-height:1.15;max-width:22ch;">{{ $post->title }}</h1>
                <p class="hero-lead" style="max-width:100%;">Publie le {{ $post->published_at ? $post->published_at->format('d/m/Y H:i') : 'recentement' }} par {{ $publishedBy }}</p>
            </div>
        </section>

        <section class="section section-soft">
            <div class="wrap" style="display:grid;gap:1rem;">
                <article class="panel">
                    @if(session('blog_success'))
                        <div class="alert alert-ok" style="margin-bottom:1rem;">{{ session('blog_success') }}</div>
                    @endif

                    @if($excerptText !== '')
                        <p class="muted" style="margin-top:0;" data-toggle-target="excerpt-preview">{{ $excerptPreview }}</p>
                        @if($excerptNeedsToggle)
                            <p class="muted" style="display:none;margin-top:0;" data-toggle-target="excerpt-full">{{ $excerptText }}</p>
                            <button type="button" class="btn-parent" data-toggle-control="excerpt" style="margin-bottom:0.9rem;">Lire plus</button>
                        @endif
                    @endif

                    <div class="muted" style="color:var(--ink-700);" data-toggle-target="content-preview">{!! nl2br(e($contentPreview)) !!}</div>
                    @if($contentNeedsToggle)
                        <div class="muted" style="color:var(--ink-700);display:none;" data-toggle-target="content-full">{!! nl2br(e($contentText)) !!}</div>
                        <button type="button" class="btn-parent" data-toggle-control="content" style="margin-top:0.8rem;">Lire plus</button>
                    @endif

                    <div class="media-frame" style="margin-top:1rem;">
                        <img src="{{ $heroImage }}" alt="{{ $post->title }}">
                    </div>

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

                <section class="panel">
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
                                @php
                                    $commentText = trim((string) $comment->content);
                                    $commentNeedsToggle = \Illuminate\Support\Str::length($commentText) > 280;
                                @endphp
                                <p class="muted" style="margin:0.3rem 0 0;" data-comment-preview="{{ $comment->id }}">{{ $commentNeedsToggle ? \Illuminate\Support\Str::limit($commentText, 280) : $commentText }}</p>
                                @if($commentNeedsToggle)
                                    <p class="muted" style="display:none;margin:0.3rem 0 0;" data-comment-full="{{ $comment->id }}">{{ $commentText }}</p>
                                    <button type="button" class="btn-parent" data-comment-toggle="{{ $comment->id }}" style="margin-top:0.55rem;">Lire plus</button>
                                @endif
                                <small class="muted">{{ $comment->created_at?->format('d/m/Y H:i') }}</small>
                            </article>
                        @empty
                            <p class="muted">Aucun commentaire pour le moment.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </section>
    </main>

    <script>
        document.querySelectorAll('[data-toggle-control]').forEach(function (button) {
            button.addEventListener('click', function () {
                const key = button.getAttribute('data-toggle-control');
                const preview = document.querySelector('[data-toggle-target="' + key + '-preview"]');
                const full = document.querySelector('[data-toggle-target="' + key + '-full"]');

                if (!preview || !full) {
                    return;
                }

                const isExpanded = full.style.display !== 'none';
                full.style.display = isExpanded ? 'none' : '';
                preview.style.display = isExpanded ? '' : 'none';
                button.textContent = isExpanded ? 'Lire plus' : 'Lire moins';
            });
        });

        document.querySelectorAll('[data-comment-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                const id = button.getAttribute('data-comment-toggle');
                const preview = document.querySelector('[data-comment-preview="' + id + '"]');
                const full = document.querySelector('[data-comment-full="' + id + '"]');

                if (!preview || !full) {
                    return;
                }

                const isExpanded = full.style.display !== 'none';
                full.style.display = isExpanded ? 'none' : '';
                preview.style.display = isExpanded ? '' : 'none';
                button.textContent = isExpanded ? 'Lire plus' : 'Lire moins';
            });
        });
    </script>
@endsection
