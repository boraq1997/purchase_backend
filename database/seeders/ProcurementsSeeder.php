<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Procurement;
use App\Models\ProcurementItem;
use App\Models\Estimate;
use App\Models\PurchaseRequest;

class ProcurementsSeeder extends Seeder
{
    public function run(): void
    {
        $estimate = Estimate::first();
        $purchaseRequest = PurchaseRequest::first();

        // ✅ استبدال purchase_order_number بـ reference_no
        $procurement = Procurement::firstOrCreate(
            [
                'estimate_id' => $estimate?->id,
                'purchase_request_id' => $purchaseRequest?->id,
                'reference_no' => 'REF-2025-001',
            ],
            [
                'purchase_date' => now(),
                'total_amount' => 4250,
                'status' => 'completed',
                'notes' => 'شراء أجهزة كمبيوتر وشاشات من شركة التقنية الحديثة.',
            ]
        );

        // ✅ أسماء الأعمدة الصحيحة لـ procurement_items
        ProcurementItem::firstOrCreate(
            [
                'procurement_id' => $procurement->id,
                'item_name' => 'حاسوب مكتبي',
            ],
            [
                'quantity' => 5,
                'unit_price' => 850,
                'unit' => 'قطعة',
                'difference' => 0,
                'notes' => 'تم استلام الأجهزة كاملة.',
            ]
        );

        ProcurementItem::firstOrCreate(
            [
                'procurement_id' => $procurement->id,
                'item_name' => 'شاشة 24 بوصة',
            ],
            [
                'quantity' => 5,
                'unit_price' => 100,
                'unit' => 'قطعة',
                'difference' => 0,
                'notes' => 'تم استلام الشاشات كاملة.',
            ]
        );

        $this->command->info('✅ Procurements & Procurement Items seeded successfully!');
    }
}