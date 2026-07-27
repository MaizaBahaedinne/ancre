<?php

namespace App\Listeners;

use App\Events\ParentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendParentNotifications
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

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
                \App\Models\Notification::create([
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
            }

            // Ajouter les autres canaux (email, SMS) ici
            if (in_array($receiver->notification_medium, ['email', 'all'])) {
                // TODO: Dispatcher email
            }

            if (in_array($receiver->notification_medium, ['sms', 'all'])) {
                // TODO: Dispatcher SMS
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
