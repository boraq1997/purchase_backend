<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ==============================
        // تعريف الصلاحيات لكل وحدة
        // ==============================

        $modules = [

            'ActivityLog' => ['view'],

            'Committee' => ['view','create','edit','delete'],
            'Department' => ['view','create','edit','delete'],
            'Estimate' => ['view','create','edit','delete','agree','show_images'],
            'EstimateItem' => ['view','create','edit','delete'],
            'PurchaseRequest' => ['view','create','edit','delete','agree','show_images'],
            'PurchaseRequestImage' => ['view','create','edit','delete'],
            'Report' => ['create'],
            'RequestItem' => ['view','create','edit','delete'],
            'Role' => ['view','create','edit','delete'],
            'User' => ['view','create','edit','delete'],
            'Unit' => ['view','create','edit','delete'],
            'Vendor' => ['view','create','edit','delete'],
            'WarehouseCheck' => ['view','create','edit','delete'],
        ];

        // ==============================
        // إنشاء الصلاحيات بنمط Module-Action
        // ==============================

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {

                Permission::firstOrCreate([
                    'name'       => "{$module}-{$action}",
                    'guard_name' => 'sanctum',
                ]);
            }
        }

        $this->command->info('✅ Permissions created successfully.');

        // ==============================
        // إنشاء الأدوار
        // ==============================

        $allPermissions = Permission::where('guard_name', 'sanctum')
            ->pluck('name')
            ->toArray();

        $roles = [

            // Admin: كل الصلاحيات
            'Admin' => $allPermissions,

            // Manager: view + edit + agree + show_images
            'Manager' => array_values(array_filter($allPermissions, function ($perm) {
                return str_ends_with($perm, '-view') ||
                       str_ends_with($perm, '-edit') ||
                       str_contains($perm, '-agree') ||
                       str_contains($perm, '-show_images');
            })),

            // Reviewer: view + agree + show_images
            'Reviewer' => array_values(array_filter($allPermissions, function ($perm) {
                return str_ends_with($perm, '-view') ||
                       str_contains($perm, '-agree') ||
                       str_contains($perm, '-show_images');
            })),

            // Employee: طلبات الشراء فقط
            'Employee' => [
                'PurchaseRequest-view',
                'PurchaseRequest-create',
                'RequestItem-view',
                'RequestItem-create',
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