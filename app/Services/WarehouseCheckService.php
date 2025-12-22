<?php

namespace App\Services;

use App\Models\WarehouseCheck;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WarehouseCheckService
{
    /**
     * جلب جميع فحوصات المستودع مع العلاقات التابعة لها
     */
    public function getAll(array $filters = []): Collection
    {
        $q = WarehouseCheck::with(['purchaseRequest', 'requestItem', 'checkedBy']);

        if (!empty($filters['purchase_request_id'])) {
            $q->where('purchase_request_id', $filters['purchase_request_id']);
        }

        if (!empty($filters['availability'])) {
            $q->where('availability', $filters['availability']);
        }

        return $q->latest('id')->get();
    }

    /**
     * جلب فحص مستودع محدد
     */
    public function getById(WarehouseCheck $check): WarehouseCheck
    {
        return $check->load(['purchaseRequest', 'requestItem', 'checkedBy']);
    }

    public function getByRequestItem($purchaseRequestId, $requestItemId): ?WarehouseCheck
    {
        return WarehouseCheck::where('purchase_request_id', $purchaseRequestId)
            ->where('request_item_id', $requestItemId)
            ->with(['purchaseRequest', 'requestItem', 'checkedBy'])
            ->first();
    }

    /**
     * إنشاء فحص جديد للمستودع
     */
    public function create(array $data): WarehouseCheck
    {
        return DB::transaction(function () use ($data) {
            $check = WarehouseCheck::create([
                'purchase_request_id' => $data['purchase_request_id'],
                'request_item_id'     => $data['request_item_id'],
                'availability'        => $data['availability'],
                'condition'           => $data['condition'] ?? null,
                'available_quantity'  => $data['available_quantity'] ?? 0,
                'recommendation'      => $data['recommendation'] ?? null,
                'notes'               => $data['notes'] ?? null,
                'checked_by'          => $data['checked_by'],
            ]);

            return $check->load(['purchaseRequest', 'requestItem', 'checkedBy']);
        });
    }

    /**
     * تحديث فحص مستودع موجود
     */
    public function update(WarehouseCheck $check, array $data): WarehouseCheck
    {
        return DB::transaction(function () use ($check, $data) {
            $check->update([
                'availability'       => $data['availability'] ?? $check->availability,
                'condition'          => $data['condition'] ?? $check->condition,
                'available_quantity' => $data['available_quantity'] ?? $check->available_quantity,
                'recommendation'     => $data['recommendation'] ?? $check->recommendation,
                'notes'              => $data['notes'] ?? $check->notes,
                'checked_by'         => $data['checked_by'] ?? $check->checked_by,
            ]);

            return $check->load(['purchaseRequest', 'requestItem', 'checkedBy']);
        });
    }

    /**
     * حذف فحص مستودع
     */
    public function delete(WarehouseCheck $check): bool
    {
        return DB::transaction(fn() => $check->delete());
    }
}