<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('edit-Role');
    }

    public function rules(): array
    {
        // Laravel passes full model here
        $role = $this->route('role');

        // Extract ID from model or raw parameter
        $roleId = $role instanceof Role ? $role->id : $role;

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],

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