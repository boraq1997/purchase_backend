<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DepartmentService
{
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

        return ($filters['all'] ?? false)
            ? $q->orderBy('name')->get()
            : $q->orderBy('name')->paginate($perPage);
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

        return $q->orderBy('name')->get();
    }

    /**
     * جلب قسم محدد مع العلاقات
     */
    public function getById(Department $department): Department
    {
        return $department->load('manager');
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

            return $department->load(['manager', 'users']);
        });
    }

    /**
     * تحديث قسم موجود
     */
    public function update(Department $department, array $data): Department
    {
        return DB::transaction(function () use ($department, $data) {
            $department->update($data);
            return $department->load(['manager', 'users']);
        });
    }

    /**
     * حذف قسم إداري
     */
    public function delete(Department $department): bool
    {
        return DB::transaction(fn() => $department->delete());
    }
}