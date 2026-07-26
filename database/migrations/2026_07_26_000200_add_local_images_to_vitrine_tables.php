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
        Schema::table('vitrine_pages', function (Blueprint $table) {
            $table->string('hero_image')->nullable()->after('hero_subtitle');
        });

        Schema::table('vitrine_social_posts', function (Blueprint $table) {
            $table->string('thumbnail_path')->nullable()->after('thumbnail_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vitrine_social_posts', function (Blueprint $table) {
            $table->dropColumn('thumbnail_path');
        });

        Schema::table('vitrine_pages', function (Blueprint $table) {
            $table->dropColumn('hero_image');
        });
    }
};
