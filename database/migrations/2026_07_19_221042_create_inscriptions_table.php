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
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enfant_id')->constrained('enfants')->cascadeOnDelete();
            $table->string('annee_scolaire', 20);
            $table->date('date_inscription');
            $table->enum('type_garde', ['Matin', 'Apres-midi', 'Journee complete']);
            $table->enum('statut', ['Active', 'Renouvelee', 'Suspendue', 'Annulee'])->default('Active');
            $table->timestamps();

            $table->index(['enfant_id', 'annee_scolaire']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
