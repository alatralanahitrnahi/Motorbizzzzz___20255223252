<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Risk;

class RiskController extends Controller
{
    public function index(Request $request)
    {
        $risks = Risk::where('business_id', $request->user()->business_id)
            ->with(['riskCategory', 'owner'])
            ->when($request->search, function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->when($request->risk_category_id, function($q) use ($request) {
                $q->where('risk_category_id', $request->risk_category_id);
            })
            ->when($request->likelihood, function($q) use ($request) {
                $q->where('likelihood', $request->likelihood);
            })
            ->when($request->impact, function($q) use ($request) {
                $q->where('impact', $request->impact);
            })
            ->when($request->risk_level, function($q) use ($request) {
                $q->where('risk_level', $request->risk_level);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->needs_review, function($q) {
                $q->where('review_date', '<=', now()->addDays(30))
                  ->orWhere('review_date', '<=', now());
            })
            ->orderBy('risk_level', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return response()->json($risks);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'risk_category_id' => 'nullable|exists:risk_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cause' => 'nullable|string',
            'effect' => 'nullable|string',
            'owner_id' => 'nullable|exists:users,id',
            'likelihood' => 'nullable|string|in:low,medium,high',
            'impact' => 'nullable|string|in:low,medium,high',
            'risk_level' => 'nullable|string|in:low,medium,high,critical',
            'status' => 'nullable|string|in:identified,assessed,mitigated,monitored,closed',
            'assessment_date' => 'nullable|date',
            'review_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);
        
        $risk = Risk::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'likelihood' => $request->likelihood ?? 'medium',
            'impact' => $request->impact ?? 'medium',
            'risk_level' => $request->risk_level ?? 'medium',
            'status' => $request->status ?? 'identified'
        ]));
        
        return response()->json(['message' => 'Risk created', 'risk' => $risk], 201);
    }
    
    public function show(Request $request, $id)
    {
        $risk = Risk::where('business_id', $request->user()->business_id)
            ->with(['riskCategory', 'owner', 'impactAssessments.assessedBy', 'mitigationStrategies.responsiblePerson', 'incidents'])
            ->findOrFail($id);
            
        return response()->json(['risk' => $risk]);
    }
    
    public function update(Request $request, $id)
    {
        $risk = Risk::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'risk_category_id' => 'nullable|exists:risk_categories,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'cause' => 'nullable|string',
            'effect' => 'nullable|string',
            'owner_id' => 'nullable|exists:users,id',
            'likelihood' => 'nullable|string|in:low,medium,high',
            'impact' => 'nullable|string|in:low,medium,high',
            'risk_level' => 'nullable|string|in:low,medium,high,critical',
            'status' => 'nullable|string|in:identified,assessed,mitigated,monitored,closed',
            'assessment_date' => 'nullable|date',
            'review_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);
        
        $risk->update($request->all());
        
        return response()->json(['message' => 'Risk updated', 'risk' => $risk]);
    }
    
    public function destroy(Request $request, $id)
    {
        $risk = Risk::where('business_id', $request->user()->business_id)->findOrFail($id);
        $risk->delete();
        
        return response()->json(['message' => 'Risk deleted']);
    }
}