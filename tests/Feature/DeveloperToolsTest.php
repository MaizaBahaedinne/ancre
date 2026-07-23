<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DeveloperToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_developer_pages_require_permission(): void
    {
        $response = $this->get(route('admin.developer.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_developer_dashboard_and_logs_are_accessible_for_authorized_users(): void
    {
        Permission::findOrCreate('developer.tools.view');

        $user = User::factory()->create();
        $user->givePermissionTo('developer.tools.view');

        File::ensureDirectoryExists(storage_path('logs'));
        File::put(storage_path('logs/laravel.log'), <<<LOG
[2026-07-23 10:00:00] local.INFO: Deployment started
[2026-07-23 10:01:00] local.ERROR: Something failed
LOG);

        $dashboardResponse = $this->actingAs($user)->get(route('admin.developer.index'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Espace developpeur', false);
        $dashboardResponse->assertSee('git pull origin main', false);

        $logsResponse = $this->actingAs($user)->get(route('admin.developer.logs'));
        $logsResponse->assertOk();
        $logsResponse->assertSee('Deployment started', false);
        $logsResponse->assertSee('Something failed', false);
    }
}
