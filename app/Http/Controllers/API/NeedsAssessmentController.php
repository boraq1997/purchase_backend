<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NeedsAssessment\StoreNeedsAssessmentRequest;
use App\Http\Requests\NeedsAssessment\UpdateNeedsAssessmentRequest;
use App\Http\Resources\NeedsAssessmentResource;
use App\Models\NeedsAssessment;
use App\Services\NeedsAssessmentService;
use Illuminate\Http\Request;

class NeedsAssessmentController extends Controller
{
    protected NeedsAssessmentService $service;

    public function __construct(NeedsAssessmentService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض جميع تقييمات الحاجة مع الفلترة
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'purchase_request_id',
            'request_item_id',
            'urgency_level',
            'needs_status',
            'assessment_state',
            'admin_decision',
        ]);

        $assessments = $this->service->getAll($filters);

        return NeedsAssessmentResource::collection($assessments);
    }

    /**
     * إنشاء تقييم حاجة جديد
     */
    public function store(StoreNeedsAssessmentRequest $request)
    {
        $data = $request->validated();
        $data['assessed_by'] = auth()->id();
        $assessment = $this->service->create($data);

        return new NeedsAssessmentResource($assessment);
    }

    /**
     * عرض تقييم حاجة محدد
     */
    public function show(NeedsAssessment $needsAssessment)
    {
        $assessment = $this->service->getById($needsAssessment);

        return new NeedsAssessmentResource($assessment);
    }

    public function getByItemAndRequest($purchaseRequestId, $requestItemId) {
        $assessment = $this->service->getByItemAndRequest($purchaseRequestId, $requestItemId);

        if (!$assessment) {
            return response()->json([
                'status' => false,
                'message' => 'needs assessment not found'
            ], 404);
        }
        return new NeedsAssessmentResource($assessment);
    }

    /**
     * تحديث تقييم موجود
     */
    public function update(UpdateNeedsAssessmentRequest $request, NeedsAssessment $needsAssessment)
    {
        $data = $request->validated();
        $data['assessed_by'] = auth()->id();
        $assessment = $this->service->update($needsAssessment, $data);

        return new NeedsAssessmentResource($assessment);
    }

    /**
     * حذف تقييم
     */
    public function destroy(NeedsAssessment $needsAssessment)
    {
        $this->service->delete($needsAssessment);

        return response()->json([
            'status'  => true,
            'message' => 'Needs assessment deleted successfully',
        ]);
    }
}