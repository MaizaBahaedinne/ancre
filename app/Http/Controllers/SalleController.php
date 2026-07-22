<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalleRequest;
use App\Http\Requests\UpdateSalleRequest;
use App\Models\Personnel;
use App\Models\Salle;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SalleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $salles = Salle::query()
            ->with('responsablePersonnel')
            ->withCount('activites')
            ->orderBy('nom')
            ->get();

        return view('salles.index', compact('salles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('salles.create', [
            'responsables' => $this->responsables(),
            'statutOptions' => Salle::STATUT_OPTIONS,
            'equipementOptions' => Salle::EQUIPEMENT_OPTIONS,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalleRequest $request): RedirectResponse
    {
        Salle::create($request->validated());

        return redirect()->route('salles.index')->with('success', 'Salle ajoutee avec succes.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Salle $salle): View
    {
        $salle->load(['responsablePersonnel', 'activites.responsablePersonnel']);

        return view('salles.show', compact('salle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Salle $salle): View
    {
        return view('salles.edit', [
            'salle' => $salle,
            'responsables' => $this->responsables(),
            'statutOptions' => Salle::STATUT_OPTIONS,
            'equipementOptions' => Salle::EQUIPEMENT_OPTIONS,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalleRequest $request, Salle $salle): RedirectResponse
    {
        $salle->update($request->validated());

        return redirect()->route('salles.index')->with('success', 'Salle mise a jour avec succes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salle $salle): RedirectResponse
    {
        if ($salle->activites()->exists()) {
            return redirect()->route('salles.index')->with('error', 'Impossible de supprimer cette salle car elle est deja liee a des activites.');
        }

        $salle->delete();

        return redirect()->route('salles.index')->with('success', 'Salle supprimee avec succes.');
    }

    private function responsables()
    {
        return Personnel::query()
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
    }
}
