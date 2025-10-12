<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MachineController extends Controller
{
    public function index()
    {
        $machines = Machine::with('workOrders')->paginate(10);
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
            'code' => 'required|string|max:50|unique:machines',
            'type' => 'required|in:cnc,lathe,welding,cutting,drilling,milling,other',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Machine::create($validated);

        return redirect()->route('machines.index')->with('success', 'Machine created successfully.');
    }

    public function show(Machine $machine)
    {
        $machine->load(['workOrders' => function($query) {
            $query->latest()->take(10);
        }]);
        
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
            'code' => 'required|string|max:50|unique:machines,code,' . $machine->id,
            'type' => 'required|in:cnc,lathe,welding,cutting,drilling,milling,other',
            'status' => 'required|in:available,in_use,maintenance,broken',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $machine->update($validated);

        return redirect()->route('machines.index')->with('success', 'Machine updated successfully.');
    }

    public function destroy(Machine $machine)
    {
        $machine->delete();
        return redirect()->route('machines.index')->with('success', 'Machine deleted successfully.');
    }
}