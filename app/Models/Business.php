<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'is_active',
        'subscription_plan',
        'subscription_expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_expires_at' => 'datetime',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    // Helper methods
    public function getSubdomainAttribute()
    {
        return $this->slug . '.monitorbizz.com';
    }

    public function isActive()
    {
        return $this->is_active && ($this->subscription_expires_at === null || $this->subscription_expires_at->isFuture());
    }
}