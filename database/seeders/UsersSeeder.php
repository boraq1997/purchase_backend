<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'أحمد علي',
                'username' => 'ahmed.ali',
                'email' => 'ahmed.ali@example.com',
                'phone' => '07700000001',
                'password' => Hash::make('password123'),
                'department_id' => 1, // تقنية المعلومات
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'سارة محمد',
                'username' => 'sara.mohammed',
                'email' => 'sara.mohammed@example.com',
                'phone' => '07700000002',
                'password' => Hash::make('password123'),
                'department_id' => 2, // المخازن
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'خالد يوسف',
                'username' => 'khaled.youssef',
                'email' => 'khaled.youssef@example.com',
                'phone' => '07700000003',
                'password' => Hash::make('password123'),
                'department_id' => 3, // المالية
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'ليلى حسن',
                'username' => 'layla.hassan',
                'email' => 'layla.hassan@example.com',
                'phone' => '07700000004',
                'password' => Hash::make('password123'),
                'department_id' => 4, // الصيانة
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'مروان كريم',
                'username' => 'marwan.karim',
                'email' => 'marwan.karim@example.com',
                'phone' => '07700000005',
                'password' => Hash::make('password123'),
                'department_id' => 5, // الموارد البشرية
                'parent_id' => null,
                'status' => 'active',
            ],
            [
                'name' => 'نور سامي',
                'username' => 'noor.sami',
                'email' => 'noor.sami@example.com',
                'phone' => '07700000006',
                'password' => Hash::make('password123'),
                'department_id' => 6, // الخدمات العامة
                'parent_id' => null,
                'status' => 'active',
            ],
        ];

        DB::table('users')->insert($users);
    }
}