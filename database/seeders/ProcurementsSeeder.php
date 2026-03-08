<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Procurement;
use App\Models\ProcurementItem;
use App\Models\PurchaseRequest;

class ProcurementsSeeder extends Seeder
{
    public function run(): void
    {
        $purchaseRequest = PurchaseRequest::first();

        $procurement = Procurement::firstOrCreate(
            [
                'purchase_request_id' => $purchaseRequest?->id,
                'reference_no'        => 'REF-2025-001',
            ],
            [
                'purchase_date' => now(),
                'total_amount'  => 4250,
                'status'        => 'completed',
                'notes'         => 'شراء أجهزة كمبيوتر وشاشات من شركة التقنية الحديثة.',
            ]
        );

        ProcurementItem::firstOrCreate(
            [
                'procurement_id' => $procurement->id,
                'item_name'      => 'حاسوب مكتبي',
            ],
            [
                'quantity'       => 5,
                'unit_price'     => 850,
                'purchase_price' => 850,
                'estimate_price' => 850,
                'notes'          => 'تم استلام الأجهزة كاملة.',
            ]
        );

        ProcurementItem::firstOrCreate(
            [
                'procurement_id' => $procurement->id,
                'item_name'      => 'شاشة 24 بوصة',
            ],
            [
                'quantity'       => 5,
                'unit_price'     => 100,
                'purchase_price' => 100,
                'estimate_price' => 100,
                'notes'          => 'تم استلام الشاشات كاملة.',
            ]
        );

        $this->command->info('✅ Procurements & Procurement Items seeded successfully!');
    }
}