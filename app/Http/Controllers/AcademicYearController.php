<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use App\Models\AcademicCalendarPeriod;
use App\Models\AcademicYear;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AcademicYearController extends Controller
{
    public function index(): View
    {
        $academicYears = AcademicYear::query()
            ->withCount(['periods', 'schoolClasses'])
            ->orderByDesc('start_date')
            ->get();

        return view('academic-years.index', compact('academicYears'));
    }

    public function create(): View
    {
        return view('academic-years.create', [
            'periodTypeOptions' => AcademicCalendarPeriod::TYPE_OPTIONS,
        ]);
    }

    public function store(StoreAcademicYearRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $periods = $this->flattenPeriods($data['periods'] ?? []);
        unset($data['periods']);

        DB::transaction(function () use ($data, $periods): void {
            if (! empty($data['is_active'])) {
                AcademicYear::query()->update(['is_active' => false]);
            }

            $academicYear = AcademicYear::create($data);
            $savedPeriods = $this->syncPeriods($academicYear, $periods);

            if ($savedPeriods === 0) {
                throw ValidationException::withMessages([
                    'periods' => 'Ajoutez au moins une periode complete avant de sauvegarder.',
                ]);
            }
        });

        return redirect()->route('academic-years.index')->with('success', 'Annee scolaire ajoutee avec succes.');
    }

    public function show(AcademicYear $academicYear): View
    {
        $academicYear->load(['periods', 'schoolClasses.school']);

        return view('academic-years.show', compact('academicYear'));
    }

    public function edit(AcademicYear $academicYear): View
    {
        $academicYear->load('periods');

        return view('academic-years.edit', [
            'academicYear' => $academicYear,
            'periodTypeOptions' => AcademicCalendarPeriod::TYPE_OPTIONS,
        ]);
    }

    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        $data = $request->validated();
        $periods = $this->flattenPeriods($data['periods'] ?? []);
        unset($data['periods']);

        DB::transaction(function () use ($academicYear, $data, $periods): void {
            if (! empty($data['is_active'])) {
                AcademicYear::query()->whereKeyNot($academicYear->id)->update(['is_active' => false]);
            }

            $academicYear->update($data);
            $academicYear->periods()->delete();
            $savedPeriods = $this->syncPeriods($academicYear, $periods);

            if ($savedPeriods === 0) {
                throw ValidationException::withMessages([
                    'periods' => 'Ajoutez au moins une periode complete avant de sauvegarder.',
                ]);
            }
        });

        return redirect()->route('academic-years.index')->with('success', 'Annee scolaire mise a jour avec succes.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        $academicYear->delete();

        return redirect()->route('academic-years.index')->with('success', 'Annee scolaire supprimee avec succes.');
    }

    private function syncPeriods(AcademicYear $academicYear, array $periods): int
    {
        $savedCount = 0;

        foreach ($periods as $period) {
            if (empty($period['title']) || empty($period['type']) || empty($period['start_date']) || empty($period['end_date'])) {
                continue;
            }

            $academicYear->periods()->create([
                'title' => $period['title'],
                'type' => $period['type'],
                'start_date' => $period['start_date'],
                'end_date' => $period['end_date'],
                'notes' => $period['notes'] ?? null,
            ]);

            $savedCount++;
        }

        return $savedCount;
    }

    /**
     * @param array<string, array<int, array<string, mixed>>> $periods
     * @return array<int, array<string, mixed>>
     */
    private function flattenPeriods(array $periods): array
    {
        $flattened = [];

        foreach ($periods as $typeRows) {
            if (! is_array($typeRows)) {
                continue;
            }

            foreach ($typeRows as $period) {
                if (! is_array($period)) {
                    continue;
                }

                $flattened[] = $period;
            }
        }

        return $flattened;
    }
}