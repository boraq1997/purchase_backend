<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'user_id',
        'title',
        'description',
        'total_estimated_cost',
        'priority',
    ];

    protected $guarded = [
        'request_number',
        'status_type',
        'status_action_by',
        'status_role',
        'status_date',
        'rejected_reason',
        'commitee_id',
        'closed_at',
    ];

    protected $casts = [
        'status_date'           => 'datetime',
        'closed_at'             => 'datetime',
        'total_estimated_cost'  => 'decimal:2'
    ];

    /*
    |--------------------------------------------------------------------------
    | العلاقات
    |--------------------------------------------------------------------------
    */

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function statusActor()
    {
        return $this->belongsTo(User::class, 'status_action_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RequestItem::class);
    }

    public function estimates(): HasMany
    {
        return $this->hasMany(Estimate::class);
    }

    public function procurements(): HasMany
    {
        return $this->hasMany(Procurement::class);
    }

    public function warehouseChecks(): HasMany
    {
        return $this->hasMany(WarehouseCheck::class);
    }

    public function needsAssessments(): HasMany
    {
        return $this->hasMany(NeedsAssessment::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(Report::class);
    }

    public function images() {
        return $this->hasMany(PurchaseRequestImage::class);
    }

    /*
    |--------------------------------------------------------------------------
    | الأحداث (Events)
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($purchaseRequest) {
            // توليد رقم الطلب تلقائيًا عند الإنشاء
            if (empty($purchaseRequest->request_number)) {
                $latestId = self::max('id') + 1;
                $purchaseRequest->request_number = sprintf('PR-%s-%04d', now()->year, $latestId);
            }

            // تعيين القيم الافتراضية
            $purchaseRequest->status_type ??= 'pending';
            $purchaseRequest->priority ??= 'medium';

            if (empty($purchaseRequest->user_id)) {
                $purchaseRequest->user_id = auth()->id();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | دوال مساعدة (Accessors & Helpers)
    |--------------------------------------------------------------------------
    */

    public function getOwnerAttribute(): string
    {
        return $this->department->name
            ?? $this->committee->name
            ?? 'غير محدد';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_type) {
            'draft' => 'مسودة',
            'pending' => 'قيد المراجعة',
            'approved' => 'تمت الموافقة',
            'rejected' => 'تم الرفض',
            'completed' => 'مكتمل',
            default => 'غير معروف',
        };
    }
}