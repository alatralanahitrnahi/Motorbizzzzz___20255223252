<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkOrder;

class WorkOrderController extends Controller {
    public function index(Request $request) {
        $workOrders = WorkOrder::where('business_id', $request->user()->business_id)
            ->with(['product', 'salesOrder', 'machine', 'assignedUser', 'operations'])
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($workOrders);
    }
    
    public function store(Request $request) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_planned' => 'required|numeric|min:0',
        ]);
        
        $woNumber = 'WO-' . date('Y') . '-' . str_pad(
            WorkOrder::where('business_id', $request->user()->business_id)->count() + 1, 
            4, '0', STR_PAD_LEFT
        );
        
        $workOrder = WorkOrder::create([
            'business_id' => $request->user()->business_id,
            'sales_order_id' => $request->sales_order_id,
            'work_order_number' => $woNumber,
            'product_id' => $request->product_id,
            'quantity_planned' => $request->quantity_planned,
            'machine_id' => $request->machine_id,
            'assigned_to' => $request->assigned_to,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'draft',
            'priority' => $request->priority ?? 'medium',
            'notes' => $request->notes,
        ]);
        
        return response()->json(['message' => 'Work order created', 'work_order' => $workOrder->load('product')], 201);
    }
    
    public function show(Request $request, $id) {
        $workOrder = WorkOrder::where('business_id', $request->user()->business_id)
            ->with([
                'product.activeBom.items.material', 
                'salesOrder', 
                'machine', 
                'assignedUser', 
                'operations', 
                'materialConsumptions.material'
            ])->findOrFail($id);
        
        return response()->json(['work_order' => $workOrder]);
    }
    
    public function update(Request $request, $id) {
        $workOrder = WorkOrder::where('business_id', $request->user()->business_id)->findOrFail($id);
        $workOrder->update($request->except(['start', 'complete', 'consume_material']));
        return response()->json(['message' => 'Work order updated', 'work_order' => $workOrder]);
    }
    
    public function destroy(Request $request, $id) {
        WorkOrder::where('business_id', $request->user()->business_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Work order deleted']);
    }
    
    public function start(Request $request, $id) {
        $workOrder = WorkOrder::where('business_id', $request->user()->business_id)->findOrFail($id);
        $workOrder->start();
        return response()->json(['message' => 'Work order started', 'work_order' => $workOrder->load('materialConsumptions')]);
    }
    
    public function complete(Request $request, $id) {
        $request->validate([
            'quantity_produced' => 'required|numeric|min:0',
            'quantity_rejected' => 'nullable|numeric|min:0',
        ]);
        
        $workOrder = WorkOrder::where('business_id', $request->user()->business_id)->findOrFail($id);
        $workOrder->complete($request->quantity_produced, $request->quantity_rejected ?? 0);
        
        return response()->json([
            'message' => 'Work order completed', 
            'work_order' => $workOrder,
            'yield_percentage' => $workOrder->yield_percentage,
        ]);
    }
    
    public function consumeMaterial(Request $request, $id) {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'actual_quantity' => 'required|numeric|min:0',
            'wastage_quantity' => 'nullable|numeric|min:0',
        ]);
        
        $workOrder = WorkOrder::where('business_id', $request->user()->business_id)->findOrFail($id);
        $consumption = $workOrder->consumeMaterial(
            $request->material_id, 
            $request->actual_quantity, 
            $request->wastage_quantity ?? 0
        );
        
        return response()->json(['message' => 'Material consumed', 'consumption' => $consumption]);
    }
}
