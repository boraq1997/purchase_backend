<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('edit-User');
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user ? $user->id : null;

        return [
            'name'          => 'sometimes|required|string|max:255',
            'username'      => "sometimes|required|string|max:255|unique:users,username,{$userId}",
            'email'         => "sometimes|required|email|max:255|unique:users,email,{$userId}",
            'phone'         => "nullable|string|max:20|unique:users,phone,{$userId}",
            'password'      => 'nullable|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'parent_id'     => 'nullable|exists:users,id',
            'status'        => 'nullable|in:active,inactive',
            'role'          => 'nullable|exists:roles,id',
        ];
    }

    public function messages()
    {
        return [
            'username.required'     => 'username is required',
            'username.unique'       => 'username already exists',
            'name.required'          => 'user name is required',
            'email.required'         => 'user email is required',
            'email.email'            => 'invalid email format',
            'email.unique'           => 'email already exists',
            'phone.unique'           => 'phone already exists',
            'password.min'           => 'password must be at least 8 characters',
            'password.confirmed'     => 'password confirmation does not match',
            'department_id.exists'   => 'department not found',
            'parent_id.exists'       => 'supervisor not found',
            'role.required'          => 'role is required',
            'role.exists'            => 'selected role does not exist',
        ];
    }
}