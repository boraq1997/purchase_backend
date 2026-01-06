<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\ActivityLogService;

class PermissionService
{
    protected ActivityLogService $logService;

    public function __construct() {
        $this->logService = new ActivityLogService();
    }

    /**
     * جلب جميع الصلاحيات مع الفلاتر الاختيارية
     */
    public function getAll(array $filters = []): LengthAwarePaginator|Collection
    {
        $q = Permission::query();

        if (!empty($filters['search'])) {
            $term = '%' . $filters['search'] . '%';
            $q->where('name', 'like', $term);
        }

        $permissions = $q->orderBy('name')->get();

        $this->logService->log(
            action: 'view_permissions',
            actionLabel: 'عرض جميع الصلاحيات',
            subjectType: Permission::class,
            metadata: [
                'filters' => $filters,
                'result_count' => count($permissions),
            ],
            module: 'الصلاحيات'
        );

        return $permissions;
    }

    /**
     * جلب صلاحية محددة
     */
    public function getById(Permission $permission): Permission
    {
        $this->logService->log(
            action: 'view_permission',
            actionLabel: 'عرض صلاحية محددة',
            subjectType: Permission::class,
            subjectId: $permission->id,
            metadata: [
                'name' => $permission->name,
                'guard_name' => $permission->guard_name,
            ],
            module: 'الصلاحيات'
        );

        return $permission;
    }

    /**
     * إنشاء صلاحية جديدة
     */
    public function create(array $data): Permission
    {
        return DB::transaction(function () use ($data) {
            if (Permission::where('name', $data['name'])->exists()) {
                throw ValidationException::withMessages(['name' => 'اسم الصلاحية مستخدم بالفعل.']);
            }

            $permission = Permission::create([
                'name'       => $data['name'],
                'guard_name' => $data['guard_name'] ?? 'web',
            ]);

            $this->logService->log(
                action: 'create_permission',
                actionLabel: 'إنشاء صلاحية جديدة',
                subjectType: Permission::class,
                subjectId: $permission->id,
                newValues: $permission->toArray(),
                module: 'الصلاحيات'
            );

            return $permission;
        });
    }

    /**
     * تحديث صلاحية موجودة
     */
    public function update(Permission $permission, array $data): Permission
    {
        return DB::transaction(function () use ($permission, $data) {
            $oldValues = $permission->toArray();

            if (isset($data['name']) && $data['name'] !== $permission->name) {
                if (Permission::where('name', $data['name'])->where('id', '!=', $permission->id)->exists()) {
                    throw ValidationException::withMessages(['name' => 'اسم الصلاحية مستخدم بالفعل.']);
                }

                $permission->update(['name' => $data['name']]);
            }

            $newValues = $permission->toArray();
            $changedFields = array_keys(array_diff_assoc($newValues, $oldValues));

            $this->logService->log(
                action: 'update_permission',
                actionLabel: 'تحديث صلاحية',
                subjectType: Permission::class,
                subjectId: $permission->id,
                oldValues: $oldValues,
                newValues: $newValues,
                changedFields: $changedFields,
                module: 'الصلاحيات'
            );

            return $permission;
        });
    }

    /**
     * حذف صلاحية
     */
    public function delete(Permission $permission): bool
    {
        return DB::transaction(function () use ($permission) {
            $oldValues = $permission->toArray();
            $deleted = $permission->delete();

            $this->logService->log(
                action: 'delete_permission',
                actionLabel: 'حذف صلاحية',
                subjectType: Permission::class,
                subjectId: $permission->id,
                oldValues: $oldValues,
                status: $deleted ? 'success' : 'failed',
                module: 'الصلاحيات'
            );

            return $deleted;
        });
    }
}