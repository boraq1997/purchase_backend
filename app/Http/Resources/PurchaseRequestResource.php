<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\NeedsAssessmentResource;

class PurchaseRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'request_number'        => $this->request_number,
            'title'                 => $this->title,
            'description'           => $this->description,
            'priority'              => $this->priority,
            'total_estimated_cost'  => $this->total_estimated_cost,

            // =======================
            // Relations (Safe Loading)
            // =======================

            'department' => $this->whenLoaded('department', function () {
                return [
                    'id'    => $this->department->id,
                    'name'  => $this->department->name,
                ];
            }),

            'committee' => $this->whenLoaded('committee', function () {
                return [
                    'id'    => $this->committee->id,
                    'name'  => $this->committee->name,
                ];
            }),

            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id'        => $this->creator->id,
                    'name'      => $this->creator->name,
                    'username'  => $this->creator->username,
                    'email'     => $this->creator->email,
                    'phone'     => $this->creator->phone,
                ];
            }),

            'status_type'       => $this->status_type,
            'status_role'       => $this->status_role,
            'status_action_by'  => $this->whenLoaded('statusActor', function () {
                return [
                    'id'        => $this->statusActor->id,
                    'name'      => $this->statusActor->name,
                    'username'  => $this->statusActor->username,
                ];
            }),
            'status_date'       => $this->status_date?->toIso8601String(),
            'rejected_reason'   => $this->rejected_reason,
            'closed_at'         => $this->closed_at?->toIso8601String(),

            // =======================
            // Items (with nested relations)
            // =======================
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id'                        => $item->id,
                        'item_name'                 => $item->item_name,
                        'quantity'                  => $item->quantity,
                        'unit'                      => $item->unit,
                        'estimated_unit_price'      => $item->estimated_unit_price,
                        'total_estimated_price'     => $item->total_estimated_price,

                        'needs_assessment'          => $this->formatNeedsAssessment($item),
                        'warehouse_check'           => $this->formatWarehouseCheck($item),

                        // عرض كل الـ estimateItems
                        'estimate'            => $item->estimateItems->map(function ($ei) {
                            return [
                                'id'             => $ei->id,
                                'item_name'      => $ei->item_name,
                                'quantity'       => $ei->quantity,
                                'unit_price'     => $ei->unit_price,
                                'total_price'    => $ei->total_price,
                                'notes'          => $ei->notes,
                                'estimate' => $ei->estimate ? [
    'id'        => $ei->estimate->id,
    'vendor'    => $ei->estimate->vendor ? [
        'id'      => $ei->estimate->vendor->id,
        'name'    => $ei->estimate->vendor->name,
        'phone1'  => $ei->estimate->vendor->phone1,
        'phone2'  => $ei->estimate->vendor->phone2,
        'email'   => $ei->estimate->vendor->email,
        'address' => $ei->estimate->vendor->address,
    ] : null,
    'estimate_date' => $ei->estimate->estimate_date,
    'total_amount'  => $ei->estimate->total_amount,
    'status'        => $ei->estimate->status,
    'notes'         => $ei->estimate->notes,
] : null,
                            ];
                        }),
                    ];
                });
            }),

            // =======================
            // علاقات ثانوية خارج الـ items
            // =======================
            'estimates' => $this->whenLoaded('estimates', function() {
    return $this->estimates->map(function($est) {
        return [
            'id' => $est->id,
            'vendor_id' => $est->vendor_id,
            'vendor' => $est->vendor ? [
                'id'      => $est->vendor->id,
                'name'    => $est->vendor->name,
                'phone1'  => $est->vendor->phone1,
                'phone2'  => $est->vendor->phone2,
                'email'   => $est->vendor->email,
                'address' => $est->vendor->address,
            ] : null,
            'estimate_date' => $est->estimate_date?->toIso8601String(),
            'total_amount'  => $est->total_amount,
            'status'        => $est->status,
            'notes'         => $est->notes,
        ];
    });
}),

            'procurements' => $this->whenLoaded('procurements', fn() =>
                $this->procurements->map(fn($proc) => [
                    'id'            => $proc->id,
                    'reference_no'  => $proc->reference_no,
                    'supplier'      => $proc->supplier_name,
                    'total_cost'    => $proc->total_cost,
                    'status'        => $proc->status,
                ])
            ),

            'warehouse_checks' => $this->whenLoaded('warehouseChecks', fn() =>
                $this->warehouseChecks->map(fn($check) => [
                    'id'                => $check->id,
                    'request_item_id'   => $check->request_item_id,
                    'availability'      => $check->availability,
                    'available_quantity'=> $check->available_quantity,
                    'recommendation'    => $check->recommendation,
                    'notes'             => $check->notes,
                    'condition'         => $check->condition,
                    'checked_by'        => $check->checked_by,
                ])
            ),

            'needs_assessments' => $this->whenLoaded(
                'needsAssessments',
                fn() => NeedsAssessmentResource::collection($this->needsAssessments)
            ),

            'report' => $this->whenLoaded('report', fn() => [
                'id'                => $this->report->id,
                'summary'           => $this->report->summary,
                'recommendations'   => $this->report->recommendations,
            ]),

            // =======================
            // Timestamps
            // =======================
            'created_at'            => $this->created_at?->toIso8601String(),
            'updated_at'            => $this->updated_at?->toIso8601String(),
        ];
    }

    // ===============================
    // Helper functions
    // ===============================
    private function formatNeedsAssessment($item)
    {
        $na = $item->needsAssessment;

        if (!$na) return null;

        return [
            'id'                                => $na->id,
            'urgency_level'                     => $na->urgency_level,
            'needs_status'                      => $na->needs_status,
            'quantity_needed_after_assessment'  => $na->quantity_needed_after_assessment,
            'modified_specifications'           => $na->modified_specifications,
            'reason'                            => $na->reason,
            'recommended_action'                => $na->recommended_action,
            'notes'                              => $na->notes,
            'admin_decision'                    => $na->admin_decision,
            'admin_comment'                     => $na->admin_comment,
            'assessed_by'                       => $na->assessedBy?->only(['id','name']),
        ];
    }

    private function formatWarehouseCheck($item)
    {
        $wc = $item->warehouseCheck;

        if (!$wc) return null;

        return [
            'id'                => $wc->id,
            'availability'      => $wc->availability,
            'available_quantity'=> $wc->available_quantity,
            'condition'         => $wc->condition,
            'recommendation'    => $wc->recommendation,
            'notes'             => $wc->notes,
            'checked_by'        => $wc->checkedBy?->only(['id','name']),
        ];
    }
}