<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض جميع المستخدمين مع إمكانية الفلترة
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['department_id', 'status', 'role']);
        $users = $this->service->getAll($filters);

        return response()->json([
            'status' => true,
            'data'   => UserResource::collection($users),
        ]);
    }

    public function availableForDepartment(Request $request): JsonResponse
    {
        $departmentId = $request->integer('department_id');

        $users = $this->service->getAvailableForDepartment($departmentId);

        return response()->json([
            'status' => true,
            'data'   => $users,
        ]);
    }

    /**
     * إنشاء مستخدم جديد
     */
    public function store(StoreUserRequest $request): UserResource
    {
        $user = $this->service->create($request->validated());
        return new UserResource($user->load(['department', 'committees', 'roles']));
    }

    /**
     * عرض مستخدم محدد
     */
    public function show(User $user): UserResource
    {
        $user = $this->service->getById($user);
        return new UserResource($user->load(['department', 'committees', 'roles']));
    }

    /**
     * تحديث بيانات مستخدم
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $user = $this->service->update($user, $request->validated());
        return new UserResource($user->load(['department', 'committees', 'roles']));
    }

    /**
     * حذف مستخدم
     */
    public function destroy(User $user): JsonResponse
    {
        $this->service->delete($user);

        return response()->json([
            'status'  => true,
            'message' => 'User deleted successfully',
        ]);
    }
}