<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcurementItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'item_name'      => $this->item_name,
            'unit'           => $this->unit,
            'quantity'       => $this->quantity,
            'unit_price'     => $this->unit_price,
            'total_price'    => $this->total_price,
            'difference'     => $this->difference,
            'notes'          => $this->notes,

            // عملية الشراء المرتبطة
            'procurement'    => $this->whenLoaded('procurement', function () {
                return [
                    'id'             => $this->procurement->id,
                    'reference_no'   => $this->procurement->reference_no,
                    'vendor_name'    => $this->procurement->vendor_name,
                    'purchase_date'  => $this->procurement->purchase_date?->toIso8601String(),
                ];
            }),

            // المادة الأصلية المرتبطة من الطلب
            'request_item'   => $this->whenLoaded('requestItem', function () {
                return [
                    'id'          => $this->requestItem->id,
                    'item_name'   => $this->requestItem->item_name,
                    'quantity'    => $this->requestItem->quantity,
                    'unit'        => $this->requestItem->unit,
                ];
            }),

            // العنصر المقابل في التقدير (إن وجد)
            'estimate_item'  => $this->whenLoaded('estimateItem', function () {
                return [
                    'id'          => $this->estimateItem->id,
                    'unit_price'  => $this->estimateItem->unit_price,
                    'total'       => $this->estimateItem->total,
                ];
            }),

            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
