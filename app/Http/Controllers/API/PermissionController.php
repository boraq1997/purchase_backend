<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    protected PermissionService $service;

    public function __construct(PermissionService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض جميع الصلاحيات مع إمكانية الفلترة
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['name', 'guard_name']);
        $permissions = $this->service->getAll($filters);

        return response()->json([
            'status' => true,
            'data'   => PermissionResource::collection($permissions),
        ]);
    }

    /**
     * إنشاء صلاحية جديدة
     */
    public function store(StorePermissionRequest $request): PermissionResource
    {
        $permission = $this->service->create($request->validated());
        return new PermissionResource($permission);
    }

    /**
     * عرض صلاحية محددة
     */
    public function show(Permission $permission): PermissionResource
    {
        return new PermissionResource($permission);
    }

    /**
     * تحديث صلاحية محددة
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): PermissionResource
    {
        $permission = $this->service->update($permission, $request->validated());
        return new PermissionResource($permission);
    }

    /**
     * حذف صلاحية محددة
     */
    public function destroy(Permission $permission): JsonResponse
    {
        $this->service->delete($permission);

        return response()->json([
            'status'  => true,
            'message' => 'Permission deleted successfully',
        ]);
    }
}