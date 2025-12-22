<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EstimateItem\StoreEstimateItemRequest;
use App\Http\Requests\EstimateItem\UpdateEstimateItemRequest;
use App\Http\Resources\EstimateItemResource;
use App\Models\EstimateItem;
use App\Services\EstimateItemService;
use Illuminate\Http\Request;

class EstimateItemController extends Controller
{
    protected EstimateItemService $service;

    public function __construct(EstimateItemService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض جميع العناصر الخاصة بعروض الأسعار
     */
    public function index(Request $request)
    {
        $filters = $request->only(['estimate_id']);
        $items = $this->service->getAll($filters);

        return EstimateItemResource::collection($items);
    }

    /**
     * عرض عنصر محدد
     */
    public function show(EstimateItem $estimateItem)
    {
        $item = $this->service->getById($estimateItem);
        return new EstimateItemResource($item->load('estimate'));
    }

    /**
     * إنشاء عنصر جديد ضمن عرض سعر
     */
    public function store(StoreEstimateItemRequest $request)
    {
        $item = $this->service->create($request->validated());
        return new EstimateItemResource($item->load('estimate'));
    }

    /**
     * تحديث عنصر عرض السعر
     */
    public function update(UpdateEstimateItemRequest $request, EstimateItem $estimateItem)
    {
        $item = $this->service->update($estimateItem, $request->validated());
        return new EstimateItemResource($item->load('estimate'));
    }

    /**
     * حذف عنصر عرض سعر
     */
    public function destroy(EstimateItem $estimateItem)
    {
        $this->service->delete($estimateItem);

        return response()->json([
            'status' => true,
            'message' => 'Estimate item deleted successfully',
        ]);
    }
}