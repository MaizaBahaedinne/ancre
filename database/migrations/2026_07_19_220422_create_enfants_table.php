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
        Schema::create('enfants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->enum('sexe', ['M', 'F']);
            $table->string('classe')->nullable();
            $table->string('photo')->nullable();
            $table->text('allergies')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index(['nom', 'prenom']);
            $table->index('classe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enfants');
    }
};
