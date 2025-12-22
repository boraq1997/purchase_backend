<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-User');
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email|max:255',
            'username'      => 'required|string|max:255|unique:users,username',
            'phone'         => 'nullable|string|max:20|unique:users,phone',
            'password'      => 'required|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'parent_id'     => 'nullable|exists:users,id',
            'status'        => 'nullable|in:active,inactive',
            'role'          => 'required|exists:roles,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required'          => 'user name is required',
            'email.required'         => 'user email is required',
            'email.email'            => 'invalid email format',
            'email.unique'           => 'email already exists',
            'phone.unique'           => 'phone already exists',
            'password.required'      => 'password is required',
            'password.min'           => 'password must be at least 8 characters',
            'password.confirmed'     => 'password confirmation does not match',
            'department_id.exists'   => 'department not found',
            'parent_id.exists'       => 'supervisor not found',
            'role.required'          => 'role is required',
            'role.exists'            => 'selected role does not exist',
        ];
    }
}