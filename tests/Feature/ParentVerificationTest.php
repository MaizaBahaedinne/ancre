<?php

namespace Tests\Feature;

use App\Models\ParentModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ParentVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_show_page_exposes_verification_prompt(): void
    {
        Permission::findOrCreate('parents.view');

        $user = User::factory()->create();
        $user->givePermissionTo('parents.view');

        $parent = ParentModel::create([
            'nom' => 'Test',
            'prenom' => 'Parent',
            'telephone' => '12345678',
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('parents.show', $parent));

        $response->assertOk();
        $response->assertSee('Verification du compte parent', false);
        $response->assertSee(route('parents.verification', $parent), false);
    }

    public function test_verification_page_is_accessible_and_renders_form(): void
    {
        Permission::findOrCreate('parents.view');
        Permission::findOrCreate('parents.update');

        $user = User::factory()->create();
        $user->givePermissionTo(['parents.view', 'parents.update']);

        $parent = ParentModel::create([
            'nom' => 'Test',
            'prenom' => 'Parent',
            'telephone' => '12345678',
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('parents.verification', $parent));

        $response->assertOk();
        $response->assertSee('Soumettre la verification', false);
    }
}