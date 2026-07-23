<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->string('adresse_rue')->nullable()->after('adresse');
            $table->string('adresse_ville')->nullable()->after('adresse_rue');
            $table->string('adresse_gouvernorat')->nullable()->after('adresse_ville');
        });
    }

    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn([
                'adresse_rue',
                'adresse_ville',
                'adresse_gouvernorat',
            ]);
        });
    }
};
