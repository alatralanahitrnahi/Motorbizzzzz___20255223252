<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBusiness;

class Bom extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'product_id',
        'version',
        'quantity',
        'is_active',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function items()
    {
        return $this->hasMany(BomItem::class);
    }

    public function calculateMaterialCost()
    {
        return $this->items->sum(function($item) {
            $material = $item->material;
            $requiredQty = $item->quantity_required * (1 + ($item->wastage_percent / 100));
            return $material->unit_price * $requiredQty;
        });
    }

    public function getMaterialRequirements($productionQuantity = 1)
    {
        return $this->items->map(function($item) use ($productionQuantity) {
            $baseQty = $item->quantity_required * $productionQuantity;
            $wastageQty = $baseQty * ($item->wastage_percent / 100);
            
            return [
                'material_id' => $item->material_id,
                'material_name' => $item->material->name,
                'required_quantity' => $baseQty,
                'wastage_quantity' => $wastageQty,
                'total_quantity' => $baseQty + $wastageQty,
                'unit' => $item->unit,
            ];
        });
    }
}
