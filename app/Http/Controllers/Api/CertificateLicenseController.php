<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CertificateLicense;

class CertificateLicenseController extends Controller
{
    public function index(Request $request)
    {
        $certificatesLicenses = CertificateLicense::where('business_id', $request->user()->business_id)
            ->with(['responsiblePerson'])
            ->when($request->search, function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->expiring_soon, function($q) {
                $q->where('expiry_date', '>=', now())
                  ->where('expiry_date', '<=', now()->addDays(30));
            })
            ->when($request->expired, function($q) {
                $q->where('expiry_date', '<', now());
            })
            ->orderBy('expiry_date', 'asc')
            ->paginate(20);
            
        return response()->json($certificatesLicenses);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'certificate_number' => 'nullable|string|max:100',
            'issuing_authority' => 'nullable|string|max:100',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'status' => 'nullable|string|in:active,expired,revoked,pending_renewal',
            'file_path' => 'nullable|string|max:500',
            'file_name' => 'nullable|string|max:255',
            'file_type' => 'nullable|string|max:50',
            'file_size' => 'nullable|integer',
            'responsible_person_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'reminder_sent' => 'nullable|boolean'
        ]);
        
        $certificateLicense = CertificateLicense::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'status' => $request->status ?? 'active',
            'reminder_sent' => $request->reminder_sent ?? false
        ]));
        
        return response()->json(['message' => 'Certificate/License created', 'certificate_license' => $certificateLicense], 201);
    }
    
    public function show(Request $request, $id)
    {
        $certificateLicense = CertificateLicense::where('business_id', $request->user()->business_id)
            ->with(['responsiblePerson'])
            ->findOrFail($id);
            
        return response()->json(['certificate_license' => $certificateLicense]);
    }
    
    public function update(Request $request, $id)
    {
        $certificateLicense = CertificateLicense::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'certificate_number' => 'nullable|string|max:100',
            'issuing_authority' => 'nullable|string|max:100',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'status' => 'nullable|string|in:active,expired,revoked,pending_renewal',
            'file_path' => 'nullable|string|max:500',
            'file_name' => 'nullable|string|max:255',
            'file_type' => 'nullable|string|max:50',
            'file_size' => 'nullable|integer',
            'responsible_person_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'reminder_sent' => 'nullable|boolean'
        ]);
        
        $certificateLicense->update($request->all());
        
        return response()->json(['message' => 'Certificate/License updated', 'certificate_license' => $certificateLicense]);
    }
    
    public function destroy(Request $request, $id)
    {
        $certificateLicense = CertificateLicense::where('business_id', $request->user()->business_id)->findOrFail($id);
        $certificateLicense->delete();
        
        return response()->json(['message' => 'Certificate/License deleted']);
    }
}