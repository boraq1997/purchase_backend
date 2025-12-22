<?php

namespace App\Services;

use App\Models\EstimateItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EstimateItemService
{
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

        return $q->latest('id')->get();
    }

    /**
     * جلب عنصر محدد مع العلاقات
     */
    public function getById(EstimateItem $item): EstimateItem
    {
        return $item->load(['estimate', 'requestItem']);
    }

    /**
     * إنشاء عنصر عرض سعر جديد
     */
    public function create(array $data): EstimateItem
    {
        return DB::transaction(function () use ($data) {
            $data['total_price'] = ($data['quantity'] ?? 1) * ($data['unit_price'] ?? 0);

            $item = EstimateItem::create($data);

            return $item->load(['estimate', 'requestItem']);
        });
    }

    /**
     * تحديث عنصر عرض سعر
     */
    public function update(EstimateItem $item, array $data): EstimateItem
    {
        return DB::transaction(function () use ($item, $data) {
            if (isset($data['quantity']) || isset($data['unit_price'])) {
                $data['total_price'] = ($data['quantity'] ?? $item->quantity) * ($data['unit_price'] ?? $item->unit_price);
            }

            $item->update($data);

            return $item->load(['estimate', 'requestItem']);
        });
    }

    /**
     * حذف عنصر عرض سعر
     */
    public function delete(EstimateItem $item): bool
    {
        return DB::transaction(fn() => $item->delete());
    }
}