<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcurementItem\StoreProcurementItemRequest;
use App\Http\Requests\ProcurementItem\UpdateProcurementItemRequest;
use App\Http\Resources\ProcurementItemResource;
use App\Models\ProcurementItem;
use App\Services\ProcurementItemService;
use Illuminate\Http\Request;

class ProcurementItemController extends Controller
{
    protected ProcurementItemService $service;

    public function __construct(ProcurementItemService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض جميع عناصر المشتريات
     */
    public function index(Request $request)
    {
        $filters = $request->only(['procurement_id']);
        $items = $this->service->getAll($filters);

        return ProcurementItemResource::collection($items);
    }

    /**
     * عرض عنصر محدد
     */
    public function show(ProcurementItem $procurementItem)
    {
        $item = $this->service->getById($procurementItem);
        return new ProcurementItemResource($item->load('procurement'));
    }

    /**
     * إنشاء عنصر جديد في عملية شراء
     */
    public function store(StoreProcurementItemRequest $request)
    {
        $item = $this->service->create($request->validated());
        return new ProcurementItemResource($item->load('procurement'));
    }

    /**
     * تحديث عنصر شراء محدد
     */
    public function update(UpdateProcurementItemRequest $request, ProcurementItem $procurementItem)
    {
        $item = $this->service->update($procurementItem, $request->validated());
        return new ProcurementItemResource($item->load('procurement'));
    }

    /**
     * حذف عنصر شراء محدد
     */
    public function destroy(ProcurementItem $procurementItem)
    {
        $this->service->delete($procurementItem);

        return response()->json([
            'status' => true,
            'message' => 'Procurement item deleted successfully',
        ]);
    }
}