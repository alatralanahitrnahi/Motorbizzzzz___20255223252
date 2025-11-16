<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'quality_standard_id',
        'name',
        'description',
        'checklist_type',
        'is_active'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function qualityStandard()
    {
        return $this->belongsTo(QualityStandard::class);
    }

    public function checklistItems()
    {
        return $this->hasMany(QualityChecklistItem::class);
    }

    public function inspections()
    {
        return $this->hasMany(QualityInspection::class);
    }
}