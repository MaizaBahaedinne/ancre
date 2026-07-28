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
    | Route Discovery
    |--------------------------------------------------------------------------
    |
    | Detect triggers from named routes so new platform modules are discovered
    | automatically without adding manual trigger definitions.
    |
    */
    'route_discovery' => [
        'enabled' => true,

        // Strip these prefixes when inferring the domain entity.
        'context_prefixes' => ['admin', 'parent', 'vitrine'],

        // Ignore technical/auth/internal route groups.
        'ignore_route_name_prefixes' => [
            'login',
            'logout',
            'password',
            'verification',
            'sanctum',
            'ignition',
            'debugbar',
            'profile.',
            'search.',
            'notifications.',
            'admin.notifications.',
            'admin.developer.',
            'admin.vitrine.',
        ],

        // Route action => trigger action
        'action_map' => [
            'index' => 'viewed',
            'show' => 'viewed',
            'store' => 'created',
            'create' => 'create_form_opened',
            'update' => 'updated',
            'edit' => 'edit_form_opened',
            'destroy' => 'deleted',
            'sync' => 'synced',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Runtime Dispatch
    |--------------------------------------------------------------------------
    |
    | Route discovery only creates trigger definitions. These options control
    | automatic runtime dispatch based on route names.
    |
    */
    'runtime' => [
        'auto_dispatch_from_routes' => true,

        // Dispatch only after successful requests using these HTTP methods.
        'dispatch_methods' => ['POST', 'PUT', 'PATCH', 'DELETE'],

        // Avoid duplicate notifications for triggers already dispatched manually.
        'skip_triggers' => [
            'parent.created',
            'school.created',
        ],
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
        'enfant' => 'family',
        'demande' => 'communication',
        'request' => 'communication',
        'inscription' => 'school_life',
        'presence' => 'presences',
        'activity' => 'activities',
        'activite' => 'activities',
        'evaluation' => 'activities',
        'incident' => 'incidents',
        'invoice' => 'payments',
        'payment' => 'payments',
        'paiement' => 'payments',
        'package' => 'payments',
        'school' => 'structure',
        'salle' => 'structure',
        'subject' => 'structure',
        'academic_year' => 'structure',
        'personnel' => 'team',
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
