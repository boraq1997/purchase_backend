<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\PurchaseRequest;
use App\Models\Vendor; // صححنا الاسم هنا

class EstimatesSeeder extends Seeder
{
    public function run(): void
    {
        $request = PurchaseRequest::first();

        // إنشاء أو جلب المورد
        $vendor = Vendor::firstOrCreate(
            ['name' => 'شركة التقنية الحديثة'],
            [
                'phone1' => '07700000000',
                'phone2' => null, // إذا لا يوجد رقم ثاني
                'email' => 'info@tech.com',
                'address' => 'بغداد - الكرادة',
                'created_by' => null, // أو يمكنك وضع User::first()->id
            ]
        );

        // إنشاء أو جلب Estimate
        $estimate = Estimate::firstOrCreate(
            [
                'purchase_request_id' => $request->id,
                'vendor_id' => $vendor->id,
            ],
            [
                'estimate_date' => now(),
                'total_amount' => 4750,
                'notes' => 'عرض سعر من شركة التقنية الحديثة يشمل التوصيل.',
                'status' => 'pending',
            ]
        );

        // إنشاء عناصر Estimate
        $items = [
            ['item_name' => 'حاسوب مكتبي', 'quantity' => 5, 'unit_price' => 850],
            ['item_name' => 'شاشة 24 بوصة', 'quantity' => 5, 'unit_price' => 100],
        ];

        foreach ($items as $item) {
            EstimateItem::firstOrCreate(
                [
                    'estimate_id' => $estimate->id,
                    'item_name' => $item['item_name'],
                ],
                [
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'notes' => 'عرض سعر تقريبي من المورد.',
                ]
            );
        }

        $this->command->info('✅ Estimates & Estimate Items seeded successfully!');
    }
}