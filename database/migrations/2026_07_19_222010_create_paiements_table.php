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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enfant_id')->constrained('enfants')->cascadeOnDelete();
            $table->decimal('montant', 10, 2);
            $table->date('date_paiement');
            $table->unsignedTinyInteger('mois');
            $table->unsignedSmallInteger('annee');
            $table->enum('mode_paiement', ['Especes', 'Carte', 'Virement', 'Cheque']);
            $table->enum('statut', ['Paye', 'En retard', 'Partiel'])->default('Paye');
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->index(['enfant_id', 'annee', 'mois']);
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
