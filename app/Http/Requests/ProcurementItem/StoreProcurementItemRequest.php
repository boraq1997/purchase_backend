<?php

namespace App\Http\Requests\ProcurementItem;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcurementItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-ProcurementItem');
    }

    public function rules(): array
    {
        return [
            'procurement_id'   => 'required|exists:procurements,id',
            'estimate_item_id' => 'required|exists:estimate_items,id',
            'quantity'         => 'required|integer|min:1',
            'unit_price'       => 'required|numeric|min:0',
            'total_price'      => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'procurement_id.required'   => 'procurement id is required',
            'procurement_id.exists'     => 'procurement not found',
            'estimate_item_id.required' => 'estimate item id is required',
            'estimate_item_id.exists'   => 'estimate item not found',
            'quantity.required'         => 'quantity is required',
            'quantity.integer'          => 'quantity must be a number',
            'unit_price.required'       => 'unit price is required',
            'unit_price.numeric'        => 'unit price must be numeric',
        ];
    }
}