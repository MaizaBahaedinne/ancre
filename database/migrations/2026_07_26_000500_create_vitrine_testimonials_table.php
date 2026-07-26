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
        Schema::create('vitrine_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('parent_name');
            $table->string('child_name')->nullable();
            $table->text('content');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vitrine_testimonials');
    }
};
