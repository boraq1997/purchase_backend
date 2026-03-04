<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestImage extends Model
{
    protected $fillable = [
        'purchase_request_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by'
    ];

    protected $appends = ['file_url'];

    public function getFileUrlAttribute() {
        return asset('storage/'. $this->file_path);
    }

    public function purchaseRequest() {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function uploader() {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
