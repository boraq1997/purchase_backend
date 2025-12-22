<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Committees\StoreCommitteeRequest;
use App\Http\Requests\Committees\UpdateCommitteeRequest;
use App\Http\Resources\CommitteeResource;
use App\Models\Committee;
use App\Services\CommitteeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommitteeController extends Controller
{
    protected CommitteeService $service;

    public function __construct(CommitteeService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض جميع اللجان
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['department_id', 'manager_user_id']);
        $committees = $this->service->getAll($filters);

        return response()->json([
            'status' => true,
            'data'   => CommitteeResource::collection($committees),
        ]);
    }

    /**
     * عرض اللجان مع الأعضاء والعلاقات التابعة
     */
    public function indexWithUsers(): JsonResponse
    {
        $committees = $this->service->getAllWithUsers();

        return response()->json([
            'status' => true,
            'data'   => CommitteeResource::collection($committees),
        ]);
    }

    /**
     * إنشاء لجنة جديدة
     */
    public function store(StoreCommitteeRequest $request): CommitteeResource
    {
        $committee = $this->service->create($request->validated());
        return new CommitteeResource($committee->load(['department', 'users.department', 'manager']));
    }

    /**
     * عرض لجنة محددة
     */
    public function show(Committee $committee): CommitteeResource
    {
        $committee = $this->service->getById($committee);
        return new CommitteeResource($committee);
    }

    /**
     * تحديث لجنة محددة
     */
    public function update(UpdateCommitteeRequest $request, Committee $committee): CommitteeResource
    {
        $committee = $this->service->update($committee, $request->validated());
        return new CommitteeResource($committee->load(['department', 'users.department', 'manager']));
    }

    /**
     * حذف لجنة محددة
     */
    public function destroy(Committee $committee): JsonResponse
    {
        $this->service->delete($committee);

        return response()->json([
            'status'  => true,
            'message' => 'Committee deleted successfully',
        ]);
    }
}