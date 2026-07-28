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
        <div class="col-12 col-xl-8 feed-stream-column">
            <div class="feed-stream">
            @if($canPublish)
                <div class="feed-card feed-composer mb-4" data-composer>
                    <div class="feed-card-body">
                        <form action="{{ route('platform.feed.announcements.store') }}" method="POST" enctype="multipart/form-data" id="feed-composer-form">
                            @csrf
                            <input type="hidden" name="mode" value="text" id="feed-composer-mode">

                            <div class="feed-composer-top">
                                <span class="feed-avatar-lg feed-composer-avatar">
                                    @if($currentUserAvatar)
                                        <img src="{{ $currentUserAvatar }}" alt="Mon avatar">
                                    @else
                                        <span>{{ strtoupper(substr((string) auth()->user()->name, 0, 1)) }}</span>
                                    @endif
                                </span>

                                <div class="feed-composer-input-wrap">
                                    <input
                                        type="text"
                                        name="title"
                                        class="form-control feed-composer-input"
                                        maxlength="255"
                                        value="{{ old('title') }}"
                                        placeholder="Commencer un post"
                                    >
                                </div>
                            </div>

                            <div class="feed-composer-tabs" role="tablist" aria-label="Modes de publication">
                                <button type="button" class="feed-composer-tab is-active" data-mode-tab="text">
                                    <i class="fa-solid fa-pen"></i>
                                    <span>Text</span>
                                </button>
                                <button type="button" class="feed-composer-tab" data-mode-tab="photo">
                                    <i class="fa-regular fa-image"></i>
                                    <span>Text + image</span>
                                </button>
                                <a href="{{ route('admin.vitrine.blog-posts') }}" class="feed-composer-tab feed-composer-tab-link">
                                    <i class="fa-solid fa-newspaper"></i>
                                    <span>Article blog</span>
                                </a>
                            </div>

                            <div class="feed-composer-panel is-visible" data-mode-panel="text">
                                <textarea
                                    name="body"
                                    class="form-control feed-composer-textarea"
                                    rows="3"
                                    required
                                    maxlength="6000"
                                    placeholder="Quoi de neuf aujourd'hui ?"
                                >{{ old('body') }}</textarea>
                            </div>

                            <div class="feed-composer-panel" data-mode-panel="photo">
                                <textarea
                                    name="body"
                                    class="form-control feed-composer-textarea"
                                    rows="3"
                                    maxlength="6000"
                                    placeholder="Ajoutez un texte pour accompagner l'image..."
                                >{{ old('body') }}</textarea>

                                <label class="feed-photo-dropzone">
                                    <input type="file" name="image" accept="image/*">
                                    <span class="feed-photo-dropzone-icon"><i class="fa-regular fa-image"></i></span>
                                    <strong>Ajouter une photo</strong>
                                    <small>JPG, PNG ou WEBP jusqu'a 5 Mo</small>
                                </label>
                            </div>

                            <div class="feed-composer-actions">
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

                        @if(!empty($item['media_url']) && $item['media_type'] === 'image')
                            <div class="feed-media-preview">
                                <img src="{{ $item['media_url'] }}" alt="{{ $item['media_alt'] ?? $item['title'] }}">
                            </div>
                        @elseif($item['media_type'] === 'activity-card')
                            <div class="feed-activity-preview">
                                <div class="feed-activity-preview-icon">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>
                                <div>
                                    <strong>{{ $item['title'] }}</strong>
                                    <div class="small text-muted">{{ $item['meta_line'] ?? '' }}</div>
                                </div>
                            </div>
                        @endif

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

                        <div class="feed-actions-horizontal">
                            @foreach(['like' => 'J’aime', 'love' => 'J’adore', 'care' => 'Solidaire', 'wow' => 'Wouah'] as $reactionKey => $reactionLabel)
                                <form method="POST" action="{{ route('platform.feed.reactions.store') }}" class="feed-action-form">
                                    @csrf
                                    <input type="hidden" name="source_type" value="{{ $item['source_type'] }}">
                                    <input type="hidden" name="source_id" value="{{ $item['source_id'] }}">
                                    <input type="hidden" name="reaction" value="{{ $reactionKey }}">
                                    <button type="submit" class="feed-action-button {{ $myReaction === $reactionKey ? 'is-active' : '' }}">
                                        <span class="feed-action-emoji">@if($reactionKey === 'like')👍@elseif($reactionKey === 'love')❤️@elseif($reactionKey === 'care')🤝@else🎉@endif</span>
                                        <span>{{ $reactionLabel }}</span>
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
                                                        $calendarReferenceDate = now();
                                                        $academicStart = $activeAcademicYear->start_date?->copy();
                                                        $academicEnd = $activeAcademicYear->end_date?->copy();

                                                        if ($academicStart && $academicEnd && (! $calendarReferenceDate->betweenIncluded($academicStart, $academicEnd))) {
                                                            $calendarReferenceDate = $academicStart->copy();
                                                        }

                                                        $calendarMonthStart = $calendarReferenceDate->copy()->startOfMonth();
                                                        $calendarMonthEnd = $calendarReferenceDate->copy()->endOfMonth();
                                                        $calendarGridStart = $calendarMonthStart->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                                                        $calendarGridEnd = $calendarMonthEnd->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                                                        $calendarDay = $calendarGridStart->copy();
                                                        $calendarToday = now()->startOfDay();
                                                        $dateTypePriority = [
                                                            \App\Models\AcademicCalendarPeriod::TYPE_PUBLIC_HOLIDAY => 5,
                                                            \App\Models\AcademicCalendarPeriod::TYPE_SCHOOL_VACATION => 4,
                                                            \App\Models\AcademicCalendarPeriod::TYPE_SYNTHESIS_EXAM => 3,
                                                            \App\Models\AcademicCalendarPeriod::TYPE_PRACTICAL_EXAM => 2,
                                                            \App\Models\AcademicCalendarPeriod::TYPE_THEORETICAL_EXAM => 1,
                                                        ];
                                                        $periodsForDate = function (\Carbon\Carbon $date) use ($activeAcademicYear) {
                                                            return $activeAcademicYear->periods->filter(function ($period) use ($date) {
                                                                return $period->start_date && $period->end_date
                                                                    && $date->betweenIncluded($period->start_date->copy()->startOfDay(), $period->end_date->copy()->endOfDay());
                                                            })->values();
                                                        };
                        </form>
                    </div>
                                                    <div class="feed-calendar-month-shell">
                                                        <div class="feed-calendar-month-head">
                                                            <div>
                                                                <div class="feed-calendar-year-label">{{ $activeAcademicYear->label }}</div>
                                                                <h3 class="feed-calendar-month-title">{{ ucfirst($calendarReferenceDate->translatedFormat('F Y')) }}</h3>
                                                            </div>
                                                            <div class="feed-calendar-month-meta">
                                                                {{ $activeAcademicYear->start_date?->format('d/m/Y') }} - {{ $activeAcademicYear->end_date?->format('d/m/Y') }}
                                                            </div>
                                                        </div>

                                                        <div class="feed-calendar-legend">
                                                            @foreach($periodColorMap as $typeValue => $color)
                                                                <div class="feed-calendar-legend-item">
                                                                    <span class="feed-calendar-legend-dot" style="background-color: {{ $color }};"></span>
                                                                    <span>{{ \App\Models\AcademicCalendarPeriod::TYPE_OPTIONS[$typeValue] ?? $typeValue }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <div class="feed-calendar-grid">
                                                            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $weekday)
                                                                <div class="feed-calendar-weekday">{{ $weekday }}</div>
                                                            @endforeach

                                                            @while($calendarDay->lte($calendarGridEnd))
                                                                @php
                                                                    $dayPeriods = $periodsForDate($calendarDay);
                                                                    $dayType = $dayPeriods->sortByDesc(fn ($period) => $dateTypePriority[$period->type] ?? 0)->first()?->type;
                                                                    $dayColor = $dayType ? ($periodColorMap[$dayType] ?? '#6b7280') : null;
                                                                    $isToday = $calendarDay->isSameDay($calendarToday);
                                                                    $isInMonth = $calendarDay->month === $calendarReferenceDate->month;
                                                                @endphp
                                                                <div class="feed-calendar-day {{ $isInMonth ? '' : 'is-outside' }} {{ $isToday ? 'is-today' : '' }} {{ $dayColor ? 'has-period' : '' }}" @if($dayColor) style="--day-accent: {{ $dayColor }};" @endif>
                                                                    <span class="feed-calendar-day-number">{{ $calendarDay->day }}</span>
                                                                    @if($dayPeriods->isNotEmpty())
                                                                        <span class="feed-calendar-day-dot"></span>
                                                                    @endif
                                                                </div>
                                                                @php $calendarDay->addDay(); @endphp
                                                            @endwhile
                                                        </div>

                                                        <div class="feed-calendar-day-summary">
                                                            <div class="feed-calendar-day-summary-title">Périodes de {{ ucfirst($calendarReferenceDate->translatedFormat('F')) }}</div>
                                                            @forelse($activeAcademicYear->periods->filter(function ($period) use ($calendarMonthStart, $calendarMonthEnd) {
                                                                return $period->start_date && $period->end_date
                                                                    && $period->end_date->greaterThanOrEqualTo($calendarMonthStart)
                                                                    && $period->start_date->lessThanOrEqualTo($calendarMonthEnd);
                                                            }) as $period)
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
                                                                <p class="text-muted mb-0">Aucune periode definie pour ce mois.</p>
                                                            @endforelse
                                                        </div>
                                                    </div>
            gap: 1rem;
            max-height: calc(100vh - 155px);
            overflow-y: auto;
            padding-right: 0.35rem;
            scrollbar-gutter: stable;
        }

        .feed-stream::-webkit-scrollbar {
            width: 10px;
        }

        .feed-stream::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.45);
            border-radius: 999px;
        }

        .feed-stream::-webkit-scrollbar-track {
            background: transparent;
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
            margin-bottom: 0 !important;
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

        .feed-composer-top {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.85rem;
        }

        .feed-composer-avatar {
            width: 48px;
            height: 48px;
        }

        .feed-composer-input-wrap {
            flex: 1;
        }

        .feed-composer-input {
            width: 100%;
            border-radius: 999px;
            border: 1px solid #d8dee8;
            background: #f4f6fa;
            padding: 0.85rem 1rem;
            font-size: 1rem;
        }

        .feed-composer-input:focus {
            border-color: #2d88ff;
            box-shadow: 0 0 0 0.15rem rgba(45, 136, 255, 0.18);
            background: #fff;
        }

        .feed-composer-tabs {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.6rem;
            margin-bottom: 0.9rem;
        }

        .feed-composer-tab {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.7rem 0.85rem;
            border-radius: 14px;
            border: 1px solid #d6dde8;
            background: #fff;
            color: #344054;
            font-weight: 700;
            text-decoration: none;
        }

        .feed-composer-tab:hover {
            background: #f6f8fb;
            color: #111827;
        }

        .feed-composer-tab.is-active {
            border-color: #2d88ff;
            background: #eaf3ff;
            color: #0f4fa8;
        }

        .feed-composer-tab-link {
            cursor: pointer;
        }

        .feed-composer-panel {
            display: none;
            gap: 0.75rem;
        }

        .feed-composer-panel.is-visible {
            display: grid;
        }

        .feed-composer-textarea {
            border-radius: 18px;
            border: 1px solid #d8dee8;
            background: #f4f6fa;
            min-height: 120px;
            resize: vertical;
        }

        .feed-composer-textarea:focus {
            border-color: #2d88ff;
            box-shadow: 0 0 0 0.15rem rgba(45, 136, 255, 0.18);
            background: #fff;
        }

        .feed-photo-dropzone {
            display: grid;
            place-items: center;
            gap: 0.35rem;
            padding: 1rem;
            border: 1.5px dashed #b8c4d5;
            border-radius: 18px;
            background: #fbfcfe;
            color: #475467;
            text-align: center;
            cursor: pointer;
        }

        .feed-photo-dropzone input {
            display: none;
        }

        .feed-photo-dropzone-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #eaf3ff;
            color: #2d88ff;
            font-size: 1.1rem;
        }

        .feed-composer-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 0.9rem;
        }

        .feed-btn-post {
            border-radius: 999px;
            font-weight: 600;
            background: #1877f2;
            border-color: #1877f2;
            min-width: 140px;
        }

        .feed-media-preview {
            margin: 0.8rem 0 0.2rem;
            border-radius: 18px;
            overflow: hidden;
            background: #f3f5f8;
            border: 1px solid #e5eaf1;
        }

        .feed-media-preview img {
            width: 100%;
            height: auto;
            display: block;
            max-height: 420px;
            object-fit: cover;
        }

        .feed-activity-preview {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-top: 0.8rem;
            padding: 0.85rem 0.95rem;
            border-radius: 18px;
            background: linear-gradient(135deg, #eef5ff, #f7fbff);
            border: 1px solid #dce8ff;
        }

        .feed-activity-preview-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #1877f2;
            color: #fff;
            font-size: 1.05rem;
            flex-shrink: 0;
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


        .feed-actions-horizontal {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.5rem;
            margin-top: 0.8rem;
            margin-bottom: 0.8rem;
            padding: 0.75rem 0;
            border-top: 1px solid #edf0f4;
            border-bottom: 1px solid #edf0f4;
        }

        .feed-action-form {
            min-width: 0;
        }

        .feed-action-button {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.65rem 0.45rem;
            border-radius: 12px;
            border: 1px solid transparent;
            background: #f5f7fb;
            color: #344054;
            font-weight: 700;
        }

        .feed-action-button:hover {
            background: #eef3fa;
        }

        .feed-action-button.is-active {
            background: #e8f1ff;
            border-color: #b7d2ff;
            color: #0f4fa8;
        }

        .feed-action-emoji {
            font-size: 1rem;
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

        .feed-inline-comment {
            display: flex;
            align-items: center;
            gap: 0.55rem;
        }

        .feed-inline-comment-box {
            flex: 1;
        }

        .feed-comment-input {
            border-radius: 999px;
            border: 1px solid #d8dee8;
            background: #f4f6fa;
            padding: 0.72rem 0.95rem;
        }

        .feed-comment-input:focus {
            border-color: #2d88ff;
            box-shadow: 0 0 0 0.15rem rgba(45, 136, 255, 0.18);
            background: #fff;
        }

        .feed-comment-submit {
            border-radius: 999px;
            min-width: 95px;
            font-weight: 700;
        }

        .feed-calendar-card {
            position: sticky;
            top: 1rem;
        }

        .feed-calendar-month-shell {
            display: grid;
            gap: 0.9rem;
        }

        .feed-calendar-month-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.2rem 0 0.4rem;
        }

        .feed-calendar-year-label {
            font-size: 0.82rem;
            color: #667085;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .feed-calendar-month-title {
            margin: 0.15rem 0 0;
            font-size: 1.35rem;
            font-weight: 800;
            color: #1c1e21;
        }

        .feed-calendar-month-meta {
            color: #667085;
            font-size: 0.85rem;
            text-align: right;
            line-height: 1.4;
        }

        .feed-calendar-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem 0.7rem;
            padding: 0.7rem;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #eef2f7;
        }

        .feed-calendar-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.82rem;
            color: #344054;
            font-weight: 600;
        }

        .feed-calendar-legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .feed-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.35rem;
            padding: 0.6rem 0.2rem 0;
        }

        .feed-calendar-weekday {
            text-align: center;
            font-size: 0.78rem;
            font-weight: 700;
            color: #98a2b3;
            padding-bottom: 0.25rem;
        }

        .feed-calendar-day {
            min-height: 46px;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid #eef2f7;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 0.45rem 0.2rem;
            position: relative;
            color: #1c1e21;
            font-weight: 700;
        }

        .feed-calendar-day.is-outside {
            opacity: 0.35;
            background: #f8fafc;
        }

        .feed-calendar-day.is-today {
            border-color: #2d88ff;
            box-shadow: 0 0 0 1px rgba(45, 136, 255, 0.15) inset;
        }

        .feed-calendar-day.has-period {
            border-color: var(--day-accent, #e2e8f0);
            background: color-mix(in srgb, var(--day-accent) 10%, #ffffff);
        }

        .feed-calendar-day-number {
            line-height: 1;
        }

        .feed-calendar-day-dot {
            position: absolute;
            bottom: 0.45rem;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--day-accent, #667085);
        }

        .feed-calendar-day-summary {
            display: grid;
            gap: 0.65rem;
            padding-top: 0.25rem;
        }

        .feed-calendar-day-summary-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #1c1e21;
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
            .feed-stream {
                max-height: none;
                overflow: visible;
                padding-right: 0;
            }

            .feed-card-body {
                padding: 0.9rem;
            }

            .feed-composer-top {
                align-items: flex-start;
            }

            .feed-composer-tabs,
            .feed-actions-horizontal {
                grid-template-columns: 1fr 1fr;
            }

            .feed-title {
                font-size: 1.05rem;
            }

            .feed-inline-comment {
                align-items: flex-start;
                flex-wrap: wrap;
            }

            .feed-comment-submit {
                width: 100%;
            }
        }
    </style>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const composer = document.querySelector('[data-composer]');
            if (!composer) {
                return;
            }

            const modeInput = composer.querySelector('#feed-composer-mode');
            const tabs = Array.from(composer.querySelectorAll('[data-mode-tab]'));
            const panels = Array.from(composer.querySelectorAll('[data-mode-panel]'));

            function setMode(mode) {
                modeInput.value = mode;

                tabs.forEach((tab) => {
                    tab.classList.toggle('is-active', tab.dataset.modeTab === mode);
                });

                panels.forEach((panel) => {
                    panel.classList.toggle('is-visible', panel.dataset.modePanel === mode);
                    panel.querySelectorAll('textarea, input[type="file"]').forEach((field) => {
                        if (panel.dataset.modePanel === mode) {
                            field.removeAttribute('disabled');
                        } else {
                            field.setAttribute('disabled', 'disabled');
                        }
                    });
                });
            }

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => setMode(tab.dataset.modeTab));
            });

            setMode(modeInput.value || 'text');
        });
    </script>
@stop
