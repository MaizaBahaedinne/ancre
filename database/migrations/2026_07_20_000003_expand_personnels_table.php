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
        Schema::table('personnels', function (Blueprint $table) {
            $table->string('sexe', 1)->nullable()->after('prenom');
            $table->date('date_naissance')->nullable()->after('sexe');
            $table->string('departement')->nullable()->after('fonction');
            $table->string('niveau_etude')->nullable()->after('departement');
            $table->string('domaine_etude')->nullable()->after('niveau_etude');
            $table->unsignedInteger('annees_experience')->default(0)->after('domaine_etude');
            $table->string('numero_cin')->nullable()->after('annees_experience');
            $table->date('date_delivrance_cin')->nullable()->after('numero_cin');
            $table->string('lieu_delivrance_cin')->nullable()->after('date_delivrance_cin');
            $table->string('numero_cnss')->nullable()->after('email');
            $table->foreignId('manager_id')->nullable()->after('date_embauche')->constrained('personnels')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->after('manager_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('manager_id');
            $table->dropColumn([
                'sexe',
                'date_naissance',
                'departement',
                'niveau_etude',
                'domaine_etude',
                'annees_experience',
                'numero_cin',
                'date_delivrance_cin',
                'lieu_delivrance_cin',
                'numero_cnss',
            ]);
        });
    }
};