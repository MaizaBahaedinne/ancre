<?php

namespace Database\Seeders;

use App\Models\Activite;
use App\Models\Enfant;
use App\Models\EnfantActivite;
use App\Models\Incident;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\ParentModel;
use App\Models\Personnel;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed realistic demo data for platform testing.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Presence::truncate();
        EnfantActivite::truncate();
        Incident::truncate();
        Paiement::truncate();
        Inscription::truncate();
        Enfant::truncate();
        ParentModel::truncate();
        Personnel::truncate();
        Activite::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $faker = fake('fr_FR');

        $maleFirstNames = [
            'Mohamed', 'Ahmed', 'Ali', 'Youssef', 'Mahdi', 'Sami', 'Walid', 'Karim',
            'Nidhal', 'Hatem', 'Oussama', 'Anis', 'Riadh', 'Nizar', 'Aymen', 'Hamza',
            'Skander', 'Moez', 'Marouane', 'Ghassen',
        ];
        $femaleFirstNames = [
            'Amira', 'Ines', 'Mariem', 'Asma', 'Rania', 'Sarra', 'Nour', 'Lina',
            'Yosra', 'Chaima', 'Ikram', 'Hela', 'Rim', 'Dorra', 'Mouna', 'Sabrine',
            'Malek', 'Aicha', 'Rahma', 'Soumaya',
        ];
        $lastNames = [
            'Ben Salah', 'Trabelsi', 'Gharbi', 'Jaziri', 'Ben Ali', 'Masmoudi',
            'Ben Hassen', 'Khelifi', 'Ayari', 'Haddad', 'Mabrouk', 'Chaabane',
            'Ben Amor', 'Karray', 'Ben Youssef', 'Sassi', 'Bouzid', 'Mekki',
            'Dridi', 'Kacem',
        ];
        $cities = [
            'Tunis', 'Sfax', 'Sousse', 'Nabeul', 'Bizerte', 'Kairouan',
            'Monastir', 'Mahdia', 'Gabes', 'Ariana',
        ];
        $professions = [
            'Enseignant', 'Commercant', 'Infirmier', 'Ingenieur', 'Chauffeur',
            'Fonctionnaire', 'Artisan', 'Avocat', 'Pharmacien', 'Technicien',
        ];
        $fullName = function () use ($maleFirstNames, $femaleFirstNames, $lastNames, $faker): string {
            $firstPool = $faker->boolean() ? $maleFirstNames : $femaleFirstNames;

            return $faker->randomElement($firstPool).' '.$faker->randomElement($lastNames);
        };

        $parents = collect();
        for ($i = 0; $i < 25; $i++) {
            $isMale = $faker->boolean();
            $parents->push(ParentModel::create([
                'nom' => $faker->randomElement($lastNames),
                'prenom' => $faker->randomElement($isMale ? $maleFirstNames : $femaleFirstNames),
                'telephone' => $faker->numerify('2#######'),
                'email' => 'parent'.($i + 1).'@demo.ancredeselites.tn',
                'adresse' => $faker->randomElement($cities).', '.$faker->numerify('Rue ####'),
                'profession' => $faker->randomElement($professions),
                'contact_urgence' => $faker->numerify('9#######'),
            ]));
        }

        $enfants = collect();
        $classes = ['Petite section', 'Moyenne section', 'Grande section', 'Preparation'];
        for ($i = 0; $i < 40; $i++) {
            $parent = $parents->random();
            $isMale = $faker->boolean();
            $enfants->push(Enfant::create([
                'parent_id' => $parent->id,
                'nom' => $faker->randomElement($lastNames),
                'prenom' => $faker->randomElement($isMale ? $maleFirstNames : $femaleFirstNames),
                'date_naissance' => $faker->dateTimeBetween('-6 years', '-2 years')->format('Y-m-d'),
                'sexe' => $faker->randomElement(['M', 'F']),
                'classe' => $faker->randomElement($classes),
                'allergies' => $faker->boolean(25) ? $faker->randomElement(['Aucune', 'Arachides', 'Lactose', 'Pollen']) : null,
                'observations' => $faker->boolean(35) ? $faker->sentence() : null,
            ]));
        }

        $currentYear = (int) now()->format('Y');
        $schoolYears = [
            ($currentYear - 1).'/'.$currentYear,
            $currentYear.'/'.($currentYear + 1),
        ];

        foreach ($enfants as $enfant) {
            Inscription::create([
                'enfant_id' => $enfant->id,
                'annee_scolaire' => $faker->randomElement($schoolYears),
                'date_inscription' => $faker->dateTimeBetween('-10 months', 'now')->format('Y-m-d'),
                'type_garde' => $faker->randomElement(['Matin', 'Apres-midi', 'Journee complete']),
                'statut' => $faker->randomElement(['Active', 'Renouvelee', 'Suspendue']),
            ]);
        }

        foreach ($enfants as $enfant) {
            $daysCount = $faker->numberBetween(8, 18);
            $dates = collect(range(1, 60))
                ->shuffle()
                ->take($daysCount)
                ->map(fn (int $offset) => Carbon::today()->subDays($offset)->toDateString())
                ->unique();

            foreach ($dates as $date) {
                $arrivee = Carbon::createFromTime(7, 30)->addMinutes($faker->numberBetween(0, 90));
                $depart = Carbon::createFromTime(15, 30)->addMinutes($faker->numberBetween(0, 120));

                Presence::create([
                    'enfant_id' => $enfant->id,
                    'date' => $date,
                    'heure_arrivee' => $arrivee->format('H:i'),
                    'heure_depart' => $faker->boolean(85) ? $depart->format('H:i') : null,
                    'personne_depot' => $fullName(),
                    'personne_retrait' => $faker->boolean(80) ? $fullName() : null,
                    'remarque' => $faker->boolean(20) ? $faker->sentence() : null,
                ]);
            }
        }

        foreach ($enfants as $enfant) {
            for ($m = 0; $m < 6; $m++) {
                $monthDate = now()->copy()->startOfMonth()->subMonths($m);
                $status = $faker->randomElement(['Paye', 'Paye', 'Paye', 'Partiel', 'En retard']);
                $amount = match ($status) {
                    'Partiel' => $faker->randomFloat(2, 80, 180),
                    'En retard' => $faker->randomFloat(2, 0, 50),
                    default => $faker->randomFloat(2, 160, 280),
                };

                $paiement = Paiement::create([
                    'enfant_id' => $enfant->id,
                    'montant' => $amount,
                    'date_paiement' => $monthDate->copy()->addDays($faker->numberBetween(1, 25))->format('Y-m-d'),
                    'mois' => (int) $monthDate->month,
                    'annee' => (int) $monthDate->year,
                    'mode_paiement' => $faker->randomElement(['Especes', 'Carte', 'Virement', 'Cheque']),
                    'statut' => $status,
                    'commentaire' => $faker->boolean(30) ? $faker->sentence() : null,
                    'reference' => null,
                ]);

                $paiement->update([
                    'reference' => sprintf('PAY-%s-%06d', $paiement->annee, $paiement->id),
                ]);
            }
        }

        $fonctions = ['Educatrice', 'Assistante', 'Responsable administratif', 'Agent de securite', 'Psychologue'];
        for ($i = 0; $i < 14; $i++) {
            $isMale = $faker->boolean();
            Personnel::create([
                'nom' => $faker->randomElement($lastNames),
                'prenom' => $faker->randomElement($isMale ? $maleFirstNames : $femaleFirstNames),
                'fonction' => $faker->randomElement($fonctions),
                'telephone' => $faker->numerify('5#######'),
                'email' => 'personnel'.($i + 1).'@demo.ancredeselites.tn',
                'date_embauche' => $faker->dateTimeBetween('-4 years', '-2 months')->format('Y-m-d'),
            ]);
        }

        $titres = [
            'Atelier dessin',
            'Jeux de motricite',
            'Conte collectif',
            'Initiation musique',
            'Jardinage',
            'Atelier langues',
            'Jeu cooperatif',
        ];

        for ($i = 0; $i < 28; $i++) {
            Activite::create([
                'titre' => $faker->randomElement($titres),
                'description' => $faker->sentence(12),
                'date' => $faker->dateTimeBetween('-2 months', '+2 months')->format('Y-m-d'),
                'heure' => Carbon::createFromTime($faker->numberBetween(8, 16), $faker->randomElement([0, 15, 30, 45]))->format('H:i'),
                'responsable' => $fullName(),
            ]);
        }

        $activites = Activite::all();
        foreach ($enfants as $enfant) {
            foreach ($activites->random($faker->numberBetween(8, 16)) as $activite) {
                EnfantActivite::firstOrCreate(
                    [
                        'enfant_id' => $enfant->id,
                        'activite_id' => $activite->id,
                    ],
                    [
                        'statut' => $faker->randomElement(['Present', 'Present', 'Present', 'Absent']),
                        'remarque' => $faker->boolean(20) ? $faker->sentence() : null,
                    ]
                );
            }
        }

        $incidentTypes = ['Blessure legere', 'Fievre', 'Conflit', 'Retard recup', 'Allergie'];
        foreach ($enfants->random(18) as $enfant) {
            Incident::create([
                'enfant_id' => $enfant->id,
                'date' => $faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
                'type_incident' => $faker->randomElement($incidentTypes),
                'description' => $faker->sentence(14),
                'action_realisee' => $faker->sentence(10),
            ]);
        }
    }
}
