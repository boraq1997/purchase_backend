<?php

namespace App\Http\Requests\EstimateItem;

use Illuminate\Foundation\Http\FormRequest;

class SearchEstimateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('view-EstimateItem');
    }

    public function rules(): array
    {
        return [
            'query'             => 'nullable|string|max:255',
            'estimate_id'       => 'nullable|exists:estimates,id',
            'request_item_id'   => 'nullable|exists:request_items,id',
            'min_unit_price'    => 'nullable|numeric|min:0',
            'max_unit_price'    => 'nullable|numeric|min:0|gte:min_unit_price',
            'order_by'          => 'nullable|in:unit_price,total_price,created_at',
            'direction'         => 'nullable|in:asc,desc',
        ];
    }
}