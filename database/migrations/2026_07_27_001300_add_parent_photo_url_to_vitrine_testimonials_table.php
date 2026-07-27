<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('vitrine_testimonials') || Schema::hasColumn('vitrine_testimonials', 'parent_photo_url')) {
            return;
        }

        Schema::table('vitrine_testimonials', function (Blueprint $table) {
            $table->string('parent_photo_url', 2048)->nullable()->after('child_name');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('vitrine_testimonials') || !Schema::hasColumn('vitrine_testimonials', 'parent_photo_url')) {
            return;
        }

        Schema::table('vitrine_testimonials', function (Blueprint $table) {
            $table->dropColumn('parent_photo_url');
        });
    }
};
