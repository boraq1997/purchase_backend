<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoleService
{
    /**
     * جلب جميع الأدوار مع الصلاحيات التابعة لها
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        $q = Role::with('permissions');

        if (!empty($filters['search'])) {
            $term = '%' . $filters['search'] . '%';
            $q->where('name', 'like', $term);
        }

        return ($filters['all'] ?? false)
            ? $q->orderBy('name')->get()
            : $q->orderBy('name')->paginate($perPage);
    }

    /**
     * جلب دور محدد مع الصلاحيات
     */
    public function getById(Role $role): Role
    {
        return $role->load('permissions');
    }

    /**
     * إنشاء دور جديد مع صلاحياته
     */
    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            if (Role::where('name', $data['name'])->exists()) {
                throw ValidationException::withMessages(['name' => 'اسم الدور مستخدم بالفعل.']);
            }

            $role = Role::create(['name' => $data['name'], 'guard_name' => 'sanctum']);

            if (!empty($data['permissions']) && is_array($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role->load('permissions');
        });
    }

    /**
     * تحديث بيانات الدور وصلاحياته
     */
    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            if (isset($data['name']) && $data['name'] !== $role->name) {
                if (Role::where('name', $data['name'])->where('id', '!=', $role->id)->exists()) {
                    throw ValidationException::withMessages(['name' => 'اسم الدور مستخدم بالفعل.']);
                }
                $role->update(['name' => $data['name']]);
            }

            if (array_key_exists('permissions', $data) && is_array($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role->load('permissions');
        });
    }

    /**
     * حذف دور
     */
    public function delete(Role $role): bool
    {
        return DB::transaction(fn() => $role->delete());
    }

    /**
     * مزامنة الصلاحيات مع الدور
     */
    public function syncPermissions(Role $role, array $permissions): Role
    {
        $role->syncPermissions($permissions);
        return $role->load('permissions');
    }
}