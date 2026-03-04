<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\EstimateImage\StoreEstimateImageRequest;
use App\Http\Resources\EstimateImageResource;
use App\Models\Estimate;
use App\Models\EstimateImage;
use App\Services\EstimateImageService;
use Illuminate\Http\JsonResponse;

class EstimateImageController extends Controller
{
    public function __construct(
        protected EstimateImageService $service
    ) {}

    public function indexByEstimate($estimateId): JsonResponse {
        $images = $this->service->getByEstimateId($estimateId);
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

    public function store(StoreEstimateImageRequest $request): JsonResponse {
        $estimate = Estimate::findOrFail(
            $request->estimate_id
        );

        $images = $this->service->uploadImages(
            $estimate,
            $request->file('images')
        );

        return response()->json([
            'message' => 'تم رفع الصور بنجاح',
            'data' => $images
        ], 201);
    }

    public function destroy(EstimateImage $estimateImage): JsonResponse {
        $this->service->deleteImage($estimateImage);

        return response()->json([
            'message' => 'تم حذف الصورة بنجاح',
        ]);
    }
}
