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
            $table->time('heure_debut')->nullable()->after('date');
            $table->time('heure_fin')->nullable()->after('heure_debut');
            $table->string('recurrence', 30)->nullable()->after('heure_fin');
            $table->date('date_fin_recurrence')->nullable()->after('recurrence');
            $table->foreignId('responsable_personnel_id')->nullable()->after('date_fin_recurrence')->constrained('personnels')->nullOnDelete();
            $table->unsignedInteger('capacite')->nullable()->after('responsable');
            $table->decimal('frais_participation', 10, 2)->nullable()->after('capacite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activites', function (Blueprint $table) {
            $table->dropConstrainedForeignId('responsable_personnel_id');
            $table->dropColumn([
                'heure_debut',
                'heure_fin',
                'recurrence',
                'date_fin_recurrence',
                'capacite',
                'frais_participation',
            ]);
        });
    }
};