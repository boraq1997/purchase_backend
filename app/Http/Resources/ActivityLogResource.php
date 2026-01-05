<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'actor' => [
                'type' => $this->actor_type,
                'id' => $this->actor_id,
                'name' => $this->actor_name,
                'role' => $this->actor_role,
            ],
            'action' => $this->action,
            'action_label' => $this->action_label,
            'status' => $this->status,
            'severity' => $this->severity,
            'subject' => [
                'type' => $this->subject_type,
                'id' => $this->subject_id,
                'identifier' => $this->subject_identifier,
            ],
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,
            'changed_fields' => $this->changed_fields,
            'module' => $this->module,
            'route' => $this->route,
            'method' => $this->method,
            'metadata' => $this->metadata,
            'duration_ms' => $this->duration_ms,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}