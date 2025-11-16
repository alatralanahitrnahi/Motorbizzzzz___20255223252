<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'quality_checklist_id',
        'inspector_id',
        'reference_id',
        'reference_type',
        'inspection_type',
        'batch_number',
        'inspection_date',
        'status',
        'notes',
        'overall_score',
        'passed',
        'completed_at'
    ];

    protected $casts = [
        'inspection_date' => 'datetime',
        'completed_at' => 'datetime',
        'overall_score' => 'decimal:2'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function qualityChecklist()
    {
        return $this->belongsTo(QualityChecklist::class);
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function inspectionResults()
    {
        return $this->hasMany(QualityInspectionResult::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function getReferenceNameAttribute()
    {
        if ($this->reference_type === 'material' && $this->reference_id) {
            $material = Material::find($this->reference_id);
            return $material ? $material->name : 'Unknown Material';
        } elseif ($this->reference_type === 'product' && $this->reference_id) {
            $product = Product::find($this->reference_id);
            return $product ? $product->name : 'Unknown Product';
        } elseif ($this->reference_type === 'work_order' && $this->reference_id) {
            $workOrder = WorkOrder::find($this->reference_id);
            return $workOrder ? 'WO-' . $workOrder->id : 'Unknown Work Order';
        }
        return 'N/A';
    }
}