#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== WORKFLOWS ===\n";
$workflows = \App\Models\NotificationWorkflow::all();

foreach ($workflows as $wf) {
    echo "\nWorkflow: {$wf->trigger}\n";
    echo "  Name: {$wf->name}\n";
    echo "  Enabled: " . ($wf->is_enabled ? 'Yes' : 'No') . "\n";
    
    echo "  Receivers:\n";
    $receivers = $wf->receivers;
    foreach ($receivers as $rec) {
        echo "    - Type: {$rec->receiver_type}, Value: {$rec->receiver_value}, Medium: {$rec->notification_medium}\n";
    }
}

echo "\n\n=== ROLES ===\n";
$roles = \Spatie\Permission\Models\Role::all();
echo "Total roles: " . $roles->count() . "\n";
foreach ($roles as $role) {
    echo "  - {$role->name}\n";
}

echo "\n\n=== USERS ===\n";
$users = \App\Models\User::all();
foreach ($users as $user) {
    echo "  - ID: {$user->id}, Email: {$user->email}, Name: {$user->name}\n";
    echo "    Roles: " . $user->roles()->pluck('name')->join(', ') . "\n";
}
