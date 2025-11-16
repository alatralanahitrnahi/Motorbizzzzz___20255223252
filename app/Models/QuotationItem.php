<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
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

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
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
