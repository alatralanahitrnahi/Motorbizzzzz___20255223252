<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\InventoryBatch;
use App\Models\InventoryTransaction;
use App\Models\Material;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Barcode;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Damage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Models\Warehouse;
use App\Models\WarehouseSlot; // ✅ <-- Add this line



class InventoryController extends Controller
{
    private const CACHE_DURATION = 3600; // 1 hour

    public function items(): ViewContract
    {
        $items = InventoryBatch::all();
        return view('inventory.items', compact('items'));
    }

    public function index(): ViewContract
    {
        $query = InventoryBatch::query()->with(['material', 'purchaseOrder.vendor']);

        // Apply filters
        $this->applyFilters($query);

        $batches = $query->latest('created_at')->paginate(15);

        $materials = Material::query()
            ->where('is_available', true)
            ->orderBy('name')
            ->get();

        $statuses = ['active', 'expired', 'damaged', 'exhausted'];

        // Optimized stats calculation
        $stats = $this->calculateStats();

        return view('inventory.index', compact('batches', 'materials', 'statuses', 'stats'));
    }
public function create(Request $request)
{
    $vendors = Cache::remember('active_vendors', self::CACHE_DURATION, function () {
        return Vendor::select('id', 'name', 'business_name', 'email', 'phone', 'company_address', 'warehouse_address')
            ->orderBy('name')
            ->get();
    });

    $materials = Cache::remember('active_materials', self::CACHE_DURATION, function () {
        return Material::where('is_available', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'unit', 'unit_price', 'gst_rate', 'unit']); // 👈 Add weight info
    });

    $purchaseOrders = PurchaseOrder::select('id', 'po_number', 'vendor_id')
        ->with('vendor:id,name,business_name')
        ->orderBy('po_number', 'desc')
        ->get();

    // ✅ Load full warehouse info (capacity, current_load, etc.)
    $warehouses = Warehouse::select('id', 'name', 'capacity', 'current_load', 'available_space')
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    $selectedPoItems = [];
    $selectedPoData = null;

    if ($request->filled('purchase_order_id')) {
        $selectedPoItems = PurchaseOrderItem::where('purchase_order_id', $request->purchase_order_id)
            ->select('id', 'material_id', 'item_name', 'quantity', 'unit_price', 'total_price', 'batch_no', 'expiry_date')
            ->get();

        $selectedPoData = PurchaseOrder::with('vendor')->find($request->purchase_order_id);
    }

    $suggestedBatchNumber = $this->createUniqueBatchNumber();

    return view('inventory.create', compact(
        'vendors',
        'materials',
        'purchaseOrders',
        'selectedPoItems',
        'selectedPoData',
        'suggestedBatchNumber',
        'warehouses'
    ));
}

public function store(Request $request): RedirectResponse
{
    try {
        $validated = $this->validateStoreRequest($request);

        DB::beginTransaction();

        // ✅ Get the warehouse
        $warehouse = Warehouse::findOrFail($validated['warehouse_id']);

        // ✅ Get the material and calculate weight
$material = Material::select('id')->findOrFail($validated['material_id']);
        $materialWeight = $material->weight_per_unit * $validated['received_quantity'];

        // ✅ Check warehouse capacity
        if ($warehouse->current_load + $materialWeight > $warehouse->capacity) {
            return back()->withInput()->with('error', 'Warehouse capacity exceeded. Cannot accept more material.');
        }

        // ✅ Create the batch
        $batch = $this->createInventoryBatch(array_merge($validated, [
            'received_weight' => $materialWeight,
            'current_weight' => $materialWeight,
        ]));

        if (!$batch || !$batch->exists) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create inventory batch.');
        }

        // ✅ Assign batch to first available slot
        $slot = WarehouseSlot::where('status', 'empty')
            ->whereHas('block', function ($query) use ($validated) {
                $query->where('warehouse_id', $validated['warehouse_id']);
            })
            ->first();

        if ($slot) {
            $slot->batch_id = $batch->id;
            $slot->status = 'full'; // Or 'partial' if tracking volume
            $slot->save();
        } else {
            DB::rollBack();
            return back()->withInput()->with('error', 'No empty slot available in the selected warehouse.');
        }

        // ✅ Create transaction log
        $this->createInitialTransaction($batch, $validated);

        // ✅ Update warehouse load
        $warehouse->current_load += $materialWeight;
        $warehouse->available_space = $warehouse->capacity - $warehouse->current_load;
        $warehouse->save();

        DB::commit();

        return $request->has('save_and_new')
            ? redirect()->route('inventory.create')->with('success', 'Inventory batch created! You can add another.')
            : redirect()->route('inventory.index')->with('success', 'Inventory batch created successfully!');

    } catch (ValidationException $e) {
        DB::rollBack();
        return back()->withErrors($e->validator)->withInput();

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('🔥 Exception during batch creation', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->withInput()->with('error', 'Error creating batch: ' . $e->getMessage());
    }
}

  private function calculateMaterialWeight($materialId, $quantity): float
{
    $material = Material::select('id', 'weight_per_unit')->findOrFail($materialId);

    // Make sure both values are numeric
    $weightPerUnit = (float) $material->weight_per_unit;
    $qty = (float) $quantity;

    return $weightPerUnit * $qty;
}


 public function getRemainingQuantity(Request $request)
    {
        $poId = $request->query('purchase_order_id');
        $materialId = $request->query('material_id');

        if (!$poId) {
            return response()->json(['error' => 'Missing purchase_order_id.'], 400);
        }

        try {
            $poItemQuery = PurchaseOrderItem::with('material')->where('purchase_order_id', $poId);
            if ($materialId) {
                $poItemQuery->where('material_id', $materialId);
            }

            $poItem = $poItemQuery->first();

            if (!$poItem) {
                return response()->json([
                    'remaining_quantity' => 0,
                    'ordered_quantity' => 0,
                    'received_quantity' => 0,
                    'unit_price' => 0,
                  //  'material_id' => null,
                ]);
            }

            $receivedQty = InventoryBatch::where('purchase_order_id', $poId)
                ->where('material_id', $poItem->material_id)
                ->sum('received_quantity');

            $orderedQty = $poItem->quantity;
            $remainingQty = max(0, $orderedQty - $receivedQty);

            return response()->json([
                'ordered_quantity' => (float) $orderedQty,
                'received_quantity' => (float) $receivedQty,
                'remaining_quantity' => (float) $remainingQty,
                'unit_price' => (float) $poItem->unit_price,
                'material_id' => $poItem->material_id,
                'material_name' => $poItem->material->name ?? $poItem->item_name ?? 'N/A',
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching PO remaining quantity: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch PO data.',
                'remaining_quantity' => 0,
                'ordered_quantity' => 0,
                'received_quantity' => 0,
                'unit_price' => 0,
            ], 500);
        }
    } 

    // 🔧 FIXED: Simplified validation method
    private function validateStoreRequest(Request $request): array
    {
        $rules = [
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'material_id' => 'required|exists:materials,id',
            'warehouse_id' => 'required|exists:warehouses,id', // ✅ NEW
            'batch_number' => 'nullable|string|unique:inventory_batches,batch_number',
            'supplier_batch_number' => 'nullable|string|max:255',
            'initial_quantity' => 'required|numeric|min:0.01',
            'quality_grade' => 'nullable|string|in:A,B,C,Premium,Standard',
            'received_date' => 'required|date|before_or_equal:today',
            'expiry_date' => 'nullable|date|after:received_date',
            'notes' => 'nullable|string|max:1000',
        ];

        $validated = $request->validate($rules);

        // 🔧 FIXED: Simple data processing
        if (empty($validated['batch_number'])) {
            $validated['batch_number'] = $this->createUniqueBatchNumber();
        }

        // Map initial_quantity to database fields
        $validated['received_quantity'] = $validated['initial_quantity'];
        $validated['current_quantity'] = $validated['initial_quantity'];

        // Get unit price from material if not provided
        if (!isset($validated['unit_price'])) {
            $material = Material::find($validated['material_id']);
            $validated['unit_price'] = $material ? $material->unit_price : 0;
        }

        // 🔧 FIXED: Validate PO constraints if PO is selected
        if ($validated['purchase_order_id']) {
            $this->validatePurchaseOrderConstraints($validated);
        }

        return $validated;
    }

    // 🔧 FIXED: Separate PO validation method
    private function validatePurchaseOrderConstraints(array &$validated): void
    {
        $poItem = PurchaseOrderItem::where('purchase_order_id', $validated['purchase_order_id'])
            ->where('material_id', $validated['material_id'])
            ->first();

        if (!$poItem) {
            throw ValidationException::withMessages([
                'material_id' => 'Selected material is not part of this Purchase Order.'
            ]);
        }

        // Check remaining quantity
        $receivedQty = InventoryBatch::where('purchase_order_id', $validated['purchase_order_id'])
            ->where('material_id', $validated['material_id'])
            ->sum('received_quantity');

        $remainingQty = $poItem->quantity - $receivedQty;

        if ($validated['received_quantity'] > $remainingQty) {
            throw ValidationException::withMessages([
                'initial_quantity' => "Cannot receive {$validated['received_quantity']} units. Only {$remainingQty} units remaining for this PO."
            ]);
        }

        // Set unit price from PO
        $validated['unit_price'] = $poItem->unit_price;
    }

    // 🔧 FIXED: Simplified batch creation
    private function createInventoryBatch(array $validated): InventoryBatch
    {
        Log::debug('📥 Creating inventory batch with data:', $validated);

        $batchData = [
            'purchase_order_id' => $validated['purchase_order_id'] ?? null,
            'material_id' => $validated['material_id'],
            'warehouse_id' => $validated['warehouse_id'], // ✅ REQUIRED!
            'batch_number' => $validated['batch_number'],
            'supplier_batch_number' => $validated['supplier_batch_number'] ?? null,
            'received_quantity' => $validated['received_quantity'],
            'current_quantity' => $validated['current_quantity'],
            'unit_price' => $validated['unit_price'] ?? 0,
            'quality_grade' => $validated['quality_grade'] ?? null,
            'received_date' => $validated['received_date'],
            'expiry_date' => $validated['expiry_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'active',
        ];

        $batch = InventoryBatch::create($batchData);

        Log::debug('✅ Created inventory batch:', [
            'id' => $batch->id,
            'batch_number' => $batch->batch_number,
            'material_id' => $batch->material_id
        ]);

        return $batch;
    }

    // 🔧 FIXED: Simplified transaction creation
    private function createInitialTransaction(InventoryBatch $batch, array $validated): void
    {
        $transactionData = [
            'transaction_id' => InventoryTransaction::generateTransactionId(),
            'batch_id' => $batch->id,
            'type' => 'intake',
            'weight' => $validated['received_quantity'],
            'quantity' => $validated['received_quantity'],
            'unit_price' => $validated['unit_price'] ?? 0,
            'total_value' => $validated['received_quantity'] * ($validated['unit_price'] ?? 0),
            'reference_number' => $batch->batch_number,
            'transaction_date' => $validated['received_date'],
            'notes' => 'Initial batch creation - ' . ($validated['notes'] ?? ''),
        ];

        InventoryTransaction::create($transactionData);

        Log::debug('✅ Created initial transaction for batch:', [
            'batch_id' => $batch->id,
            'quantity' => $validated['received_quantity']
        ]);
    }
  

  // Show inventory map with batches and warehouses + slots
    public function showInventoryMap(Request $request)
    {
        $batches = Batch::all();
        $warehouses = Warehouse::with('blocks.slots')->get();
        return view('inventory.map', compact('batches', 'warehouses'));
    }

    // Assign a batch to a warehouse slot and update its status
    public function assignBatchToSlot(Request $request)
    {
        $slot = WarehouseSlot::find($request->slot_id);
        if (!$slot) {
            return redirect()->back()->with('error', 'Slot not found.');
        }

        $slot->batch_id = $request->batch_id;
        $slot->status = $request->status; // expected values: 'full', 'partial', 'empty'
        $slot->save();

        return redirect()->back()->with('success', 'Batch assigned to slot successfully.');
    }
  
    // Keep existing methods for show, edit, update, destroy, dispatch, damage, etc.
    public function show(InventoryBatch $inventory): ViewContract
    {
        $inventory->load(['material', 'purchaseOrder.vendor', 'transactions']);

        $transactions = $inventory->transactions()
            ->latest('transaction_date')
            ->get();

        $summary = $this->calculateTransactionSummary($transactions);

        $batch = $inventory;
        return view('inventory.show', compact('batch', 'transactions', 'summary'));
    }

  public function edit(InventoryBatch $inventory)
{
    if (in_array($inventory->status, ['exhausted'])) {
        return redirect()->route('inventory.index')
            ->with('error', 'Cannot edit exhausted batches.');
    }

    $materials = Material::where('is_available', true)
        ->orderBy('name')
        ->get();

    $purchaseOrders = PurchaseOrder::with('vendor')
        ->whereIn('status', ['approved', 'pending'])
        ->orderBy('po_number')
        ->get();

    $batch = $inventory->load('purchaseOrder.items');

    $selectedPoItems = $batch->purchaseOrder && $batch->purchaseOrder->items
        ? $batch->purchaseOrder->items
        : collect();

    $orderedQty = 0;
    $totalReceivedQty = 0;
    $remainingQty = 0;

    if ($batch->purchase_order_id && $batch->material_id) {
        $poItem = PurchaseOrderItem::where('purchase_order_id', $batch->purchase_order_id)
            ->where('material_id', $batch->material_id)
            ->first();

        $orderedQty = $poItem ? $poItem->quantity : 0;

        $totalReceivedQty = InventoryBatch::where('purchase_order_id', $batch->purchase_order_id)
            ->where('material_id', $batch->material_id)
            ->where('id', '!=', $batch->id)
            ->sum('received_quantity');

        $remainingQty = max(0, $orderedQty - $totalReceivedQty);
    }

    // ✅ Add this line to load warehouses
    $warehouses = Warehouse::select('id', 'name')->orderBy('name')->get();

    return view('inventory.edit', compact(
        'batch',
        'materials',
        'purchaseOrders',
        'selectedPoItems',
        'orderedQty',
        'totalReceivedQty',
        'remainingQty',
        'warehouses' // ✅ Add to view
    ));
}


    public function update(Request $request, InventoryBatch $inventory): RedirectResponse
    {
        try {
            $validated = $this->validateUpdateRequest($request, $inventory);

            $inventory->update($validated);

            return redirect()->route('inventory.index')
                ->with('success', 'Inventory batch updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating inventory batch: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error updating inventory batch: ' . $e->getMessage());
        }
    }

    protected function validateUpdateRequest(Request $request, InventoryBatch $inventory): array
    {
        // Map the Blade field 'initial_quantity' to 'received_quantity' before validation
        $request->merge([
            'received_quantity' => $request->input('initial_quantity'),
        ]);

        $validated = $request->validate([
            'batch_number' => [
                'required',
                'string',
                Rule::unique('inventory_batches', 'batch_number')->ignore($inventory->id)
            ],
            'supplier_batch_number' => 'nullable|string|max:255',
            'received_quantity' => 'required|numeric|min:0',
            'quality_grade' => 'nullable|string|in:A,B,C,Premium,Standard',
            'received_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:received_date',
            'notes' => 'nullable|string|max:1000',
              'warehouse_id' => 'required|exists:warehouses,id', // ✅ Added line

        ]);

        if ($inventory->purchase_order_id && $inventory->material_id) {
            $poItem = PurchaseOrderItem::where('purchase_order_id', $inventory->purchase_order_id)
                ->where('material_id', $inventory->material_id)
                ->first();

            $orderedQty = $poItem ? $poItem->quantity : 0;

            $otherReceived = InventoryBatch::where('purchase_order_id', $inventory->purchase_order_id)
                ->where('material_id', $inventory->material_id)
                ->where('id', '!=', $inventory->id)
                ->sum('received_quantity');

            $newTotalReceived = $otherReceived + $validated['received_quantity'];

            if ($newTotalReceived > $orderedQty) {
                throw ValidationException::withMessages([
                    'initial_quantity' => "Cannot add more. Remaining quantity is 0 out of ordered quantity({$orderedQty})."
                ]);
            }
        }

        return $validated;
    }

    public function destroy(InventoryBatch $inventory): RedirectResponse
{
    try {
        DB::beginTransaction();

        // ✅ Step 1: Get weight of material being removed
        $material = Material::findOrFail($inventory->material_id);
        $warehouse = Warehouse::findOrFail($inventory->warehouse_id);

        // Fallback if `weight_per_unit` is missing
        $weightPerUnit = floatval($material->weight_per_unit ?? 1);
        $batchWeight = $weightPerUnit * floatval($inventory->current_quantity); // or received_quantity if preferred

        // ✅ Step 2: Update warehouse current_load and available_space
        $warehouse->current_load = max(0, $warehouse->current_load - $batchWeight);
        $warehouse->available_space = $warehouse->capacity - $warehouse->current_load;
        $warehouse->save();

        // ✅ Step 3: Delete related transactions and batch
        $inventory->transactions()->delete();
        $inventory->delete();

        DB::commit();

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory batch deleted and warehouse updated!');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error deleting inventory batch: ' . $e->getMessage());
        return redirect()->route('inventory.index')
            ->with('error', 'Error deleting inventory batch: ' . $e->getMessage());
    }
}

    private function applyFilters($query): void
    {
        $query->when(request('status'), fn($q) => $q->where('status', request('status')))
            ->when(request('material'), fn($q) => $q->whereHas('material', fn($sub) => 
                $sub->where('name', 'like', '%' . request('material') . '%')))
            ->when(request('batch_number'), fn($q) => 
                $q->where('batch_number', 'like', '%' . request('batch_number') . '%'))
            ->when(request('storage_location'), fn($q) => 
                $q->where('storage_location', 'like', '%' . request('storage_location') . '%'));
    }

    private function calculateStats(): array
    {
        $activeBatches = InventoryBatch::where('status', 'active')->get();

        return [
            'total_batches' => InventoryBatch::count(),
            'active_batches' => $activeBatches->count(),
            'expired_batches' => InventoryBatch::where(function ($query) {
                $query->whereDate('expiry_date', '<=', today())
                      ->orWhere('status', 'expired');
            })->count(),
            'low_stock_count' => $activeBatches->where('current_quantity', '<', 10)->count(),
            'total_value' => $activeBatches->sum(function ($batch) {
                return $batch->current_quantity * ($batch->unit_price ?? 0);
            }),
        ];
    }

    private function calculateTransactionSummary($transactions): array
    {
        return [
            'total_intake' => $transactions->where('type', 'intake')->sum('quantity'),
            'total_dispatch' => $transactions->where('type', 'dispatch')->sum('quantity'),
            'total_damage' => $transactions->where('type', 'damage')->sum('quantity'),
            'total_adjustment' => $transactions->where('type', 'adjustment')->sum('quantity')
        ];
    }

    private function createUniqueBatchNumber(int $attempt = 1): string
    {
        if ($attempt > 10) {
            throw new \Exception('Unable to generate unique batch number after multiple attempts');
        }

        $prefix = 'BATCH';
        $date = now()->format('ymd');
        $random = strtoupper(Str::random(4));

        $batchNumber = "{$prefix}-{$date}-{$random}";

        if ($this->batchNumberExists($batchNumber)) {
            return $this->createUniqueBatchNumber($attempt + 1);
        }

        return $batchNumber;
    }

    private function batchNumberExists(string $batchNumber): bool
    {
        return InventoryBatch::where('batch_number', $batchNumber)->exists();
    }

    // Keep dispatch and damage methods as they were working
    public function showDispatch(int $id): ViewContract
    {
        $batch = InventoryBatch::with(['material', 'purchaseOrder.vendor'])->findOrFail($id);
        return view('inventory.dispatch', compact('batch'));
    }

    public function dispatch(Request $request): RedirectResponse
    {
        try {
            $validated = $this->validateDispatchRequest($request);
            $batch = InventoryBatch::findOrFail($validated['batch_id']);

            // Handle custom dispatch destination
            $dispatchTo = $validated['dispatch_to'];
            if ($dispatchTo === 'Other' && !empty($validated['custom_dispatch_to'])) {
                $dispatchTo = $validated['custom_dispatch_to'];
            }

            // Check if batch is available for dispatch
            if (!in_array($batch->status, ['active'])) {
                return redirect()->route('inventory.index')
                    ->with('error', 'Cannot dispatch from this batch. Status: ' . $batch->status);
            }

            // Check stock availability
            if ($batch->current_quantity < $validated['quantity']) {
                return redirect()->route('inventory.index')
                    ->with('error', 'Insufficient stock! Available: ' . $batch->current_quantity);
            }

            DB::beginTransaction();

            // Create dispatch transaction with processed dispatch_to
            $validated['dispatch_to'] = $dispatchTo;
            $this->createDispatchTransaction($batch, $validated);
            
            // Update batch quantities and status
            $this->updateBatchAfterDispatch($batch, $validated);

            DB::commit();

            return redirect()->route('inventory.index')
                ->with('success', 'Material dispatched successfully! Quantity: ' . $validated['quantity'] . ' to ' . $dispatchTo);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error dispatching material: ' . $e->getMessage());
            
            return redirect()->route('inventory.index')
                ->with('error', 'Error dispatching material: ' . $e->getMessage());
        }
    }

    public function markDamaged(Request $request): RedirectResponse
    {
        try {
            $validated = $this->validateDamageRequest($request);
            $batch = InventoryBatch::findOrFail($validated['batch_id']);

            // Check if batch is available for damage recording
            if (!in_array($batch->status, ['active', 'expired'])) {
                return redirect()->route('inventory.index')
                    ->with('error', 'Cannot record damage for this batch. Status: ' . $batch->status);
            }

            // Check stock availability
            if ($batch->current_quantity < $validated['quantity']) {
                return redirect()->route('inventory.index')
                    ->with('error', 'Damage quantity exceeds available stock! Available: ' . $batch->current_quantity);
            }

            DB::beginTransaction();

            // Create damage transaction
            $this->createDamageTransaction($batch, $validated);
            
            // Update batch quantities and status
            $this->updateBatchAfterDamage($batch, $validated);

            DB::commit();

            return redirect()->route('inventory.index')
                ->with('success', 'Damage recorded successfully! Quantity: ' . $validated['quantity']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error recording damage: ' . $e->getMessage());
            
            return redirect()->route('inventory.index')
                ->with('error', 'Error recording damage: ' . $e->getMessage());
        }
    }

    private function validateDispatchRequest(Request $request): array
    {
        return $request->validate([
            'batch_id' => 'required|exists:inventory_batches,id',
            'quantity' => 'required|numeric|min:0.001',
            'weight' => 'required|numeric|min:0.001',
            'dispatch_to' => 'required|string|max:255',
            'custom_dispatch_to' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'transaction_date' => 'nullable|date',
        ]);
    }

    private function validateDamageRequest(Request $request): array
    {
        return $request->validate([
            'batch_id' => 'required|exists:inventory_batches,id',
            'weight' => 'required|numeric|min:0.001',
            'quantity' => 'required|integer|min:1',
            'damage_type' => 'required|in:expired,contaminated,physical_damage,other',
            'reason' => 'required|string|max:500'
        ]);
    }

    private function createDispatchTransaction(InventoryBatch $batch, array $validated): void
    {
        InventoryTransaction::create([
            'transaction_id' => InventoryTransaction::generateTransactionId(),
            'batch_id' => $validated['batch_id'],
            'type' => 'dispatch',
            'weight' => $validated['weight'],
            'quantity' => $validated['quantity'],
            'unit_price' => $batch->unit_price,
            'total_value' => $validated['quantity'] * $batch->unit_price,
            'reference_number' => $validated['reference_number'] ?? null,
            'dispatch_to' => $validated['dispatch_to'],
            'purpose' => $validated['purpose'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'transaction_date' => $validated['transaction_date'] ?? now(),
        ]);
    }

    private function updateBatchAfterDispatch(InventoryBatch $batch, array $validated): void
    {
        $newQuantity = max(0, $batch->current_quantity - $validated['quantity']);
        $newWeight = max(0, $batch->current_weight - $validated['weight']);

        $status = $batch->status;
        if ($newQuantity <= 0) {
            $status = 'exhausted';
        }

        $batch->update([
            'current_weight' => $newWeight,
            'current_quantity' => $newQuantity,
            'status' => $status
        ]);
    }

    private function createDamageTransaction(InventoryBatch $batch, array $validated): void
    {
        InventoryTransaction::create([
            'transaction_id' => InventoryTransaction::generateTransactionId(),
            'batch_id' => $batch->id,
            'type' => 'damage',
            'weight' => $validated['weight'],
            'quantity' => $validated['quantity'],
            'unit_price' => $batch->unit_price,
            'total_value' => $validated['quantity'] * $batch->unit_price,
            'damage_type' => $validated['damage_type'],
            'reason' => $validated['reason'],
            'transaction_date' => now()
        ]);
    }

    private function updateBatchAfterDamage(InventoryBatch $batch, array $validated): void
    {
        $newQuantity = max(0, $batch->current_quantity - $validated['quantity']);
        $newWeight = max(0, $batch->current_weight - $validated['weight']);

        $status = $batch->status;
        if ($newQuantity <= 0) {
            $status = 'damaged';
        }

        $batch->update([
            'current_weight' => $newWeight,
            'current_quantity' => $newQuantity,
            'status' => $status
        ]);
    }
}