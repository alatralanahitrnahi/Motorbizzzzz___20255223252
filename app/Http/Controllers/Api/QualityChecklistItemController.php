<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QualityChecklistItem;
use App\Models\QualityChecklist;

class QualityChecklistItemController extends Controller
{
    public function index(Request $request)
    {
        $qualityChecklistItems = QualityChecklistItem::whereHas('qualityChecklist', function($q) use ($request) {
                $q->where('business_id', $request->user()->business_id);
            })
            ->with('qualityChecklist')
            ->when($request->quality_checklist_id, function($q) use ($request) {
                $q->where('quality_checklist_id', $request->quality_checklist_id);
            })
            ->paginate(20);
            
        return response()->json($qualityChecklistItems);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'quality_checklist_id' => 'required|exists:quality_checklists,id',
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'criteria_type' => 'required|in:pass_fail,numeric,text',
            'acceptable_criteria' => 'nullable|string',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
            'is_required' => 'nullable|boolean'
        ]);
        
        // Verify quality checklist belongs to the same business
        $qualityChecklist = QualityChecklist::where('business_id', $request->user()->business_id)
            ->findOrFail($request->quality_checklist_id);
        
        $qualityChecklistItem = QualityChecklistItem::create($request->all());
        
        return response()->json(['message' => 'Quality checklist item created', 'quality_checklist_item' => $qualityChecklistItem], 201);
    }
    
    public function show(Request $request, $id)
    {
        $qualityChecklistItem = QualityChecklistItem::whereHas('qualityChecklist', function($q) use ($request) {
                $q->where('business_id', $request->user()->business_id);
            })
            ->with('qualityChecklist')
            ->findOrFail($id);
            
        return response()->json(['quality_checklist_item' => $qualityChecklistItem]);
    }
    
    public function update(Request $request, $id)
    {
        $qualityChecklistItem = QualityChecklistItem::whereHas('qualityChecklist', function($q) use ($request) {
                $q->where('business_id', $request->user()->business_id);
            })->findOrFail($id);
        
        $request->validate([
            'quality_checklist_id' => 'sometimes|required|exists:quality_checklists,id',
            'item_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'criteria_type' => 'sometimes|required|in:pass_fail,numeric,text',
            'acceptable_criteria' => 'nullable|string',
            'min_value' => 'nullable|numeric',
            'max_value' => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
            'is_required' => 'nullable|boolean'
        ]);
        
        // Verify quality checklist belongs to the same business
        if ($request->quality_checklist_id) {
            $qualityChecklist = QualityChecklist::where('business_id', $request->user()->business_id)
                ->findOrFail($request->quality_checklist_id);
        }
        
        $qualityChecklistItem->update($request->all());
        
        return response()->json(['message' => 'Quality checklist item updated', 'quality_checklist_item' => $qualityChecklistItem]);
    }
    
    public function destroy(Request $request, $id)
    {
        $qualityChecklistItem = QualityChecklistItem::whereHas('qualityChecklist', function($q) use ($request) {
                $q->where('business_id', $request->user()->business_id);
            })->findOrFail($id);
            
        $qualityChecklistItem->delete();
        
        return response()->json(['message' => 'Quality checklist item deleted']);
    }
}