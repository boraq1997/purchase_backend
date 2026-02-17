<?php

namespace App\Services;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PurchaseRequestImageService
{
    protected string $disk = 'public';


    public function getByPurchaseRequestId(int $purchaseRequestId) {
        return PurchaseRequestImage::where('purchase_request_id', $purchaseRequestId)->latest()->get();
    }

    public function getById(int $imageId): PurchaseRequestImage {
        return PurchaseRequestImage::findOrFail($imageId);
    }

    /**
     * رفع عدة صور لطلب شراء
     */
    public function uploadImages(PurchaseRequest $purchaseRequest, array $images): array
    {
        $uploadedImages = [];

        foreach ($images as $image) {

            $fileName = Str::uuid() . '.' . $image->getClientOriginalExtension();

            $path = $image->storeAs(
                "purchase_requests/{$purchaseRequest->id}",
                $fileName,
                $this->disk
            );

            $uploadedImages[] = PurchaseRequestImage::create([
                'purchase_request_id' => $purchaseRequest->id,
                'file_name'           => $fileName,
                'file_path'           => $path,
                'file_type'           => $image->getClientMimeType(),
                'file_size'           => $image->getSize(),
                'uploaded_by'         => auth()->id(),
            ]);
        }

        return $uploadedImages;
    }

    /**
     * حذف صورة
     */
    public function deleteImage(PurchaseRequestImage $image): void
    {
        if (Storage::disk($this->disk)->exists($image->file_path)) {
            Storage::disk($this->disk)->delete($image->file_path);
        }

        $image->delete();
    }
}