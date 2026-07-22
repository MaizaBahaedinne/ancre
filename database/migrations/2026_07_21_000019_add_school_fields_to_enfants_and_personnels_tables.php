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
            $table->foreignId('school_class_id')->nullable()->after('parent_id')->constrained('school_classes')->nullOnDelete();
        });

        Schema::table('personnels', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('photo')->constrained('schools')->nullOnDelete();
            $table->foreignId('school_class_id')->nullable()->after('school_id')->constrained('school_classes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_class_id');
            $table->dropConstrainedForeignId('school_id');
        });

        Schema::table('enfants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_class_id');
        });
    }
};