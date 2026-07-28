<?php

namespace Database\Seeders;

use App\Models\NotificationWorkflow;
use App\Models\NotificationReceiver;
use App\Support\TriggerRegistry;
use Illuminate\Database\Seeder;

class NotificationWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $definitions = app(TriggerRegistry::class)->definitions();

        foreach ($definitions as $workflowData) {

            $workflow = NotificationWorkflow::updateOrCreate(
                ['trigger' => $workflowData['trigger']],
                [
                    'name' => $workflowData['name'],
                    'description' => $workflowData['description'],
                    'is_enabled' => $workflowData['is_enabled'],
                    'module' => $workflowData['module'],
                ]
            );

            $receivers = is_array($workflowData['receivers'] ?? null) ? $workflowData['receivers'] : [];
            foreach ($receivers as $receiverData) {
                NotificationReceiver::updateOrCreate(
                    [
                        'workflow_id' => $workflow->id,
                        'receiver_type' => $receiverData['receiver_type'] ?? null,
                        'receiver_value' => $receiverData['receiver_value'] ?? null,
                        'notification_medium' => $receiverData['notification_medium'] ?? 'system',
                    ],
                    [
                        'is_enabled' => (bool) ($receiverData['is_enabled'] ?? true),
                        'conditions' => $receiverData['conditions'] ?? null,
                    ]
                );
            }
        }
    }
}
