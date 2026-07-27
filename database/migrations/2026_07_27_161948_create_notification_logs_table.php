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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->string('channel'); // email, sms, system
            $table->string('recipient'); // email address or phone number
            $table->enum('status', ['pending', 'sent', 'failed', 'bounced'])->default('pending');
            $table->longText('error_message')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();

            $table->index('notification_id');
            $table->index('status');
            $table->index('channel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
