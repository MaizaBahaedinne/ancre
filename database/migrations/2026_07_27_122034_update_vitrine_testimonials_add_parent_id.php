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
        Schema::table('vitrine_testimonials', function (Blueprint $table) {
            if (!Schema::hasColumn('vitrine_testimonials', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
                $table->foreign('parent_id')->references('id')->on('parents')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vitrine_testimonials', function (Blueprint $table) {
            if (Schema::hasColumn('vitrine_testimonials', 'parent_id')) {
                // Drop foreign key if it exists
                try {
                    $table->dropForeign(['parent_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }
                $table->dropColumn('parent_id');
            }
        });
    }
};
