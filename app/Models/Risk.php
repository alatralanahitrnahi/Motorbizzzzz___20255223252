<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'risk_category_id',
        'title',
        'description',
        'cause',
        'effect',
        'owner_id',
        'likelihood',
        'impact',
        'risk_level',
        'status',
        'assessment_date',
        'review_date',
        'notes'
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'review_date' => 'date'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function riskCategory()
    {
        return $this->belongsTo(RiskCategory::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function impactAssessments()
    {
        return $this->hasMany(RiskImpactAssessment::class);
    }

    public function mitigationStrategies()
    {
        return $this->hasMany(RiskMitigationStrategy::class);
    }

    public function incidents()
    {
        return $this->hasMany(RiskIncident::class);
    }

    public function getRiskScoreAttribute()
    {
        $likelihoodScores = ['low' => 1, 'medium' => 2, 'high' => 3];
        $impactScores = ['low' => 1, 'medium' => 2, 'high' => 3];
        
        $likelihoodScore = $likelihoodScores[$this->likelihood] ?? 1;
        $impactScore = $impactScores[$this->impact] ?? 1;
        
        return $likelihoodScore * $impactScore;
    }

    public function needsReview()
    {
        if ($this->review_date) {
            $reviewDate = \Carbon\Carbon::parse($this->review_date);
            $now = \Carbon\Carbon::now();
            return $reviewDate->isPast() || $reviewDate->diffInDays($now) <= 30;
        }
        return false;
    }
}