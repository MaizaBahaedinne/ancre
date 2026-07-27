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
        Schema::table('enfants', function (Blueprint $table) {
            $table->dropIndex('enfants_classe_index');
            $table->dropForeign(['school_class_id']);
            $table->dropColumn(['classe', 'school_class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enfants', function (Blueprint $table) {
            $table->string('classe')->nullable()->after('sexe');
            $table->foreignId('school_class_id')->nullable()->after('classe')->constrained('school_classes')->nullOnDelete();
            $table->index('classe');
        });
    }
};
