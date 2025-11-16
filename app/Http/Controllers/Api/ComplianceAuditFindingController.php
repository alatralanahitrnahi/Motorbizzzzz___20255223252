<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplianceAuditFinding;

class ComplianceAuditFindingController extends Controller
{
    public function index(Request $request)
    {
        $complianceAuditFindings = ComplianceAuditFinding::where('business_id', $request->user()->business_id)
            ->with(['complianceAudit', 'complianceRequirement', 'assignedTo'])
            ->when($request->search, function($q) use ($request) {
                $q->where('description', 'like', "%{$request->search}%");
            })
            ->when($request->severity, function($q) use ($request) {
                $q->where('severity', $request->severity);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->open, function($q) {
                $q->whereIn('status', ['open', 'in_progress']);
            })
            ->when($request->overdue, function($q) {
                $q->where('due_date', '<', now())
                  ->whereIn('status', ['open', 'in_progress']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return response()->json($complianceAuditFindings);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'compliance_audit_id' => 'required|exists:compliance_audits,id',
            'compliance_requirement_id' => 'nullable|exists:compliance_requirements,id',
            'description' => 'required|string',
            'evidence' => 'nullable|string',
            'severity' => 'nullable|string|in:low,medium,high,critical',
            'status' => 'nullable|string|in:open,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'corrective_action' => 'nullable|string',
            'resolution_date' => 'nullable|date',
            'resolution_notes' => 'nullable|string'
        ]);
        
        $complianceAuditFinding = ComplianceAuditFinding::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'severity' => $request->severity ?? 'low',
            'status' => $request->status ?? 'open'
        ]));
        
        return response()->json(['message' => 'Compliance audit finding created', 'compliance_audit_finding' => $complianceAuditFinding], 201);
    }
    
    public function show(Request $request, $id)
    {
        $complianceAuditFinding = ComplianceAuditFinding::where('business_id', $request->user()->business_id)
            ->with(['complianceAudit', 'complianceRequirement', 'assignedTo'])
            ->findOrFail($id);
            
        return response()->json(['compliance_audit_finding' => $complianceAuditFinding]);
    }
    
    public function update(Request $request, $id)
    {
        $complianceAuditFinding = ComplianceAuditFinding::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'compliance_audit_id' => 'sometimes|required|exists:compliance_audits,id',
            'compliance_requirement_id' => 'nullable|exists:compliance_requirements,id',
            'description' => 'sometimes|required|string',
            'evidence' => 'nullable|string',
            'severity' => 'nullable|string|in:low,medium,high,critical',
            'status' => 'nullable|string|in:open,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'corrective_action' => 'nullable|string',
            'resolution_date' => 'nullable|date',
            'resolution_notes' => 'nullable|string'
        ]);
        
        $complianceAuditFinding->update($request->all());
        
        return response()->json(['message' => 'Compliance audit finding updated', 'compliance_audit_finding' => $complianceAuditFinding]);
    }
    
    public function destroy(Request $request, $id)
    {
        $complianceAuditFinding = ComplianceAuditFinding::where('business_id', $request->user()->business_id)->findOrFail($id);
        $complianceAuditFinding->delete();
        
        return response()->json(['message' => 'Compliance audit finding deleted']);
    }
}