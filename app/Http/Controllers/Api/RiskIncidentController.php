<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiskIncident;

class RiskIncidentController extends Controller
{
    public function index(Request $request)
    {
        $riskIncidents = RiskIncident::where('business_id', $request->user()->business_id)
            ->with(['risk', 'reportedBy'])
            ->when($request->search, function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->when($request->risk_id, function($q) use ($request) {
                $q->where('risk_id', $request->risk_id);
            })
            ->when($request->severity, function($q) use ($request) {
                $q->where('severity', $request->severity);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->high_severity, function($q) {
                $q->whereIn('severity', ['high', 'critical']);
            })
            ->when($request->open, function($q) {
                $q->where('status', '!=', 'resolved')
                  ->where('status', '!=', 'closed');
            })
            ->orderBy('incident_date', 'desc')
            ->paginate(20);
            
        return response()->json($riskIncidents);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'risk_id' => 'nullable|exists:risks,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'incident_date' => 'required|date',
            'incident_type' => 'nullable|string|max:100',
            'reported_by' => 'nullable|exists:users,id',
            'affected_areas' => 'nullable|string',
            'financial_loss' => 'nullable|numeric|min:0',
            'affected_people' => 'nullable|integer|min:0',
            'severity' => 'nullable|string|in:low,medium,high,critical',
            'status' => 'nullable|string|in:reported,investigated,resolved,closed',
            'immediate_actions' => 'nullable|string',
            'root_cause' => 'nullable|string',
            'corrective_actions' => 'nullable|string',
            'resolution_date' => 'nullable|date',
            'lessons_learned' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);
        
        $riskIncident = RiskIncident::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'reported_by' => $request->reported_by ?? $request->user()->id,
            'severity' => $request->severity ?? 'medium',
            'status' => $request->status ?? 'reported'
        ]));
        
        return response()->json(['message' => 'Risk incident created', 'risk_incident' => $riskIncident], 201);
    }
    
    public function show(Request $request, $id)
    {
        $riskIncident = RiskIncident::where('business_id', $request->user()->business_id)
            ->with(['risk', 'reportedBy'])
            ->findOrFail($id);
            
        return response()->json(['risk_incident' => $riskIncident]);
    }
    
    public function update(Request $request, $id)
    {
        $riskIncident = RiskIncident::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'risk_id' => 'nullable|exists:risks,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'incident_date' => 'sometimes|required|date',
            'incident_type' => 'nullable|string|max:100',
            'reported_by' => 'nullable|exists:users,id',
            'affected_areas' => 'nullable|string',
            'financial_loss' => 'nullable|numeric|min:0',
            'affected_people' => 'nullable|integer|min:0',
            'severity' => 'nullable|string|in:low,medium,high,critical',
            'status' => 'nullable|string|in:reported,investigated,resolved,closed',
            'immediate_actions' => 'nullable|string',
            'root_cause' => 'nullable|string',
            'corrective_actions' => 'nullable|string',
            'resolution_date' => 'nullable|date',
            'lessons_learned' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);
        
        $riskIncident->update($request->all());
        
        return response()->json(['message' => 'Risk incident updated', 'risk_incident' => $riskIncident]);
    }
    
    public function destroy(Request $request, $id)
    {
        $riskIncident = RiskIncident::where('business_id', $request->user()->business_id)->findOrFail($id);
        $riskIncident->delete();
        
        return response()->json(['message' => 'Risk incident deleted']);
    }
}