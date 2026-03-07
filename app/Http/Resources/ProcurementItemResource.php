<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcurementItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'item_name'      => $this->item_name,
            'quantity'       => $this->quantity,
            'unit_price'     => $this->unit_price,
            'purchase_price' => $this->purchase_price,
            'estimate_price' => $this->estimate_price,
            'total_price'    => $this->total_price,
            'difference'     => $this->difference,
            'notes'          => $this->notes,

            'unit'           => $this->whenLoaded('unit', fn() => [
                'id'   => $this->unit->id,
                'name' => $this->unit->name,
            ]),

            'estimate'       => $this->whenLoaded('estimate', fn() => [
                'id'           => $this->estimate->id,
                'vendor'       => $this->estimate->vendor?->name,
                'total_amount' => $this->estimate->total_amount,
            ]),

            'estimate_item'  => $this->whenLoaded('estimateItem', fn() => [
                'id'        => $this->estimateItem->id,
                'item_name' => $this->estimateItem->item_name,
                'quantity'  => $this->estimateItem->quantity,
            ]),

            'request_item'   => $this->whenLoaded('requestItem', fn() => [
                'id'        => $this->requestItem->id,
                'item_name' => $this->requestItem->item_name,
                'quantity'  => $this->requestItem->quantity,
            ]),

            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}