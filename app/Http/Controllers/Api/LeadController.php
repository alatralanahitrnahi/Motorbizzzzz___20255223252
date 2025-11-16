<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;

class LeadController extends Controller {
    public function index(Request $request) {
        $leads = Lead::where('business_id', $request->user()->business_id)
            ->with('assignedUser')->paginate(20);
        return response()->json($leads);
    }
    
    public function store(Request $request) {
        $request->validate(['company_name' => 'required', 'contact_person' => 'required']);
        $lead = Lead::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'status' => 'new',
        ]));
        return response()->json(['message' => 'Lead created', 'lead' => $lead], 201);
    }
    
    public function show(Request $request, $id) {
        $lead = Lead::where('business_id', $request->user()->business_id)
            ->with(['assignedUser', 'quotations'])->findOrFail($id);
        return response()->json(['lead' => $lead]);
    }
    
    public function update(Request $request, $id) {
        $lead = Lead::where('business_id', $request->user()->business_id)->findOrFail($id);
        $lead->update($request->all());
        return response()->json(['message' => 'Lead updated', 'lead' => $lead]);
    }
    
    public function destroy(Request $request, $id) {
        Lead::where('business_id', $request->user()->business_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Lead deleted']);
    }
    
    public function convert(Request $request, $id) {
        $lead = Lead::where('business_id', $request->user()->business_id)->findOrFail($id);
        $customer = $lead->convertToCustomer($request->all());
        return response()->json(['message' => 'Lead converted to customer', 'customer' => $customer]);
    }
}
