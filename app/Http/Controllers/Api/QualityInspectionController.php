<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QualityInspection;
use App\Models\QualityChecklist;
use App\Models\Material;
use App\Models\Product;
use App\Models\WorkOrder;

class QualityInspectionController extends Controller
{
    public function index(Request $request)
    {
        $qualityInspections = QualityInspection::where('business_id', $request->user()->business_id)
            ->with(['qualityChecklist', 'inspector'])
            ->when($request->search, function($q) use ($request) {
                $q->where('batch_number', 'like', "%{$request->search}%");
            })
            ->when($request->inspection_type, function($q) use ($request) {
                $q->where('inspection_type', $request->inspection_type);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return response()->json($qualityInspections);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'quality_checklist_id' => 'required|exists:quality_checklists,id',
            'reference_id' => 'nullable|integer',
            'reference_type' => 'nullable|in:material,product,work_order',
            'inspection_type' => 'required|in:incoming,in_process,final',
            'batch_number' => 'nullable|string|max:100',
            'inspection_date' => 'required|date',
            'status' => 'required|in:pending,in_progress,completed,rejected',
            'notes' => 'nullable|string'
        ]);
        
        // Verify quality checklist belongs to the same business
        $qualityChecklist = QualityChecklist::where('business_id', $request->user()->business_id)
            ->findOrFail($request->quality_checklist_id);
            
        // Verify reference belongs to the same business if provided
        if ($request->reference_id && $request->reference_type) {
            $model = null;
            switch ($request->reference_type) {
                case 'material':
                    $model = Material::where('business_id', $request->user()->business_id)->find($request->reference_id);
                    break;
                case 'product':
                    $model = Product::where('business_id', $request->user()->business_id)->find($request->reference_id);
                    break;
                case 'work_order':
                    $model = WorkOrder::where('business_id', $request->user()->business_id)->find($request->reference_id);
                    break;
            }
            
            if (!$model) {
                return response()->json(['message' => 'Invalid reference'], 400);
            }
        }
        
        $qualityInspection = QualityInspection::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'inspector_id' => $request->user()->id,
            'overall_score' => null,
            'passed' => null,
            'completed_at' => null
        ]));
        
        return response()->json(['message' => 'Quality inspection created', 'quality_inspection' => $qualityInspection], 201);
    }
    
    public function show(Request $request, $id)
    {
        $qualityInspection = QualityInspection::where('business_id', $request->user()->business_id)
            ->with(['qualityChecklist', 'inspector', 'inspectionResults.qualityChecklistItem'])
            ->findOrFail($id);
            
        return response()->json(['quality_inspection' => $qualityInspection]);
    }
    
    public function update(Request $request, $id)
    {
        $qualityInspection = QualityInspection::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'quality_checklist_id' => 'sometimes|required|exists:quality_checklists,id',
            'reference_id' => 'nullable|integer',
            'reference_type' => 'nullable|in:material,product,work_order',
            'inspection_type' => 'sometimes|required|in:incoming,in_process,final',
            'batch_number' => 'nullable|string|max:100',
            'inspection_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:pending,in_progress,completed,rejected',
            'notes' => 'nullable|string',
            'overall_score' => 'nullable|numeric|min:0|max:100',
            'passed' => 'nullable|boolean',
            'completed_at' => 'nullable|date'
        ]);
        
        // Verify quality checklist belongs to the same business
        if ($request->quality_checklist_id) {
            $qualityChecklist = QualityChecklist::where('business_id', $request->user()->business_id)
                ->findOrFail($request->quality_checklist_id);
        }
        
        // Verify reference belongs to the same business if provided
        if ($request->reference_id && $request->reference_type) {
            $model = null;
            switch ($request->reference_type) {
                case 'material':
                    $model = Material::where('business_id', $request->user()->business_id)->find($request->reference_id);
                    break;
                case 'product':
                    $model = Product::where('business_id', $request->user()->business_id)->find($request->reference_id);
                    break;
                case 'work_order':
                    $model = WorkOrder::where('business_id', $request->user()->business_id)->find($request->reference_id);
                    break;
            }
            
            if (!$model) {
                return response()->json(['message' => 'Invalid reference'], 400);
            }
        }
        
        $qualityInspection->update($request->all());
        
        return response()->json(['message' => 'Quality inspection updated', 'quality_inspection' => $qualityInspection]);
    }
    
    public function destroy(Request $request, $id)
    {
        $qualityInspection = QualityInspection::where('business_id', $request->user()->business_id)->findOrFail($id);
        $qualityInspection->delete();
        
        return response()->json(['message' => 'Quality inspection deleted']);
    }
    
    public function completeInspection(Request $request, $id)
    {
        $qualityInspection = QualityInspection::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'overall_score' => 'required|numeric|min:0|max:100',
            'passed' => 'required|boolean',
            'notes' => 'nullable|string'
        ]);
        
        $qualityInspection->update([
            'overall_score' => $request->overall_score,
            'passed' => $request->passed,
            'status' => 'completed',
            'completed_at' => now(),
            'notes' => $request->notes
        ]);
        
        return response()->json(['message' => 'Quality inspection completed', 'quality_inspection' => $qualityInspection]);
    }
}