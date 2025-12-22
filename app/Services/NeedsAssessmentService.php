<?php

namespace App\Services;

use App\Models\NeedsAssessment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class NeedsAssessmentService
{
    /**
     * جلب جميع التقييمات مع الفلترة
     */
    public function getAll(array $filters = []): Collection
    {
        $q = NeedsAssessment::with([
            'purchaseRequest',
            'requestItem',
            'assessedBy',
        ]);

        if (!empty($filters['purchase_request_id'])) {
            $q->where('purchase_request_id', $filters['purchase_request_id']);
        }

        if (!empty($filters['request_item_id'])) {
            $q->where('request_item_id', $filters['request_item_id']);
        }

        if (!empty($filters['urgency_level'])) {
            $q->where('urgency_level', $filters['urgency_level']);
        }

        if (!empty($filters['needs_status'])) {
            $q->where('needs_status', $filters['needs_status']);
        }

        if (!empty($filters['assessment_state'])) {
            $q->where('assessment_state', $filters['assessment_state']);
        }

        if (!empty($filters['admin_decision'])) {
            $q->where('admin_decision', $filters['admin_decision']);
        }

        return $q->latest('id')->get();
    }

    /**
     * جلب تقييم واحد مع العلاقات
     */
    public function getById(NeedsAssessment $assessment): NeedsAssessment
    {
        return $assessment->load([
            'purchaseRequest',
            'requestItem',
            'assessedBy',
        ]);
    }

    public function getByItemAndRequest(int $purchaseRequestId, int $requestItemId): ?NeedsAssessment {
        return NeedsAssessment::with([
            'purchaseRequest',
            'requestItem',
            'assessedBy',
        ])
        ->where('purchase_request_id', $purchaseRequestId)
        ->where('request_item_id', $requestItemId)
        ->first();
    }

    /**
     * إنشاء تقييم جديد
     */
    public function create(array $data): NeedsAssessment
    {
        return DB::transaction(function () use ($data) {

            $assessment = NeedsAssessment::create([
                'purchase_request_id' => $data['purchase_request_id'],
                'request_item_id'     => $data['request_item_id'],

                'urgency_level'       => $data['urgency_level'],
                'needs_status'        => $data['needs_status'],

                'quantity_needed_after_assessment' => $data['quantity_needed_after_assessment'] ?? null,

                'modified_specifications' => $data['modified_specifications'] ?? null,
                'reason'                  => $data['reason'] ?? null,
                'recommended_action'      => $data['recommended_action'] ?? null,
                'notes'                   => $data['notes'] ?? null,

                'assessment_state'   => $data['assessment_state'],
                'assessed_by'        => $data['assessed_by'],

                'admin_decision'     => $data['admin_decision'] ?? 'pending',
                'admin_comment'      => $data['admin_comment'] ?? null,
            ]);

            return $assessment->load([
                'purchaseRequest',
                'requestItem',
                'assessedBy',
            ]);
        });
    }

    /**
     * تحديث تقييم موجود
     */
    public function update(NeedsAssessment $assessment, array $data): NeedsAssessment
    {
        return DB::transaction(function () use ($assessment, $data) {

            $assessment->update([
                'purchase_request_id' => $data['purchase_request_id'] ?? $assessment->purchase_request_id,
                'request_item_id'     => $data['request_item_id'] ?? $assessment->request_item_id,

                'urgency_level'       => $data['urgency_level'] ?? $assessment->urgency_level,
                'needs_status'        => $data['needs_status'] ?? $assessment->needs_status,

                'quantity_needed_after_assessment' =>
                    $data['quantity_needed_after_assessment'] ?? $assessment->quantity_needed_after_assessment,

                'modified_specifications' => $data['modified_specifications'] ?? $assessment->modified_specifications,
                'reason'                  => $data['reason'] ?? $assessment->reason,
                'recommended_action'      => $data['recommended_action'] ?? $assessment->recommended_action,
                'notes'                   => $data['notes'] ?? $assessment->notes,

                'assessment_state'   => $data['assessment_state'] ?? $assessment->assessment_state,
                'assessed_by'        => $data['assessed_by'] ?? $assessment->assessed_by,

                'admin_decision'     => $data['admin_decision'] ?? $assessment->admin_decision,
                'admin_comment'      => $data['admin_comment'] ?? $assessment->admin_comment,
            ]);

            return $assessment->load([
                'purchaseRequest',
                'requestItem',
                'assessedBy',
            ]);
        });
    }

    /**
     * حذف تقييم
     */
    public function delete(NeedsAssessment $assessment): bool
    {
        return DB::transaction(fn () => $assessment->delete());
    }
}