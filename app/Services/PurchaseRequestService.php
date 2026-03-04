<?php

namespace App\Services;

use App\Models\PurchaseRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\RequestItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
            'items.unit',
            'items.estimateItems.estimate',
            'estimates',
            'procurements',
            'warehouseChecks',
            'needsAssessments',
            'report',
            'statusActor',
            'images',
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
            'images'
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

            $images = $data['images'] ?? [];
            unset($data['images']);

            $purchaseRequest = PurchaseRequest::create($data);

            // إنشاء العناصر التابعة في حال وُجدت
            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $purchaseRequest->items()->create([
                        'item_name' => $item['item_name'] ?? null,
                        'quantity'  => $item['quantity'] ?? 1,
                        'unit_id'      => $item['unit_id'] ?? null,
                        'specs'     => $item['specs'] ?? null,
                    ]);
                }
            }

            if (!empty($images) && is_array($images)) {
                foreach($images as $image) {
                    $fileName = \Str::uuid() . '.' . $image->getClientOriginalExtension();

                    $path = $image->storeAs(
                        "purchase_requests/{$purchaseRequest->id}",
                        $fileName,
                        'public'
                    );

                    $purchaseRequest->images()->create([
                        'file_name' => $fileName,
                        'file_path' => $path,
                        'file_type' => $image->getClientMimeType(),
                        'file_size' => $image->getSize(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            return $purchaseRequest->load(['department', 'creator', 'items', 'images']);
        });
    }

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

            if (isset($data['items']) && is_array($data['items'])) {

                $existingItems = $purchaseRequest->items()
                    ->with(['needsAssessment', 'warehouseCheck'])
                    ->get();

                $existingItemIds = $existingItems->pluck('id')->toArray();

                $sentItems    = collect($data['items']);
                $sentItemIds  = $sentItems->pluck('id')->filter()->toArray(); // فقط اللي فيها id

                $protectedItemIds = $existingItems->filter(function ($item) {
                    return $item->needsAssessment()->exists() || $item->warehouseCheck()->exists();
                })->pluck('id')->toArray();

                // المواد التي "اختفت" من الطلب (يعني يريد يحذفها)
                $missingItemIds = array_diff($existingItemIds, $sentItemIds);

                $protectedMissing = array_intersect($missingItemIds, $protectedItemIds);
                if (!empty($protectedMissing)) {
                    throw ValidationException::withMessages([
                        'items' => 'لا يمكن حذف مواد لديها تقييم حاجة أو فحص مستودع.'
                    ]);
                }

                $idsToDelete = array_diff($missingItemIds, $protectedItemIds);
                if (!empty($idsToDelete)) {
                    RequestItem::whereIn('id', $idsToDelete)->delete();
                }

                foreach ($data['items'] as $itemData) {

                    if (!empty($itemData['id'])) {
                        /** @var RequestItem|null $itemModel */
                        $itemModel = $existingItems->firstWhere('id', $itemData['id']);

                        if (!$itemModel) {
                            continue;
                        }

                        if ($itemModel->needsAssessment()->exists() || $itemModel->warehouseCheck()->exists()) {
                            throw ValidationException::withMessages([
                                'items' => 'لا يمكن تعديل مادة لديها تقييم حاجة أو فحص مستودع.'
                            ]);
                        }

                        // مسموح تعديلها
                        $itemModel->update([
                            'item_name'             => $itemData['item_name'] ?? $itemModel->item_name,
                            'quantity'              => $itemData['quantity']  ?? $itemModel->quantity,
                            'unit_id'              => $itemData['unit']      ?? $itemModel->unit_id,
                            'specifications'        => $itemData['specifications'] ?? $itemModel->specifications,
                            'estimated_unit_price'  => $itemData['estimated_unit_price'] ?? $itemModel->estimated_unit_price,
                        ]);

                    } else {
                        // ===== حالة: مادة جديدة (بدون id) =====
                        $purchaseRequest->items()->create([
                            'item_name'             => $itemData['item_name'],
                            'quantity'              => $itemData['quantity']  ?? 1,
                            'unit_id'               => $itemData['unit'] ?? null,
                            'specifications'        => $itemData['specifications'] ?? null,
                            'estimated_unit_price'  => $itemData['estimated_unit_price'] ?? null,
                        ]);
                    }
                }

                if (!empty($data['images']) && is_array($data['images'])) {

                    foreach ($data['images'] as $image) {

                        $fileName = \Str::uuid() . '.' . $image->getClientOriginalExtension();

                        $path = $image->storeAs(
                            "purchase_requests/{$purchaseRequest->id}",
                            $fileName,
                            'public'
                        );

                        $purchaseRequest->images()->create([
                            'file_name'   => $fileName,
                            'file_path'   => $path,
                            'file_type'   => $image->getClientMimeType(),
                            'file_size'   => $image->getSize(),
                            'uploaded_by' => auth()->id(),
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

            return $purchaseRequest->load(['department', 'creator', 'items', 'images']);
        });
    }

    /**
     * حذف طلب شراء
     */
    public function delete(PurchaseRequest $purchaseRequest): bool
    {
        return DB::transaction(function () use ($purchaseRequest) {

            foreach ($purchaseRequest->images as $image) {
                \Storage::disk('public')->delete($image->file_path);
            }

            $purchaseRequest->images()->delete();
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