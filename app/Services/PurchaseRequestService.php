<?php

namespace App\Services;

use App\Models\PurchaseRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\RequestItem;

class PurchaseRequestService
{
    /**
     * جلب جميع طلبات الشراء مع العلاقات الرئيسية
     */
    public function getAll(array $filters = []): Collection
    {
        $q = PurchaseRequest::with([
            'department',
            'committee',
            'creator',
            'items.estimateItems.estimate',
            'estimates',
            'procurements',
            'warehouseChecks',
            'needsAssessments',
            'report',
            'statusActor',
        ]);

        if (!empty($filters['search'])) {
            $term = '%' . $filters['search'] . '%';
            $q->where(function($qq) use ($term) {
                $qq->where('request_number', 'like', $term)
                    ->orWhere('title', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        if (!empty($filters['department_id'])) {
            $q->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['status_type'])) {
            $q->where('status_type', $filters['status_type']);
        }

        if (!empty($filters['priority'])) {
            $q->where('priority', $filters['priority']);
        }

        if (!empty($filters['date_from'])) {
            $q->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $q->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $q->latest('id')->get();
    }

    /**
     * جلب طلب شراء محدد
     */
    public function getById(PurchaseRequest $purchaseRequest): PurchaseRequest
    {
        return $purchaseRequest->load([
            'department',
            'committee',
            'creator',
            'items.estimateItems',
            'items.estimateItems.estimate',
            'estimates',
            'procurements',
            'warehouseChecks',
            'needsAssessments',
            'report',
            'statusActor',
        ]);
    }

    /**
     * إنشاء طلب شراء جديد
     */
    public function create(array $data): PurchaseRequest
    {
        return DB::transaction(function () use ($data) {
            $data['request_number'] ??= 'PR-' . now()->format('Y') . '-' . strtoupper(uniqid());
            $data['status_type'] ??= 'draft';
            $data['priority'] ??= 'medium';

            $purchaseRequest = PurchaseRequest::create($data);

            // إنشاء العناصر التابعة في حال وُجدت
            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $purchaseRequest->items()->create([
                        'item_name' => $item['item_name'] ?? null,
                        'quantity'  => $item['quantity'] ?? 1,
                        'unit'      => $item['unit'] ?? null,
                        'specs'     => $item['specs'] ?? null,
                    ]);
                }
            }

            return $purchaseRequest->load(['department', 'creator', 'items']);
        });
    }

    /**
     * تحديث طلب شراء موجود
     */

public function update(PurchaseRequest $purchaseRequest, array $data): PurchaseRequest
{
    return DB::transaction(function () use ($purchaseRequest, $data) {

        // ===== تحديث بيانات الطلب الرئيسية =====
        $purchaseRequest->update([
            'title'         => $data['title']         ?? $purchaseRequest->title,
            'description'   => $data['description']   ?? $purchaseRequest->description,
            'priority'      => $data['priority']      ?? $purchaseRequest->priority,
            'department_id' => $data['department_id'] ?? $purchaseRequest->department_id,
        ]);

        // ===== التعامل مع المواد =====
        if (isset($data['items']) && is_array($data['items'])) {

            // كل المواد الموجودة في قاعدة البيانات مع العلاقات
            $existingItems = $purchaseRequest->items()
                ->with(['needsAssessment', 'warehouseCheck'])
                ->get();

            $existingItemIds = $existingItems->pluck('id')->toArray();

            // الـ IDs القادمة من الطلب (المواد التي بقيت أو تم تعديلها)
            $sentItems    = collect($data['items']);
            $sentItemIds  = $sentItems->pluck('id')->filter()->toArray(); // فقط اللي فيها id

            // المواد المحمية (اللي عليها تقييم أو فحص مستودع)
            $protectedItemIds = $existingItems->filter(function ($item) {
                return $item->needsAssessment()->exists() || $item->warehouseCheck()->exists();
            })->pluck('id')->toArray();

            // المواد التي "اختفت" من الطلب (يعني يريد يحذفها)
            $missingItemIds = array_diff($existingItemIds, $sentItemIds);

            // لو حاول يحذف مادة عليها تقييم/فحص → نمنع العملية ونرجع خطأ
            $protectedMissing = array_intersect($missingItemIds, $protectedItemIds);
            if (!empty($protectedMissing)) {
                throw ValidationException::withMessages([
                    'items' => 'لا يمكن حذف مواد لديها تقييم حاجة أو فحص مستودع.'
                ]);
            }

            // المواد التي يُسمح بحذفها فعلاً (لا تقييم ولا فحص)
            $idsToDelete = array_diff($missingItemIds, $protectedItemIds);
            if (!empty($idsToDelete)) {
                RequestItem::whereIn('id', $idsToDelete)->delete();
            }

            // تحديث/إنشاء المواد
            foreach ($data['items'] as $itemData) {

                // ===== حالة: مادة موجودة (لديها id) =====
                if (!empty($itemData['id'])) {
                    /** @var RequestItem|null $itemModel */
                    $itemModel = $existingItems->firstWhere('id', $itemData['id']);

                    if (!$itemModel) {
                        continue; // احتياطاً
                    }

                    // المادة عليها تقييم/فحص → ممنوع تعديلها
                    if ($itemModel->needsAssessment()->exists() || $itemModel->warehouseCheck()->exists()) {
                        // خيار 1: تجاهل تعديلها بصمت
                        // continue;

                        // خيار 2: إرجاع خطأ للمستخدم (أوضح للفرونت إند)
                        throw ValidationException::withMessages([
                            'items' => 'لا يمكن تعديل مادة لديها تقييم حاجة أو فحص مستودع.'
                        ]);
                    }

                    // مسموح تعديلها
                    $itemModel->update([
                        'item_name'             => $itemData['item_name'] ?? $itemModel->item_name,
                        'quantity'              => $itemData['quantity']  ?? $itemModel->quantity,
                        'unit'                  => $itemData['unit']      ?? $itemModel->unit,
                        'specifications'        => $itemData['specifications'] ?? $itemModel->specifications,
                        'estimated_unit_price'  => $itemData['estimated_unit_price'] ?? $itemModel->estimated_unit_price,
                    ]);

                } else {
                    // ===== حالة: مادة جديدة (بدون id) =====
                    $purchaseRequest->items()->create([
                        'item_name'             => $itemData['item_name'],
                        'quantity'              => $itemData['quantity']  ?? 1,
                        'unit'                  => $itemData['unit']      ?? null,
                        'specifications'        => $itemData['specifications'] ?? null,
                        'estimated_unit_price'  => $itemData['estimated_unit_price'] ?? null,
                    ]);
                }
            }
        }

        // تحديث الحالة إن وُجدت تقديرات والطلب مازال draft
        if (
            $purchaseRequest->estimates()->exists() &&
            $purchaseRequest->status_type === 'draft'
        ) {
            $purchaseRequest->update(['status_type' => 'pending']);
        }

        return $purchaseRequest->load(['department', 'creator', 'items']);
    });
}

    /**
     * حذف طلب شراء
     */
    public function delete(PurchaseRequest $purchaseRequest): bool
    {
        return DB::transaction(function () use ($purchaseRequest) {
            $purchaseRequest->items()->delete();
            return $purchaseRequest->delete();
        });
    }

    /**
     * تغيير حالة الطلب (draft → pending → review → approved → procured → completed)
     */
    public function changeStatus(
        PurchaseRequest $purchaseRequest,
        string $status,
        ?string $note = null
    ): PurchaseRequest
    {
        $allowed = ['draft', 'pending', 'approved', 'rejected', 'completed'];

        if (!in_array($status, $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => 'حالة غير صالحة.'
            ]);
        }

        return DB::transaction(function () use ($purchaseRequest, $status, $note) {

            $purchaseRequest->status_type = $status;
            $purchaseRequest->status_action_by = auth()->id();
            $purchaseRequest->status_date = now();

            // الدور الحالي (من المستخدم)
            $purchaseRequest->status_role = auth()->user()?->hasRole('alameen')
                ? 'alameen'
                : 'alameenAssestant';

            // في حالة الرفض
            if ($status === 'rejected') {
                if (!$note) {
                    throw ValidationException::withMessages([
                        'note' => 'سبب الرفض مطلوب'
                    ]);
                }

                $purchaseRequest->rejected_reason = $note;
            }

            // في حالة الإغلاق
            if ($status === 'completed') {
                $purchaseRequest->closed_at = now();
            }

            // تنظيف سبب الرفض إذا لم يكن رفض
            if ($status !== 'rejected') {
                $purchaseRequest->rejected_reason = null;
            }

            $purchaseRequest->save();

            return $purchaseRequest->load([
                'department',
                'creator',
                'committee',
            ]);
        });
    }
}