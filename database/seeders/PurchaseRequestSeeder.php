<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseRequestSeeder extends Seeder
{
    public function run(): void
    {
        // مثال على وحدات القياس
        $units = DB::table('units')->pluck('id', 'code');

        $requests = [
            [
                'request_number' => 'PR-0001',
                'department_id' => 2, // المخازن
                'committee_id' => 1,
                'user_id' => 2,
                'title' => 'شراء مستلزمات مكتبية',
                'description' => 'طلب شراء أقلام، دفاتر، وأوراق طباعة.',
                'total_estimated_cost' => 500000,
                'status_type' => 'pending',
                'priority' => 'medium',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'request_number' => 'PR-0002',
                'department_id' => 4, // الصيانة
                'committee_id' => 3,
                'user_id' => 4,
                'title' => 'شراء قطع غيار سيارات',
                'description' => 'قطع غيار للسيارات التابعة للكراج.',
                'total_estimated_cost' => 1500000,
                'status_type' => 'pending',
                'priority' => 'high',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('purchase_requests')->insert($requests);

        // إضافة عناصر الطلب
        $requestItems = [
            // عناصر PR-0001
            [
                'purchase_request_id' => 1,
                'item_name' => 'أقلام حبر',
                'specifications' => 'أقلام حبر زرقاء و سوداء، عالية الجودة',
                'quantity' => 100,
                'unit_id' => $units['pcs'] ?? null,
                'estimated_unit_price' => 500,
                'created_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'purchase_request_id' => 1,
                'item_name' => 'دفاتر A4',
                'specifications' => 'دفاتر 100 صفحة، غلاف صلب',
                'quantity' => 50,
                'unit_id' => $units['pcs'] ?? null,
                'estimated_unit_price' => 1200,
                'created_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // عناصر PR-0002
            [
                'purchase_request_id' => 2,
                'item_name' => 'فلتر زيت للسيارة',
                'specifications' => 'فلتر أصلي لجميع سيارات الكراج',
                'quantity' => 20,
                'unit_id' => $units['pcs'] ?? null,
                'estimated_unit_price' => 50000,
                'created_by' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'purchase_request_id' => 2,
                'item_name' => 'زيت محرك',
                'specifications' => 'زيت 10W-40 عالي الجودة',
                'quantity' => 40,
                'unit_id' => $units['liter'] ?? null,
                'estimated_unit_price' => 30000,
                'created_by' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('request_items')->insert($requestItems);
    }
}