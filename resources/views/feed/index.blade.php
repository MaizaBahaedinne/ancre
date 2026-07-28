@extends('adminlte::page')

@section('title', 'Fil d\'accueil')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Fil d'accueil de la plateforme</h1>
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

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            @if($canPublish)
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>Nouvelle annonce / publication</strong>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('platform.feed.announcements.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label">Titre</label>
                                <input type="text" id="title" name="title" class="form-control" required maxlength="255" value="{{ old('title') }}">
                            </div>
                            <div class="mb-3">
                                <label for="body" class="form-label">Contenu</label>
                                <textarea id="body" name="body" class="form-control" rows="4" required maxlength="6000">{{ old('body') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-paper-plane"></i> Publier
                            </button>
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
                <article class="card mb-4" id="feed-item-{{ $item['source_type'] }}-{{ $item['source_id'] }}">
                    <div class="card-body">
                        <div class="feed-item-header d-flex align-items-center gap-3 mb-3">
                            <span class="feed-author-avatar">
                                @if(!empty($item['author_avatar']))
                                    <img src="{{ $item['author_avatar'] }}" alt="Avatar {{ $item['author_name'] }}">
                                @else
                                    <span>{{ $avatarFallback }}</span>
                                @endif
                            </span>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <strong>{{ $item['author_name'] }}</strong>
                                    <span class="badge rounded-pill text-bg-light">{{ $item['source_label'] }}</span>
                                </div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($item['published_at'])->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>

                        <h3 class="feed-title">{{ $item['title'] }}</h3>
                        <p class="feed-content mb-3">{{ $item['content'] }}</p>

                        @if(!empty($item['target_url']))
                            <a href="{{ $item['target_url'] }}" class="btn btn-sm btn-outline-primary mb-3" target="_blank" rel="noopener">
                                Ouvrir la source <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        @endif

                        <div class="feed-stats d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">
                                {{ $reactions['total'] }} reactions • {{ $commentCount }} commentaires
                            </small>
                        </div>

                        <div class="feed-actions d-flex gap-2 flex-wrap mb-3">
                            @foreach(['like' => '👍', 'love' => '❤️', 'care' => '🤝', 'wow' => '🎉'] as $reactionKey => $reactionLabel)
                                <form method="POST" action="{{ route('platform.feed.reactions.store') }}">
                                    @csrf
                                    <input type="hidden" name="source_type" value="{{ $item['source_type'] }}">
                                    <input type="hidden" name="source_id" value="{{ $item['source_id'] }}">
                                    <input type="hidden" name="reaction" value="{{ $reactionKey }}">
                                    <button type="submit" class="btn btn-sm {{ $myReaction === $reactionKey ? 'btn-primary' : 'btn-outline-secondary' }}">
                                        {{ $reactionLabel }} {{ $reactions[$reactionKey] ?? 0 }}
                                    </button>
                                </form>
                            @endforeach
                        </div>

                        <div class="feed-comments mb-3">
                            @foreach($comments as $comment)
                                <div class="feed-comment-row">
                                    <strong>{{ $comment->user?->name ?? 'Utilisateur' }}</strong>
                                    <span>{{ $comment->content }}</span>
                                    <small>{{ $comment->created_at?->format('d/m H:i') }}</small>
                                </div>
                            @endforeach
                        </div>

                        <form method="POST" action="{{ route('platform.feed.comments.store') }}" class="feed-comment-form">
                            @csrf
                            <input type="hidden" name="source_type" value="{{ $item['source_type'] }}">
                            <input type="hidden" name="source_id" value="{{ $item['source_id'] }}">
                            <div class="input-group">
                                <input type="text" name="content" class="form-control" maxlength="2000" placeholder="Ajouter un commentaire..." required>
                                <button class="btn btn-outline-primary" type="submit">Publier</button>
                            </div>
                        </form>
                    </div>
                </article>
            @empty
                <div class="card">
                    <div class="card-body text-center text-muted py-5">
                        Aucun contenu dans le fil pour le moment.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="col-12 col-xl-4">
            <div class="card sticky-xl-top" style="top: 1rem;">
                <div class="card-header">
                    <strong>Calendrier annee scolaire en cours</strong>
                </div>
                <div class="card-body">
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
        .feed-title {
            font-size: 1.15rem;
            margin-bottom: 0.5rem;
        }

        .feed-content {
            white-space: pre-line;
        }

        .feed-author-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e5062, #e0a63f);
            color: #fff;
            font-weight: 800;
            flex-shrink: 0;
        }

        .feed-author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .feed-comment-row {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 0.5rem;
            padding: 0.4rem 0;
            border-bottom: 1px dashed #e5e7eb;
            font-size: 0.92rem;
        }

        .feed-comment-row:last-child {
            border-bottom: none;
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
            background: #f8fafc;
        }

        .feed-period-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-top: 0.3rem;
            flex-shrink: 0;
        }
    </style>
@stop
