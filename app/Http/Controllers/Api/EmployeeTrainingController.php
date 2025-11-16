<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeTraining;
use App\Models\TrainingProgram;
use App\Models\User;

class EmployeeTrainingController extends Controller
{
    public function index(Request $request)
    {
        $employeeTrainings = EmployeeTraining::where('business_id', $request->user()->business_id)
            ->with(['user', 'trainingProgram'])
            ->when($request->user_id, function($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->when($request->training_program_id, function($q) use ($request) {
                $q->where('training_program_id', $request->training_program_id);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->paginate(20);
            
        return response()->json($employeeTrainings);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'training_program_id' => 'required|exists:training_programs,id',
            'assigned_date' => 'required|date',
            'start_date' => 'nullable|date',
            'completion_date' => 'nullable|date',
            'score' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:assigned,in_progress,completed,overdue',
            'feedback' => 'nullable|string'
        ]);
        
        // Verify user belongs to the same business
        $user = User::where('business_id', $request->user()->business_id)
            ->findOrFail($request->user_id);
            
        // Verify training program belongs to the same business
        $trainingProgram = TrainingProgram::where('business_id', $request->user()->business_id)
            ->findOrFail($request->training_program_id);
        
        $employeeTraining = EmployeeTraining::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id
        ]));
        
        return response()->json(['message' => 'Employee training created', 'employee_training' => $employeeTraining], 201);
    }
    
    public function show(Request $request, $id)
    {
        $employeeTraining = EmployeeTraining::where('business_id', $request->user()->business_id)
            ->with(['user', 'trainingProgram'])
            ->findOrFail($id);
            
        return response()->json(['employee_training' => $employeeTraining]);
    }
    
    public function update(Request $request, $id)
    {
        $employeeTraining = EmployeeTraining::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'training_program_id' => 'sometimes|required|exists:training_programs,id',
            'assigned_date' => 'sometimes|required|date',
            'start_date' => 'nullable|date',
            'completion_date' => 'nullable|date',
            'score' => 'nullable|numeric|min:0|max:100',
            'status' => 'sometimes|required|in:assigned,in_progress,completed,overdue',
            'feedback' => 'nullable|string'
        ]);
        
        // Verify user belongs to the same business
        if ($request->user_id) {
            $user = User::where('business_id', $request->user()->business_id)
                ->findOrFail($request->user_id);
        }
            
        // Verify training program belongs to the same business
        if ($request->training_program_id) {
            $trainingProgram = TrainingProgram::where('business_id', $request->user()->business_id)
                ->findOrFail($request->training_program_id);
        }
        
        $employeeTraining->update($request->all());
        
        return response()->json(['message' => 'Employee training updated', 'employee_training' => $employeeTraining]);
    }
    
    public function destroy(Request $request, $id)
    {
        $employeeTraining = EmployeeTraining::where('business_id', $request->user()->business_id)->findOrFail($id);
        $employeeTraining->delete();
        
        return response()->json(['message' => 'Employee training deleted']);
    }
    
    public function completeTraining(Request $request, $id)
    {
        $employeeTraining = EmployeeTraining::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'completion_date' => 'required|date',
            'score' => 'nullable|numeric|min:0|max:100',
            'feedback' => 'nullable|string'
        ]);
        
        $employeeTraining->update([
            'completion_date' => $request->completion_date,
            'score' => $request->score,
            'status' => 'completed'
        ]);
        
        return response()->json(['message' => 'Training completed', 'employee_training' => $employeeTraining]);
    }
}