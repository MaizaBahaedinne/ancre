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
        if (! Schema::hasTable('academic_subjects')) {
            Schema::create('academic_subjects', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('level', 50);
                $table->decimal('default_coefficient', 4, 2)->default(1);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['name', 'level']);
                $table->index('level');
            });
        }

        if (! Schema::hasTable('enfant_evaluations')) {
            Schema::create('enfant_evaluations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('enfant_id')->constrained()->cascadeOnDelete();
                $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
                $table->foreignId('school_class_id')->nullable()->constrained()->nullOnDelete();
                $table->string('trimester', 20);
                $table->decimal('general_average', 5, 2)->nullable();
                $table->unsignedSmallInteger('class_rank')->nullable();
                $table->date('bulletin_received_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['enfant_id', 'academic_year_id', 'trimester']);
                $table->index(['academic_year_id', 'trimester']);
            });
        }

        if (! Schema::hasTable('enfant_evaluation_grades')) {
            Schema::create('enfant_evaluation_grades', function (Blueprint $table) {
                $table->id();
                $table->foreignId('enfant_evaluation_id')->constrained()->cascadeOnDelete();
                $table->foreignId('academic_subject_id')->constrained()->cascadeOnDelete();
                $table->decimal('grade', 5, 2);
                $table->decimal('coefficient', 4, 2)->default(1);
                $table->timestamps();

                $table->unique(['enfant_evaluation_id', 'academic_subject_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('enfant_evaluation_grades')) {
            Schema::drop('enfant_evaluation_grades');
        }

        if (Schema::hasTable('enfant_evaluations')) {
            Schema::drop('enfant_evaluations');
        }

        if (Schema::hasTable('academic_subjects')) {
            Schema::drop('academic_subjects');
        }
    }
};
