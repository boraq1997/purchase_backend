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
        'estimate_item_id',
        'estimate_id',
        'item_name',
        'unit_id',
        'quantity',
        'unit_price',
        'purchase_price',
        'estimate_price',
        'notes',
    ];

    protected $casts = [
        'quantity'       => 'decimal:2',
        'unit_price'     => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'estimate_price' => 'decimal:2',
        'difference'     => 'decimal:2',
        'total_price'    => 'decimal:2',
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

    public function estimateItem()
    {
        return $this->belongsTo(EstimateItem::class);
    }

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}