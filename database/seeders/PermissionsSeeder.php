<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'User',
            'Department',
            'Role',
            'Permission',
            'Committees',
            'Estimate',
            'EstimateItem',
            'PurchaseRequest',
            'Procurement',
            'ProcurementItem',
            'Vendors',
        ];

        $actions = ['view', 'create', 'edit', 'delete'];

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
        ];

        foreach ($extraPermissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'sanctum',
            ]);
        }

        $this->command->info('✅ Permissions seeded successfully!');
    }
}
