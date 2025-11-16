<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\BelongsToBusiness;

class WorkOrder extends Model
{
    use HasFactory, BelongsToBusiness;

    protected $fillable = [
        'business_id',
        'sales_order_id',
        'work_order_number',
        'product_id',
        'quantity_planned',
        'quantity_produced',
        'quantity_rejected',
        'machine_id',
        'assigned_to',
        'start_date',
        'end_date',
        'status',
        'priority',
        'actual_start_time',
        'actual_end_time',
        'notes',
    ];

    protected $casts = [
        'quantity_planned' => 'decimal:2',
        'quantity_produced' => 'decimal:2',
        'quantity_rejected' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function operations()
    {
        return $this->hasMany(WorkOrderOperation::class);
    }

    public function materialConsumptions()
    {
        return $this->hasMany(MaterialConsumption::class);
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'reference');
    }

    public function start()
    {
        $this->update([
            'status' => 'in_progress',
            'actual_start_time' => now(),
        ]);

        if ($this->product && $this->product->activeBom) {
            $bom = $this->product->activeBom;
            foreach ($bom->items as $bomItem) {
                $requiredQty = $bomItem->quantity_required * $this->quantity_planned;
                
                $this->materialConsumptions()->create([
                    'material_id' => $bomItem->material_id,
                    'planned_quantity' => $requiredQty,
                ]);
                
                $bomItem->material->reserve($requiredQty);
            }
        }
    }

    public function complete($quantityProduced, $quantityRejected = 0)
    {
        $this->update([
            'status' => 'completed',
            'quantity_produced' => $quantityProduced,
            'quantity_rejected' => $quantityRejected,
            'actual_end_time' => now(),
        ]);

        if ($this->product) {
            $this->product->adjustStock($quantityProduced, 'in', $this);
            
            $batch = $this->product->createBatch($quantityProduced, $this);
        }
        
        foreach ($this->materialConsumptions as $consumption) {
            if ($consumption->actual_quantity) {
                $consumption->material->unreserve($consumption->planned_quantity);
            }
        }
    }

    public function consumeMaterial($materialId, $actualQuantity, $wastageQuantity = 0)
    {
        $consumption = $this->materialConsumptions()->where('material_id', $materialId)->first();
        
        if ($consumption) {
            $consumption->update([
                'actual_quantity' => $actualQuantity,
                'wastage_quantity' => $wastageQuantity,
            ]);
        } else {
            $consumption = $this->materialConsumptions()->create([
                'material_id' => $materialId,
                'actual_quantity' => $actualQuantity,
                'wastage_quantity' => $wastageQuantity,
            ]);
        }

        $material = Material::find($materialId);
        if ($material) {
            StockMovement::create([
                'business_id' => $this->business_id,
                'item_type' => 'material',
                'item_id' => $materialId,
                'movement_type' => 'out',
                'quantity' => $actualQuantity + $wastageQuantity,
                'reference_type' => get_class($this),
                'reference_id' => $this->id,
                'notes' => "Consumed for WO: {$this->work_order_number}",
            ]);
        }

        return $consumption;
    }

    public function getYieldPercentageAttribute()
    {
        if ($this->quantity_produced > 0) {
            return round(($this->quantity_produced / ($this->quantity_produced + $this->quantity_rejected)) * 100, 2);
        }
        return 0;
    }
}