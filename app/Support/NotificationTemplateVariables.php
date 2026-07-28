<?php

namespace App\Support;

class NotificationTemplateVariables
{
    /**
     * Return available placeholders for a given workflow trigger.
     *
     * @return array{generic:array<int,array{name:string,description:string}>,specific:array<int,array{name:string,description:string}>,relations:array<int,array{relation:string,variables:string}>}
     */
    public static function forTrigger(string $trigger): array
    {
        $generic = [
            ['name' => '{trigger}', 'description' => 'Nom technique du trigger (ex: parent.created)'],
            ['name' => '{workflow_name}', 'description' => 'Nom du workflow configure'],
            ['name' => '{subject}', 'description' => 'Sujet envoye par le payload (si fourni)'],
            ['name' => '{description}', 'description' => 'Description envoyee par le payload (si fournie)'],
        ];

        $specific = match ($trigger) {
            'parent.created' => [
                ['name' => '{parent_id}', 'description' => 'ID du parent cree'],
                ['name' => '{parent_nom}', 'description' => 'Nom du parent'],
                ['name' => '{parent_prenom}', 'description' => 'Prenom du parent'],
                ['name' => '{parent_full_name}', 'description' => 'Nom complet du parent'],
                ['name' => '{parent_email}', 'description' => 'Email du parent'],
                ['name' => '{parent_phone}', 'description' => 'Telephone du parent'],
                ['name' => '{parent_user_name}', 'description' => 'Nom du compte utilisateur lie au parent'],
                ['name' => '{parent_user_email}', 'description' => 'Email du compte utilisateur lie au parent'],
                ['name' => '{children_count}', 'description' => 'Nombre d\'enfants lies au parent'],
                ['name' => '{created_by_id}', 'description' => 'ID de l\'utilisateur createur'],
                ['name' => '{created_by_name}', 'description' => 'Nom de l\'utilisateur createur'],
                ['name' => '{created_by_email}', 'description' => 'Email de l\'utilisateur createur'],
            ],
            'school.created' => [
                ['name' => '{school_id}', 'description' => 'ID de l\'ecole creee'],
                ['name' => '{school_name}', 'description' => 'Nom de l\'ecole creee'],
                ['name' => '{created_by}', 'description' => 'ID de l\'utilisateur createur'],
                ['name' => '{created_by_name}', 'description' => 'Nom de l\'utilisateur createur'],
                ['name' => '{created_by_email}', 'description' => 'Email de l\'utilisateur createur'],
            ],
            default => [
                ['name' => '{metadata_key}', 'description' => 'Toute cle scalaire presente dans metadata sera disponible'],
            ],
        };

        $relations = match ($trigger) {
            'parent.created' => [
                ['relation' => 'parent.user', 'variables' => '{parent_user_name}, {parent_user_email}'],
                ['relation' => 'parent.enfants', 'variables' => '{children_count}'],
                ['relation' => 'acteur de creation', 'variables' => '{created_by_id}, {created_by_name}, {created_by_email}'],
            ],
            'school.created' => [
                ['relation' => 'acteur de creation', 'variables' => '{created_by}, {created_by_name}, {created_by_email}'],
            ],
            default => [
                ['relation' => 'payload.metadata', 'variables' => 'Toutes les cles scalaires sont exploitables en template'],
            ],
        };

        return [
            'generic' => $generic,
            'specific' => $specific,
            'relations' => $relations,
        ];
    }
}
