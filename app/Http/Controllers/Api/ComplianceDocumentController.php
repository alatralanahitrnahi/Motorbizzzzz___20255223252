<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplianceDocument;

class ComplianceDocumentController extends Controller
{
    public function index(Request $request)
    {
        $complianceDocuments = ComplianceDocument::where('business_id', $request->user()->business_id)
            ->with(['complianceRequirement', 'approvedBy'])
            ->when($request->search, function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->when($request->document_type, function($q) use ($request) {
                $q->where('document_type', $request->document_type);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->needs_review, function($q) {
                $q->where('review_date', '<=', now()->addDays(7))
                  ->orWhere('review_date', '<=', now());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return response()->json($complianceDocuments);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'compliance_requirement_id' => 'nullable|exists:compliance_requirements,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'nullable|string|max:100',
            'file_path' => 'nullable|string|max:500',
            'file_name' => 'nullable|string|max:255',
            'file_type' => 'nullable|string|max:50',
            'file_size' => 'nullable|integer',
            'version' => 'nullable|integer|min:1',
            'approved_by' => 'nullable|exists:users,id',
            'approval_date' => 'nullable|date',
            'effective_date' => 'nullable|date',
            'review_date' => 'nullable|date|after:effective_date',
            'status' => 'nullable|string|in:draft,approved,archived',
            'notes' => 'nullable|string'
        ]);
        
        $complianceDocument = ComplianceDocument::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'version' => $request->version ?? 1,
            'status' => $request->status ?? 'draft'
        ]));
        
        return response()->json(['message' => 'Compliance document created', 'compliance_document' => $complianceDocument], 201);
    }
    
    public function show(Request $request, $id)
    {
        $complianceDocument = ComplianceDocument::where('business_id', $request->user()->business_id)
            ->with(['complianceRequirement', 'approvedBy'])
            ->findOrFail($id);
            
        return response()->json(['compliance_document' => $complianceDocument]);
    }
    
    public function update(Request $request, $id)
    {
        $complianceDocument = ComplianceDocument::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'compliance_requirement_id' => 'nullable|exists:compliance_requirements,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'nullable|string|max:100',
            'file_path' => 'nullable|string|max:500',
            'file_name' => 'nullable|string|max:255',
            'file_type' => 'nullable|string|max:50',
            'file_size' => 'nullable|integer',
            'version' => 'nullable|integer|min:1',
            'approved_by' => 'nullable|exists:users,id',
            'approval_date' => 'nullable|date',
            'effective_date' => 'nullable|date',
            'review_date' => 'nullable|date|after:effective_date',
            'status' => 'nullable|string|in:draft,approved,archived',
            'notes' => 'nullable|string'
        ]);
        
        $complianceDocument->update($request->all());
        
        return response()->json(['message' => 'Compliance document updated', 'compliance_document' => $complianceDocument]);
    }
    
    public function destroy(Request $request, $id)
    {
        $complianceDocument = ComplianceDocument::where('business_id', $request->user()->business_id)->findOrFail($id);
        $complianceDocument->delete();
        
        return response()->json(['message' => 'Compliance document deleted']);
    }
}