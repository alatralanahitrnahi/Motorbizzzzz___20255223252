<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quality_checklist_id',
        'item_name',
        'description',
        'criteria_type',
        'acceptable_criteria',
        'min_value',
        'max_value',
        'sort_order',
        'is_required'
    ];

    protected $casts = [
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2'
    ];

    public function qualityChecklist()
    {
        return $this->belongsTo(QualityChecklist::class);
    }

    public function inspectionResults()
    {
        return $this->hasMany(QualityInspectionResult::class);
    }
}