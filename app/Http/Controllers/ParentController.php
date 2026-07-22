<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParentRequest;
use App\Http\Requests\UpdateParentRequest;
use App\Models\Enfant;
use App\Models\ParentModel;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ParentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $search = request('search');

        $baseQuery = ParentModel::query()
            ->with('user')
            ->when($search, function ($query, $searchValue) {
                $query->where('nom', 'like', "%{$searchValue}%")
                    ->orWhere('prenom', 'like', "%{$searchValue}%")
                    ->orWhere('telephone', 'like', "%{$searchValue}%")
                    ->orWhere('email', 'like', "%{$searchValue}%");
            });

        $statsQuery = clone $baseQuery;

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'with_user' => (clone $statsQuery)->whereNotNull('user_id')->count(),
            'without_user' => (clone $statsQuery)->whereNull('user_id')->count(),
            'with_email' => (clone $statsQuery)->whereNotNull('email')->where('email', '!=', '')->count(),
        ];

        $parents = $baseQuery
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('parents.index', compact('parents', 'search', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('parents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cin_recto')) {
            $data['cin_recto'] = $request->file('cin_recto')->store('parents/cin', 'public');
        }

        if ($request->hasFile('cin_verso')) {
            $data['cin_verso'] = $request->file('cin_verso')->store('parents/cin', 'public');
        }

        ParentModel::create($data);

        return redirect()
            ->route('parents.index')
            ->with('success', 'Parent ajoute avec succes.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ParentModel $parent): View
    {
        $parent->load(['user.roles'])->loadCount('enfants');

        $linkedEnfants = Enfant::query()
            ->with([
                'familyRelations' => fn ($query) => $query
                    ->where('parent_id', $parent->id),
            ])
            ->where('parent_id', $parent->id)
            ->orWhereHas('familyRelations', fn ($query) => $query->where('parent_id', $parent->id))
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('parents.show', compact('parent', 'linkedEnfants'));
    }

    public function createUser(ParentModel $parent, Request $request): RedirectResponse
    {
        if ($parent->user_id) {
            return redirect()->route('parents.show', $parent)->with('error', 'Ce parent a deja un compte utilisateur associe.');
        }

        if (empty($parent->email)) {
            return redirect()->route('parents.show', $parent)->with('error', 'Impossible de creer un compte utilisateur sans email pour ce parent.');
        }

        $temporaryPassword = null;
        $user = User::query()->where('email', $parent->email)->first();

        if (! $user) {
            $temporaryPassword = Str::password(10);

            $user = User::create([
                'name' => trim($parent->nom.' '.$parent->prenom),
                'email' => $parent->email,
                'password' => Hash::make($temporaryPassword),
            ]);
        }

        Role::firstOrCreate([
            'name' => 'Parent',
            'guard_name' => 'web',
        ]);

        $user->assignRole('Parent');

        $parent->update([
            'user_id' => $user->id,
        ]);

        return redirect()
            ->route('parents.show', $parent)
            ->with('success', 'Compte utilisateur associe au parent avec succes.')
            ->with('temporary_password', $temporaryPassword);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ParentModel $parent): View
    {
        return view('parents.edit', compact('parent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParentRequest $request, ParentModel $parent): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cin_recto')) {
            if ($parent->cin_recto && Storage::disk('public')->exists($parent->cin_recto)) {
                Storage::disk('public')->delete($parent->cin_recto);
            }

            $data['cin_recto'] = $request->file('cin_recto')->store('parents/cin', 'public');
        }

        if ($request->hasFile('cin_verso')) {
            if ($parent->cin_verso && Storage::disk('public')->exists($parent->cin_verso)) {
                Storage::disk('public')->delete($parent->cin_verso);
            }

            $data['cin_verso'] = $request->file('cin_verso')->store('parents/cin', 'public');
        }

        $parent->update($data);

        return redirect()
            ->route('parents.index')
            ->with('success', 'Parent mis a jour avec succes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParentModel $parent): RedirectResponse
    {
        if ($parent->cin_recto && Storage::disk('public')->exists($parent->cin_recto)) {
            Storage::disk('public')->delete($parent->cin_recto);
        }

        if ($parent->cin_verso && Storage::disk('public')->exists($parent->cin_verso)) {
            Storage::disk('public')->delete($parent->cin_verso);
        }

        $parent->delete();

        return redirect()
            ->route('parents.index')
            ->with('success', 'Parent supprime avec succes.');
    }
}
