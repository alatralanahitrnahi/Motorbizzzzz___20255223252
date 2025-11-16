<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QualityStandard;

class QualityStandardController extends Controller
{
    public function index(Request $request)
    {
        $qualityStandards = QualityStandard::where('business_id', $request->user()->business_id)
            ->when($request->search, function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('standard_code', 'like', "%{$request->search}%");
            })
            ->when($request->is_active, function($q) use ($request) {
                $q->where('is_active', $request->is_active);
            })
            ->paginate(20);
            
        return response()->json($qualityStandards);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'standard_code' => 'required|string|unique:quality_standards,standard_code',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean'
        ]);
        
        $qualityStandard = QualityStandard::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'version' => $request->version ?? '1.0',
            'is_active' => $request->is_active ?? true
        ]));
        
        return response()->json(['message' => 'Quality standard created', 'quality_standard' => $qualityStandard], 201);
    }
    
    public function show(Request $request, $id)
    {
        $qualityStandard = QualityStandard::where('business_id', $request->user()->business_id)
            ->with('checklists')
            ->findOrFail($id);
            
        return response()->json(['quality_standard' => $qualityStandard]);
    }
    
    public function update(Request $request, $id)
    {
        $qualityStandard = QualityStandard::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'standard_code' => 'sometimes|required|string|unique:quality_standards,standard_code,'.$qualityStandard->id,
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean'
        ]);
        
        $qualityStandard->update($request->all());
        
        return response()->json(['message' => 'Quality standard updated', 'quality_standard' => $qualityStandard]);
    }
    
    public function destroy(Request $request, $id)
    {
        $qualityStandard = QualityStandard::where('business_id', $request->user()->business_id)->findOrFail($id);
        $qualityStandard->delete();
        
        return response()->json(['message' => 'Quality standard deleted']);
    }
}