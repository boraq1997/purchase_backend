<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'estimate_id',
        'reference_no',
        'purchase_date',
        'total_amount',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
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

    // public function estimate()
    // {
    //     return $this->belongsTo(Estimate::class);
    // }

    public function items()
    {
        return $this->hasMany(ProcurementItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}