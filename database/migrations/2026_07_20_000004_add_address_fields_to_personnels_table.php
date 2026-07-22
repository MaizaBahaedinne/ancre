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
        Schema::table('personnels', function (Blueprint $table) {
            $table->string('adresse_rue')->nullable()->after('lieu_delivrance_cin');
            $table->string('adresse_ville')->nullable()->after('adresse_rue');
            $table->string('adresse_gouvernorat')->nullable()->after('adresse_ville');
            $table->string('adresse_code_postal', 20)->nullable()->after('adresse_gouvernorat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->dropColumn([
                'adresse_rue',
                'adresse_ville',
                'adresse_gouvernorat',
                'adresse_code_postal',
            ]);
        });
    }
};