<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('vitrine_blog_posts')) {
            return;
        }

        Schema::table('vitrine_blog_posts', function (Blueprint $table) {
            $table->string('cover_url', 2048)->nullable()->change();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('vitrine_blog_posts')) {
            return;
        }

        Schema::table('vitrine_blog_posts', function (Blueprint $table) {
            // Truncate any data longer than 255 characters before shrinking
            DB::statement('UPDATE vitrine_blog_posts SET cover_url = SUBSTRING(cover_url, 1, 255) WHERE LENGTH(cover_url) > 255');
            $table->string('cover_url', 255)->nullable()->change();
        });
    }
};
