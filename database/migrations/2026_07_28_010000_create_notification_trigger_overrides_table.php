<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_trigger_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('trigger')->unique();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('module')->nullable();
            $table->boolean('is_enabled')->nullable();
            $table->json('receivers')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_trigger_overrides');
    }
};
