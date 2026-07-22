<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_request_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('action_type', 20);
            $table->string('label', 255);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['action_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_request_subjects');
    }
};