<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'code'          => $this->code,
            'description'   => $this->description,

            // مدير القسم
            'manager'       => $this->whenLoaded('manager', function () {
                return [
                    'id'        => $this->manager->id,
                    'name'      => $this->manager->name,
                    'username'  => $this->manager->username,
                    'email'     => $this->manager->email,
                    'phone'     => $this->manager->phone,
                ];
            }),

            // المستخدمون المرتبطون بالقسم
            'users'         => $this->whenLoaded('users', function () {
                return $this->users->map(fn($user) => [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'username'  => $user->username,
                    'email'     => $user->email,
                    'phone'     => $user->phone,
                    'status'    => $user->status,
                ]);
            }),

            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}