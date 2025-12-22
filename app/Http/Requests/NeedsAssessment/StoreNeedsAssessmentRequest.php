<?php

namespace App\Http\Requests\NeedsAssessment;

use Illuminate\Foundation\Http\FormRequest;

class StoreNeedsAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create-NeedsAssessment');
    }

    public function rules(): array
    {
        return [
            'purchase_request_id' => 'required|exists:purchase_requests,id',
            'request_item_id'     => 'required|exists:request_items,id',

            'urgency_level'       => 'required|in:low,medium,high,critical',
            'needs_status'        => 'required|in:needed,not_needed,modified',

            'quantity_needed_after_assessment' => 'nullable|integer|min:1',

            'modified_specifications' => 'nullable|string|max:2000',
            'reason'                  => 'nullable|string|max:2000',
            'recommended_action'      => 'nullable|string|max:2000',
            'notes'                   => 'nullable|string|max:2000',

            'assessment_state'   => 'required|in:draft,final',

            'admin_decision'     => 'nullable|in:pending,approved,rejected',
            'admin_comment'      => 'nullable|string|max:2000',
        ];
    }

    public function messages()
    {
        return [
            'purchase_request_id.required' => 'The purchase request is required.',
            'purchase_request_id.exists'   => 'Purchase request not found.',

            'request_item_id.required'     => 'The request item is required.',
            'request_item_id.exists'       => 'Request item not found.',

            'urgency_level.required'       => 'Urgency level is required.',
            'urgency_level.in'             => 'Urgency level must be: low, medium, high, critical.',

            'needs_status.required'        => 'Needs status is required.',
            'needs_status.in'              => 'Needs status must be: needed, not_needed, modified.',

            'quantity_needed_after_assessment.integer' => 'Quantity must be a valid number.',
            'quantity_needed_after_assessment.min'     => 'Quantity must be at least 1.',

            'modified_specifications.string' => 'Modified specifications must be text.',
            'reason.string'                  => 'Reason must be text.',
            'recommended_action.string'      => 'Recommended action must be text.',
            'notes.string'                   => 'Notes must be text.',

            'assessment_state.required' => 'Assessment state is required.',
            'assessment_state.in'       => 'Assessment state must be: draft, final.',

            'assessed_by.required'      => 'Assessed_by (evaluator user) is required.',
            'assessed_by.exists'        => 'Evaluator user not found.',

            'admin_decision.in'         => 'Admin decision must be: pending, approved, rejected.',
            'admin_comment.string'      => 'Admin comment must be text.',
        ];
    }
}