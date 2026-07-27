<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = \App\Models\User::with('roles')->limit(5)->get();
foreach($users as $u) {
    echo $u->id . ' - ' . $u->email . ' - ' . $u->roles->pluck('name')->join(', ') . "\n";
}
