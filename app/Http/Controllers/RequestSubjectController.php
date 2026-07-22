<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommunicationSubjectRequest;
use App\Http\Requests\UpdateCommunicationSubjectRequest;
use App\Models\ParentRequestSubject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RequestSubjectController extends Controller
{
    public function index(): View
    {
        $subjects = ParentRequestSubject::query()
            ->orderBy('action_type')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();

        return view('request-subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        return view('request-subjects.create');
    }

    public function store(StoreCommunicationSubjectRequest $request): RedirectResponse
    {
        ParentRequestSubject::create($request->validated());

        return redirect()->route('demandes-sujets.index')->with('success', 'Sujet ajoute avec succes.');
    }

    public function edit(ParentRequestSubject $demandes_sujet): View
    {
        return view('request-subjects.edit', ['subject' => $demandes_sujet]);
    }

    public function update(UpdateCommunicationSubjectRequest $request, ParentRequestSubject $demandes_sujet): RedirectResponse
    {
        $demandes_sujet->update($request->validated());

        return redirect()->route('demandes-sujets.index')->with('success', 'Sujet mis a jour avec succes.');
    }

    public function destroy(ParentRequestSubject $demandes_sujet): RedirectResponse
    {
        $demandes_sujet->delete();

        return redirect()->route('demandes-sujets.index')->with('success', 'Sujet supprime avec succes.');
    }
}