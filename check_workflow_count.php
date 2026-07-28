<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'triggers='.App\Models\NotificationWorkflow::count().PHP_EOL;
echo 'sample='.App\Models\NotificationWorkflow::orderBy('trigger')->limit(20)->pluck('trigger')->implode(', ').PHP_EOL;
