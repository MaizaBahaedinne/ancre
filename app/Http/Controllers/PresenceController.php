<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresenceRequest;
use App\Http\Requests\UpdatePresenceRequest;
use App\Models\Enfant;
use App\Models\ParentModel;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PresenceController extends Controller
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
        $scope = request('scope', 'today');
        $isArchiveScope = $scope === 'archive';

        $date = request('date');
        $mois = request('mois');
        $annee = request('annee');

        $presencesQuery = Presence::with('enfant');

        if ($this->isParentUser()) {
            if (empty($parentChildIds)) {
                $presencesQuery->whereRaw('1 = 0');
            } else {
                $presencesQuery->whereIn('enfant_id', $parentChildIds);
            }
        }

        if ($isArchiveScope) {
            $presencesQuery->whereDate('date', '<', $today)
                ->when($date, fn ($query, $value) => $query->whereDate('date', $value))
                ->when($mois, fn ($query, $value) => $query->whereMonth('date', $value))
                ->when($annee, fn ($query, $value) => $query->whereYear('date', $value));
        } else {
            $presencesQuery->whereDate('date', $today);

            $date = $today->toDateString();
            $mois = null;
            $annee = null;
        }

        $presences = $presencesQuery
            ->orderByDesc('date')
            ->get();

        $enfants = $this->allowedChildrenQuery()
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('presences.index', compact('presences', 'enfants', 'date', 'mois', 'annee', 'scope', 'isArchiveScope', 'today'));
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

        return view('presences.create', compact('enfants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePresenceRequest $request): RedirectResponse
    {
        if ($this->isParentUser()) {
            $canUseChild = $this->allowedChildrenQuery()
                ->whereKey($request->validated('enfant_id'))
                ->exists();

            abort_unless($canUseChild, 403);
        }

        Presence::updateOrCreate(
            [
                'enfant_id' => $request->validated('enfant_id'),
                'date' => $request->validated('date'),
            ],
            $request->validated()
        );

        return redirect()
            ->route('presences.index')
            ->with('success', 'Presence enregistree avec succes.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Presence $presence): View
    {
        $this->ensureParentCanAccessPresence($presence);

        $presence->load('enfant.parent');

        return view('presences.show', compact('presence'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presence $presence): View
    {
        $this->ensureParentCanAccessPresence($presence);

        $enfants = $this->allowedChildrenQuery()
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('presences.edit', compact('presence', 'enfants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePresenceRequest $request, Presence $presence): RedirectResponse
    {
        $this->ensureParentCanAccessPresence($presence);

        $validated = $request->validated();

        if ($this->isParentUser()) {
            $canUseChild = $this->allowedChildrenQuery()
                ->whereKey($validated['enfant_id'])
                ->exists();

            abort_unless($canUseChild, 403);
        }

        $presence->update($validated);

        return redirect()
            ->route('presences.index')
            ->with('success', 'Presence mise a jour avec succes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presence $presence): RedirectResponse
    {
        $this->ensureParentCanAccessPresence($presence);

        $presence->delete();

        return redirect()
            ->route('presences.index')
            ->with('success', 'Presence supprimee avec succes.');
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

    private function ensureParentCanAccessPresence(Presence $presence): void
    {
        if (! $this->isParentUser()) {
            return;
        }

        $canAccess = $this->allowedChildrenQuery()
            ->whereKey($presence->enfant_id)
            ->exists();

        abort_unless($canAccess, 403);
    }
}
