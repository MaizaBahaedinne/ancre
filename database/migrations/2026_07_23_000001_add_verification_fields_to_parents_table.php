<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->string('verification_token', 80)->nullable()->unique()->after('contact_urgence');
            $table->string('verification_status')->default('pending')->after('contact_urgence');
            $table->timestamp('verification_submitted_at')->nullable()->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('verification_submitted_at');
            $table->string('verification_signature')->nullable()->after('verified_at');
            $table->timestamp('verification_terms_accepted_at')->nullable()->after('verification_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn([
                'verification_token',
                'verification_status',
                'verification_submitted_at',
                'verified_at',
                'verification_signature',
                'verification_terms_accepted_at',
            ]);
        });
    }
};