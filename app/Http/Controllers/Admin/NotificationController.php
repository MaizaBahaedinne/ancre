<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get unread notifications count for current user
     */
    public function count()
    {
        $count = auth()->user()->unreadNotifications()->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get all unread notifications for current user
     */
    public function unread()
    {
        $notifications = auth()->user()
            ->unreadNotifications()
            ->where('notification_type', 'system')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json($this->enrichNotifications($notifications));
    }

    /**
     * Get notifications archive (already read notifications only), paginated by limit.
     */
    public function archive(Request $request)
    {
        $limit = (int) $request->integer('limit', 50);
        $limit = max(10, min($limit, 100));

        $notifications = auth()->user()
            ->notifications()
            ->where('notification_type', 'system')
            ->whereNotNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($this->enrichNotifications($notifications));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        auth()->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * @param Collection<int, Notification> $notifications
     * @return Collection<int, array<string, mixed>>
     */
    private function enrichNotifications(Collection $notifications): Collection
    {
        $actorIds = $notifications
            ->map(function (Notification $notification) {
                $metadata = is_array($notification->metadata) ? $notification->metadata : [];

                return $metadata['created_by_id']
                    ?? $metadata['created_by']
                    ?? $metadata['tested_by_id']
                    ?? null;
            })
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $actors = User::query()
            ->with(['personnel', 'parentProfile'])
            ->whereIn('id', $actorIds)
            ->get()
            ->keyBy('id');

        return $notifications->map(function (Notification $notification) use ($actors): array {
            $metadata = is_array($notification->metadata) ? $notification->metadata : [];

            $actorId = $metadata['created_by_id']
                ?? $metadata['created_by']
                ?? $metadata['tested_by_id']
                ?? null;

            $actor = is_numeric($actorId) ? $actors->get((int) $actorId) : null;
            $actorName = $metadata['created_by_name'] ?? $actor?->name;
            $actorAvatar = $actor?->avatarUrl();

            $item = $notification->toArray();
            $item['actor_name'] = $actorName;
            $item['actor_avatar_url'] = $actorAvatar;
            $item['action_url'] = $this->resolveActionUrl($notification);

            return $item;
        });
    }

    private function resolveActionUrl(Notification $notification): ?string
    {
        $metadata = is_array($notification->metadata) ? $notification->metadata : [];

        if (!empty($metadata['action_url']) && is_string($metadata['action_url'])) {
            return $metadata['action_url'];
        }

        return match ($notification->trigger) {
            'parent.created' => !empty($metadata['parent_id'])
                ? route('parents.show', ['parent' => $metadata['parent_id']])
                : null,
            'school.created' => !empty($metadata['school_id'])
                ? route('schools.show', ['school' => $metadata['school_id']])
                : null,
            default => null,
        };
    }
}
