<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBusiness;

class Quotation extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'customer_id',
        'lead_id',
        'quote_number',
        'quote_date',
        'valid_until',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'terms_conditions',
        'notes',
    ];

    protected $casts = [
        'quote_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function salesOrder()
    {
        return $this->hasOne(SalesOrder::class);
    }

    public function calculateTotals()
    {
        $subtotal = $this->items->sum('total_price');
        $taxAmount = $this->items->sum(function($item) {
            return ($item->total_price * $item->tax_rate) / 100;
        });
        
        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $subtotal + $taxAmount - $this->discount_amount,
        ]);
    }

    public function convertToSalesOrder()
    {
        $salesOrder = SalesOrder::create([
            'business_id' => $this->business_id,
            'customer_id' => $this->customer_id,
            'quotation_id' => $this->id,
            'order_number' => 'SO-' . date('Y') . '-' . str_pad(SalesOrder::where('business_id', $this->business_id)->count() + 1, 3, '0', STR_PAD_LEFT),
            'order_date' => now(),
            'status' => 'pending',
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'total_amount' => $this->total_amount,
        ]);

        foreach ($this->items as $item) {
            $salesOrder->items()->create([
                'item_type' => $item->item_type,
                'item_id' => $item->item_id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_rate' => $item->tax_rate,
                'discount_percent' => $item->discount_percent,
                'total_price' => $item->total_price,
            ]);
        }

        $this->update(['status' => 'accepted']);

        return $salesOrder;
    }
}
