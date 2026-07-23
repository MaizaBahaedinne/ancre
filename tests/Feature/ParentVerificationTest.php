<?php

namespace Tests\Feature;

use App\Models\ParentModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ParentVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_show_generates_missing_verification_token_and_renders_link(): void
    {
        Permission::findOrCreate('parents.view');

        $user = User::factory()->create();
        $user->givePermissionTo('parents.view');

        $parent = ParentModel::create([
            'nom' => 'Test',
            'prenom' => 'Parent',
            'telephone' => '12345678',
            'email' => 'parent@example.com',
            'verification_status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('parents.show', $parent));

        $response->assertOk();
        $response->assertSee('Verification du compte parent', false);

        $parent->refresh();

        $this->assertNotNull($parent->verification_token);
        $response->assertSee(route('parents.verification', $parent->verification_token), false);
    }

    public function test_verification_page_is_public_and_renders_form(): void
    {
        $parent = ParentModel::create([
            'nom' => 'Test',
            'prenom' => 'Parent',
            'telephone' => '12345678',
            'email' => 'parent@example.com',
            'verification_status' => 'pending',
            'verification_token' => 'ABC123TOKEN',
        ]);

        $response = $this->get(route('parents.verification', $parent->verification_token));

        $response->assertOk();
        $response->assertSee('Soumettre la verification', false);
        $response->assertSee('Email du parent', false);
    }

    public function test_verification_submission_creates_parent_user_after_validation(): void
    {
        $parent = ParentModel::create([
            'nom' => 'Test',
            'prenom' => 'Parent',
            'telephone' => '12345678',
            'email' => 'parent@example.com',
            'verification_status' => 'pending',
            'verification_token' => 'ABC123TOKEN',
        ]);

        $response = $this->from(route('parents.verification', $parent->verification_token))->post(route('parents.verification.store', $parent->verification_token), [
            'email' => 'parent@example.com',
            'verification_signature' => 'Parent Signature',
            'terms_accepted' => 1,
            'identity_documents' => [
                UploadedFile::fake()->create('recto.pdf', 100, 'application/pdf'),
                UploadedFile::fake()->create('verso.pdf', 100, 'application/pdf'),
            ],
        ]);

        $response->assertRedirect(route('parents.verification', $parent->verification_token));
        $response->assertSessionHas('success');

        $createdUser = User::query()->where('email', 'parent@example.com')->first();

        $this->assertNotNull($createdUser);

        $this->assertDatabaseHas('parents', [
            'id' => $parent->id,
            'verification_status' => 'verified',
            'user_id' => $createdUser?->id,
        ]);

        $this->assertSame($createdUser?->id, $parent->fresh()->user_id);
    }
}