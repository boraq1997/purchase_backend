<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentsSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'الإدارة العامة',
                'username' => 'general_dept',
                'code' => 'GEN01',
                'description' => 'الإدارة المسؤولة عن الإشراف العام على جميع الأقسام',
            ],
            [
                'name' => 'قسم المشتريات',
                'username' => 'purchases_dept',
                'code' => 'PUR01',
                'description' => 'مسؤول عن عمليات الشراء والعطاءات والمناقصات',
            ],
            [
                'name' => 'قسم المالية',
                'username' => 'finance_dept',
                'code' => 'FIN01',
                'description' => 'يتولى مهام المحاسبة وإدارة النفقات والموازنات',
            ],
            [
                'name' => 'قسم المستودع',
                'username' => 'warehouse_dept',
                'code' => 'WH01',
                'description' => 'يتابع استلام وتخزين المواد وصرفها حسب الطلبات',
            ],
            [
                'name' => 'قسم تقنية المعلومات',
                'username' => 'it_dept',
                'code' => 'IT01',
                'description' => 'مسؤول عن الأنظمة التقنية والدعم الفني',
            ],
        ];

        foreach ($departments as $dep) {
            Department::firstOrCreate(
                ['code' => $dep['code']],
                $dep
            );
        }

        $this->command->info('✅ Departments seeded successfully!');
    }
}