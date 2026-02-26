<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,

            'name'        => $this->name,
            'code'        => $this->code,
            'description' => $this->description,

            'created_at'  => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updated_at?->format('Y-m-d H:i:s'),

            // عدد العناصر المرتبطة (اختياري – يظهر فقط عند تحميل العلاقة)
            'request_items_count' => $this->whenCounted('requestItems'),
        ];
    }
}