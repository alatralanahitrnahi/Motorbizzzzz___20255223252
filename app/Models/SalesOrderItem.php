<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'item_type',
        'item_id',
        'description',
        'quantity',
        'unit_price',
        'tax_rate',
        'discount_percent',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function item()
    {
        if ($this->item_type === 'material') {
            return $this->belongsTo(Material::class, 'item_id');
        } elseif ($this->item_type === 'product') {
            return $this->belongsTo(Product::class, 'item_id');
        }
        return null;
    }
}
