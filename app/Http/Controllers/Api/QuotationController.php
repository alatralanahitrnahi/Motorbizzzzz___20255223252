<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quotation;

class QuotationController extends Controller {
    public function index(Request $request) {
        $quotations = Quotation::where('business_id', $request->user()->business_id)
            ->with(['customer', 'items'])->paginate(20);
        return response()->json($quotations);
    }
    
    public function store(Request $request) {
        $request->validate(['customer_id' => 'required|exists:customers,id', 'quote_date' => 'required|date']);
        $quoteNumber = 'QT-' . date('Y') . '-' . str_pad(
            Quotation::where('business_id', $request->user()->business_id)->count() + 1, 3, '0', STR_PAD_LEFT
        );
        
        $quotation = Quotation::create([
            'business_id' => $request->user()->business_id,
            'customer_id' => $request->customer_id,
            'lead_id' => $request->lead_id,
            'quote_number' => $quoteNumber,
            'quote_date' => $request->quote_date,
            'valid_until' => $request->valid_until,
            'status' => 'draft',
        ]);
        
        if ($request->items) {
            foreach ($request->items as $item) {
                $quotation->items()->create($item);
            }
            $quotation->calculateTotals();
        }
        
        return response()->json(['message' => 'Quotation created', 'quotation' => $quotation->load('items')], 201);
    }
    
    public function show(Request $request, $id) {
        $quotation = Quotation::where('business_id', $request->user()->business_id)
            ->with(['customer', 'items'])->findOrFail($id);
        return response()->json(['quotation' => $quotation]);
    }
    
    public function update(Request $request, $id) {
        $quotation = Quotation::where('business_id', $request->user()->business_id)->findOrFail($id);
        $quotation->update($request->except('items'));
        
        if ($request->items) {
            $quotation->items()->delete();
            foreach ($request->items as $item) {
                $quotation->items()->create($item);
            }
            $quotation->calculateTotals();
        }
        
        return response()->json(['message' => 'Quotation updated', 'quotation' => $quotation->load('items')]);
    }
    
    public function destroy(Request $request, $id) {
        Quotation::where('business_id', $request->user()->business_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Quotation deleted']);
    }
    
    public function convertToOrder(Request $request, $id) {
        $quotation = Quotation::where('business_id', $request->user()->business_id)->findOrFail($id);
        $salesOrder = $quotation->convertToSalesOrder();
        return response()->json(['message' => 'Quotation converted to sales order', 'sales_order' => $salesOrder->load('items')]);
    }
}
