#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Get admin user
$admin = \App\Models\User::where('email', 'admin@ancredeselites.tn')->first();

echo "Testing API for Admin (ID: {$admin->id})\n\n";

// Test unread notifications
$unread = $admin->unreadNotifications()->get();
echo "Unread notifications: {$unread->count()}\n";
foreach ($unread as $notif) {
    echo "  - ID: {$notif->id}, Trigger: {$notif->trigger}, Subject: {$notif->subject}\n";
}

// Mark one as read
if ($unread->count() > 0) {
    $first = $unread->first();
    $first->markAsRead();
    echo "\n✓ Marked notification {$first->id} as read\n";
    
    // Check again
    $unreadAfter = $admin->unreadNotifications()->get();
    echo "Unread after marking: {$unreadAfter->count()}\n";
}

// Check all notifications
$all = $admin->notifications()->get();
echo "\nTotal notifications for admin: {$all->count()}\n";
