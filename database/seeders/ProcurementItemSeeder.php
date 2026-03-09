<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcurementItemSeeder extends Seeder
{
    public function run(): void
    {
        $units = DB::table('units')->pluck('id', 'code');

        $procurementItems = [
            // PO-0001
            [
                'procurement_id' => 1,
                'request_item_id' => 1,
                'estimate_item_id' => 1,
                'estimate_id' => 1,
                'item_name' => 'أقلام حبر',
                'unit_id' => $units['pcs'] ?? null,
                'quantity' => 100,
                'unit_price' => 500,
                'purchase_price' => 500,
                'estimate_price' => 500,
                'notes' => 'تم الشراء وفقاً للعرض',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'procurement_id' => 1,
                'request_item_id' => 2,
                'estimate_item_id' => 2,
                'estimate_id' => 2,
                'item_name' => 'دفاتر A4',
                'unit_id' => $units['pcs'] ?? null,
                'quantity' => 50,
                'unit_price' => 1200,
                'purchase_price' => 1200,
                'estimate_price' => 1200,
                'notes' => 'دفاتر غلاف صلب',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // PO-0002
            [
                'procurement_id' => 2,
                'request_item_id' => 3,
                'estimate_item_id' => 3,
                'estimate_id' => 3,
                'item_name' => 'فلتر زيت للسيارة',
                'unit_id' => $units['pcs'] ?? null,
                'quantity' => 20,
                'unit_price' => 50000,
                'purchase_price' => 50000,
                'estimate_price' => 50000,
                'notes' => 'فلتر أصلي للسيارات',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('procurement_items')->insert($procurementItems);
    }
}