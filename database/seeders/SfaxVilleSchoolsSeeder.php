<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SfaxVilleSchoolsSeeder extends Seeder
{
    /**
     * Seed Sfax Ville schools.
     */
    public function run(): void
    {
        $schools = [
            [
                'name' => 'Al Moustakbel',
                'address_route' => "Route de l'Aeroport Km 3",
                'address_street' => null,
                'address_postal_code' => '3000',
                'address_city' => 'Sfax Ville',
                'address_governorate' => 'Sfax',
                'city' => 'Sfax Ville',
                'phone' => null,
                'director_name' => null,
                'director_contact' => null,
            ],
            [
                'name' => 'Amal Junior',
                'address_route' => 'Route Teniour Km 3',
                'address_street' => 'Rue Abdelaziz Thaalbi',
                'address_postal_code' => '3062',
                'address_city' => 'Sfax Ville',
                'address_governorate' => 'Sfax',
                'city' => 'Sfax Ville',
                'phone' => null,
                'director_name' => null,
                'director_contact' => null,
            ],
            [
                'name' => 'Nour El Maaref',
                'address_route' => null,
                'address_street' => '16 Rue El-Bousten',
                'address_postal_code' => '3000',
                'address_city' => 'Sfax Ville',
                'address_governorate' => 'Sfax',
                'city' => 'Sfax Ville',
                'phone' => null,
                'director_name' => null,
                'director_contact' => null,
            ],
            [
                'name' => 'Izdihar School',
                'address_route' => 'Route Manzel Cheker Km 1',
                'address_street' => 'Devant Lycee 20 Mars',
                'address_postal_code' => '3000',
                'address_city' => 'Sfax Ville',
                'address_governorate' => 'Sfax',
                'city' => 'Sfax Ville',
                'phone' => null,
                'director_name' => null,
                'director_contact' => null,
            ],
            [
                'name' => 'Nova School',
                'address_route' => 'Route de Gremda Km 2.5',
                'address_street' => 'Pres Imm. Residence Baya',
                'address_postal_code' => null,
                'address_city' => 'Sfax Ville',
                'address_governorate' => 'Sfax',
                'city' => 'Sfax Ville',
                'phone' => null,
                'director_name' => null,
                'director_contact' => null,
            ],
            [
                'name' => 'La Coupole',
                'address_route' => 'Route Menzel Chaker Km 2.5',
                'address_street' => null,
                'address_postal_code' => '3018',
                'address_city' => 'Sfax Ville',
                'address_governorate' => 'Sfax',
                'city' => 'Sfax Ville',
                'phone' => null,
                'director_name' => null,
                'director_contact' => null,
            ],
            [
                'name' => 'Anouar',
                'address_route' => 'Route Menzel Chaker Km 1.5',
                'address_street' => null,
                'address_postal_code' => '3003',
                'address_city' => 'Sfax Ville',
                'address_governorate' => 'Sfax',
                'city' => 'Sfax Ville',
                'phone' => null,
                'director_name' => null,
                'director_contact' => null,
            ],
        ];

        foreach ($schools as $school) {
            School::updateOrCreate(
                ['name' => $school['name']],
                $school,
            );
        }
    }
}