<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_id',
        'request_item_id',
        'item_name',
        'quantity',
        'unit_price',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | العلاقات
    |--------------------------------------------------------------------------
    */

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
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
            $item->total_price = ($item->quantity ?? 0) * ($item->unit_price ?? 0);
        });

        static::saved(function($item) {
            if ($item->estimate) {
                $item->estimate->update([
                    'total_amount' => $item->estimate->estimateItems()->sum('total_price')
                ]);
            }
        });
    }
}