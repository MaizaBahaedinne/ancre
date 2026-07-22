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
        Schema::table('activites', function (Blueprint $table) {
            $table->json('recurrence_jours')->nullable()->after('recurrence');
            $table->unsignedTinyInteger('recurrence_jour_mois')->nullable()->after('recurrence_jours');
            $table->date('recurrence_date_annuelle')->nullable()->after('recurrence_jour_mois');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activites', function (Blueprint $table) {
            $table->dropColumn([
                'recurrence_jours',
                'recurrence_jour_mois',
                'recurrence_date_annuelle',
            ]);
        });
    }
};