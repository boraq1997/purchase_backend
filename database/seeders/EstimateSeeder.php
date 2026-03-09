<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstimateSeeder extends Seeder
{
    public function run(): void
    {
        $estimates = [
            [
                'purchase_request_id' => 1,
                'request_item_id' => 1,
                'vendor_id' => 1,
                'estimate_date' => now(),
                'total_amount' => 50000,
                'notes' => 'عرض سعر أولي من شركة ألفا',
                'status' => 'pending',
                'created_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'purchase_request_id' => 1,
                'request_item_id' => 2,
                'vendor_id' => 2,
                'estimate_date' => now(),
                'total_amount' => 60000,
                'notes' => 'عرض سعر من شركة بيتا',
                'status' => 'pending',
                'created_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'purchase_request_id' => 2,
                'request_item_id' => 3,
                'vendor_id' => 3,
                'estimate_date' => now(),
                'total_amount' => 1000000,
                'notes' => 'عرض من شركة جاما للفلتر والزيوت',
                'status' => 'pending',
                'created_by' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('estimates')->insert($estimates);
    }
}