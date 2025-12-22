<?php

namespace App\Http\Requests\Committees;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommitteeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('edit-Committees');
    }

    public function rules(): array
    {
        $committee = $this->route('committee');
        $committeeId = $committee ? $committee->id : null;

        return [
            'name'            => "sometimes|required|string|max:255|unique:committees,name,{$committeeId}",
            'department_id'   => 'sometimes|nullable|exists:departments,id',
            'manager_user_id' => 'nullable|exists:users,id',
            'description'     => 'nullable|string',
            'users' => 'nullable|array',
            'users.*' => 'nullable|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required'            => 'committee name is required',
            'name.unique'              => 'committee name already exists',
            'department_id.required'   => 'department is required',
            'department_id.exists'     => 'department does not exist',
            'manager_user_id.exists'   => 'manager user not found',
            'users.array'              => 'users must be an array',
            'users.*.exists'           => 'one or more user ids are invalid',
        ];
    }
}

