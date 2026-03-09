<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseCheckSeeder extends Seeder
{
    public function run(): void
    {
        $warehouseChecks = [
            [
                'purchase_request_id' => 1,
                'request_item_id' => 1,
                'availability' => 'available',
                'item_condition' => 'new',
                'available_quantity' => 50,
                'recommendation' => 'provide_from_stock',
                'notes' => '50 قلم متوفرة في المخزن',
                'checked_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'purchase_request_id' => 1,
                'request_item_id' => 2,
                'availability' => 'partial',
                'item_condition' => 'new',
                'available_quantity' => 20,
                'recommendation' => 'purchase_new',
                'notes' => 'متوفر جزئياً في المخزن',
                'checked_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'purchase_request_id' => 2,
                'request_item_id' => 3,
                'availability' => 'unavailable',
                'item_condition' => null,
                'available_quantity' => 0,
                'recommendation' => 'purchase_new',
                'notes' => 'لا توجد مخزونات متوفرة',
                'checked_by' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('warehouse_checks')->insert($warehouseChecks);
    }
}