<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcurementSeeder extends Seeder
{
    public function run(): void
    {
        $procurements = [
            [
                'purchase_request_id' => 1,
                'reference_no' => 'PO-0001',
                'purchase_date' => now(),
                'total_amount' => 110000,
                'status' => 'in_progress',
                'notes' => 'شراء المستلزمات المكتبية من الموردين',
                'created_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'purchase_request_id' => 2,
                'reference_no' => 'PO-0002',
                'purchase_date' => now(),
                'total_amount' => 1000000,
                'status' => 'in_progress',
                'notes' => 'شراء قطع غيار السيارات',
                'created_by' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('procurements')->insert($procurements);
    }
}