<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PermissionService
{
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

        return $q->orderBy('name')->get();
    }

    /**
     * جلب صلاحية محددة
     */
    public function getById(Permission $permission): Permission
    {
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

            return $permission;
        });
    }

    /**
     * تحديث صلاحية موجودة
     */
    public function update(Permission $permission, array $data): Permission
    {
        return DB::transaction(function () use ($permission, $data) {
            if (isset($data['name']) && $data['name'] !== $permission->name) {
                if (Permission::where('name', $data['name'])->where('id', '!=', $permission->id)->exists()) {
                    throw ValidationException::withMessages(['name' => 'اسم الصلاحية مستخدم بالفعل.']);
                }

                $permission->update(['name' => $data['name']]);
            }

            return $permission;
        });
    }

    /**
     * حذف صلاحية
     */
    public function delete(Permission $permission): bool
    {
        return DB::transaction(fn() => $permission->delete());
    }
}