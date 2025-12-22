<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => Hash::make('123456'),
                'department_id' => $departments->first()?->id,
                'status' => 'active',
            ],
            [
                'name' => 'مدير المشتريات',
                'email' => 'manager@purchases.com',
                'username' => 'manager',
                'password' => Hash::make('123456'),
                'department_id' => $departments->where('name', 'قسم المشتريات')->first()?->id,
                'status' => 'active',
            ],
        ];

        foreach ($users as $u) {
            User::firstOrCreate(['email' => $u['email']], $u);
        }

        $this->command->info('✅ Users seeded successfully!');
    }
}