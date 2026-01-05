<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ActivityLog extends Model
{
    /**
     * =============================
     * Table
     * =============================
     */
    protected $table = 'activity_logs';

    /**
     * =============================
     * Mass Assignment
     * =============================
     */
    protected $fillable = [
        // Actor
        'actor_type',
        'actor_id',
        'actor_name',
        'actor_role',

        // Action
        'action',
        'action_label',
        'status',
        'severity',

        // Subject
        'subject_type',
        'subject_id',
        'subject_identifier',

        // Changes
        'old_values',
        'new_values',
        'changed_fields',

        // Request context
        'ip_address',
        'user_agent',
        'device_type',
        'platform',
        'request_id',

        // System scope
        'module',
        'route',
        'method',

        // Performance & metadata
        'duration_ms',
        'metadata',
    ];

    /**
     * =============================
     * Casts
     * =============================
     */
    protected $casts = [
        'old_values'      => 'array',
        'new_values'      => 'array',
        'changed_fields'  => 'array',
        'metadata'        => 'array',
        'duration_ms'     => 'integer',
    ];

    /**
     * =============================
     * Default Attributes
     * =============================
     */
    protected $attributes = [
        'status'   => 'success',
        'severity' => 'info',
    ];

    /**
     * =============================
     * Query Scopes
     * =============================
     */

    // فلترة حسب الموديل المتأثر
    public function scopeForSubject(Builder $query, string $type, ?int $id = null): Builder
    {
        return $query
            ->where('subject_type', $type)
            ->when($id, fn ($q) => $q->where('subject_id', $id));
    }

    // فلترة حسب الفاعل
    public function scopeForActor(Builder $query, string $type, ?int $id = null): Builder
    {
        return $query
            ->where('actor_type', $type)
            ->when($id, fn ($q) => $q->where('actor_id', $id));
    }

    // فلترة حسب الخطورة
    public function scopeSeverity(Builder $query, string $level): Builder
    {
        return $query->where('severity', $level);
    }

    // فلترة حسب العملية
    public function scopeAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * =============================
     * Helper Methods
     * =============================
     */

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}