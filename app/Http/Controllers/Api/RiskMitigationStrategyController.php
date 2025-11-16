<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiskMitigationStrategy;

class RiskMitigationStrategyController extends Controller
{
    public function index(Request $request)
    {
        $riskMitigationStrategies = RiskMitigationStrategy::where('business_id', $request->user()->business_id)
            ->with(['risk', 'responsiblePerson'])
            ->when($request->risk_id, function($q) use ($request) {
                $q->where('risk_id', $request->risk_id);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->upcoming, function($q) {
                $q->where('start_date', '>=', now())
                  ->where('start_date', '<=', now()->addDays(7))
                  ->where('status', 'planned');
            })
            ->when($request->overdue, function($q) {
                $q->where('end_date', '<', now())
                  ->where('status', '!=', 'completed');
            })
            ->orderBy('end_date', 'asc')
            ->paginate(20);
            
        return response()->json($riskMitigationStrategies);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'risk_id' => 'required|exists:risks,id',
            'strategy_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'actions' => 'nullable|string',
            'responsible_person_id' => 'nullable|exists:users,id',
            'cost' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'nullable|string|in:planned,in_progress,implemented,completed',
            'effectiveness' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string'
        ]);
        
        $riskMitigationStrategy = RiskMitigationStrategy::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'status' => $request->status ?? 'planned'
        ]));
        
        return response()->json(['message' => 'Risk mitigation strategy created', 'risk_mitigation_strategy' => $riskMitigationStrategy], 201);
    }
    
    public function show(Request $request, $id)
    {
        $riskMitigationStrategy = RiskMitigationStrategy::where('business_id', $request->user()->business_id)
            ->with(['risk', 'responsiblePerson'])
            ->findOrFail($id);
            
        return response()->json(['risk_mitigation_strategy' => $riskMitigationStrategy]);
    }
    
    public function update(Request $request, $id)
    {
        $riskMitigationStrategy = RiskMitigationStrategy::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'risk_id' => 'sometimes|required|exists:risks,id',
            'strategy_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'actions' => 'nullable|string',
            'responsible_person_id' => 'nullable|exists:users,id',
            'cost' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'nullable|string|in:planned,in_progress,implemented,completed',
            'effectiveness' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string'
        ]);
        
        $riskMitigationStrategy->update($request->all());
        
        return response()->json(['message' => 'Risk mitigation strategy updated', 'risk_mitigation_strategy' => $riskMitigationStrategy]);
    }
    
    public function destroy(Request $request, $id)
    {
        $riskMitigationStrategy = RiskMitigationStrategy::where('business_id', $request->user()->business_id)->findOrFail($id);
        $riskMitigationStrategy->delete();
        
        return response()->json(['message' => 'Risk mitigation strategy deleted']);
    }
}