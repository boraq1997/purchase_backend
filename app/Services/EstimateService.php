<?php

namespace App\Services;

use App\Models\Estimate;
use App\Models\RequestItem;
use App\Models\EstimateItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\EstimateImage;

class EstimateService
{
    protected ActivityLogService $logService;

    public function __construct() {
        $this->logService = new ActivityLogService();
    }

    /*
    |--------------------------------------------------------------------------
    | Get all estimates with optional filters
    |--------------------------------------------------------------------------
    */
    public function getAll(array $filters = [])
    {
        $q = Estimate::with([
            'vendor',
            'purchaseRequest',
            'requestItem',
            'estimateItems.requestItem',
            'creator',
            'images',
        ]);

        if (!empty($filters['vendor_id'])) {
            $q->where('vendor_id', $filters['vendor_id']);
        }

        if (!empty($filters['department_id'])) {
            $q->whereHas('purchaseRequest', function (Builder $query) use ($filters) {
                $query->where('department_id', $filters['department_id']);
            });
        }

        if (!empty($filters['request_title'])) {
            $q->whereHas('purchaseRequest', function (Builder $query) use ($filters) {
                $query->where('title', 'like', '%' . $filters['request_title'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }

        $this->logService->log(
            action: 'view_estimates',
            actionLabel: 'View all estimates',
            subjectType: Estimate::class,
            metadata: ['filters' => $filters],
            module: 'Estimates'
        );

        return $q->latest('id');
    }

    /*
    |--------------------------------------------------------------------------
    | Get a single estimate by model
    |--------------------------------------------------------------------------
    */
    public function getById(Estimate $estimate): Estimate
    {
        $estimate = $estimate->load([
            'vendor',
            'purchaseRequest',
            'requestItem',
            'estimateItems.requestItem',
            'creator',
            'images',
        ]);

        $this->logService->log(
            action: 'view_estimate',
            actionLabel: 'View estimate',
            subjectType: Estimate::class,
            subjectId: $estimate->id,
            module: 'Estimates'
        );

        return $estimate;
    }

    /*
    |--------------------------------------------------------------------------
    | Get the latest estimate linked to a specific request item
    |--------------------------------------------------------------------------
    */
    public function getByItem(int $requestItemId): ?Estimate
    {
        $estimate = Estimate::with([
            'vendor',
            'purchaseRequest',
            'estimateItems.requestItem',
            'images.uploader',
        ])
            ->where('request_item_id', $requestItemId)
            ->latest()
            ->first();

        $this->logService->log(
            action: 'view_estimate_by_item',
            actionLabel: 'View estimate by request item',
            subjectType: Estimate::class,
            subjectId: $estimate?->id,
            metadata: ['request_item_id' => $requestItemId],
            module: 'Estimates'
        );

        return $estimate;
    }

    /*
    |--------------------------------------------------------------------------
    | Create a simple estimate for a single request item
    |--------------------------------------------------------------------------
    */
    public function createForItem(int $requestItemId, array $data): Estimate
    {
        return DB::transaction(function () use ($requestItemId, $data) {

            $item = RequestItem::findOrFail($requestItemId);

            $images = $data['images'] ?? null;
            unset($data['images']);

            $estimate = Estimate::create([
                'purchase_request_id' => $item->purchase_request_id,
                'request_item_id'     => $item->id,
                'vendor_id'           => $data['vendor_id'] ?? null,
                'estimate_date'       => $data['estimate_date'] ?? now(),
                'total_amount'        => $data['total_amount'] ?? 0,
                'notes'               => $data['notes'] ?? null,
                'status'              => $data['status'] ?? 'pending',
                'created_by'          => auth()->id(),
            ]);

            // Upload images if provided
            $this->handleImage($estimate, $images);

            $this->logService->log(
                action: 'create_estimate_for_item',
                actionLabel: 'Create estimate for request item',
                subjectType: Estimate::class,
                subjectId: $estimate->id,
                newValues: $estimate->toArray(),
                metadata: ['request_item_id' => $requestItemId],
                module: 'Estimates'
            );

            return $estimate->load([
                'vendor',
                'requestItem',
                'purchaseRequest',
                'creator',
                'images',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Create an estimate with multiple items
    |--------------------------------------------------------------------------
    */
    public function createWithItems(
        int $purchaseRequestId,
        array $estimateData,
        array $itemsData
    ): Estimate {
        return DB::transaction(function () use ($purchaseRequestId, $estimateData, $itemsData) {

            // Validate that all request items belong to the given purchase request
            $requestItemIds = collect($itemsData)->pluck('request_item_id')->unique()->toArray();

            $requestItems = RequestItem::where('purchase_request_id', $purchaseRequestId)
                ->whereIn('id', $requestItemIds)
                ->get();

            if ($requestItems->count() !== count($requestItemIds)) {
                throw ValidationException::withMessages([
                    'items' => ['Some items do not belong to the specified purchase request.'],
                ]);
            }

            // Extract images before creating the estimate
            $images = $estimateData['images'] ?? null;
            unset($estimateData['images']);

            $estimate = Estimate::create([
                'purchase_request_id' => $purchaseRequestId,
                'request_item_id'     => null,
                'vendor_id'           => $estimateData['vendor_id'] ?? null,
                'estimate_date'       => $estimateData['estimate_date'] ?? now(),
                'total_amount'        => 0,
                'notes'               => $estimateData['notes'] ?? null,
                'status'              => $estimateData['status'] ?? 'pending',
                'created_by'          => auth()->id(),
            ]);

            // Create estimate items and calculate total
            $totalAmount = 0;

            foreach ($itemsData as $itemData) {
                $requestItem = $requestItems->firstWhere('id', $itemData['request_item_id']);

                $quantity   = $itemData['quantity'] ?? $requestItem->quantity ?? 1;
                $unitPrice  = $itemData['unit_price'];
                $totalPrice = $quantity * $unitPrice;

                EstimateItem::create([
                    'estimate_id'     => $estimate->id,
                    'request_item_id' => $requestItem->id,
                    'item_name'       => $requestItem->item_name,
                    'quantity'        => $quantity,
                    'unit_price'      => $unitPrice,
                    'total_price'     => $totalPrice,
                    'notes'           => $itemData['notes'] ?? null,
                ]);

                $totalAmount += $totalPrice;
            }

            // Update total amount after all items are created
            $estimate->update(['total_amount' => $totalAmount]);

            // Upload images once after all items are processed (not inside the loop)
            $this->handleImage($estimate, $images);

            $this->logService->log(
                action: 'create_estimate_with_items',
                actionLabel: 'Create estimate with multiple items',
                subjectType: Estimate::class,
                subjectId: $estimate->id,
                newValues: $estimate->toArray(),
                metadata: [
                    'purchase_request_id' => $purchaseRequestId,
                    'items_count'         => count($itemsData),
                ],
                module: 'Estimates'
            );

            return $estimate->load([
                'vendor',
                'purchaseRequest',
                'estimateItems.requestItem',
                'creator',
                'images',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Update an existing estimate
    |--------------------------------------------------------------------------
    */
    public function update(Estimate $estimate, array $data): Estimate
    {
        return DB::transaction(function () use ($estimate, $data) {

            $oldValues = $estimate->toArray();

            // Extract images and items before updating
            $images    = $data['images'] ?? null;
            $itemsData = $data['items'] ?? null;
            unset($data['images'], $data['items']);

            // Update main estimate fields
            $estimate->update($data);

            // Sync estimate items if provided
            if ($itemsData !== null) {

                // Determine which request item IDs are coming from the frontend
                $incomingRequestItemIds = collect($itemsData)
                    ->pluck('request_item_id')
                    ->filter()
                    ->toArray();

                // Remove items that were deleted by the user
                $estimate->estimateItems()
                    ->whereNotIn('request_item_id', $incomingRequestItemIds)
                    ->delete();

                // Update or create each item
                foreach ($itemsData as $itemData) {
                    $quantity  = $itemData['quantity'] ?? 1;
                    $unitPrice = $itemData['unit_price'] ?? 0;

                    $estimate->estimateItems()->updateOrCreate(
                        ['request_item_id' => $itemData['request_item_id']],
                        [
                            'item_name'   => $itemData['item_name'] ?? null,
                            'quantity'    => $quantity,
                            'unit_price'  => $unitPrice,
                            'total_price' => $quantity * $unitPrice,
                            'notes'       => $itemData['notes'] ?? null,
                        ]
                    );
                }

                // Recalculate total amount based on updated items
                $estimate->update([
                    'total_amount' => $estimate->estimateItems()->sum('total_price'),
                ]);
            }

            // Upload new images if provided
            $this->handleImage($estimate, $images);

            $this->logService->log(
                action: 'update_estimate',
                actionLabel: 'Update estimate',
                subjectType: Estimate::class,
                subjectId: $estimate->id,
                oldValues: $oldValues,
                newValues: $estimate->fresh()->toArray(),
                module: 'Estimates'
            );

            return $estimate->load([
                'vendor',
                'purchaseRequest',
                'requestItem',
                'estimateItems.requestItem',
                'creator',
                'images',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Delete an estimate and its related images from storage
    |--------------------------------------------------------------------------
    */
    public function delete(Estimate $estimate): bool
    {
        return DB::transaction(function () use ($estimate) {

            $oldValues = $estimate->toArray();

            // Delete image files from storage before removing DB records
            foreach ($estimate->images as $image) {
                Storage::disk('public')->delete($image->file_path);
            }

            $estimate->images()->delete();

            $deleted = $estimate->delete();

            $this->logService->log(
                action: 'delete_estimate',
                actionLabel: 'Delete estimate',
                subjectType: Estimate::class,
                subjectId: $estimate->id,
                oldValues: $oldValues,
                status: $deleted ? 'success' : 'failed',
                module: 'Estimates'
            );

            return $deleted;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Handle image uploads for an estimate
    |--------------------------------------------------------------------------
    */
    protected function handleImage(Estimate $estimate, ?array $images): void
    {
        if (!$images) {
            return;
        }

        foreach ($images as $image) {
            if (!$image instanceof UploadedFile) {
                continue;
            }

            $fileName = Str::uuid() . '.' . $image->getClientOriginalExtension();

            $path = $image->storeAs(
                "estimates/{$estimate->id}",
                $fileName,
                'public'
            );

            $estimate->images()->create([
                'file_name'   => $fileName,
                'file_path'   => $path,
                'file_type'   => $image->getClientMimeType(),
                'file_size'   => $image->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }
    }
}