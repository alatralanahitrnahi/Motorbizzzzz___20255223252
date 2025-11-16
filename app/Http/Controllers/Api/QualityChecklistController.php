<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QualityChecklist;
use App\Models\QualityStandard;

class QualityChecklistController extends Controller
{
    public function index(Request $request)
    {
        $qualityChecklists = QualityChecklist::where('business_id', $request->user()->business_id)
            ->with('qualityStandard')
            ->when($request->search, function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            })
            ->when($request->checklist_type, function($q) use ($request) {
                $q->where('checklist_type', $request->checklist_type);
            })
            ->when($request->is_active, function($q) use ($request) {
                $q->where('is_active', $request->is_active);
            })
            ->paginate(20);
            
        return response()->json($qualityChecklists);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quality_standard_id' => 'nullable|exists:quality_standards,id',
            'description' => 'nullable|string',
            'checklist_type' => 'required|in:incoming,in_process,final',
            'is_active' => 'nullable|boolean'
        ]);
        
        // Verify quality standard belongs to the same business
        if ($request->quality_standard_id) {
            $qualityStandard = QualityStandard::where('business_id', $request->user()->business_id)
                ->findOrFail($request->quality_standard_id);
        }
        
        $qualityChecklist = QualityChecklist::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'is_active' => $request->is_active ?? true
        ]));
        
        return response()->json(['message' => 'Quality checklist created', 'quality_checklist' => $qualityChecklist], 201);
    }
    
    public function show(Request $request, $id)
    {
        $qualityChecklist = QualityChecklist::where('business_id', $request->user()->business_id)
            ->with(['qualityStandard', 'checklistItems'])
            ->findOrFail($id);
            
        return response()->json(['quality_checklist' => $qualityChecklist]);
    }
    
    public function update(Request $request, $id)
    {
        $qualityChecklist = QualityChecklist::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'quality_standard_id' => 'nullable|exists:quality_standards,id',
            'description' => 'nullable|string',
            'checklist_type' => 'sometimes|required|in:incoming,in_process,final',
            'is_active' => 'nullable|boolean'
        ]);
        
        // Verify quality standard belongs to the same business
        if ($request->quality_standard_id) {
            $qualityStandard = QualityStandard::where('business_id', $request->user()->business_id)
                ->findOrFail($request->quality_standard_id);
        }
        
        $qualityChecklist->update($request->all());
        
        return response()->json(['message' => 'Quality checklist updated', 'quality_checklist' => $qualityChecklist]);
    }
    
    public function destroy(Request $request, $id)
    {
        $qualityChecklist = QualityChecklist::where('business_id', $request->user()->business_id)->findOrFail($id);
        $qualityChecklist->delete();
        
        return response()->json(['message' => 'Quality checklist deleted']);
    }
}