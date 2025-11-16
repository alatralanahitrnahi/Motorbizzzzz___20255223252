<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPosition;
use App\Models\Department;

class JobPositionController extends Controller
{
    public function index(Request $request)
    {
        $jobPositions = JobPosition::where('business_id', $request->user()->business_id)
            ->with('department')
            ->when($request->search, function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%");
            })
            ->when($request->department_id, function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            })
            ->when($request->is_active, function($q) use ($request) {
                $q->where('is_active', $request->is_active);
            })
            ->paginate(20);
            
        return response()->json($jobPositions);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean'
        ]);
        
        // Verify department belongs to the same business
        $department = Department::where('business_id', $request->user()->business_id)
            ->findOrFail($request->department_id);
        
        $jobPosition = JobPosition::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'is_active' => $request->is_active ?? true
        ]));
        
        return response()->json(['message' => 'Job position created', 'job_position' => $jobPosition], 201);
    }
    
    public function show(Request $request, $id)
    {
        $jobPosition = JobPosition::where('business_id', $request->user()->business_id)
            ->with(['department', 'employees'])
            ->findOrFail($id);
            
        return response()->json(['job_position' => $jobPosition]);
    }
    
    public function update(Request $request, $id)
    {
        $jobPosition = JobPosition::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'department_id' => 'sometimes|required|exists:departments,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'employment_type' => 'sometimes|required|in:full_time,part_time,contract,intern',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean'
        ]);
        
        // Verify department belongs to the same business
        if ($request->department_id) {
            $department = Department::where('business_id', $request->user()->business_id)
                ->findOrFail($request->department_id);
        }
        
        $jobPosition->update($request->all());
        
        return response()->json(['message' => 'Job position updated', 'job_position' => $jobPosition]);
    }
    
    public function destroy(Request $request, $id)
    {
        $jobPosition = JobPosition::where('business_id', $request->user()->business_id)->findOrFail($id);
        $jobPosition->delete();
        
        return response()->json(['message' => 'Job position deleted']);
    }
}