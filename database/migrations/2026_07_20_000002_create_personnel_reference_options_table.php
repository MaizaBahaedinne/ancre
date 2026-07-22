<?php

use App\Models\PersonnelReferenceOption;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personnel_reference_options', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('label');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['type', 'label']);
            $table->index(['type', 'is_active', 'sort_order']);
        });

        DB::table('personnel_reference_options')->insert([
            ['type' => PersonnelReferenceOption::TYPE_FONCTION, 'label' => 'Directeur', 'sort_order' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_FONCTION, 'label' => 'Responsable administratif', 'sort_order' => 20, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_FONCTION, 'label' => 'Educateur', 'sort_order' => 30, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_FONCTION, 'label' => 'Aide educateur', 'sort_order' => 40, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_FONCTION, 'label' => 'Agent administratif', 'sort_order' => 50, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_FONCTION, 'label' => 'Agent de soutien', 'sort_order' => 60, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            ['type' => PersonnelReferenceOption::TYPE_DEPARTEMENT, 'label' => 'Direction', 'sort_order' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_DEPARTEMENT, 'label' => 'Administration', 'sort_order' => 20, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_DEPARTEMENT, 'label' => 'Pedagogie', 'sort_order' => 30, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_DEPARTEMENT, 'label' => 'Sante et securite', 'sort_order' => 40, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_DEPARTEMENT, 'label' => 'Logistique', 'sort_order' => 50, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            ['type' => PersonnelReferenceOption::TYPE_NIVEAU_ETUDE, 'label' => 'Bac', 'sort_order' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_NIVEAU_ETUDE, 'label' => 'Bac +1', 'sort_order' => 20, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_NIVEAU_ETUDE, 'label' => 'Bac +2', 'sort_order' => 30, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_NIVEAU_ETUDE, 'label' => 'Bac +3', 'sort_order' => 40, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_NIVEAU_ETUDE, 'label' => 'Bac +4', 'sort_order' => 50, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_NIVEAU_ETUDE, 'label' => 'Bac +5', 'sort_order' => 60, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => PersonnelReferenceOption::TYPE_NIVEAU_ETUDE, 'label' => 'Doctorat', 'sort_order' => 70, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_reference_options');
    }
};