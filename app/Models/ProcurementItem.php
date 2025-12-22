<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_id',
        'request_item_id',
        'item_name',
        'unit',
        'quantity',
        'unit_price',
        'difference',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | العلاقات
    |--------------------------------------------------------------------------
    */

    public function procurement()
    {
        return $this->belongsTo(Procurement::class);
    }

    public function requestItem()
    {
        return $this->belongsTo(RequestItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | الأحداث (Booted)
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::saving(function ($item) {
            // في حالة كان هناك سعر تقديري من الطلب
            $requestItem = $item->requestItem;
            if ($requestItem) {
                $item->difference = ($item->unit_price ?? 0) - ($requestItem->estimated_unit_price ?? 0);
            }
        });
    }
}