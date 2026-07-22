<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaiementRequest;
use App\Http\Requests\UpdatePaiementRequest;
use App\Models\Enfant;
use App\Models\ParentModel;
use App\Models\Paiement;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PaiementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $today = Carbon::today();
        $parent = $this->currentParent();
        $parentChildIds = $this->isParentUser()
            ? ($parent?->enfants()->pluck('id')->all() ?? [])
            : [];
        $scope = request('scope', 'current');
        $isArchiveScope = $scope === 'archive';
        $enfantId = request('enfant_id');
        $statut = request('statut');
        $mois = $isArchiveScope ? request('mois') : (int) $today->format('n');
        $annee = $isArchiveScope ? request('annee') : (int) $today->format('Y');

        $paiementsQuery = Paiement::with('enfant')
            ->when($this->isParentUser(), function ($query) use ($parentChildIds) {
                if (empty($parentChildIds)) {
                    return $query->whereRaw('1 = 0');
                }

                return $query->whereIn('enfant_id', $parentChildIds);
            })
            ->when($enfantId, fn ($query, $value) => $query->where('enfant_id', $value))
            ->when($statut, fn ($query, $value) => $query->where('statut', $value))
            ->orderByDesc('date_paiement')
            ->orderByDesc('id');

        if ($isArchiveScope) {
            $paiementsQuery->where(function ($query) use ($today) {
                $query->where('annee', '<', (int) $today->format('Y'))
                    ->orWhere(function ($subQuery) use ($today) {
                        $subQuery->where('annee', (int) $today->format('Y'))
                            ->where('mois', '<', (int) $today->format('n'));
                    });
            })
                ->when($mois, fn ($query, $value) => $query->where('mois', $value))
                ->when($annee, fn ($query, $value) => $query->where('annee', $value));
        } else {
            $paiementsQuery
                ->where('mois', (int) $today->format('n'))
                ->where('annee', (int) $today->format('Y'));
        }

        $paiements = $paiementsQuery->get();

        $impayesCount = Paiement::query()
            ->when($this->isParentUser(), function ($query) use ($parentChildIds) {
                if (empty($parentChildIds)) {
                    return $query->whereRaw('1 = 0');
                }

                return $query->whereIn('enfant_id', $parentChildIds);
            })
            ->where('statut', 'En retard')
            ->when(! $isArchiveScope, function ($query) use ($today) {
                $query->where('mois', (int) $today->format('n'))
                    ->where('annee', (int) $today->format('Y'));
            })
            ->count();

        $enfants = Enfant::orderBy('nom')->orderBy('prenom')->get();

        return view('paiements.index', compact(
            'paiements',
            'impayesCount',
            'enfants',
            'enfantId',
            'statut',
            'mois',
            'annee',
            'scope',
            'isArchiveScope',
            'today'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $enfants = $this->allowedChildrenQuery()
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('paiements.create', compact('enfants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaiementRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($this->isParentUser()) {
            $canUseChild = $this->allowedChildrenQuery()
                ->whereKey($validated['enfant_id'])
                ->exists();

            abort_unless($canUseChild, 403);
        }

        $paiement = Paiement::create($validated);

        if (empty($paiement->reference)) {
            $paiement->update([
                'reference' => $this->buildPaiementReference($paiement),
            ]);
        }

        return redirect()
            ->route('paiements.index')
            ->with('success', 'Paiement enregistre avec succes.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paiement $paiement): View
    {
        $this->ensureParentCanAccessPaiement($paiement);

        $paiement->load('enfant.parent');

        return view('paiements.show', compact('paiement'));
    }

    /**
     * Download receipt PDF for the specified payment.
     */
    public function receipt(Paiement $paiement)
    {
        $this->ensureParentCanAccessPaiement($paiement);

        $paiement->load('enfant.parent');

        $pdf = Pdf::loadView('paiements.receipt', [
            'paiement' => $paiement,
            'generatedAt' => now(),
        ]);

        $fileName = sprintf(
            'recu-paiement-%s.pdf',
            str_replace(' ', '-', strtolower($paiement->reference ?: $this->buildPaiementReference($paiement)))
        );

        return $pdf->download($fileName);
    }

    private function buildPaiementReference(Paiement $paiement): string
    {
        $year = $paiement->annee ?: (int) optional($paiement->date_paiement)->format('Y') ?: (int) now()->format('Y');

        return sprintf('PAY-%s-%06d', $year, $paiement->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paiement $paiement): View
    {
        $this->ensureParentCanAccessPaiement($paiement);

        $enfants = $this->allowedChildrenQuery()
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('paiements.edit', compact('paiement', 'enfants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaiementRequest $request, Paiement $paiement): RedirectResponse
    {
        $this->ensureParentCanAccessPaiement($paiement);

        $validated = $request->validated();

        if ($this->isParentUser()) {
            $canUseChild = $this->allowedChildrenQuery()
                ->whereKey($validated['enfant_id'])
                ->exists();

            abort_unless($canUseChild, 403);
        }

        $paiement->update($validated);

        return redirect()
            ->route('paiements.index')
            ->with('success', 'Paiement mis a jour avec succes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paiement $paiement): RedirectResponse
    {
        $this->ensureParentCanAccessPaiement($paiement);

        $paiement->delete();

        return redirect()
            ->route('paiements.index')
            ->with('success', 'Paiement supprime avec succes.');
    }

    private function isParentUser(): bool
    {
        return (bool) auth()->user()?->hasRole('Parent');
    }

    private function currentParent(): ?ParentModel
    {
        $userId = auth()->id();

        if (! $userId) {
            return null;
        }

        return ParentModel::query()->where('user_id', $userId)->first();
    }

    private function allowedChildrenQuery()
    {
        $query = Enfant::query();

        if (! $this->isParentUser()) {
            return $query;
        }

        $parent = $this->currentParent();

        if (! $parent) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($scope) use ($parent) {
            $scope->where('parent_id', $parent->id)
                ->orWhereHas('familyRelations', function ($relationScope) use ($parent) {
                    $relationScope->where('parent_id', $parent->id);
                });
        });
    }

    private function ensureParentCanAccessPaiement(Paiement $paiement): void
    {
        if (! $this->isParentUser()) {
            return;
        }

        $canAccess = $this->allowedChildrenQuery()
            ->whereKey($paiement->enfant_id)
            ->exists();

        abort_unless($canAccess, 403);
    }
}
