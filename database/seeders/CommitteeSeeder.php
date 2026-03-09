<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommitteeSeeder extends Seeder
{
    public function run(): void
    {
        $committees = [
            [
                'name' => 'لجنة الشراء العامة',
                'department_id' => 6, // الخدمات العامة
                'description' => 'لجنة مسؤولة عن المشتريات اليومية والعقود',
                'manager_user_id' => 1, // superadmin
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'لجنة تقييم الموردين',
                'department_id' => 2, // المخازن
                'description' => 'لجنة مسؤولة عن تقييم الموردين والمناقصات',
                'manager_user_id' => 2, // مثال: مستخدم المخازن
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'لجنة الصيانة والمعدات',
                'department_id' => 4, // الصيانة
                'description' => 'لجنة مسؤولة عن متابعة الصيانة والمعدات',
                'manager_user_id' => 4, // مثال: مستخدم الصيانة
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('committees')->insert($committees);
    }
}