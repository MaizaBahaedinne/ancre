<?php

namespace Tests\Feature;

use App\Models\ParentModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
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

        $response->assertSee(URL::signedRoute('parents.verification', ['parent' => $parent->id]), false);
    }

    public function test_verification_page_is_public_and_renders_form(): void
    {
        $parent = ParentModel::create([
            'nom' => 'Test',
            'prenom' => 'Parent',
            'telephone' => '12345678',
            'email' => 'parent@example.com',
            'verification_status' => 'pending',
        ]);

        $response = $this->get(URL::signedRoute('parents.verification', ['parent' => $parent->id]));

        $response->assertOk();
        $response->assertSee('Soumettre la verification', false);
        $response->assertSee('Email du parent', false);
    }

    public function test_verification_submission_creates_parent_user_after_validation(): void
    {
        Storage::fake('public');

        $parent = ParentModel::create([
            'nom' => 'Test',
            'prenom' => 'Parent',
            'telephone' => '12345678',
            'email' => 'parent@example.com',
            'verification_status' => 'pending',
        ]);

        $verificationUrl = URL::signedRoute('parents.verification', ['parent' => $parent->id]);
        $documentUrl = URL::signedRoute('parents.verification.document', ['parent' => $parent->id]);
        $signatureUrl = URL::signedRoute('parents.verification.signature', ['parent' => $parent->id]);

        $this->post($documentUrl, [
            'side' => 'cin_recto',
            'cin_file' => UploadedFile::fake()->create('recto.pdf', 100, 'application/pdf'),
        ])->assertOk();

        $this->post($documentUrl, [
            'side' => 'cin_verso',
            'cin_file' => UploadedFile::fake()->create('verso.pdf', 100, 'application/pdf'),
        ])->assertOk();

        $this->postJson($signatureUrl, [
            'signature_data' => 'data:image/png;base64,'.base64_encode('fake-signature-binary'),
        ])->assertOk();

        $response = $this->from($verificationUrl)->post(URL::signedRoute('parents.verification.store', ['parent' => $parent->id]), [
            'email' => 'parent@example.com',
            'terms_accepted' => 1,
        ]);

        $response->assertRedirect($verificationUrl);
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