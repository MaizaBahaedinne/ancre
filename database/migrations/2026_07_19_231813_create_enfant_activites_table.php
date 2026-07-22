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
        Schema::create('enfant_activites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enfant_id')->constrained('enfants')->cascadeOnDelete();
            $table->foreignId('activite_id')->constrained('activites')->cascadeOnDelete();
            $table->enum('statut', ['Present', 'Absent'])->default('Present');
            $table->text('remarque')->nullable();
            $table->timestamps();

            $table->unique(['enfant_id', 'activite_id']);
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enfant_activites');
    }
};
