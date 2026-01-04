<?php

namespace App\Http\Requests\Estimate;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEstimateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('edit-Estimate');
    }

    public function rules(): array
    {
        return [
            'purchase_request_id' => 'sometimes|exists:purchase_requests,id',
            'request_item_id'     => 'sometimes|nullable|exists:request_items,id',
            'vendor_id'           => 'sometimes|nullable|exists:vendors,id',

            'estimate_date'       => 'sometimes|nullable|date',
            'total_amount'        => 'sometimes|required|numeric|min:0',

            'notes'               => 'nullable|string',
            'status'              => 'nullable|in:pending,accepted,rejected',

            'items'                       => 'nullable|array',
            'items.*.id'                  => 'nullable|exists:estimate_items,id',
            'items.*.request_item_id'     => 'required_with:items|exists:request_items,id',
            'items.*.unit_price'          => 'required_with:items|numeric|min:0',
            'items.*.quantity'            => 'required_with:items|numeric|min:1',
            'items.*.total_price'         => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'purchase_request_id.exists'   => 'purchase request not found',
            'request_item_id.exists'       => 'request item not found',
            'vendor_id.exists'             => 'vendor not found',

            'estimate_date.date'           => 'estimate date must be a valid date',

            'total_amount.required'        => 'total amount is required',
            'total_amount.numeric'         => 'total amount must be numeric',

            'status.in'                    => 'status must be pending, accepted or rejected',

            'items.array'                  => 'items must be an array',
            'items.*.id.exists'            => 'estimate item not found',
            'items.*.request_item_id.required_with' => 'request item id is required',
            'items.*.unit_price.required_with'      => 'unit price is required',
            'items.*.quantity.required_with'        => 'quantity is required',
        ];
    }
}