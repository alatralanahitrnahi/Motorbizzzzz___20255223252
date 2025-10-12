<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\{PurchaseOrder, PurchaseOrderItem, Vendor, Material, InventoryBatch, Warehouse};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Cache, Log, Auth};
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Notifications\PurchaseOrderCreated;
use App\Notifications\PurchaseOrderUpdated;
use App\Notifications\PurchaseOrderDeleted;
use App\Models\User;
use App\Models\MaterialVendor;


class PurchaseOrderController extends Controller
{
    private const CACHE_DURATION = 60;

    private const BASE_RULES = [
        'vendor_id' => 'required|exists:vendors,id',
        'order_date' => 'required|date',
        'po_date' => 'nullable|date',
        'expected_delivery' => 'nullable|date|after_or_equal:order_date',
        'payment_mode' => 'nullable|in:cash,bank_transfer,cheque',
        'credit_days' => 'nullable|integer|in:0,15,30,45,60',
        'status' => 'nullable|in:pending,approved,received,completed',
        'shipping_address' => 'nullable|string|max:500',
        'notes' => 'nullable|string',
        'items' => 'required|array|min:1',
        'items.*.material_id' => 'required|exists:materials,id',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.unit_price' => 'required|numeric|min:0.01',
        'items.*.gst_rate' => 'required|numeric|min:0',
    ];

    public function index(Request $request)
    {
        $query = PurchaseOrder::with([
            'vendor:id,name,business_name',
            'items:id,purchase_order_id,item_name,quantity,material_id',
            'items.material:id,name,code'
        ])->select([
            'id', 'po_number', 'vendor_id', 'po_date', 'order_date',
            'expected_delivery', 'total_amount', 'gst_amount',
            'final_amount', 'status', 'created_at'
        ]);

        $this->applyFilters($query, $request);

        $orders = $query
            ->withSum('items as total_quantity', 'quantity')
            ->withSum('items as total_items_price', 'total_price')
            ->withSum('items as total_gst', 'gst_amount')
            ->withSum('items as net_total', 'net_price')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        $summary = [
            'pending' => PurchaseOrder::where('status', 'pending')->count(),
            'approved' => PurchaseOrder::where('status', 'approved')->count(),
            'received' => PurchaseOrder::where('status', 'received')->count(),
            'totalValue' => PurchaseOrder::sum('final_amount')
        ];

        return view('purchase_orders.index', compact('orders', 'summary'));
    }

public function approve(Request $request, PurchaseOrder $purchaseOrder)
{
    $this->authorize('approve', $purchaseOrder);

    if ($purchaseOrder->status !== PurchaseOrder::STATUS_PENDING) {
        return back()->with('error', 'Purchase order cannot be approved.');
    }

    $purchaseOrder->update([
        'status' => PurchaseOrder::STATUS_APPROVED,
        'approved_by' => Auth::id(),
        'approved_at' => now(),
    ]);

    // ✅ Optional: Mark notification as read
    if ($request->filled('notification_id')) {
        Auth::user()->notifications()->where('id', $request->notification_id)->first()?->markAsRead();
    }

    return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                     ->with('success', 'Purchase order approved.');
}

   public function create()
{
    $vendors = Vendor::all();
    $materials = Material::all();
    return view('purchase_orders.create', compact('vendors', 'materials'));
}

public function store(Request $request)
{
    $validated = $request->validate(self::BASE_RULES);

    // Custom validation for available quantities
    $this->validateMaterialQuantities($validated['items']);

    $order = null;

    DB::transaction(function () use ($validated, &$order) {
        $poNumber = $this->generatePoNumber();

        // Calculate total amounts
        $totalAmount = 0;
        $totalGst = 0;

        foreach ($validated['items'] as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $itemGst = ($itemTotal * $item['gst_rate']) / 100;
            $totalAmount += $itemTotal;
            $totalGst += $itemGst;
        }

        $finalAmount = $totalAmount + $totalGst;

        // Create the PO (status is hardcoded to 'pending')
        $order = PurchaseOrder::create([
            'vendor_id' => $validated['vendor_id'],
            'order_date' => $validated['order_date'],
            'po_date' => $validated['po_date'] ?? now()->format('Y-m-d'),
            'expected_delivery' => $validated['expected_delivery'] ?? null,
            'shipping_address' => $validated['shipping_address'] ?? null,
            'status' => 'pending', // force status to pending
            'notes' => $validated['notes'] ?? null,
            'po_number' => $poNumber,
            'created_by' => Auth::id(),
            'total_amount' => $totalAmount,
            'gst_amount' => $totalGst,
            'final_amount' => $finalAmount,
        ]);

        // Save PO items
        foreach ($validated['items'] as $item) {
            $this->handleItemCreation($order, $item, $validated['vendor_id']);
        }
    });

    // Notify Admins AFTER transaction
    $creator = Auth::user();
    $admins = User::where('role', 'admin')->get();

    foreach ($admins as $admin) {
        $admin->notify(new PurchaseOrderCreated($order->id, $creator->name));
    }

    return redirect()->route('purchase-orders.index')->with('success', 'Purchase order created successfully.');
}


    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::with('items')->findOrFail($id);
        $vendors = Vendor::all();
        $materials = Material::select('id', 'name', 'code', 'unit_price', 'unit', 'gst_rate', 'vendor_id')->get();

        return view('purchase_orders.edit', compact('purchaseOrder', 'vendors', 'materials'));
    }

    public function update(Request $request, $id)
    {
        // Step 1: Validate input including nested item rules
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'order_date' => 'required|date',
            'po_date' => 'nullable|date',
            'expected_delivery' => 'required|date',
            'shipping_address' => 'required|string|max:255',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.gst_rate' => 'required|numeric|min:0',
            'items.*.item_name' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $id) {
            // Step 2: Fetch the purchase order with items
            $order = PurchaseOrder::with('items')->findOrFail($id);

            // Step 3: Simply delete old items (NO stock restoration needed)
            $order->items()->delete();

            // Step 4: Validate stock availability
            $this->validateMaterialQuantities($validated['items']);

            // Step 5: Calculate totals
            $totalAmount = 0;
            $totalGst = 0;

            foreach ($validated['items'] as $item) {
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $itemGst = ($itemTotal * $item['gst_rate']) / 100;
                $totalAmount += $itemTotal;
                $totalGst += $itemGst;
            }

            // Step 6: Update the purchase order
            $order->update([
                'vendor_id'         => $validated['vendor_id'],
                'order_date'        => $validated['order_date'],
                'po_date'           => $validated['po_date'] ?? $order->po_date,
                'expected_delivery' => $validated['expected_delivery'],
                'shipping_address'  => $validated['shipping_address'],
                'status'            => $validated['status'] ?? $order->status,
                'notes'             => $validated['notes'],
                'total_amount'      => $totalAmount,
                'gst_amount'        => $totalGst,
                'final_amount'      => $totalAmount + $totalGst,
            ]);

            // Step 7: Create new items WITHOUT modifying material_vendor quantities
            foreach ($validated['items'] as $item) {
                $this->handleItemCreation($order, $item, $validated['vendor_id']);
            }
        });

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order updated successfully.');
    }


public function destroy($id)
{
    DB::transaction(function () use ($id) {
        $order = PurchaseOrder::with('items')->findOrFail($id);

        // ✅ Notify all admin users before deletion
       foreach (User::where('role', 'admin')->get() as $admin) {
    $admin->notify(new PurchaseOrderDeleted($order, Auth::user()));
}

        // 🟢 Revert material quantities back to vendor stock
        foreach ($order->items as $item) {
            $materialVendor = \App\Models\MaterialVendor::where('material_id', $item->material_id)
                ->where('vendor_id', $order->vendor_id)
                ->first();

            if ($materialVendor) {
                $materialVendor->quantity += $item->quantity;
                $materialVendor->save();
            }
        }

        // 🧹 Delete related notifications (from previous creation/update)
        $poUrl = route('purchase-orders.show', $order->id);

        DB::table('notifications')
            ->whereJsonContains('data->url', $poUrl)
            ->delete();

        // ❌ Delete PO items and the order
        $order->items()->delete();
        $order->delete();
    });

    return redirect()->route('purchase-orders.index')->with('success', 'Purchase order deleted successfully.');
}




  
public function show($id)
{
    // Eager load items with material and vendor
    $purchaseOrder = PurchaseOrder::with(['items.material', 'vendor'])->findOrFail($id);

    return view('purchase_orders.show', compact('purchaseOrder'));
}

    /**
     * Get materials for a specific vendor with current available quantities
     */
    public function getVendorMaterials($vendorId)
    {
        try {
            // Get materials for the vendor with current quantities from material_vendor table
            $materials = \App\Models\MaterialVendor::with('material')
                ->where('vendor_id', $vendorId)
                ->whereHas('material', function ($query) {
                    $query->where('status', 'active');
                })
                ->get()
                ->map(function ($materialVendor) {
                    return [
                        'id' => $materialVendor->material->id,
                        'name' => $materialVendor->material->name,
                        'quantity' => $materialVendor->quantity, // From material_vendor table
                        'unit_price' => $materialVendor->unit_price,
                        'gst_rate' => $materialVendor->gst_rate ?? 0,
                        'unit' => $materialVendor->material->unit,
                        'remaining_qty' => $this->calculateRemainingQuantity($materialVendor->material_id, $vendorId),
                    ];
                });

            return response()->json($materials);
        } catch (\Exception $e) {
            Log::error('Error fetching vendor materials: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch materials'], 500);
        }
    }

    /**
     * Calculate remaining quantity for a material with a specific vendor
     */
    private function calculateRemainingQuantity($materialId, $vendorId, $excludeOrderId = null)
    {
        // Get available quantity from material_vendor table
        $materialVendor = \App\Models\MaterialVendor::where('material_id', $materialId)
            ->where('vendor_id', $vendorId)
            ->first();

        if (!$materialVendor) {
            return 0;
        }

        $availableQty = $materialVendor->quantity;

        // Calculate total ordered quantity for this material from this vendor (optimized query)
        $orderedQty = \App\Models\PurchaseOrderItem::join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->where('purchase_orders.vendor_id', $vendorId)
            ->where('purchase_order_items.material_id', $materialId)
            ->whereNotIn('purchase_orders.status', ['cancelled', 'rejected', 'completed'])
            ->when($excludeOrderId, function ($query) use ($excludeOrderId) {
                return $query->where('purchase_orders.id', '!=', $excludeOrderId);
            })
            ->sum('purchase_order_items.quantity');

        return max(0, $availableQty - $orderedQty);
    }

    protected function applyFilters($query, Request $request)
    {
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('po_date')) {
            $query->whereDate('po_date', $request->po_date);
        }
    }

    private function generatePoNumber(): string
    {
        $lastId = PurchaseOrder::max('id') ?? 0;
        return 'PO-' . now()->format('Y') . '-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
    }

 private function validateMaterialQuantities(array $items): void
{
    $errors = [];
    $vendorId = request()->input('vendor_id');

    // Eager load materials and material_vendors to avoid N+1
    $materialIds = array_column($items, 'material_id');
    $materials = Material::whereIn('id', $materialIds)->get()->keyBy('id');
    $materialVendors = \App\Models\MaterialVendor::where('vendor_id', $vendorId)
        ->whereIn('material_id', $materialIds)
        ->get()
        ->keyBy('material_id');

    foreach ($items as $index => $item) {
        $materialId = $item['material_id'];
        $requestedQty = $item['quantity'];

        // Check if material exists
        $material = $materials->get($materialId);
        if (!$material) {
            $errors["items.{$index}.material_id"] = "Material not found.";
            continue;
        }

        // Check if vendor has this material
        $materialVendor = $materialVendors->get($materialId);
        if (!$materialVendor) {
            $errors["items.{$index}.material_id"] = "Material '{$material->name}' not available from selected vendor.";
            continue;
        }

        $availableQty = $materialVendor->quantity;
        $remainingQty = $this->calculateRemainingQuantity($materialId, $vendorId);

        // Validate against remaining quantity (not just available)
        if ($requestedQty > $remainingQty) {
            $errors["items.{$index}.quantity"] = "Only {$remainingQty} units of '{$material->name}' available (Total: {$availableQty}, Already ordered: " . ($availableQty - $remainingQty) . ").";
        }

        // Validate minimum quantity
        if ($requestedQty <= 0) {
            $errors["items.{$index}.quantity"] = "Quantity must be greater than 0.";
        }
    }

    if (!empty($errors)) {
        throw \Illuminate\Validation\ValidationException::withMessages($errors);
    }
}


    /**
     * Debug method to check material availability - USE THIS TO DEBUG
     * Add this route: Route::get('/debug-material/{materialId}/{vendorId}', [PurchaseOrderController::class, 'debugMaterialAvailability']);
     */
    public function debugMaterialAvailability($materialId, $vendorId)
    {
        $materialVendor = \App\Models\MaterialVendor::where('material_id', $materialId)
            ->where('vendor_id', $vendorId)
            ->first();

        if (!$materialVendor) {
            return response()->json(['error' => 'Material not found for vendor']);
        }

        $availableQty = $materialVendor->quantity;

        // Get all existing orders for this material from this vendor
        $existingOrders = \App\Models\PurchaseOrderItem::with('purchaseOrder')
            ->whereHas('purchaseOrder', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId)
                      ->whereNotIn('status', ['cancelled', 'rejected', 'completed']);
            })
            ->where('material_id', $materialId)
            ->get();

        $totalOrdered = $existingOrders->sum('quantity');
        $remainingQty = max(0, $availableQty - $totalOrdered);

        // Get material name
        $material = Material::find($materialId);
        $materialName = $material ? $material->name : 'Unknown';

        return response()->json([
            'material_name' => $materialName,
            'material_id' => $materialId,
            'vendor_id' => $vendorId,
            'available_qty' => $availableQty,
            'total_ordered' => $totalOrdered,
            'remaining_qty' => $remainingQty,
            'existing_orders' => $existingOrders->map(function ($item) {
                return [
                    'po_id' => $item->purchaseOrder->id,
                    'po_number' => $item->purchaseOrder->po_number,
                    'status' => $item->purchaseOrder->status,
                    'quantity' => $item->quantity,
                    'created_at' => $item->created_at,
                ];
            }),
        ]);
    }

    /**
     * TEMPORARY: Disable validation for testing
     * Replace validateMaterialQuantities with this method temporarily
     */
    private function validateMaterialQuantitiesDisabled(array $items): void
    {
        $vendorId = request()->input('vendor_id');
        
        foreach ($items as $index => $item) {
            $materialId = $item['material_id'];
            $requestedQty = $item['quantity'];

            $materialVendor = \App\Models\MaterialVendor::where('material_id', $materialId)
                ->where('vendor_id', $vendorId)
                ->first();

            if (!$materialVendor) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "items.{$index}.material_id" => "Material not found for selected vendor."
                ]);
            }

            // Get material name
            $material = Material::find($materialId);
            $materialName = $material ? $material->name : "Material ID {$materialId}";

            // Just log, don't validate
            \Log::info("🔍 DEBUG — Material: {$materialName}, Vendor: {$vendorId}, Available: {$materialVendor->quantity}, Requested: {$requestedQty}");
        }
    }

    /**
     * Handle item creation - ONLY updates purchase_order_items table
     * Does NOT modify material_vendor quantities
     */
  private function handleItemCreation(PurchaseOrder $order, array $item, int $vendorId): void
{
    $material = Material::findOrFail($item['material_id']);

    $materialVendor = \App\Models\MaterialVendor::where('material_id', $item['material_id'])
        ->where('vendor_id', $vendorId)
        ->firstOrFail();

    $availableQty = $materialVendor->quantity;
    $requestedQty = $item['quantity'];

    if ($availableQty < $requestedQty) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'quantity' => "Requested quantity ({$requestedQty}) exceeds available quantity ({$availableQty})."
        ]);
    }

    // Pricing
    $unitPrice = $item['unit_price'];
    $gstRate = $item['gst_rate'];
    $totalAmount = $requestedQty * $unitPrice;
    $gstAmount = ($totalAmount * $gstRate) / 100;
    $finalAmount = $totalAmount + $gstAmount;

    // Save item
    $order->items()->create([
        'material_id'       => $item['material_id'],
        'item_name'         => $item['item_name'] ?? $material->name,
        'quantity'          => $requestedQty,
        'unit_price'        => $unitPrice,
        'gst_rate'          => $gstRate,
        'available_qty'     => $availableQty,
        'remaining_qty'     => $availableQty - $requestedQty,
        'total_amount'      => $totalAmount,
        'gst_amount'        => $gstAmount,
        'final_amount'      => $finalAmount,
        'total_price'       => $finalAmount,
        'net_price'         => $totalAmount,
        'expected_delivery' => $item['expected_delivery'] ?? null,
    ]);

    // ✅ Subtract from material vendor qty
    $materialVendor->quantity = max(0, $availableQty - $requestedQty);
    $materialVendor->save();
}

  public function generatePdf($id)
{
    $purchaseOrder = PurchaseOrder::with('vendor', 'items')->findOrFail($id);

    $pdf = Pdf::loadView('purchase_orders.pdf', compact('purchaseOrder'));

    return $pdf->download("PurchaseOrder_{$purchaseOrder->po_number}.pdf");
}

    /**
     * Create inventory batch when PO is approved
     */
    private function createInventoryBatch(PurchaseOrder $purchaseOrder, $item)
    {
        // Generate batch number
        $batchNumber = 'BATCH-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Get first available warehouse
        $warehouseId = \App\Models\Warehouse::first()?->id ?? 1;
        
        \App\Models\InventoryBatch::create([
            'batch_number' => $batchNumber,
            'purchase_order_id' => $purchaseOrder->id,
            'material_id' => $item->material_id,
            'warehouse_id' => $warehouseId,
            'ordered_quantity' => $item->quantity,
            'received_quantity' => $item->quantity,
            'current_quantity' => $item->quantity,
            'remaining_quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'received_by' => Auth::id(),
            'received_date' => now(),
            'status' => 'received',
            'notes' => "Auto-created from PO approval: {$purchaseOrder->po_number}",
        ]);
        
        Log::info("Inventory batch created for PO {$purchaseOrder->po_number}, Material ID: {$item->material_id}, Quantity: {$item->quantity}");
    }
}