<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\ActivityLogController;
use App\Http\Controllers\API\CommitteeController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\EstimateController;
use App\Http\Controllers\API\EstimateImageController;
use App\Http\Controllers\API\EstimateItemController;
use App\Http\Controllers\API\NeedsAssessmentController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\ProcurementController;
use App\Http\Controllers\API\ProcurementItemController;
use App\Http\Controllers\API\PurchaseRequestController;
use App\Http\Controllers\API\PurchaseRequestImageController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\UnitController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VendorController;
use App\Http\Controllers\API\WarehouseCheckController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api and protected via Sanctum authentication
| unless explicitly noted. Permission-based middleware is applied per route.
|
*/

// ═══════════════════════════════════════════════════════
//  AUTH — Public
// ═══════════════════════════════════════════════════════

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

// ═══════════════════════════════════════════════════════
//  AUTHENTICATED ROUTES
// ═══════════════════════════════════════════════════════

Route::middleware('auth:sanctum')->group(function () {

    // -------------------------------------------------------
    //  Auth — Session Management
    // -------------------------------------------------------

    Route::get('/me',              [AuthController::class, 'me']);
    Route::post('/refresh',        [AuthController::class, 'refresh']);
    Route::post('/logout',         [AuthController::class, 'logout']);
    Route::post('/change-password',[UserController::class, 'updateUserPassword']);

    // -------------------------------------------------------
    //  Dashboard
    // -------------------------------------------------------

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('api.dashboard');

    // -------------------------------------------------------
    //  Activity Logs
    // -------------------------------------------------------

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->middleware('permission:view-ActivityLog');

    // -------------------------------------------------------
    //  Users
    // -------------------------------------------------------

    Route::prefix('users')->group(function () {
        Route::get('/',                        [UserController::class, 'index'])->middleware('permission:view-User');
        Route::post('/',                       [UserController::class, 'store'])->middleware('permission:create-User');
        Route::get('/available-for-department',[UserController::class, 'availableForDepartment'])->middleware('permission:view-User');
        Route::get('/{user}',                  [UserController::class, 'show'])->middleware('permission:view-User');
        Route::put('/{user}',                  [UserController::class, 'update'])->middleware('permission:edit-User');
        Route::delete('/{user}',               [UserController::class, 'destroy'])->middleware('permission:delete-User');
    });

    // -------------------------------------------------------
    //  Departments
    // -------------------------------------------------------

    Route::prefix('departments')->group(function () {
        Route::get('/',             [DepartmentController::class, 'index'])->middleware('permission:view-Department');
        Route::post('/',            [DepartmentController::class, 'store'])->middleware('permission:create-Department');
        Route::get('/with-users',   [DepartmentController::class, 'indexWithUsers'])->middleware('permission:view-Department');
        Route::get('/{department}', [DepartmentController::class, 'show'])->middleware('permission:view-Department');
        Route::put('/{department}', [DepartmentController::class, 'update'])->middleware('permission:edit-Department');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->middleware('permission:delete-Department');
    });

    // -------------------------------------------------------
    //  Vendors
    // -------------------------------------------------------

    Route::prefix('vendors')->group(function () {
        Route::get('/',                 [VendorController::class, 'index'])->middleware('permission:view-Vendors');
        Route::post('/',                [VendorController::class, 'store'])->middleware('permission:create-Vendors');
        Route::get('/with-relations',   [VendorController::class, 'indexWithRelations'])->middleware('permission:view-Vendors');
        Route::get('/{vendor}',         [VendorController::class, 'show'])->middleware('permission:view-Vendors');
        Route::put('/{vendor}',         [VendorController::class, 'update'])->middleware('permission:edit-Vendors');
        Route::delete('/{vendor}',      [VendorController::class, 'destroy'])->middleware('permission:delete-Vendors');
    });

    // -------------------------------------------------------
    //  Committees
    // -------------------------------------------------------

    Route::prefix('committees')->group(function () {
        Route::get('/',                 [CommitteeController::class, 'index'])->middleware('permission:view-Committees');
        Route::post('/',                [CommitteeController::class, 'store'])->middleware('permission:create-Committees');
        Route::get('/with-relations',   [CommitteeController::class, 'indexWithUsers'])->middleware('permission:view-Committees');
        Route::get('/{committee}',      [CommitteeController::class, 'show'])->middleware('permission:view-Committees');
        Route::put('/{committee}',      [CommitteeController::class, 'update'])->middleware('permission:edit-Committees');
        Route::delete('/{committee}',   [CommitteeController::class, 'destroy'])->middleware('permission:delete-Committees');
    });

    // -------------------------------------------------------
    //  Roles
    // -------------------------------------------------------

    Route::prefix('roles')->group(function () {
        Route::get('/',        [RoleController::class, 'index'])->middleware('permission:view-Role');
        Route::post('/',       [RoleController::class, 'store'])->middleware('permission:create-Role');
        Route::get('/{role}',  [RoleController::class, 'show'])->middleware('permission:view-Role');
        Route::put('/{role}',  [RoleController::class, 'update'])->middleware('permission:edit-Role');
        Route::delete('/{role}',[RoleController::class, 'destroy'])->middleware('permission:delete-Role');
    });

    // -------------------------------------------------------
    //  Permissions
    // -------------------------------------------------------

    Route::prefix('permissions')->group(function () {
        Route::get('/',              [PermissionController::class, 'index'])->middleware('permission:view-Permission');
        Route::post('/',             [PermissionController::class, 'store'])->middleware('permission:create-Permission');
        Route::get('/{permission}',  [PermissionController::class, 'show'])->middleware('permission:view-Permission');
        Route::put('/{permission}',  [PermissionController::class, 'update'])->middleware('permission:edit-Permission');
        Route::delete('/{permission}',[PermissionController::class, 'destroy'])->middleware('permission:delete-Permission');
    });

    // -------------------------------------------------------
    //  Units
    // -------------------------------------------------------

    Route::prefix('units')->group(function () {
        Route::get('/',       [UnitController::class, 'index']);
        Route::post('/',      [UnitController::class, 'store']);
        Route::put('/{unit}', [UnitController::class, 'update']);
        Route::delete('/{unit}', [UnitController::class, 'destroy']);
    });

    // ═══════════════════════════════════════════════════════
    //  PURCHASE REQUESTS
    // ═══════════════════════════════════════════════════════

    // -------------------------------------------------------
    //  Purchase Requests — Core CRUD
    // -------------------------------------------------------

    Route::prefix('purchase-requests')->group(function () {
        Route::get('/',                     [PurchaseRequestController::class, 'index'])->middleware('permission:view-PurchaseRequest');
        Route::post('/',                    [PurchaseRequestController::class, 'store'])->middleware('permission:create-PurchaseRequest');
        Route::get('/{purchaseRequest}',    [PurchaseRequestController::class, 'show'])->middleware('permission:view-PurchaseRequest');
        Route::put('/{purchaseRequest}',    [PurchaseRequestController::class, 'update'])->middleware('permission:edit-PurchaseRequest');
        Route::delete('/{purchaseRequest}', [PurchaseRequestController::class, 'destroy'])->middleware('permission:delete-PurchaseRequest');
        Route::patch('/{purchaseRequest}/status', [PurchaseRequestController::class, 'updateStatus'])->middleware('permission:edit-PurchaseRequest');

        // Estimates scoped to a purchase request
        Route::get('/{purchaseRequest}/estimates',            [EstimateController::class, 'getByPurchaseRequest'])->middleware('permission:view-Estimate');
        Route::post('/{purchaseRequest}/estimates/with-items',[EstimateController::class, 'storeWithItems'])->middleware('permission:create-Estimate');

        // Images scoped to a purchase request
        Route::get('/{purchaseRequest}/images', [PurchaseRequestImageController::class, 'indexByPurchaseRequest']);
    });

    // -------------------------------------------------------
    //  Purchase Request Images — Standalone Operations
    // -------------------------------------------------------

    Route::post('/purchase-requests/images',          [PurchaseRequestImageController::class, 'store']);
    Route::get('/purchase-request-images/{image}',    [PurchaseRequestImageController::class, 'show']);
    Route::delete('/purchase-request-images/{image}', [PurchaseRequestImageController::class, 'destroy']);

    // ═══════════════════════════════════════════════════════
    //  ESTIMATES
    // ═══════════════════════════════════════════════════════

    // -------------------------------------------------------
    //  Estimates — Core CRUD
    // -------------------------------------------------------

    Route::prefix('estimates')->group(function () {
        Route::get('/',                 [EstimateController::class, 'index'])->middleware('permission:view-Estimate');
        Route::get('/by-item/{item}',   [EstimateController::class, 'getByItem'])->middleware('permission:view-Estimate');
        Route::get('/{estimate}',       [EstimateController::class, 'show'])->middleware('permission:view-Estimate');
        Route::put('/{estimate}',       [EstimateController::class, 'update'])->middleware('permission:edit-Estimate');
        Route::delete('/{estimate}',    [EstimateController::class, 'destroy'])->middleware('permission:delete-Estimate');
    });

    // -------------------------------------------------------
    //  Estimates — Scoped to Request Item
    // -------------------------------------------------------

    Route::prefix('request-items/{requestItem}')->group(function () {
        Route::get('/estimates',  [EstimateController::class, 'indexForItem'])->middleware('permission:view-Estimate');
        Route::post('/estimates', [EstimateController::class, 'storeForItem'])->middleware('permission:create-Estimate');
    });

    // -------------------------------------------------------
    //  Estimate Items
    // -------------------------------------------------------

    Route::prefix('estimate-items')->group(function () {
        Route::get('/',               [EstimateItemController::class, 'index'])->middleware('permission:view-EstimateItem');
        Route::post('/',              [EstimateItemController::class, 'store'])->middleware('permission:create-EstimateItem');
        Route::get('/{estimateItem}', [EstimateItemController::class, 'show'])->middleware('permission:view-EstimateItem');
        Route::put('/{estimateItem}', [EstimateItemController::class, 'update'])->middleware('permission:edit-EstimateItem');
        Route::delete('/{estimateItem}',[EstimateItemController::class, 'destroy'])->middleware('permission:delete-EstimateItem');
    });

    // -------------------------------------------------------
    //  Estimate Images — Standalone Operations
    // -------------------------------------------------------

    Route::delete('/estimate-images/{estimateImage}', [EstimateImageController::class, 'destroy']);

    // ═══════════════════════════════════════════════════════
    //  WAREHOUSE & ASSESSMENTS
    // ═══════════════════════════════════════════════════════

    // -------------------------------------------------------
    //  Warehouse Checks
    // -------------------------------------------------------

    Route::prefix('warehouse-checks')->group(function () {
        Route::get('/',                                              [WarehouseCheckController::class, 'index']);
        Route::post('/',                                             [WarehouseCheckController::class, 'store']);
        Route::get('/report/{purchaseRequestId}/{requestItemId}',    [WarehouseCheckController::class, 'showItemReport']);
        Route::get('/{warehouseCheck}',                              [WarehouseCheckController::class, 'show']);
        Route::put('/{warehouseCheck}',                              [WarehouseCheckController::class, 'update']);
        Route::delete('/{warehouseCheck}',                           [WarehouseCheckController::class, 'destroy']);
    });

    // -------------------------------------------------------
    //  Needs Assessments  (currently disabled)
    // -------------------------------------------------------

    // Route::prefix('needs-assessments')->group(function () {
    //     Route::get('/',                                              [NeedsAssessmentController::class, 'index'])->middleware('permission:NeedsAssessment-view');
    //     Route::post('/',                                             [NeedsAssessmentController::class, 'store'])->middleware('permission:NeedsAssessment-create');
    //     Route::get('/by-item/{purchaseRequestId}/{requestItemId}',   [NeedsAssessmentController::class, 'getByItemAndRequest'])->middleware('permission:NeedsAssessment-view');
    //     Route::get('/{needsAssessment}',                             [NeedsAssessmentController::class, 'show'])->middleware('permission:NeedsAssessment-view');
    //     Route::put('/{needsAssessment}',                             [NeedsAssessmentController::class, 'update'])->middleware('permission:NeedsAssessment-edit');
    //     Route::delete('/{needsAssessment}',                          [NeedsAssessmentController::class, 'destroy'])->middleware('permission:NeedsAssessment-delete');
    // });

    // ═══════════════════════════════════════════════════════
    //  PROCUREMENT
    // ═══════════════════════════════════════════════════════

    // -------------------------------------------------------
    //  Procurements
    // -------------------------------------------------------

    Route::prefix('procurements')->group(function () {
        Route::get('/',                         [ProcurementController::class, 'index'])->middleware('permission:view-Procurement');
        Route::post('/',                        [ProcurementController::class, 'store'])->middleware('permission:create-Procurement');
        Route::get('/{procurement}',            [ProcurementController::class, 'show'])->middleware('permission:view-Procurement');
        Route::put('/{procurement}',            [ProcurementController::class, 'update'])->middleware('permission:edit-Procurement');
        Route::delete('/{procurement}',         [ProcurementController::class, 'destroy'])->middleware('permission:delete-Procurement');
        Route::patch('/{procurement}/complete', [ProcurementController::class, 'markAsCompleted'])->middleware('permission:edit-Procurement');
    });

    // -------------------------------------------------------
    //  Procurement Items
    // -------------------------------------------------------

    Route::prefix('procurement-items')->group(function () {
        Route::get('/',       [ProcurementItemController::class, 'index'])->middleware('permission:view-ProcurementItem');
        Route::post('/',      [ProcurementItemController::class, 'store'])->middleware('permission:create-ProcurementItem');
        Route::get('/{item}', [ProcurementItemController::class, 'show'])->middleware('permission:view-ProcurementItem');
        Route::put('/{item}', [ProcurementItemController::class, 'update'])->middleware('permission:edit-ProcurementItem');
        Route::delete('/{item}',[ProcurementItemController::class, 'destroy'])->middleware('permission:delete-ProcurementItem');
    });

    // ═══════════════════════════════════════════════════════
    //  REPORTS
    // ═══════════════════════════════════════════════════════

    Route::prefix('reports')->group(function () {
        Route::get('/',                  [ReportController::class, 'index']);
        Route::post('/',                 [ReportController::class, 'store']);
        Route::get('/{report}',          [ReportController::class, 'show']);
        Route::get('/{report}/download', [ReportController::class, 'download']);
        Route::put('/{report}',          [ReportController::class, 'update']);
        Route::delete('/{report}',       [ReportController::class, 'destroy']);
    });

    Route::get('/reports/generate/{purchaseRequestId}', [ReportController::class, 'generate']);

    Route::get('/debug-permissions', function () {
        return auth()->user()->getAllPermissions()->pluck('name');
    });
}); // end auth:sanctum