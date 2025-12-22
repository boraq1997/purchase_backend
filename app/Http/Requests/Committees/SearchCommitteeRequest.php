<?php

namespace App\Http\Requests\Committees;

use Illuminate\Foundation\Http\FormRequest;

class SearchCommitteeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('view-Committees');
    }

    public function rules(): array
    {
        return [
            'query'          => 'nullable|string|max:255',
            'department_id'  => 'nullable|exists:departments,id',
            'manager_user_id'=> 'nullable|exists:users,id',
            'order_by'       => 'nullable|in:name,created_at',
            'direction'      => 'nullable|in:asc,desc',
        ];
    }
}