<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplianceRequirement;

class ComplianceRequirementController extends Controller
{
    public function index(Request $request)
    {
        $complianceRequirements = ComplianceRequirement::where('business_id', $request->user()->business_id)
            ->when($request->search, function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->when($request->category, function($q) use ($request) {
                $q->where('category', $request->category);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->expiring_soon, function($q) {
                $q->where('expiry_date', '>=', now())
                  ->where('expiry_date', '<=', now()->addDays(30));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return response()->json($complianceRequirements);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'authority' => 'nullable|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'status' => 'nullable|string|in:active,inactive,archived',
            'priority' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string'
        ]);
        
        $complianceRequirement = ComplianceRequirement::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'status' => $request->status ?? 'active',
            'priority' => $request->priority ?? 1
        ]));
        
        return response()->json(['message' => 'Compliance requirement created', 'compliance_requirement' => $complianceRequirement], 201);
    }
    
    public function show(Request $request, $id)
    {
        $complianceRequirement = ComplianceRequirement::where('business_id', $request->user()->business_id)
            ->with(['documents', 'auditFindings', 'responsibleUsers'])
            ->findOrFail($id);
            
        return response()->json(['compliance_requirement' => $complianceRequirement]);
    }
    
    public function update(Request $request, $id)
    {
        $complianceRequirement = ComplianceRequirement::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'authority' => 'nullable|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'status' => 'nullable|string|in:active,inactive,archived',
            'priority' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string'
        ]);
        
        $complianceRequirement->update($request->all());
        
        return response()->json(['message' => 'Compliance requirement updated', 'compliance_requirement' => $complianceRequirement]);
    }
    
    public function destroy(Request $request, $id)
    {
        $complianceRequirement = ComplianceRequirement::where('business_id', $request->user()->business_id)->findOrFail($id);
        $complianceRequirement->delete();
        
        return response()->json(['message' => 'Compliance requirement deleted']);
    }
}