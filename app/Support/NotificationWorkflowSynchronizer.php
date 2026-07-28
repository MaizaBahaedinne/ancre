<?php

namespace App\Support;

use App\Models\NotificationReceiver;
use App\Models\NotificationTriggerOverride;
use App\Models\NotificationWorkflow;

class NotificationWorkflowSynchronizer
{
    /**
     * Synchronize workflow definitions from TriggerRegistry to database.
     *
     * @return array{workflows:int, receivers:int}
     */
    public function sync(): array
    {
        $definitions = app(TriggerRegistry::class)->definitions();
        $enabledOverrides = NotificationTriggerOverride::query()
            ->whereNotNull('is_enabled')
            ->pluck('is_enabled', 'trigger');

        $workflowCount = 0;
        $receiverCount = 0;

        foreach ($definitions as $workflowData) {
            if (empty($workflowData['trigger'])) {
                continue;
            }

            $workflow = NotificationWorkflow::query()->firstOrNew([
                'trigger' => $workflowData['trigger'],
            ]);

            $isEnabled = $workflow->exists
                ? (bool) $workflow->is_enabled
                : (bool) ($workflowData['is_enabled'] ?? false);

            if ($enabledOverrides->has($workflowData['trigger'])) {
                $isEnabled = (bool) $enabledOverrides->get($workflowData['trigger']);
            }

            $workflow->fill([
                'name' => $workflowData['name'] ?? $workflowData['trigger'],
                'description' => $workflowData['description'] ?? null,
                'is_enabled' => $isEnabled,
                'module' => $workflowData['module'] ?? 'general',
            ]);
            $workflow->save();

            $workflowCount++;

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

                $receiverCount++;
            }
        }

        return [
            'workflows' => $workflowCount,
            'receivers' => $receiverCount,
        ];
    }
}
