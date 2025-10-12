<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index()
    {
        $machines = Machine::where('business_id', auth()->user()->business_id)->get();
        return view('machines.index', compact('machines'));
    }

    public function create()
    {
        return view('machines.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'model' => 'nullable|string|max:100',
            'status' => 'required|in:operational,maintenance,down',
        ]);

        $validated['business_id'] = auth()->user()->business_id;

        Machine::create($validated);

        return redirect()->route('machines.index')->with('success', 'Machine added successfully!');
    }

    public function show(Machine $machine)
    {
        return view('machines.show', compact('machine'));
    }

    public function edit(Machine $machine)
    {
        return view('machines.edit', compact('machine'));
    }

    public function update(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'model' => 'nullable|string|max:100',
            'status' => 'required|in:operational,maintenance,down',
        ]);

        $machine->update($validated);

        return redirect()->route('machines.index')->with('success', 'Machine updated successfully!');
    }

    public function destroy(Machine $machine)
    {
        $machine->delete();
        return redirect()->route('machines.index')->with('success', 'Machine deleted successfully!');
    }
}