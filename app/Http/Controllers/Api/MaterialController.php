<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $materials = Material::where('business_id', $request->user()->business_id)->get();
        
        return response()->json([
            'materials' => $materials,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
            'gst_rate' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $code = $request->code ?? 'MAT-' . strtoupper(substr(uniqid(), -6));

        $material = Material::create([
            'name' => $request->name,
            'code' => $code,
            'unit' => $request->unit,
            'unit_price' => $request->unit_price ?? 0,
            'gst_rate' => $request->gst_rate ?? 18,
            'category' => $request->category,
            'description' => $request->description,
            'is_active' => true,
            'business_id' => $request->user()->business_id,
        ]);

        return response()->json([
            'message' => 'Material created successfully',
            'material' => $material,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $material = Material::where('business_id', $request->user()->business_id)
            ->findOrFail($id);

        return response()->json([
            'material' => $material,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
            'gst_rate' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $material = Material::where('business_id', $request->user()->business_id)
            ->findOrFail($id);

        $material->update($request->only([
            'name', 'unit', 'unit_price', 'gst_rate', 'category', 'description'
        ]));

        return response()->json([
            'message' => 'Material updated successfully',
            'material' => $material,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $material = Material::where('business_id', $request->user()->business_id)
            ->findOrFail($id);

        $material->delete();

        return response()->json([
            'message' => 'Material deleted successfully',
        ]);
    }
}
