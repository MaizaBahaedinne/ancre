<?php

namespace App\Http\Controllers;

use App\Models\AcademicSubject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = AcademicSubject::query()
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        return view('subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        $subjectsByLevel = AcademicSubject::query()
            ->orderBy('level')
            ->orderBy('name')
            ->get()
            ->groupBy('level');

        return view('subjects.create', [
            'levelOptions' => AcademicSubject::LEVEL_OPTIONS,
            'subjectsByLevel' => $subjectsByLevel,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'in:'.implode(',', AcademicSubject::LEVEL_OPTIONS)],
            'default_coefficient' => ['required', 'numeric', 'min:0.25', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $request->validate([
            'name' => [
                Rule::unique('academic_subjects', 'name')->where(fn ($query) => $query->where('level', $validated['level'])),
            ],
        ]);

        AcademicSubject::create([
            'name' => $validated['name'],
            'level' => $validated['level'],
            'default_coefficient' => $validated['default_coefficient'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return redirect()
            ->route('subjects.create')
            ->with('success', 'Matiere ajoutee avec succes.')
            ->with('selected_level', $validated['level']);
    }

    public function edit(AcademicSubject $subject): View
    {
        return view('subjects.edit', [
            'subject' => $subject,
            'levelOptions' => AcademicSubject::LEVEL_OPTIONS,
        ]);
    }

    public function update(Request $request, AcademicSubject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'in:'.implode(',', AcademicSubject::LEVEL_OPTIONS)],
            'default_coefficient' => ['required', 'numeric', 'min:0.25', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $request->validate([
            'name' => [
                Rule::unique('academic_subjects', 'name')
                    ->ignore($subject->id)
                    ->where(fn ($query) => $query->where('level', $validated['level'])),
            ],
        ]);

        $subject->update([
            'name' => $validated['name'],
            'level' => $validated['level'],
            'default_coefficient' => $validated['default_coefficient'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('subjects.index')->with('success', 'Matiere mise a jour avec succes.');
    }

    public function destroy(AcademicSubject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Matiere supprimee avec succes.');
    }
}
