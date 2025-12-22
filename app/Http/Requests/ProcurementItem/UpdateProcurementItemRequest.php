<?php

namespace App\Http\Requests\ProcurementItem;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcurementItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('edit-ProcurementItem');
    }

    public function rules(): array
    {
        return [
            'procurement_id'   => 'sometimes|required|exists:procurements,id',
            'estimate_item_id' => 'sometimes|required|exists:estimate_items,id',
            'quantity'         => 'sometimes|required|integer|min:1',
            'unit_price'       => 'sometimes|required|numeric|min:0',
            'total_price'      => 'nullable|numeric|min:0',
            'notes'            => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'procurement_id.exists'     => 'procurement not found',
            'estimate_item_id.exists'   => 'estimate item not found',
            'quantity.integer'          => 'quantity must be a valid number',
            'unit_price.numeric'        => 'unit price must be numeric',
        ];
    }
}