<?php

namespace Database\Seeders;

use App\Models\AcademicSubject;
use Illuminate\Database\Seeder;

class AcademicSubjectsPrimarySeeder extends Seeder
{
    /**
     * Seed primary-level subjects from 1st to 6th year.
     */
    public function run(): void
    {
        $levels = AcademicSubject::LEVEL_OPTIONS;

        $subjectsByLevel = [
            $levels[0] => [
                'Arabe',
                'Mathematiques',
                'Eveil scientifique',
                'Technologie',
                'Education islamique',
                'Education artistique',
                'Education musicale',
                'Education physique',
            ],
            $levels[1] => [
                'Arabe',
                'Francais',
                'Mathematiques',
                'Eveil scientifique',
                'Technologie',
                'Education islamique',
                'Arts',
                'Musique',
                'EPS',
            ],
            $levels[2] => [
                'Arabe',
                'Francais',
                'Mathematiques',
                'Sciences naturelles',
                'Technologie',
                'Education islamique',
                'Arts',
                'Musique',
                'EPS',
            ],
            $levels[3] => [
                'Arabe',
                'Francais',
                'Mathematiques',
                'Sciences',
                'Technologie',
                'Education islamique',
                'Histoire-Geographie',
                'Arts',
                'Musique',
                'EPS',
                'Anglais',
            ],
            $levels[4] => [
                'Arabe',
                'Francais',
                'Mathematiques',
                'Sciences',
                'Sciences sociales',
                'Technologie',
                'Education islamique',
                'Arts',
                'Musique',
                'EPS',
                'Anglais',
            ],
            $levels[5] => [
                'Arabe',
                'Francais',
                'Mathematiques',
                'Sciences',
                'Histoire',
                'Geographie',
                'Technologie',
                'Education islamique',
                'Arts',
                'Musique',
                'EPS',
                'Anglais',
            ],
        ];

        foreach ($subjectsByLevel as $level => $subjects) {
            foreach ($subjects as $subjectName) {
                AcademicSubject::updateOrCreate(
                    [
                        'name' => $subjectName,
                        'level' => $level,
                    ],
                    [
                        'default_coefficient' => 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
