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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enfant_id')->constrained('enfants')->cascadeOnDelete();
            $table->date('date');
            $table->string('type_incident');
            $table->text('description');
            $table->text('action_realisee')->nullable();
            $table->timestamps();

            $table->index(['enfant_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
