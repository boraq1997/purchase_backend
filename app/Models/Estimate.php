<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'request_item_id',
        'vendor_id',
        'estimate_date',
        'total_amount',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'estimate_date' => 'date', // لأن العمود date وليس datetime
        'total_amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | العلاقات
    |--------------------------------------------------------------------------
    */

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function requestItem(): BelongsTo
    {
        return $this->belongsTo(RequestItem::class);
    }

    public function estimateItems(): HasMany
    {
        return $this->hasMany(EstimateItem::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | الأحداث
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::saved(function (Estimate $estimate) {
            $request = $estimate->purchaseRequest;

            if ($request && $request->status_type === 'draft') {
                $request->update(['status_type' => 'pending']);
            }
        });
    }
}