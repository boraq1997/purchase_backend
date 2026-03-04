<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\CommitteeController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\EstimateController;
use App\Http\Controllers\API\EstimateItemController;
use App\Http\Controllers\API\NeedsAssessmentController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\WarehouseCheckController;
use App\Http\Controllers\API\ProcurementController;
use App\Http\Controllers\API\ProcurementItemController;
use App\Http\Controllers\API\PurchaseRequestController;
use App\Http\Controllers\API\VendorController;
use App\Http\Controllers\API\ActivityLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\PurchaseRequestImageController;
use App\Http\Controllers\API\UnitController;

// ================= LOGIN =================
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [UserController::class, 'updateUserPassword']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('api.dashboard');

    // ================= ACTIVITY LOG =================
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->middleware('permission:view-ActivityLogs');

    // ================= USERS =================
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:view-User');
        Route::get('/available-for-department', [UserController::class, 'availableForDepartment'])
            ->middleware('permission:view-User');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:create-User');
        Route::get('/{user}', [UserController::class, 'show'])->middleware('permission:view-User');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:edit-User');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:delete-User');
    });

    // ================= DEPARTMENTS =================
    Route::prefix('departments')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->middleware('permission:view-Department');
        Route::post('/', [DepartmentController::class, 'store'])->middleware('permission:create-Department');
        Route::get('/with-users', [DepartmentController::class, 'indexWithUsers'])
            ->middleware('permission:view-Department');
        Route::get('/{department}', [DepartmentController::class, 'show'])->middleware('permission:view-Department');
        Route::put('/{department}', [DepartmentController::class, 'update'])->middleware('permission:edit-Department');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->middleware('permission:delete-Department');
    });

    // ================= VENDORS =================
    Route::prefix('vendors')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->middleware('permission:view-Vendors');
        Route::get('/with-relations', [VendorController::class, 'indexWithRelations'])->middleware('permission:view-Vendors');
        Route::get('/{vendor}', [VendorController::class, 'show'])->middleware('permission:view-Vendors');
        Route::post('/', [VendorController::class, 'store'])->middleware('permission:create-Vendors');
        Route::put('/{vendor}', [VendorController::class, 'update'])->middleware('permission:edit-Vendors');
        Route::delete('/{vendor}', [VendorController::class, 'destroy'])->middleware('permission:delete-Vendors');
    });

    // ================= COMMITTEES =================
    Route::prefix('committees')->group(function () {
        Route::get('/', [CommitteeController::class, 'index'])->middleware('permission:view-Committees');
        Route::get('/with-relations', [CommitteeController::class, 'indexWithUsers'])->middleware('permission:view-Committees');
        Route::post('/', [CommitteeController::class, 'store'])->middleware('permission:create-Committees');
        Route::get('/{committee}', [CommitteeController::class, 'show'])->middleware('permission:view-Committees');
        Route::put('/{committee}', [CommitteeController::class, 'update'])->middleware('permission:edit-Committees');
        Route::delete('/{committee}', [CommitteeController::class, 'destroy'])->middleware('permission:delete-Committees');
    });

    // ================= ROLES =================
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('permission:view-Role');
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:create-Role');
        Route::get('/{role}', [RoleController::class, 'show'])->middleware('permission:view-Role');
        Route::put('/{role}', [RoleController::class, 'update'])->middleware('permission:edit-Role');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('permission:delete-Role');
    });

    // ================= PERMISSIONS =================
    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->middleware('permission:view-Permission');
        Route::post('/', [PermissionController::class, 'store'])->middleware('permission:create-Permission');
        Route::get('/{permission}', [PermissionController::class, 'show'])->middleware('permission:view-Permission');
        Route::put('/{permission}', [PermissionController::class, 'update'])->middleware('permission:edit-Permission');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:delete-Permission');
    });

    // ================= ESTIMATES =================
    Route::prefix('request-items/{requestItem}')->group(function () {
        Route::post('estimates', [EstimateController::class, 'store'])->middleware('permission:create-Estimate');
        Route::get('estimates', [EstimateController::class, 'indexForItem'])->middleware('permission:view-Estimate');
    });

    Route::get('/estimates', [EstimateController::class, 'index'])->middleware('permission:view-Estimate');

    Route::get('/estimates/by-item/{item}', [EstimateController::class, 'getByItem'])->middleware('permission:view-Estimate');

    Route::post('/purchase-requests/{purchaseRequests}/estimates/with-items',[EstimateController::class, 'storeWithItems'])->middleware('permission:create-Estimate');

    // ================= ESTIMATE ITEMS =================
    Route::prefix('estimate-items')->group(function () {
        Route::get('/', [EstimateItemController::class, 'index'])->middleware('permission:view-EstimateItem');
        Route::post('/', [EstimateItemController::class, 'store'])->middleware('permission:create-EstimateItem');
        Route::get('/{estimateItem}', [EstimateItemController::class, 'show'])->middleware('permission:view-EstimateItem');
        Route::put('/{estimateItem}', [EstimateItemController::class, 'update'])->middleware('permission:edit-EstimateItem');
        Route::delete('/{estimateItem}', [EstimateItemController::class, 'destroy'])->middleware('permission:delete-EstimateItem');
    });

    // ================= PURCHASE REQUESTS =================
    Route::prefix('purchase-requests')->group(function () {
        Route::get('/', [PurchaseRequestController::class, 'index'])->middleware('permission:view-PurchaseRequest');
        Route::post('/', [PurchaseRequestController::class, 'store'])->middleware('permission:create-PurchaseRequest');
        Route::get('/{purchaseRequest}', [PurchaseRequestController::class, 'show'])->middleware('permission:view-PurchaseRequest');
        Route::put('/{purchaseRequest}', [PurchaseRequestController::class, 'update'])->middleware('permission:edit-PurchaseRequest');
        Route::delete('/{purchaseRequest}', [PurchaseRequestController::class, 'destroy'])->middleware('permission:delete-PurchaseRequest');
        Route::patch('/{purchaseRequest}/status', [PurchaseRequestController::class, 'updateStatus'])
            ->middleware('permission:edit-PurchaseRequest');
    });

    // ================= PURCHASE REQUEST IMAGES =================
    Route::post('purchase-requests/images', [PurchaseRequestImageController::class, 'store']);
        //->middleware('permission:PurchaseRequestImage-create');

    Route::delete('purchase-request-images/{image}', [PurchaseRequestImageController::class, 'destroy']);
        //->middleware('permission:PurchaseRequestImage-delete');

    Route::get('purchase-requests/{id}/images', [PurchaseRequestImageController::class, 'indexByPurchaseRequest']);
        //->middleware('permission:PurchaseRequestImage-view');

    Route::get('purchase-request-images/{id}', [PurchaseRequestImageController::class, 'show']);
        //->middleware('permission:PurchaseRequestImage-view');

    // ================= WAREHOUSE CHECKS =================
    Route::prefix('warehouse-checks')->group(function () {
        Route::get('/', [WarehouseCheckController::class, 'index']);//->middleware('permission:WarehouseCheck-view');
        Route::post('/', [WarehouseCheckController::class, 'store']);//->middleware('permission:WarehouseCheck-create');
        Route::get('/report/{purchaseRequestId}/{requestItemId}', [WarehouseCheckController::class, 'showItemReport']);
            //->middleware('permission:WarehouseCheck-view');
        Route::get('/{warehouseCheck}', [WarehouseCheckController::class, 'show']);//->middleware('permission:WarehouseCheck-view');
        Route::put('/{warehouseCheck}', [WarehouseCheckController::class, 'update']);//->middleware('permission:WarehouseCheck-edit');
        Route::delete('/{warehouseCheck}', [WarehouseCheckController::class, 'destroy']);//->middleware('permission:WarehouseCheck-delete');
    });

    // ================= NEEDS ASSESSMENTS =================
    // Route::prefix('needs-assessments')->group(function () {
    //     Route::get('/', [NeedsAssessmentController::class, 'index'])->middleware('permission:NeedsAssessment-view');
    //     Route::post('/', [NeedsAssessmentController::class, 'store'])->middleware('permission:NeedsAssessment-create');
    //     Route::get('/by-item/{purchaseRequestId}/{requestItemId}',
    //         [NeedsAssessmentController::class, 'getByItemAndRequest'])
    //         ->middleware('permission:NeedsAssessment-view');
    //     Route::get('/{needsAssessment}', [NeedsAssessmentController::class, 'show'])->middleware('permission:NeedsAssessment-view');
    //     Route::put('/{needsAssessment}', [NeedsAssessmentController::class, 'update'])->middleware('permission:NeedsAssessment-edit');
    //     Route::delete('/{needsAssessment}', [NeedsAssessmentController::class, 'destroy'])->middleware('permission:NeedsAssessment-delete');
    // });

    // ================= REPORTS =================
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index']);//->middleware('permission:Report-view');
        Route::post('/', [ReportController::class, 'store']);//->middleware('permission:Report-create');
        Route::get('/{report}', [ReportController::class, 'show']);//->middleware('permission:Report-view');
        Route::get('/{report}/download', [ReportController::class, 'download']);//->middleware('permission:Report-view');
        Route::put('/{report}', [ReportController::class, 'update']);//->middleware('permission:Report-edit');
        Route::delete('/{report}', [ReportController::class, 'destroy']);//->middleware('permission:Report-delete');
    });

    Route::get('reportGene/{purchaseRequestId}', [ReportController::class, 'generate']);
        //->middleware('permission:Report-create');

    // ================= PROCUREMENTS =================
    Route::prefix('procurements')->group(function () {
        Route::get('/', [ProcurementController::class, 'index'])->middleware('permission:view-Procurement');
        Route::get('/{procurement}', [ProcurementController::class, 'show'])->middleware('permission:view-Procurement');
        Route::post('/', [ProcurementController::class, 'store'])->middleware('permission:create-Procurement');
        Route::put('/{procurement}', [ProcurementController::class, 'update'])->middleware('permission:edit-Procurement');
        Route::delete('/{procurement}', [ProcurementController::class, 'destroy'])->middleware('permission:delete-Procurement');
    });

    // ================= PROCUREMENT ITEMS =================
    Route::prefix('procurement-items')->group(function () {
        Route::get('/', [ProcurementItemController::class, 'index'])->middleware('permission:view-ProcurementItem');
        Route::get('/{item}', [ProcurementItemController::class, 'show'])->middleware('permission:view-ProcurementItem');
        Route::post('/', [ProcurementItemController::class, 'store'])->middleware('permission:create-ProcurementItem');
        Route::put('/{item}', [ProcurementItemController::class, 'update'])->middleware('permission:edit-ProcurementItem');
        Route::delete('/{item}', [ProcurementItemController::class, 'destroy'])->middleware('permission:delete-ProcurementItem');
    });

    // ================= UNITS =================
    Route::prefix('units')->group(function () {
        Route::get('/', [UnitController::class, 'index']);//->middleware('permission:Unit-view');
        Route::post('/', [UnitController::class, 'store']);//->middleware('permission:Unit-create');
        Route::put('/{unit}', [UnitController::class, 'update']);//->middleware('permission:Unit-edit');
        Route::delete('/{unit}', [UnitController::class, 'destroy']);//->middleware('permission:Unit-delete');
    });
});