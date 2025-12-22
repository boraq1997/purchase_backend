<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Committee;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | الخطوة 1: تشغيل Seeder الأدوار والصلاحيات أولاً
        |--------------------------------------------------------------------------
        */
        $this->call(RolesAndPermissionsSeeder::class);

        /*
        |--------------------------------------------------------------------------
        | الخطوة 2: إنشاء الأقسام الأساسية
        |--------------------------------------------------------------------------
        */
        $itDepartment = Department::firstOrCreate([
            'name' => 'IT Department',
        ], [
            'username' => 'it_dept',
            'code' => 'IT01',
            'description' => 'Handles all tech-related operations',
        ]);

        $hrDepartment = Department::firstOrCreate([
            'name' => 'HR Department',
        ], [
            'username' => 'hr_dept',
            'code' => 'HR01',
            'description' => 'Responsible for human resources',
        ]);

        /*
        |--------------------------------------------------------------------------
        | الخطوة 3: إنشاء مستخدمين
        |--------------------------------------------------------------------------
        */
        $admin = User::firstOrCreate(
            ['email' => 'boraqnz@gmail.com'],
            [
                'name' => 'Boraq Nezar',
                'username' => 'superadmin',
                'password' => bcrypt('password'),
                'department_id' => $itDepartment->id,
                'status' => 'active',
            ]
        );

        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'IT Manager',
                'username' => 'supermanager',
                'password' => bcrypt('password'),
                'department_id' => $itDepartment->id,
                'status' => 'active',
            ]
        );

        $employee = User::firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'HR Employee',
                'username' => 'employee',
                'password' => bcrypt('password'),
                'department_id' => $hrDepartment->id,
                'status' => 'active',
            ]
        );

        // تحديث المدراء للأقسام
        $itDepartment->update(['manager_user_id' => $manager->id]);
        $hrDepartment->update(['manager_user_id' => $employee->id]);

        /*
        |--------------------------------------------------------------------------
        | الخطوة 4: تعيين الأدوار للمستخدمين
        |--------------------------------------------------------------------------
        */
        $admin->assignRole('Admin');
        $manager->assignRole('Manager');
        $employee->assignRole('Employee');

        /*
        |--------------------------------------------------------------------------
        | الخطوة 5: إنشاء لجنة وربط المستخدمين بها
        |--------------------------------------------------------------------------
        */
        $committee = Committee::firstOrCreate([
            'name' => 'Budget Committee',
            'department_id' => $itDepartment->id,
            'manager_user_id' => $manager->id,
        ], [
            'description' => 'Oversees budget planning and allocation',
        ]);

        $committee->users()->syncWithoutDetaching([$admin->id, $manager->id]);

        /*
        |--------------------------------------------------------------------------
        | الخطوة 6: تشغيل بقية الـ Seeders لتعبئة البيانات التجريبية
        |--------------------------------------------------------------------------
        */
        $this->call([
            DepartmentsSeeder::class,
            UsersSeeder::class,
            CommitteesSeeder::class,
            PurchaseRequestsSeeder::class,
            EstimatesSeeder::class,
            ProcurementsSeeder::class,
            WarehouseChecksSeeder::class,
            NeedsAssessmentsSeeder::class,
            ReportsSeeder::class,
        ]);

        $this->command->info('✅ Database seeded successfully with all core and related data!');
    }
}