<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $reports = [
            [
                'purchase_request_id' => 1,
                'report_type' => 'full_review',
                'data' => json_encode([
                    'summary' => 'مراجعة كاملة لجميع العناصر واحتياجاتها',
                    'items_reviewed' => [
                        ['item_name' => 'أقلام حبر', 'status' => 'approved', 'notes' => 'متوفر جزئياً، شراء الباقي'],
                        ['item_name' => 'دفاتر A4', 'status' => 'approved', 'notes' => 'كمية إضافية مطلوبة'],
                    ],
                    'recommendations' => [
                        'شراء الكمية المتبقية من الأقلام',
                        'شراء دفاتر إضافية',
                    ],
                ]),
                'file_path' => null,
                'generated_by' => 2,
                'generated_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'purchase_request_id' => 2,
                'report_type' => 'procurement_recommendation',
                'data' => json_encode([
                    'summary' => 'تقييم الاحتياجات والمخزون',
                    'items_reviewed' => [
                        ['item_name' => 'فلتر زيت للسيارة', 'status' => 'approved', 'notes' => 'شراء عاجل للورشة'],
                    ],
                    'recommendations' => [
                        'شراء الفلتر فوراً',
                    ],
                ]),
                'file_path' => null,
                'generated_by' => 4,
                'generated_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('reports')->insert($reports);
    }
}