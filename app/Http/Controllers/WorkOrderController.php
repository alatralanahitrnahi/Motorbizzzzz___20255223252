<?php

namespace App\Http\Controllers;

use App\Models\{WorkOrder, Machine, Material, MaterialConsumption};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};

class WorkOrderController extends Controller
{
    public function index()
    {
        $workOrders = WorkOrder::with(['machine', 'operator'])
            ->latest()
            ->paginate(10);
            
        return view('work_orders.index', compact('workOrders'));
    }

    public function create()
    {
        $machines = Machine::available()->get();
        $materials = Material::available()->get();
        
        return view('work_orders.create', compact('machines', 'materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.planned_quantity' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($validated) {
            // Generate WO number
            $woNumber = 'WO-' . now()->format('Ymd') . '-' . str_pad(WorkOrder::count() + 1, 4, '0', STR_PAD_LEFT);

            // Create work order
            $workOrder = WorkOrder::create([
                'wo_number' => $woNumber,
                'machine_id' => $validated['machine_id'],
                'operator_id' => Auth::id(),
                'product_name' => $validated['product_name'],
                'quantity' => $validated['quantity'],
                'status' => 'pending',
            ]);

            // Create material consumptions
            foreach ($validated['materials'] as $material) {
                MaterialConsumption::create([
                    'work_order_id' => $workOrder->id,
                    'material_id' => $material['material_id'],
                    'planned_quantity' => $material['planned_quantity'],
                ]);
            }
        });

        return redirect()->route('work-orders.index')->with('success', 'Work order created successfully.');
    }

    public function show(WorkOrder $workOrder)
    {
        $workOrder->load(['machine', 'operator', 'materialConsumptions.material']);
        return view('work_orders.show', compact('workOrder'));
    }

    public function start(WorkOrder $workOrder)
    {
        if ($workOrder->status !== 'pending') {
            return back()->with('error', 'Work order cannot be started.');
        }

        DB::transaction(function () use ($workOrder) {
            $workOrder->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            // Update machine status
            $workOrder->machine->update(['status' => 'in_use']);
        });

        return back()->with('success', 'Work order started successfully.');
    }

    public function complete(Request $request, WorkOrder $workOrder)
    {
        if ($workOrder->status !== 'in_progress') {
            return back()->with('error', 'Work order cannot be completed.');
        }

        $validated = $request->validate([
            'materials' => 'required|array',
            'materials.*.actual_quantity' => 'required|numeric|min:0',
            'materials.*.waste_quantity' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($workOrder, $validated) {
            // Update material consumptions
            foreach ($validated['materials'] as $id => $data) {
                MaterialConsumption::where('id', $id)->update([
                    'actual_quantity' => $data['actual_quantity'],
                    'waste_quantity' => $data['waste_quantity'] ?? 0,
                ]);
            }

            // Complete work order
            $workOrder->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Free up machine
            $workOrder->machine->update(['status' => 'available']);
        });

        return redirect()->route('work-orders.show', $workOrder)->with('success', 'Work order completed successfully.');
    }
}