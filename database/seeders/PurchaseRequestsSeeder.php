<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseRequest;
use App\Models\RequestItem;
use App\Models\User;
use App\Models\Department;

class PurchaseRequestsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $department = Department::first();

        // إنشاء طلب شراء
        $request = PurchaseRequest::firstOrCreate(
            [
                'title' => 'طلب شراء أجهزة كمبيوتر',
                'description' => 'شراء 5 أجهزة حاسوب للمكتب الفني',
                'user_id' => $user->id,
                'department_id' => $department->id,
            ],
            [
                'status_type' => 'pending',
                'priority' => 'high',
            ]
        );

        // المواد المطلوبة ضمن الطلب
        $items = [
            [
                'item_name' => 'حاسوب مكتبي',
                'quantity' => 5,
                'estimated_unit_price' => 800,
                'unit_id' => null,
            ],
            [
                'item_name' => 'شاشة 24 بوصة',
                'quantity' => 5,
                'estimated_unit_price' => 150,
                'unit_id' => null,
            ],
        ];

        foreach ($items as $item) {
            RequestItem::firstOrCreate(
                [
                    'purchase_request_id' => $request->id,
                    'item_name' => $item['item_name'],
                ],
                [
                    'specifications'       => $item['item_name'] . ' بمواصفات قياسية',
                    'quantity'             => $item['quantity'],
                    'unit_id'              => $item['unit_id'] ?? null, // ✅ هنا
                    'estimated_unit_price' => $item['estimated_unit_price'],
                    'total_estimated_price'=> $item['quantity'] * $item['estimated_unit_price'],
                    'created_by'           => $user->id,
                ]
            );
        }

        $this->command->info('✅ Purchase Requests & Items seeded successfully!');
    }
}