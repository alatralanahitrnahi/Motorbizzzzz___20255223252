<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::where('business_id', $request->user()->business_id)
            ->with('manager')
            ->when($request->search, function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            })
            ->when($request->is_active, function($q) use ($request) {
                $q->where('is_active', $request->is_active);
            })
            ->paginate(20);
            
        return response()->json($departments);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean'
        ]);
        
        // Verify manager belongs to the same business
        if ($request->manager_id) {
            $manager = User::where('business_id', $request->user()->business_id)
                ->findOrFail($request->manager_id);
        }
        
        $department = Department::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'is_active' => $request->is_active ?? true
        ]));
        
        return response()->json(['message' => 'Department created', 'department' => $department], 201);
    }
    
    public function show(Request $request, $id)
    {
        $department = Department::where('business_id', $request->user()->business_id)
            ->with(['manager', 'jobPositions', 'employees'])
            ->findOrFail($id);
            
        return response()->json(['department' => $department]);
    }
    
    public function update(Request $request, $id)
    {
        $department = Department::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean'
        ]);
        
        // Verify manager belongs to the same business
        if ($request->manager_id) {
            $manager = User::where('business_id', $request->user()->business_id)
                ->findOrFail($request->manager_id);
        }
        
        $department->update($request->all());
        
        return response()->json(['message' => 'Department updated', 'department' => $department]);
    }
    
    public function destroy(Request $request, $id)
    {
        $department = Department::where('business_id', $request->user()->business_id)->findOrFail($id);
        $department->delete();
        
        return response()->json(['message' => 'Department deleted']);
    }
}