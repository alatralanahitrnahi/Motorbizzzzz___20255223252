<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingMaterial;
use App\Models\TrainingProgram;

class TrainingMaterialController extends Controller
{
    public function index(Request $request)
    {
        $trainingMaterials = TrainingMaterial::whereHas('trainingProgram', function($q) use ($request) {
                $q->where('business_id', $request->user()->business_id);
            })
            ->with('trainingProgram')
            ->when($request->training_program_id, function($q) use ($request) {
                $q->where('training_program_id', $request->training_program_id);
            })
            ->paginate(20);
            
        return response()->json($trainingMaterials);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'training_program_id' => 'required|exists:training_programs,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'nullable|string',
            'file_type' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer'
        ]);
        
        // Verify training program belongs to the same business
        $trainingProgram = TrainingProgram::where('business_id', $request->user()->business_id)
            ->findOrFail($request->training_program_id);
        
        $trainingMaterial = TrainingMaterial::create($request->all());
        
        return response()->json(['message' => 'Training material created', 'training_material' => $trainingMaterial], 201);
    }
    
    public function show(Request $request, $id)
    {
        $trainingMaterial = TrainingMaterial::whereHas('trainingProgram', function($q) use ($request) {
                $q->where('business_id', $request->user()->business_id);
            })
            ->with('trainingProgram')
            ->findOrFail($id);
            
        return response()->json(['training_material' => $trainingMaterial]);
    }
    
    public function update(Request $request, $id)
    {
        $trainingMaterial = TrainingMaterial::whereHas('trainingProgram', function($q) use ($request) {
                $q->where('business_id', $request->user()->business_id);
            })->findOrFail($id);
        
        $request->validate([
            'training_program_id' => 'sometimes|required|exists:training_programs,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'nullable|string',
            'file_type' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer'
        ]);
        
        // Verify training program belongs to the same business
        if ($request->training_program_id) {
            $trainingProgram = TrainingProgram::where('business_id', $request->user()->business_id)
                ->findOrFail($request->training_program_id);
        }
        
        $trainingMaterial->update($request->all());
        
        return response()->json(['message' => 'Training material updated', 'training_material' => $trainingMaterial]);
    }
    
    public function destroy(Request $request, $id)
    {
        $trainingMaterial = TrainingMaterial::whereHas('trainingProgram', function($q) use ($request) {
                $q->where('business_id', $request->user()->business_id);
            })->findOrFail($id);
            
        $trainingMaterial->delete();
        
        return response()->json(['message' => 'Training material deleted']);
    }
}