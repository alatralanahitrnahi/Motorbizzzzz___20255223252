<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $vendors = Vendor::where('business_id', $request->user()->business_id)->get();
        
        return response()->json([
            'vendors' => $vendors,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gstin' => 'nullable|string|max:15',
        ]);

        $vendor = Vendor::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'gstin' => $request->gstin,
            'business_id' => $request->user()->business_id,
        ]);

        return response()->json([
            'message' => 'Vendor created successfully',
            'vendor' => $vendor,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $vendor = Vendor::where('business_id', $request->user()->business_id)
            ->findOrFail($id);

        return response()->json([
            'vendor' => $vendor,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gstin' => 'nullable|string|max:15',
        ]);

        $vendor = Vendor::where('business_id', $request->user()->business_id)
            ->findOrFail($id);

        $vendor->update($request->only([
            'name', 'email', 'phone', 'address', 'gstin'
        ]));

        return response()->json([
            'message' => 'Vendor updated successfully',
            'vendor' => $vendor,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $vendor = Vendor::where('business_id', $request->user()->business_id)
            ->findOrFail($id);

        $vendor->delete();

        return response()->json([
            'message' => 'Vendor deleted successfully',
        ]);
    }
}
