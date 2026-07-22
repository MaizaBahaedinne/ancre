<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed roles, permissions, and default users.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'users.manage',
            'dashboard.view',
            'parents.view',
            'parents.create',
            'parents.update',
            'parents.delete',
            'children.view',
            'children.create',
            'children.update',
            'children.delete',
            'registrations.view',
            'registrations.create',
            'registrations.update',
            'registrations.delete',
            'packages.view',
            'packages.create',
            'packages.update',
            'packages.delete',
            'attendance.view',
            'attendance.create',
            'attendance.update',
            'attendance.delete',
            'payments.view',
            'payments.create',
            'payments.update',
            'payments.delete',
            'personnels.view',
            'personnels.create',
            'personnels.update',
            'personnels.delete',
            'reports.view',
            'activities.view',
            'activities.create',
            'activities.update',
            'activities.delete',
            'rooms.view',
            'rooms.create',
            'rooms.update',
            'rooms.delete',
            'schools.view',
            'schools.create',
            'schools.update',
            'schools.delete',
            'academic-years.view',
            'academic-years.create',
            'academic-years.update',
            'academic-years.delete',
            'incidents.view',
            'incidents.create',
            'incidents.update',
            'incidents.delete',
            'requests.view',
            'requests.update',
            'requests.subjects.manage',
            'requests.parent',
            'notifications.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'Administrateur', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'Responsable', 'guard_name' => 'web']);
        $educatorRole = Role::firstOrCreate(['name' => 'Educateur', 'guard_name' => 'web']);
        $parentRole = Role::firstOrCreate(['name' => 'Parent', 'guard_name' => 'web']);

        $adminRole->syncPermissions(Permission::all());

        $managerRole->syncPermissions([
            'dashboard.view',
            'parents.view',
            'parents.create',
            'parents.update',
            'children.view',
            'children.create',
            'children.update',
            'children.delete',
            'registrations.view',
            'registrations.create',
            'registrations.update',
            'registrations.delete',
            'packages.view',
            'packages.create',
            'packages.update',
            'packages.delete',
            'payments.view',
            'payments.create',
            'payments.update',
            'payments.delete',
            'personnels.view',
            'personnels.create',
            'personnels.update',
            'personnels.delete',
            'reports.view',
            'attendance.view',
            'attendance.create',
            'attendance.update',
            'attendance.delete',
            'activities.view',
            'activities.create',
            'activities.update',
            'activities.delete',
            'rooms.view',
            'rooms.create',
            'rooms.update',
            'rooms.delete',
            'schools.view',
            'schools.create',
            'schools.update',
            'schools.delete',
            'academic-years.view',
            'academic-years.create',
            'academic-years.update',
            'academic-years.delete',
            'incidents.view',
            'incidents.create',
            'incidents.update',
            'incidents.delete',
            'requests.view',
            'requests.update',
            'requests.subjects.manage',
            'notifications.view',
        ]);

        $educatorRole->syncPermissions([
            'dashboard.view',
            'children.view',
            'children.create',
            'children.update',
            'children.delete',
            'packages.view',
            'attendance.view',
            'attendance.create',
            'attendance.update',
            'attendance.delete',
            'activities.view',
            'activities.create',
            'activities.update',
            'rooms.view',
            'schools.view',
            'academic-years.view',
            'incidents.view',
            'incidents.create',
            'incidents.update',
            'requests.view',
            'requests.update',
        ]);

        $parentRole->syncPermissions([
            'dashboard.view',
            'children.view',
            'packages.view',
            'payments.view',
            'requests.parent',
            'notifications.view',
        ]);

        $users = [
            [
                'name' => 'Admin Garderie',
                'email' => 'admin@ancredeselites.tn',
                'role' => 'Administrateur',
            ],
            [
                'name' => 'Responsable Garderie',
                'email' => 'responsable@ancredeselites.tn',
                'role' => 'Responsable',
            ],
            [
                'name' => 'Educateur Garderie',
                'email' => 'educateur@ancredeselites.tn',
                'role' => 'Educateur',
            ],
            [
                'name' => 'Parent Demo',
                'email' => 'parent@ancredeselites.tn',
                'role' => 'Parent',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                ]
            );

            $user->syncRoles([$data['role']]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
