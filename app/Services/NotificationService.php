<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationLog;
use App\Models\NotificationWorkflow;
use App\Models\User;

class NotificationService
{
    /**
     * Dispatch a notification to users
     */
    public static function dispatch($trigger, $data = [])
    {
        $workflow = NotificationWorkflow::getByTrigger($trigger);

        if (!$workflow) {
            return;
        }

        $receivers = $workflow->receivers()->where('is_enabled', true)->get();

        foreach ($receivers as $receiver) {
            self::dispatchToReceiver($workflow, $receiver, $data);
        }
    }

    /**
     * Dispatch notification to a specific receiver
     */
    private static function dispatchToReceiver($workflow, $receiver, $data)
    {
        $users = self::resolveReceivers($receiver);

        foreach ($users as $user) {
            self::createNotification($workflow, $user, $receiver, $data);
        }
    }

    /**
     * Create a notification record
     */
    private static function createNotification($workflow, $user, $receiver, $data)
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'trigger' => $workflow->trigger,
            'subject' => $data['subject'] ?? $workflow->name,
            'description' => $data['description'] ?? '',
            'notification_type' => 'system',
            'receiver_type' => $receiver->receiver_type,
            'metadata' => $data['metadata'] ?? [],
        ]);

        // Log notification dispatch
        NotificationLog::create([
            'notification_id' => $notification->id,
            'channel' => 'system',
            'recipient' => $user->email,
            'status' => 'sent',
        ]);

        // TODO: Dispatch email and SMS
    }

    /**
     * Resolve receiver users based on type
     */
    private static function resolveReceivers($receiver)
    {
        return match ($receiver->receiver_type) {
            'role' => User::role($receiver->receiver_value)->get(),
            'user' => User::where('id', $receiver->receiver_value)->get(),
            default => collect(),
        };
    }

    /**
     * Get unread notifications for a user
     */
    public static function getUnread(User $user)
    {
        return $user->notifications()
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mark notification as read
     */
    public static function markAsRead(Notification $notification)
    {
        $notification->markAsRead();
    }
}
