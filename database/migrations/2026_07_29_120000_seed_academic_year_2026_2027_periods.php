<?php

use App\Models\AcademicCalendarPeriod;
use App\Models\AcademicYear;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $academicYear = AcademicYear::query()->updateOrCreate(
                ['label' => '2026-2027'],
                [
                    'start_date' => '2026-09-01',
                    'end_date' => '2027-06-30',
                    'registration_fee' => 50,
                    'is_active' => true,
                ]
            );

            $academicYear->periods()->delete();

            $periods = [
                [
                    'title' => 'Vacances de rentree',
                    'type' => AcademicCalendarPeriod::TYPE_SCHOOL_VACATION,
                    'start_date' => '2026-08-28',
                    'end_date' => '2026-09-13',
                    'notes' => 'Preparation avant la reprise des cours.',
                ],
                [
                    'title' => 'Naissance du Prophete',
                    'type' => AcademicCalendarPeriod::TYPE_PUBLIC_HOLIDAY,
                    'start_date' => '2026-09-27',
                    'end_date' => '2026-09-27',
                    'notes' => 'Jour ferie national. Date a confirmer selon le calendrier officiel.',
                ],
                [
                    'title' => 'Jour de l evacuation',
                    'type' => AcademicCalendarPeriod::TYPE_PUBLIC_HOLIDAY,
                    'start_date' => '2026-10-15',
                    'end_date' => '2026-10-15',
                    'notes' => 'Jour ferie national en Tunisie.',
                ],
                [
                    'title' => 'Examen du premier trimestre',
                    'type' => AcademicCalendarPeriod::TYPE_THEORETICAL_EXAM,
                    'start_date' => '2026-11-30',
                    'end_date' => '2026-12-11',
                    'notes' => 'Evaluation du premier trimestre.',
                ],
                [
                    'title' => 'Vacances d hiver',
                    'type' => AcademicCalendarPeriod::TYPE_SCHOOL_VACATION,
                    'start_date' => '2026-12-19',
                    'end_date' => '2027-01-03',
                    'notes' => 'Pause hivernale.',
                ],
                [
                    'title' => 'Jour de la revolution et de la jeunesse',
                    'type' => AcademicCalendarPeriod::TYPE_PUBLIC_HOLIDAY,
                    'start_date' => '2027-01-14',
                    'end_date' => '2027-01-14',
                    'notes' => 'Jour ferie national en Tunisie.',
                ],
                [
                    'title' => 'Examen du deuxieme trimestre',
                    'type' => AcademicCalendarPeriod::TYPE_PRACTICAL_EXAM,
                    'start_date' => '2027-02-22',
                    'end_date' => '2027-03-05',
                    'notes' => 'Evaluation du deuxieme trimestre.',
                ],
                [
                    'title' => 'Jour de l independance',
                    'type' => AcademicCalendarPeriod::TYPE_PUBLIC_HOLIDAY,
                    'start_date' => '2027-03-20',
                    'end_date' => '2027-03-20',
                    'notes' => 'Jour ferie national en Tunisie.',
                ],
                [
                    'title' => 'Vacances de printemps',
                    'type' => AcademicCalendarPeriod::TYPE_SCHOOL_VACATION,
                    'start_date' => '2027-03-27',
                    'end_date' => '2027-04-11',
                    'notes' => 'Pause printaniere.',
                ],
                [
                    'title' => 'Jour des martyrs',
                    'type' => AcademicCalendarPeriod::TYPE_PUBLIC_HOLIDAY,
                    'start_date' => '2027-04-09',
                    'end_date' => '2027-04-09',
                    'notes' => 'Jour ferie national en Tunisie.',
                ],
                [
                    'title' => 'Fete du travail',
                    'type' => AcademicCalendarPeriod::TYPE_PUBLIC_HOLIDAY,
                    'start_date' => '2027-05-01',
                    'end_date' => '2027-05-01',
                    'notes' => 'Jour ferie national en Tunisie.',
                ],
                [
                    'title' => 'Examen de synthese',
                    'type' => AcademicCalendarPeriod::TYPE_SYNTHESIS_EXAM,
                    'start_date' => '2027-05-17',
                    'end_date' => '2027-05-28',
                    'notes' => 'Evaluation de fin d annee pour le primaire.',
                ],
            ];

            foreach ($periods as $period) {
                $academicYear->periods()->create($period);
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            $academicYear = AcademicYear::query()->where('label', '2026-2027')->first();

            if (! $academicYear) {
                return;
            }

            $academicYear->periods()->delete();
            $academicYear->delete();
        });
    }
};
