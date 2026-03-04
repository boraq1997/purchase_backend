<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PurchaseRequestImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'purchase_request_id' => $this->purchase_request_id,
            'file_name' => $this->file_name,
            'file_url'            => $this->file_path ? asset('storage/'. $this->file_path) : null,
            'file_type' => $this->file_type,
            'file_size' => $this->file_size,
            'uploaded_by' => $this->uploaded_by,
            'created_at' => $this->created_at,
        ];
    }
}