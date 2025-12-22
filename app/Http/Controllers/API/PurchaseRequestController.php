<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchases\StorePurchaseRequest;
use App\Http\Requests\Purchases\UpdatePurchaseRequest;
use App\Http\Resources\PurchaseRequestResource;
use App\Models\PurchaseRequest;
use App\Services\PurchaseRequestService;
use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{
    protected PurchaseRequestService $service;

    public function __construct(PurchaseRequestService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض جميع طلبات الشراء مع الفلاتر
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'department_id',
            'status_type',
            'priority',
            'date_from',
            'date_to',
        ]);
        $requests = $this->service->getAll($filters);

        return PurchaseRequestResource::collection($requests);
    }

    /**
     * إنشاء طلب شراء جديد
     */
    public function store(StorePurchaseRequest $request)
    {
        $purchaseRequest = $this->service->create($request->validated());
        return new PurchaseRequestResource($purchaseRequest->load(['creator', 'department']));
    }

    /**
     * عرض تفاصيل طلب شراء محدد
     */
    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest = $this->service->getById($purchaseRequest);
        return new PurchaseRequestResource($purchaseRequest);
    }

    /**
     * تحديث طلب شراء محدد
     */
    public function update(UpdatePurchaseRequest $request, PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest = $this->service->update($purchaseRequest, $request->validated());
        return new PurchaseRequestResource($purchaseRequest->load(['creator', 'department']));
    }

    /**
     * حذف طلب شراء محدد
     */
    public function destroy(PurchaseRequest $purchaseRequest)
    {
        $this->service->delete($purchaseRequest);
        return response()->json(['message' => 'Purchase request deleted successfully']);
    }

    /**
     * تحديث حالة الطلب (مثل الموافقة أو الرفض)
     */
    public function updateStatus(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'status' => 'required|string',
            'note'   => 'nullable|string|max:500',
        ]);

        $purchaseRequest = $this->service->changeStatus(
            $purchaseRequest,
            $request->status,
            $request->note
        );

        return new PurchaseRequestResource($purchaseRequest);
    }
}