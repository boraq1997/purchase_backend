<?php

namespace App\Services;

use App\Models\RequestItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RequestItemService
{
    /**
     * إنشاء مادة جديدة داخل الطلب
     */
    public function create(array $data): RequestItem
    {
        return DB::transaction(function () use ($data) {
            $item = RequestItem::create($data);
            return $item->load('purchaseRequest');
        });
    }

    /**
     * تحديث مادة موجودة
     */
    public function update(RequestItem $item, array $data): RequestItem
    {
        return DB::transaction(function () use ($item, $data) {
            $item->update($data);
            return $item->load('purchaseRequest');
        });
    }

    /**
     * حذف مادة من الطلب
     */
    public function delete(RequestItem $item): bool
    {
        return DB::transaction(fn() => $item->delete());
    }

    /**
     * جلب جميع المواد مع الطلبات التابعة لها
     */
    public function getAll(): Collection
    {
        return RequestItem::with('purchaseRequest')
            ->latest('id')
            ->get();
    }

    /**
     * جلب مادة محددة مع الطلب التابع لها
     */
    public function getById(RequestItem $item): RequestItem
    {
        return $item->load('purchaseRequest');
    }
}