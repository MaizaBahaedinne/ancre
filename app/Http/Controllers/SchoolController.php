<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use App\Models\AcademicYear;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SchoolController extends Controller
{
    public function index(): View
    {
        $schools = School::query()
            ->withCount('classes')
            ->orderBy('name')
            ->get();

        return view('schools.index', compact('schools'));
    }

    public function create(): View
    {
        return view('schools.create', [
            'academicYears' => AcademicYear::query()->orderByDesc('start_date')->get(),
        ]);
    }

    public function store(StoreSchoolRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $classes = $data['classes'] ?? [];
        unset($data['classes']);

        $school = School::create($data);
        $this->syncClasses($school, $classes);

        return redirect()->route('schools.index')->with('success', 'Ecole ajoutee avec succes.');
    }

    public function show(School $school): View
    {
        $school->load(['classes.academicYear']);

        return view('schools.show', compact('school'));
    }

    public function edit(School $school): View
    {
        $school->load('classes');

        return view('schools.edit', [
            'school' => $school,
            'academicYears' => AcademicYear::query()->orderByDesc('start_date')->get(),
        ]);
    }

    public function update(UpdateSchoolRequest $request, School $school): RedirectResponse
    {
        $data = $request->validated();
        $classes = $data['classes'] ?? [];
        unset($data['classes']);

        $school->update($data);
        $school->classes()->delete();
        $this->syncClasses($school, $classes);

        return redirect()->route('schools.index')->with('success', 'Ecole mise a jour avec succes.');
    }

    public function destroy(School $school): RedirectResponse
    {
        $school->delete();

        return redirect()->route('schools.index')->with('success', 'Ecole supprimee avec succes.');
    }

    private function syncClasses(School $school, array $classes): void
    {
        foreach ($classes as $class) {
            if (empty($class['academic_year_id']) || empty($class['name'])) {
                continue;
            }

            $school->classes()->create([
                'academic_year_id' => $class['academic_year_id'],
                'name' => $class['name'],
                'level' => $class['level'] ?? null,
                'capacity' => $class['capacity'] ?? null,
                'is_active' => (bool) ($class['is_active'] ?? true),
            ]);
        }
    }
}