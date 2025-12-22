<?php

namespace App\Http\Requests\WarehouseCheck;

use Illuminate\Foundation\Http\FormRequest;

class SearchWarehouseCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('view-WarehouseCheck');
    }

    public function rules(): array
    {
        return [
            'query'               => 'nullable|string|max:255',
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'request_item_id'     => 'nullable|exists:request_items,id',
            'availability'        => 'nullable|in:available,partial,unavailable',
            'checked_by'          => 'nullable|exists:users,id',
            'order_by'            => 'nullable|in:availability,available_quantity,created_at',
            'direction'           => 'nullable|in:asc,desc',
        ];
    }
}