<?php

namespace App\Http\Requests\WarehouseCheck;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-WarehouseCheck');
    }

    public function rules(): array
    {
        return [
            'purchase_request_id' => 'required|exists:purchase_requests,id',
            'request_item_id'     => 'required|exists:request_items,id',
            'availability'        => 'required|in:available,partial,unavailable',
            'item_condition'      => 'nullable|string|max:255',
            'available_quantity'  => 'nullable|integer|min:0',
            'recommendation'      => 'nullable|string|max:500',
            'notes'               => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'purchase_request_id.required' => 'purchase request id is required',
            'purchase_request_id.exists'   => 'purchase request not found',
            'request_item_id.required'     => 'request item id is required',
            'request_item_id.exists'       => 'request item not found',
            'availability.required'        => 'availability status is required',
            'availability.in'              => 'availability must be one of: available, partial, unavailable',
        ];
    }
}