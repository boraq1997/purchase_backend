<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdmin2Seeder extends Seeder
{
    public function run(): void
    {
        // إنشاء أو استرجاع المستخدم إذا كان موجودًا بالفعل
        $admin = User::firstOrCreate(
            ['username' => 'superadmin'], // شرط البحث
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'phone' => '07799999999',
                'password' => Hash::make('password'),
                'department_id' => null,  // يمكن ربط قسم لاحقاً
                'parent_id' => null,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("✅ Super Admin user seeded successfully (ID: {$admin->id})");
    }
}