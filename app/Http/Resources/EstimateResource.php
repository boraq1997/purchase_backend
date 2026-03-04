<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\EstimateImageResource;

class EstimateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,

            'vendor'        => $this->whenLoaded('vendor', function () {
                return [
                    'id'       => $this->vendor->id,
                    'name'     => $this->vendor->name,
                    'phone1'   => $this->vendor->phone1,
                    'phone2'   => $this->vendor->phone2,
                    'email'    => $this->vendor->email,
                    'address'  => $this->vendor->address,
                ];
            }),

            'estimate_date' => $this->estimate_date?->toIso8601String(),
            'total_amount'  => $this->total_amount,
            'status'        => $this->status,
            'notes'         => $this->notes,

            'purchase_request' => $this->whenLoaded('purchaseRequest', function () {
                return [
                    'id'             => $this->purchaseRequest->id,
                    'request_number' => $this->purchaseRequest->request_number,
                    'title'          => $this->purchaseRequest->title,
                    'priority'       => $this->purchaseRequest->priority,
                ];
            }),

            'request_item' => $this->request_item_id
                ? new RequestItemResource($this->whenLoaded('requestItem'))
                : ($this->relationLoaded('estimateItems') && $this->estimateItems->count() === 1
                    ? [
                        'id'        => $this->estimateItems[0]->request_item_id,
                        'item_name' => $this->estimateItems[0]->item_name,
                        'quantity'  => $this->estimateItems[0]->quantity,
                        'unit'      => $this->estimateItems[0]->requestItem->unit ?? null,
                    ]
                    : null
                ),

            'estimate_items' => $this->whenLoaded('estimateItems', function () {
                return $this->estimateItems->map(function ($item) {
                    return [
                        'id'          => $item->id,
                        'item_name'   => $item->item_name,
                        'quantity'    => $item->quantity,
                        'unit_price'  => $item->unit_price,
                        'total_price' => $item->total_price,
                        'notes'       => $item->notes,
                        'request_item'=> $item->requestItem ? [
                            'id'        => $item->requestItem->id,
                            'item_name' => $item->requestItem->item_name,
                            'quantity'  => $item->requestItem->quantity,
                            'unit'      => $item->requestItem->unit,
                        ] : null,
                    ];
                });
            }),
            'images' => $this->whenLoaded('images', function() {
                return EstimateImageResource::collection($this->images);
            }),

            'created_by' => $this->whenLoaded('creator', function () {
                return [
                    'id'       => $this->creator->id,
                    'fullname' => $this->creator->fullname,
                ];
            }),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}