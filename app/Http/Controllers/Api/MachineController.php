<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Machine;

class MachineController extends Controller
{
    public function index(Request $request)
    {
        $machines = Machine::where('business_id', $request->user()->business_id)->get();
        
        return response()->json([
            'machines' => $machines,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'status' => 'nullable|in:available,in_use,maintenance,broken',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $code = 'MCH-' . strtoupper(substr(uniqid(), -6));

        $machine = Machine::create([
            'name' => $request->name,
            'code' => $code,
            'type' => $request->type ?? 'other',
            'status' => $request->status ?? 'available',
            'location' => $request->location,
            'description' => $request->description,
            'business_id' => $request->user()->business_id,
        ]);

        return response()->json([
            'message' => 'Machine created successfully',
            'machine' => $machine,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $machine = Machine::where('business_id', $request->user()->business_id)
            ->findOrFail($id);

        return response()->json([
            'machine' => $machine,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'status' => 'nullable|in:available,in_use,maintenance,broken',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $machine = Machine::where('business_id', $request->user()->business_id)
            ->findOrFail($id);

        $machine->update($request->only([
            'name', 'type', 'status', 'location', 'description'
        ]));

        return response()->json([
            'message' => 'Machine updated successfully',
            'machine' => $machine,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $machine = Machine::where('business_id', $request->user()->business_id)
            ->findOrFail($id);

        $machine->delete();

        return response()->json([
            'message' => 'Machine deleted successfully',
        ]);
    }
}
