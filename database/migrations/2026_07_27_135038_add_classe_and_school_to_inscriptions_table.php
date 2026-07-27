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
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->string('classe')->nullable()->after('type_garde');
            $table->unsignedBigInteger('school_class_id')->nullable()->after('classe');
            $table->foreign('school_class_id')->references('id')->on('school_classes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropForeign(['school_class_id']);
            $table->dropColumn(['classe', 'school_class_id']);
        });
    }
};
