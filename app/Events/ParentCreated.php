<?php

namespace App\Events;

use App\Contracts\DefinesNotificationTrigger;
use App\Models\ParentModel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParentCreated implements DefinesNotificationTrigger
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ParentModel $parent
    ) {
    }

    public static function notificationTriggerDefinition(): array
    {
        return [
            'trigger' => 'parent.created',
            'name' => 'Nouveau parent inscrit',
            'description' => 'Notifier admin et responsable lors de l\'inscription d\'un nouveau parent',
            'module' => 'family',
            'is_enabled' => true,
        ];
    }
}
