<?php

namespace App\Services;

use App\Models\Estimate;
use App\Models\RequestItem;
use App\Models\EstimateItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogService;

class EstimateService
{

    protected ActivityLogService $logService;

    public function __construct() {
        $this->logService = new ActivityLogService();
    }

    /**
     * إنشاء عرض سعر بدون عناصر (حالة بسيطة)
     */
    public function create(array $data): Estimate
    {
        return DB::transaction(function () use ($data) {
            $estimate = Estimate::create($data);

            $this->logService->log(
                action: 'create_estimate',
                actionLabel: 'إنشاء عرض سعر',
                subjectType: Estimate::class,
                subjectId: $estimate->id,
                newValues: $estimate->toArray(),
                module: 'عروض الأسعار'
            );

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

            $this->logService->log(
                action: 'create_estimate_with_items',
                actionLabel: 'إنشاء عرض سعر مع عناصر',
                subjectType: Estimate::class,
                subjectId: $estimate->id,
                newValues: $estimate->toArray(),
                metadata: [
                    'purchase_request_id' => $purchaseRequestId,
                    'items_count' => count($itemsData),
                ],
                module: 'عروض الأسعار'
            );

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
            $oldValues = $estimate->toArray();

            // ── 1. استخرج الـ items وأزلها من بيانات الـ estimate
            $itemsData = $data['items'] ?? null;
            unset($data['items']);

            // ── 2. حدّث بيانات الـ estimate الأساسية
            $estimate->update($data);

            // ── 3. sync جدول estimate_items
            if ($itemsData !== null) {

                // IDs المواد القادمة من الـ Frontend
                $incomingRequestItemIds = collect($itemsData)
                    ->pluck('request_item_id')
                    ->filter()
                    ->toArray();

                // احذف المواد التي أُزيلت من القائمة
                $estimate->estimateItems()
                    ->whereNotIn('request_item_id', $incomingRequestItemIds)
                    ->delete();

                // حدّث أو أضف كل مادة
                foreach ($itemsData as $itemData) {
                    $quantity  = $itemData['quantity'] ?? 1;
                    $unitPrice = $itemData['unit_price'] ?? 0;

                    $estimate->estimateItems()->updateOrCreate(
                        [
                            'request_item_id' => $itemData['request_item_id'],
                        ],
                        [
                            'item_name'   => $itemData['item_name'] ?? null,
                            'quantity'    => $quantity,
                            'unit_price'  => $unitPrice,
                            'total_price' => $quantity * $unitPrice,
                            'notes'       => $itemData['notes'] ?? null,
                        ]
                    );
                }

                // أعد حساب المجموع الكلي
                $estimate->update([
                    'total_amount' => $estimate->estimateItems()->sum('total_price'),
                ]);
            }

            $this->logService->log(
                action: 'update_estimate',
                actionLabel: 'تحديث عرض سعر',
                subjectType: Estimate::class,
                subjectId: $estimate->id,
                oldValues: $oldValues,
                newValues: $estimate->fresh()->toArray(),
                module: 'عروض الأسعار'
            );

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
        return DB::transaction(function () use ($estimate) {

            $oldValues = $estimate->toArray();
            $deleted = $estimate->delete();

            $this->logService->log(
                action: 'delete_estimate',
                actionLabel: 'حذف عرض سعر',
                subjectType: Estimate::class,
                subjectId: $estimate->id,
                oldValues: $oldValues,
                status: $deleted ? 'success' : 'failed',
                module: 'عروض الأسعار'
            );

            return $deleted;
        });
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

        $this->logService->log(
            action: 'view_estimates',
            actionLabel: 'عرض جميع عروض الأسعار',
            subjectType: Estimate::class,
            metadata: ['filters' => $filters],
            module: 'عروض الأسعار'
        );

        return $q->latest('id');
    }

    /**
     * جلب عرض سعر محدد
     */
    public function getById(Estimate $estimate): Estimate
    {
        $estimate = $estimate->load([
            'vendor',
            'purchaseRequest',
            'requestItem',
            'estimateItems.requestItem',
            'creator',
        ]);

        $this->logService->log(
            action: 'view_estimate',
            actionLabel: 'عرض عرض سعر',
            subjectType: Estimate::class,
            subjectId: $estimate->id,
            module: 'عروض الأسعار'
        );

        return $estimate;
    }

    /**
     * جلب عرض سعر مرتبط بمادة معينة
     */
    public function getByItem(int $requestItemId): ?Estimate
    {
        $estimate = Estimate::with([
            'vendor',
            'purchaseRequest',
            'estimateItems.requestItem',
        ])
            ->where('request_item_id', $requestItemId)
            ->latest()
            ->first();

        $this->logService->log(
            action: 'view_estimate_by_item',
            actionLabel: 'عرض عرض سعر حسب المادة',
            subjectType: Estimate::class,
            subjectId: $estimate?->id,
            metadata: ['request_item_id' => $requestItemId],
            module: 'عروض الأسعار'
        );

        return $estimate;
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

            $this->logService->log(
                action: 'create_estimate_for_item',
                actionLabel: 'إنشاء عرض سعر لمادة',
                subjectType: Estimate::class,
                subjectId: $estimate->id,
                newValues: $estimate->toArray(),
                metadata: ['request_item_id' => $requestItemId],
                module: 'عروض الأسعار'
            );

            return $estimate->load([
                'vendor',
                'requestItem',
                'purchaseRequest',
                'creator',
            ]);
        });
    }
}