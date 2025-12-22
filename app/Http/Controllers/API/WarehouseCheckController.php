<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseCheck\StoreWarehouseCheckRequest;
use App\Http\Requests\WarehouseCheck\UpdateWarehouseCheckRequest;
use App\Http\Resources\WarehouseCheckResource;
use App\Models\WarehouseCheck;
use App\Services\WarehouseCheckService;

class WarehouseCheckController extends Controller
{
    protected WarehouseCheckService $service;

    public function __construct(WarehouseCheckService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $checks = $this->service->getAll();
        return WarehouseCheckResource::collection($checks);
    }

    public function showItemReport($purchaseRequestId, $requestItemId)
    {
        $check = WarehouseCheck::where('purchase_request_id', $purchaseRequestId)
            ->where('request_item_id', $requestItemId)
            ->first();

        if (!$check) {
            return response()->json(['message' => 'No warehouse check found'], 404);
        }

        return new WarehouseCheckResource($check->load(['purchaseRequest', 'requestItem', 'checkedBy']));
    }

    /**
     * إنشاء أو تحديث فحص المستودع
     */
    public function store(StoreWarehouseCheckRequest $request)
    {
        $data = $request->validated();
        $data['checked_by'] = auth()->id();
        $check = $this->service->create($data);
        return new WarehouseCheckResource($check->load(['purchaseRequest', 'requestItem', 'checkedBy']));
    }

    public function update(UpdateWarehouseCheckRequest $request, WarehouseCheck $warehouseCheck)
    {
        $data = $request->validated();
        $data['checked_by'] = auth()->id();
        $updated = $this->service->update($warehouseCheck, $data);
        return new WarehouseCheckResource($updated);
    }

    /**
     * عرض فحص محدد
     */
    public function show(WarehouseCheck $warehouseCheck)
    {
        $check = $this->service->getById($warehouseCheck);
        return new WarehouseCheckResource($check);
    }

    public function destroy(WarehouseCheck $warehouseCheck)
    {
        $this->service->delete($warehouseCheck);
        return response()->json(['message' => 'Warehouse check deleted successfully']);
    }
}