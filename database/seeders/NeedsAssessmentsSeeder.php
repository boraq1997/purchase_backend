<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NeedsAssessment;
use App\Models\RequestItem;
use App\Models\PurchaseRequest;
use App\Models\User;

class NeedsAssessmentsSeeder extends Seeder
{
    public function run(): void
    {
        $item = RequestItem::first();
        $purchaseRequest = $item?->purchaseRequest;
        $user = User::first();

        NeedsAssessment::firstOrCreate(
            [
                'request_item_id' => $item->id,
            ],
            [
                'purchase_request_id' => $purchaseRequest?->id,
                'needs_status' => 'needed', // ✅ العمود الصحيح
                'reason' => 'المادة ضرورية لتجهيز المكتب الفني الجديد.', // ✅ بدل justification
                'modified_specifications' => null,
                'assessed_by' => $user->id,
            ]
        );

        $this->command->info('✅ Needs Assessments seeded successfully!');
    }
}