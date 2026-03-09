<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommitteeUserSeeder extends Seeder
{
    public function run(): void
    {
        $committeeUsers = [
            ['user_id' => 1, 'committee_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 2, 'committee_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 3, 'committee_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 4, 'committee_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => 5, 'committee_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('committee_user')->insert($committeeUsers);
    }
}