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
        Schema::create('notification_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('trigger')->unique(); // parent.created, child.registered, etc
            $table->string('name');
            $table->longText('description')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->string('module')->default('core'); // core, family, activities, incidents, payments
            $table->json('config')->nullable(); // configuration additionnelle
            $table->timestamps();

            $table->index('trigger');
            $table->index('module');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_workflows');
    }
};
