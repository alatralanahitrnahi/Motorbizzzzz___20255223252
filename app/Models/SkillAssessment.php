<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'skill_name',
        'proficiency_level',
        'description',
        'assessed_date',
        'assessed_by',
        'score',
        'comments',
        'next_review_date'
    ];

    protected $casts = [
        'assessed_date' => 'date',
        'next_review_date' => 'date',
        'score' => 'decimal:2'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assessedBy()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }
}