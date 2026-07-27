#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Get the admin user
$admin = \App\Models\User::where('email', 'admin@ancredeselites.tn')->first();
echo "Admin user: " . ($admin ? $admin->id . ' - ' . $admin->email : 'NOT FOUND') . "\n";

if (!$admin) {
    echo "Error: Admin user not found!\n";
    exit(1);
}

// Check notifications for admin
$notifications = \App\Models\Notification::where('user_id', $admin->id)->get();
echo "\nNotifications for admin (user_id={$admin->id}): {$notifications->count()}\n";

foreach ($notifications as $notif) {
    echo "  - ID: {$notif->id}, Trigger: {$notif->trigger}, Subject: {$notif->subject}\n";
    echo "    Read: " . ($notif->read_at ? 'Yes' : 'No') . "\n";
}

// Also check all notifications
echo "\nAll notifications in DB:\n";
$allNotifications = \App\Models\Notification::all();
foreach ($allNotifications as $notif) {
    echo "  - ID: {$notif->id}, User: {$notif->user_id}, Trigger: {$notif->trigger}\n";
}
