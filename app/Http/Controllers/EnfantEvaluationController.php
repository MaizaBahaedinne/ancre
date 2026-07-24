<?php

namespace App\Http\Controllers;

use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\Enfant;
use App\Models\EnfantEvaluation;
use App\Models\Inscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

        $this->upsertEvaluation($enfant, $validated);

        return redirect()
            ->route('enfants.show', $enfant)
            ->with('success', 'Bulletin trimestriel enregistre avec succes.');
    }

    public function upsertByInscription(Request $request, Inscription $inscription): RedirectResponse
    {
        $inscription->load('enfant.schoolClass');

        if (! $inscription->enfant) {
            return back()->withErrors([
                'evaluations' => 'Aucun enfant n\'est rattache a cette inscription.',
            ])->withInput();
        }

        $academicYear = AcademicYear::query()
            ->where('label', $inscription->annee_scolaire)
            ->first();

        if (! $academicYear) {
            return back()->withErrors([
                'evaluations' => 'L\'annee scolaire de cette inscription n\'existe pas dans la table des annees scolaires.',
            ])->withInput();
        }

        $validated = $request->validate([
            'trimester' => ['required', 'in:'.implode(',', EnfantEvaluation::TRIMESTER_OPTIONS)],
            'general_average' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'class_rank' => ['nullable', 'integer', 'min:1', 'max:200'],
            'bulletin_received_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'grades' => ['nullable', 'array'],
            'grades.*' => ['nullable', 'numeric', 'min:0', 'max:20'],
        ]);

        $validated['academic_year_id'] = $academicYear->id;

        $this->upsertEvaluation($inscription->enfant, $validated);

        return redirect()
            ->route('inscriptions.show', $inscription)
            ->with('success', 'Bulletin trimestriel enregistre avec succes.');
    }

    private function upsertEvaluation(Enfant $enfant, array $validated): void
    {
        $level = $enfant->schoolClass?->level ?: $enfant->classe;

        if (! $level) {
            throw ValidationException::withMessages([
                'evaluations' => 'Le niveau de l\'enfant est introuvable. Assignez une classe ou un niveau avant la saisie du bulletin.',
            ]);
        }

        $subjects = AcademicSubject::query()
            ->where('is_active', true)
            ->get();

        $normalizedCurrentLevel = $this->normalizeLevelLabel($level);

        $subjectsForLevel = $subjects->filter(
            fn (AcademicSubject $subject) => $this->normalizeLevelLabel($subject->level) === $normalizedCurrentLevel
        )->values();

        if ($subjectsForLevel->isEmpty() && preg_match('/\d+/', $normalizedCurrentLevel, $matches)) {
            $targetYear = $matches[0];

            $subjectsForLevel = $subjects->filter(
                fn (AcademicSubject $subject) => preg_match('/\d+/', $this->normalizeLevelLabel($subject->level), $m) && ($m[0] ?? null) === $targetYear
            )->values();
        }

        $subjectIds = $subjectsForLevel->pluck('id')->all();

        if (empty($subjectIds)) {
            throw ValidationException::withMessages([
                'evaluations' => 'Aucune matiere active n\'est configuree pour ce niveau.',
            ]);
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
    }

    private function normalizeLevelLabel(?string $level): string
    {
        $value = Str::ascii((string) $level);
        $value = mb_strtolower($value, 'UTF-8');

        return preg_replace('/\s+/', ' ', trim($value)) ?: '';
    }
}
