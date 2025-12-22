<?php

namespace App\Http\Requests\WarehouseCheck;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('edit-WarehouseCheck');
    }

    public function rules(): array
    {
        return [
            'purchase_request_id' => 'sometimes|required|exists:purchase_requests,id',
            'request_item_id'     => 'sometimes|required|exists:request_items,id',
            'availability'        => 'nullable|in:available,unavailable',
            'item_condition'      => 'nullable|string|max:255',
            'available_quantity'  => 'nullable|integer|min:0',
            'recommendation'      => 'nullable|string|max:500',
            'notes'               => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'purchase_request_id.exists' => 'purchase request not found',
            'request_item_id.exists'     => 'request item not found',
            'availability.in'            => 'invalid availability value',
        ];
    }
}