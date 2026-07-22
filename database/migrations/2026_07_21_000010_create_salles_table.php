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
        Schema::create('salles', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->string('etage', 50);
            $table->unsignedInteger('capacite');
            $table->json('equipements')->nullable();
            $table->string('statut', 30)->default('disponible');
            $table->foreignId('responsable_personnel_id')->nullable()->constrained('personnels')->nullOnDelete();
            $table->timestamps();

            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salles');
    }
};
