<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'ActivityLogs'     => ['view'],
            'Committees'       => ['view', 'create', 'edit', 'delete'],
            'Department'       => ['view', 'create', 'edit', 'delete'],
            'Estimate'         => ['view', 'create', 'edit', 'delete'],
            'EstimateItem'     => ['view', 'create', 'edit', 'delete'],
            'PurchaseRequest'  => ['view', 'create', 'edit', 'delete'],
            'Procurement'      => ['view', 'create', 'edit', 'delete'],
            'ProcurementItem'  => ['view', 'create', 'edit', 'delete'],
            'Report'           => ['view', 'create', 'edit', 'delete'],
            'Role'             => ['view', 'create', 'edit', 'delete'],
            'User'             => ['view', 'create', 'edit', 'delete'],
            'Permission'       => ['view', 'create', 'edit', 'delete'],
            'Vendors'          => ['view', 'create', 'edit', 'delete'],
        ];

        // نمط action-Module مطابق لـ api.php
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name'       => "{$action}-{$module}",
                    'guard_name' => 'sanctum',
                ]);
            }
        }

        $this->command->info('✅ Permissions created successfully.');

        $allPermissions = Permission::where('guard_name', 'sanctum')
            ->pluck('name')
            ->toArray();

        $roles = [
            'Admin' => $allPermissions,

            'Manager' => array_values(array_filter($allPermissions, function ($perm) {
                return str_starts_with($perm, 'view-') ||
                       str_starts_with($perm, 'edit-');
            })),

            'Reviewer' => array_values(array_filter($allPermissions, function ($perm) {
                return str_starts_with($perm, 'view-');
            })),

            'Employee' => [
                'view-PurchaseRequest',
                'create-PurchaseRequest',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate([
                'name'       => $roleName,
                'guard_name' => 'sanctum',
            ]);

            $role->syncPermissions($rolePermissions);
            $this->command->info("✅ Role '{$roleName}' synced.");
        }

        $this->command->info('✅ Roles & Permissions seeded successfully.');
    }
}