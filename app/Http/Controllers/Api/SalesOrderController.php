<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesOrder;

class SalesOrderController extends Controller {
    public function index(Request $request) {
        $orders = SalesOrder::where('business_id', $request->user()->business_id)
            ->with(['customer', 'items'])->paginate(20);
        return response()->json($orders);
    }
    
    public function store(Request $request) {
        $request->validate(['customer_id' => 'required|exists:customers,id', 'order_date' => 'required|date']);
        $orderNumber = 'SO-' . date('Y') . '-' . str_pad(
            SalesOrder::where('business_id', $request->user()->business_id)->count() + 1, 3, '0', STR_PAD_LEFT
        );
        
        $order = SalesOrder::create([
            'business_id' => $request->user()->business_id,
            'customer_id' => $request->customer_id,
            'quotation_id' => $request->quotation_id,
            'order_number' => $orderNumber,
            'order_date' => $request->order_date,
            'delivery_date' => $request->delivery_date,
            'status' => 'pending',
            'priority' => $request->priority ?? 'medium',
        ]);
        
        if ($request->items) {
            foreach ($request->items as $item) {
                $order->items()->create($item);
            }
        }
        
        return response()->json(['message' => 'Sales order created', 'sales_order' => $order->load('items')], 201);
    }
    
    public function show(Request $request, $id) {
        $order = SalesOrder::where('business_id', $request->user()->business_id)
            ->with(['customer', 'items', 'workOrders'])->findOrFail($id);
        return response()->json(['sales_order' => $order]);
    }
    
    public function update(Request $request, $id) {
        $order = SalesOrder::where('business_id', $request->user()->business_id)->findOrFail($id);
        $order->update($request->except('items'));
        return response()->json(['message' => 'Sales order updated', 'sales_order' => $order->load('items')]);
    }
    
    public function destroy(Request $request, $id) {
        SalesOrder::where('business_id', $request->user()->business_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Sales order deleted']);
    }
}
