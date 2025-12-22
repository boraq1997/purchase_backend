<?php

namespace App\Http\Requests\ProcurementItem;

use Illuminate\Foundation\Http\FormRequest;

class SearchProcurementItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('view-ProcurementItem');
    }

    public function rules(): array
    {
        return [
            'query'             => 'nullable|string|max:255',
            'procurement_id'    => 'nullable|exists:procurements,id',
            'estimate_item_id'  => 'nullable|exists:estimate_items,id',
            'min_quantity'      => 'nullable|integer|min:1',
            'max_quantity'      => 'nullable|integer|min:1|gte:min_quantity',
            'order_by'          => 'nullable|in:quantity,unit_price,total_price,created_at',
            'direction'         => 'nullable|in:asc,desc',
        ];
    }
}