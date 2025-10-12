<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\ProfileController; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\VendorController;
use App\Http\Controllers\MaterialController; 
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationSettingsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\MaterialCheckController;
use App\Http\Controllers\WarehouseBlockController;
use App\Http\Controllers\QualityAnalysisController;
use App\Http\Controllers\LocationController;



// Notification management routes
Route::delete('/notifications/{id}/dismiss', [NotificationController::class, 'dismiss'])->name('notifications.dismiss');
Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
Route::delete('/notifications/dismiss-all', [NotificationController::class, 'dismissAll'])->name('notifications.dismissAll');
Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

// routes/web.php
Route::get('/locations/states', [LocationController::class, 'getStates'])->name('locations.states');
Route::get('/locations/cities/{state_id}', [LocationController::class, 'getCities'])->name('locations.cities');

Route::get('/materials/all', [MaterialController::class, 'getAvailableMaterials']);
Route::get('/api/states', [LocationController::class, 'getStates']);
Route::get('/api/cities/{state_id}', [LocationController::class, 'getCities']);
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
// routes/web.php
Route::get('/inventory/po-items/{po_id}', [InventoryController::class, 'getPoItems']);

Route::get('/get-po-remaining-quantity', [InventoryController::class, 'getRemainingQuantity']);


// Test route for debugging
Route::get('/test-error', function () {
    abort(500, 'Testing error logging');
});

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

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['role:admin'])->prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/users', [AdminController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [AdminController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/users-list', [AdminController::class, 'index'])->name('users');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    });

    // Module permission-based access
    Route::middleware(['module.permission:dashboard.users.index,view'])->group(function () {
        Route::get('/dashboard/users', [AdminController::class, 'index'])->name('dashboard.users.index');
    });

    Route::middleware(['module.permission:dashboard.users.index,edit'])->group(function () {
        Route::post('/dashboard/users', [AdminController::class, 'store'])->name('dashboard.users.store');
        Route::put('/dashboard/users/{user}', [AdminController::class, 'update'])->name('dashboard.users.update');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Admin routes - only for admin users (separate from dashboard)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
});

// Routes for inventory management (protected by auth middleware)
Route::middleware(['auth', 'role:admin,inventory_manager'])
    ->prefix('inventory')
    ->name('inventory.')
    ->group(function () {

        // Main inventory CRUD
    // Inventory landing page:
Route::get('/', [InventoryController::class, 'index'])->name('index');

// Inventory items:
Route::get('/items', [InventoryController::class, 'items'])->name('items.index');


        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{inventory}', [InventoryController::class, 'show'])->name('show');
        Route::get('/{inventory}/edit', [InventoryController::class, 'edit'])->name('edit');
      //  Route::put('/{inventory}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::post('/generate-batch-number', [InventoryController::class, 'generateBatchNumber'])->name('generate-batch-number');

        // Sub routes
        Route::get('/categories', [InventoryController::class, 'categories'])->name('categories');
        Route::get('/stock', [InventoryController::class, 'stock'])->name('stock');
        Route::get('/low-stock', [InventoryController::class, 'low-stock'])->name('low-stock');

        // Dispatch routes
        Route::post('/dispatch', [InventoryController::class, 'dispatch'])->name('dispatch');
        Route::get('/dispatch/{id}', [InventoryController::class, 'showDispatch'])->name('showDispatch');

        // Damage routes
        Route::post('/damage/mark', [InventoryController::class, 'markDamaged'])->name('markDamaged');
        Route::post('/damage', [InventoryController::class, 'storeDamage'])->name('storeDamage');
      Route::post('/damage', [InventoryController::class, 'storeDamage'])->name('damage');


        // Inventory items
    });


Route::delete('/dashboard/users/{user}', [AdminController::class, 'destroy'])->name('dashboard.users.destroy');


// Purchase Orders routes - for admin and purchase team
Route::middleware(['auth', 'role:admin,purchase_team'])
    ->prefix('purchase-orders')
    ->name('purchase-orders.')
    ->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
        Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
        Route::get('/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('show');
        Route::get('/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('edit');
        Route::put('/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('update');
        Route::delete('/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('destroy');
        Route::patch('/{purchaseOrder}/status', [PurchaseOrderController::class, 'updateStatus'])->name('update_status');
        Route::get('/{purchaseOrder}/generate-pdf', [PurchaseOrderController::class, 'generatePdf'])->name('generate_pdf');
    });
Route::get('/purchase-orders/{id}/generate-pdf', [PurchaseOrderController::class, 'generatePdf'])
    ->name('purchase_orders.generate_pdf');


// Vendors resource - protected routes
Route::middleware(['auth'])->group(function () {
    Route::resource('vendors', VendorController::class);
});

// Materials resource - protected routes
Route::middleware(['auth'])->group(function () {
    Route::resource('materials', MaterialController::class);
});

// Barcode routes - protected routes
Route::middleware(['auth'])->prefix('barcode')->name('barcode.')->group(function () {
    Route::get('/', [BarcodeController::class, 'index'])->name('index');
    Route::get('/create', [BarcodeController::class, 'create'])->name('create');
    Route::post('/store', [BarcodeController::class, 'store'])->name('store');
    Route::get('/dashboard', [BarcodeController::class, 'dashboard'])->name('dashboard');
    Route::get('/generate', [BarcodeController::class, 'generate'])->name('generate');
    Route::get('/batch-print', [BarcodeController::class, 'batchPrint'])->name('batch-print');
    Route::get('/generate-barcode/{number}', [BarcodeController::class, 'generateBarcode'])->name('generate-barcode');
    Route::get('/generate-qr/{data}', [BarcodeController::class, 'generateQR'])->name('generate-qr');
    Route::get('/image/{number}', [BarcodeController::class, 'generateBarcode'])->name('image');
    Route::get('/{barcode}', [BarcodeController::class, 'show'])->name('show');
    Route::get('/{barcode}/edit', [BarcodeController::class, 'edit'])->name('edit');
    Route::put('/{barcode}', [BarcodeController::class, 'update'])->name('update');
    Route::delete('/{id}', [BarcodeController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-action', [BarcodeController::class, 'bulkAction'])->name('bulk-action');
  //  Route::post('/scan', [BarcodeController::class, 'scan'])->name('scan');
});

Route::post('/barcode/scan', [BarcodeController::class, 'scan'])->name('barcode.scan');

// Email Verification routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    
    Route::get('/email/verify/{id}/{hash}', function () {
        // Handle email verification here
    })->middleware(['signed'])->name('verification.verify');
    
    Route::post('/email/verification-notification', function () {
        // Resend verification email here
    })->middleware(['throttle:6,1'])->name('verification.resend');
});

// Utility routes - accessible to authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications');
    
    Route::get('/help', function () {
        return view('help.index');
    })->name('help');
});


Route::prefix('reports')->name('reports.')->group(function() {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('exportExcel');
    Route::get('/export-pdf', [ReportController::class, 'exportPDF'])->name('exportPDF');
});


// Fallback route for 404 errors - This should be at the very end
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});



Route::group(['prefix' => 'barcode', 'middleware' => 'auth'], function () {
    // Enhanced printing
    Route::get('/print-advanced', [BarcodeController::class, 'batchPrintAdvanced'])->name('barcode.print-advanced');
    
    // Analytics
    Route::get('/analytics', [BarcodeController::class, 'analytics'])->name('barcode.analytics');
    
    // Quick search
    Route::get('/search', [BarcodeController::class, 'quickSearch'])->name('barcode.search');
    
    // API scanning
    Route::post('/api/scan', [BarcodeController::class, 'apiScan'])->name('barcode.api-scan');
});

Route::get('/transactions/{id}', [ReportController::class, 'show'])->name('reports.show');


// Approval routes
Route::post('/purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])
     ->name('purchase-orders.approve')
     ->middleware(['auth', 'can:approve,purchaseOrder']);


Route::post('/purchase-orders/{purchaseOrder}/reject', [PurchaseOrderController::class, 'reject'])
     ->name('purchase-orders.reject') 
     ->middleware('can:approve,purchaseOrder');

// Notification routes
 Route::get('/notifications', [NotificationController::class, 'index'])
     ->name('notifications.index');

Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
     ->name('notifications.read');

Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
     ->name('notifications.read-all');

Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])
     ->name('notifications.unread-count');

Route::post('/notifications/{id}/mark-as-read', function ($id) {
    $notification = Auth::user()->notifications()->findOrFail($id);
    $notification->markAsRead();
    return redirect()->back()->with('success', 'Notification marked as read.');
})->name('notifications.markAsRead');

Route::post('/notifications/cleanup', [NotificationController::class, 'manualCleanup'])
    ->name('notifications.cleanup');


Route::prefix('inventory')->name('inventory.')->group(function () {

    // Create inventory batch
    Route::get('/create', [InventoryController::class, 'create'])->name('create');

    // Receive inventory stock
    Route::get('/{material}/receive', [InventoryController::class, 'receive'])->name('receive');

    // History page (ADD THIS)
    Route::get('/{material}/history', [InventoryController::class, 'history'])->name('history');

    // Adjust stock (ADD THIS)
    Route::get('/{material}/adjust', [InventoryController::class, 'adjust'])->name('adjust');
});

// Report route (ADD THIS)
Route::get('/reports/material/{material}', [ReportController::class, 'materialReport'])->name('reports.material');

// purchase -order
Route::get('/po-details', [PurchaseOrderController::class, 'getPoMaterialDetails']);
Route::get('/purchase-order/{id}/items', [PurchaseOrderController::class, 'getItems']);
Route::get('/purchase-orders/{id}/items', [PurchaseOrderController::class, 'items']);

//inventory
Route::get('inventory/create', [InventoryController::class, 'create'])->name('inventory.create');

Route::match(['put', 'patch'], '/inventory/{inventory}', [InventoryController::class, 'update'])
     ->name('inventory.update');


//check material availble
Route::middleware(['auth'])->group(function () {
    Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
});

Route::patch('/dashboard/users/{user}/status', [AdminController::class, 'toggleStatus'])
    ->middleware('auth')
    ->name('users.toggleStatus');


// Option 1: Apply to specific routes
Route::middleware(['auth', 'check.material.availability'])->group(function () {
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::put('/purchase-orders/{id}', [PurchaseOrderController::class, 'update'])->name('purchase-orders.update');
});

// Option 2: Apply to entire purchase order resource (except index and show)
Route::resource('purchase-orders', PurchaseOrderController::class)
    ->middleware(['auth'])
    ->except(['store', 'update']);

Route::resource('purchase-orders', PurchaseOrderController::class)
    ->middleware(['auth', 'check.material.availability'])
    ->only(['store', 'update']);


Route::prefix('dashboard/warehouses')->name('dashboard.warehouses.')->middleware(['auth'])->group(function () {
    Route::get('/', [WarehouseController::class, 'index'])
         ->name('index')
         ->middleware('can:viewAny,App\Models\Warehouse');
    
    Route::get('/create', [WarehouseController::class, 'create'])
         ->name('create')
         ->middleware('can:create,App\Models\Warehouse');
    
    Route::post('/', [WarehouseController::class, 'store'])
         ->name('store')
         ->middleware('can:create,App\Models\Warehouse');
    
    Route::get('/{warehouse}', [WarehouseController::class, 'show'])
         ->name('show')
         ->middleware('can:view,warehouse');
    
    Route::get('/{warehouse}/edit', [WarehouseController::class, 'edit'])
         ->name('edit')
         ->middleware('can:update,warehouse');
    
    Route::put('/{warehouse}', [WarehouseController::class, 'update'])
         ->name('update')
         ->middleware('can:update,warehouse');
    
    Route::delete('/{warehouse}', [WarehouseController::class, 'destroy'])
         ->name('destroy')
         ->middleware('can:delete,warehouse');
});

Route::delete('/dashboard/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])
    ->name('dashboard.warehouses.destroy');

Route::prefix('dashboard/warehouses')->name('dashboard.warehouses.')->middleware(['auth'])->group(function () {
    Route::patch('/{warehouse}/toggle-status', [WarehouseController::class, 'toggleStatus'])->name('toggle-status');

    Route::get('/{warehouse}/assign-staff', [WarehouseController::class, 'assignStaff'])->name('assign-staff');
    Route::post('/{warehouse}/assign-staff', [WarehouseController::class, 'storeStaffAssignment'])->name('store-staff');
    Route::delete('/{warehouse}/remove-staff/{user}', [WarehouseController::class, 'removeStaffAssignment'])->name('remove-staff');
    Route::patch('/{warehouse}/update-role/{user}', [WarehouseController::class, 'updateStaffRole'])->name('update-role');
});


  Route::post('dashboard/warehouses/{id}/blocks', [WarehouseController::class, 'addBlock'])->name('dashboard.warehouses.addBlock');

// GROUPED route (clean and RESTful)
Route::prefix('dashboard/warehouses/{warehouse}')->group(function () {
    Route::get('blocks', [WarehouseBlockController::class, 'index'])->name('warehouses.blocks.index');
    Route::post('blocks', [WarehouseBlockController::class, 'store'])->name('warehouses.blocks.store');
      Route::get('blocks/create', [WarehouseBlockController::class, 'create'])->name('warehouses.blocks.create'); // 👈 Add this

});
Route::get('dashboard/blocks', [WarehouseBlockController::class, 'all'])->name('warehouses.blocks.all');


// Add these routes to your existing warehouse block routes
Route::get('warehouses/{warehouse}/blocks/{block}', [WarehouseBlockController::class, 'show'])
    ->name('warehouses.blocks.show');

Route::get('warehouses/{warehouse}/blocks/{block}/slots/{slot}', [WarehouseBlockController::class, 'getSlotDetails'])
    ->name('warehouses.blocks.slots.details');


// API routes for barcode functionality
Route::prefix('api/materials')->group(function () {
    // Get material by barcode scan
    Route::post('/barcode', [MaterialController::class, 'getByBarcode']);
    
    // Search materials
    Route::get('/search', [MaterialController::class, 'search']);
    
    // Regenerate barcode for specific material
    Route::patch('/{material}/regenerate-barcode', [MaterialController::class, 'regenerateBarcode']);
    
    // Get available materials (updated to include SKU and barcode)
    Route::get('/available', [MaterialController::class, 'getAvailableMaterials']);
});

// Web routes (if you want web interface for barcode scanning)
Route::prefix('materials')->group(function () {
    Route::get('/scan', function () {
        return view('materials.scan');
    })->name('materials.scan');
    
    Route::get('/barcode/{material}', function (Material $material) {
        return view('materials.barcode', compact('material'));
    })->name('materials.barcode');
});

// Standard resource routes (existing)
Route::resource('materials', MaterialController::class);

Route::post('/check-material', [MaterialCheckController::class, 'check'])->name('materials.check');
Route::post('/check-material', [MaterialCheckController::class, 'check'])->middleware('auth');

Route::prefix('admin')->group(function () {
    Route::get('/material-requests/{id}', [MaterialCheckController::class, 'show']);
    Route::post('/material-requests/{id}/dismiss', [MaterialCheckController::class, 'dismiss']);
    Route::post('/material-requests/dismiss-all', [MaterialCheckController::class, 'dismissAll']);
    Route::get('/pending-materials-count', [MaterialCheckController::class, 'getCount']);

  Route::get('/material-requests', [MaterialCheckController::class, 'index'])
        ->name('admin.material-requests');
});

Route::put('/api/material-row/{id}', [MaterialController::class, 'update']);
Route::get('/api/vendor-material-price', [MaterialController::class, 'getUnitPrice']);


Route::middleware('auth')->group(function () {
    Route::get('/vendors/{vendor}/materials', [VendorController::class, 'materials'])->name('vendors.materials');

});
// web.php or api.php
Route::get('/vendors/{vendor}/materials', [VendorController::class, 'getMaterials']);

Route::get('/api/vendors/{vendor}/materials', [VendorController::class, 'getMaterials'])
    ->name('api.vendors.materials');

// Route for fetching vendor materials
Route::get('/vendors/{vendorId}/materials', [PurchaseOrderController::class, 'materials'])
    ->name('vendors.materials');

// Alternative: If you want to keep it within purchase order routes
Route::prefix('purchase-orders')->group(function () {
    Route::get('/vendors/{vendorId}/materials', [PurchaseOrderController::class, 'materials'])
        ->name('purchase-orders.vendor-materials');
});

// In routes/web.php
Route::get('/vendors/{vendor}/materials', function($vendorId) {
    try {
        // Adjust this query based on your database structure
        $materials = DB::table('vendor_materials')
            ->join('materials', 'vendor_materials.material_id', '=', 'materials.id')
            ->where('vendor_materials.vendor_id', $vendorId)
            ->select('materials.id', 'materials.name', 'vendor_materials.unit_price', 'materials.gst_rate')
            ->get();
        
        return response()->json($materials);
    } catch (Exception $e) {
        return response()->json(['error' => 'Failed to fetch materials'], 500);
    }
})->name('vendors.materials');

Route::post('/warehouses/{warehouse}/blocks', [WarehouseBlockController::class, 'store'])->name('warehouses.blocks.store');
// Block Routes (within warehouse)
Route::get('/warehouses/{warehouse}/blocks', [WarehouseBlockController::class, 'index'])->name('warehouses.blocks.index');
Route::get('/warehouses/{warehouse}/blocks/{block}/edit', [WarehouseBlockController::class, 'edit'])->name('warehouses.blocks.edit');
Route::put('/warehouses/{warehouse}/blocks/{block}', [WarehouseBlockController::class, 'update'])->name('warehouses.blocks.update');
Route::delete('/warehouses/{warehouse}/blocks/{block}', [WarehouseBlockController::class, 'destroy'])->name('warehouses.blocks.destroy');

Route::get('/slot-details/{id}', [WarehouseBlockController::class, 'getSlotDetails']);


Route::middleware(['auth'])->group(function () {
    
    // Quality Analysis Routes
    Route::prefix('quality-analysis')->name('quality-analysis.')->group(function () {
        
        // Main CRUD routes
        Route::get('/', [QualityAnalysisController::class, 'index'])->name('index');
        Route::get('/create', [QualityAnalysisController::class, 'create'])->name('create');
        Route::post('/', [QualityAnalysisController::class, 'store'])->name('store');
        Route::get('/{id}', [QualityAnalysisController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [QualityAnalysisController::class, 'edit'])->name('edit');
        Route::put('/{id}', [QualityAnalysisController::class, 'update'])->name('update');
        Route::delete('/{id}', [QualityAnalysisController::class, 'destroy'])->name('destroy');
        
        // Quality Actions
        Route::post('/{id}/approve', [QualityAnalysisController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [QualityAnalysisController::class, 'reject'])->name('reject');
        
        // Bulk Actions
        Route::post('/bulk-approve', [QualityAnalysisController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/generate-barcodes', [QualityAnalysisController::class, 'generateBarcodes'])->name('generate-barcodes');
        Route::post('/print-barcodes', [QualityAnalysisController::class, 'printBarcodes'])->name('print-barcodes');
        
        // AJAX Routes
        Route::get('/purchase-order-items/{purchaseOrderId}', [QualityAnalysisController::class, 'getPurchaseOrderItems'])->name('purchase-order-items');
        
    });
    
});

// In your routes/web.php file (for debugging only)
Route::get('/debug-permissions', [DashboardController::class, 'debugPermissions'])->middleware('auth');

Route::post('/admin/material-requests/{id}/dismiss', [MaterialCheckController::class, 'dismiss'])->name('material-requests.dismiss');

Route::get('/api/materials/search', [MaterialController::class, 'search']);


Route::post('quality-analysis/bulk-reject', [QualityAnalysisController::class, 'bulkReject'])->name('quality_analysis.bulk-reject');

Route::get('quality-analysis/export', [QualityAnalysisController::class, 'export'])->name('quality-analysis.export');