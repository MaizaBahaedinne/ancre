@extends('adminlte::page')

@section('title', 'Fil d\'accueil')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center feed-page-title-wrap">
        <h1 class="feed-page-title">Fil d'accueil</h1>
        <small class="text-muted">Timeline interne</small>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4 feed-layout-row">
        <div class="col-12 col-xl-8">
            @if($canPublish)
                <div class="feed-card feed-composer mb-4">
                    <div class="feed-card-body">
                        <div class="feed-composer-title">Creer une publication</div>
                        <form action="{{ route('platform.feed.announcements.store') }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    class="form-control feed-input"
                                    required
                                    maxlength="255"
                                    value="{{ old('title') }}"
                                    placeholder="Titre de la publication"
                                >
                            </div>
                            <div class="mb-3">
                                <textarea
                                    id="body"
                                    name="body"
                                    class="form-control feed-input feed-textarea"
                                    rows="4"
                                    required
                                    maxlength="6000"
                                    placeholder="Quoi de neuf aujourd'hui ?"
                                >{{ old('body') }}</textarea>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary feed-btn-post">
                                    <i class="fa-solid fa-paper-plane"></i> Publier
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            @forelse($feedItems as $item)
                @php
                    $itemKey = $item['source_type'].':'.$item['source_id'];
                    $reactions = $reactionSummary[$itemKey] ?? ['like' => 0, 'love' => 0, 'care' => 0, 'wow' => 0, 'total' => 0];
                    $commentCount = $commentSummary[$itemKey] ?? 0;
                    $comments = $latestComments[$itemKey] ?? [];
                    $myReaction = $currentUserReactions[$itemKey] ?? null;
                    $avatarFallback = strtoupper(substr((string) ($item['author_name'] ?? 'S'), 0, 1));
                @endphp
                <article class="feed-card mb-4" id="feed-item-{{ $item['source_type'] }}-{{ $item['source_id'] }}">
                    <div class="feed-card-body">
                        <div class="feed-item-header d-flex align-items-center gap-3">
                            <span class="feed-avatar-lg">
                                @if(!empty($item['author_avatar']))
                                    <img src="{{ $item['author_avatar'] }}" alt="Avatar {{ $item['author_name'] }}">
                                @else
                                    <span>{{ $avatarFallback }}</span>
                                @endif
                            </span>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center flex-wrap gap-2 feed-author-line">
                                    <strong>{{ $item['author_name'] }}</strong>
                                    <span class="feed-source-badge">{{ $item['source_label'] }}</span>
                                </div>
                                <small class="feed-meta-time">{{ \Carbon\Carbon::parse($item['published_at'])->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>

                        <div class="feed-body-text">
                            <h3 class="feed-title">{{ $item['title'] }}</h3>
                            <p class="feed-content mb-0">{{ $item['content'] }}</p>
                        </div>

                        @if(!empty($item['target_url']))
                            <div class="feed-link-wrap">
                                <a href="{{ $item['target_url'] }}" class="feed-source-link" target="_blank" rel="noopener">
                                    Ouvrir la source <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                        @endif

                        <div class="feed-stats d-flex justify-content-between align-items-center">
                            <small class="feed-stats-text">
                                {{ $reactions['total'] }} reactions · {{ $commentCount }} commentaires
                            </small>
                        </div>

                        <div class="feed-actions d-flex gap-2 flex-wrap">
                            @foreach(['like' => '👍', 'love' => '❤️', 'care' => '🤝', 'wow' => '🎉'] as $reactionKey => $reactionLabel)
                                <form method="POST" action="{{ route('platform.feed.reactions.store') }}">
                                    @csrf
                                    <input type="hidden" name="source_type" value="{{ $item['source_type'] }}">
                                    <input type="hidden" name="source_id" value="{{ $item['source_id'] }}">
                                    <input type="hidden" name="reaction" value="{{ $reactionKey }}">
                                    <button type="submit" class="feed-react-btn {{ $myReaction === $reactionKey ? 'is-active' : '' }}">
                                        {{ $reactionLabel }} {{ $reactions[$reactionKey] ?? 0 }}
                                    </button>
                                </form>
                            @endforeach
                        </div>

                        <div class="feed-comments">
                            @foreach($comments as $comment)
                                @php
                                    $commentAuthor = $comment->user?->name ?? 'Utilisateur';
                                    $commentAvatarFallback = strtoupper(substr((string) $commentAuthor, 0, 1));
                                @endphp
                                <div class="feed-comment-row">
                                    <span class="feed-avatar-sm">
                                        @if(!empty($comment->author_avatar))
                                            <img src="{{ $comment->author_avatar }}" alt="Avatar {{ $commentAuthor }}">
                                        @else
                                            <span>{{ $commentAvatarFallback }}</span>
                                        @endif
                                    </span>
                                    <div class="feed-comment-bubble">
                                        <div class="feed-comment-top">
                                            <strong>{{ $commentAuthor }}</strong>
                                            <small>{{ $comment->created_at?->format('d/m H:i') }}</small>
                                        </div>
                                        <div>{{ $comment->content }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <form method="POST" action="{{ route('platform.feed.comments.store') }}" class="feed-comment-form">
                            @csrf
                            <input type="hidden" name="source_type" value="{{ $item['source_type'] }}">
                            <input type="hidden" name="source_id" value="{{ $item['source_id'] }}">
                            <div class="input-group">
                                <input type="text" name="content" class="form-control feed-input" maxlength="2000" placeholder="Ecrire un commentaire..." required>
                                <button class="btn btn-primary" type="submit">Publier</button>
                            </div>
                        </form>
                    </div>
                </article>
            @empty
                <div class="feed-card">
                    <div class="feed-card-body text-center text-muted py-5">
                        Aucun contenu dans le fil pour le moment.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="col-12 col-xl-4">
            <div class="feed-card feed-calendar-card sticky-xl-top" style="top: 1rem;">
                <div class="feed-card-header">
                    <strong>Calendrier annee scolaire en cours</strong>
                </div>
                <div class="feed-card-body">
                    @if($activeAcademicYear)
                        <p class="mb-3">
                            <strong>{{ $activeAcademicYear->label }}</strong><br>
                            <small class="text-muted">{{ $activeAcademicYear->start_date?->format('d/m/Y') }} - {{ $activeAcademicYear->end_date?->format('d/m/Y') }}</small>
                        </p>

                        <div class="feed-calendar-list">
                            @forelse($activeAcademicYear->periods as $period)
                                @php
                                    $periodColor = $periodColorMap[$period->type] ?? '#6b7280';
                                    $typeLabel = \App\Models\AcademicCalendarPeriod::TYPE_OPTIONS[$period->type] ?? $period->type;
                                @endphp
                                <div class="feed-calendar-item">
                                    <span class="feed-period-dot" style="background-color: {{ $periodColor }};"></span>
                                    <div>
                                        <strong>{{ $period->title }}</strong>
                                        <div class="text-muted small">{{ $typeLabel }}</div>
                                        <div class="small">{{ $period->start_date?->format('d/m') }} - {{ $period->end_date?->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">Aucune periode definie pour cette annee.</p>
                            @endforelse
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucune annee scolaire active.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .content-wrapper {
            background: #eef2f7;
        }

        .feed-page-title-wrap {
            padding-bottom: 0.25rem;
        }

        .feed-page-title {
            margin: 0;
            font-size: 1.65rem;
            font-weight: 700;
            color: #1c1e21;
        }

        .feed-card {
            border: 1px solid #dfe3ea;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        }

        .feed-card-header {
            padding: 0.95rem 1.1rem;
            border-bottom: 1px solid #edf0f4;
        }

        .feed-card-body {
            padding: 1rem 1.1rem;
        }

        .feed-composer {
            background: linear-gradient(180deg, #ffffff, #f8fbff);
        }

        .feed-composer-title {
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #1c1e21;
        }

        .feed-input {
            border-radius: 999px;
            border: 1px solid #d8dee8;
            background: #f4f6fa;
            padding-top: 0.62rem;
            padding-bottom: 0.62rem;
        }

        .feed-input:focus {
            border-color: #2d88ff;
            box-shadow: 0 0 0 0.15rem rgba(45, 136, 255, 0.2);
            background: #fff;
        }

        .feed-textarea {
            border-radius: 18px;
            resize: vertical;
        }

        .feed-btn-post {
            border-radius: 999px;
            font-weight: 600;
            background: #1877f2;
            border-color: #1877f2;
            min-width: 140px;
        }

        .feed-item-header {
            margin-bottom: 0.85rem;
        }

        .feed-avatar-lg,
        .feed-avatar-sm {
            border-radius: 50%;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            flex-shrink: 0;
            background: linear-gradient(135deg, #2463eb, #10b7a5);
        }

        .feed-avatar-lg {
            width: 46px;
            height: 46px;
            font-size: 1rem;
        }

        .feed-avatar-sm {
            width: 34px;
            height: 34px;
            font-size: 0.82rem;
        }

        .feed-avatar-lg img,
        .feed-avatar-sm img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .feed-author-line {
            color: #1c1e21;
        }

        .feed-source-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            background: #e8f1ff;
            color: #1b5dbf;
            font-size: 0.76rem;
            font-weight: 700;
            padding: 0.18rem 0.5rem;
        }

        .feed-meta-time {
            color: #65676b;
        }

        .feed-title {
            font-size: 1.15rem;
            margin-bottom: 0.45rem;
            font-weight: 700;
            color: #1c1e21;
        }

        .feed-content {
            white-space: pre-line;
            color: #1c1e21;
            margin: 0;
        }

        .feed-body-text {
            margin-bottom: 0.65rem;
        }

        .feed-link-wrap {
            margin-bottom: 0.7rem;
        }

        .feed-source-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: #1877f2;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
        }

        .feed-source-link:hover {
            text-decoration: underline;
        }

        .feed-stats {
            margin-top: 0.65rem;
            padding-top: 0.5rem;
            border-top: 1px solid #edf0f4;
        }

        .feed-stats-text {
            color: #65676b;
            font-weight: 500;
        }

        .feed-actions {
            margin-top: 0.55rem;
            margin-bottom: 0.75rem;
            border-bottom: 1px solid #edf0f4;
            padding-bottom: 0.75rem;
        }

        .feed-react-btn {
            border: 1px solid #d4dbe5;
            background: #fff;
            color: #4b5563;
            border-radius: 999px;
            font-size: 0.86rem;
            font-weight: 600;
            padding: 0.28rem 0.65rem;
        }

        .feed-react-btn:hover {
            background: #f3f6fa;
        }

        .feed-react-btn.is-active {
            color: #0b5ed7;
            border-color: #a9caff;
            background: #eaf3ff;
        }

        .feed-comments {
            display: grid;
            gap: 0.55rem;
            margin-bottom: 0.8rem;
        }

        .feed-comment-row {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .feed-comment-bubble {
            flex: 1;
            background: #f0f2f5;
            border-radius: 14px;
            padding: 0.45rem 0.65rem;
            color: #1c1e21;
            font-size: 0.9rem;
        }

        .feed-comment-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.45rem;
            margin-bottom: 0.1rem;
        }

        .feed-comment-top small {
            color: #65676b;
            white-space: nowrap;
        }

        .feed-comment-form .btn {
            border-radius: 999px;
            min-width: 105px;
        }

        .feed-calendar-list {
            display: grid;
            gap: 0.7rem;
        }

        .feed-calendar-item {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            padding: 0.55rem;
            border-radius: 12px;
            background: #f4f7fb;
        }

        .feed-period-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-top: 0.3rem;
            flex-shrink: 0;
        }

        @media (max-width: 991.98px) {
            .feed-card-body {
                padding: 0.9rem;
            }

            .feed-title {
                font-size: 1.05rem;
            }

            .feed-comment-form .btn {
                min-width: 88px;
            }
        }
    </style>
@stop
