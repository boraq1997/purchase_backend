<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseImage\StorePurchaseRequestImagesRequest;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestImage;
use App\Services\PurchaseRequestImageService;
use Illuminate\Http\JsonResponse;

class PurchaseRequestImageController extends Controller
{
    public function __construct(
        protected PurchaseRequestImageService $service
    ) {}

    public function indexByPurchaseRequest($purchaseRequestId): JsonResponse {
        $images = $this->service->getByPurchaseRequestId($purchaseRequestId);
        return response()->json([
            'data' => $images
        ]);
    }

    public function show($id): JsonResponse {
        $image = $this->service->getById($id);
        return response()->json([
            'data' => $image
        ]);
    }

    public function store(StorePurchaseRequestImagesRequest $request): JsonResponse
    {
        $purchaseRequest = PurchaseRequest::findOrFail(
            $request->purchase_request_id
        );

        $images = $this->service->uploadImages(
            $purchaseRequest,
            $request->file('images')
        );

        return response()->json([
            'message' => 'تم رفع الصور بنجاح.',
            'data'    => $images
        ], 201);
    }

    public function destroy(PurchaseRequestImage $image): JsonResponse
    {
        $this->service->deleteImage($image);

        return response()->json([
            'message' => 'تم حذف الصورة بنجاح.'
        ]);
    }
}
