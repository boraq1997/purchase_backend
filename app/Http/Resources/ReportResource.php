<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'summary'           => $this->summary,
            'recommendations'   => $this->recommendations,
            'notes'             => $this->notes,
            'status'            => $this->status,

            // الطلب المرتبط بالتقرير
            'purchase_request'  => $this->whenLoaded('purchaseRequest', function () {
                return [
                    'id'             => $this->purchaseRequest->id,
                    'request_number' => $this->purchaseRequest->request_number,
                    'title'          => $this->purchaseRequest->title,
                ];
            }),

            // اللجنة المسؤولة عن التقرير
            'committee'         => $this->whenLoaded('committee', function () {
                return [
                    'id'   => $this->committee->id,
                    'name' => $this->committee->name,
                ];
            }),

            // المستخدم الذي أنشأ التقرير
            'created_by'        => $this->whenLoaded('createdBy', function () {
                return [
                    'id'       => $this->createdBy->id,
                    'name'     => $this->createdBy->name,
                    'username' => $this->createdBy->username,
                    'email'    => $this->createdBy->email,
                ];
            }),

            // المستخدم الذي اعتمد التقرير (إن وُجد)
            'approved_by'       => $this->whenLoaded('approvedBy', function () {
                return [
                    'id'       => $this->approvedBy->id,
                    'name'     => $this->approvedBy->name,
                    'username' => $this->approvedBy->username,
                    'email'    => $this->approvedBy->email,
                ];
            }),

            'created_at'        => $this->created_at?->toIso8601String(),
            'updated_at'        => $this->updated_at?->toIso8601String(),
        ];
    }
}
