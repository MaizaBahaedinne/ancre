#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Check notifications
$notifications = \App\Models\Notification::orderBy('created_at', 'DESC')->limit(10)->get();

echo "Total notifications: " . $notifications->count() . "\n\n";

foreach ($notifications as $notif) {
    echo "ID: {$notif->id}, User: {$notif->user_id}, Trigger: {$notif->trigger}\n";
    echo "Subject: {$notif->subject}\n";
    echo "Created: {$notif->created_at}\n";
    echo "---\n";
}
