<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiskCategory;

class RiskCategoryController extends Controller
{
    public function index(Request $request)
    {
        $riskCategories = RiskCategory::where('business_id', $request->user()->business_id)
            ->with(['owner'])
            ->when($request->search, function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->when($request->category_type, function($q) use ($request) {
                $q->where('category_type', $request->category_type);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('name', 'asc')
            ->paginate(20);
            
        return response()->json($riskCategories);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_type' => 'nullable|string|max:100',
            'owner_id' => 'nullable|exists:users,id',
            'status' => 'nullable|string|in:active,inactive',
            'notes' => 'nullable|string'
        ]);
        
        $riskCategory = RiskCategory::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'status' => $request->status ?? 'active'
        ]));
        
        return response()->json(['message' => 'Risk category created', 'risk_category' => $riskCategory], 201);
    }
    
    public function show(Request $request, $id)
    {
        $riskCategory = RiskCategory::where('business_id', $request->user()->business_id)
            ->with(['owner', 'risks'])
            ->findOrFail($id);
            
        return response()->json(['risk_category' => $riskCategory]);
    }
    
    public function update(Request $request, $id)
    {
        $riskCategory = RiskCategory::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category_type' => 'nullable|string|max:100',
            'owner_id' => 'nullable|exists:users,id',
            'status' => 'nullable|string|in:active,inactive',
            'notes' => 'nullable|string'
        ]);
        
        $riskCategory->update($request->all());
        
        return response()->json(['message' => 'Risk category updated', 'risk_category' => $riskCategory]);
    }
    
    public function destroy(Request $request, $id)
    {
        $riskCategory = RiskCategory::where('business_id', $request->user()->business_id)->findOrFail($id);
        $riskCategory->delete();
        
        return response()->json(['message' => 'Risk category deleted']);
    }
}