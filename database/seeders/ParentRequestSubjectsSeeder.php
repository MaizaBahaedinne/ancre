<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParentRequestSubjectsSeeder extends Seeder
{
    /**
     * Seed parent request subjects for demandes and reclamations.
     */
    public function run(): void
    {
        $now = now();

        $subjects = [
            // Demandes
            ['action_type' => 'demande', 'label' => 'Demande de rendez-vous avec l\'administration', 'sort_order' => 10],
            ['action_type' => 'demande', 'label' => 'Demande d\'attestation de presence', 'sort_order' => 20],
            ['action_type' => 'demande', 'label' => 'Demande d\'information sur inscription', 'sort_order' => 30],
            ['action_type' => 'demande', 'label' => 'Demande de changement d\'horaire', 'sort_order' => 40],
            ['action_type' => 'demande', 'label' => 'Demande de changement de classe', 'sort_order' => 50],
            ['action_type' => 'demande', 'label' => 'Demande de menu dejeuner adapte', 'sort_order' => 60],
            ['action_type' => 'demande', 'label' => 'Demande d\'inscription a une activite', 'sort_order' => 70],
            ['action_type' => 'demande', 'label' => 'Demande de report de paiement', 'sort_order' => 80],
            ['action_type' => 'demande', 'label' => 'Demande de document administratif', 'sort_order' => 90],
            ['action_type' => 'demande', 'label' => 'Demande autre', 'sort_order' => 100],

            // Reclamations
            ['action_type' => 'reclamation', 'label' => 'Reclamation sur facturation', 'sort_order' => 10],
            ['action_type' => 'reclamation', 'label' => 'Reclamation sur paiement non pris en compte', 'sort_order' => 20],
            ['action_type' => 'reclamation', 'label' => 'Reclamation sur retard de traitement', 'sort_order' => 30],
            ['action_type' => 'reclamation', 'label' => 'Reclamation sur comportement', 'sort_order' => 40],
            ['action_type' => 'reclamation', 'label' => 'Reclamation sur suivi pedagogique', 'sort_order' => 50],
            ['action_type' => 'reclamation', 'label' => 'Reclamation sur dejeuner', 'sort_order' => 60],
            ['action_type' => 'reclamation', 'label' => 'Reclamation sur activites', 'sort_order' => 70],
            ['action_type' => 'reclamation', 'label' => 'Reclamation sur securite', 'sort_order' => 80],
            ['action_type' => 'reclamation', 'label' => 'Reclamation sur transport', 'sort_order' => 90],
            ['action_type' => 'reclamation', 'label' => 'Reclamation autre', 'sort_order' => 100],
        ];

        foreach ($subjects as $subject) {
            DB::table('parent_request_subjects')->updateOrInsert(
                [
                    'action_type' => $subject['action_type'],
                    'label' => $subject['label'],
                ],
                [
                    'is_active' => true,
                    'sort_order' => $subject['sort_order'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}