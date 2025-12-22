<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username'      => $this->username,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'status'        => $this->status,
            
            // القسم
            'department'    => $this->department ? [
                'id'    => $this->department->id,
                'name'  => $this->department->name,
            ] : null,

            // المشرف
            'parent' => new UserResource($this->whenLoaded('parent')),

            // الأدوار والصلاحيات
            'role' => $this->whenLoaded('roles', fn() => $this->getRoleNames()->first()),
            'permissions'   => $this->whenLoaded('permissions', fn() => $this->getAllPermissions()->pluck('name')),

            // اللجان المرتبطة بالمستخدم
            'committees'    => $this->whenLoaded('committees', function () {
                return $this->committees->map(fn($committee) => [
                    'id'            => $committee->id,
                    'name'          => $committee->name,
                    'description'   => $committee->description,
                    'department'    => $committee->department ? [
                        'id'   => $committee->department->id,
                        'name' => $committee->department->name,
                    ] : null,
                    'manager'       => $committee->manager ? [
                        'id'    => $committee->manager->id,
                        'name'  => $committee->manager->name,
                        'email' => $committee->manager->email,
                        'phone' => $committee->manager->phone,
                    ] : null,
                ]);
            }),

            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
