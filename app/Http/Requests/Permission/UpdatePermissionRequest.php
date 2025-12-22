<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('edit-Permission');
    }

    public function rules(): array
    {
        $permissionId = $this->route('permission');

        return [
            'name'        => "sometimes|required|string|max:100|unique:permissions,name,{$permissionId}",
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