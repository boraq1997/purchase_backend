<?php

namespace App\Services;

use App\Models\NeedsAssessment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogService;

class NeedsAssessmentService
{

    protected ActivityLogService $logService;

    public function __construct() {
        $this->logService = new ActivityLogService();
    }

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

        $result = $q->latest('id')->get();

        $this->logService->log(
            action: 'view_needs_assessments',
            actionLabel: 'عرض جميع تقييمات الحاجة',
            subjectType: NeedsAssessment::class,
            metadata: [
                'filters' => $filters,
                'result_count' => count($result),
            ],
            module: 'تقييمات الحاجة'
        );

        return $result;
    }

    /**
     * جلب تقييم واحد مع العلاقات
     */
    public function getById(NeedsAssessment $assessment): NeedsAssessment
    {
        $assessment = $assessment->load([
            'purchaseRequest',
            'requestItem',
            'assessedBy',
        ]);

        $this->logService->log(
            action: 'view_needs_assessment',
            actionLabel: 'عرض تقييم حاجة محدد',
            subjectType: NeedsAssessment::class,
            subjectId: $assessment->id,
            module: 'تقييمات الحاجة'
        );

        return $assessment;
    }

    public function getByItemAndRequest(int $purchaseRequestId, int $requestItemId): ?NeedsAssessment {
        $assessment = NeedsAssessment::with([
            'purchaseRequest',
            'requestItem',
            'assessedBy',
        ])
        ->where('purchase_request_id', $purchaseRequestId)
        ->where('request_item_id', $requestItemId)
        ->first();

        if ($assessment) {
            $this->logService->log(
                action: 'view_needs_assessment_by_item_request',
                actionLabel: 'عرض تقييم حاجة حسب الطلب والمادة',
                subjectType: NeedsAssessment::class,
                subjectId: $assessment->id,
                metadata: [
                    'purchase_request_id' => $purchaseRequestId,
                    'request_item_id' => $requestItemId,
                ],
                module: 'تقييمات الحاجة'
            );
        }

        return $assessment;
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

            $this->logService->log(
                action: 'create_needs_assessment',
                actionLabel: 'إنشاء تقييم حاجة جديد',
                subjectType: NeedsAssessment::class,
                subjectId: $assessment->id,
                newValues: $assessment->toArray(),
                module: 'تقييمات الحاجة'
            );

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
            $oldValues = $assessment->toArray();

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

            $newValues = $assessment->toArray();
            $changedFields = array_keys(array_diff_assoc($newValues, $oldValues));

            $this->logService->log(
                action: 'update_needs_assessment',
                actionLabel: 'تحديث تقييم حاجة',
                subjectType: NeedsAssessment::class,
                subjectId: $assessment->id,
                oldValues: $oldValues,
                newValues: $newValues,
                changedFields: $changedFields,
                module: 'تقييمات الحاجة'
            );

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
        return DB::transaction(function () use ($assessment) {
            $oldValues = $assessment->toArray();
            $deleted = $assessment->delete();

            $this->logService->log(
                action: 'delete_needs_assessment',
                actionLabel: 'حذف تقييم حاجة',
                subjectType: NeedsAssessment::class,
                subjectId: $assessment->id,
                oldValues: $oldValues,
                status: $deleted ? 'success' : 'failed',
                module: 'تقييمات الحاجة'
            );

            return $deleted;
        });
    }
}