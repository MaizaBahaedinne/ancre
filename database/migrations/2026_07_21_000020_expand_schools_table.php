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
        Schema::table('schools', function (Blueprint $table) {
            $table->string('address_route')->nullable()->after('name');
            $table->string('address_street')->nullable()->after('address_route');
            $table->string('address_postal_code', 20)->nullable()->after('address_street');
            $table->string('address_city')->nullable()->after('address_postal_code');
            $table->string('address_governorate')->nullable()->after('address_city');
            $table->string('director_name')->nullable()->after('phone');
            $table->string('director_contact')->nullable()->after('director_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'address_route',
                'address_street',
                'address_postal_code',
                'address_city',
                'address_governorate',
                'director_name',
                'director_contact',
            ]);
        });
    }
};