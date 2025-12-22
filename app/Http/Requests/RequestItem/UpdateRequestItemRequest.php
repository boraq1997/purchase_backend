<?php

namespace App\Http\Requests\RequestItem;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequestItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('edit-RequestItem');
    }

    public function rules(): array
    {
        return [
            'purchase_request_id' => 'sometimes|required|exists:purchase_requests,id',
            'name'                => 'sometimes|required|string|max:255',
            'description'         => 'nullable|string|max:500',
            'quantity'            => 'sometimes|required|integer|min:1',
            'unit'                => 'nullable|string|max:50',
            'estimated_price'     => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'purchase_request_id.exists'   => 'purchase request not found',
            'name.required'                => 'item name is required',
            'quantity.required'            => 'quantity is required',
            'quantity.integer'             => 'quantity must be a number',
        ];
    }
}