<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $email = 'parent.test.' . time() . '@test.tn';
        
        // Create test user
        $userId = DB::table('users')->insertGetId([
            'name' => 'Parent Test ' . time(),
            'email' => $email,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create test parent using Eloquent to dispatch events
        $parent = new \App\Models\ParentModel([
            'nom' => 'Test',
            'prenom' => 'Parent',
            'numero_cin' => '12345678',
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
            'user_id' => $userId,
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);
        $parent->save();

        // Dispatch the ParentCreated event manually
        \Illuminate\Support\Facades\Event::dispatch(new \App\Events\ParentCreated($parent));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete test parents and users created by this migration
        // (They will be identified by the pattern and creation time)
        DB::table('parents')->where('email', 'like', 'parent.test.%')->delete();
        DB::table('users')->where('email', 'like', 'parent.test.%')->delete();
    }
};
