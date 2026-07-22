<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_request_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_request_id')->constrained('parent_requests')->cascadeOnDelete();
            $table->foreignId('sender_user_id')->constrained('users')->cascadeOnDelete();
            $table->longText('message');
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index(['parent_request_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_request_messages');
    }
};