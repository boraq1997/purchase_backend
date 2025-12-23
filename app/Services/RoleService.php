<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;

class RoleService
{
    /**
     * جلب جميع الأدوار مع الصلاحيات التابعة لها
     */
    public function getAll(array $filters = [], int $perPage = 15)
    {
        $q = Role::query()
            ->with('permissions')
            ->withCount('permissions');

        /**
         * 🔍 1. فلترة نص عامة (Global Search)
         * ?search=admin
         */
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';

            $q->where(function (Builder $query) use ($search) {
                $query->where('roles.name', 'like', $search)
                    ->orWhere('roles.guard_name', 'like', $search)
                    ->orWhereHas('permissions', function (Builder $p) use ($search) {
                        $p->where('permissions.name', 'like', $search);
                    });
            });
        }

        /**
         * 🎯 2. فلترة متقدمة
         */

        // فلترة بالاسم
        if (!empty($filters['name'])) {
            $q->where('name', 'like', '%' . $filters['name'] . '%');
        }

        // فلترة بالحارس
        if (!empty($filters['guard_name'])) {
            $q->where('guard_name', $filters['guard_name']);
        }

        // فلترة بالصلاحيات (role يحتوي صلاحية أو أكثر)
        // permissions[]=create-user&permissions[]=delete-user
        if (!empty($filters['permissions']) && is_array($filters['permissions'])) {
            $q->whereHas('permissions', function (Builder $p) use ($filters) {
                $p->whereIn('permissions.name', $filters['permissions']);
            });
        }

        // عدد الصلاحيات (min / max)
        if (!empty($filters['permissions_count_min'])) {
            $q->having('permissions_count', '>=', $filters['permissions_count_min']);
        }

        if (!empty($filters['permissions_count_max'])) {
            $q->having('permissions_count', '<=', $filters['permissions_count_max']);
        }

        // فلترة بتاريخ الإنشاء
        if (!empty($filters['created_from'])) {
            $q->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_to'])) {
            $q->whereDate('created_at', '<=', $filters['created_to']);
        }

        /**
         * 🔃 الترتيب
         */
        $orderBy  = $filters['order_by']  ?? 'name';
        $orderDir = $filters['order_dir'] ?? 'asc';

        $q->orderBy($orderBy, $orderDir);

        /**
         * 📄 pagination أو all
         */
        return !empty($filters['all'])
            ? $q->get()
            : $q->paginate($filters['per_page'] ?? $perPage);
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