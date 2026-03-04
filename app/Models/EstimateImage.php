<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimateImage extends Model
{
    protected $fillable = [
        'estimate_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by'
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    protected $appends = ['file_url'];

    public function getFileUrlAttribute() {
        return asset('storage/'.$this->file_path);
    }

    public function estimate() {
        return $this->belongsTo(Estimate::class);
    }

    public function uploader() {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
