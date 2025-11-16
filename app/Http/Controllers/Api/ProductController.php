<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller {
    public function index(Request $request) {
        $products = Product::where('business_id', $request->user()->business_id)
            ->with('activeBom.items.material')
            ->when($request->search, function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('product_code', 'like', "%{$request->search}%");
            })->paginate(20);
        return response()->json($products);
    }
    
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string',
            'selling_price' => 'required|numeric|min:0',
        ]);
        
        $productCode = 'PRD-' . date('Y') . '-' . str_pad(
            Product::where('business_id', $request->user()->business_id)->count() + 1, 
            4, '0', STR_PAD_LEFT
        );
        
        $product = Product::create([
            'business_id' => $request->user()->business_id,
            'product_code' => $productCode,
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'unit' => $request->unit,
            'selling_price' => $request->selling_price,
            'cost_price' => $request->cost_price,
            'reorder_level' => $request->reorder_level ?? 0,
            'current_stock' => $request->current_stock ?? 0,
            'manufacturing_time' => $request->manufacturing_time ?? 0,
            'is_manufactured' => $request->is_manufactured ?? true,
            'is_saleable' => $request->is_saleable ?? true,
        ]);
        
        return response()->json(['message' => 'Product created', 'product' => $product], 201);
    }
    
    public function show(Request $request, $id) {
        $product = Product::where('business_id', $request->user()->business_id)
            ->with(['activeBom.items.material', 'boms', 'workOrders'])->findOrFail($id);
        return response()->json(['product' => $product]);
    }
    
    public function update(Request $request, $id) {
        $product = Product::where('business_id', $request->user()->business_id)->findOrFail($id);
        $product->update($request->all());
        return response()->json(['message' => 'Product updated', 'product' => $product]);
    }
    
    public function destroy(Request $request, $id) {
        Product::where('business_id', $request->user()->business_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Product deleted']);
    }
}
