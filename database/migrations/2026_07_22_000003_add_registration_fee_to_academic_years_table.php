<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->decimal('registration_fee', 10, 2)->default(0)->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn('registration_fee');
        });
    }
};