<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBusiness;

class ProductBatch extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'product_id',
        'batch_number',
        'quantity',
        'quantity_available',
        'manufactured_date',
        'expiry_date',
        'work_order_id',
        'location_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'quantity_available' => 'decimal:2',
        'manufactured_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function location()
    {
        return $this->belongsTo(InventoryLocation::class, 'location_id');
    }

    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    public function isExpiringSoon($days = 30)
    {
        return $this->expiry_date && $this->expiry_date <= now()->addDays($days);
    }

    public function reserve($quantity)
    {
        if ($this->quantity_available >= $quantity) {
            $this->decrement('quantity_available', $quantity);
            $this->update(['status' => 'reserved']);
            return true;
        }
        return false;
    }

    public function consume($quantity)
    {
        if ($this->quantity_available >= $quantity) {
            $this->decrement('quantity_available', $quantity);
            $this->decrement('quantity', $quantity);
            
            if ($this->quantity_available == 0) {
                $this->update(['status' => 'shipped']);
            }
            return true;
        }
        return false;
    }
}
