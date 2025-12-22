<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Vendors\StoreVendorRequest;
use App\Http\Requests\Vendors\UpdateVendorRequest;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use App\Services\VendorService;

class VendorController extends Controller
{
    protected VendorService $service;

    public function __construct(VendorService $service)
    {
        $this->service = $service;
    }

    // جلب كل Vendors مع إمكانية الفلاتر و pagination
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'all']); // فقط الفلاتر المسموح بها
        $vendors = $this->service->getAll($filters);

        return response()->json([
            'status' => true,
            'data' => $vendors, // الخدمة تعيد Resource Collection
        ]);
    }

    // جلب جميع Vendors مع العلاقات (creator + estimates)
    public function indexWithRelations(Request $request)
    {
        $filters = $request->only(['search', 'all']);
        $vendors = $this->service->getAllWithRelation($filters);

        return response()->json([
            'status' => true,
            'data' => $vendors,
        ]);
    }

    // عرض Vendor واحد
    public function show(Vendor $vendor)
    {
        $vendor = $this->service->getById($vendor);
        return new VendorResource($vendor);
    }

    // إنشاء Vendor جديد
    public function store(StoreVendorRequest $request)
    {
        $vendor = $this->service->createNew($request->validated());
        return response()->json([
            'status' => true,
            'data' => $vendor,
        ]);
    }

    // تحديث Vendor موجود
    public function update(UpdateVendorRequest $request, Vendor $vendor)
    {
        $vendor = $this->service->updateVendorInfo($vendor, $request->validated());
        return response()->json([
            'status' => true,
            'data' => $vendor,
        ]);
    }

    // حذف Vendor
    public function destroy(Vendor $vendor)
    {
        $this->service->deleteOne($vendor);
        return response()->json([
            'status' => true,
            'message' => 'Vendor was deleted successfully',
        ]);
    }
}