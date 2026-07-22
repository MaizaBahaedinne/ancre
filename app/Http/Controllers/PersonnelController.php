<?php

namespace App\Http\Controllers;

use App\Models\PersonnelReferenceOption;
use App\Models\School;
use App\Models\SchoolClass;
use App\Http\Requests\StorePersonnelRequest;
use App\Http\Requests\UpdatePersonnelRequest;
use App\Models\Personnel;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PersonnelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $search = request('search');

        $baseQuery = Personnel::query()
            ->with('user')
            ->when($search, function ($query, $value) {
                $query->where('nom', 'like', "%{$value}%")
                    ->orWhere('prenom', 'like', "%{$value}%")
                    ->orWhere('fonction', 'like', "%{$value}%")
                    ->orWhere('telephone', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%");
            });

        $statsQuery = clone $baseQuery;

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'with_email' => (clone $statsQuery)->whereNotNull('email')->where('email', '!=', '')->count(),
            'by_department' => (clone $statsQuery)
                ->selectRaw('departement, COUNT(*) as aggregate')
                ->whereNotNull('departement')
                ->where('departement', '!=', '')
                ->groupBy('departement')
                ->orderByDesc('aggregate')
                ->get(),
            'by_function' => (clone $statsQuery)
                ->selectRaw('fonction, COUNT(*) as aggregate')
                ->whereNotNull('fonction')
                ->where('fonction', '!=', '')
                ->groupBy('fonction')
                ->orderByDesc('aggregate')
                ->get(),
            'by_gender' => (clone $statsQuery)
                ->selectRaw('sexe, COUNT(*) as aggregate')
                ->whereNotNull('sexe')
                ->where('sexe', '!=', '')
                ->groupBy('sexe')
                ->orderBy('sexe')
                ->get(),
        ];

        $personnels = $baseQuery
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return view('personnels.index', compact('personnels', 'search', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('personnels.create', $this->formData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonnelRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $createUserAccount = (bool) $request->boolean('create_user_account');
        $userRole = $data['user_role'] ?? null;

        unset($data['create_user_account'], $data['user_role']);

        $temporaryPassword = null;

        if ($createUserAccount) {
            $temporaryPassword = Str::password(10);

            $user = User::create([
                'name' => trim(($data['nom'] ?? '').' '.($data['prenom'] ?? '')),
                'email' => $data['email'],
                'password' => Hash::make($temporaryPassword),
            ]);

            Role::firstOrCreate([
                'name' => $userRole,
                'guard_name' => 'web',
            ]);

            $user->assignRole($userRole);
            $data['user_id'] = $user->id;
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('personnels', 'public');
        }

        Personnel::create($data);

        return redirect()
            ->route('personnels.index')
            ->with('success', 'Employe ajoute avec succes.')
            ->with('temporary_password', $temporaryPassword);
    }

    /**
     * Display the specified resource.
     */
    public function show(Personnel $personnel): View
    {
        $personnel->load(['manager', 'user']);
        $roles = Role::query()->orderBy('name')->get();

        return view('personnels.show', compact('personnel', 'roles'));
    }

    public function createUser(Personnel $personnel, Request $request): RedirectResponse
    {
        if ($personnel->user_id) {
            return redirect()->route('personnels.show', $personnel)->with('error', 'Ce personnel a deja un compte utilisateur associe.');
        }

        if (empty($personnel->email)) {
            return redirect()->route('personnels.show', $personnel)->with('error', 'Impossible de creer un compte utilisateur sans email pour ce personnel.');
        }

        $validated = $request->validate([
            'user_role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $temporaryPassword = null;
        $user = User::query()->where('email', $personnel->email)->first();

        if (! $user) {
            $temporaryPassword = Str::password(10);

            $user = User::create([
                'name' => trim($personnel->nom.' '.$personnel->prenom),
                'email' => $personnel->email,
                'password' => Hash::make($temporaryPassword),
            ]);
        }

        Role::firstOrCreate([
            'name' => $validated['user_role'],
            'guard_name' => 'web',
        ]);

        $user->syncRoles([$validated['user_role']]);

        $personnel->update([
            'user_id' => $user->id,
        ]);

        return redirect()
            ->route('personnels.show', $personnel)
            ->with('success', 'Compte utilisateur associe au personnel avec succes.')
            ->with('temporary_password', $temporaryPassword);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Personnel $personnel): View
    {
        return view('personnels.edit', [
            'personnel' => $personnel,
            ...$this->formData($personnel),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonnelRequest $request, Personnel $personnel): RedirectResponse
    {
        $data = $request->validated();
        $userRole = $data['user_role'] ?? null;
        unset($data['user_role']);

        if ($request->hasFile('photo')) {
            if ($personnel->photo && Storage::disk('public')->exists($personnel->photo)) {
                Storage::disk('public')->delete($personnel->photo);
            }

            $data['photo'] = $request->file('photo')->store('personnels', 'public');
        }

        if ($personnel->user && ! empty($data['email'])) {
            $personnel->user->update([
                'name' => trim(($data['nom'] ?? '').' '.($data['prenom'] ?? '')),
                'email' => $data['email'],
            ]);

            if ($userRole) {
                Role::firstOrCreate([
                    'name' => $userRole,
                    'guard_name' => 'web',
                ]);

                $personnel->user->syncRoles([$userRole]);
            }
        }

        $personnel->update($data);

        return redirect()->route('personnels.index')->with('success', 'Employe mis a jour avec succes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Personnel $personnel): RedirectResponse
    {
        if ($personnel->photo && Storage::disk('public')->exists($personnel->photo)) {
            Storage::disk('public')->delete($personnel->photo);
        }

        if ($personnel->user) {
            $personnel->user->delete();
        }

        $personnel->delete();

        return redirect()->route('personnels.index')->with('success', 'Employe supprime avec succes.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(?Personnel $personnel = null): array
    {
        $options = PersonnelReferenceOption::query()
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get()
            ->groupBy('type');

        $managers = Personnel::query()
            ->when($personnel, fn ($query) => $query->whereKeyNot($personnel->id))
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        $roles = Role::query()->orderBy('name')->get();

        return [
            'fonctions' => $options->get(PersonnelReferenceOption::TYPE_FONCTION, collect()),
            'departements' => $options->get(PersonnelReferenceOption::TYPE_DEPARTEMENT, collect()),
            'niveauxEtude' => $options->get(PersonnelReferenceOption::TYPE_NIVEAU_ETUDE, collect()),
            'managers' => $managers,
            'schools' => School::query()->orderBy('name')->get(),
            'schoolClasses' => SchoolClass::query()->with(['school', 'academicYear'])->where('is_active', true)->orderBy('name')->get(),
            'roles' => $roles,
            'linkedUserRole' => $personnel?->user?->roles?->pluck('name')?->first(),
        ];
    }
}
