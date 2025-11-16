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
// QMS Controllers
use App\Http\Controllers\Api\QualityStandardController;
use App\Http\Controllers\Api\QualityChecklistController;
use App\Http\Controllers\Api\QualityChecklistItemController;
use App\Http\Controllers\Api\QualityInspectionController;
use App\Http\Controllers\Api\QualityInspectionResultController;
// HR Controllers
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\JobPositionController;
use App\Http\Controllers\Api\TrainingProgramController;
use App\Http\Controllers\Api\TrainingMaterialController;
use App\Http\Controllers\Api\EmployeeTrainingController;
use App\Http\Controllers\Api\SkillAssessmentController;
// Compliance & Risk Management Controllers
use App\Http\Controllers\Api\ComplianceRequirementController;
use App\Http\Controllers\Api\ComplianceDocumentController;
use App\Http\Controllers\Api\ComplianceAuditController;
use App\Http\Controllers\Api\ComplianceAuditFindingController;
use App\Http\Controllers\Api\CertificateLicenseController;
use App\Http\Controllers\Api\RiskCategoryController;
use App\Http\Controllers\Api\RiskController;
use App\Http\Controllers\Api\RiskImpactAssessmentController;
use App\Http\Controllers\Api\RiskMitigationStrategyController;
use App\Http\Controllers\Api\RiskIncidentController;
use App\Http\Controllers\Api\BusinessContinuityPlanController;
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

    // QMS Routes
    Route::apiResource('quality-standards', QualityStandardController::class);
    Route::apiResource('quality-checklists', QualityChecklistController::class);
    Route::apiResource('quality-checklist-items', QualityChecklistItemController::class);
    Route::apiResource('quality-inspections', QualityInspectionController::class);
    Route::post('quality-inspections/{id}/complete', [QualityInspectionController::class, 'completeInspection']);
    Route::apiResource('quality-inspection-results', QualityInspectionResultController::class);

    // HR Routes
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('job-positions', JobPositionController::class);
    Route::apiResource('training-programs', TrainingProgramController::class);
    Route::apiResource('training-materials', TrainingMaterialController::class);
    Route::apiResource('employee-trainings', EmployeeTrainingController::class);
    Route::post('employee-trainings/{id}/complete', [EmployeeTrainingController::class, 'completeTraining']);
    Route::apiResource('skill-assessments', SkillAssessmentController::class);

    // Compliance Management Routes
    Route::apiResource('compliance-requirements', ComplianceRequirementController::class);
    Route::apiResource('compliance-documents', ComplianceDocumentController::class);
    Route::apiResource('compliance-audits', ComplianceAuditController::class);
    Route::apiResource('compliance-audit-findings', ComplianceAuditFindingController::class);
    Route::apiResource('certificates-licenses', CertificateLicenseController::class);

    // Risk Management Routes
    Route::apiResource('risk-categories', RiskCategoryController::class);
    Route::apiResource('risks', RiskController::class);
    Route::apiResource('risk-impact-assessments', RiskImpactAssessmentController::class);
    Route::apiResource('risk-mitigation-strategies', RiskMitigationStrategyController::class);
    Route::apiResource('risk-incidents', RiskIncidentController::class);
    Route::apiResource('business-continuity-plans', BusinessContinuityPlanController::class);

    Route::get('/notifications', [NotificationController::class, 'apiIndex']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);

    Route::get('/purchase-orders/{poId}/items/{materialId}', [PurchaseOrderController::class, 'getItemQuantity']);
    
    Route::get('/purchase-orders/{purchaseOrder}/items', [PurchaseOrderController::class, 'items'])
        ->name('purchase-orders.items');

    Route::post('/inventory/generate-batch-number', [InventoryController::class, 'generateBatchNumber']);
});