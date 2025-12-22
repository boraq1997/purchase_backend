<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Committee;
use App\Models\User;
use App\Models\Department;

class CommitteesSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::where('name', 'قسم المشتريات')->first();
        $manager = User::where('email', 'manager@purchases.com')->first();

        $committee = Committee::firstOrCreate([
            'name' => 'لجنة الشراء الرئيسية',
            'department_id' => $department?->id,
            'manager_user_id' => $manager?->id,
        ]);

        // أعضاء اللجنة
        $committee->users()->sync(User::pluck('id')->take(3));

        $this->command->info('✅ Committees seeded successfully!');
    }
}