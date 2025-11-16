<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityInspectionResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'quality_inspection_id',
        'quality_checklist_item_id',
        'result_value',
        'passed',
        'remarks',
        'attachment_path'
    ];

    protected $casts = [
        'passed' => 'boolean'
    ];

    public function qualityInspection()
    {
        return $this->belongsTo(QualityInspection::class);
    }

    public function qualityChecklistItem()
    {
        return $this->belongsTo(QualityChecklistItem::class);
    }
}