<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-Role');
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:100|unique:roles,name',
            'guard_name'    => 'nullable|string|in:web,sanctum,api',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ];
    }

    public function messages()
    {
        return [
            'name.required'        => 'role name is required',
            'name.unique'          => 'role name already exists',
            'permissions.array'    => 'permissions must be an array',
            'permissions.*.exists' => 'one or more permissions are invalid',
        ];
    }
}