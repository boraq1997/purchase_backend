<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected RoleService $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض جميع الأدوار مع الصلاحيات
     */
    public function index(Request $request): JsonResponse
    {
        $roles = $this->service->getAll($request->all());

        return response()->json([
            'status' => true,
            'data'   => RoleResource::collection($roles),
            'meta'   => [
                'current_page' => method_exists($roles, 'currentPage') ? $roles->currentPage() : 1,
                'total'        => method_exists($roles, 'total') ? $roles->total() : $roles->count(),
            ]
        ]);
    }

    /**
     * إنشاء دور جديد مع الصلاحيات
     */
    public function store(StoreRoleRequest $request): RoleResource
    {
        $role = $this->service->create($request->validated());
        return new RoleResource($role->load('permissions'));
    }

    /**
     * عرض تفاصيل دور محدد
     */
    public function show(Role $role): RoleResource
    {
        $role = $this->service->getById($role);
        return new RoleResource($role->load('permissions'));
    }

    /**
     * تحديث دور محدد مع صلاحياته
     */
    public function update(UpdateRoleRequest $request, Role $role): RoleResource
    {
        $role = $this->service->update($role, $request->validated());
        return new RoleResource($role->load('permissions'));
    }

    /**
     * حذف دور محدد
     */
    public function destroy(Role $role): JsonResponse
    {
        $this->service->delete($role);

        return response()->json([
            'status'  => true,
            'message' => 'Role deleted successfully',
        ]);
    }
}