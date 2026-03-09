<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcurementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'reference_no'     => $this->reference_no,
            'purchase_date'    => $this->purchase_date?->toIso8601String(),
            'total_amount'     => $this->total_amount,
            'status'           => $this->status,
            'notes'            => $this->notes,

            'purchase_request' => $this->whenLoaded('purchaseRequest', function () {
                return [
                    'id'             => $this->purchaseRequest->id,
                    'request_number' => $this->purchaseRequest->request_number,
                    'title'          => $this->purchaseRequest->title,
                    'description'    => $this->purchaseRequest->description,

                    'department' => $this->purchaseRequest->department ? [
                        'id'   => $this->purchaseRequest->department->id,
                        'name' => $this->purchaseRequest->department->name,
                    ] : null,
                ];
            }),

            'items'            => $this->whenLoaded('items',
                fn() => ProcurementItemResource::collection($this->items)
            ),

            'created_by'       => $this->whenLoaded('creator', fn() => [
                'id'       => $this->creator->id,
                'fullname' => $this->creator->fullname,
            ]),

            'created_at'       => $this->created_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),
        ];
    }
}