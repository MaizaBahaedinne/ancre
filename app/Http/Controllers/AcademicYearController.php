<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use App\Models\AcademicCalendarPeriod;
use App\Models\AcademicYear;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

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
        $periods = $data['periods'] ?? [];
        unset($data['periods']);

        if (! empty($data['is_active'])) {
            AcademicYear::query()->update(['is_active' => false]);
        }

        $academicYear = AcademicYear::create($data);
        $this->syncPeriods($academicYear, $periods);

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
        $periods = $data['periods'] ?? [];
        unset($data['periods']);

        if (! empty($data['is_active'])) {
            AcademicYear::query()->whereKeyNot($academicYear->id)->update(['is_active' => false]);
        }

        $academicYear->update($data);
        $academicYear->periods()->delete();
        $this->syncPeriods($academicYear, $periods);

        return redirect()->route('academic-years.index')->with('success', 'Annee scolaire mise a jour avec succes.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        $academicYear->delete();

        return redirect()->route('academic-years.index')->with('success', 'Annee scolaire supprimee avec succes.');
    }

    private function syncPeriods(AcademicYear $academicYear, array $periods): void
    {
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
        }
    }
}