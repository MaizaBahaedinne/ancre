<?php

namespace App\Listeners;

use App\Events\ParentCreated;
use App\Mail\NewParentNotification;
use App\Models\Notification;
use App\Models\NotificationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendParentNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ParentCreated $event): void
    {
        // Récupérer le workflow pour ce trigger
        $workflow = \App\Models\NotificationWorkflow::getByTrigger('parent.created');
        
        if (!$workflow) {
            return;
        }

        // Récupérer les récepteurs du workflow
        $receivers = $workflow->receivers()->where('is_enabled', true)->get();

        foreach ($receivers as $receiver) {
            $this->dispatchNotification($workflow, $receiver, $event->parent);
        }
    }

    private function dispatchNotification($workflow, $receiver, $parent)
    {
        // Déterminer les utilisateurs qui recevront la notification
        $users = $this->resolveReceivers($receiver);

        foreach ($users as $user) {
            // Créer la notification système
            if (in_array($receiver->notification_medium, ['system', 'all'])) {
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'trigger' => 'parent.created',
                    'subject' => "Nouveau parent inscrit: {$parent->user->name}",
                    'description' => "Un nouveau parent {$parent->user->email} a été inscrit au système.",
                    'notification_type' => 'system',
                    'receiver_type' => $receiver->receiver_type,
                    'metadata' => [
                        'parent_id' => $parent->id,
                        'user_name' => $parent->user->name,
                        'email' => $parent->user->email,
                    ],
                ]);

                // Log notification system
                NotificationLog::create([
                    'notification_id' => $notification->id,
                    'channel' => 'system',
                    'recipient' => $user->email,
                    'status' => 'sent',
                ]);
            }

            // Envoyer email
            if (in_array($receiver->notification_medium, ['email', 'all'])) {
                try {
                    Mail::to($user->email)->queue(new NewParentNotification($parent));
                    
                    // Log email
                    Notification::create([
                        'user_id' => $user->id,
                        'trigger' => 'parent.created',
                        'subject' => "Nouveau parent inscrit: {$parent->user->name}",
                        'description' => "Un email de notification a été envoyé.",
                        'notification_type' => 'email',
                        'receiver_type' => $receiver->receiver_type,
                        'metadata' => [
                            'parent_id' => $parent->id,
                        ],
                    ]);
                } catch (\Exception $e) {
                    // Log erreur
                    NotificationLog::create([
                        'notification_id' => 0,
                        'channel' => 'email',
                        'recipient' => $user->email,
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                }
            }

            // SMS (à implémenter)
            if (in_array($receiver->notification_medium, ['sms', 'all'])) {
                // TODO: Implémenter l'envoi SMS
            }
        }
    }

    private function resolveReceivers($receiver)
    {
        return match ($receiver->receiver_type) {
            'role' => \App\Models\User::role($receiver->receiver_value)->get(),
            'user' => \App\Models\User::where('id', $receiver->receiver_value)->get(),
            default => collect(),
        };
    }
}
