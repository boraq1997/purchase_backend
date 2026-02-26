<?php

namespace App\Http\Requests\Departments;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('edit-Department');
    }

    public function rules(): array
    {
        $departmentId = $this->route('department')->id;

        return [
            'name'              => 'sometimes|required|string|max:255',
            'code'              => "max:50|unique:departments,code,{$departmentId}",
            'manager_user_id'   => 'sometimes|nullable|exists:users,id',
            'description'       => 'sometimes|nullable|string',

            // users تعتبر اختيارية
            'users'             => 'sometimes|array',
            'users.*'           => 'exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required'             => 'the depa name is req',
            'code.unique'               => 'the depa code is taken',
            'manager_user_id.exists'    => 'the depa manager is\'t exists',
            'users.array'               => 'the user ids must be an array',
            'users.*.exists'            => 'one more user ids are invalid',
        ];
    }
}

