#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "Creating test parent...\n";

try {
    $timestamp = time();
    $email = 'parent.test.' . $timestamp . '@test.tn';

    // Create user
    $user = \App\Models\User::create([
        'name' => 'Parent Test ' . $timestamp,
        'email' => $email,
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'email_verified_at' => now(),
    ]);

    echo "User created: {$user->id} ({$user->email})\n";

    // Create parent
    $parent = \App\Models\ParentModel::create([
        'nom' => 'Test',
        'prenom' => 'Parent',
        'numero_cin' => '12345' . substr($timestamp, -3),
        'date_delivrance_cin' => '2010-01-01',
        'date_naissance' => '1970-01-01',
        'sexe' => 'M',
        'telephone' => '+21695000000',
        'email' => $email,
        'adresse' => 'Rue de Test',
        'adresse_rue' => 'Rue de Test',
        'adresse_ville' => 'Sfax',
        'adresse_gouvernorat' => 'Sfax',
        'profession' => 'Test',
        'contact_urgence' => 'Test Contact',
        'user_id' => $user->id,
        'verification_status' => 'verified',
        'verified_at' => now(),
    ]);

    echo "Parent created: {$parent->id}\n";

    // Dispatch event
    echo "Dispatching ParentCreated event...\n";
    \Illuminate\Support\Facades\Event::dispatch(new \App\Events\ParentCreated($parent));
    
    echo "✓ Parent created and event dispatched successfully!\n";
    
    // Check notifications
    echo "\nChecking notifications...\n";
    $notifications = \App\Models\Notification::where('trigger', 'parent.created')->get();
    echo "Total notifications created: {$notifications->count()}\n";
    foreach ($notifications as $notif) {
        echo "  - ID: {$notif->id}, User: {$notif->user_id}, Subject: {$notif->subject}\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
