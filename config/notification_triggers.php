<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Trigger Discovery
    |--------------------------------------------------------------------------
    |
    | Event class suffixes used by TriggerRegistry to auto-detect triggers.
    | Example: ParentCreated => parent.created
    |
    */
    'action_suffixes' => [
        'Created',
        'Updated',
        'Deleted',
        'Registered',
        'Generated',
        'Absent',
        'Approved',
        'Rejected',
        'Paid',
        'Sent',
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Mapping
    |--------------------------------------------------------------------------
    |
    | Used to infer a workflow module from trigger entity prefix.
    |
    */
    'module_map' => [
        'parent' => 'family',
        'child' => 'family',
        'presence' => 'presences',
        'activity' => 'activities',
        'evaluation' => 'activities',
        'incident' => 'incidents',
        'invoice' => 'payments',
        'payment' => 'payments',
    ],

    /*
    |--------------------------------------------------------------------------
    | Curated Defaults
    |--------------------------------------------------------------------------
    |
    | These definitions override auto-discovered values and include default
    | receivers. Add your new module triggers here only when you need custom
    | labels/receivers. Otherwise, event discovery handles them automatically.
    |
    */
    'definitions' => [
        [
            'trigger' => 'parent.created',
            'name' => 'Nouveau parent inscrit',
            'description' => 'Notifier admin et responsable lors de l\'inscription d\'un nouveau parent',
            'module' => 'family',
            'is_enabled' => true,
            'receivers' => [
                ['receiver_type' => 'role', 'receiver_value' => 'Administrateur', 'notification_medium' => 'all', 'is_enabled' => true],
                ['receiver_type' => 'role', 'receiver_value' => 'Responsable', 'notification_medium' => 'email', 'is_enabled' => true],
            ],
        ],
        [
            'trigger' => 'child.registered',
            'name' => 'Enfant inscrit',
            'description' => 'Notifier les parents lors de l\'inscription d\'un enfant',
            'module' => 'family',
            'is_enabled' => true,
            'receivers' => [],
        ],
        [
            'trigger' => 'presence.absent',
            'name' => 'Absence enfant',
            'description' => 'Notifier les parents lors d\'une absence non justifiée',
            'module' => 'presences',
            'is_enabled' => true,
            'receivers' => [
                ['receiver_type' => 'role', 'receiver_value' => 'Parent', 'notification_medium' => 'sms', 'is_enabled' => true],
            ],
        ],
        [
            'trigger' => 'activity.created',
            'name' => 'Nouvelle activité créée',
            'description' => 'Notifier les parents des nouvelles activités',
            'module' => 'activities',
            'is_enabled' => true,
            'receivers' => [],
        ],
        [
            'trigger' => 'evaluation.created',
            'name' => 'Évaluation enfant',
            'description' => 'Notifier les parents d\'une nouvelle évaluation',
            'module' => 'activities',
            'is_enabled' => true,
            'receivers' => [
                ['receiver_type' => 'role', 'receiver_value' => 'Parent', 'notification_medium' => 'email', 'is_enabled' => true],
            ],
        ],
        [
            'trigger' => 'incident.created',
            'name' => 'Incident créé',
            'description' => 'Notifier admin et responsable d\'un nouvel incident',
            'module' => 'incidents',
            'is_enabled' => true,
            'receivers' => [
                ['receiver_type' => 'role', 'receiver_value' => 'Administrateur', 'notification_medium' => 'system', 'is_enabled' => true],
            ],
        ],
        [
            'trigger' => 'invoice.generated',
            'name' => 'Facture générée',
            'description' => 'Notifier les parents d\'une nouvelle facture',
            'module' => 'payments',
            'is_enabled' => true,
            'receivers' => [
                ['receiver_type' => 'role', 'receiver_value' => 'Parent', 'notification_medium' => 'all', 'is_enabled' => true],
            ],
        ],
    ],
];
