<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBusiness;

class Product extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'product_code',
        'name',
        'description',
        'category',
        'unit',
        'selling_price',
        'cost_price',
        'reorder_level',
        'current_stock',
        'bom_id',
        'manufacturing_time',
        'is_manufactured',
        'is_saleable',
        'images',
        'product_type',
        'reserved_quantity',
        'stock_status',
        'location_id',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'reserved_quantity' => 'decimal:2',
        'manufacturing_time' => 'integer',
        'is_manufactured' => 'boolean',
        'is_saleable' => 'boolean',
        'images' => 'array',
    ];

    public function bom()
    {
        return $this->belongsTo(Bom::class);
    }

    public function boms()
    {
        return $this->hasMany(Bom::class);
    }

    public function activeBom()
    {
        return $this->hasOne(Bom::class)->where('is_active', true);
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'item');
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function adjustStock($quantity, $type = 'adjustment', $reference = null)
    {
        $this->increment('current_stock', $quantity);
        
        StockMovement::create([
            'business_id' => $this->business_id,
            'item_type' => 'product',
            'item_id' => $this->id,
            'movement_type' => $type,
            'quantity' => $quantity,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference?->id,
        ]);
    }

    public function location()
    {
        return $this->belongsTo(InventoryLocation::class, 'location_id');
    }

    public function batches()
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function activeBatches()
    {
        return $this->hasMany(ProductBatch::class)->where('status', 'available');
    }

    public function reserve($quantity)
    {
        if (($this->current_stock - $this->reserved_quantity) >= $quantity) {
            $this->increment('reserved_quantity', $quantity);
            $this->update(['stock_status' => 'reserved']);
            return true;
        }
        return false;
    }

    public function unreserve($quantity)
    {
        $this->decrement('reserved_quantity', min($quantity, $this->reserved_quantity));
        
        if ($this->reserved_quantity == 0) {
            $this->update(['stock_status' => 'available']);
        }
        return true;
    }

    public function getAvailableQuantity()
    {
        return $this->current_stock - $this->reserved_quantity;
    }

    public function createBatch($quantity, $workOrder = null, $location = null)
    {
        $batchNumber = 'BATCH-' . $this->product_code . '-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        
        return ProductBatch::create([
            'business_id' => $this->business_id,
            'product_id' => $this->id,
            'batch_number' => $batchNumber,
            'quantity' => $quantity,
            'quantity_available' => $quantity,
            'manufactured_date' => now(),
            'work_order_id' => $workOrder?->id,
            'location_id' => $location?->id ?? $this->location_id,
            'status' => 'available',
        ]);
    }
}