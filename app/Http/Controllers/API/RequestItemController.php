<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestItem\StoreRequestItemRequest;
use App\Http\Requests\RequestItem\UpdateRequestItemRequest;
use App\Http\Resources\RequestItemResource;
use App\Models\RequestItem;
use App\Services\RequestItemService;
use Illuminate\Http\Request;

class RequestItemController extends Controller
{
    protected RequestItemService $service;

    public function __construct(RequestItemService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض جميع المواد المطلوبة
     */
    public function index(Request $request)
    {
        $filters = $request->only(['purchase_request_id', 'status']);
        $items = $this->service->getAll($filters);

        return RequestItemResource::collection($items);
    }

    /**
     * عرض مادة محددة
     */
    public function show(RequestItem $requestItem)
    {
        $item = $this->service->getById($requestItem);
        return new RequestItemResource($item);
    }

    /**
     * إنشاء مادة جديدة ضمن طلب شراء
     */
    public function store(StoreRequestItemRequest $request)
    {
        $item = $this->service->create($request->validated());
        return new RequestItemResource($item->load('purchaseRequest'));
    }

    /**
     * تحديث مادة محددة
     */
    public function update(UpdateRequestItemRequest $request, RequestItem $requestItem)
    {
        $item = $this->service->update($requestItem, $request->validated());
        return new RequestItemResource($item->load('purchaseRequest'));
    }

    /**
     * حذف مادة محددة
     */
    public function destroy(RequestItem $requestItem)
    {
        $this->service->delete($requestItem);

        return response()->json([
            'status' => true,
            'message' => 'Request item deleted successfully',
        ]);
    }
}