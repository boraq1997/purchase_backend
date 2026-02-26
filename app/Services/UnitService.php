<?php

namespace App\Services;

use App\Models\Unit;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\DB;

class UnitService
{
    protected ActivityLogService $activityLog;

    public function __construct(ActivityLogService $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    /*
    |--------------------------------------------------------------------------
    | عرض الوحدات
    |--------------------------------------------------------------------------
    */

    public function index(array $filters = [], int $perPage = 15)
    {
        $query = Unit::query();

        // فلتر بحث بالاسم أو الكود
        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | إنشاء وحدة جديدة
    |--------------------------------------------------------------------------
    */

    public function store(array $data): Unit
    {
        return DB::transaction(function () use ($data) {

            if (!empty($data['code'])) {
                $data['code'] = strtoupper($data['code']);
            }

            $unit = Unit::create($data);

            // $this->activityLog->log(
            //     description: 'تم إنشاء وحدة جديدة',
            //     subject: $unit,
            //     properties: [
            //         'new' => $unit->toArray()
            //     ]
            // );

            return $unit;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | تحديث وحدة
    |--------------------------------------------------------------------------
    */

    public function update(Unit $unit, array $data): Unit
    {
        return DB::transaction(function () use ($unit, $data) {

            $oldData = $unit->getOriginal();

            if (!empty($data['code'])) {
                $data['code'] = strtoupper($data['code']);
            }

            $unit->update($data);

            // $this->activityLog->log(
            //     description: 'تم تحديث وحدة',
            //     subject: $unit,
            //     properties: [
            //         'old' => $oldData,
            //         'new' => $unit->fresh()->toArray()
            //     ]
            // );

            return $unit;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | حذف وحدة
    |--------------------------------------------------------------------------
    */

    public function delete(Unit $unit): void
    {
        DB::transaction(function () use ($unit) {

            $data = $unit->toArray();

            $unit->delete();

            // $this->activityLog->log(
            //     description: 'تم حذف وحدة',
            //     properties: [
            //         'deleted' => $data
            //     ]
            // );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | عرض وحدة واحدة
    |--------------------------------------------------------------------------
    */

    public function getById(int $id): Unit
    {
        return Unit::findOrFail($id);
    }
}