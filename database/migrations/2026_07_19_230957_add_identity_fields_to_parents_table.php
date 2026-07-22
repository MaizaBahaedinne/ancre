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
        Schema::table('parents', function (Blueprint $table) {
            $table->string('numero_cin', 20)->nullable()->unique()->after('prenom');
            $table->date('date_delivrance_cin')->nullable()->after('numero_cin');
            $table->date('date_naissance')->nullable()->after('date_delivrance_cin');
            $table->enum('sexe', ['M', 'F'])->nullable()->after('date_naissance');
            $table->string('cin_recto')->nullable()->after('contact_urgence');
            $table->string('cin_verso')->nullable()->after('cin_recto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->dropUnique(['numero_cin']);
            $table->dropColumn([
                'numero_cin',
                'date_delivrance_cin',
                'date_naissance',
                'sexe',
                'cin_recto',
                'cin_verso',
            ]);
        });
    }
};
