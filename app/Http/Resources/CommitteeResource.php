<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommitteeResource extends JsonResource
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
            'description'   => $this->description,

            // القسم التابع للجنة
            'department'    => $this->whenLoaded('department', function () {
                return [
                    'id'   => $this->department->id,
                    'name' => $this->department->name,
                ];
            }),

            // مدير اللجنة
            'manager'       => $this->whenLoaded('manager', function () {
                return [
                    'id'        => $this->manager->id,
                    'name'      => $this->manager->name,
                    'username'  => $this->manager->username,
                    'email'     => $this->manager->email,
                    'phone'     => $this->manager->phone,
                ];
            }),

            // الأعضاء ضمن اللجنة
            'users'         => $this->whenLoaded('users', function () {
                return $this->users->map(fn($user) => [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'username'  => $user->username,
                    'email'     => $user->email,
                    'phone'     => $user->phone,
                    'department'=> $user->department ? [
                        'id'   => $user->department->id,
                        'name' => $user->department->name,
                    ] : null,
                ]);
            }),

            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
