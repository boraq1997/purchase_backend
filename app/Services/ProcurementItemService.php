<?php

namespace App\Services;

use App\Models\ProcurementItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProcurementItemService
{
    /**
     * جلب جميع عناصر الشراء مع عملية الشراء التابعة لها
     */
    public function getAll(array $filters = []): Collection
    {
        $q = ProcurementItem::with('procurement', 'unit');

        if (!empty($filters['procurement_id'])) {
            $q->where('procurement_id', $filters['procurement_id']);
        }

        if (!empty($filters['search'])) {
            $term = '%' . $filters['search'] . '%';
            $q->where('name', 'like', $term);
        }

        return $q->latest('id')->get();
    }

    /**
     * جلب عنصر شراء محدد مع علاقته
     */
    public function getById(ProcurementItem $item): ProcurementItem
    {
        return $item->load('procurement'. 'unit ');
    }

    /**
     * إنشاء عنصر شراء جديد
     */
    public function create(array $data): ProcurementItem
    {
        return DB::transaction(function () use ($data) {
            $data['total_price'] = ($data['quantity'] ?? 1) * ($data['unit_price'] ?? 0);

            $item = ProcurementItem::create($data);

            return $item->load('procurement');
        });
    }

    /**
     * تحديث عنصر شراء
     */
    public function update(ProcurementItem $item, array $data): ProcurementItem
    {
        return DB::transaction(function () use ($item, $data) {
            if (isset($data['quantity']) || isset($data['unit_price'])) {
                $data['total_price'] = ($data['quantity'] ?? $item->quantity) * ($data['unit_price'] ?? $item->unit_price);
            }

            $item->update($data);

            return $item->load('procurement');
        });
    }

    /**
     * حذف عنصر شراء
     */
    public function delete(ProcurementItem $item): bool
    {
        return DB::transaction(fn() => $item->delete());
    }
}