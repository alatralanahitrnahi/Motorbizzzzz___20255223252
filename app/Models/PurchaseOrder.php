<?php

namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\BelongsToBusiness;

class PurchaseOrder extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'vendor_id',
        'po_date',
        'order_date',
        'po_number',
        'created_by',
        'expected_delivery',
        'shipping_address',
        'status',
        'notes',
        'total_amount',
        'gst_amount',
        'final_amount',
        'business_id',
    ];
  
  protected $casts = [
    'po_date' => 'datetime',
    'order_date' => 'datetime',
    'expected_delivery' => 'datetime',
];


public function items()
{
    return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
}


public function vendor()
{
    return $this->belongsTo(\App\Models\Vendor::class);
}

  public function canBeEdited(): bool
{
    // Adjust the logic based on your business rules
    return $this->status === 'pending';
}
public function qualityAnalysis()
{
    return $this->hasOne(QualityAnalysis::class);
}

  
   /**
     * Check if this purchase order has a quality analysis
     */
    public function hasQualityAnalysis(): bool
    {
        return $this->qualityAnalysis()->exists();
    }

    /**
     * Scope to get purchase orders without quality analysis
     */
    public function scopeWithoutQualityAnalysis($query)
    {
        return $query->whereDoesntHave('qualityAnalysis');
    }

    /**
     * Scope to get purchase orders with quality analysis
     */
    public function scopeWithQualityAnalysis($query)
    {
        return $query->whereHas('qualityAnalysis');
    }

  protected static function booted()
{
    static::deleting(function ($order) {
        $url = route('purchase-orders.show', $order->id);

        // Delete notifications pointing to this PO
        \DB::table('notifications')
            ->where('type', 'dashboard')
            ->whereJsonContains('data->url', $url)
            ->delete();

        // Optional: Log who deleted it
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['po_id' => $order->id])
            ->log("Purchase Order #{$order->po_number} was deleted by " . auth()->user()->name);
    });
}

  public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}


}
