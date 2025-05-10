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
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/summary', [DashboardController::class, 'getSummary'])->name('dashboard.summary');
    Route::get('/dashboard/depreciation', [DashboardController::class, 'getDepreciation'])->name('dashboard.depreciation');

    // Token Refresh Route (for AJAX requests)
    Route::post('/auth/refresh-token', [AuthController::class, 'refreshToken'])->name('auth.refresh-token');

    // User Profile
    Route::get('/profile', function () {
        return view('Profile');
    })->name('profile');

    //-------------------------------------------------------------------------
    // ORGANIZATION MANAGEMENT
    //-------------------------------------------------------------------------

    // Building Management
    Route::prefix('buildings')->group(function () {
        Route::get('/', [BuildingController::class, 'index'])->name('buildings');
        Route::get('/data', [BuildingController::class, 'getData'])->name('buildings.data');
        Route::post('/store', [BuildingController::class, 'store'])->name('buildings.store');
        Route::put('/update/{id}', [BuildingController::class, 'update'])->name('buildings.update');
        Route::delete('/delete/{id}', [BuildingController::class, 'destroy'])->name('buildings.destroy');
        Route::post('/import', [BuildingController::class, 'import'])->name('buildings.import');
    });

    // Room Management
    Route::prefix('rooms')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('rooms');
        Route::get('/data', [RoomController::class, 'getData'])->name('rooms.data');
        Route::post('/store', [RoomController::class, 'store'])->name('rooms.store');
        Route::put('/update/{id}', [RoomController::class, 'update'])->name('rooms.update');
        Route::delete('/delete/{id}', [RoomController::class, 'destroy'])->name('rooms.destroy');
        Route::post('/import', [RoomController::class, 'import'])->name('rooms.import');
    });

    // Vendor Management
    Route::prefix('vendor')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('vendor');
        Route::post('/store', [VendorController::class, 'store'])->name('vendor.store');
        Route::put('/update/{id}', [VendorController::class, 'update'])->name('vendor.update');
        Route::delete('/delete/{id}', [VendorController::class, 'destroy'])->name('vendor.destroy');
        Route::post('/import', [VendorController::class, 'import'])->name('vendor.import');
    });

    // User Management
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user');
        Route::get('/search', [UserController::class, 'index'])->name('user.search');

        // User API Routes
        Route::post('/store', [UserController::class, 'storeUser'])->name('users.store');
        Route::put('/update/{id}', [UserController::class, 'updateUser'])->name('users.update');
        Route::delete('/delete/{id}', [UserController::class, 'destroyUser'])->name('users.destroy');
    });

    // Role Management
    Route::get('/roles', [RoleController::class, 'index'])->name('roles');
    Route::get('/roles/permissions', [RoleController::class, 'getAllPermissions'])->name('roles.permissions');
    Route::get('/roles/{id}', [RoleController::class, 'show'])->name('roles.show');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');

    //-------------------------------------------------------------------------
    // ASSET MANAGEMENT
    //-------------------------------------------------------------------------

    // Asset Management Routes
    Route::prefix('asset')->name('asset-')->group(function () {
        Route::get('/unit', [UnitAssetController::class, 'index'])->name('unit');
        Route::get('/detail/{id?}', [AssetDetailsController::class, 'show'])->name('details');
    });

    // Master Asset routes
    Route::prefix('asset-master')->group(function () {
        Route::get('/', [MasterAssetController::class, 'index'])->name('asset-master');
        Route::post('/', [MasterAssetController::class, 'storeMasterAsset'])->name('asset-master.store');
        Route::get('/data', [MasterAssetController::class, 'getMasterAssetData'])->name('asset-master.data');
        Route::post('/import', [MasterAssetController::class, 'importMasterAsset'])->name('asset-master.import');
        Route::get('/export-pdf', [MasterAssetController::class, 'exportMasterAssetPDF'])->name('export-asset-master-pdf');
        Route::get('/{id}', [MasterAssetController::class, 'getMasterAsset'])->name('asset-master.get');
        Route::put('/{id}', [MasterAssetController::class, 'updateMasterAsset'])->name('asset-master.update');
        Route::delete('/{id}', [MasterAssetController::class, 'destroyMasterAsset'])->name('asset-master.destroy');
    });

    // View Master Asset with linked assets
    Route::get('/view-asset-master/{id}', [ViewMasterAssetController::class, 'getMasterAssetById'])->name('view-asset-master');
    Route::get('/view-asset-master/{id}/edit', [ViewMasterAssetController::class, 'editMasterAsset'])->name('asset-master.edit');
    Route::get('/view-asset-master/{id}/export-pdf', [ViewMasterAssetController::class, 'exportMasterAssetPDF'])->name('export-master-asset-pdf');

    // Asset direct routes
    Route::get('/asset/{id}', [AssetDetailsController::class, 'show'])->name('asset.details');
    Route::put('/asset/update/{id}', [AssetDetailsController::class, 'updateAsset'])->name('asset.update');

    // Categories Management
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoriesController::class, 'index'])->name('categories');
        Route::post('/store', [CategoriesController::class, 'store'])->name('categories.store');
        Route::put('/update/{id}', [CategoriesController::class, 'update'])->name('categories.update');
        Route::delete('/delete/{id}', [CategoriesController::class, 'destroy'])->name('categories.destroy');
        Route::get('/by-asset-type', [CategoriesController::class, 'getByAssetType'])->name('categories.by-asset-type');
        Route::post('/import', [CategoriesController::class, 'import'])->name('categories.import');
    });

    // Brand routes
    Route::prefix('brands')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('brands');
        Route::get('/{id}', [BrandController::class, 'getBrand'])->name('brands.get');
        Route::post('/', [BrandController::class, 'store'])->name('brands.store');
        Route::put('/{id}', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/{id}', [BrandController::class, 'destroy'])->name('brands.destroy');
        Route::post('/import', [BrandController::class, 'import'])->name('brands.import');
    });

    // Asset routes
    Route::get('/assets', [UnitAssetController::class, 'index'])->name('assets');
    Route::get('/assets/data', [UnitAssetController::class, 'getAssetData'])->name('assets.data');
    Route::get('/assets/{id}', [UnitAssetController::class, 'getAsset'])->name('assets.get');
    Route::post('/assets', [UnitAssetController::class, 'storeAsset'])->name('assets.store');
    Route::put('/assets/{id}', [UnitAssetController::class, 'updateAsset'])->name('assets.update');
    Route::delete('/assets/{id}', [UnitAssetController::class, 'destroyAsset'])->name('assets.destroy');
    Route::get('assets/barcode/generate/{id}', [UnitAssetController::class, 'generateBarcode'])->name('assets.barcode.generate');
    Route::post('/assets/import', [UnitAssetController::class, 'importAssets'])->name('assets.import');
    Route::get('/assets/export/pdf', [UnitAssetController::class, 'exportUnitAssetPDF'])->name('assets.export.pdf');

    // Asset Documents routes
    Route::get('/asset-documents/asset/{id}', [AssetDocumentsController::class, 'getAssetDocuments'])->name('asset-documents.get');
    Route::get('/asset-documents/{id}', [AssetDocumentsController::class, 'getDocument'])->name('document.view');
    Route::post('/asset-documents', [AssetDocumentsController::class, 'store'])->name('asset-documents.store');
    Route::put('/asset-documents/{id}', [AssetDocumentsController::class, 'update'])->name('asset-documents.update');
    Route::delete('/asset-documents/{id}', [AssetDocumentsController::class, 'destroy'])->name('asset-documents.destroy');
    Route::get('/asset-documents', [AssetDocumentsController::class, 'index'])->name('asset-documents');
    Route::post('/asset-documents/{id}/assign', [AssetDocumentsController::class, 'assignToAssets'])->name('asset-documents.assign');
    Route::delete('/asset-documents/asset/{assetId}/documents/{documentId}', [AssetDocumentsController::class, 'unlinkFromAsset'])->name('asset-documents.unlink');
    Route::get('/asset-documents/asset/{assetId}/all-documents', [AssetDocumentsController::class, 'getAssetDocuments'])->name('asset-documents.getAssetDocuments');
    Route::post('/asset-documents/asset/{assetId}/documents', [AssetDocumentsController::class, 'createAssetDocument'])->name('asset-documents.createAssetDocument');

    // Asset Depreciation route
    Route::get('/asset-depreciation/{assetId}', [AssetDepreciationController::class, 'getAssetDepreciation'])
        ->name('asset.depreciation.get');
    Route::put('/asset-depreciation/{assetId}', [AssetDepreciationController::class, 'updateAssetDepreciation'])
        ->name('asset-depreciation.update');

    // Asset Mutation routes
    Route::get('/asset-mutations/asset/{id}', [AssetMutationController::class, 'getAssetMutationHistory'])->name('asset-mutations.get');

    //-------------------------------------------------------------------------
    // PROCUREMENT MANAGEMENT
    //-------------------------------------------------------------------------

    // Procurement Routes
    Route::prefix('procurement')->name('procurement.')->group(function () {
        // Request Management
        Route::get('/request', [ProcurementRequestController::class, 'index'])->name('request');
        Route::get('/form-request', function () {
            return view('Procurement.Request.FormRequest');
        })->name('form-request');
         Route::get('/detail-request/{id}', [ProcurementRequestController::class, 'show'])->name('detail-request');

        // Price Comparison
        Route::get('/price-comparison', [ProcurementPriceComparisonController::class, 'index'])->name('price-comparison');
        Route::get('/form-comparison/{id?}', function ($id = null) {
            return view('Procurement.Comparison.FormComparison', ['id' => $id]);
        })->name('form-comparison');
        Route::get('/form-vendor-comparison/{id?}', function ($id = null) {
            return view('Procurement.Comparison.FormComparisonVendor', ['comparison_id' => $id]);
        })->name('form-vendor-comparison');
        Route::get('/detail-comparison/{id}', [ProcurementPriceComparisonController::class, 'show'])->name('detail-comparison');

        // Price Comparison API
        Route::post('/price-comparison', [ProcurementPriceComparisonController::class, 'store'])->name('store-price-comparison');
        Route::get('/price-comparison/data', [ProcurementPriceComparisonController::class, 'index'])->name('price-comparison-data');
        Route::get('/price-comparison/{id}', [ProcurementPriceComparisonController::class, 'show'])->name('show-price-comparison');
        Route::get('/procurement/request', [ProcurementRequestController::class, 'search'])->name('search');
        Route::post('/price-comparison/create-from-detail', [ProcurementPriceComparisonController::class, 'createFromDetail'])->name('create-price-comparison-from-detail');

        // Vendor Offer API
        Route::get('/price-comparison/vendor-offer/{id}', [ProcurementPriceComparisonController::class, 'getVendorOffer'])->name('get-vendor-offer');
        Route::post('/price-comparison/vendor-offer', [ProcurementPriceComparisonController::class, 'createVendorOffer'])->name('create-vendor-offer');
        Route::put('/price-comparison/vendor-offer/{id}', [ProcurementPriceComparisonController::class, 'updateVendorOffer'])->name('update-vendor-offer');
        Route::delete('/price-comparison/vendor-offer/{id}', [ProcurementPriceComparisonController::class, 'deleteVendorOffer'])->name('delete-vendor-offer');

        // Complete Price Comparison
        Route::post('/price-comparison/{id}/complete', [ProcurementPriceComparisonController::class, 'completeComparison'])->name('complete-price-comparison');

        // Purchase Order
        Route::get('/purchase-order', [ProcurementPurchaseOrderController::class, 'index'])->name('purchase-order');
        Route::get('/form-purchase-order/{id?}', function ($id = null) {
            return view('Procurement.PurchaseOrder.FormPurchaseOrder', ['id' => $id]);
        })->name('form-purchase-order');
        Route::get('/detail-purchase-order/{id}', [ProcurementPurchaseOrderController::class, 'show'])->name('detail-purchase-order');
        Route::post('/purchase-order/vendor-offers', [ProcurementPurchaseOrderController::class, 'createFromVendorOffers'])->name('purchase-order.create-from-vendor-offers');
        Route::get('/purchase-order/detail/{id}/export-pdf', [ProcurementPurchaseOrderController::class, 'exportPurchaseOrderDetailPDF'])->name('purchase-order.detail.export-pdf');

        // Receipt
        Route::get('/receipt', [ProcurementReceiptController::class, 'index'])->name('receipt');
        Route::get('/form-receipt/{id?}', function ($id = null) {
            return view('Procurement.Receipt.FormReceipt', ['id' => $id]);
        })->name('form-receipt');
        Route::get('/detail-receipt/{id}', [ProcurementReceiptController::class, 'show'])->name('receipt.show');
        Route::post('/receipt', [ProcurementReceiptController::class, 'create'])->name('receipt.create');
        Route::get('/receipt/detail/{id}/export-pdf', [ProcurementReceiptController::class, 'exportReceiptDetailPDF'])->name('receipt.export-pdf');

        // Inside the procurement route group
        Route::post('/request', [ProcurementRequestController::class, 'store'])->name('store');
        Route::put('/request/{id}', [ProcurementRequestController::class, 'update'])->name('update');
        Route::get('/request/{id}', [ProcurementRequestController::class, 'getOne'])->name('getOne');
        Route::delete('/request/{id}', [ProcurementRequestController::class, 'destroy'])->name('destroy');
        Route::post('/request/{id}/manager-approval', [ProcurementRequestController::class, 'managerApproval'])->name('manager-approval');
        Route::post('/request/{id}/director-approval', [ProcurementRequestController::class, 'directorApproval'])->name('director-approval');
        Route::post('/request/{id}/reject', [ProcurementRequestController::class, 'rejectProcurement'])->name('reject');
    });

    //-------------------------------------------------------------------------
    // REPORTING
    //-------------------------------------------------------------------------

    // Report Routes
    Route::prefix('report')->name('report.')->group(function () {
        Route::get('/complain', [ComplainRepairController::class, 'getAllComplaints'])->name('complain');
        Route::get('/complain/export-pdf', [ComplainRepairController::class, 'exportComplaintPDF'])->name('complain.export.pdf');
        Route::get('/maintenance', function () {
            return view('Report.MaintenanceReport');
        })->name('maintenance');
        Route::get('/finance', [FinanceReportController::class, 'getAllTransactions'])->name('finance');
        Route::get('/finance/export-pdf', [FinanceReportController::class, 'exportFinanceReportPDF'])->name('finance.export.pdf');
        Route::get('/opname', [OpnameReportController::class, 'index'])->name('opname');

        // Add the depreciation report routes inside this group
        Route::get('/depreciation', [DepreciationReportController::class, 'getDepreciationReport'])->name('depreciation');
        Route::get('/depreciation/export-pdf', [DepreciationReportController::class, 'exportDepreciationReportPDF'])->name('depreciation.export-pdf');
    });

    // Asset QR routes
    Route::post('/assets/qr/generate-bulk', [UnitAssetController::class, 'generateBulkQR'])->name('assets.qr.generate-bulk');
    Route::get('/assets/qr/preview', [UnitAssetController::class, 'previewQRCodes'])->name('assets.qr.preview');
    Route::match(['get', 'post'], '/assets/qr/print-pdf', [UnitAssetController::class, 'printQRCodesPDF'])->name('assets.qr.print-pdf');

    // Checkout routes
    Route::post('/assets/checkout', [AssetDetailsController::class, 'checkoutAsset'])->name('asset.checkout');

    // Checkin route
    Route::post('/asset/checkin', [AssetDetailsController::class, 'checkinAsset'])->name('asset.checkin');

    // Report Lost route
    Route::post('/asset/lost', [AssetDetailsController::class, 'reportAssetLost'])->name('asset.lost');

    // Report Found route
    Route::post('/assets/found', [AssetDetailsController::class, 'reportAssetFound'])->name('asset.found');

    // Dispose route
    Route::post('/asset/dispose', [AssetDetailsController::class, 'disposeAsset'])->name('asset.dispose');

    // Asset History routes
    Route::get('/asset-histories/{id}', [AssetHistoryController::class, 'getAssetHistory'])->name('asset-histories.get');

    // Maintenance routes
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance');
    Route::get('/maintenance/{id}', [MaintenanceController::class, 'getMaintenance']);
    Route::get('/maintenance/detail/{id}', [MaintenanceController::class, 'showMaintenanceDetail'])->name('maintenance.detail');
    Route::post('/maintenance', [MaintenanceController::class, 'createMaintenance'])->name('maintenance.create');
    Route::put('/maintenance/{id}', [MaintenanceController::class, 'update']);
    Route::delete('/maintenance/{id}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
    Route::get('/maintenance/export/pdf', [MaintenanceController::class, 'exportMaintenancePDF'])->name('maintenance.export.pdf');
    Route::get('/maintenance/export/pdf/{id}', [MaintenanceController::class, 'exportMaintenanceDetailPDF'])->name('maintenance.export.detail.pdf');
    Route::post('/maintenance-reports', [MaintenanceController::class, 'createMaintenanceReport'])->name('maintenance.reports.create');
    // Calibration routes
    Route::get('/calibrations', [CalibrationController::class, 'index'])->name('calibration');
    Route::post('/calibrations/bulk', [CalibrationController::class, 'createBulkCalibrations'])->name('calibrations.bulk.create');
    Route::get('/calibrations/{id}', [CalibrationController::class, 'getCalibration']);
    Route::put('/calibrations/{id}', [CalibrationController::class, 'update']);
    Route::delete('/calibrations/bulk', [CalibrationController::class, 'destroy'])->name('calibrations.bulk.delete');
    Route::get('/calibrations/export/pdf', [CalibrationController::class, 'exportCalibrationPDF'])->name('calibrations.export.pdf');

    // Asset Finance routes
    Route::get('/asset-transactions/asset/{assetId}', [AssetFinanceController::class, 'getAllTransactions'])->name('asset-transactions.get');
    Route::put('/asset-transactions/{transactionId}', [AssetFinanceController::class, 'updateTransaction'])->name('asset-transactions.update');
    Route::delete('/asset-transactions/{transactionId}', [AssetFinanceController::class, 'deleteTransaction'])->name('asset-transactions.destroy');
    Route::post('/asset-transactions', [AssetFinanceController::class, 'createTransaction'])->name('asset-transactions.store');
    Route::get('/asset-transactions/{transactionId}', [AssetFinanceController::class, 'getTransaction'])->name('asset-transactions.show');

    // Opname report routes
    Route::get('/opnames', [OpnameReportController::class, 'getAllOpnames'])->name('opnames.getAll');
    Route::get('/opname-detail/{id}', [OpnameReportController::class, 'showOpnameDetail'])->name('opnames.detail');
    Route::get('/opname-detail/{id}/export-pdf', [OpnameReportController::class, 'exportOpnameDetailPDF'])->name('opnames.export.pdf');

    // Complaint & Repair Routes
    Route::prefix('complaint-repair')->name('complaint.')->group(function () {
        Route::get('/', [ComplainRepairController::class, 'getAllComplaints'])->name('index');
        Route::get('/detail/{id}', [ComplainRepairController::class, 'showComplaintDetail'])->name('detail');
        Route::get('/export-pdf', [ComplainRepairController::class, 'exportComplaintPDF'])->name('export.pdf');
        Route::get('/detail/{id}/export-pdf', [ComplainRepairController::class, 'exportComplaintDetailPDF'])->name('detail.export.pdf');
        Route::post('/complaints', [ComplainRepairController::class, 'createComplaint'])->name('create');
        Route::delete('/complaints/{id}', [ComplainRepairController::class, 'destroyComplaint'])->name('destroy');
        Route::post('/repairs', [ComplainRepairController::class, 'createRepair'])->name('repair.create');
    });

    // Add this route
    Route::get('/asset/{id}/export-pdf', [AssetDetailsController::class, 'exportAssetDetailPDF'])->name('asset.export-pdf');

    // Add this route in the authenticated routes group
    Route::get('/rooms/{id}', [RoomController::class, 'getById'])->name('rooms.getById');
});

// Edit routes for master assets
Route::get('/asset-master/{id}/edit', [ViewMasterAssetController::class, 'editMasterAsset']);
Route::put('/asset-master/{id}', [ViewMasterAssetController::class, 'updateMasterAsset'])->name('asset-master.update');

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->view('Error.NotFound', [], 404);
});

// New calibration detail route
Route::get('/calibration/detail/{id}', [CalibrationController::class, 'showCalibrationDetail'])->name('calibration.detail');
Route::get('/calibration/detail/{id}/export-pdf', [CalibrationController::class, 'exportCalibrationDetailPDF'])->name('calibration.detail.export.pdf');
