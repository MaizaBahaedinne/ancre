<?php

namespace Database\Seeders;

use App\Models\NotificationWorkflow;
use App\Models\NotificationReceiver;
use Illuminate\Database\Seeder;

class NotificationWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $definitions = [
            [
                'workflow' => [
                    'trigger' => 'parent.created',
                    'name' => 'Nouveau parent inscrit',
                    'description' => 'Notifier admin et responsable lors de l\'inscription d\'un nouveau parent',
                    'is_enabled' => true,
                    'module' => 'family',
                ],
                'receivers' => [
                    ['receiver_type' => 'role', 'receiver_value' => 'Administrateur', 'notification_medium' => 'all', 'is_enabled' => true],
                    ['receiver_type' => 'role', 'receiver_value' => 'Responsable', 'notification_medium' => 'email', 'is_enabled' => true],
                ],
            ],
            [
                'workflow' => [
                    'trigger' => 'child.registered',
                    'name' => 'Enfant inscrit',
                    'description' => 'Notifier les parents lors de l\'inscription d\'un enfant',
                    'is_enabled' => true,
                    'module' => 'family',
                ],
                'receivers' => [],
            ],
            [
                'workflow' => [
                    'trigger' => 'presence.absent',
                    'name' => 'Absence enfant',
                    'description' => 'Notifier les parents lors d\'une absence non justifiée',
                    'is_enabled' => true,
                    'module' => 'presences',
                ],
                'receivers' => [
                    ['receiver_type' => 'role', 'receiver_value' => 'Parent', 'notification_medium' => 'sms', 'is_enabled' => true],
                ],
            ],
            [
                'workflow' => [
                    'trigger' => 'activity.created',
                    'name' => 'Nouvelle activité créée',
                    'description' => 'Notifier les parents des nouvelles activités',
                    'is_enabled' => true,
                    'module' => 'activities',
                ],
                'receivers' => [],
            ],
            [
                'workflow' => [
                    'trigger' => 'evaluation.created',
                    'name' => 'Évaluation enfant',
                    'description' => 'Notifier les parents d\'une nouvelle évaluation',
                    'is_enabled' => true,
                    'module' => 'activities',
                ],
                'receivers' => [
                    ['receiver_type' => 'role', 'receiver_value' => 'Parent', 'notification_medium' => 'email', 'is_enabled' => true],
                ],
            ],
            [
                'workflow' => [
                    'trigger' => 'incident.created',
                    'name' => 'Incident créé',
                    'description' => 'Notifier admin et responsable d\'un nouvel incident',
                    'is_enabled' => true,
                    'module' => 'incidents',
                ],
                'receivers' => [
                    ['receiver_type' => 'role', 'receiver_value' => 'Administrateur', 'notification_medium' => 'system', 'is_enabled' => true],
                ],
            ],
            [
                'workflow' => [
                    'trigger' => 'invoice.generated',
                    'name' => 'Facture générée',
                    'description' => 'Notifier les parents d\'une nouvelle facture',
                    'is_enabled' => true,
                    'module' => 'payments',
                ],
                'receivers' => [
                    ['receiver_type' => 'role', 'receiver_value' => 'Parent', 'notification_medium' => 'all', 'is_enabled' => true],
                ],
            ],
        ];

        foreach ($definitions as $definition) {
            $workflowData = $definition['workflow'];

            $workflow = NotificationWorkflow::updateOrCreate(
                ['trigger' => $workflowData['trigger']],
                [
                    'name' => $workflowData['name'],
                    'description' => $workflowData['description'],
                    'is_enabled' => $workflowData['is_enabled'],
                    'module' => $workflowData['module'],
                ]
            );

            NotificationReceiver::query()->where('workflow_id', $workflow->id)->delete();

            foreach ($definition['receivers'] as $receiverData) {
                NotificationReceiver::create($receiverData + ['workflow_id' => $workflow->id]);
            }
        }
    }
}
