<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\ProfileController; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\VendorController;
use App\Http\Controllers\MaterialController; 
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\MaterialCheckController;
use App\Http\Controllers\WarehouseBlockController;
use App\Http\Controllers\QualityAnalysisController;
use App\Http\Controllers\LocationController;

/*
|--------------------------------------------------------------------------
| Web Routes - CLEANED VERSION
|--------------------------------------------------------------------------
*/

// Home route
Route::get('/', function () {
    return redirect('/login');
})->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// CSRF Refresh Route
Route::get('/refresh-csrf', function () {
    return response()->json(['token' => csrf_token()]);
})->name('refresh.csrf');

// Protected routes
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/status', [AdminController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
    });
    
    // Materials
    Route::resource('materials', MaterialController::class);
    Route::get('/materials/all', [MaterialController::class, 'getAvailableMaterials']);
    Route::post('/check-material', [MaterialCheckController::class, 'check'])->name('materials.check');
    
    // Vendors
    Route::resource('vendors', VendorController::class);
    Route::get('/vendors/{vendor}/materials', [VendorController::class, 'getMaterials'])->name('vendors.materials');
    
    // Purchase Orders
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
        Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
        Route::get('/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('show');
        Route::get('/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('edit');
        Route::put('/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('update');
        Route::delete('/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('destroy');
        Route::patch('/{purchaseOrder}/status', [PurchaseOrderController::class, 'updateStatus'])->name('update_status');
        Route::post('/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('approve');
        Route::post('/{purchaseOrder}/reject', [PurchaseOrderController::class, 'reject'])->name('reject');
        Route::get('/{purchaseOrder}/generate-pdf', [PurchaseOrderController::class, 'generatePdf'])->name('generate_pdf');
    });
    
    // Inventory
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/items', [InventoryController::class, 'items'])->name('items.index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{inventory}', [InventoryController::class, 'show'])->name('show');
        Route::get('/{inventory}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{inventory}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::post('/generate-batch-number', [InventoryController::class, 'generateBatchNumber'])->name('generate-batch-number');
        Route::get('/po-items/{po_id}', [InventoryController::class, 'getPoItems']);
        Route::get('/get-po-remaining-quantity', [InventoryController::class, 'getRemainingQuantity']);
    });
    
    // Warehouses
    Route::prefix('dashboard/warehouses')->name('dashboard.warehouses.')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('index');
        Route::get('/create', [WarehouseController::class, 'create'])->name('create');
        Route::post('/', [WarehouseController::class, 'store'])->name('store');
        Route::get('/{warehouse}', [WarehouseController::class, 'show'])->name('show');
        Route::get('/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('edit');
        Route::put('/{warehouse}', [WarehouseController::class, 'update'])->name('update');
        Route::delete('/{warehouse}', [WarehouseController::class, 'destroy'])->name('destroy');
        Route::patch('/{warehouse}/toggle-status', [WarehouseController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    // Warehouse Blocks
    Route::prefix('warehouses')->name('warehouses.blocks.')->group(function () {
        Route::get('/blocks/all', [WarehouseBlockController::class, 'all'])->name('all');
        Route::get('/{warehouse}/blocks', [WarehouseBlockController::class, 'index'])->name('index');
        Route::get('/{warehouse}/blocks/create', [WarehouseBlockController::class, 'create'])->name('create');
        Route::post('/{warehouse}/blocks', [WarehouseBlockController::class, 'store'])->name('store');
        Route::get('/{warehouse}/blocks/{block}', [WarehouseBlockController::class, 'show'])->name('show');
        Route::get('/{warehouse}/blocks/{block}/edit', [WarehouseBlockController::class, 'edit'])->name('edit');
        Route::put('/{warehouse}/blocks/{block}', [WarehouseBlockController::class, 'update'])->name('update');
        Route::delete('/{warehouse}/blocks/{block}', [WarehouseBlockController::class, 'destroy'])->name('destroy');
    });
    
    // Barcodes
    Route::prefix('barcode')->name('barcode.')->group(function () {
        Route::get('/', [BarcodeController::class, 'index'])->name('index');
        Route::get('/dashboard', [BarcodeController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [BarcodeController::class, 'create'])->name('create');
        Route::post('/store', [BarcodeController::class, 'store'])->name('store');
        Route::get('/batch-print', [BarcodeController::class, 'batchPrint'])->name('batch-print');
        Route::get('/{barcode}', [BarcodeController::class, 'show'])->name('show');
        Route::get('/{barcode}/edit', [BarcodeController::class, 'edit'])->name('edit');
        Route::put('/{barcode}', [BarcodeController::class, 'update'])->name('update');
        Route::delete('/{barcode}', [BarcodeController::class, 'destroy'])->name('destroy');
        Route::post('/scan', [BarcodeController::class, 'scan'])->name('scan');
    });
    
    // Quality Analysis
    Route::prefix('quality-analysis')->name('quality-analysis.')->group(function () {
        Route::get('/', [QualityAnalysisController::class, 'index'])->name('index');
        Route::get('/create', [QualityAnalysisController::class, 'create'])->name('create');
        Route::post('/', [QualityAnalysisController::class, 'store'])->name('store');
        Route::get('/{id}', [QualityAnalysisController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [QualityAnalysisController::class, 'edit'])->name('edit');
        Route::put('/{id}', [QualityAnalysisController::class, 'update'])->name('update');
        Route::delete('/{id}', [QualityAnalysisController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/approve', [QualityAnalysisController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [QualityAnalysisController::class, 'reject'])->name('reject');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function() {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/export-pdf', [ReportController::class, 'exportPDF'])->name('exportPDF');
    });
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{id}/dismiss', [NotificationController::class, 'dismiss'])->name('dismiss');
        Route::delete('/dismiss-all', [NotificationController::class, 'dismissAll'])->name('dismissAll');
    });
    
    // Location API
    Route::get('/locations/states', [LocationController::class, 'getStates'])->name('locations.states');
    Route::get('/locations/cities/{state_id}', [LocationController::class, 'getCities'])->name('locations.cities');
});

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});