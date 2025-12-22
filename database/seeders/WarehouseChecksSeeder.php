<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WarehouseCheck;
use App\Models\RequestItem;
use App\Models\PurchaseRequest;
use App\Models\User;

class WarehouseChecksSeeder extends Seeder
{
    public function run(): void
    {
        $item = RequestItem::first();
        $user = User::first();
        $purchaseRequest = $item?->purchaseRequest ?? null;

        WarehouseCheck::firstOrCreate(
            [
                'request_item_id' => $item->id,
            ],
            [
                'purchase_request_id' => $purchaseRequest?->id,
                'availability' => 'available', // ✅ enum value
                'item_condition' => 'new', // ✅ من القيم المسموح بها
                'available_quantity' => 2,
                'recommendation' => 'provide_from_stock', // ✅ enum موجود فعلاً
                'notes' => 'المادة متوفرة في المستودع وبحالة ممتازة.',
                'checked_by' => $user->id,
            ]
        );

        $this->command->info('✅ Warehouse Checks seeded successfully!');
    }
}