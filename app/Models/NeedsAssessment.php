<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NeedsAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'request_item_id',
        'urgency_level',
        'needs_status',
        'quantity_needed_after_assessment',
        'modified_specifications',
        'reason',
        'recommended_action',
        'notes',
        'assessed_by',
        'assessment_state',
        'admin_decision',
        'admin_comment',
    ];

    protected $casts = [
        'urgency_level'                   => 'string',
        'needs_status'                    => 'string',
        'assessment_state'                => 'string',
        'admin_decision'                  => 'string',
        'quantity_needed_after_assessment'=> 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | العلاقات
    |--------------------------------------------------------------------------
    */

    /**
     * الطلب المرتبط بالتقييم
     */
    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /**
     * المادة المرتبطة بالتقييم
     */
    public function requestItem(): BelongsTo
    {
        return $this->belongsTo(RequestItem::class);
    }

    /**
     * المستخدم الذي قام بالتقييم
     */
    public function assessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }
}