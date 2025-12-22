<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstimateItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'item_name'       => $this->item_name,
            'quantity'        => $this->quantity,
            'unit_price'      => $this->unit_price,
            'total_price'     => $this->total_price,
            'notes'           => $this->notes,

            // التقدير المرتبط بهذا العنصر
            'estimate'        => $this->whenLoaded('estimate', function () {
                return [
                    'id'            => $this->estimate->id,
                    'vendor_name'   => $this->estimate->vendor_name,
                    'total_amount'  => $this->estimate->total_amount,
                    'status'        => $this->estimate->status,
                ];
            }),

            // المادة الأصلية من طلب الشراء (إن وجدت)
            'request_item'    => $this->whenLoaded('requestItem', function () {
                return [
                    'id'         => $this->requestItem->id,
                    'item_name'  => $this->requestItem->item_name,
                    'quantity'   => $this->requestItem->quantity,
                    'unit'       => $this->requestItem->unit,
                ];
            }),

            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
        ];
    }
}