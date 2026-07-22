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
        Schema::table('enfants', function (Blueprint $table) {
            $table->json('allergie_options')->nullable()->after('has_allergie');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enfants', function (Blueprint $table) {
            $table->dropColumn('allergie_options');
        });
    }
};