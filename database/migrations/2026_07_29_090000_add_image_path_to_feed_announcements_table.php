<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feed_announcements', function (Blueprint $table) {
            if (! Schema::hasColumn('feed_announcements', 'image_path')) {
                $table->string('image_path')->nullable()->after('body');
            }
        });
    }

    public function down(): void
    {
        Schema::table('feed_announcements', function (Blueprint $table) {
            if (Schema::hasColumn('feed_announcements', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
    }
};
