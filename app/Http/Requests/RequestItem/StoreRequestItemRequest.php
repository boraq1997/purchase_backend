<?php

namespace App\Http\Requests\RequestItem;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-RequestItem');
    }

    public function rules(): array
    {
        return [
            'purchase_request_id' => 'required|exists:purchase_requests,id',
            'name'                => 'required|string|max:255',
            'description'         => 'nullable|string|max:500',
            'quantity'            => 'required|integer|min:1',
            'unit'                => 'nullable|string|max:50',
            'estimated_price'     => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'purchase_request_id.required' => 'purchase request id is required',
            'purchase_request_id.exists'   => 'purchase request not found',
            'name.required'                => 'item name is required',
            'quantity.required'            => 'quantity is required',
            'quantity.integer'             => 'quantity must be a valid number',
            'unit.max'                     => 'unit name is too long',
        ];
    }
}