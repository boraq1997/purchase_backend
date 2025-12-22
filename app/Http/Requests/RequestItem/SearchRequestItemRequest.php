<?php

namespace App\Http\Requests\RequestItem;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequestItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('view-RequestItem');
    }

    public function rules(): array
    {
        return [
            'query'                => 'nullable|string|max:255',
            'purchase_request_id'  => 'nullable|exists:purchase_requests,id',
            'min_quantity'         => 'nullable|integer|min:1',
            'max_quantity'         => 'nullable|integer|min:1|gte:min_quantity',
            'order_by'             => 'nullable|in:name,quantity,created_at',
            'direction'            => 'nullable|in:asc,desc',
        ];
    }
}