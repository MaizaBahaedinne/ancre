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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('trigger'); // ex: parent.created, child.registered
            $table->string('subject');
            $table->longText('description');
            $table->enum('notification_type', ['system', 'email', 'sms'])->default('system');
            $table->string('receiver_type')->nullable(); // 'user', 'role', 'dynamic'
            $table->integer('receiver_id')->nullable();
            $table->string('receiver_role')->nullable(); // 'admin', 'responsable', etc
            $table->json('metadata')->nullable(); // données dynamiques
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('trigger');
            $table->index('notification_type');
            $table->index('read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
