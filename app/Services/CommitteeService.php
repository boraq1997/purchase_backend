<?php

namespace App\Services;

use App\Models\Committee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CommitteeService
{
    /**
     * جلب جميع اللجان مع القسم والمدير وأعضائها
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        $q = Committee::with(['department', 'manager', 'users']);

        if (!empty($filters['search'])) {
            $term = '%' . $filters['search'] . '%';
            $q->where('name', 'like', $term);
        }

        if (!empty($filters['department_id'])) {
            $q->where('department_id', $filters['department_id']);
        }

        return ($filters['all'] ?? false)
            ? $q->latest('id')->get()
            : $q->latest('id')->paginate($perPage);
    }

    public function getAllWithUsers(): Collection
    {
        return Committee::with(['department', 'manager', 'users'])->get();
    }

    /**
     * جلب لجنة محددة مع العلاقات
     */
    public function getById(Committee $committee): Committee
    {
        return $committee->load(['department', 'manager', 'users']);
    }

    /**
     * إنشاء لجنة جديدة مع أعضائها
     */
    public function create(array $data): Committee
    {
        return DB::transaction(function () use ($data) {
            $committee = Committee::create([
                'name'             => $data['name'],
                'description'      => $data['description'] ?? null,
                'department_id'    => $data['department_id'] ?? null,
                'manager_user_id'  => $data['manager_user_id'] ?? null,
            ]);

            if (!empty($data['users']) && is_array($data['users'])) {
                $committee->users()->sync($data['users']);
            }

            return $committee->load(['department', 'manager', 'users']);
        });
    }

    /**
     * تحديث لجنة موجودة
     */
    public function update(Committee $committee, array $data): Committee
    {
        return DB::transaction(function () use ($committee, $data) {
            $committee->update([
                'name'             => $data['name'] ?? $committee->name,
                'description'      => $data['description'] ?? $committee->description,
                'department_id'    => $data['department_id'] ?? $committee->department_id,
                'manager_user_id'  => $data['manager_user_id'] ?? $committee->manager_user_id,
            ]);

            if (array_key_exists('users', $data) && is_array($data['users'])) {
                $committee->users()->sync($data['users']);
            }

            return $committee->load(['department', 'manager', 'users']);
        });
    }

    /**
     * حذف لجنة
     */
    public function delete(Committee $committee): bool
    {
        return DB::transaction(function () use ($committee) {
            $committee->users()->detach();
            return $committee->delete();
        });
    }
}