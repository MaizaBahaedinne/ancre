<?php

namespace App\Listeners;

use App\Events\ParentCreated;
use App\Mail\NewParentNotification;
use App\Models\Notification;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class SendParentNotifications
{

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
        $payload = $this->buildPayload($workflow, $parent);

        foreach ($users as $user) {
            [$subject, $description] = $this->buildContent($workflow, $payload);

            // Créer la notification système
            if (in_array($receiver->notification_medium, ['system', 'all'])) {
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'trigger' => 'parent.created',
                    'subject' => $subject,
                    'description' => $description,
                    'notification_type' => 'system',
                    'receiver_type' => $receiver->receiver_type,
                    'metadata' => $payload['metadata'],
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
                        'subject' => $subject,
                        'description' => "Un email de notification a été envoyé.",
                        'notification_type' => 'email',
                        'receiver_type' => $receiver->receiver_type,
                        'metadata' => $payload['metadata'],
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
        try {
            return match ($receiver->receiver_type) {
                'role' => \App\Models\User::role($receiver->receiver_value)->get(),
                'user' => \App\Models\User::where('id', $receiver->receiver_value)->get(),
                default => collect(),
            };
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            // Role doesn't exist, return empty collection
            \Illuminate\Support\Facades\Log::warning("Role '{$receiver->receiver_value}' does not exist", ['receiver' => $receiver]);
            return collect();
        }
    }

    private function buildPayload($workflow, $parent): array
    {
        $actor = Auth::user();
        $parentUser = $parent->user;

        return [
            'subject' => "Nouveau parent inscrit: ".($parentUser->name ?? ($parent->prenom.' '.$parent->nom)),
            'description' => "Un nouveau parent ".($parent->email ?? ($parentUser->email ?? '')) ." a ete inscrit au systeme.",
            'metadata' => [
                'parent_id' => $parent->id,
                'parent_nom' => $parent->nom,
                'parent_prenom' => $parent->prenom,
                'parent_full_name' => trim(($parent->prenom ?? '').' '.($parent->nom ?? '')),
                'parent_email' => $parent->email,
                'parent_phone' => $parent->telephone,
                'parent_user_name' => $parentUser->name ?? null,
                'parent_user_email' => $parentUser->email ?? null,
                'children_count' => $parent->enfants()->count(),
                'created_by_id' => $actor?->id,
                'created_by_name' => $actor?->name,
                'created_by_email' => $actor?->email,
                'action_url' => route('parents.show', $parent),
                // Backward compatibility with previous templates/keys.
                'user_name' => $parentUser->name ?? null,
                'email' => $parentUser->email ?? $parent->email,
            ],
        ];
    }

    /**
     * @return array{0:string,1:string}
     */
    private function buildContent($workflow, array $data): array
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
            ? $this->renderTemplate($subjectTemplate, $context)
            : (string) ($data['subject'] ?? $workflow->name);

        $description = $descriptionTemplate !== ''
            ? $this->renderTemplate($descriptionTemplate, $context)
            : (string) ($data['description'] ?? '');

        return [$subject, $description];
    }

    private function renderTemplate(string $template, array $context): string
    {
        $rendered = $template;

        foreach ($context as $key => $value) {
            $safeValue = (string) $value;
            $rendered = str_replace('{{'.$key.'}}', $safeValue, $rendered);
            $rendered = str_replace('{'.$key.'}', $safeValue, $rendered);
        }

        $rendered = preg_replace('/\{[a-zA-Z0-9_]+\}/', '', $rendered) ?? $rendered;
        $rendered = preg_replace('/\s{2,}/', ' ', $rendered) ?? $rendered;

        return trim($rendered);
    }
}
