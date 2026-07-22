<?php

use App\Models\ActivityRegistration;
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
        Schema::create('activity_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activite_id')->constrained('activites')->cascadeOnDelete();
            $table->foreignId('enfant_id')->constrained('enfants')->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->string('status', 40)->default(ActivityRegistration::STATUS_PENDING_PAYMENT);
            $table->string('participation_status', 20)->nullable();
            $table->decimal('amount_due', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference', 120)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['activite_id', 'enfant_id']);
            $table->index('status');
            $table->index('participation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_registrations');
    }
};
