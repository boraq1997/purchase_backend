<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UnitService;
use App\Http\Resources\UnitResource;
use App\Http\Requests\Units\StoreUnitRequest;
use App\Http\Requests\Units\UpdateUnitRequest;
use App\Models\Unit;

class UnitController extends Controller
{
    protected UnitService $service;

    public function __construct(UnitService $service) {
        $this->service = $service;
    }
    public function index(Request $request) {
        $filters = $request->only(['serch']);
        $perPage = (int) $request->get('per_page', 15);

        $units = $this->service->index($filters, $perPage);

        return response()->json([
            'success'   => true,
            'data'      => UnitResource::collection($units),
            'meta'      => [
                'current_page'  => $units->currentPage(),
                'lastPage'      => $units->lastPage(),
                'per_page'      => $units->perPage(),
                'total'         => $units->total(),
            ],
        ]);
    }

    public function store(StoreUnitRequest $request) {
        $unit = $this->service->store($request->validated());
        return (new UnitResource($unit))->response()->setStatusCode(201);
    }

    public function update(UpdateUnitRequest $request, Unit $unit) {
        $unit = $this->service->update($unit, $request->validated());
        return new UnitResource($unit);
    }

    public function destroy(Unit $unit) {
        try {
            $this->service->delete($unit);
            return response()->json([
                'success' => true,
                'message' => 'تم حذف الوحدة بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
