<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🧩 جميع الوحدات (Modules)
        $modules = [
            'User',
            'Department',
            'Role',
            'Permission',
            'Committees',
            'Estimate',
            'EstimateItem',
            'PurchaseRequest',
            'RequestItem',
            'Procurement',
            'ProcurementItem',
            'WarehouseCheck',
            'NeedsAssessment',
            'Report',
            'Vendors'
        ];

        // ⚙️ الإجراءات (Actions)
        $actions = ['view', 'create', 'edit', 'delete'];

        // 🛠️ إنشاء الصلاحيات
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action}-{$module}",
                    'guard_name' => 'sanctum',
                ]);
            }
        }

        // صلاحيات إضافية خاصة
        $extraPermissions = [
            'view-department-users',
            'approve-purchase-request',
            'review-estimate',
            'finalize-procurement',
            'generate-report',
        ];

        foreach ($extraPermissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'sanctum',
            ]);
        }

        // ✅ عرض الصلاحيات بعد الإنشاء
        $this->command->info('✅ All Permissions seeded successfully!');

        /*
        |--------------------------------------------------------------------------
        | إنشاء الأدوار (Roles)
        |--------------------------------------------------------------------------
        */

        // جلب جميع الصلاحيات
        $allPermissions = Permission::pluck('name')->toArray();

        // 🧑‍💼 الأدوار الأساسية
        $roles = [
            'Admin' => $allPermissions, // كل الصلاحيات
            'Manager' => array_filter($allPermissions, fn($p) =>
                str_contains($p, 'view') ||
                str_contains($p, 'edit') ||
                str_contains($p, 'approve') ||
                str_contains($p, 'review')
            ),
            'Reviewer' => array_filter($allPermissions, fn($p) =>
                str_contains($p, 'view') ||
                str_contains($p, 'review')
            ),
            'Employee' => array_filter($allPermissions, fn($p) =>
                str_contains($p, 'view') ||
                str_contains($p, 'create-PurchaseRequest') ||
                str_contains($p, 'create-Estimate')
            ),
        ];

        // إنشاء الأدوار وربط الصلاحيات
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'sanctum',
            ]);
            $role->syncPermissions($rolePermissions);
        }

        $this->command->info('✅ Roles and permissions seeded successfully!');
    }
}