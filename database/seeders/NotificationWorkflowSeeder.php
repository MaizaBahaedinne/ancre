<?php

namespace Database\Seeders;

use App\Support\NotificationWorkflowSynchronizer;
use Illuminate\Database\Seeder;

class NotificationWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(NotificationWorkflowSynchronizer::class)->sync();
    }
}
