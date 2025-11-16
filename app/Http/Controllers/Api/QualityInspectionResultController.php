<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QualityInspectionResult;
use App\Models\QualityInspection;
use App\Models\QualityChecklistItem;

class QualityInspectionResultController extends Controller
{
    public function index(Request $request)
    {
        $qualityInspectionResults = QualityInspectionResult::whereHas('qualityInspection', function($q) use ($request) {
                $q->where('business_id', $request->user()->business_id);
            })
            ->with(['qualityInspection', 'qualityChecklistItem'])
            ->when($request->quality_inspection_id, function($q) use ($request) {
                $q->where('quality_inspection_id', $request->quality_inspection_id);
            })
            ->paginate(20);
            
        return response()->json($qualityInspectionResults);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'quality_inspection_id' => 'required|exists:quality_inspections,id',
            'quality_checklist_item_id' => 'required|exists:quality_checklist_items,id',
            'result_value' => 'nullable|string',
            'passed' => 'nullable|boolean',
            'remarks' => 'nullable|string',
            'attachment_path' => 'nullable|string'
        ]);
        
        // Verify quality inspection belongs to the same business
        $qualityInspection = QualityInspection::where('business_id', $request->user()->business_id)
            ->findOrFail($request->quality_inspection_id);
            
        // Verify quality checklist item belongs to the same checklist
        $qualityChecklistItem = QualityChecklistItem::where('quality_checklist_id', $qualityInspection->quality_checklist_id)
            ->findOrFail($request->quality_checklist_item_id);
        
        $qualityInspectionResult = QualityInspectionResult::create($request->all());
        
        return response()->json(['message' => 'Quality inspection result created', 'quality_inspection_result' => $qualityInspectionResult], 201);
    }
    
    public function show(Request $request, $id)
    {
        $qualityInspectionResult = QualityInspectionResult::whereHas('qualityInspection', function($q) use ($request) {
                $q->where('business_id', $request->user()->business_id);
            })
            ->with(['qualityInspection', 'qualityChecklistItem'])
            ->findOrFail($id);
            
        return response()->json(['quality_inspection_result' => $qualityInspectionResult]);
    }
    
    public function update(Request $request, $id)
    {
        $qualityInspectionResult = QualityInspectionResult::whereHas('qualityInspection', function($q) use ($request) {
                $q->where('business_id', $request->user()->business_id);
            })->findOrFail($id);
        
        $request->validate([
            'quality_inspection_id' => 'sometimes|required|exists:quality_inspections,id',
            'quality_checklist_item_id' => 'sometimes|required|exists:quality_checklist_items,id',
            'result_value' => 'nullable|string',
            'passed' => 'nullable|boolean',
            'remarks' => 'nullable|string',
            'attachment_path' => 'nullable|string'
        ]);
        
        // Verify quality inspection belongs to the same business
        if ($request->quality_inspection_id) {
            $qualityInspection = QualityInspection::where('business_id', $request->user()->business_id)
                ->findOrFail($request->quality_inspection_id);
        }
        
        // Verify quality checklist item belongs to the same checklist
        if ($request->quality_checklist_item_id && isset($qualityInspection)) {
            $qualityChecklistItem = QualityChecklistItem::where('quality_checklist_id', $qualityInspection->quality_checklist_id)
                ->findOrFail($request->quality_checklist_item_id);
        }
        
        $qualityInspectionResult->update($request->all());
        
        return response()->json(['message' => 'Quality inspection result updated', 'quality_inspection_result' => $qualityInspectionResult]);
    }
    
    public function destroy(Request $request, $id)
    {
        $qualityInspectionResult = QualityInspectionResult::whereHas('qualityInspection', function($q) use ($request) {
                $q->where('business_id', $request->user()->business_id);
            })->findOrFail($id);
            
        $qualityInspectionResult->delete();
        
        return response()->json(['message' => 'Quality inspection result deleted']);
    }
}