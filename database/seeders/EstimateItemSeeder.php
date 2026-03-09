<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstimateItemSeeder extends Seeder
{
    public function run(): void
    {
        $estimateItems = [
            [
                'estimate_id' => 1,
                'request_item_id' => 1,
                'item_name' => 'أقلام حبر',
                'quantity' => 100,
                'unit_price' => 500,
                'total_price' => 50000,
                'notes' => 'الأقلام ذات جودة عالية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'estimate_id' => 2,
                'request_item_id' => 2,
                'item_name' => 'دفاتر A4',
                'quantity' => 50,
                'unit_price' => 1200,
                'total_price' => 60000,
                'notes' => 'دفاتر غلاف صلب',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'estimate_id' => 3,
                'request_item_id' => 3,
                'item_name' => 'فلتر زيت للسيارة',
                'quantity' => 20,
                'unit_price' => 50000,
                'total_price' => 1000000,
                'notes' => 'فلتر أصلي',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('estimate_items')->insert($estimateItems);
    }
}