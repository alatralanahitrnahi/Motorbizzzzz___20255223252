<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryLocation;

class InventoryLocationController extends Controller {
    public function index(Request $request) {
        $locations = InventoryLocation::where('business_id', $request->user()->business_id)
            ->when($request->type, function($q) use ($request) {
                $q->where('location_type', $request->type);
            })
            ->when($request->active !== null, function($q) use ($request) {
                $q->where('is_active', $request->active);
            })->get();
        return response()->json($locations);
    }
    
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'location_type' => 'required|in:warehouse,shop_floor,quarantine,shipping,returns',
        ]);
        
        $code = 'LOC-' . strtoupper(substr(uniqid(), -6));
        
        $location = InventoryLocation::create([
            'business_id' => $request->user()->business_id,
            'name' => $request->name,
            'code' => $code,
            'location_type' => $request->location_type,
            'capacity' => $request->capacity,
            'address' => $request->address,
            'is_active' => true,
        ]);
        
        return response()->json(['message' => 'Location created', 'location' => $location], 201);
    }
    
    public function show(Request $request, $id) {
        $location = InventoryLocation::where('business_id', $request->user()->business_id)
            ->with(['materials', 'products'])->findOrFail($id);
        
        $utilization = $location->getCurrentUtilization();
        
        return response()->json([
            'location' => $location,
            'current_utilization' => $utilization,
            'capacity_percentage' => $location->capacity ? ($utilization / $location->capacity) * 100 : 0,
        ]);
    }
    
    public function update(Request $request, $id) {
        $location = InventoryLocation::where('business_id', $request->user()->business_id)->findOrFail($id);
        $location->update($request->all());
        return response()->json(['message' => 'Location updated', 'location' => $location]);
    }
    
    public function destroy(Request $request, $id) {
        InventoryLocation::where('business_id', $request->user()->business_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Location deleted']);
    }
}
