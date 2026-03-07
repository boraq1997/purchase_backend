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
     * Get all estimates with optional filters
     * GET /estimates
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
     * Get a single estimate
     * GET /estimates/{estimate}
     */
    public function show(Estimate $estimate)
    {
        $estimate = $this->service->getById($estimate);

        return new EstimateResource($estimate);
    }

    /**
     * Get the latest estimate linked to a specific request item
     * GET /request-items/{requestItemId}/estimate
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
    * GET /purchase-requests/{purchaseRequest}/estimates
    */
    public function getByPurchaseRequest(int $purchaseRequest)
    {
        $estimates = $this->service->getByPurchaseRequest($purchaseRequest);

        return EstimateResource::collection($estimates);
    }

    /**
     * Create a simple estimate for a single request item
     * POST /request-items/{requestItem}/estimates
     */
    public function storeForItem(StoreEstimateRequest $request, int $requestItem)
    {
        $data           = $request->validated();
        $data['images'] = $request->file('images');

        $estimate = $this->service->createForItem($requestItem, $data);

        return new EstimateResource($estimate);
    }

    /**
     * Create an estimate with multiple items
     * POST /purchase-requests/{purchaseRequest}/estimates/with-items
     */
    public function storeWithItems(
        StoreEstimateWithItemsRequest $request,
        int $purchaseRequest
    ) {
        $data           = $request->validated();
        $data['images'] = $request->file('images');
        $items          = $data['items'];

        unset($data['items']);

        $estimate = $this->service->createWithItems(
            $purchaseRequest,
            $data,
            $items
        );

        return new EstimateResource($estimate);
    }

    /**
     * Update an existing estimate
     * PUT /estimates/{estimate}
     */
    public function update(UpdateEstimateRequest $request, Estimate $estimate)
    {
        $data           = $request->validated();
        $data['images'] = $request->file('images'); // files are not included in validated()

        $estimate = $this->service->update($estimate, $data);

        return new EstimateResource($estimate);
    }

    /**
     * Delete an estimate
     * DELETE /estimates/{estimate}
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