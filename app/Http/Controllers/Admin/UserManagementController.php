<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::with('roles')->orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->syncRoles($data['roles']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur cree avec succes.');
    }

    public function edit(User $user): View
    {
        $user->load('roles');
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => ! empty($data['password']) ? Hash::make($data['password']) : $user->password,
        ]);

        $user->syncRoles($data['roles']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis a jour avec succes.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprime avec succes.');
    }
}