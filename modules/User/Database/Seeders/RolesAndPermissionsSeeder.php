<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $registrar = app(PermissionRegistrar::class);
        $registrar->forgetCachedPermissions();

        $permissions = [
            // CRM: Leads
            'leads.view',
            'leads.create',
            'leads.update',
            'leads.delete',
            'leads.assign',
            'leads.convert',

            // CRM: Customers
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',

            // CRM: Deals
            'deals.view',
            'deals.create',
            'deals.update',
            'deals.delete',

            // CRM: Activities and follow-ups
            'activities.view',
            'activities.create',
            'activities.update',
            'activities.delete',

            // User administration
            'users.view',
            'users.create',
            'users.update',
            'users.deactivate',
            'users.assign-roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $allPermissions = Permission::where(
            'guard_name',
            'web'
        )->pluck('name')->all();

        $rolePermissions = [
            'super_admin' => $allPermissions,

            'admin' => [
                'users.view',
                'users.create',
                'users.update',
                'users.deactivate',
                'users.assign-roles',
            ],

            'management' => [
                'leads.view',
                'customers.view',
                'deals.view',
                'activities.view',
            ],

            'sales' => [
                'leads.view',
                'leads.create',
                'leads.update',
                'leads.assign',
                'leads.convert',
                'customers.view',
                'customers.create',
                'customers.update',
                'deals.view',
                'deals.create',
                'deals.update',
                'activities.view',
                'activities.create',
                'activities.update',
            ],

            'marketing' => [
                'leads.view',
                'leads.create',
                'leads.update',
                'activities.view',
                'activities.create',
                'activities.update',
            ],

            'project_manager' => [],

            'developer' => [],

            'finance_staff' => [],
        ];

        foreach ($rolePermissions as $roleName => $rolePermissionNames) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($rolePermissionNames);
        }

        $registrar->forgetCachedPermissions();
    }
}
