#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "Updating notification receivers with correct role names...\n";

// Map old role names to new ones
$roleMapping = [
    'admin' => 'Administrateur',
    'responsable' => 'Responsable',
    'parent' => 'Parent',
];

$receivers = \App\Models\NotificationReceiver::where('receiver_type', 'role')->get();

foreach ($receivers as $receiver) {
    if (array_key_exists($receiver->receiver_value, $roleMapping)) {
        $oldValue = $receiver->receiver_value;
        $newValue = $roleMapping[$oldValue];
        $receiver->update(['receiver_value' => $newValue]);
        echo "✓ Updated: {$oldValue} → {$newValue}\n";
    }
}

echo "\nDone! Receivers updated.\n";
