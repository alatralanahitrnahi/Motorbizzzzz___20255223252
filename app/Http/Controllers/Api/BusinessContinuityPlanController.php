<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessContinuityPlan;

class BusinessContinuityPlanController extends Controller
{
    public function index(Request $request)
    {
        $businessContinuityPlans = BusinessContinuityPlan::where('business_id', $request->user()->business_id)
            ->with(['owner'])
            ->when($request->search, function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->due_for_testing, function($q) {
                $q->where('next_test_date', '<=', now()->addDays(30))
                  ->orWhere('next_test_date', '<=', now());
            })
            ->orderBy('next_test_date', 'asc')
            ->paginate(20);
            
        return response()->json($businessContinuityPlans);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scope' => 'nullable|string',
            'objectives' => 'nullable|string',
            'owner_id' => 'nullable|exists:users,id',
            'critical_functions' => 'nullable|string',
            'recovery_strategies' => 'nullable|string',
            'resource_requirements' => 'nullable|string',
            'contact_information' => 'nullable|string',
            'communication_plan' => 'nullable|string',
            'last_tested_date' => 'nullable|date',
            'next_test_date' => 'nullable|date|after:last_tested_date',
            'status' => 'nullable|string|in:active,inactive,testing,review_required',
            'notes' => 'nullable|string'
        ]);
        
        $businessContinuityPlan = BusinessContinuityPlan::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'status' => $request->status ?? 'active'
        ]));
        
        return response()->json(['message' => 'Business continuity plan created', 'business_continuity_plan' => $businessContinuityPlan], 201);
    }
    
    public function show(Request $request, $id)
    {
        $businessContinuityPlan = BusinessContinuityPlan::where('business_id', $request->user()->business_id)
            ->with(['owner'])
            ->findOrFail($id);
            
        return response()->json(['business_continuity_plan' => $businessContinuityPlan]);
    }
    
    public function update(Request $request, $id)
    {
        $businessContinuityPlan = BusinessContinuityPlan::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'scope' => 'nullable|string',
            'objectives' => 'nullable|string',
            'owner_id' => 'nullable|exists:users,id',
            'critical_functions' => 'nullable|string',
            'recovery_strategies' => 'nullable|string',
            'resource_requirements' => 'nullable|string',
            'contact_information' => 'nullable|string',
            'communication_plan' => 'nullable|string',
            'last_tested_date' => 'nullable|date',
            'next_test_date' => 'nullable|date|after:last_tested_date',
            'status' => 'nullable|string|in:active,inactive,testing,review_required',
            'notes' => 'nullable|string'
        ]);
        
        $businessContinuityPlan->update($request->all());
        
        return response()->json(['message' => 'Business continuity plan updated', 'business_continuity_plan' => $businessContinuityPlan]);
    }
    
    public function destroy(Request $request, $id)
    {
        $businessContinuityPlan = BusinessContinuityPlan::where('business_id', $request->user()->business_id)->findOrFail($id);
        $businessContinuityPlan->delete();
        
        return response()->json(['message' => 'Business continuity plan deleted']);
    }
}