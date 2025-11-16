<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplianceAudit;

class ComplianceAuditController extends Controller
{
    public function index(Request $request)
    {
        $complianceAudits = ComplianceAudit::where('business_id', $request->user()->business_id)
            ->with(['auditor'])
            ->when($request->search, function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->when($request->audit_type, function($q) use ($request) {
                $q->where('audit_type', $request->audit_type);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->upcoming, function($q) {
                $q->where('planned_date', '>=', now())
                  ->where('planned_date', '<=', now()->addDays(7))
                  ->where('status', 'planned');
            })
            ->when($request->overdue, function($q) {
                $q->where('planned_date', '<', now())
                  ->where('status', '!=', 'completed');
            })
            ->orderBy('planned_date', 'asc')
            ->paginate(20);
            
        return response()->json($complianceAudits);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audit_type' => 'nullable|string|in:internal,external,regulatory',
            'auditor_id' => 'nullable|exists:users,id',
            'planned_date' => 'nullable|date',
            'actual_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'status' => 'nullable|string|in:planned,in_progress,completed,cancelled',
            'scope' => 'nullable|string',
            'objectives' => 'nullable|string',
            'findings_summary' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'action_items' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);
        
        $complianceAudit = ComplianceAudit::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'status' => $request->status ?? 'planned'
        ]));
        
        return response()->json(['message' => 'Compliance audit created', 'compliance_audit' => $complianceAudit], 201);
    }
    
    public function show(Request $request, $id)
    {
        $complianceAudit = ComplianceAudit::where('business_id', $request->user()->business_id)
            ->with(['auditor', 'findings.complianceRequirement'])
            ->findOrFail($id);
            
        return response()->json(['compliance_audit' => $complianceAudit]);
    }
    
    public function update(Request $request, $id)
    {
        $complianceAudit = ComplianceAudit::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'audit_type' => 'nullable|string|in:internal,external,regulatory',
            'auditor_id' => 'nullable|exists:users,id',
            'planned_date' => 'nullable|date',
            'actual_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'status' => 'nullable|string|in:planned,in_progress,completed,cancelled',
            'scope' => 'nullable|string',
            'objectives' => 'nullable|string',
            'findings_summary' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'action_items' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);
        
        $complianceAudit->update($request->all());
        
        return response()->json(['message' => 'Compliance audit updated', 'compliance_audit' => $complianceAudit]);
    }
    
    public function destroy(Request $request, $id)
    {
        $complianceAudit = ComplianceAudit::where('business_id', $request->user()->business_id)->findOrFail($id);
        $complianceAudit->delete();
        
        return response()->json(['message' => 'Compliance audit deleted']);
    }
}