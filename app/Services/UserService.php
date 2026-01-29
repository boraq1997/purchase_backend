<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    /**
     * جلب المستخدمين مع الفلاتر الاختيارية
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        $q = User::with(['department', 'roles', 'permissions', 'committees']);

        if (!empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }

        if (!empty($filters['department_id'])) {
            $q->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['search'])) {
            $term = '%' . $filters['search'] . '%';
            $q->where(function ($qq) use ($term) {
                $qq->where('name', 'like', $term)
                   ->orWhere('username', 'like', $term)
                   ->orWhere('email', 'like', $term)
                   ->orWhere('phone', 'like', $term);
            });
        }

        return ($filters['all'] ?? false)
            ? $q->latest('id')->get()
            : $q->latest('id')->paginate($perPage);
    }

    /**
     * جلب مستخدم محدد مع العلاقات
     */
    public function getById(User $user): User
    {
        return $user->load(['department', 'roles', 'permissions']);
    }

    public function getAvailableForDepartment(?int $departmentId = null)
    {
        return User::select('id', 'name', 'email', 'username')
            ->where(function ($q) use ($departmentId) {
                // مستخدم بلا قسم
                $q->whereNull('department_id');

                // في حالة التعديل: أظهر مستخدمي نفس القسم
                if ($departmentId) {
                    $q->orWhere('department_id', $departmentId);
                }
            })
            ->whereNotIn('id', function ($query) use ($departmentId) {
                $query->select('manager_user_id')
                    ->from('departments')
                    ->whereNotNull('manager_user_id');

                // في حالة التعديل: استثناء مدير القسم الحالي
                if ($departmentId) {
                    $query->where('id', '!=', $departmentId);
                }
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * إنشاء مستخدم جديد
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // حالة افتراضية إن لم تُرسل
            $data['status'] = $data['status'] ?? 'active';

            /** @var User $user */
            $user = User::create($data);

            // تعيين الأدوار/الصلاحيات إن أُرسلت
            if (array_key_exists('role', $data)) {
                $user->syncRoles([$data['role']]); // مهم: syncRoles وليس assignRole
            }
            if (array_key_exists('permissions', $data) && is_array($data['permissions'])) {
                $user->syncPermissions($data['permissions']);
            }

            return $user->load(['department', 'roles', 'permissions']);
        });
    }

    /**
     * تحديث بيانات مستخدم
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {

            // 1. كلمة السر
            if (array_key_exists('password', $data)) {
                if ($data['password']) {
                    $data['password'] = Hash::make($data['password']);
                } else {
                    unset($data['password']);
                }
            }

            // 2. تحديث البيانات الأساسية
            $user->update($data);

            // 3. تعيين دور واحد فقط
            if (array_key_exists('role', $data)) {
                $user->syncRoles([$data['role']]); // مهم: syncRoles وليس assignRole
            }

            // 4. تعيين صلاحيات فردية (إن وجدت)
            if (array_key_exists('permissions', $data) && is_array($data['permissions'])) {
                $user->syncPermissions($data['permissions']);
            }

            return $user->load(['department', 'roles', 'permissions']);
        });
    }

    /**
     * حذف مستخدم
     */
    public function delete(User $user): bool
    {
        return DB::transaction(fn() => $user->delete());
    }

    /**
     * تغيير حالة المستخدم (active/inactive/suspended ...الخ)
     */
    public function setStatus(User $user, string $status): User
    {
        $allowed = ['active', 'inactive', 'suspended'];
        if (!in_array($status, $allowed, true)) {
            throw ValidationException::withMessages(['status' => 'قيمة حالة غير صالحة.']);
        }

        $user->update(['status' => $status]);
        return $user;
    }

    /**
     * تغيير كلمة المرور مع التحقق الاختياري من الحالية
     */
    public function changePassword(User $user, string $newPassword, ?string $currentPassword = null): User
    {
        if ($currentPassword !== null && !Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'كلمة المرور الحالية غير صحيحة.'
            ]);
        }

        $user->update([
            'password' => Hash::make($newPassword)
        ]);
        return $user;
    }

    /**
     * مزامنة الأدوار للمستخدم
     */
    public function syncRoles(User $user, array $roles): User
    {
        $user->syncRoles($roles);
        return $user->load('roles');
    }

    /**
     * مزامنة الصلاحيات المباشرة للمستخدم
     */
    public function syncPermissions(User $user, array $permissions): User
    {
        $user->syncPermissions($permissions);
        return $user->load('permissions');
    }
}