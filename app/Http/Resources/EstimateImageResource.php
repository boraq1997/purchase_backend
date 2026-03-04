<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstimateImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'estimate_id' => $this->estimate_id,
            'file_name' => $this->file_name,
            'file_url' => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'file_type' => $this->file_type,
            'file_size' => $this->file_size,
            'uploaded_by' => $this->uploaded_by,
            'created_at' => $this->created_at,
        ];
    }
}
