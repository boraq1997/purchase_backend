<?php

namespace App\Services;

use App\Models\Procurement;
use App\Models\Estimate;
use App\Models\PurchaseRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProcurementService
{
    /**
     * جلب جميع عمليات الشراء مع العلاقات الضرورية
     */
    public function getAll(): Collection
    {
        return Procurement::with([
            'purchaseRequest',
            'estimate',
            'items',
        ])
        ->latest('id')
        ->get();
    }

    /**
     * جلب عملية شراء محددة مع العلاقات
     */
    public function getById(Procurement $procurement): Procurement
    {
        return $procurement->load([
            'purchaseRequest',
            'estimate',
            'items',
        ]);
    }

    /**
     * إنشاء عملية شراء جديدة بناءً على عرض سعر محدد
     */
    public function create(array $data): Procurement
    {
        return DB::transaction(function () use ($data) {
            /** التحقق من وجود التقدير (estimate) **/
            $estimate = Estimate::findOrFail($data['estimate_id']);

            /** الحصول على الطلب المرتبط بالتقدير **/
            $purchaseRequest = PurchaseRequest::findOrFail($estimate->purchase_request_id);

            /** توليد رقم العملية في حال لم يُرسل **/
            $data['procurement_number'] ??= 'PO-' . now()->format('Y') . '-' . strtoupper(uniqid());

            /** إنشاء العملية **/
            $procurement = Procurement::create(array_merge($data, [
                'purchase_request_id' => $purchaseRequest->id,
            ]));

            /** عند الإنشاء، إذا أضيفت عناصر شراء **/
            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $procurement->items()->create([
                        'name'        => $item['name'] ?? null,
                        'quantity'    => $item['quantity'] ?? 1,
                        'unit_price'  => $item['unit_price'] ?? 0,
                        'total_price' => ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0),
                        'notes'       => $item['notes'] ?? null,
                    ]);
                }
            }

            /** تحديث حالة الطلب إلى procured **/
            $purchaseRequest->update(['status_type' => 'procured']);

            /** تحديث حالة التقدير إذا لزم **/
            $estimate->update(['status' => 'accepted']);

            return $procurement->load(['purchaseRequest', 'estimate', 'items']);
        });
    }

    /**
     * تحديث بيانات عملية الشراء
     */
    public function update(Procurement $procurement, array $data): Procurement
    {
        return DB::transaction(function () use ($procurement, $data) {
            $procurement->update($data);

            /** تحديث أو استبدال عناصر الشراء (إن أُرسلت) **/
            if (isset($data['items']) && is_array($data['items'])) {
                $procurement->items()->delete(); // إعادة إنشاء العناصر
                foreach ($data['items'] as $item) {
                    $procurement->items()->create([
                        'name'        => $item['name'] ?? null,
                        'quantity'    => $item['quantity'] ?? 1,
                        'unit_price'  => $item['unit_price'] ?? 0,
                        'total_price' => ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0),
                        'notes'       => $item['notes'] ?? null,
                    ]);
                }
            }

            return $procurement->load(['purchaseRequest', 'estimate', 'items']);
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
     * تأكيد استلام المواد وتحديث حالة الطلب
     */
    public function markAsReceived(Procurement $procurement): Procurement
    {
        if ($procurement->status === 'received') {
            throw ValidationException::withMessages(['status' => 'تم استلام هذه العملية مسبقًا.']);
        }

        return DB::transaction(function () use ($procurement) {
            $procurement->update(['status' => 'received']);

            $procurement->purchaseRequest?->update(['status_type' => 'completed']);

            return $procurement->load(['purchaseRequest', 'estimate', 'items']);
        });
    }
}