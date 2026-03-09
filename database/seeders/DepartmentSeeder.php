<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'إدارة تقنية المعلومات', 'code' => 'IT', 'description' => 'قسم مسؤول عن الأنظمة والحواسيب والشبكات', 'manager_user_id' => null],
            ['name' => 'المخازن', 'code' => 'WH', 'description' => 'قسم مسؤول عن إدارة المخزون والمواد', 'manager_user_id' => null],
            ['name' => 'المالية', 'code' => 'FIN', 'description' => 'قسم إدارة الشؤون المالية والموازنات', 'manager_user_id' => null],
            ['name' => 'الصيانة', 'code' => 'MAINT', 'description' => 'قسم مسؤول عن صيانة المعدات والمرافق', 'manager_user_id' => null],
            ['name' => 'الموارد البشرية', 'code' => 'HR', 'description' => 'قسم إدارة الموظفين والتوظيف والتدريب', 'manager_user_id' => null],
            ['name' => 'الخدمات العامة', 'code' => 'SERV', 'description' => 'قسم مسؤول عن الخدمات العامة والمشتريات اليومية', 'manager_user_id' => null],
        ];

        DB::table('departments')->insert($departments);
    }
}