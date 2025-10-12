<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\BelongsToBusiness;

class Machine extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'name',
        'code',
        'type',
        'status',
        'location',
        'description',
        'business_id',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    // Status helpers
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function isInUse()
    {
        return $this->status === 'in_use';
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}