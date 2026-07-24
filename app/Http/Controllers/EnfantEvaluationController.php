<?php

namespace App\Http\Controllers;

use App\Models\AcademicSubject;
use App\Models\Enfant;
use App\Models\EnfantEvaluation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnfantEvaluationController extends Controller
{
    public function upsert(Request $request, Enfant $enfant): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'trimester' => ['required', 'in:'.implode(',', EnfantEvaluation::TRIMESTER_OPTIONS)],
            'general_average' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'class_rank' => ['nullable', 'integer', 'min:1', 'max:200'],
            'bulletin_received_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'grades' => ['nullable', 'array'],
            'grades.*' => ['nullable', 'numeric', 'min:0', 'max:20'],
        ]);

        $level = $enfant->schoolClass?->level ?: $enfant->classe;

        if (! $level) {
            return back()->withErrors([
                'evaluations' => 'Le niveau de l\'enfant est introuvable. Assignez une classe ou un niveau avant la saisie du bulletin.',
            ])->withInput();
        }

        $subjectIds = AcademicSubject::query()
            ->where('level', $level)
            ->where('is_active', true)
            ->pluck('id')
            ->all();

        if (empty($subjectIds)) {
            return back()->withErrors([
                'evaluations' => 'Aucune matiere active n\'est configuree pour ce niveau.',
            ])->withInput();
        }

        $gradesInput = collect($validated['grades'] ?? [])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value) => (float) $value);

        DB::transaction(function () use ($enfant, $validated, $gradesInput, $subjectIds): void {
            $evaluation = EnfantEvaluation::query()->updateOrCreate(
                [
                    'enfant_id' => $enfant->id,
                    'academic_year_id' => $validated['academic_year_id'],
                    'trimester' => $validated['trimester'],
                ],
                [
                    'school_class_id' => $enfant->school_class_id,
                    'general_average' => $validated['general_average'] ?? null,
                    'class_rank' => $validated['class_rank'] ?? null,
                    'bulletin_received_at' => $validated['bulletin_received_at'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            $evaluation->grades()->delete();

            $weightedSum = 0.0;
            $coefficientSum = 0.0;

            $subjects = AcademicSubject::query()
                ->whereIn('id', $subjectIds)
                ->get()
                ->keyBy('id');

            foreach ($gradesInput as $subjectId => $gradeValue) {
                $subjectId = (int) $subjectId;

                if (! isset($subjects[$subjectId])) {
                    continue;
                }

                $coefficient = (float) $subjects[$subjectId]->default_coefficient;

                $evaluation->grades()->create([
                    'academic_subject_id' => $subjectId,
                    'grade' => $gradeValue,
                    'coefficient' => $coefficient,
                ]);

                $weightedSum += $gradeValue * $coefficient;
                $coefficientSum += $coefficient;
            }

            if (! isset($validated['general_average']) && $coefficientSum > 0) {
                $evaluation->update([
                    'general_average' => round($weightedSum / $coefficientSum, 2),
                ]);
            }
        });

        return redirect()
            ->route('enfants.show', $enfant)
            ->with('success', 'Bulletin trimestriel enregistre avec succes.');
    }
}
