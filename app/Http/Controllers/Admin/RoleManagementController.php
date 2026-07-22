<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleManagementController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount(['permissions', 'users'])->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::query()->orderBy('name')->get()->groupBy(fn (Permission $permission) => explode('.', $permission->name)[0]);

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($data['permissions'] ?? []);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role cree avec succes.');
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');
        $permissions = Permission::query()->orderBy('name')->get()->groupBy(fn (Permission $permission) => explode('.', $permission->name)[0]);

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update([
            'name' => $data['name'],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role mis a jour avec succes.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return redirect()->route('admin.roles.index')->with('error', 'Impossible de supprimer un role assigne a des utilisateurs.');
        }

        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role supprime avec succes.');
    }
}