<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
            'name'          => $this->name,
            'guard_name'    => $this->guard_name,

            // الصلاحيات المرتبطة بالدور
            'permissions'   => $this->whenLoaded('permissions', function () {
                return $this->permissions->map(fn($permission) => [
                    'id'   => $permission->id,
                    'name' => $permission->name,
                ]);
            }),

            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
