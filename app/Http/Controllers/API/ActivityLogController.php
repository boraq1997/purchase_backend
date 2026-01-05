<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Logs\ActivityLogFilterRequest;
use App\Http\Resources\ActivityLogResource;
use App\Services\ActivityLogService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;


class ActivityLogController extends Controller
{
    protected ActivityLogService $logService;

    public function __construct(ActivityLogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Display a paginated list of activity logs.
     *
     * @param ActivityLogFilterRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ActivityLogFilterRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $perPage = $filters['per_page'] ?? 50;

        $logs = $this->logService->getActivityLogs($filters, $perPage);

        return ActivityLogResource::collection($logs);
    }
}