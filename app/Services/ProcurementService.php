<?php

namespace App\Services;

use App\Models\Procurement;
use App\Models\PurchaseRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProcurementService
{
    /**
     * جلب جميع عمليات الشراء
     */
    public function getAll(): Collection
    {
        return Procurement::with([
            'purchaseRequest',
            'items.unit',
            'items.estimate',
            'items.estimateItem',
        ])
        ->latest('id')
        ->get();
    }

    /**
     * جلب عملية شراء محددة
     */
    public function getById(Procurement $procurement): Procurement
    {
        return $procurement->load([
            'purchaseRequest',
            'items.unit',
            'items.estimate',
            'items.estimateItem',
        ]);
    }

    /**
     * إنشاء عملية شراء جديدة
     */
    public function create(array $data): Procurement
    {
        return DB::transaction(function () use ($data) {

            $data['reference_no'] ??= 'PO-' . now()->format('Y') . '-' . strtoupper(uniqid());
            $data['created_by']   = auth()->id();

            $procurement = Procurement::create([
                'purchase_request_id' => $data['purchase_request_id'],
                'reference_no'        => $data['reference_no'],
                'purchase_date'       => $data['purchase_date'] ?? null,
                'status'              => $data['status'] ?? 'in_progress',
                'notes'               => $data['notes'] ?? null,
                'created_by'          => $data['created_by'],
            ]);

            foreach ($data['items'] as $item) {
                $procurement->items()->create([
                    'estimate_id'      => $item['estimate_id'],
                    'estimate_item_id' => $item['estimate_item_id'],
                    'item_name'        => $item['item_name'],
                    'unit_id'          => $item['unit_id'] ?? null,
                    'quantity'         => $item['quantity'],
                    'unit_price'       => $item['unit_price'] ?? 0,
                    'purchase_price'   => $item['purchase_price'],
                    'estimate_price'   => $item['estimate_price'],
                    'notes'            => $item['notes'] ?? null,
                ]);
            }

            // حساب المبلغ الإجمالي
            $procurement->update([
                'total_amount' => $procurement->items()->sum('total_price'),
            ]);

            return $procurement->load([
                'purchaseRequest',
                'items.unit',
                'items.estimate',
                'items.estimateItem',
            ]);
        });
    }

    /**
     * تحديث عملية شراء
     */
    public function update(Procurement $procurement, array $data): Procurement
    {
        return DB::transaction(function () use ($procurement, $data) {

            $procurement->update([
                'purchase_request_id' => $data['purchase_request_id'] ?? $procurement->purchase_request_id,
                'reference_no'        => $data['reference_no'] ?? $procurement->reference_no,
                'purchase_date'       => $data['purchase_date'] ?? $procurement->purchase_date,
                'status'              => $data['status'] ?? $procurement->status,
                'notes'               => $data['notes'] ?? $procurement->notes,
            ]);

            if (isset($data['items']) && is_array($data['items'])) {
                $procurement->items()->delete();

                foreach ($data['items'] as $item) {
                    $procurement->items()->create([
                        'estimate_id'      => $item['estimate_id'],
                        'estimate_item_id' => $item['estimate_item_id'],
                        'item_name'        => $item['item_name'],
                        'unit_id'          => $item['unit_id'] ?? null,
                        'quantity'         => $item['quantity'],
                        'unit_price'       => $item['unit_price'] ?? 0,
                        'purchase_price'   => $item['purchase_price'],
                        'estimate_price'   => $item['estimate_price'],
                        'notes'            => $item['notes'] ?? null,
                    ]);
                }

                $procurement->update([
                    'total_amount' => $procurement->items()->sum('total_price'),
                ]);
            }

            return $procurement->load([
                'purchaseRequest',
                'items.unit',
                'items.estimate',
                'items.estimateItem',
            ]);
        });
    }

    /**
     * حذف عملية شراء
     */
    public function delete(Procurement $procurement): bool
    {
        return DB::transaction(function () use ($procurement) {
            $procurement->items()->delete();
            return $procurement->delete();
        });
    }

    /**
     * تأكيد اكتمال العملية
     */
    public function markAsCompleted(Procurement $procurement): Procurement
    {
        if ($procurement->status === 'completed') {
            throw ValidationException::withMessages([
                'status' => 'هذه العملية مكتملة مسبقاً',
            ]);
        }

        return DB::transaction(function () use ($procurement) {
            $procurement->update(['status' => 'completed']);

            $procurement->purchaseRequest?->update(['status_type' => 'completed']);

            return $procurement->load([
                'purchaseRequest',
                'items.unit',
                'items.estimate',
                'items.estimateItem',
            ]);
        });
    }
}