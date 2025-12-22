<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $models = ['User', 'Department', 'Session', 'Role', 'PersonalAccessToken'];
        $actions = ['create', 'edit', 'delete', 'view'];

        $permissions = [];

        // إنشاء الصلاحيات التلقائية
        foreach ($models as $model) {
            foreach ($actions as $action) {
                $permName = "{$action}-{$model}";
                Permission::firstOrCreate(['name' => $permName]);
                $permissions[] = $permName;
            }
        }

        // صلاحيات مخصصة خارج النمط القياسي
        $customPermissions = [
            'view-department-users',
            // أضف أي صلاحيات خاصة هنا
        ];

        foreach ($customPermissions as $permName) {
            Permission::firstOrCreate(['name' => $permName]);
            $permissions[] = $permName;
        }

        // إنشاء دور Super Admin ومنحه كل الصلاحيات
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions($permissions);

        // إنشاء مستخدم Super Admin
        $superAdminEmail = 'superadmin@example.com';
        $superAdmin = User::firstOrCreate(
            ['email' => $superAdminEmail],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        $superAdmin->assignRole($superAdminRole);

        $this->command->info('Super Admin user and all permissions created successfully!');
    }
}
