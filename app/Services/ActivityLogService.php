<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityLogService
{
    /**
     * =============================
     * Log a new activity
     * =============================
     *
     * This method creates a new ActivityLog record.
     * It automatically fills actor info from the authenticated user (Sanctum),
     * merges metadata, and tracks old/new values and request info.
     *
     * @param string $action              The action name (e.g., 'create_student')
     * @param string|null $actionLabel    Human-readable label for the action
     * @param int|null $actorId           Actor ID (optional, auto from auth if null)
     * @param string|null $actorName      Actor name (optional, auto from auth if null)
     * @param string|null $actorRole      Actor role (optional, auto from auth if null)
     * @param string $actorType           Actor type/class (default: 'System', auto from auth if user)
     * @param string|null $subjectType    Subject model type (e.g., 'Student')
     * @param int|null $subjectId         Subject ID
     * @param string|null $subjectIdentifier Subject unique code/slug
     * @param array $oldValues            Old values (before update)
     * @param array $newValues            New values (after update)
     * @param array $changedFields        List of fields that changed
     * @param string $status              Status (success, failed)
     * @param string $severity            Severity (info, warning, critical)
     * @param string|null $module         Module or system scope
     * @param string|null $route          Request route/path
     * @param string|null $method         HTTP method
     * @param array $metadata             Additional metadata (will merge with request info)
     * @param int|null $durationMs        Duration of action in milliseconds
     *
     * @return ActivityLog
     */
    public function log(
        string $action,
        string $actionLabel = null,
        ?int $actorId = null,
        ?string $actorName = null,
        ?string $actorRole = null,
        string $actorType = 'System',
        ?string $subjectType = null,
        ?int $subjectId = null,
        ?string $subjectIdentifier = null,
        array $oldValues = [],
        array $newValues = [],
        array $changedFields = [],
        string $status = 'success',
        string $severity = 'info',
        ?string $module = null,
        ?string $route = null,
        ?string $method = null,
        array $metadata = [],
        ?int $durationMs = null,
    ): ActivityLog {
        // ===============================
        // Actor auto-fill from authenticated user (Sanctum)
        // ===============================
        if ($user = auth('sanctum')->user()) {
            $actorId   = $actorId ?? $user->id;                             // ID of authenticated user
            $actorName = $actorName ?? ($user->name ?? $user->username);    // Name or username
            $actorRole = $actorRole ?? $user->roles?->first()?->name;       // First role if using Spatie Permission
            $actorType = $actorType ?? class_basename($user);        // Model class (Admin, User, etc.)
        }

        // ===============================
        // Merge default request metadata with provided metadata
        // ===============================
        $defaultMetadata = [
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
            'device_type' => $metadata['device_type'] ?? null,
            'platform'    => $metadata['platform'] ?? null,
            'request_id'  => $metadata['request_id'] ?? Str::uuid()->toString(),
        ];

        $metadata = array_merge($defaultMetadata, $metadata);
        // ===============================
        // Create ActivityLog record
        // ===============================
        return ActivityLog::create([
            // Actor info
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'actor_name' => $actorName,
            'actor_role' => $actorRole,

            // Action info
            'action' => $action,
            'action_label' => $actionLabel,
            'status' => $status,
            'severity' => $severity,

            // Subject info
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'subject_identifier' => $subjectIdentifier,

            // Change tracking
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'changed_fields' => $changedFields ?: null,

            // Request context
            'ip_address' => $metadata['ip_address'],
            'user_agent' => $metadata['user_agent'],
            'device_type' => $metadata['device_type'],
            'platform' => $metadata['platform'],
            'request_id' => $metadata['request_id'],

            // System scope
            'module' => $module,
            'route' => $route ?? Request::path(),
            'method' => $method ?? Request::method(),

            // Performance & additional metadata
            'duration_ms' => $durationMs,
            'metadata' => $metadata,
        ]);
    }

    /**
     * =============================
     * Retrieve Activity Logs with filters
     * =============================
     *
     * Filters supported:
     * - actor_type, actor_id, actor_name, actor_role
     * - subject_type, subject_id, subject_identifier
     * - action, action_label
     * - status, severity
     * - module, route, method
     * - metadata (JSON key-value search)
     * - from_date, to_date
     * - q (global text search)
     *
     * Pagination is applied by $perPage
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getActivityLogs(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = ActivityLog::query();

        // ===============================
        // Actor Filters
        // ===============================
        if (!empty($filters['actor_type'])) {
            $query->where('actor_type', $filters['actor_type']);
        }
        if (!empty($filters['actor_id'])) {
            $query->where('actor_id', $filters['actor_id']);
        }
        if (!empty($filters['actor_name'])) {
            $query->where('actor_name', 'like', '%' . $filters['actor_name'] . '%');
        }
        if (!empty($filters['actor_role'])) {
            $query->where('actor_role', $filters['actor_role']);
        }

        // ===============================
        // Subject Filters
        // ===============================
        if (!empty($filters['subject_type'])) {
            $query->where('subject_type', $filters['subject_type']);
        }
        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }
        if (!empty($filters['subject_identifier'])) {
            $query->where('subject_identifier', 'like', '%' . $filters['subject_identifier'] . '%');
        }

        // ===============================
        // Action Filters
        // ===============================
        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }
        if (!empty($filters['action_label'])) {
            $query->where('action_label', 'like', '%' . $filters['action_label'] . '%');
        }

        // ===============================
        // Status & Severity
        // ===============================
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['severity'])) {
            $query->where('severity', $filters['severity']);
        }

        // ===============================
        // Module / Route / Method Filters
        // ===============================
        if (!empty($filters['module'])) {
            $query->where('module', $filters['module']);
        }
        if (!empty($filters['route'])) {
            $query->where('route', 'like', '%' . $filters['route'] . '%');
        }
        if (!empty($filters['method'])) {
            $query->where('method', $filters['method']);
        }

        // ===============================
        // Metadata JSON filters
        // ===============================
        if (!empty($filters['metadata']) && is_array($filters['metadata'])) {
            foreach ($filters['metadata'] as $key => $value) {
                $query->whereJsonContains('metadata->' . $key, $value);
            }
        }

        // ===============================
        // Date Range Filters
        // ===============================
        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        // ===============================
        // Global Search (q)
        // ===============================
        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $term = '%' . $filters['q'] . '%';
                $q->where('action', 'like', $term)
                  ->orWhere('action_label', 'like', $term)
                  ->orWhere('actor_name', 'like', $term)
                  ->orWhere('subject_identifier', 'like', $term)
                  ->orWhere('route', 'like', $term);
            });
        }

        // ===============================
        // Sorting
        // ===============================
        $query->orderByDesc('created_at'); // Newest logs first

        // ===============================
        // Pagination
        // ===============================
        return $query->paginate($perPage);
    }
}