<?php

namespace App\Services;

use App\Models\EstimateItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogService;

class EstimateItemService
{

    protected ActivityLogService $logService;

    public function __construct() {
        $this->logService = new ActivityLogService();
    }

    /**
     * جلب جميع عناصر التقديرات مع علاقاتها
     */
    public function getAll(array $filters = []): Collection
    {
        $q = EstimateItem::with(['estimate', 'requestItem']);

        if (!empty($filters['estimate_id'])) {
            $q->where('estimate_id', $filters['estimate_id']);
        }

        if (!empty($filters['request_item_id'])) {
            $q->where('request_item_id', $filters['request_item_id']);
        }

        $result = $q->latest('id')->get();

        $this->logService->log(
            action: 'view_estimate_items',
            actionLabel: 'عرض جميع عناصر عروض الأسعار',
            subjectType: EstimateItem::class,
            metadata: [
                'filters' => $filters,
                'result_count' => count($result)
            ],
            module: 'عناصر عروض الأسعار'
        );

        return $result;
    }

    /**
     * جلب عنصر محدد مع العلاقات
     */
    public function getById(EstimateItem $item): EstimateItem
    {
        $item = $item->load(['estimate', 'requestItem']);

        $this->logService->log(
            action: 'view_estimate_item',
            actionLabel: 'عرض عنصر عرض سعر',
            subjectType: EstimateItem::class,
            subjectId: $item->id,
            module: 'عناصر عروض الأسعار'
        );

        return $item;
    }

    /**
     * إنشاء عنصر عرض سعر جديد
     */
    public function create(array $data): EstimateItem
    {
        return DB::transaction(function () use ($data) {
            $data['total_price'] = ($data['quantity'] ?? 1) * ($data['unit_price'] ?? 0);

            $item = EstimateItem::create($data);

            $this->logService->log(
                action: 'create_estimate_item',
                actionLabel: 'إنشاء عنصر عرض سعر',
                subjectType: EstimateItem::class,
                subjectId: $item->id,
                newValues: $item->toArray(),
                module: 'عناصر عروض الأسعار'
            );

            return $item->load(['estimate', 'requestItem']);
        });
    }

    /**
     * تحديث عنصر عرض سعر
     */
    public function update(EstimateItem $item, array $data): EstimateItem
    {
        return DB::transaction(function () use ($item, $data) {

            $oldValues = $item->toArray();

            if (isset($data['quantity']) || isset($data['unit_price'])) {
                $data['total_price'] = ($data['quantity'] ?? $item->quantity) * ($data['unit_price'] ?? $item->unit_price);
            }

            $item->update($data);

            $newValues = $item->toArray();
            $changedFields = array_keys(array_diff_assoc($newValues, $oldValues));

            $this->logService->log(
                action: 'update_estimate_item',
                actionLabel: 'تحديث عنصر عرض سعر',
                subjectType: EstimateItem::class,
                subjectId: $item->id,
                oldValues: $oldValues,
                newValues: $newValues,
                changedFields: $changedFields,
                module: 'عناصر عروض الأسعار'
            );

            return $item->load(['estimate', 'requestItem']);
        });
    }

    /**
     * حذف عنصر عرض سعر
     */
    public function delete(EstimateItem $item): bool
    {
        return DB::transaction(function () use ($item) {

            $oldValues = $item->toArray();
            $deleted = $item->delete();

            $this->logService->log(
                action: 'delete_estimate_item',
                actionLabel: 'حذف عنصر عرض سعر',
                subjectType: EstimateItem::class,
                subjectId: $item->id,
                oldValues: $oldValues,
                status: $deleted ? 'success' : 'failed',
                module: 'عناصر عروض الأسعار'
            );

            return $deleted;
        });
    }
}