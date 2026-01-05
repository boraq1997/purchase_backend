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
use App\Services\ActivityLogService;


class CommitteeController extends Controller
{
    protected CommitteeService $service;
    protected ActivityLogService $activityLog;
    

    public function __construct(
        CommitteeService $service,
        ActivityLogService $activityLog
    )
    {
        $this->service = $service;
        $this->activityLog = $activityLog;

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
        $data = $request->validated();
        $committee = $this->service->create($data);

        // =============================
        // Log activity
        // =============================
        $this->activityLog->log(
            action: 'create_committee',
            actionLabel: 'إنشاء لجنة جديدة',
            actorId: auth()->id(),
            actorName: auth()->user()?->name,
            actorType: 'User',
            subjectType: 'Committee',
            subjectId: $committee->id,
            subjectIdentifier: $committee->id,
            newValues: $committee->toArray(),
            module: 'committees'
        );

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
        $oldValues = $committee->toArray();
        $updatedData = $request->validated();
        $committee = $this->service->update($committee, $updatedData);

        $changedFields = array_keys(array_diff_assoc($committee->toArray(), $oldValues));

        // =============================
        // Log activity
        // =============================
        $this->activityLog->log(
            action: 'update_committee',
            actionLabel: 'تحديث بيانات اللجنة',
            actorId: auth()->id(),
            actorName: auth()->user()?->name,
            actorType: 'User',
            subjectType: 'Committee',
            subjectId: $committee->id,
            subjectIdentifier: $committee->id,
            oldValues: $oldValues,
            newValues: $committee->toArray(),
            changedFields: $changedFields,
            module: 'committees'
        );

        return new CommitteeResource($committee->load(['department', 'users.department', 'manager']));
    }

    /**
     * حذف لجنة محددة
     */
    public function destroy(Committee $committee): JsonResponse
    {
        $oldValues = $committee->toArray();
        $this->service->delete($committee);

        // =============================
        // Log activity
        // =============================
        $this->activityLog->log(
            action: 'delete_committee',
            actionLabel: 'حذف لجنة',
            actorId: auth()->id(),
            actorName: auth()->user()?->name,
            actorType: 'User',
            subjectType: 'Committee',
            subjectId: $committee->id,
            subjectIdentifier: $committee->id,
            oldValues: $oldValues,
            module: 'committees',
            status: 'success',
            severity: 'critical'
        );

        return response()->json([
            'status'  => true,
            'message' => 'Committee deleted successfully',
        ]);
    }
}