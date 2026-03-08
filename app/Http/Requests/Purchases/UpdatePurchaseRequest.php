<?php

namespace App\Http\Requests\Purchases;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('edit-PurchaseRequest');
    }

    public function rules(): array
    {
        $requestId = $this->route('purchase_request');

        return [
            'title'           => 'sometimes|required|string|max:255',
            'description'     => 'nullable|string',
            'department_id'   => 'sometimes|required|exists:departments,id',
            'user_id'         => 'sometimes|required|exists:users,id',
            'priority'        => 'nullable|in:low,medium,high',
            'status'          => 'nullable|in:pending,approved,rejected,completed',
            'items'           => 'nullable|array',
            'items.*.id'      => 'nullable|exists:request_items,id',
            'items.*.item_name'    => 'required_with:items|string|max:255',
            'items.*.quantity'=> 'required_with:items|integer|min:1',
            'items.*.unit'    => 'nullable|exists:units,id',
            'items.*.specifications'   => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'title.required'           => 'purchase request title is required',
            'department_id.exists'     => 'department not found',
            'user_id.exists'           => 'user not found',
            'items.array'              => 'items must be an array',
            'items.*.id.exists'        => 'one or more items are invalid',
            'items.*.item_name.required_with' => 'item name is required',
            'items.*.quantity.required_with' => 'item quantity is required',
            'items.*.quantity.integer' => 'item quantity must be a number',
        ];
    }
}