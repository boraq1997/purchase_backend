<?php

namespace App\Http\Requests\Departments;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-Department');
    }

    public function rules(): array
    {
        return [
            'name'              => 'required|string|max:255',
            'code'              => 'required|string|unique:departments,code|max:50',
            'manager_user_id'   => 'nullable|exists:users,id',
            'description'       => 'nullable|string',
            'users'             => 'nullable|array',
            'users.*'           => 'exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required'             => 'the depa name is req',
            'code.required'             => 'the depa code is req',
            'code.unique'               => 'the depa code is taken',
            'manager_user_id.exists'    => 'the depa manager is\'t exists',
            'users.array'               => 'the user ids must be an array',
            'users.*.exists'            => 'one more user ids are invalid',
        ];
    }
}
