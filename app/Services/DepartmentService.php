<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogService;

class DepartmentService
{
    protected ActivityLogService $logService;

    public function __construct() {
        $this->logService = new ActivityLogService();
    }
    /**
     * جلب جميع الأقسام مع المدير والعلاقات التابعة
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        $q = Department::with('manager');

        if (!empty($filters['search'])) {
            $term = '%' . $filters['search'] . '%';
            $q->where('name', 'like', $term);
        }

        if (!empty($filters['parent_id'])) {
            $q->where('parent_id', $filters['parent_id']);
        }

        $result = ($filters['all'] ?? false)
            ? $q->orderBy('name')->get()
            : $q->orderBy('name')->paginate($perPage);

        $this->logService->log(
            action: 'view_departments',
            actionLabel: 'عرض جميع الأقسام',
            subjectType: Department::class,
            subjectId: null,
            metadata: [
                'filters' => $filters,
                'result_count' => is_countable($result) ? count($result) : $result->total()
            ],
            module: 'الأقسام'
        );

        return $result;
    }

    /**
     * جلب جميع الأقسام مع المستخدمين والمدير
     */
    public function getAllWithUsers(array $filters = []): Collection
    {
        $q = Department::with(['manager', 'users']);

        if (!empty($filters['search'])) {
            $term = '%' . $filters['search'] . '%';
            $q->where('name', 'like', $term);
        }

        if (!empty($filters['parent_id'])) {
            $q->where('parent_id', $filters['parent_id']);
        }

        $result = $q->orderBy('name')->get();

        $this->logService->log(
            action: 'view_departments_with_users',
            actionLabel: 'عرض الأقسام مع المستخدمين والمدير',
            subjectType: Department::class,
            subjectId: null,
            metadata: [
                'filters' => $filters,
                'result_count' => count($result)
            ],
            module: 'الأقسام'
        );

        return $result;
    }

    /**
     * جلب قسم محدد مع العلاقات
     */
    public function getById(Department $department): Department
    {
        $department = $department->load('manager');

        $this->logService->log(
            action: 'view_department',
            actionLabel: 'عرض قسم محدد',
            subjectType: Department::class,
            subjectId: $department->id,
            metadata: [
                'department_name' => $department->name
            ],
            module: 'الأقسام'
        );

        return $department;
    }

    /**
     * إنشاء قسم جديد
     */
    public function create(array $data): Department
    {
        return DB::transaction(function () use ($data) {
            // إنشاء القسم
            $department = Department::create([
                'name'             => $data['name'],
                'code'             => $data['code'],
                'description'      => $data['description'] ?? null,
                'manager_user_id'  => $data['manager_user_id'] ?? null,
            ]);

            // ربط المستخدمين (إذا كان الحقل موجود ومعبأ)
            if (array_key_exists('users', $data)) {

                if (!empty($data['users'])) {
                    \App\Models\User::whereIn('id', $data['users'])
                        ->update(['department_id' => $department->id]);
                }
            }

            $this->logService->log(
                action: 'create_department',
                actionLabel: 'إنشاء قسم جديد',
                subjectType: Department::class,
                subjectId: $department->id,
                newValues: $department->toArray(),
                module: 'الأقسام'
            );

            return $department->load(['manager', 'users']);
        });
    }

    /**
     * تحديث قسم موجود
     */
    public function update(Department $department, array $data): Department
    {
        return DB::transaction(function () use ($department, $data) {
            $oldValues = $department->toArray();

            $department->update($data);

            $newValues = $department->toArray();
            $changedFields = array_keys(array_diff_assoc($newValues, $oldValues));

            $this->logService->log(
                action: 'update_department',
                actionLabel: 'تحديث قسم',
                subjectType: Department::class,
                subjectId: $department->id,
                oldValues: $oldValues,
                newValues: $newValues,
                changedFields: $changedFields,
                module: 'الأقسام'
            );

            return $department->load(['manager', 'users']);
        });
    }

    /**
     * حذف قسم إداري
     */
    public function delete(Department $department): bool
    {
        
        return DB::transaction(function () use ($department) {
            $oldValues = $department->toArray();

            $deleted = $department->delete();
            $this->logService->log(
                action: 'delete_department',
                actionLabel: 'حذف قسم',
                subjectType: Department::class,
                subjectId: $department->id,
                oldValues: $oldValues,
                status: $deleted ? 'success' : 'failed',
                module: 'الأقسام'
            );
            return $deleted;
        });

    }
}