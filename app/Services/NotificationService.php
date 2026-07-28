<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationLog;
use App\Models\NotificationWorkflow;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

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
     * Send a test notification for a workflow to one target user.
     */
    public static function sendTest(NotificationWorkflow $workflow, User $user, array $metadata = []): Notification
    {
        $receiver = (object) [
            'receiver_type' => 'user',
        ];

        $defaultMetadata = self::defaultTestMetadata($workflow->trigger, $user);

        $data = [
            'subject' => 'Test notification: '.$workflow->name,
            'description' => 'Notification de test generee depuis l\'interface workflow.',
            'metadata' => array_merge([
                'test_mode' => true,
                'tested_at' => now()->toDateTimeString(),
                'tested_by_id' => auth()->id(),
            ], $defaultMetadata, $metadata),
        ];

        return self::createNotification($workflow, $user, $receiver, $data);
    }

    /**
     * Create a notification record
     */
    private static function createNotification($workflow, $user, $receiver, $data): Notification
    {
        [$subject, $description] = self::buildContent($workflow, $data);

        $notification = Notification::create([
            'user_id' => $user->id,
            'trigger' => $workflow->trigger,
            'subject' => $subject,
            'description' => $description,
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
        return $notification;
    }

    /**
     * Resolve receiver users based on type
     */
    private static function resolveReceivers($receiver)
    {
        try {
            return match ($receiver->receiver_type) {
                'role' => User::role($receiver->receiver_value)->get(),
                'user' => User::where('id', $receiver->receiver_value)->get(),
                default => collect(),
            };
        } catch (RoleDoesNotExist $exception) {
            Log::warning('Notification receiver role does not exist', [
                'receiver_type' => $receiver->receiver_type ?? null,
                'receiver_value' => $receiver->receiver_value ?? null,
                'error' => $exception->getMessage(),
            ]);

            return collect();
        }
    }

    /**
     * Build subject and description from workflow templates, with fallback to payload defaults.
     *
     * Supported placeholders: {key} and {{key}} using payload values and metadata.
     *
     * @return array{0:string,1:string}
     */
    private static function buildContent($workflow, array $data): array
    {
        $config = is_array($workflow->config) ? $workflow->config : [];

        $context = [];
        foreach ($data as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $context[(string) $key] = (string) $value;
            }
        }

        $metadata = is_array($data['metadata'] ?? null) ? $data['metadata'] : [];
        foreach ($metadata as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $context[(string) $key] = (string) $value;
            }
        }

        $context['trigger'] = (string) $workflow->trigger;
        $context['workflow_name'] = (string) $workflow->name;

        $subjectTemplate = is_string($config['subject_template'] ?? null) ? trim($config['subject_template']) : '';
        $descriptionTemplate = is_string($config['description_template'] ?? null) ? trim($config['description_template']) : '';

        $subject = $subjectTemplate !== ''
            ? self::renderTemplate($subjectTemplate, $context)
            : (string) ($data['subject'] ?? $workflow->name);

        $description = $descriptionTemplate !== ''
            ? self::renderTemplate($descriptionTemplate, $context)
            : (string) ($data['description'] ?? '');

        return [$subject, $description];
    }

    private static function renderTemplate(string $template, array $context): string
    {
        $rendered = $template;

        foreach ($context as $key => $value) {
            $safeValue = (string) $value;
            $rendered = str_replace('{{'.$key.'}}', $safeValue, $rendered);
            $rendered = str_replace('{'.$key.'}', $safeValue, $rendered);
        }

        // Remove any unresolved placeholders from the final text.
        $rendered = preg_replace('/\{[a-zA-Z0-9_]+\}/', '', $rendered) ?? $rendered;

        // Normalize whitespace after placeholder cleanup.
        $rendered = preg_replace('/\s{2,}/', ' ', $rendered) ?? $rendered;

        return trim($rendered);
    }

    /**
     * @return array<string, mixed>
     */
    private static function defaultTestMetadata(string $trigger, User $targetUser): array
    {
        [$entity] = array_pad(explode('.', $trigger, 2), 2, null);
        $entity = (string) ($entity ?? 'item');

        $generic = [
            'entity' => $entity,
            'entity_id' => 999999,
            'entity_name' => Str::headline(str_replace('_', ' ', $entity)).' Test',
            $entity.'_id' => 999999,
            $entity.'_name' => Str::headline(str_replace('_', ' ', $entity)).' Test',
        ];

        return array_merge($generic, match ($trigger) {
            'parent.created' => [
                'parent_id' => 999999,
                'parent_nom' => 'Parent',
                'parent_prenom' => 'Test',
                'parent_full_name' => 'Parent Test',
                'parent_email' => 'parent.test@example.com',
                'parent_phone' => '+21600000000',
                'parent_user_name' => $targetUser->name,
                'parent_user_email' => $targetUser->email,
                'children_count' => 1,
                'created_by_id' => auth()->id(),
                'created_by_name' => auth()->user()?->name,
                'created_by_email' => auth()->user()?->email,
            ],
            'school.created' => [
                'school_id' => 999999,
                'school_name' => 'Ecole Test',
                'created_by' => auth()->id(),
                'created_by_name' => auth()->user()?->name,
                'created_by_email' => auth()->user()?->email,
            ],
            default => [
                'created_by_id' => auth()->id(),
                'created_by_name' => auth()->user()?->name,
                'created_by_email' => auth()->user()?->email,
            ],
        });
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
