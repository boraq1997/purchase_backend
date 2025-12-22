<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class SearchPermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('view-Permission');
    }

    public function rules(): array
    {
        return [
            'query'      => 'nullable|string|max:255',
            'guard_name' => 'nullable|string|in:web,sanctum,api',
            'order_by'   => 'nullable|in:name,created_at',
            'direction'  => 'nullable|in:asc,desc',
        ];
    }
}