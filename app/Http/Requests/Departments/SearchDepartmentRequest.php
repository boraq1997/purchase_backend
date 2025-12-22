<?php

namespace App\Http\Requests\Departments;

use Illuminate\Foundation\Http\FormRequest;

class SearchDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('view-Department');
    }

    public function rules(): array
    {
        return [
            'query' => 'nullable|string|max:255',
            'manager_user_id' => 'nullable|exists:users,id',
            'order_by' => 'nullable|in:name,code,created_at',
            'direction' => 'nullable|in:asc,desc',
        ];
    }
}