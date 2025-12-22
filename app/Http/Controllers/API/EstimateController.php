<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Estimate\StoreEstimateRequest;
use App\Http\Requests\Estimate\UpdateEstimateRequest;
use App\Http\Requests\Estimate\StoreEstimateWithItemsRequest;
use App\Http\Resources\EstimateResource;
use App\Models\Estimate;
use App\Services\EstimateService;
use Illuminate\Http\Request;

class EstimateController extends Controller
{
    protected EstimateService $service;

    public function __construct(EstimateService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض جميع عروض الأسعار مع الفلاتر
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'vendor_id',
            'department_id',
            'request_title',
            'status',
        ]);

        $perPage = $request->integer('per_page', 15);

        $estimates = $this->service
            ->getAll($filters)
            ->paginate($perPage);

        return EstimateResource::collection($estimates);
    }

    /**
     * عرض عرض سعر محدد
     */
    public function show(Estimate $estimate)
    {
        $estimate = $this->service->getById($estimate);

        return new EstimateResource($estimate);
    }

    /**
     * جلب آخر عرض سعر لمادة معيّنة
     */
    public function getByItem(int $requestItemId)
    {
        $estimate = $this->service->getByItem($requestItemId);

        if (!$estimate) {
            return response()->json(['data' => null], 200);
        }

        return new EstimateResource($estimate);
    }

    /**
     * إنشاء عرض سعر لمادة واحدة
     * POST /request-items/{requestItem}/estimates
     */
    public function storeForItem(StoreEstimateRequest $request, int $requestItem)
    {
        $estimate = $this->service->createForItem(
            $requestItem,
            $request->validated()
        );

        return new EstimateResource($estimate);
    }

    /**
     * إنشاء عرض سعر مع عناصر متعددة
     * POST /purchase-requests/{purchaseRequest}/estimates/with-items
     */
    public function storeWithItems(
        StoreEstimateWithItemsRequest $request,
        int $purchaseRequest
    ) {
        $data  = $request->validated();
        $items = $data['items'];

        unset($data['items']);

        $estimate = $this->service->createWithItems(
            $purchaseRequest,
            $data,
            $items
        );

        return new EstimateResource($estimate);
    }

    /**
     * تحديث عرض سعر
     */
    public function update(UpdateEstimateRequest $request, Estimate $estimate)
    {
        $estimate = $this->service->update(
            $estimate,
            $request->validated()
        );

        return new EstimateResource($estimate);
    }

    /**
     * حذف عرض سعر
     */
    public function destroy(Estimate $estimate)
    {
        $this->service->delete($estimate);

        return response()->json([
            'status'  => true,
            'message' => 'Estimate deleted successfully',
        ]);
    }
}