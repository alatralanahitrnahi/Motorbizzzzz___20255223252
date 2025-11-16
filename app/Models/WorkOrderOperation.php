<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'operation_name',
        'machine_id',
        'operator_id',
        'sequence',
        'planned_duration',
        'actual_duration',
        'status',
        'notes',
    ];

    protected $casts = [
        'sequence' => 'integer',
        'planned_duration' => 'integer',
        'actual_duration' => 'integer',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
