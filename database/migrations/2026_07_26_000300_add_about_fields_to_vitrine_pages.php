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
            $table->text('mission')->nullable()->after('content');
            $table->text('vision')->nullable()->after('mission');
            $table->text('valeurs')->nullable()->after('vision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vitrine_pages', function (Blueprint $table) {
            $table->dropColumn(['mission', 'vision', 'valeurs']);
        });
    }
};
