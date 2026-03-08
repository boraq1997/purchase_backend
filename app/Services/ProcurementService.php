<?php

namespace App\Services;

use App\Models\Procurement;
use App\Models\Estimate;
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

            $procurement = Procurement::create([
                'purchase_request_id' => $data['purchase_request_id'],
                'reference_no'        => $data['reference_no'],
                'purchase_date'       => $data['purchase_date'] ?? null,
                'status'              => $data['status'] ?? 'in_progress',
                'notes'               => $data['notes'] ?? null,
                'created_by'          => auth()->id(),
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

            // تحديث حالة عروض الأسعار
            $selectedEstimateIds = collect($data['items'])
                ->pluck('estimate_id')
                ->unique()
                ->toArray();

            Estimate::whereIn('id', $selectedEstimateIds)
                ->update(['status' => 'accepted']);

            Estimate::where('purchase_request_id', $data['purchase_request_id'])
                ->whereNotIn('id', $selectedEstimateIds)
                ->update(['status' => 'rejected']);

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

                $oldEstimateIds = $procurement->items()
                    ->pluck('estimate_id')
                    ->unique()
                    ->toArray();

                Estimate::whereIn('id', $oldEstimateIds)
                    ->update(['status' => 'pending']);

                $procurement->items()->delete();

                foreach ($data['items'] as $item) {
                    $procurement->items()->create([
                        'estimate_id'      => $item['estimate_id'] ?? null,
                        'estimate_item_id' => $item['estimate_item_id'] ?? null,
                        'item_name'        => $item['item_name'],
                        'unit_id'          => $item['unit_id'] ?? null,
                        'quantity'         => $item['quantity'],
                        'unit_price'       => $item['unit_price'] ?? 0,
                        'purchase_price'   => $item['purchase_price'] ?? 0,
                        'estimate_price'   => $item['estimate_price'] ?? 0,
                        'notes'            => $item['notes'] ?? null,
                    ]);
                }

                $selectedEstimateIds = collect($data['items'])
                    ->pluck('estimate_id')
                    ->unique()
                    ->toArray();

                Estimate::whereIn('id', $selectedEstimateIds)
                    ->update(['status' => 'accepted']);

                Estimate::where('purchase_request_id', $procurement->purchase_request_id)
                    ->whereNotIn('id', $selectedEstimateIds)
                    ->update(['status' => 'rejected']);

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

            $estimateIds = $procurement->items()
                ->pluck('estimate_id')
                ->unique()
                ->toArray();

            Estimate::whereIn('id', $estimateIds)
                ->update(['status' => 'pending']);

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