<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendarPeriod;
use App\Models\AcademicYear;
use App\Models\Activite;
use App\Models\FeedAnnouncement;
use App\Models\FeedComment;
use App\Models\FeedReaction;
use App\Models\VitrineBlogPost;
use App\Support\AvatarUrl;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class PlatformFeedController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $canPublish = $this->canPublish($request);

        $items = $this->buildFeedItems();
        $metrics = $this->buildEngagementMetrics($items, $user?->id);

        $activeYear = AcademicYear::query()
            ->active()
            ->with(['periods' => fn ($query) => $query->orderBy('start_date')])
            ->first();

        return view('feed.index', [
            'canPublish' => $canPublish,
            'feedItems' => $items,
            'reactionSummary' => $metrics['reactionSummary'],
            'commentSummary' => $metrics['commentSummary'],
            'latestComments' => $metrics['latestComments'],
            'currentUserReactions' => $metrics['currentUserReactions'],
            'currentUserAvatar' => $this->resolveUserAvatar($user),
            'activeAcademicYear' => $activeYear,
            'periodColorMap' => $this->periodColorMap(),
        ]);
    }

    public function storeAnnouncement(Request $request): RedirectResponse
    {
        if (! $this->canPublish($request)) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'body' => 'required|string|max:6000',
            'mode' => 'required|in:text,photo',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $title = trim((string) ($validated['title'] ?? ''));
        if ($title === '') {
            $title = str($validated['body'])->squish()->limit(72)->toString();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('feed/announcements', 'public');
        }

        FeedAnnouncement::query()->create([
            'user_id' => $request->user()->id,
            'title' => $title,
            'body' => $validated['body'],
            'image_path' => $imagePath,
            'is_published' => true,
            'published_at' => now(),
        ]);

        return redirect()->route('platform.feed')->with('success', 'Publication ajoutee au fil avec succes.');
    }

    public function react(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_type' => 'required|in:announcement,blog,activity',
            'source_id' => 'required|integer|min:1',
            'reaction' => 'required|in:like,love,care,wow',
        ]);

        if (! $this->sourceExists($validated['source_type'], (int) $validated['source_id'])) {
            return redirect()->route('platform.feed')->withErrors(['reaction' => 'Source de publication introuvable.']);
        }

        FeedReaction::query()->updateOrCreate(
            [
                'source_type' => $validated['source_type'],
                'source_id' => (int) $validated['source_id'],
                'user_id' => $request->user()->id,
            ],
            [
                'reaction' => $validated['reaction'],
            ]
        );

        return redirect()->route('platform.feed')->with('success', 'Reaction enregistree.');
    }

    public function comment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_type' => 'required|in:announcement,blog,activity',
            'source_id' => 'required|integer|min:1',
            'content' => 'required|string|max:2000',
        ]);

        if (! $this->sourceExists($validated['source_type'], (int) $validated['source_id'])) {
            return redirect()->route('platform.feed')->withErrors(['content' => 'Source de publication introuvable.']);
        }

        FeedComment::query()->create([
            'source_type' => $validated['source_type'],
            'source_id' => (int) $validated['source_id'],
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        return redirect()->route('platform.feed')->with('success', 'Commentaire ajoute.');
    }

    private function buildFeedItems(): Collection
    {
        $announcements = FeedAnnouncement::query()
            ->with(['author.personnel', 'author.parentProfile'])
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->limit(80)
            ->get()
            ->map(function (FeedAnnouncement $announcement): array {
                $publishedAt = $announcement->published_at ?? $announcement->created_at;

                return [
                    'source_type' => 'announcement',
                    'source_id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->body,
                    'published_at' => $publishedAt,
                    'author_name' => $announcement->author?->name ?? 'Systeme',
                    'author_avatar' => $this->resolveUserAvatar($announcement->author),
                    'source_label' => 'Annonce',
                    'target_url' => null,
                    'media_type' => $announcement->image_path ? 'image' : null,
                    'media_url' => $announcement->image_path ? AvatarUrl::fromPath($announcement->image_path) : null,
                    'media_alt' => $announcement->title,
                    'meta_line' => null,
                ];
            });

        $blogs = VitrineBlogPost::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->limit(80)
            ->get()
            ->map(function (VitrineBlogPost $blog): array {
                $publishedAt = $blog->published_at ?? $blog->created_at;

                return [
                    'source_type' => 'blog',
                    'source_id' => $blog->id,
                    'title' => $blog->title,
                    'content' => $blog->excerpt ?: strip_tags((string) $blog->content),
                    'published_at' => $publishedAt,
                    'author_name' => 'Equipe communication',
                    'author_avatar' => null,
                    'source_label' => 'Blog',
                    'target_url' => route('vitrine.blog.show', ['slug' => $blog->slug]),
                    'media_type' => $blog->cover_url ? 'image' : null,
                    'media_url' => $blog->cover_url ? AvatarUrl::fromPath($blog->cover_url) : null,
                    'media_alt' => $blog->title,
                    'meta_line' => 'Article de blog',
                ];
            });

        $activities = Activite::query()
            ->with(['responsablePersonnel.user.personnel', 'responsablePersonnel.user.parentProfile'])
            ->orderByDesc('date')
            ->orderByDesc('heure_debut')
            ->limit(80)
            ->get()
            ->map(function (Activite $activity): array {
                $activityDate = $activity->date?->copy() ?? now();
                $startTime = $activity->heure_debut ?: $activity->heure;
                if ($startTime) {
                    $activityDate->setTimeFromTimeString(strlen($startTime) === 5 ? $startTime.':00' : $startTime);
                }

                $responsableName = trim((string) (($activity->responsablePersonnel?->prenom ?? '').' '.($activity->responsablePersonnel?->nom ?? '')));
                $responsableUser = $activity->responsablePersonnel?->user;
                $avatar = $activity->responsablePersonnel?->photo
                    ? asset('storage/'.$activity->responsablePersonnel->photo)
                    : $this->resolveUserAvatar($responsableUser);
                $activityTime = $startTime ?: 'Horaire non precise';
                $metaLine = trim(($activity->date?->format('d/m/Y') ?? '').' · '.$activityTime);

                return [
                    'source_type' => 'activity',
                    'source_id' => $activity->id,
                    'title' => $activity->titre,
                    'content' => $activity->description ?: 'Activite planifiee.',
                    'published_at' => $activityDate,
                    'author_name' => $responsableName !== '' ? $responsableName : 'Equipe pedagogique',
                    'author_avatar' => $avatar,
                    'source_label' => 'Activite',
                    'target_url' => route('activites.show', ['activite' => $activity->id]),
                    'media_type' => 'activity-card',
                    'media_url' => null,
                    'media_alt' => $activity->titre,
                    'meta_line' => $metaLine,
                ];
            });

        return $announcements
            ->concat($blogs)
            ->concat($activities)
            ->sortByDesc(fn (array $item) => Carbon::parse($item['published_at']))
            ->take(120)
            ->values();
    }

    /**
     * @param Collection<int, array<string, mixed>> $items
     * @return array<string, mixed>
     */
    private function buildEngagementMetrics(Collection $items, ?int $userId): array
    {
        $sourceTypes = $items->pluck('source_type')->unique()->values();
        $sourceIds = $items->pluck('source_id')->unique()->values();

        $reactions = FeedReaction::query()
            ->when($sourceTypes->isNotEmpty(), fn ($query) => $query->whereIn('source_type', $sourceTypes))
            ->when($sourceIds->isNotEmpty(), fn ($query) => $query->whereIn('source_id', $sourceIds))
            ->get();

        $comments = FeedComment::query()
            ->with(['user.personnel', 'user.parentProfile'])
            ->when($sourceTypes->isNotEmpty(), fn ($query) => $query->whereIn('source_type', $sourceTypes))
            ->when($sourceIds->isNotEmpty(), fn ($query) => $query->whereIn('source_id', $sourceIds))
            ->orderByDesc('created_at')
            ->get();

        $reactionSummary = [];
        $currentUserReactions = [];

        foreach ($reactions as $reaction) {
            $key = $reaction->source_type.':'.$reaction->source_id;
            $reactionSummary[$key] = $reactionSummary[$key] ?? [
                'like' => 0,
                'love' => 0,
                'care' => 0,
                'wow' => 0,
                'total' => 0,
            ];

            if (!array_key_exists($reaction->reaction, $reactionSummary[$key])) {
                $reactionSummary[$key][$reaction->reaction] = 0;
            }

            $reactionSummary[$key][$reaction->reaction]++;
            $reactionSummary[$key]['total']++;

            if ($userId && (int) $reaction->user_id === $userId) {
                $currentUserReactions[$key] = $reaction->reaction;
            }
        }

        $commentSummary = [];
        $latestComments = [];

        foreach ($comments as $comment) {
            $key = $comment->source_type.':'.$comment->source_id;
            $commentSummary[$key] = ($commentSummary[$key] ?? 0) + 1;
            $latestComments[$key] = $latestComments[$key] ?? [];

            if (count($latestComments[$key]) < 3) {
                $comment->setAttribute('author_avatar', $this->resolveUserAvatar($comment->user));
                $latestComments[$key][] = $comment;
            }
        }

        return [
            'reactionSummary' => $reactionSummary,
            'commentSummary' => $commentSummary,
            'latestComments' => $latestComments,
            'currentUserReactions' => $currentUserReactions,
        ];
    }

    private function canPublish(Request $request): bool
    {
        $user = $request->user();

        if (!$user) {
            return false;
        }

        return $user->hasAnyRole(['Administrateur', 'Responsable']);
    }

    private function sourceExists(string $sourceType, int $sourceId): bool
    {
        return match ($sourceType) {
            'announcement' => FeedAnnouncement::query()->whereKey($sourceId)->exists(),
            'blog' => VitrineBlogPost::query()->whereKey($sourceId)->exists(),
            'activity' => Activite::query()->whereKey($sourceId)->exists(),
            default => false,
        };
    }

    /**
     * @return array<string, string>
     */
    private function periodColorMap(): array
    {
        return [
            AcademicCalendarPeriod::TYPE_THEORETICAL_EXAM => '#f59e0b',
            AcademicCalendarPeriod::TYPE_PRACTICAL_EXAM => '#2563eb',
            AcademicCalendarPeriod::TYPE_SYNTHESIS_EXAM => '#7c3aed',
            AcademicCalendarPeriod::TYPE_SCHOOL_VACATION => '#059669',
            AcademicCalendarPeriod::TYPE_PUBLIC_HOLIDAY => '#dc2626',
        ];
    }

    private function resolveUserAvatar($user): ?string
    {
        if (! $user) {
            return null;
        }

        return $user->avatarUrl();
    }
}
