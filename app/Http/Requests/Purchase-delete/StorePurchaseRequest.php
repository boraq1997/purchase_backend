<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-PurchaseRequest');
    }

    public function rules(): array
    {
        return [
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'department_id'   => 'required|exists:departments,id',
            'user_id'         => 'required|exists:users,id',
            'priority'        => 'nullable|in:low,medium,high',
            'status'          => 'nullable|in:pending,approved,rejected,completed',
            'items'           => 'nullable|array',
            'items.*.item_name'    => 'required_with:items|string|max:255',
            'items.*.quantity'     => 'required_with:items|integer|min:1',
            'items.*.unit'         => 'nullable|string|max:50',
            'items.*.specifications'        => 'nullable|string|max:500',
            'images'          => 'nullable|array',
            'images.*'        => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'title.required'           => 'purchase request title is required',
            'department_id.required'   => 'department is required',
            'department_id.exists'     => 'department not found',
            'user_id.required'         => 'user is required',
            'user_id.exists'           => 'user not found',
            'items.array'              => 'items must be an array',
            'items.*.item_name.required_with' => 'item name is required',
            'items.*.quantity.required_with' => 'item quantity is required',
            'items.*.quantity.integer' => 'item quantity must be a number',
        ];
    }
}