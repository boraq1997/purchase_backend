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
            'ActivityLog'            => ['view'],
            'Department'             => ['view', 'create', 'edit', 'delete'],
            'Committee'              => ['view', 'create', 'edit', 'delete'],
            'Estimate'               => ['view', 'create', 'edit', 'delete', 'approve'],
            'EstimateItem'           => ['view', 'create', 'edit', 'delete'],
            'Procurement'            => ['view', 'create', 'edit', 'delete'],
            'ProcurementItem'        => ['view', 'create', 'edit', 'delete'],
            'PurchaseRequest'        => ['view', 'create', 'edit', 'delete'],
            'PurchaseRequestImage'   => ['view', 'create', 'edit', 'delete'],
            'Report'                 => ['view', 'create', 'edit', 'delete'],
            'RequestItem'            => ['view', 'create', 'edit', 'delete'],
            'Role'                   => ['view', 'create', 'edit', 'delete'],
            'Unit'                   => ['view', 'create', 'edit', 'delete'],
            'User'                   => ['view', 'create', 'edit', 'delete'],
            'Vendor'                 => ['view', 'create', 'edit', 'delete'],
            'WarehouseCheck'         => ['view', 'create', 'edit', 'delete', 'accept'],
            'Permission'             => ['view', 'create', 'edit', 'delete'],
        ];

        // إنشاء الصلاحيات
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action}-{$module}",
                    'guard_name' => 'sanctum',
                ]);
            }
        }

        $this->command->info('✅ Permissions created successfully.');

        // جمع كل الصلاحيات
        $allPermissions = Permission::where('guard_name', 'sanctum')
            ->pluck('name')
            ->toArray();

        // تحديد الأدوار والصلاحيات
        $roles = [
            'Admin' => $allPermissions, // كل الصلاحيات

            'Employee' => array_values(array_filter($allPermissions, function ($perm) {
                // مسؤول المشتريات فقط الصلاحيات المتعلقة بالطلبات والمشتريات
                return str_starts_with($perm, 'PurchaseRequest') ||
                       str_starts_with($perm, 'PurchaseRequestImage') ||
                       str_starts_with($perm, 'RequestItem') ||
                       str_starts_with($perm, 'Procurement') ||
                       str_starts_with($perm, 'ProcurementItem') ||
                       str_starts_with($perm, 'Estimate') ||
                       str_starts_with($perm, 'EstimateItem') ||
                       str_starts_with($perm, 'WarehouseCheck');
            })),

            'Manager' => array_values(array_filter($allPermissions, function ($perm) {
                // المدير عادة view + edit
                return str_starts_with($perm, 'view-') || str_starts_with($perm, 'edit-');
            })),

            'Reviewer' => array_values(array_filter($allPermissions, function ($perm) {
                // المراجع: view فقط
                return str_starts_with($perm, 'view-');
            })),
        ];

        // إنشاء وربط الأدوار بالصلاحيات
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