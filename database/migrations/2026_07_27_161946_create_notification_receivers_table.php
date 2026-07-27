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
        Schema::create('notification_receivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('notification_workflows')->onDelete('cascade');
            $table->string('receiver_type'); // 'role', 'user', 'dynamic'
            $table->string('receiver_value')->nullable(); // 'admin', 'parent', 'user_id:5', etc
            $table->enum('notification_medium', ['system', 'email', 'sms', 'all'])->default('system');
            $table->boolean('is_enabled')->default(true);
            $table->json('conditions')->nullable(); // conditions dynamiques
            $table->timestamps();

            $table->index('workflow_id');
            $table->index('receiver_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_receivers');
    }
};
