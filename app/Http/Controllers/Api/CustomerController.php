<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller {
    public function index(Request $request) {
        $customers = Customer::where('business_id', $request->user()->business_id)
            ->when($request->search, function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            })->paginate(20);
        return response()->json($customers);
    }
    
    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255', 'email' => 'nullable|email']);
        $customer = Customer::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'customer_code' => 'CUST-' . strtoupper(substr(uniqid(), -6)),
            'status' => 'active',
        ]));
        return response()->json(['message' => 'Customer created', 'customer' => $customer], 201);
    }
    
    public function show(Request $request, $id) {
        $customer = Customer::where('business_id', $request->user()->business_id)
            ->with(['quotations', 'salesOrders'])->findOrFail($id);
        return response()->json(['customer' => $customer]);
    }
    
    public function update(Request $request, $id) {
        $customer = Customer::where('business_id', $request->user()->business_id)->findOrFail($id);
        $customer->update($request->all());
        return response()->json(['message' => 'Customer updated', 'customer' => $customer]);
    }
    
    public function destroy(Request $request, $id) {
        Customer::where('business_id', $request->user()->business_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Customer deleted']);
    }
}
