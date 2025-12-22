<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-Permission');
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:100|unique:permissions,name',
            'guard_name'  => 'nullable|string|in:web,sanctum,api',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'permission name is required',
            'name.unique'   => 'permission name already exists',
        ];
    }
}