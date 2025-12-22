<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-Report');
    }

    public function rules(): array
    {
        return [
            'purchase_request_id' => 'required|exists:purchase_requests,id',
            'committee_id'        => 'nullable|exists:committees,id',
            'report_title'        => 'required|string|max:255',
            'report_body'         => 'required|string',
            'recommendations'     => 'nullable|string|max:1000',
            'status'              => 'nullable|in:draft,submitted,approved,rejected',
            'created_by'          => 'required|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'purchase_request_id.required' => 'purchase request id is required',
            'purchase_request_id.exists'   => 'purchase request not found',
            'report_title.required'        => 'report title is required',
            'report_body.required'         => 'report body is required',
            'committee_id.exists'          => 'committee not found',
            'created_by.required'          => 'creator user is required',
            'created_by.exists'            => 'creator user not found',
        ];
    }
}