<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;
use App\Models\PurchaseRequest;
use App\Models\User;

class ReportsSeeder extends Seeder
{
    public function run(): void
    {
        $request = PurchaseRequest::first();
        $user = User::first();

        Report::firstOrCreate(
            [
                'purchase_request_id' => $request->id,
            ],
            [
                'report_type' => 'procurement_recommendation',
                'data' => json_encode([
                    'summary' => 'تمت الموافقة على شراء الأجهزة المطلوبة بعد تقييم العروض.',
                    'recommendations' => 'الموافقة على التوريد من شركة التقنية الحديثة.',
                ], JSON_UNESCAPED_UNICODE),
                'file_path' => null,
                'generated_by' => $user->id,
                'generated_at' => now(),
            ]
        );

        $this->command->info('✅ Reports seeded successfully!');
    }
}