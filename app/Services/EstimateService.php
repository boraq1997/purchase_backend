<?php

namespace App\Services;

use App\Models\Estimate;
use App\Models\RequestItem;
use App\Models\EstimateItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class EstimateService
{
    /**
     * إنشاء عرض سعر بدون عناصر (حالة بسيطة)
     */
    public function create(array $data): Estimate
    {
        return DB::transaction(function () use ($data) {
            $estimate = Estimate::create($data);

            return $estimate->load([
                'vendor',
                'purchaseRequest',
                'requestItem',
                'creator',
            ]);
        });
    }

    /**
     * إنشاء عرض سعر مع عناصر متعددة
     */
    public function createWithItems(
        int $purchaseRequestId,
        array $estimateData,
        array $itemsData
    ): Estimate {
        return DB::transaction(function () use ($purchaseRequestId, $estimateData, $itemsData) {

            $requestItemIds = collect($itemsData)->pluck('request_item_id')->unique()->toArray();

            $requestItems = RequestItem::where('purchase_request_id', $purchaseRequestId)
                ->whereIn('id', $requestItemIds)
                ->get();

            if ($requestItems->count() !== count($requestItemIds)) {
                throw ValidationException::withMessages([
                    'items' => ['بعض المواد لا تنتمي إلى طلب الشراء المحدد'],
                ]);
            }

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

            $estimate->update([
                'total_amount' => $totalAmount,
            ]);

            return $estimate->load([
                'vendor',
                'purchaseRequest',
                'estimateItems.requestItem',
                'creator',
            ]);
        });
    }

    /**
     * تحديث عرض سعر
     */
    public function update(Estimate $estimate, array $data): Estimate
    {
        return DB::transaction(function () use ($estimate, $data) {
            $estimate->update($data);

            return $estimate->load([
                'vendor',
                'purchaseRequest',
                'requestItem',
                'estimateItems.requestItem',
                'creator',
            ]);
        });
    }

    /**
     * حذف عرض سعر
     */
    public function delete(Estimate $estimate): bool
    {
        return DB::transaction(fn () => $estimate->delete());
    }

    /**
     * جلب جميع عروض الأسعار مع الفلاتر
     */
    public function getAll(array $filters = []): Builder
    {
        $q = Estimate::with([
            'vendor',
            'purchaseRequest',
            'estimateItems.requestItem',
            'creator',
        ]);

        if (!empty($filters['vendor_id'])) {
            $q->where('vendor_id', $filters['vendor_id']);
        }

        if (!empty($filters['department_id'])) {
            $q->whereHas('purchaseRequest', function ($query) use ($filters) {
                $query->where('department_id', $filters['department_id']);
            });
        }

        if (!empty($filters['request_title'])) {
            $q->whereHas('purchaseRequest', function ($query) use ($filters) {
                $query->where('title', 'LIKE', '%' . $filters['request_title'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }

        return $q->latest('id');
    }

    /**
     * جلب عرض سعر محدد
     */
    public function getById(Estimate $estimate): Estimate
    {
        return $estimate->load([
            'vendor',
            'purchaseRequest',
            'requestItem',
            'estimateItems.requestItem',
            'creator',
        ]);
    }

    /**
     * جلب عرض سعر مرتبط بمادة معينة
     */
    public function getByItem(int $requestItemId): ?Estimate
    {
        return Estimate::with([
                'vendor',
                'purchaseRequest',
                'estimateItems.requestItem',
            ])
            ->where('request_item_id', $requestItemId)
            ->latest()
            ->first();
    }

    /**
     * إنشاء عرض سعر لمادة واحدة
     */
    public function createForItem(int $requestItemId, array $data): Estimate
    {
        return DB::transaction(function () use ($requestItemId, $data) {

            $item = RequestItem::findOrFail($requestItemId);

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

            return $estimate->load([
                'vendor',
                'requestItem',
                'purchaseRequest',
                'creator',
            ]);
        });
    }
}