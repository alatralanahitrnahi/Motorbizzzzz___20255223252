<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SkillAssessment;
use App\Models\User;

class SkillAssessmentController extends Controller
{
    public function index(Request $request)
    {
        $skillAssessments = SkillAssessment::where('business_id', $request->user()->business_id)
            ->with(['user', 'assessedBy'])
            ->when($request->user_id, function($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->when($request->skill_name, function($q) use ($request) {
                $q->where('skill_name', 'like', "%{$request->skill_name}%");
            })
            ->paginate(20);
            
        return response()->json($skillAssessments);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'skill_name' => 'required|string|max:255',
            'proficiency_level' => 'required|in:beginner,intermediate,advanced,expert',
            'description' => 'nullable|string',
            'assessed_date' => 'required|date',
            'assessed_by' => 'nullable|exists:users,id',
            'score' => 'nullable|numeric|min:0|max:100',
            'comments' => 'nullable|string',
            'next_review_date' => 'nullable|date'
        ]);
        
        // Verify user belongs to the same business
        $user = User::where('business_id', $request->user()->business_id)
            ->findOrFail($request->user_id);
            
        // Verify assessor belongs to the same business
        if ($request->assessed_by) {
            $assessor = User::where('business_id', $request->user()->business_id)
                ->findOrFail($request->assessed_by);
        }
        
        $skillAssessment = SkillAssessment::create(array_merge($request->all(), [
            'business_id' => $request->user()->business_id,
            'assessed_by' => $request->assessed_by ?? $request->user()->id
        ]));
        
        return response()->json(['message' => 'Skill assessment created', 'skill_assessment' => $skillAssessment], 201);
    }
    
    public function show(Request $request, $id)
    {
        $skillAssessment = SkillAssessment::where('business_id', $request->user()->business_id)
            ->with(['user', 'assessedBy'])
            ->findOrFail($id);
            
        return response()->json(['skill_assessment' => $skillAssessment]);
    }
    
    public function update(Request $request, $id)
    {
        $skillAssessment = SkillAssessment::where('business_id', $request->user()->business_id)->findOrFail($id);
        
        $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'skill_name' => 'sometimes|required|string|max:255',
            'proficiency_level' => 'sometimes|required|in:beginner,intermediate,advanced,expert',
            'description' => 'nullable|string',
            'assessed_date' => 'sometimes|required|date',
            'assessed_by' => 'nullable|exists:users,id',
            'score' => 'nullable|numeric|min:0|max:100',
            'comments' => 'nullable|string',
            'next_review_date' => 'nullable|date'
        ]);
        
        // Verify user belongs to the same business
        if ($request->user_id) {
            $user = User::where('business_id', $request->user()->business_id)
                ->findOrFail($request->user_id);
        }
            
        // Verify assessor belongs to the same business
        if ($request->assessed_by) {
            $assessor = User::where('business_id', $request->user()->business_id)
                ->findOrFail($request->assessed_by);
        }
        
        $skillAssessment->update($request->all());
        
        return response()->json(['message' => 'Skill assessment updated', 'skill_assessment' => $skillAssessment]);
    }
    
    public function destroy(Request $request, $id)
    {
        $skillAssessment = SkillAssessment::where('business_id', $request->user()->business_id)->findOrFail($id);
        $skillAssessment->delete();
        
        return response()->json(['message' => 'Skill assessment deleted']);
    }
}