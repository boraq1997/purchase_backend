<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Departments\StoreDepartmentRequest;
use App\Http\Requests\Departments\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    protected DepartmentService $service;

    public function __construct(DepartmentService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض جميع الأقسام
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['manager_user_id']);
        $departments = $this->service->getAll($filters);

        return response()->json([
            'status' => true,
            'data'   => DepartmentResource::collection($departments),
        ]);
    }

    /**
     * عرض الأقسام مع المستخدمين والمديرين
     */
    public function indexWithUsers(): JsonResponse
    {
        $departments = $this->service->getAllWithUsers();

        return response()->json([
            'status' => true,
            'data'   => DepartmentResource::collection($departments),
        ]);
    }

    /**
     * إنشاء قسم جديد
     */
    public function store(StoreDepartmentRequest $request): DepartmentResource
    {
        $department = $this->service->create($request->validated());
        return new DepartmentResource($department->load(['manager', 'users']));
    }

    /**
     * عرض قسم محدد
     */
    public function show(Department $department): DepartmentResource
    {
        $department = $this->service->getById($department);
        return new DepartmentResource($department);
    }

    /**
     * تحديث قسم محدد
     */
    public function update(UpdateDepartmentRequest $request, Department $department): DepartmentResource
    {
        $department = $this->service->update($department, $request->validated());
        return new DepartmentResource($department->load(['manager', 'users']));
    }

    /**
     * حذف قسم محدد
     */
    public function destroy(Department $department): JsonResponse
    {
        $this->service->delete($department);

        return response()->json([
            'status'  => true,
            'message' => 'Department deleted successfully',
        ]);
    }
}