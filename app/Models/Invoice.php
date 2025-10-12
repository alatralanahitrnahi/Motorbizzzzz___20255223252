<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\BelongsToBusiness;

class Invoice extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'invoice_number',
        'work_order_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_gstin',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'total_amount',
        'status',
        'notes',
        'business_id',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Helper methods
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && !$this->isPaid();
    }

    // Generate invoice number
    public static function generateInvoiceNumber()
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $lastInvoice = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -4) + 1 : 1;
        
        return "INV-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}