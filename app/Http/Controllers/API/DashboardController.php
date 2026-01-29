<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Committee;
use App\Models\Department;
use App\Models\Estimate;
use App\Models\NeedsAssessment;
use App\Models\Procurement;
use App\Models\PurchaseRequest;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WarehouseCheck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'month');
        
        $dateRange = $this->getDateRange($period);
        $previousDateRange = $this->getPreviousDateRange($period);
        
        $data = [
            // Main Statistics
            'totalRequests' => $this->getTotalRequests($dateRange),
            'requestsGrowth' => $this->getGrowthPercentage(
                $this->getTotalRequests($dateRange),
                $this->getTotalRequests($previousDateRange)
            ),
            'pendingRequests' => $this->getPendingRequests($dateRange),
            'pendingPercentage' => $this->getPendingPercentage($dateRange),
            'totalSpending' => $this->getTotalSpending($dateRange),
            'spendingGrowth' => $this->getGrowthPercentage(
                $this->getTotalSpending($dateRange),
                $this->getTotalSpending($previousDateRange)
            ),
            'activeVendors' => $this->getActiveVendors($dateRange),
            'totalVendors' => Vendor::count(),
            
            // Additional Statistics
            'warehouseChecks' => WarehouseCheck::whereBetween('created_at', $dateRange)->count(),
            'availableItems' => WarehouseCheck::whereBetween('created_at', $dateRange)
                ->where('availability', 'available')
                ->count(),
            'estimates' => Estimate::whereBetween('created_at', $dateRange)->count(),
            'pendingEstimates' => Estimate::whereBetween('created_at', $dateRange)
                ->where('status', 'pending')
                ->count(),
            'procurements' => Procurement::whereBetween('created_at', $dateRange)->count(),
            'completedProcurements' => Procurement::whereBetween('created_at', $dateRange)
                ->where('status', 'completed')
                ->count(),
            'activeCommittees' => Committee::count(),
            'totalCommittees' => Committee::count(),
            
            // Department Performance
            'departmentPerformance' => $this->getDepartmentPerformance($dateRange),
            
            // Recent Activities
            'recentActivities' => $this->getRecentActivities(),
            
            // Top Departments
            'topDepartments' => $this->getTopDepartments($dateRange),
            
            // Top Vendors
            'topVendors' => $this->getTopVendors($dateRange),
            
            // Charts Data
            'requestsChart' => $this->getRequestsChartData($period),
            'priorityChart' => $this->getPriorityChartData($dateRange),
            'spendingChart' => $this->getSpendingChartData($period),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    private function getDateRange($period)
    {
        switch ($period) {
            case 'quarter':
                return [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()];
            case 'year':
                return [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()];
            case 'month':
            default:
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
        }
    }
    
    private function getPreviousDateRange($period)
    {
        switch ($period) {
            case 'quarter':
                return [
                    Carbon::now()->subQuarter()->startOfQuarter(),
                    Carbon::now()->subQuarter()->endOfQuarter()
                ];
            case 'year':
                return [
                    Carbon::now()->subYear()->startOfYear(),
                    Carbon::now()->subYear()->endOfYear()
                ];
            case 'month':
            default:
                return [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ];
        }
    }
    
    private function getTotalRequests($dateRange)
    {
        return PurchaseRequest::whereBetween('created_at', $dateRange)->count();
    }
    
    private function getPendingRequests($dateRange)
    {
        return PurchaseRequest::whereBetween('created_at', $dateRange)
            ->where('status_type', 'pending')
            ->count();
    }
    
    private function getPendingPercentage($dateRange)
    {
        $total = $this->getTotalRequests($dateRange);
        if ($total == 0) return 0;
        
        $pending = $this->getPendingRequests($dateRange);
        return round(($pending / $total) * 100, 1);
    }
    
    private function getTotalSpending($dateRange)
    {
        return Procurement::whereBetween('purchase_date', $dateRange)
            ->where('status', 'completed')
            ->sum('total_amount');
    }
    
    private function getActiveVendors($dateRange)
    {
        return Vendor::whereHas('estimates', function ($query) use ($dateRange) {
            $query->whereBetween('created_at', $dateRange);
        })->count();
    }
    
    private function getGrowthPercentage($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }
    
    private function getDepartmentPerformance($dateRange)
    {
        $departments = Department::select('departments.id', 'departments.name')
            ->withCount([
                'purchaseRequests as requests' => function ($query) use ($dateRange) {
                    $query->whereBetween('created_at', $dateRange);
                },
                'purchaseRequests as completed' => function ($query) use ($dateRange) {
                    $query->whereBetween('created_at', $dateRange)
                        ->where('status_type', 'completed');
                }
            ])
            ->withSum([
                'purchaseRequests as spending' => function ($query) use ($dateRange) {
                    $query->whereBetween('created_at', $dateRange)
                        ->where('status_type', 'completed');
                }
            ], 'total_estimated_cost')
            ->having('requests', '>', 0)
            ->orderByDesc('requests')
            ->limit(10)
            ->get();
        
        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
        
        return $departments->map(function ($dept, $index) use ($colors) {
            $performance = $dept->requests > 0 ? round(($dept->completed / $dept->requests) * 100, 1) : 0;
            
            return [
                'name' => $dept->name,
                'requests' => $dept->requests,
                'completed' => $dept->completed,
                'spending' => $dept->spending ?? 0,
                'performance' => $performance,
                'color' => $colors[$index % count($colors)]
            ];
        });
    }
    
    private function getRecentActivities()
    {
        $activities = ActivityLog::with(['actor'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        
        $icons = [
            'create' => 'pi-plus-circle',
            'update' => 'pi-pencil',
            'delete' => 'pi-trash',
            'approve' => 'pi-check-circle',
            'reject' => 'pi-times-circle',
            'submit' => 'pi-send',
        ];
        
        $colors = [
            'create' => '#10b981',
            'update' => '#3b82f6',
            'delete' => '#ef4444',
            'approve' => '#22c55e',
            'reject' => '#f59e0b',
            'submit' => '#8b5cf6',
        ];
        
        return $activities->map(function ($activity) use ($icons, $colors) {
            return [
                'title' => $activity->action_label ?? $activity->action,
                'description' => $activity->actor_name . ' - ' . $activity->subject_type,
                'time' => $activity->created_at->diffForHumans(),
                'icon' => $icons[$activity->action] ?? 'pi-info-circle',
                'color' => $colors[$activity->action] ?? '#6b7280'
            ];
        });
    }
    
    private function getTopDepartments($dateRange)
    {
        $departments = Department::select('departments.id', 'departments.name')
            ->withCount([
                'purchaseRequests as requests' => function ($query) use ($dateRange) {
                    $query->whereBetween('created_at', $dateRange);
                }
            ])
            ->having('requests', '>', 0)
            ->orderByDesc('requests')
            ->limit(5)
            ->get();
        
        $total = $departments->sum('requests');
        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
        
        return $departments->map(function ($dept, $index) use ($total, $colors) {
            return [
                'name' => $dept->name,
                'requests' => $dept->requests,
                'percentage' => $total > 0 ? round(($dept->requests / $total) * 100, 1) : 0,
                'color' => $colors[$index]
            ];
        });
    }
    
    private function getTopVendors($dateRange)
    {
        $vendors = Vendor::select('vendors.id', 'vendors.name')
            ->join('estimates', 'vendors.id', '=', 'estimates.vendor_id')
            ->whereBetween('estimates.created_at', $dateRange)
            ->where('estimates.status', 'accepted')
            ->groupBy('vendors.id', 'vendors.name')
            ->selectRaw('SUM(estimates.total_amount) as total_value')
            ->selectRaw('COUNT(estimates.id) as estimate_count')
            ->orderByDesc('total_value')
            ->limit(5)
            ->get();
        
        $total = $vendors->sum('total_value');
        $colors = ['#f59e0b', '#3b82f6', '#10b981', '#ef4444', '#8b5cf6'];
        
        return $vendors->map(function ($vendor, $index) use ($total, $colors) {
            // Calculate rating based on acceptance rate and activity
            $rating = min(5, 3 + ($vendor->estimate_count / 10));
            
            return [
                'name' => $vendor->name,
                'value' => $vendor->total_value,
                'percentage' => $total > 0 ? round(($vendor->total_value / $total) * 100, 1) : 0,
                'rating' => round($rating, 1),
                'color' => $colors[$index]
            ];
        });
    }
    
    private function getRequestsChartData($period)
    {
        $months = $this->getMonthsForPeriod($period);
        $labels = [];
        $draft = [];
        $pending = [];
        $approved = [];
        $rejected = [];
        $completed = [];
        
        foreach ($months as $month) {
            $labels[] = $month['label'];
            
            $requests = PurchaseRequest::whereBetween('created_at', [$month['start'], $month['end']])
                ->select('status_type', DB::raw('count(*) as count'))
                ->groupBy('status_type')
                ->pluck('count', 'status_type');
            
            $draft[] = $requests['draft'] ?? 0;
            $pending[] = $requests['pending'] ?? 0;
            $approved[] = $requests['approved'] ?? 0;
            $rejected[] = $requests['rejected'] ?? 0;
            $completed[] = $requests['completed'] ?? 0;
        }
        
        return [
            'labels' => $labels,
            'draft' => $draft,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'completed' => $completed
        ];
    }
    
    private function getPriorityChartData($dateRange)
    {
        $priorities = PurchaseRequest::whereBetween('created_at', $dateRange)
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority');
        
        return [
            $priorities['low'] ?? 0,
            $priorities['medium'] ?? 0,
            $priorities['high'] ?? 0
        ];
    }
    
    private function getSpendingChartData($period)
    {
        $months = $this->getMonthsForPeriod($period);
        $labels = [];
        $data = [];
        
        foreach ($months as $month) {
            $labels[] = $month['label'];
            
            $spending = Procurement::whereBetween('purchase_date', [$month['start'], $month['end']])
                ->where('status', 'completed')
                ->sum('total_amount');
            
            $data[] = $spending;
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
    
    private function getMonthsForPeriod($period)
    {
        $months = [];
        $monthNames = [
            'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
            'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
        ];
        
        switch ($period) {
            case 'year':
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $months[] = [
                        'label' => $monthNames[$date->month - 1],
                        'start' => $date->startOfMonth()->toDateTimeString(),
                        'end' => $date->endOfMonth()->toDateTimeString()
                    ];
                }
                break;
            case 'quarter':
                for ($i = 2; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $months[] = [
                        'label' => $monthNames[$date->month - 1],
                        'start' => $date->startOfMonth()->toDateTimeString(),
                        'end' => $date->endOfMonth()->toDateTimeString()
                    ];
                }
                break;
            case 'month':
            default:
                for ($i = 5; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $months[] = [
                        'label' => $monthNames[$date->month - 1],
                        'start' => $date->startOfMonth()->toDateTimeString(),
                        'end' => $date->endOfMonth()->toDateTimeString()
                    ];
                }
                break;
        }
        
        return $months;
    }
}