<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskImpactAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'risk_id',
        'financial_impact',
        'operational_impact',
        'reputational_impact',
        'legal_impact',
        'safety_impact',
        'assessment_details',
        'assessed_by',
        'assessment_date',
        'methodology'
    ];

    protected $casts = [
        'financial_impact' => 'decimal:2',
        'operational_impact' => 'decimal:2',
        'reputational_impact' => 'decimal:2',
        'legal_impact' => 'decimal:2',
        'safety_impact' => 'decimal:2',
        'assessment_date' => 'date'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function risk()
    {
        return $this->belongsTo(Risk::class);
    }

    public function assessedBy()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function getTotalImpactScoreAttribute()
    {
        return ($this->financial_impact ?? 0) + 
               ($this->operational_impact ?? 0) + 
               ($this->reputational_impact ?? 0) + 
               ($this->legal_impact ?? 0) + 
               ($this->safety_impact ?? 0);
    }
}