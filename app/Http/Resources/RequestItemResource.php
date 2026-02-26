<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'item_name'           => $this->item_name,
            'description'         => $this->description,
            'quantity'            => $this->quantity,
            'unit' => $this->whenLoaded('unit', function () {
                return [
                    'id'   => $this->unit->id,
                    'name' => $this->unit->name,
                    'code' => $this->unit->code,
                ];
            }),
            'unit_price'          => $this->unit_price,
            'total'               => $this->total,

            // الطلب المرتبط بهذا العنصر
            'purchase_request'    => $this->whenLoaded('purchaseRequest', function () {
                return [
                    'id'             => $this->purchaseRequest->id,
                    'request_number' => $this->purchaseRequest->request_number,
                    'title'          => $this->purchaseRequest->title,
                ];
            }),

            // التقديرات الخاصة بالعنصر
            'estimate_items'      => $this->whenLoaded('estimateItems', function () {
                return $this->estimateItems->map(fn($estimateItem) => [
                    'id'          => $estimateItem->id,
                    'supplier'    => $estimateItem->estimate?->supplier_name,
                    'unit_price'  => $estimateItem->unit_price,
                    'total'       => $estimateItem->total,
                ]);
            }),

            // عمليات الشراء الفعلية الخاصة بهذا العنصر
            'procurement_items'   => $this->whenLoaded('procurementItems', function () {
                return $this->procurementItems->map(fn($procItem) => [
                    'id'           => $procItem->id,
                    'procurement'  => [
                        'id'           => $procItem->procurement->id,
                        'reference_no' => $procItem->procurement->reference_no,
                    ],
                    'purchased_qty' => $procItem->purchased_quantity,
                    'unit_cost'     => $procItem->unit_cost,
                    'total_cost'    => $procItem->total_cost,
                ]);
            }),

            // فحص المستودع لهذا العنصر (إن وجد)
            'warehouse_check'     => $this->whenLoaded('warehouseCheck', function () {
                return [
                    'id'                 => $this->warehouseCheck->id,
                    'availability'       => $this->warehouseCheck->availability,
                    'available_quantity' => $this->warehouseCheck->available_quantity,
                    'recommendation'     => $this->warehouseCheck->recommendation,
                ];
            }),

            'created_at'          => $this->created_at?->toIso8601String(),
            'updated_at'          => $this->updated_at?->toIso8601String(),
        ];
    }
}
