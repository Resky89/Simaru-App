<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\UnitAssetController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetDetailsController;
use App\Http\Controllers\AssetDocumentController;
use App\Http\Controllers\AssetDepreciationController;
use App\Http\Controllers\AssetMutationController;
use App\Http\Controllers\AssetHistoryController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Controllers\BrandController;
use Illuminate\Http\Request;
use App\Http\Controllers\ProcurementRequestController;
use App\Http\Controllers\AssetFinanceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CalibrationController;
use App\Http\Controllers\OpnameReportController;
use App\Http\Controllers\FinanceReportController;
use App\Http\Controllers\ComplainRepairController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\MasterAssetController;
use App\Http\Controllers\ViewMasterAssetController;
use App\Http\Controllers\AssetDocumentsController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\DepreciationReportController;
use App\Http\Controllers\ProcurementPriceComparisonController;
use App\Http\Controllers\ProcurementPurchaseOrderController;
use App\Http\Controllers\ProcurementReceiptController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CsrfTokenController;

//=============================================================================
// PUBLIC ROUTES
//=============================================================================

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes (Guest only)
Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

// Logout Route (accessible to authenticated users)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// CSRF Token Refresh Route - Make sure it's accessible without authentication
Route::post('/csrf-token-refresh', function () {
    return response()->json(['token' => csrf_token()]);
});

//=============================================================================
// AUTHENTICATION ROUTES
//=============================================================================

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('/verify-token', [AuthController::class, 'verifyToken'])->name('auth.verify-token');
    Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->name('auth.refresh-token');
    // ...other auth routes
});

// Session check route
Route::post('/auth/check-session', [AuthController::class, 'checkSession'])->name('auth.check-session');

//=============================================================================
// PROTECTED ROUTES
//=============================================================================

// Protected Routes (require authentication)
Route::middleware([AuthMiddleware::class])->group(function () {
    //-------------------------------------------------------------------------
    // DASHBOARD
    //-------------------------------------------------------------------------

    Route::prefix('dashboard')->middleware('permission:dashboard:view')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/summary', [DashboardController::class, 'getSummary'])->name('dashboard.summary');
        Route::get('/calendar', [DashboardController::class, 'getCalendarEvents'])->name('dashboard.calendar');
        Route::get('/activities', [DashboardController::class, 'getAssetActivities'])->name('dashboard.activities');
    });

    // Token Refresh Route (for AJAX requests)
    Route::post('/auth/refresh-token', [AuthController::class, 'refreshToken'])->name('auth.refresh-token');

    // User Profile
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');

    //-------------------------------------------------------------------------
    // ORGANIZATION MANAGEMENT
    //-------------------------------------------------------------------------

    // Building Management
    Route::prefix('buildings')->middleware('permission:building:view|room:create|room:edit|asset:checkout|asset:create|asset:edit|report:depreciation')->group(function () {
        Route::get('/', [BuildingController::class, 'index'])->name('buildings');
        Route::get('/{id}', [BuildingController::class, 'show'])
            ->name('buildings.show')
            ->middleware('permission:building:view');
        Route::post('/store', [BuildingController::class, 'store'])
            ->name('buildings.store')
            ->middleware('permission:building:create');
        Route::put('/update/{id}', [BuildingController::class, 'update'])
            ->name('buildings.update')
            ->middleware('permission:building:edit');
        Route::delete('/delete/{id}', [BuildingController::class, 'destroy'])
            ->name('buildings.destroy')
            ->middleware('permission:building:delete');
        Route::post('/import', [BuildingController::class, 'import'])
            ->name('buildings.import')
            ->middleware('permission:building:import');
    });

    // Room Management
    Route::prefix('rooms')->middleware('permission:room:view|asset:create|asset:edit|asset:checkout|report:depreciation')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('rooms');
        Route::get('/{id}', [RoomController::class, 'show'])
            ->name('rooms.show')
            ->middleware('permission:room:view');
        Route::post('/store', [RoomController::class, 'store'])
            ->name('rooms.store')
            ->middleware('permission:room:create');
        Route::put('/update/{id}', [RoomController::class, 'update'])
            ->name('rooms.update')
            ->middleware('permission:room:edit');
        Route::delete('/delete/{id}', [RoomController::class, 'destroy'])
            ->name('rooms.destroy')
            ->middleware('permission:room:delete');
        Route::post('/import', [RoomController::class, 'import'])
            ->name('rooms.import')
            ->middleware('permission:room:import');
    });

    // Vendor Management
    Route::prefix('vendors')->middleware('permission:vendor:view|maintenance:create|maintenance:edit|calibration:report|price-comparison:vendor-offer:create|price-comparison:vendor-offer:edit')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('vendor');
        Route::post('/store', [VendorController::class, 'store'])
            ->name('vendor.store')
            ->middleware('permission:vendor:create');
        Route::put('/update/{id}', [VendorController::class, 'update'])
            ->name('vendor.update')
            ->middleware('permission:vendor:edit');
        Route::delete('/delete/{id}', [VendorController::class, 'destroy'])
            ->name('vendor.destroy')
            ->middleware('permission:vendor:delete');
        Route::post('/import', [VendorController::class, 'import'])
            ->name('vendor.import')
            ->middleware('permission:vendor:import');
    });

    //-------------------------------------------------------------------------
    // USER & ROLE MANAGEMENT
    //-------------------------------------------------------------------------

    // User Management
    Route::prefix('user')->middleware('permission:user:view|asset:create|asset:edit|asset:checkout|report:depreciation|receipt:create')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user');
        Route::post('/store', [UserController::class, 'storeUser'])
            ->name('users.store')
            ->middleware('permission:user:create');
        Route::put('/update/{id}', [UserController::class, 'updateUser'])
            ->name('users.update')
            ->middleware('permission:user:edit');
        Route::delete('/delete/{id}', [UserController::class, 'destroyUser'])
            ->name('users.destroy')
            ->middleware('permission:user:delete');
    });
    Route::get('/user/by-permission/{permissionName}', [UserController::class, 'getUsersByPermission'])
        ->name('users.by-permission')
        ->middleware('permission:maintenance:create|maintenance:edit');

    // Role Management
    Route::middleware('permission:role:view|user:create|user:edit')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles');
        Route::get('/roles/permissions', [RoleController::class, 'getAllPermissions'])->name('roles.permissions');
        Route::get('/roles/{id}', [RoleController::class, 'show'])->name('roles.show');
        Route::post('/roles', [RoleController::class, 'store'])
            ->name('roles.store')
            ->middleware('permission:role:create');
        Route::put('/roles/{id}', [RoleController::class, 'update'])
            ->name('roles.update')
            ->middleware('permission:role:edit');
        Route::delete('/roles/{id}', [RoleController::class, 'destroy'])
            ->name('roles.destroy')
            ->middleware('permission:role:delete');
    });

    //-------------------------------------------------------------------------
    // ASSET MANAGEMENT
    //-------------------------------------------------------------------------

    // Asset Management Routes
    Route::prefix('asset')->name('asset-')->middleware('permission:asset:view')->group(function () {
        Route::get('/detail/{id?}', [AssetDetailsController::class, 'show'])->name('details');
    });

    // Asset direct routes
    Route::middleware('permission:asset:view')->group(function () {
        Route::get('/asset/{id}', [AssetDetailsController::class, 'show'])->name('asset.details');
        Route::put('/asset/update/{id}', [AssetDetailsController::class, 'updateAsset'])
            ->name('asset.update')
            ->middleware('permission:asset:edit');
        Route::get('/asset/{id}/export-pdf', [AssetDetailsController::class, 'exportAssetDetailPDF'])
            ->name('asset.export-pdf')
            ->middleware('permission:asset:export');
    });

    // Master Asset routes
    Route::prefix('asset-master')->middleware('permission:asset-master:view|asset:create|asset:edit|report:depreciation|procurement:create|procurement:edit')->group(function () {
        Route::get('/', [MasterAssetController::class, 'index'])->name('asset-master');
        Route::get('/data', [MasterAssetController::class, 'getMasterAssetData'])->name('asset-master.data');
        Route::get('/export-pdf', [MasterAssetController::class, 'exportMasterAssetPDF'])
            ->name('export-asset-master-pdf')
            ->middleware('permission:asset-master:export');
        Route::get('/{id}', [MasterAssetController::class, 'getMasterAsset'])->name('asset-master.get');

        // Write operations
        Route::post('/', [MasterAssetController::class, 'storeMasterAsset'])
            ->name('asset-master.store')
            ->middleware('permission:asset-master:create');
        Route::post('/import', [MasterAssetController::class, 'importMasterAsset'])
            ->name('asset-master.import')
            ->middleware('permission:asset-master:import');
        Route::put('/{id}', [MasterAssetController::class, 'updateMasterAsset'])
            ->name('asset-master.update')
            ->middleware('permission:asset-master:edit');
        Route::delete('/{id}', [MasterAssetController::class, 'destroyMasterAsset'])
            ->name('asset-master.destroy')
            ->middleware('permission:asset-master:delete');
    });

    // View Master Asset with linked assets
    Route::middleware('permission:asset-master:view')->group(function () {
        Route::get('/view-asset-master/{id}', [ViewMasterAssetController::class, 'getMasterAssetById'])->name('view-asset-master');
        Route::get('/view-asset-master/{id}/edit', [ViewMasterAssetController::class, 'editMasterAsset'])->name('asset-master.edit');
        Route::get('/view-asset-master/{id}/export-pdf', [ViewMasterAssetController::class, 'exportViewMasterAssetPDF'])
            ->name('export-view-master-asset-pdf')
            ->middleware('permission:asset-master:export');
    });

    // Categories Management
    Route::prefix('categories')->middleware('permission:asset-subcategory:view|asset-master:create|asset-master:edit|report:depreciation')->group(function () {
        Route::get('/', [CategoriesController::class, 'index'])->name('categories');
        Route::get('/{id}', [CategoriesController::class, 'show'])->name('categories.show');

        // Write operations
        Route::post('/store', [CategoriesController::class, 'store'])
            ->name('categories.store')
            ->middleware('permission:asset-subcategory:create');
        Route::put('/update/{id}', [CategoriesController::class, 'update'])
            ->name('categories.update')
            ->middleware('permission:asset-subcategory:edit');
        Route::delete('/delete/{id}', [CategoriesController::class, 'destroy'])
            ->name('categories.destroy')
            ->middleware('permission:asset-subcategory:delete');
        Route::post('/import', [CategoriesController::class, 'import'])
            ->name('categories.import')
            ->middleware('permission:asset-subcategory:import');
    });

    // Brand routes
    Route::prefix('brands')->middleware('permission:brand:view|asset-master:create|asset-master:edit')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('brands');
        Route::get('/{id}', [BrandController::class, 'getBrand'])->name('brands.get');

        // Write operations
        Route::post('/', [BrandController::class, 'store'])
            ->name('brands.store')
            ->middleware('permission:brand:create');
        Route::put('/{id}', [BrandController::class, 'update'])
            ->name('brands.update')
            ->middleware('permission:brand:edit');
        Route::delete('/{id}', [BrandController::class, 'destroy'])
            ->name('brands.destroy')
            ->middleware('permission:brand:delete');
        Route::post('/import', [BrandController::class, 'import'])
            ->name('brands.import')
            ->middleware('permission:brand:import');
    });

    // Asset routes
    Route::middleware('permission:asset:view|maintenance:create|maintenance:edit|calibration:create|calibration:edit|complaint:create|complaint:edit|document:assign')->group(function () {
        // Read operations
        Route::get('/assets', [UnitAssetController::class, 'index'])->name('assets');
        Route::get('/assets/{id}', [UnitAssetController::class, 'getAsset'])->name('assets.get');
        Route::get('assets/barcode/generate/{id}', [UnitAssetController::class, 'generateBarcode'])->name('assets.barcode.generate');
        Route::get('/assets/export/pdf', [UnitAssetController::class, 'exportUnitAssetPDF'])
            ->name('assets.export.pdf')
            ->middleware('permission:asset:export');

        // Write operations
        Route::post('/assets', [UnitAssetController::class, 'storeAsset'])
            ->name('assets.store')
            ->middleware('permission:asset:create');
        Route::put('/assets/{id}', [UnitAssetController::class, 'updateAsset'])
            ->name('assets.update')
            ->middleware('permission:asset:edit');
        Route::delete('/assets/{id}', [UnitAssetController::class, 'destroyAsset'])
            ->name('assets.destroy')
            ->middleware('permission:asset:delete');
        Route::post('/assets/import', [UnitAssetController::class, 'importAssets'])
            ->name('assets.import')
            ->middleware('permission:asset:import');
    });

    // Asset QR routes
    Route::middleware('permission:asset:view')->group(function () {
        Route::post('/assets/qr/generate-bulk', [UnitAssetController::class, 'generateBulkQR'])->name('assets.qr.generate-bulk');
        Route::get('/assets/qr/preview', [UnitAssetController::class, 'previewQRCodes'])->name('assets.qr.preview');
        Route::match(['get', 'post'], '/assets/qr/print-pdf', [UnitAssetController::class, 'printQRCodesPDF'])->name('assets.qr.print-pdf');
        Route::match(['get', 'post'], '/assets/qr/print-direct', [UnitAssetController::class, 'printQRCodesDirect'])->name('assets.qr.print-direct');
    });

    // Asset operations routes
    Route::middleware('permission:asset:view')->group(function () {
        Route::get('/asset-documents/asset/{assetId}/all-documents', [AssetDocumentsController::class, 'getAssetDocuments'])
            ->name('asset-documents.getAssetDocuments');
        Route::get('/asset-mutations/asset/{id}', [AssetMutationController::class, 'getAssetMutationHistory'])->name('asset-mutations.get');
        Route::get('/asset-histories/{id}', [AssetHistoryController::class, 'getAssetHistory'])->name('asset-histories.get');
        // Checkout route
        Route::post('/assets/checkout', [AssetDetailsController::class, 'checkoutAsset'])
            ->name('asset.checkout')
            ->middleware('permission:asset:checkout');

        // Checkin route
        Route::post('/asset/checkin', [AssetDetailsController::class, 'checkinAsset'])
            ->name('asset.checkin')
            ->middleware('permission:asset:checkout');

        // Report Lost route
        Route::post('/asset/lost', [AssetDetailsController::class, 'reportAssetLost'])
            ->name('asset.lost')
            ->middleware('permission:asset:report-loss');

        // Report Found route
        Route::post('/assets/found', [AssetDetailsController::class, 'reportAssetFound'])
            ->name('asset.found')
            ->middleware('permission:asset:report-found');

        // Dispose route
        Route::post('/asset/dispose', [AssetDetailsController::class, 'disposeAsset'])
            ->name('asset.dispose')
            ->middleware('permission:asset:dispose');

        Route::get('/asset-depreciation/{assetId}', [AssetDepreciationController::class, 'getAssetDepreciation'])
            ->name('asset.depreciation.get')
            ->middleware('permission:asset:depreciation:view');

        Route::post('/asset-documents/asset/{assetId}/documents', [AssetDocumentsController::class, 'createAssetDocument'])
            ->name('asset-documents.createAssetDocument')
            ->middleware('permission:asset:document:create');

        Route::put('/asset-depreciation/{assetId}', [AssetDepreciationController::class, 'updateAssetDepreciation'])
            ->name('asset-depreciation.update')
            ->middleware('permission:asset:depreciation:edit');

        // Asset Finance routes
        Route::middleware('permission:asset:transaction:view')->group(function () {
            // Read operations
            Route::get('/asset-transactions/asset/{assetId}', [AssetFinanceController::class, 'getAllTransactions'])->name('asset-transactions.get');
            Route::get('/asset-transactions/{transactionId}', [AssetFinanceController::class, 'getTransaction'])->name('asset-transactions.show');

            // Write operations
            Route::post('/asset-transactions', [AssetFinanceController::class, 'createTransaction'])
                ->name('asset-transactions.store')
                ->middleware('permission:asset:transaction:create');
            Route::put('/asset-transactions/{transactionId}', [AssetFinanceController::class, 'updateTransaction'])
                ->name('asset-transactions.update')
                ->middleware('permission:asset:transaction:edit');
            Route::delete('/asset-transactions/{transactionId}', [AssetFinanceController::class, 'deleteTransaction'])
                ->name('asset-transactions.destroy')
                ->middleware('permission:asset:transaction:delete');
        });
    });

    // Asset Documents routes
    Route::middleware('permission:document:view')->group(function () {
        // Read operations
        Route::get('/asset-documents', [AssetDocumentsController::class, 'index'])->name('asset-documents');
        Route::get('/asset-documents/{id}', [AssetDocumentsController::class, 'getDocument'])->name('document.view');
        Route::get('/asset-documents/asset/{id}', [AssetDocumentsController::class, 'getAssetDocuments'])->name('asset-documents.get');

        // Write operations
        Route::post('/asset-documents', [AssetDocumentsController::class, 'store'])
            ->name('asset-documents.store')
            ->middleware('permission:document:create');
        Route::put('/asset-documents/{id}', [AssetDocumentsController::class, 'update'])
            ->name('asset-documents.update')
            ->middleware('permission:document:edit');
        Route::delete('/asset-documents/{id}', [AssetDocumentsController::class, 'destroy'])
            ->name('asset-documents.destroy')
            ->middleware('permission:document:delete');
        Route::post('/asset-documents/{id}/assign', [AssetDocumentsController::class, 'assignToAssets'])
            ->name('asset-documents.assign')
            ->middleware('permission:document:assign');
        Route::delete('/asset-documents/asset/{assetId}/documents/{documentId}', [AssetDocumentsController::class, 'unlinkFromAsset'])
            ->name('asset-documents.unlink')
            ->middleware('permission:document:unlink');
    });


    //-------------------------------------------------------------------------
    // ASSET MAINTENANCE & CALIBRATION
    //-------------------------------------------------------------------------

    // Maintenance routes
    Route::prefix('maintenance')->middleware('permission:maintenance:view|maintenance-report:medical|maintenance-report:non-medical')->group(function () {
        // Read operations
        Route::get('/', [MaintenanceController::class, 'index'])->name('maintenance');
        Route::get('/detail/{id}', [MaintenanceController::class, 'showMaintenanceDetail'])->name('maintenance.detail');
        Route::get('/export/pdf', [MaintenanceController::class, 'exportMaintenancePDF'])
            ->name('maintenance.export.pdf')
            ->middleware('permission:maintenance:export');
        Route::get('/export/pdf/{id}', [MaintenanceController::class, 'exportMaintenanceDetailPDF'])
            ->name('maintenance.export.detail.pdf')
            ->middleware('permission:maintenance:export');
        Route::get('/{id}', [MaintenanceController::class, 'getMaintenance']);

        // Write operations
        Route::post('/', [MaintenanceController::class, 'createMaintenance'])
            ->name('maintenance.create')
            ->middleware('permission:maintenance:create');
        Route::put('/{id}', [MaintenanceController::class, 'update'])
            ->middleware('permission:maintenance:edit');
        Route::delete('/{id}', [MaintenanceController::class, 'destroy'])
            ->name('maintenance.destroy')
            ->middleware('permission:maintenance:delete');
        Route::post('/reports', [MaintenanceController::class, 'createMaintenanceReport'])
            ->name('maintenance.reports.create')
            ->middleware('permission:maintenance-report:medical|maintenance-report:non-medical');
        Route::patch('/{id}/start', [MaintenanceController::class, 'startMaintenance'])
            ->name('maintenance.start')
            ->middleware('permission:maintenance-report:medical|maintenance-report:non-medical');
    });

    // Maintenance task reminders route (no permission required, only authentication)
    Route::get('/maintenance/task-reminders', [MaintenanceController::class, 'getTaskReminders'])
        ->name('maintenance.task-reminders');

    // Calibration task reminders route (no permission required, only authentication)
    Route::get('/calibrations/task-reminders', [CalibrationController::class, 'getTaskReminders'])
        ->name('calibrations.task-reminders');

    // Calibration routes
    Route::prefix('calibrations')->middleware('permission:calibration:view')->group(function () {
        // Read operations
        Route::get('/', [CalibrationController::class, 'index'])->name('calibration');
        Route::get('/assets', [CalibrationController::class, 'getAssetsForCalibration'])->name('calibrations.assets');
        Route::get('/export/pdf', [CalibrationController::class, 'exportCalibrationPDF'])
            ->name('calibrations.export.pdf')
            ->middleware('permission:calibration:export');
        Route::get('/{id}', [CalibrationController::class, 'getCalibration']);

        // Write operations
        Route::post('/bulk', [CalibrationController::class, 'createBulkCalibrations'])
            ->name('calibrations.bulk.create')
            ->middleware('permission:calibration:create');
        Route::put('/report/{id}', [CalibrationController::class, 'reportCalibration'])
            ->name('calibration.report')
            ->middleware('permission:calibration:report');
        Route::put('/schedule/{id}', [CalibrationController::class, 'updateCalibrationSchedule'])
            ->name('calibration.schedule.update')
            ->middleware('permission:calibration:edit');
        Route::delete('/bulk', [CalibrationController::class, 'destroy'])
            ->name('calibrations.bulk.delete')
            ->middleware('permission:calibration:delete');
        Route::patch('/{id}/start', [CalibrationController::class, 'startCalibration'])
            ->name('calibration.start')
            ->middleware('permission:calibration:edit');
    });

    // Complaint & Repair Routes
    Route::prefix('complaint-repair')->name('complaint.')->middleware('permission:complaint:view')->group(function () {
        // Read operations
        Route::get('/', [ComplainRepairController::class, 'index'])->name('index');
        Route::get('/detail/{id}', [ComplainRepairController::class, 'show'])->name('detail');
        Route::get('/export-pdf', [ComplainRepairController::class, 'exportPDF'])
            ->name('export.pdf')
            ->middleware('permission:complaint:export');
        Route::get('/detail/{id}/export-pdf', [ComplainRepairController::class, 'exportDetailPDF'])
            ->name('detail.export.pdf')
            ->middleware('permission:complaint:export');

        // Write operations
        Route::post('/complaints', [ComplainRepairController::class, 'store'])
            ->name('create')
            ->middleware('permission:complaint:create');
        Route::delete('/complaints/{id}', [ComplainRepairController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:complaint:delete');
        Route::post('/repairs', [ComplainRepairController::class, 'storeRepair'])
            ->name('repair.create')
            ->middleware(['permission:repair:medical|repair:non-medical']);

        // Start repair process
        Route::patch('/{id}/start', [ComplainRepairController::class, 'startRepair'])
            ->name('start')
            ->middleware(['permission:repair:medical|repair:non-medical']);
    });

    //-------------------------------------------------------------------------
    // PROCUREMENT MANAGEMENT
    //-------------------------------------------------------------------------

    // Procurement Routes
    Route::prefix('procurement')->name('procurement.')->middleware('permission:procurement:view|price-comparison:view|purchase-order:view|receipt:view')->group(function () {
        // Request Management
        Route::get('/request', [ProcurementRequestController::class, 'index'])->name('request')->middleware('permission:procurement:view|price-comparison:create|price-comparison:edit');
        Route::get('/form-request', function () {
            return view('Procurement.Request.FormRequest');
        })->name('form-request')->middleware('permission:procurement:create|procurement:edit');
        Route::get('/detail-request/{id}', [ProcurementRequestController::class, 'show'])->name('detail-request');
        Route::get('/request/{id}', [ProcurementRequestController::class, 'getOne'])->name('getOne');
        Route::get('/procurement/request', [ProcurementRequestController::class, 'search'])->name('search');

        // Procurement request write operations
        Route::post('/request', [ProcurementRequestController::class, 'store'])
            ->name('store')
            ->middleware('permission:procurement:create');
        Route::put('/request/{id}', [ProcurementRequestController::class, 'update'])
            ->name('update')
            ->middleware('permission:procurement:edit');
        Route::delete('/request/{id}', [ProcurementRequestController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:procurement:delete');
        Route::post('/request/{id}/manager-approval', [ProcurementRequestController::class, 'managerApproval'])
            ->name('manager-approval')
            ->middleware('permission:procurement:approve:manager');
        Route::post('/request/{id}/director-approval', [ProcurementRequestController::class, 'directorApproval'])
            ->name('director-approval')
            ->middleware('permission:procurement:approve:director');
        Route::post('/request/{id}/reject', [ProcurementRequestController::class, 'rejectProcurement'])
            ->name('reject')
            ->middleware('permission:procurement:reject');

        // Start procurement process
        Route::patch('/request/{id}/start', [ProcurementRequestController::class, 'startProcurement'])
            ->name('start')
            ->middleware('permission:procurement:approve:manager|procurement:approve:director');

        // Price Comparison read operations
        Route::get('/price-comparison', [ProcurementPriceComparisonController::class, 'index'])
            ->name('price-comparison')
            ->middleware('permission:price-comparison:view|purchase-order:vendor-offers:select');
        Route::get('/form-comparison/{id?}', function ($id = null) {
            return view('Procurement.Comparison.FormComparison', ['id' => $id]);
        })->name('form-comparison')->middleware('permission:price-comparison:create|price-comparison:edit');
        Route::get('/edit-comparison/{id}', [ProcurementPriceComparisonController::class, 'edit'])
            ->name('edit-comparison')
            ->middleware('permission:price-comparison:edit');
        Route::get('/form-vendor-comparison/{id?}', function ($id = null) {
            return view('Procurement.Comparison.FormComparisonVendor', ['comparison_id' => $id]);
        })->name('form-vendor-comparison')->middleware('permission:price-comparison:vendor-offer:create|price-comparison:vendor-offer:edit');
        Route::get('/detail-comparison/{id}', [ProcurementPriceComparisonController::class, 'show'])
            ->name('detail-comparison')
            ->middleware('permission:price-comparison:view|purchase-order:vendor-offers:select');

        // Price Comparison write operations
        Route::post('/price-comparison', [ProcurementPriceComparisonController::class, 'store'])
            ->name('store-price-comparison')
            ->middleware('permission:price-comparison:create');
        Route::put('/price-comparison/{id}', [ProcurementPriceComparisonController::class, 'update'])
            ->name('update-comparison')
            ->middleware('permission:price-comparison:edit');
        Route::post('/price-comparison/create-from-detail', [ProcurementPriceComparisonController::class, 'createFromDetail'])
            ->name('create-price-comparison-from-detail')
            ->middleware('permission:price-comparison:create');
        Route::post('/price-comparison/{id}/complete', [ProcurementPriceComparisonController::class, 'completeComparison'])
            ->name('complete-price-comparison')
            ->middleware('permission:price-comparison:complete');

        // Vendor Offer API
        Route::get('/price-comparison/vendor-offer/{id}', [ProcurementPriceComparisonController::class, 'getVendorOffer'])
            ->name('get-vendor-offer');
        Route::post('/price-comparison/vendor-offer', [ProcurementPriceComparisonController::class, 'createVendorOffer'])
            ->name('create-vendor-offer')->middleware('permission:price-comparison:vendor-offer:create');
        Route::put('/price-comparison/vendor-offer/{id}', [ProcurementPriceComparisonController::class, 'updateVendorOffer'])
            ->name('update-vendor-offer')->middleware('permission:price-comparison:vendor-offer:edit');
        Route::delete('/price-comparison/vendor-offer/{id}', [ProcurementPriceComparisonController::class, 'deleteVendorOffer'])
            ->name('delete-vendor-offer')->middleware('permission:price-comparison:vendor-offer:delete');


        // Purchase Order read operations
        Route::get('/purchase-order', [ProcurementPurchaseOrderController::class, 'index'])
            ->name('purchase-order')
            ->middleware('permission:purchase-order:view|receipt:create');
        Route::get('/form-purchase-order/{id?}', function ($id = null) {
            return view('Procurement.PurchaseOrder.FormPurchaseOrder', ['id' => $id]);
        })->name('form-purchase-order')->middleware('permission:purchase-order:vendor-offers:select');
        Route::get('/detail-purchase-order/{id}', [ProcurementPurchaseOrderController::class, 'show'])
            ->name('detail-purchase-order')
            ->middleware('permission:purchase-order:view|receipt:create|purchase-order:vendor-offers:select');
        Route::get('/purchase-order/detail/{id}/export-pdf', [ProcurementPurchaseOrderController::class, 'exportPurchaseOrderDetailPDF'])
            ->name('purchase-order.detail.export-pdf')
            ->middleware('permission:purchase-order:export');

        // Purchase Order write operations
        Route::post('/purchase-order/vendor-offers', [ProcurementPurchaseOrderController::class, 'createFromVendorOffers'])
            ->name('purchase-order.create-from-vendor-offers')
            ->middleware('permission:purchase-order:vendor-offers:select');

        // Receipt routes
        Route::get('/receipt', [ProcurementReceiptController::class, 'index'])->name('receipt')->middleware('permission:receipt:view');
        Route::get('/form-receipt/{id?}', function ($id = null) {
            return view('Procurement.Receipt.FormReceipt', ['id' => $id]);
        })->name('form-receipt')->middleware('permission:receipt:create');
        Route::get('/detail-receipt/{id}', [ProcurementReceiptController::class, 'show'])->name('receipt.show')->middleware('permission:receipt:view');
        Route::post('/receipt', [ProcurementReceiptController::class, 'create'])->name('receipt.create')->middleware('permission:receipt:create');
        Route::get('/receipt/detail/{id}/export-pdf', [ProcurementReceiptController::class, 'exportReceiptDetailPDF'])->name('receipt.export-pdf')->middleware('permission:receipt:export');
    });

    //-------------------------------------------------------------------------
    // REPORTING
    //-------------------------------------------------------------------------

    // Report Routes
    Route::prefix('report')->name('report.')->group(function () {
        // Finance report
        Route::get('/finance', [FinanceReportController::class, 'getAllTransactions'])
            ->name('finance')
            ->middleware('permission:report:finance');
        Route::get('/finance/export-pdf', [FinanceReportController::class, 'exportFinanceReportPDF'])
            ->name('finance.export.pdf')
            ->middleware('permission:report:finance');

        // Opname report
        Route::get('/opname', [OpnameReportController::class, 'index'])
            ->name('opname')
            ->middleware('permission:report:opname');

        // Depreciation report
        Route::get('/depreciation', [DepreciationReportController::class, 'getDepreciationReport'])
            ->name('depreciation')
            ->middleware('permission:report:depreciation');
        Route::get('/depreciation/export-pdf', [DepreciationReportController::class, 'exportDepreciationReportPDF'])
            ->name('depreciation.export-pdf')
            ->middleware('permission:report:depreciation');
    });

    // Opname report routes
    Route::middleware('permission:report:opname')->group(function () {
        Route::get('/opnames', [OpnameReportController::class, 'getAllOpnames'])->name('opnames.getAll');
        Route::get('/opname-detail/{id}', [OpnameReportController::class, 'showOpnameDetail'])->name('opnames.detail');
        Route::get('/opname-detail/{id}/export-pdf', [OpnameReportController::class, 'exportOpnameDetailPDF'])
            ->name('opnames.export.pdf');
    });

    // Notification Routes
    Route::middleware([AuthMiddleware::class])->group(function () {
        // View all notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

        // Mark notification as read
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    });
});

//=============================================================================
// ROUTES OUTSIDE THE AUTH MIDDLEWARE GROUP
//=============================================================================

// Edit routes for master assets
Route::middleware(['permission:asset-master:edit'])->group(function () {
    Route::get('/asset-master/{id}/edit', [ViewMasterAssetController::class, 'editMasterAsset']);
    Route::put('/asset-master/{id}', [ViewMasterAssetController::class, 'updateMasterAsset'])->name('asset-master.update');
});

// Calibration detail routes
Route::middleware(['permission:calibration:view'])->group(function () {
    Route::get('/calibration/detail/{id}', [CalibrationController::class, 'showCalibrationDetail'])->name('calibration.detail');
    Route::get('/calibration/edit/{id}', [CalibrationController::class, 'update'])
        ->name('calibration.edit')
        ->middleware('permission:calibration:edit');
    Route::get('/calibration/detail/{id}/export-pdf', [CalibrationController::class, 'exportCalibrationDetailPDF'])
        ->name('calibration.detail.export.pdf')
        ->middleware('permission:calibration:export');
});

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->view('Error.NotFound', [], 404);
});

