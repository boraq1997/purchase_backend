<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class SearchUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('view-User');
    }

    public function rules(): array
    {
        return [
            'query'         => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'status'        => 'nullable|in:active,inactive',
            'role'          => 'nullable|string|exists:roles,name',
            'order_by'      => 'nullable|in:name,email,created_at',
            'direction'     => 'nullable|in:asc,desc',
        ];
    }
}