<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingProgram;

class TrainingProgramController extends Controller
{
    public function index(Request $request)
    {
        $trainingPrograms = TrainingProgram::where('business_id', $request->user()->business_id)
            ->when($request->search, function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%");
            })
            ->when($request->is_active, function($q) use ($request) {
                $q->where('is_active', $request->is_active);
            })
            ->paginate(20);
            
        return response()->json($trainingPrograms);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'trainer' => 'nullable|string|max:255',
            'duration_hours' => 'nullable|integer|min:0',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced',
            'is_active' => 'nullable|boolean'
        ]);
        
        $trainingProgram = TrainingProgram::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'difficulty_level' => $request->difficulty_level ?? 'beginner',
            'is_active' => $request->is_active ?? true
        ]));
        
        return response()->json(['message' => 'Training program created', 'training_program' => $trainingProgram], 201);
    }
    
    public function show(Request $request, $id)
    {
        $trainingProgram = TrainingProgram::where('business_id', $request->user()->business_id)
            ->with('trainingMaterials')
            ->findOrFail($id);
            
        return response()->json(['training_program' => $trainingProgram]);
    }
    
    public function update(Request $request, $id)
    {
        $trainingProgram = TrainingProgram::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'trainer' => 'nullable|string|max:255',
            'duration_hours' => 'nullable|integer|min:0',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced',
            'is_active' => 'nullable|boolean'
        ]);
        
        $trainingProgram->update($request->all());
        
        return response()->json(['message' => 'Training program updated', 'training_program' => $trainingProgram]);
    }
    
    public function destroy(Request $request, $id)
    {
        $trainingProgram = TrainingProgram::where('business_id', $request->user()->business_id)->findOrFail($id);
        $trainingProgram->delete();
        
        return response()->json(['message' => 'Training program deleted']);
    }
}