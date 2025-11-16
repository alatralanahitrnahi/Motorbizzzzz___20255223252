<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBusiness;

class StockMovement extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'item_type',
        'item_id',
        'movement_type',
        'quantity',
        'from_location',
        'to_location',
        'reference_type',
        'reference_id',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function item()
    {
        if ($this->item_type === 'material') {
            return $this->belongsTo(Material::class, 'item_id');
        } elseif ($this->item_type === 'product') {
            return $this->belongsTo(Product::class, 'item_id');
        }
        return null;
    }

    public function reference()
    {
        if ($this->reference_type && $this->reference_id) {
            return $this->morphTo();
        }
        return null;
    }
}
