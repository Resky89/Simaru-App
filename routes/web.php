<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ViewAssetController;
use App\Http\Controllers\LocationController;
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
use App\Http\Controllers\ProcurementDetailRequestController;
use App\Http\Controllers\AssetFinanceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CalibrationController;
use App\Http\Controllers\OpnameReportController;
use App\Http\Controllers\FinanceReportController;
use App\Http\Controllers\ComplainRepairController;

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

    // Location Management
    Route::prefix('location')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('location');

        // Building API Routes
        Route::post('/buildings/store', [LocationController::class, 'storeBuilding'])->name('buildings.store');
        Route::put('/buildings/update/{id}', [LocationController::class, 'updateBuilding'])->name('buildings.update');
        Route::delete('/buildings/delete/{id}', [LocationController::class, 'destroyBuilding'])->name('buildings.destroy');

        // Room API Routes
        Route::post('/rooms/store', [LocationController::class, 'storeRoom'])->name('rooms.store');
        Route::put('/rooms/update/{id}', [LocationController::class, 'updateRoom'])->name('rooms.update');
        Route::delete('/rooms/delete/{id}', [LocationController::class, 'destroyRoom'])->name('rooms.destroy');
    });

    // Vendor Management
    Route::prefix('vendor')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('vendor');
        Route::post('/store', [VendorController::class, 'store'])->name('vendor.store');
        Route::put('/update/{id}', [VendorController::class, 'update'])->name('vendor.update');
        Route::delete('/delete/{id}', [VendorController::class, 'destroy'])->name('vendor.destroy');
    });

    // User Management
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user');

        // User API Routes
        Route::post('/users/store', [UserController::class, 'storeUser'])->name('users.store');
        Route::put('/users/update/{id}', [UserController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/delete/{id}', [UserController::class, 'destroyUser'])->name('users.destroy');
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
        Route::get('/categories', function () {
            return redirect()->route('categories');
        })->name('categories');
        Route::get('/view', [ViewAssetController::class, 'index'])->name('view');
        Route::get('/detail/{id?}', [AssetDetailsController::class, 'show'])->name('details');
    });

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
    });

    // Brand routes - REPLACING OLD BRAND ROUTES
    Route::prefix('brands')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('brands');
        Route::get('/{id}', [BrandController::class, 'getBrand'])->name('brands.get');
        Route::post('/', [BrandController::class, 'store'])->name('brands.store');
        Route::put('/{id}', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/{id}', [BrandController::class, 'destroy'])->name('brands.destroy');
    });

    // Asset routes
    Route::get('/assets', [ViewAssetController::class, 'index'])->name('assets');
    Route::get('/assets/data', [ViewAssetController::class, 'getAssetData'])->name('assets.data');
    Route::get('/assets/{id}', [ViewAssetController::class, 'getAsset'])->name('assets.get');
    Route::post('/assets', [ViewAssetController::class, 'storeAsset'])->name('assets.store');
    Route::put('/assets/{id}', [ViewAssetController::class, 'updateAsset'])->name('assets.update');
    Route::delete('/assets/{id}', [ViewAssetController::class, 'destroyAsset'])->name('assets.destroy');
    Route::get('assets/barcode/generate/{id}', [ViewAssetController::class, 'generateBarcode'])->name('assets.barcode.generate');

    // Asset Documents routes
    Route::get('/asset-documents/asset/{id}', [AssetDocumentController::class, 'getAssetDocuments'])->name('asset-documents.get');
    Route::post('/asset-documents', [AssetDocumentController::class, 'store'])->name('asset-documents.store');
    Route::delete('/asset-documents/{id}', [AssetDocumentController::class, 'destroy'])->name('asset-documents.destroy');

    // Asset Depreciation route
    Route::get('/asset-depreciation/{assetId}', [AssetDepreciationController::class, 'getAssetDepreciation'])
        ->name('asset.depreciation.get');

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
        Route::get('/detail-request/{id}', [ProcurementDetailRequestController::class, 'show'])->name('detail-request');

        // Price Comparison
        Route::get('/price-comparison', function () {
            return view('Procurement.Comparison.PriceComparison');
        })->name('price-comparison');
        Route::get('/form-comparison/{id?}', function ($id = null) {
            return view('Procurement.Comparison.FormComparison', ['id' => $id]);
        })->name('form-comparison');
        Route::get('/form-vendor-comparison/{id?}', function ($id = null) {
            return view('Procurement.Comparison.FormComparisonVendor', ['id' => $id]);
        })->name('form-vendor-comparison');
        Route::get('/detail-comparison/{id?}', function ($id = null) {
            return view('Procurement.Comparison.DetailComparison', ['id' => $id]);
        })->name('detail-comparison');

        // Purchase Order
        Route::get('/purchase-order', function () {
            return view('Procurement.PurchaseOrder.PurchaseOrder');
        })->name('purchase-order');
        Route::get('/form-purchase-order/{id?}', function ($id = null) {
            return view('Procurement.PurchaseOrder.FormPurchaseOrder', ['id' => $id]);
        })->name('form-purchase-order');
        Route::get('/detail-purchase-order/{id?}', function ($id = null) {
            return view('Procurement.PurchaseOrder.DetailPurchaseOrder', ['id' => $id]);
        })->name('detail-purchase-order');

        // Receipt
        Route::get('/receipt', function () {
            return view('Procurement.Receipt.Receipt');
        })->name('receipt');
        Route::get('/form-receipt/{id?}', function ($id = null) {
            return view('Procurement.Receipt.FormReceipt', ['id' => $id]);
        })->name('form-receipt');
        Route::get('/detail-receipt/{id?}', function ($id = null) {
            return view('Procurement.Receipt.DetailReceipt', ['id' => $id]);
        })->name('detail-receipt');

        // Inside the procurement route group
        Route::post('/procurements', [ProcurementRequestController::class, 'store'])->name('store');
        Route::put('/procurements/{id}', [ProcurementRequestController::class, 'update'])->name('update');
        Route::get('/procurements/{id}', [ProcurementRequestController::class, 'getOne'])->name('getOne');
        Route::delete('/procurements/{id}', [ProcurementRequestController::class, 'destroy'])->name('destroy');
    });

    //-------------------------------------------------------------------------
    // REPORTING
    //-------------------------------------------------------------------------

    // Report Routes
    Route::prefix('report')->name('report.')->group(function () {
        Route::get('/complain', [ComplainRepairController::class, 'getAllComplaints'])->name('complain');
        Route::get('/complain/export-pdf', [ComplainRepairController::class, 'exportComplaintPDF'])->name('complain.export.pdf');
        Route::get('/depreciation', function () {
            return view('Report.DepreciationReport');
        })->name('depreciation');
        Route::get('/calibration', function () {
            return view('Report.CalibrationReport');
        })->name('calibration');
        Route::get('/maintenance', function () {
            return view('Report.MaintenanceReport');
        })->name('maintenance');
        Route::get('/finance', [FinanceReportController::class, 'getAllTransactions'])->name('finance');
        Route::get('/finance/export-pdf', [FinanceReportController::class, 'exportFinanceReportPDF'])->name('finance.export.pdf');
        Route::get('/inspection', function () {
            return view('Report.InspectionReport');
        })->name('inspection');
        Route::get('/opname', [OpnameReportController::class, 'index'])->name('opname');
    });

    // Asset QR routes
    Route::post('/assets/qr/generate-bulk', [ViewAssetController::class, 'generateBulkQR'])->name('assets.qr.generate-bulk');
    Route::get('/assets/qr/preview', [ViewAssetController::class, 'previewQRCodes'])->name('assets.qr.preview');
    Route::match(['get', 'post'], '/assets/qr/print-pdf', [ViewAssetController::class, 'printQRCodesPDF'])->name('assets.qr.print-pdf');

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

    // Calibration route
    Route::get('/calibrations', [CalibrationController::class, 'index'])->name('calibration');
    Route::post('/calibrations/bulk', [CalibrationController::class, 'createBulkCalibrations'])->name('calibrations.bulk.create');
    Route::get('/calibrations/{id}', [CalibrationController::class, 'getCalibration']);
    Route::put('/calibrations/{id}', [CalibrationController::class, 'update']);
    Route::delete('/calibrations/bulk', [CalibrationController::class, 'destroy'])->name('calibrations.bulk.delete');

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
        Route::post('/complaints', [ComplainRepairController::class, 'createComplaint'])->name('create');
    });
});

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->view('Error.NotFound', [], 404);
});
