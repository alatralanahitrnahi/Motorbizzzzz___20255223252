<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\Machine;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function index()
    {
        $workOrders = WorkOrder::where('business_id', auth()->user()->business_id)
            ->with('machine')
            ->latest()
            ->get();
        return view('work-orders.index', compact('workOrders'));
    }

    public function create()
    {
        $machines = Machine::where('business_id', auth()->user()->business_id)->get();
        return view('work-orders.create', compact('machines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_number' => 'required|string|max:50',
            'machine_id' => 'required|exists:machines,id',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        $validated['business_id'] = auth()->user()->business_id;
        $validated['status'] = 'pending';
        $validated['created_by'] = auth()->id();

        WorkOrder::create($validated);

        return redirect()->route('work-orders.index')->with('success', 'Work order created successfully!');
    }

    public function show(WorkOrder $workOrder)
    {
        return view('work-orders.show', compact('workOrder'));
    }

    public function start(WorkOrder $workOrder)
    {
        $workOrder->update([
            'status' => 'in_progress',
            'started_at' => now(),
            'started_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Work order started!');
    }

    public function complete(WorkOrder $workOrder)
    {
        $workOrder->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Work order completed!');
    }
}