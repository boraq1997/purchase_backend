<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NeedsAssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'request_item_id'                  => $this->request_item_id,

            'id'                               => $this->id,

            // مستوى الإلحاح
            'urgency_level'                    => $this->urgency_level,

            // قرار التقييم
            'needs_status'                     => $this->needs_status,

            // الكمية بعد التقييم
            'quantity_needed_after_assessment' => $this->quantity_needed_after_assessment,

            // مواصفات معدلة
            'modified_specifications'          => $this->modified_specifications,

            // سبب الحاجة / قرار عدم الحاجة
            'reason'                           => $this->reason,

            // الإجراء المقترح (شراء، استبدال، صيانة...)
            'recommended_action'               => $this->recommended_action,

            // ملاحظات إضافية
            'notes'                            => $this->notes,

            // حالة التقييم
            'assessment_state'                 => $this->assessment_state,

            // قرار الإدارة
            'admin_decision'                   => $this->admin_decision,
            'admin_comment'                    => $this->admin_comment,

            // الطلب المرتبط
            'purchase_request' => $this->whenLoaded('purchaseRequest', function () {
                return [
                    'id'             => $this->purchaseRequest->id,
                    'request_number' => $this->purchaseRequest->request_number,
                    'title'          => $this->purchaseRequest->title,
                ];
            }),

            // العنصر المرتبط بالتقييم
            'request_item' => $this->whenLoaded('requestItem', function () {
                return [
                    'id'       => $this->requestItem->id,
                    'item_name'=> $this->requestItem->item_name,
                    'quantity' => $this->requestItem->quantity,
                    'unit'     => $this->requestItem->unit,
                ];
            }),

            // الموظف الذي قام بالتقييم
            'assessed_by' => $this->whenLoaded('assessedBy', function () {
                return [
                    'id'       => $this->assessedBy->id,
                    'name'     => $this->assessedBy->name,
                    'username' => $this->assessedBy->username,
                    'email'    => $this->assessedBy->email,
                ];
            }),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}