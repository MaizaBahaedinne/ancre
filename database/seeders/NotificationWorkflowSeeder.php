<?php

namespace Database\Seeders;

use App\Models\NotificationWorkflow;
use App\Models\NotificationReceiver;
use Illuminate\Database\Seeder;

class NotificationWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // FAMILLE - Parent inscrit
        $workflow = NotificationWorkflow::create([
            'trigger' => 'parent.created',
            'name' => 'Nouveau parent inscrit',
            'description' => 'Notifier admin et responsable lors de l\'inscription d\'un nouveau parent',
            'is_enabled' => true,
            'module' => 'family',
        ]);

        NotificationReceiver::create([
            'workflow_id' => $workflow->id,
            'receiver_type' => 'role',
            'receiver_value' => 'Administrateur',
            'notification_medium' => 'all',
            'is_enabled' => true,
        ]);

        NotificationReceiver::create([
            'workflow_id' => $workflow->id,
            'receiver_type' => 'role',
            'receiver_value' => 'Responsable',
            'notification_medium' => 'email',
            'is_enabled' => true,
        ]);

        // FAMILLE - Enfant inscrit
        $workflow = NotificationWorkflow::create([
            'trigger' => 'child.registered',
            'name' => 'Enfant inscrit',
            'description' => 'Notifier les parents lors de l\'inscription d\'un enfant',
            'is_enabled' => true,
            'module' => 'family',
        ]);

        // PRÉSENCES - Absence remarquée
        $workflow = NotificationWorkflow::create([
            'trigger' => 'presence.absent',
            'name' => 'Absence enfant',
            'description' => 'Notifier les parents lors d\'une absence non justifiée',
            'is_enabled' => true,
            'module' => 'presences',
        ]);

        NotificationReceiver::create([
            'workflow_id' => $workflow->id,
            'receiver_type' => 'role',
            'receiver_value' => 'Parent',
            'notification_medium' => 'sms',
            'is_enabled' => true,
        ]);

        // ACTIVITÉS - Nouvelle activité
        $workflow = NotificationWorkflow::create([
            'trigger' => 'activity.created',
            'name' => 'Nouvelle activité créée',
            'description' => 'Notifier les parents des nouvelles activités',
            'is_enabled' => true,
            'module' => 'activities',
        ]);

        // ÉVALUATIONS - Évaluation enfant
        $workflow = NotificationWorkflow::create([
            'trigger' => 'evaluation.created',
            'name' => 'Évaluation enfant',
            'description' => 'Notifier les parents d\'une nouvelle évaluation',
            'is_enabled' => true,
            'module' => 'activities',
        ]);

        NotificationReceiver::create([
            'workflow_id' => $workflow->id,
            'receiver_type' => 'role',
            'receiver_value' => 'Parent',
            'notification_medium' => 'email',
            'is_enabled' => true,
        ]);

        // INCIDENTS - Incident créé
        $workflow = NotificationWorkflow::create([
            'trigger' => 'incident.created',
            'name' => 'Incident créé',
            'description' => 'Notifier admin et responsable d\'un nouvel incident',
            'is_enabled' => true,
            'module' => 'incidents',
        ]);

        NotificationReceiver::create([
            'workflow_id' => $workflow->id,
            'receiver_type' => 'role',
            'receiver_value' => 'Administrateur',
            'notification_medium' => 'system',
            'is_enabled' => true,
        ]);

        // PAIEMENTS - Facture générée
        $workflow = NotificationWorkflow::create([
            'trigger' => 'invoice.generated',
            'name' => 'Facture générée',
            'description' => 'Notifier les parents d\'une nouvelle facture',
            'is_enabled' => true,
            'module' => 'payments',
        ]);

        NotificationReceiver::create([
            'workflow_id' => $workflow->id,
            'receiver_type' => 'role',
            'receiver_value' => 'Parent',
            'notification_medium' => 'all',
            'is_enabled' => true,
        ]);
    }
}
