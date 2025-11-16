<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bom;
use App\Models\Product;

class BomController extends Controller {
    public function index(Request $request) {
        $boms = Bom::where('business_id', $request->user()->business_id)
            ->with(['product', 'items.material'])->paginate(20);
        return response()->json($boms);
    }
    
    public function store(Request $request) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'items' => 'required|array',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity_required' => 'required|numeric|min:0',
        ]);
        
        $bom = Bom::create([
            'business_id' => $request->user()->business_id,
            'product_id' => $request->product_id,
            'version' => $request->version ?? '1.0',
            'quantity' => $request->quantity ?? 1,
            'is_active' => true,
        ]);
        
        Bom::where('business_id', $request->user()->business_id)
            ->where('product_id', $request->product_id)
            ->where('id', '!=', $bom->id)
            ->update(['is_active' => false]);
        
        foreach ($request->items as $item) {
            $bom->items()->create([
                'material_id' => $item['material_id'],
                'quantity_required' => $item['quantity_required'],
                'unit' => $item['unit'] ?? 'kg',
                'wastage_percent' => $item['wastage_percent'] ?? 0,
            ]);
        }
        
        return response()->json(['message' => 'BOM created', 'bom' => $bom->load('items.material')], 201);
    }
    
    public function show(Request $request, $id) {
        $bom = Bom::where('business_id', $request->user()->business_id)
            ->with(['product', 'items.material'])->findOrFail($id);
        
        $materialCost = $bom->calculateMaterialCost();
        $requirements = $bom->getMaterialRequirements();
        
        return response()->json([
            'bom' => $bom,
            'material_cost' => $materialCost,
            'requirements' => $requirements,
        ]);
    }
    
    public function update(Request $request, $id) {
        $bom = Bom::where('business_id', $request->user()->business_id)->findOrFail($id);
        $bom->update($request->except('items'));
        
        if ($request->items) {
            $bom->items()->delete();
            foreach ($request->items as $item) {
                $bom->items()->create($item);
            }
        }
        
        return response()->json(['message' => 'BOM updated', 'bom' => $bom->load('items.material')]);
    }
    
    public function destroy(Request $request, $id) {
        Bom::where('business_id', $request->user()->business_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'BOM deleted']);
    }
}
