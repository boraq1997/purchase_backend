<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'item_name',
        'quantity',
        'unit_id',
        'estimated_unit_price',
        'total_estimated_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'estimated_unit_price' => 'decimal:2',
        'total_estimated_price' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | العلاقات
    |--------------------------------------------------------------------------
    */

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function unit() {
        return $this->belongsTo(Unit::class);
    }

    public function warehouseCheck(): HasOne
    {
        return $this->hasOne(WarehouseCheck::class);
    }

    public function needsAssessment(): HasOne
    {
        return $this->hasOne(NeedsAssessment::class);
    }

    public function estimates(): HasMany
    {
        return $this->hasMany(Estimate::class, 'request_item_id');
    }

    public function estimateItems(): HasMany {
        return $this->hasMany(EstimateItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | الأحداث (Booted)
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        // حساب السعر الإجمالي التخميني قبل الحفظ
        static::saving(function ($item) {
            $item->total_estimated_price = ($item->quantity ?? 0) * ($item->estimated_unit_price ?? 0);
        });

        // تحديث حالة الطلب عند إضافة عنصر جديد (إن كانت مسودة)
        static::created(function ($item) {
            $purchaseRequest = $item->purchaseRequest;
            if ($purchaseRequest && $purchaseRequest->status_type === 'draft') {
                $purchaseRequest->update(['status_type' => 'pending']);
            }
        });
    }
}