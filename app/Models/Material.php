<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBusiness;

/**
 * Class Material
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $description
 * @property string $unit
 * @property float $unit_price
 * @property float $gst_rate
 * @property string $category
 * @property bool $is_available
 * @property float|null $length
 * @property float|null $width
 * @property float|null $height
 * @property float|null $weight
 * @property float|null $volume
 */
class Material extends Model
{
    use BelongsToBusiness;
    
    // Protect the 'id' from mass assignment
    protected $guarded = ['id'];

    // Fields that can be mass-assigned
    protected $fillable = [
        'name',
        'code',
        'sku',
        'barcode',
        'description',
        'unit',
        'unit_price',
        'gst_rate',
        'category',
        'is_available',
        'dimensions',
        'business_id',
        'material_type',
        'reserved_quantity',
        'stock_status',
        'location_id',
        'current_stock',
        'reorder_level',
    ];

    // Cast fields to appropriate data types
    protected $casts = [
        'unit_price'   => 'decimal:2',
        'gst_rate'     => 'decimal:2',
        'is_available' => 'boolean',
        'dimensions' => 'array',
        'reserved_quantity' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'reorder_level' => 'decimal:2',
    ];

// In Material.php model
public function getDimensionsAttribute($value)
{
    return json_decode($value, true);
}

  
    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'material_vendor', 'material_id', 'vendor_id')
->withPivot('unit_price', 'quantity', 'material_name', 'gst_rate')
                    ->withTimestamps(); // only if you added timestamps
    }
  
  
   // Scope for available materials
    public function scopeAvailable($query)
    {
        return $query->where('is_available', 1);
    }
    
    // Check if material is currently available
    public function isAvailable()
    {
        return $this->is_available == 1;
    }
  

   public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
  public function purchaseOrders()
{
    return $this->hasMany(PurchaseOrder::class); // Adjust if using pivot
}
    /**
     * Get related inventory batches.
     */
    public function inventoryBatches(): HasMany
    {
        return $this->hasMany(InventoryBatch::class);
    }

    /**
     * Get related barcodes.
     */
    public function barcodes(): HasMany
    {
        return $this->hasMany(Barcode::class);
    }
  
   /**
     * Search materials by various fields
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('code', 'LIKE', "%{$search}%")
              ->orWhere('sku', 'LIKE', "%{$search}%")
              ->orWhere('barcode', $search)
              ->orWhere('category', 'LIKE', "%{$search}%");
        });
    }
  
   /**
     * Get material by barcode
     */
    public static function findByBarcode($barcode)
    {
        return static::where('barcode', $barcode)->first();
    }

    /**
     * Get material by SKU
     */
    public static function findBySku($sku)
    {
        return static::where('sku', $sku)->first();
    }

    /**
     * Calculate the current stock based on active inventory batches.
     *
     * @return float
     */
    public function getCurrentStock(): float
    {
        return (float) $this->inventoryBatches()
            ->where('status', 'active')
            ->sum('current_quantity');
    }

    public function location()
    {
        return $this->belongsTo(InventoryLocation::class, 'location_id');
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

    public function isLowStock()
    {
        return $this->current_stock <= $this->reorder_level;
    }
}