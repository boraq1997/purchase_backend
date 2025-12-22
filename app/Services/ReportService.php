<?php

namespace App\Services;

use App\Models\Report;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * جلب جميع التقارير مع علاقاتها
     */
    public function getAll(array $filters = []): Collection
    {
        $q = Report::with(['purchaseRequest', 'generatedBy']);

        if (!empty($filters['purchase_request_id'])) {
            $q->where('purchase_request_id', $filters['purchase_request_id']);
        }

        if (!empty($filters['report_type'])) {
            $q->where('report_type', $filters['report_type']);
        }

        return $q->latest('id')->get();
    }

    /**
     * جلب تقرير محدد مع علاقاته
     */
    public function getById(Report $report): Report
    {
        return $report->load(['purchaseRequest', 'generatedBy']);
    }

    /**
     * إنشاء تقرير جديد
     */
    public function create(array $data): Report
    {
        return DB::transaction(function () use ($data) {
            $data['generated_at'] ??= now();

            $report = Report::create($data);

            return $report->load(['purchaseRequest', 'generatedBy']);
        });
    }

    /**
     * تحديث تقرير
     */
    public function update(Report $report, array $data): Report
    {
        return DB::transaction(function () use ($report, $data) {
            $report->update($data);
            return $report->load(['purchaseRequest', 'generatedBy']);
        });
    }

    /**
     * حذف تقرير
     */
    public function delete(Report $report): bool
    {
        return DB::transaction(fn() => $report->delete());
    }
}