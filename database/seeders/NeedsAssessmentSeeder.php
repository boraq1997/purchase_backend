<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NeedsAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $needsAssessments = [
            [
                'purchase_request_id' => 1,
                'request_item_id' => 1,
                'assessed_by' => 3,
                'urgency_level' => 'medium',
                'needs_status' => 'needed',
                'quantity_needed_after_assessment' => 50,
                'modified_specifications' => null,
                'reason' => 'الكمية الحالية تغطي جزء من الاحتياج',
                'recommended_action' => 'شراء الباقي من السوق',
                'notes' => 'تم التقييم من قبل المشرف',
                'assessment_state' => 'final',
                'admin_decision' => 'approved',
                'admin_comment' => 'تم الموافقة على الشراء',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'purchase_request_id' => 1,
                'request_item_id' => 2,
                'assessed_by' => 3,
                'urgency_level' => 'high',
                'needs_status' => 'needed',
                'quantity_needed_after_assessment' => 30,
                'modified_specifications' => null,
                'reason' => 'الكمية المتوفرة غير كافية',
                'recommended_action' => 'شراء جديد',
                'notes' => 'المراجعة النهائية مطلوبة',
                'assessment_state' => 'final',
                'admin_decision' => 'approved',
                'admin_comment' => 'شراء عاجل',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'purchase_request_id' => 2,
                'request_item_id' => 3,
                'assessed_by' => 5,
                'urgency_level' => 'critical',
                'needs_status' => 'needed',
                'quantity_needed_after_assessment' => 20,
                'modified_specifications' => null,
                'reason' => 'الفلتر أساسي لصيانة المركبات',
                'recommended_action' => 'شراء جديد فوراً',
                'notes' => 'الأولوية القصوى',
                'assessment_state' => 'final',
                'admin_decision' => 'approved',
                'admin_comment' => 'تمت الموافقة',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('needs_assessments')->insert($needsAssessments);
    }
}