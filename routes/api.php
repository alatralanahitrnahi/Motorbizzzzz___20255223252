<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\MachineController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\QuotationController;
use App\Http\Controllers\Api\SalesOrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BomController;
use App\Http\Controllers\Api\WorkOrderController;
use App\Http\Controllers\Api\InventoryLocationController;
use App\Models\PurchaseOrder;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('materials', MaterialController::class);
    Route::apiResource('vendors', VendorController::class);
    Route::apiResource('machines', MachineController::class);
    
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('leads', LeadController::class);
    Route::post('leads/{id}/convert', [LeadController::class, 'convert']);
    
    Route::apiResource('quotations', QuotationController::class);
    Route::post('quotations/{id}/convert-to-order', [QuotationController::class, 'convertToOrder']);
    
    Route::apiResource('sales-orders', SalesOrderController::class);
    
    Route::apiResource('products', ProductController::class);
    Route::apiResource('boms', BomController::class);
    
    Route::apiResource('work-orders', WorkOrderController::class);
    Route::post('work-orders/{id}/start', [WorkOrderController::class, 'start']);
    Route::post('work-orders/{id}/complete', [WorkOrderController::class, 'complete']);
    Route::post('work-orders/{id}/consume-material', [WorkOrderController::class, 'consumeMaterial']);
    
    Route::apiResource('inventory-locations', InventoryLocationController::class);

    Route::get('/notifications', [NotificationController::class, 'apiIndex']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);

    Route::get('/purchase-orders/{poId}/items/{materialId}', [PurchaseOrderController::class, 'getItemQuantity']);
    
    Route::get('/purchase-orders/{purchaseOrder}/items', [PurchaseOrderController::class, 'items'])
        ->name('purchase-orders.items');

    Route::post('/inventory/generate-batch-number', [InventoryController::class, 'generateBatchNumber']);
});
