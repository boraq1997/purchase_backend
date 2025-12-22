<?php

namespace App\Http\Requests\EstimateItem;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstimateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-EstimateItem');
    }

    public function rules(): array
    {
        return [
            'estimate_id'       => 'required|exists:estimates,id',
            'request_item_id'   => 'required|exists:request_items,id',
            'item_name'         => 'required|string|max:255',
            'unit_price'        => 'required|numeric|min:0',
            'total_price'       => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'estimate_id.required'      => 'estimate id is required',
            'estimate_id.exists'        => 'estimate not found',
            'request_item_id.required'  => 'request item id is required',
            'request_item_id.exists'    => 'request item not found',
            'unit_price.required'       => 'unit price is required',
            'unit_price.numeric'        => 'unit price must be numeric',
        ];
    }
}