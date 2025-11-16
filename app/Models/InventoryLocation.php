<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToBusiness;

class InventoryLocation extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'name',
        'code',
        'location_type',
        'capacity',
        'address',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function materials()
    {
        return $this->hasMany(Material::class, 'location_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'location_id');
    }

    public function productBatches()
    {
        return $this->hasMany(ProductBatch::class, 'location_id');
    }

    public function getCurrentUtilization()
    {
        $totalMaterials = $this->materials()->sum('current_stock');
        $totalProducts = $this->products()->sum('current_stock');
        return $totalMaterials + $totalProducts;
    }
}
