<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiskImpactAssessment;

class RiskImpactAssessmentController extends Controller
{
    public function index(Request $request)
    {
        $riskImpactAssessments = RiskImpactAssessment::where('business_id', $request->user()->business_id)
            ->with(['risk', 'assessedBy'])
            ->when($request->risk_id, function($q) use ($request) {
                $q->where('risk_id', $request->risk_id);
            })
            ->when($request->assessed_by, function($q) use ($request) {
                $q->where('assessed_by', $request->assessed_by);
            })
            ->orderBy('assessment_date', 'desc')
            ->paginate(20);
            
        return response()->json($riskImpactAssessments);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'risk_id' => 'required|exists:risks,id',
            'financial_impact' => 'nullable|numeric|min:0',
            'operational_impact' => 'nullable|numeric|min:0|max:100',
            'reputational_impact' => 'nullable|numeric|min:0|max:100',
            'legal_impact' => 'nullable|numeric|min:0|max:100',
            'safety_impact' => 'nullable|numeric|min:0|max:100',
            'assessment_details' => 'nullable|string',
            'assessed_by' => 'nullable|exists:users,id',
            'assessment_date' => 'nullable|date',
            'methodology' => 'nullable|string'
        ]);
        
        $riskImpactAssessment = RiskImpactAssessment::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'assessed_by' => $request->assessed_by ?? $request->user()->id,
            'assessment_date' => $request->assessment_date ?? now()
        ]));
        
        return response()->json(['message' => 'Risk impact assessment created', 'risk_impact_assessment' => $riskImpactAssessment], 201);
    }
    
    public function show(Request $request, $id)
    {
        $riskImpactAssessment = RiskImpactAssessment::where('business_id', $request->user()->business_id)
            ->with(['risk', 'assessedBy'])
            ->findOrFail($id);
            
        return response()->json(['risk_impact_assessment' => $riskImpactAssessment]);
    }
    
    public function update(Request $request, $id)
    {
        $riskImpactAssessment = RiskImpactAssessment::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'risk_id' => 'sometimes|required|exists:risks,id',
            'financial_impact' => 'nullable|numeric|min:0',
            'operational_impact' => 'nullable|numeric|min:0|max:100',
            'reputational_impact' => 'nullable|numeric|min:0|max:100',
            'legal_impact' => 'nullable|numeric|min:0|max:100',
            'safety_impact' => 'nullable|numeric|min:0|max:100',
            'assessment_details' => 'nullable|string',
            'assessed_by' => 'nullable|exists:users,id',
            'assessment_date' => 'nullable|date',
            'methodology' => 'nullable|string'
        ]);
        
        $riskImpactAssessment->update($request->all());
        
        return response()->json(['message' => 'Risk impact assessment updated', 'risk_impact_assessment' => $riskImpactAssessment]);
    }
    
    public function destroy(Request $request, $id)
    {
        $riskImpactAssessment = RiskImpactAssessment::where('business_id', $request->user()->business_id)->findOrFail($id);
        $riskImpactAssessment->delete();
        
        return response()->json(['message' => 'Risk impact assessment deleted']);
    }
}