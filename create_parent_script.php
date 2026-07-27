<?php

require __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Create a test user and parent
$user = \App\Models\User::create([
    'name' => 'Test Parent User',
    'email' => 'testparent' . time() . '@test.tn',
    'password' => \Illuminate\Support\Facades\Hash::make('password'),
]);

$parent = \App\Models\ParentModel::create([
    'nom' => 'TestNom',
    'prenom' => 'TestParent',
    'numero_cin' => '12345678',
    'date_delivrance_cin' => '2020-01-01',
    'date_naissance' => '1980-01-01',
    'sexe' => 'masculin',
    'telephone' => '+21695123456',
    'email' => $user->email,
    'adresse' => 'Rue de test',
    'adresse_rue' => 'Rue de test',
    'adresse_ville' => 'Sfax',
    'adresse_gouvernorat' => 'Sfax',
    'profession' => 'Professeur',
    'contact_urgence' => 'Contact urgence test',
    'user_id' => $user->id,
]);

echo "Parent created: " . $parent->id . "\n";
echo "User: " . $user->email . "\n";
