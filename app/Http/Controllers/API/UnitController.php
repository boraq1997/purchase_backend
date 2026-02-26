<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UnitService;
use App\Http\Resources\UnitResource;
use App\Http\Requests\Unit\StoreUnitRequest;
use App\Http\Requests\Unit\UpdateUnitRequest;
use App\Models\Unit;

class UnitController extends Controller
{
    protected UnitService $service;

    public function index(Request $request) {
        $filters = $request->only(['name', 'code']);
        $units = $this->service->index($filters);

        return response()->json([
            'success' => true,
            'data' => UnitResource::collection($units),
        ]);
    }

    public function store(StoreUnitRequest $request) {
        $unit = $this->service->store($request->validated());
        return new UnitResource($unit);
    }

    public function update(UpdateUnitRequest $request, Unit $unit) {
        $unit = $this->service->update($unit, $request->validated());
        return new UnitResource($unit);
    }

    public function destroy(Unit $unit) {
        $this->service->delete($unit);
        return response()->json([
            'success' => true,
            'message' => 'unit was deleted successfuly',
        ]);
    }
}
