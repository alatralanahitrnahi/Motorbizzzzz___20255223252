<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\BelongsToBusiness;

class WorkOrder extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'wo_number',
        'machine_id',
        'operator_id',
        'product_name',
        'quantity',
        'status',
        'started_at',
        'completed_at',
        'notes',
        'business_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'quantity' => 'integer',
    ];

    // Relationships
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function materialConsumptions()
    {
        return $this->hasMany(MaterialConsumption::class);
    }

    // Status helpers
    public function isActive()
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    // Calculate duration
    public function getDurationAttribute()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInMinutes($this->completed_at);
        }
        return null;
    }
}