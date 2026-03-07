<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Procurement\StoreProcurementRequest;
use App\Http\Requests\Procurement\UpdateProcurementRequest;
use App\Http\Resources\ProcurementResource;
use App\Models\Procurement;
use App\Services\ProcurementService;
use Illuminate\Http\JsonResponse;
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
    public function index(Request $request): JsonResponse
    {
        $procurements = $this->service->getAll();

        return response()->json([
            'status' => true,
            'data'   => ProcurementResource::collection($procurements),
        ]);
    }

    /**
     * عرض عملية شراء محددة
     */
    public function show(Procurement $procurement): JsonResponse
    {
        $procurement = $this->service->getById($procurement);

        return response()->json([
            'status' => true,
            'data'   => new ProcurementResource($procurement),
        ]);
    }

    /**
     * إنشاء عملية شراء جديدة
     */
    public function store(StoreProcurementRequest $request): JsonResponse
    {
        $procurement = $this->service->create($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'تم إنشاء عملية الشراء بنجاح',
            'data'    => new ProcurementResource($procurement),
        ], 201);
    }

    /**
     * تحديث عملية شراء محددة
     */
    public function update(UpdateProcurementRequest $request, Procurement $procurement): JsonResponse
    {
        $procurement = $this->service->update($procurement, $request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'تم تحديث عملية الشراء بنجاح',
            'data'    => new ProcurementResource($procurement),
        ]);
    }

    /**
     * حذف عملية شراء محددة
     */
    public function destroy(Procurement $procurement): JsonResponse
    {
        $this->service->delete($procurement);

        return response()->json([
            'status'  => true,
            'message' => 'تم حذف عملية الشراء بنجاح',
        ]);
    }

    /**
     * تأكيد اكتمال العملية
     */
    public function markAsCompleted(Procurement $procurement): JsonResponse
    {
        $procurement = $this->service->markAsCompleted($procurement);

        return response()->json([
            'status'  => true,
            'message' => 'تم تأكيد اكتمال عملية الشراء',
            'data'    => new ProcurementResource($procurement),
        ]);
    }
}