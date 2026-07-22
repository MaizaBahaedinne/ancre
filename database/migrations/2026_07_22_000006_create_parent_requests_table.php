<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('enfant_id')->constrained('enfants')->cascadeOnDelete();
            $table->string('action_type', 20);
            $table->foreignId('subject_id')->nullable()->constrained('parent_request_subjects')->nullOnDelete();
            $table->string('subject_snapshot', 255)->nullable();
            $table->string('subject_other', 255)->nullable();
            $table->longText('description');
            $table->json('attachments')->nullable();
            $table->string('workflow_status', 30)->default('cree');
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('in_progress_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->longText('resolution_note')->nullable();
            $table->foreignId('handled_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['parent_id', 'workflow_status']);
            $table->index(['enfant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_requests');
    }
};