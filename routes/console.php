<?php

use App\Support\NotificationWorkflowSynchronizer;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('notifications:sync-triggers', function () {
    $result = app(NotificationWorkflowSynchronizer::class)->sync();

    $this->info('Notification triggers synchronized successfully.');
    $this->line('Workflows synchronized: '.$result['workflows']);
    $this->line('Receivers synchronized: '.$result['receivers']);
})->purpose('Synchronize notification workflows from TriggerRegistry and discovered events');
