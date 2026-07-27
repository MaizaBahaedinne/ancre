<?php

namespace Database\Seeders;

use App\Models\Activite;
use App\Models\Enfant;
use App\Models\EnfantActivite;
use App\Models\Incident;
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
     * Seed realistic demo data for Sfax region.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Presence::truncate();
        EnfantActivite::truncate();
        Incident::truncate();
        Paiement::truncate();
        Inscription::truncate();
        Enfant::truncate();
        ParentModel::truncate();
        Activite::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Sfax-specific Tunisian names
        $maleFirstNames = ['Mohamed', 'Ahmed', 'Ali', 'Youssef', 'Mahdi', 'Sami', 'Walid', 'Karim', 'Nidhal', 'Hatem'];
        $femaleFirstNames = ['Amira', 'Ines', 'Mariem', 'Asma', 'Rania', 'Sarra', 'Nour', 'Lina', 'Yosra', 'Chaima'];
        $lastNames = ['Ben Ali', 'Hmad', 'Kouki', 'Chihi', 'Turki', 'Dallali', 'Ayouni', 'Belkhiria', 'Jebali', 'Gaied'];

        // Create 15 parents (smaller, more manageable dataset)
        $parents = [];
        for ($i = 0; $i < 15; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $firstName = $faker->randomElement($gender === 'male' ? $maleFirstNames : $femaleFirstNames);
            $lastName = $faker->randomElement($lastNames);

            $parents[] = ParentModel::create([
                'nom' => $lastName,
                'prenom' => $firstName,
                'email' => strtolower("{$firstName}.{$lastName}.{$i}@example.tn"),
                'telephone' => $faker->numerify('2166########'),
                'adresse' => "Sfax, Tunisie",
                'adresse_ville' => 'Sfax',
                'adresse_gouvernorat' => 'Sfax',
                'profession' => $faker->jobTitle(),
                'sexe' => $gender === 'male' ? 'M' : 'F',
            ]);
        }

        // Create 25 children
        $children = [];
        for ($i = 0; $i < 25; $i++) {
            $gender = $faker->randomElement(['M', 'F']);
            $firstName = $gender === 'M' ? $faker->randomElement($maleFirstNames) : $faker->randomElement($femaleFirstNames);
            $lastName = $faker->randomElement($lastNames);

            $hasAllergie = $faker->boolean(30);
            $allergieOptions = [];
            if ($hasAllergie) {
                $allergieOptions = $faker->randomElements(['Arachides', 'Fruits de mer', 'Œufs', 'Lait', 'Gluten'], random_int(1, 3));
            }

            $children[] = Enfant::create([
                'parent_id' => $faker->randomElement($parents)->id,
                'nom' => $lastName,
                'prenom' => $firstName,
                'date_naissance' => $faker->dateTimeBetween('-5 years', '-2 years'),
                'sexe' => $gender,
                'has_allergie' => $hasAllergie,
                'allergie_options' => $allergieOptions,
                'allergies' => $hasAllergie ? implode(', ', $allergieOptions) : null,
                'observations' => $hasAllergie ? "Attention: enfant allergique" : null,
            ]);
        }

        // Create inscriptions for children (only if packages exist)
        $classes = ['Petite section', 'Moyenne section', 'Grande section'];
        $packages = \App\Models\Package::where('is_active', true)->pluck('id')->all();
        $now = Carbon::now();

        if (!empty($packages)) {
            foreach ($children as $child) {
                if ($faker->boolean(80)) { // 80% enrolled
                    Inscription::create([
                        'enfant_id' => $child->id,
                        'package_id' => $faker->randomElement($packages),
                        'annual_registration_fee' => $faker->randomFloat(2, 50, 150),
                        'package_monthly_total' => $faker->randomFloat(2, 100, 300),
                        'total_amount' => $faker->randomFloat(2, 500, 1500),
                        'annee_scolaire' => '2026-2027',
                        'date_inscription' => $faker->dateTimeBetween('-3 months', 'now'),
                        'type_garde' => $faker->randomElement(['Matin', 'Apres-midi', 'Journee complete']),
                        'statut' => $faker->randomElement(['Active', 'Renouvelee']),
                        'classe' => $faker->randomElement($classes),
                        'school_class_id' => null,
                    ]);
                }
            }
        }

        // Create some activities (optional)
        // Skipped - activities require complex relationships and personnel

        // Create presences for the current month
        $enfantIds = Enfant::pluck('id')->all();
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();

        for ($i = 0; $i < 50; $i++) {
            $presenceDate = $faker->dateTimeBetween($startDate, $endDate);
            if (!empty($enfantIds)) {
                Presence::create([
                    'enfant_id' => $faker->randomElement($enfantIds),
                    'date' => $presenceDate,
                    'heure_arrivee' => $faker->time(),
                    'heure_depart' => $faker->time(),
                ]);
            }
        }

        // Create some payments
        for ($i = 0; $i < 20; $i++) {
            if (!empty($children)) {
                $child = $faker->randomElement($children);
                $paymentDate = $faker->dateTimeBetween('-2 months', 'now');
                Paiement::create([
                    'enfant_id' => $child->id,
                    'montant' => $faker->randomFloat(2, 50, 300),
                    'date_paiement' => $paymentDate,
                    'mode_paiement' => $faker->randomElement(['Especes', 'Cheque', 'Virement']),
                    'mois' => $paymentDate->format('m'),
                    'annee' => $paymentDate->format('Y'),
                ]);
            }
        }

        $this->command->info('✅ Sfax demo data seeded successfully!');
    }
}
