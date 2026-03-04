<?php 

namespace App\Services;

use App\Models\Estimate;
use App\Models\EstimateImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EstimateImageService {
    protected string $disk = 'public';

    public function getByEstimateId(int $estimateId) {
        return EstimateImage::where('estimate_id', $estimateId)->latest()->get();
    }

    public function getById(int $imageId) {
        return EstimateImage::findOrFail($imageId);
    }

    public function uploadImages(Estimate $estimate, array $images) {
        $uploadedImages = [];

        foreach($images as $image) {
            $fileName = Str::uuid() . '.' . $image->getClientOriginalExtension();

            $path = $image->storeAs(
                "estimate/{$estimate->id}",
                $fileName,
                $this->disk
            );

            $uploadedImages[] = EstimateImage::create([
                'estimate_id' => $estimate->id,
                'file_name' => $fileName,
                'file_path' => $path,
                'file_type' => $image->getClientMimeType(),
                'file_size' => $image->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }
        return $uploadedImages;
    }

    public function deleteImage(EstimateImage $image) {
        if (Storage::disk($this->disk)->exists($image->file_path)) {
            Storage::disk($this->disk)->delete($image->file_path);
        }
        $image->delete();
    }
}