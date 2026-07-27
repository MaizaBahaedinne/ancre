<?php

namespace Database\Seeders;

use App\Models\Enfant;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\ParentModel;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SfaxDemoDataSeeder extends Seeder
{
    /**
     * Seed realistic demo data for Sfax region - no external dependencies.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Presence::truncate();
        Inscription::truncate();
        Paiement::truncate();
        Enfant::truncate();
        ParentModel::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Hard-coded Sfax parents data (15 parents)
        $parentsData = [
            ['nom' => 'Ben Ali', 'prenom' => 'Mohamed', 'email' => 'mohamed.benali@example.tn', 'telephone' => '21666123456', 'profession' => 'Ingénieur', 'sexe' => 'M'],
            ['nom' => 'Hmad', 'prenom' => 'Ahmed', 'email' => 'ahmed.hmad@example.tn', 'telephone' => '21666234567', 'profession' => 'Médecin', 'sexe' => 'M'],
            ['nom' => 'Kouki', 'prenom' => 'Ali', 'email' => 'ali.kouki@example.tn', 'telephone' => '21666345678', 'profession' => 'Avocat', 'sexe' => 'M'],
            ['nom' => 'Chihi', 'prenom' => 'Youssef', 'email' => 'youssef.chihi@example.tn', 'telephone' => '21666456789', 'profession' => 'Professeur', 'sexe' => 'M'],
            ['nom' => 'Turki', 'prenom' => 'Mahdi', 'email' => 'mahdi.turki@example.tn', 'telephone' => '21666567890', 'profession' => 'Commerçant', 'sexe' => 'M'],
            ['nom' => 'Dallali', 'prenom' => 'Sami', 'email' => 'sami.dallali@example.tn', 'telephone' => '21666678901', 'profession' => 'Employé', 'sexe' => 'M'],
            ['nom' => 'Ayouni', 'prenom' => 'Walid', 'email' => 'walid.ayouni@example.tn', 'telephone' => '21666789012', 'profession' => 'Technicien', 'sexe' => 'M'],
            ['nom' => 'Belkhiria', 'prenom' => 'Karim', 'email' => 'karim.belkhiria@example.tn', 'telephone' => '21666890123', 'profession' => 'Musicien', 'sexe' => 'M'],
            ['nom' => 'Jebali', 'prenom' => 'Nidhal', 'email' => 'nidhal.jebali@example.tn', 'telephone' => '21666901234', 'profession' => 'Artistique', 'sexe' => 'M'],
            ['nom' => 'Gaied', 'prenom' => 'Hatem', 'email' => 'hatem.gaied@example.tn', 'telephone' => '21667012345', 'profession' => 'Consultant', 'sexe' => 'M'],
            ['nom' => 'Ben Salah', 'prenom' => 'Amira', 'email' => 'amira.bensalah@example.tn', 'telephone' => '21667123456', 'profession' => 'Infirmière', 'sexe' => 'F'],
            ['nom' => 'Mansour', 'prenom' => 'Ines', 'email' => 'ines.mansour@example.tn', 'telephone' => '21667234567', 'profession' => 'Directrice', 'sexe' => 'F'],
            ['nom' => 'Khaled', 'prenom' => 'Mariem', 'email' => 'mariem.khaled@example.tn', 'telephone' => '21667345678', 'profession' => 'Comptable', 'sexe' => 'F'],
            ['nom' => 'Saïdi', 'prenom' => 'Asma', 'email' => 'asma.saidi@example.tn', 'telephone' => '21667456789', 'profession' => 'Traductrice', 'sexe' => 'F'],
            ['nom' => 'Ridha', 'prenom' => 'Rania', 'email' => 'rania.ridha@example.tn', 'telephone' => '21667567890', 'profession' => 'Pharmacienne', 'sexe' => 'F'],
        ];

        $parents = [];
        foreach ($parentsData as $data) {
            $parents[] = ParentModel::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'adresse' => 'Sfax, Tunisie',
                'adresse_ville' => 'Sfax',
                'adresse_gouvernorat' => 'Sfax',
                'profession' => $data['profession'],
                'sexe' => $data['sexe'],
            ]);
        }

        // Hard-coded children data (25 children)
        $childrenData = [
            ['nom' => 'Ben Ali', 'prenom' => 'Karim', 'dob' => '2021-03-15', 'sexe' => 'M', 'allergie' => null],
            ['nom' => 'Ben Ali', 'prenom' => 'Leila', 'dob' => '2021-06-20', 'sexe' => 'F', 'allergie' => null],
            ['nom' => 'Hmad', 'prenom' => 'Youssef', 'dob' => '2021-09-10', 'sexe' => 'M', 'allergie' => 'Arachides'],
            ['nom' => 'Hmad', 'prenom' => 'Nour', 'dob' => '2022-01-05', 'sexe' => 'F', 'allergie' => null],
            ['nom' => 'Kouki', 'prenom' => 'Ali', 'dob' => '2021-04-12', 'sexe' => 'M', 'allergie' => null],
            ['nom' => 'Kouki', 'prenom' => 'Sarra', 'dob' => '2021-07-18', 'sexe' => 'F', 'allergie' => 'Œufs'],
            ['nom' => 'Chihi', 'prenom' => 'Mahdi', 'dob' => '2021-11-22', 'sexe' => 'M', 'allergie' => null],
            ['nom' => 'Chihi', 'prenom' => 'Lina', 'dob' => '2022-02-28', 'sexe' => 'F', 'allergie' => null],
            ['nom' => 'Turki', 'prenom' => 'Sami', 'dob' => '2021-05-08', 'sexe' => 'M', 'allergie' => 'Lait'],
            ['nom' => 'Turki', 'prenom' => 'Yosra', 'dob' => '2021-08-14', 'sexe' => 'F', 'allergie' => null],
            ['nom' => 'Dallali', 'prenom' => 'Walid', 'dob' => '2021-10-30', 'sexe' => 'M', 'allergie' => null],
            ['nom' => 'Dallali', 'prenom' => 'Chaima', 'dob' => '2022-03-12', 'sexe' => 'F', 'allergie' => null],
            ['nom' => 'Ayouni', 'prenom' => 'Karim', 'dob' => '2021-02-19', 'sexe' => 'M', 'allergie' => null],
            ['nom' => 'Ayouni', 'prenom' => 'Ikram', 'dob' => '2021-09-25', 'sexe' => 'F', 'allergie' => null],
            ['nom' => 'Belkhiria', 'prenom' => 'Nidhal', 'dob' => '2021-06-07', 'sexe' => 'M', 'allergie' => 'Fruits de mer'],
            ['nom' => 'Belkhiria', 'prenom' => 'Hela', 'dob' => '2021-12-11', 'sexe' => 'F', 'allergie' => null],
            ['nom' => 'Jebali', 'prenom' => 'Hatem', 'dob' => '2021-04-23', 'sexe' => 'M', 'allergie' => null],
            ['nom' => 'Jebali', 'prenom' => 'Rim', 'dob' => '2021-11-03', 'sexe' => 'F', 'allergie' => null],
            ['nom' => 'Gaied', 'prenom' => 'Anis', 'dob' => '2021-07-29', 'sexe' => 'M', 'allergie' => null],
            ['nom' => 'Gaied', 'prenom' => 'Dorra', 'dob' => '2022-01-17', 'sexe' => 'F', 'allergie' => null],
            ['nom' => 'Ben Salah', 'prenom' => 'Riadh', 'dob' => '2021-08-06', 'sexe' => 'M', 'allergie' => null],
            ['nom' => 'Mansour', 'prenom' => 'Mouna', 'dob' => '2021-05-15', 'sexe' => 'F', 'allergie' => null],
            ['nom' => 'Khaled', 'prenom' => 'Skander', 'dob' => '2021-10-21', 'sexe' => 'M', 'allergie' => null],
            ['nom' => 'Saïdi', 'prenom' => 'Moez', 'dob' => '2021-03-09', 'sexe' => 'M', 'allergie' => null],
            ['nom' => 'Ridha', 'prenom' => 'Sabrine', 'dob' => '2021-12-27', 'sexe' => 'F', 'allergie' => null],
        ];

        $children = [];
        foreach ($childrenData as $data) {
            $children[] = Enfant::create([
                'parent_id' => $parents[array_rand($parents)]->id,
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'date_naissance' => $data['dob'],
                'sexe' => $data['sexe'],
                'has_allergie' => !is_null($data['allergie']),
                'allergie_options' => $data['allergie'] ? [$data['allergie']] : [],
                'allergies' => $data['allergie'],
                'observations' => $data['allergie'] ? "Attention: enfant allergique à {$data['allergie']}" : null,
            ]);
        }

        // Create inscriptions (80% of children)
        $classes = ['Petite section', 'Moyenne section', 'Grande section'];
        $packages = \App\Models\Package::where('is_active', true)->pluck('id')->all();
        
        if (!empty($packages)) {
            foreach (array_slice($children, 0, intval(count($children) * 0.8)) as $child) {
                Inscription::create([
                    'enfant_id' => $child->id,
                    'package_id' => $packages[array_rand($packages)],
                    'annual_registration_fee' => rand(50, 150),
                    'package_monthly_total' => rand(100, 300),
                    'total_amount' => rand(500, 1500),
                    'annee_scolaire' => '2026-2027',
                    'date_inscription' => Carbon::now()->subDays(rand(1, 90))->format('Y-m-d'),
                    'type_garde' => ['Matin', 'Apres-midi', 'Journee complete'][array_rand(['Matin', 'Apres-midi', 'Journee complete'])],
                    'statut' => ['Active', 'Renouvelee'][array_rand(['Active', 'Renouvelee'])],
                    'classe' => $classes[array_rand($classes)],
                    'school_class_id' => null,
                ]);
            }
        }

        // Create presences (avoid duplicates with date unique constraint)
        $now = Carbon::now();
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();
        $createdPresences = [];

        for ($i = 0; $i < 30; $i++) {
            if (empty($children)) break;
            
            $child = $children[array_rand($children)];
            $presenceDate = $startDate->copy()->addDays(rand(0, $startDate->diffInDays($endDate)))->format('Y-m-d');
            $key = "{$child->id}-{$presenceDate}";
            
            if (!isset($createdPresences[$key])) {
                Presence::create([
                    'enfant_id' => $child->id,
                    'date' => $presenceDate,
                    'heure_arrivee' => sprintf('%02d:%02d:00', rand(7, 9), rand(0, 59)),
                    'heure_depart' => sprintf('%02d:%02d:00', rand(16, 18), rand(0, 59)),
                ]);
                $createdPresences[$key] = true;
            }
        }

        // Create payments (random children)
        foreach (array_slice($children, 0, rand(5, 10)) as $child) {
            Paiement::create([
                'enfant_id' => $child->id,
                'montant' => rand(50, 300),
                'date_paiement' => Carbon::now()->subDays(rand(1, 60))->format('Y-m-d H:i:s'),
                'mode_paiement' => ['Especes', 'Cheque', 'Virement'][array_rand(['Especes', 'Cheque', 'Virement'])],
                'mois' => rand(1, 12),
                'annee' => 2026,
            ]);
        }

        $this->command->info('✅ Sfax demo data seeded successfully (without Faker)!');
    }
}

