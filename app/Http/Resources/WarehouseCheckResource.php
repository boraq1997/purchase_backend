<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseCheckResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'request_item_id' => $this->request_item_id,
            'id'                 => $this->id,
            'availability'        => $this->availability,
            'condition'           => $this->condition,
            'available_quantity'  => $this->available_quantity,
            'recommendation'      => $this->recommendation,
            'notes'               => $this->notes,

            // الطلب المرتبط بالفحص
            'purchase_request'    => $this->whenLoaded('purchaseRequest', function () {
                return [
                    'id'             => $this->purchaseRequest->id,
                    'request_number' => $this->purchaseRequest->request_number,
                    'title'          => $this->purchaseRequest->title,
                ];
            }),

            // المادة المطلوبة ضمن الطلب
            'request_item'        => $this->whenLoaded('requestItem', function () {
                return [
                    'id'         => $this->requestItem->id,
                    'item_name'  => $this->requestItem->item_name,
                    'quantity'   => $this->requestItem->quantity,
                    'unit'       => $this->requestItem->unit,
                ];
            }),

            // المستخدم الذي قام بالفحص
            'checked_by'          => $this->whenLoaded('checkedBy', function () {
                return [
                    'id'       => $this->checkedBy->id,
                    'name'     => $this->checkedBy->name,
                    'username' => $this->checkedBy->username,
                    'email'    => $this->checkedBy->email,
                ];
            }),

            'created_at'          => $this->created_at?->toIso8601String(),
            'updated_at'          => $this->updated_at?->toIso8601String(),
        ];
    }
}
