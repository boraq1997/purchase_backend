<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Procurement\StoreProcurementRequest;
use App\Http\Requests\Procurement\UpdateProcurementRequest;
use App\Http\Resources\ProcurementResource;
use App\Models\Procurement;
use App\Services\ProcurementService;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    protected ProcurementService $service;

    public function __construct(ProcurementService $service)
    {
        $this->service = $service;
    }

    /**
     * عرض جميع عمليات الشراء
     */
    public function index(Request $request)
    {
        $filters = $request->only(['estimate_id', 'status', 'department_id']);
        $procurements = $this->service->getAll($filters);

        return ProcurementResource::collection($procurements);
    }

    /**
     * عرض عملية شراء محددة
     */
    public function show(Procurement $procurement)
    {
        $procurement = $this->service->getById($procurement);
        return new ProcurementResource($procurement->load(['estimate', 'procurementItems']));
    }

    /**
     * إنشاء عملية شراء جديدة
     */
    public function store(StoreProcurementRequest $request)
    {
        $procurement = $this->service->create($request->validated());
        return new ProcurementResource($procurement->load(['estimate', 'procurementItems']));
    }

    /**
     * تحديث عملية شراء محددة
     */
    public function update(UpdateProcurementRequest $request, Procurement $procurement)
    {
        $procurement = $this->service->update($procurement, $request->validated());
        return new ProcurementResource($procurement->load(['estimate', 'procurementItems']));
    }

    /**
     * حذف عملية شراء محددة
     */
    public function destroy(Procurement $procurement)
    {
        $this->service->delete($procurement);

        return response()->json([
            'status' => true,
            'message' => 'Procurement deleted successfully',
        ]);
    }
}