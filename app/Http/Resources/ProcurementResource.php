<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcurementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'reference_no'       => $this->reference_no,
            'purchase_date'      => $this->purchase_date?->toIso8601String(),
            'invoice_number'     => $this->invoice_number,
            'invoice_date'       => $this->invoice_date?->toIso8601String(),
            'vendor_name'        => $this->vendor_name,
            'total_amount'       => $this->total_amount,
            'status'             => $this->status,
            'notes'              => $this->notes,

            // الطلب المرتبط بعملية الشراء
            'purchase_request'   => $this->whenLoaded('purchaseRequest', function () {
                return [
                    'id'             => $this->purchaseRequest->id,
                    'request_number' => $this->purchaseRequest->request_number,
                    'title'          => $this->purchaseRequest->title,
                ];
            }),

            // التقدير الذي تمت عليه عملية الشراء
            'estimate'           => $this->whenLoaded('estimate', function () {
                return [
                    'id'            => $this->estimate->id,
                    'vendor_name'   => $this->estimate->vendor_name,
                    'total_amount'  => $this->estimate->total_amount,
                ];
            }),

            // القسم أو اللجنة (إن كانت موجودة في العلاقة)
            'department'         => $this->whenLoaded('department', function () {
                return [
                    'id'   => $this->department->id,
                    'name' => $this->department->name,
                ];
            }),
            'committee'          => $this->whenLoaded('committee', function () {
                return [
                    'id'   => $this->committee->id,
                    'name' => $this->committee->name,
                ];
            }),

            // العناصر ضمن عملية الشراء
            'items'              => $this->whenLoaded('items', function () {
                return ProcurementItemResource::collection($this->items);
            }),

            // تواريخ النظام
            'created_at'         => $this->created_at?->toIso8601String(),
            'updated_at'         => $this->updated_at?->toIso8601String(),
        ];
    }
}
