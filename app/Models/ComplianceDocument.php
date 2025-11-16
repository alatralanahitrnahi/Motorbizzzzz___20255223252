<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'compliance_requirement_id',
        'title',
        'description',
        'document_type',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'version',
        'approved_by',
        'approval_date',
        'effective_date',
        'review_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'approval_date' => 'date',
        'effective_date' => 'date',
        'review_date' => 'date',
        'file_size' => 'integer',
        'version' => 'integer'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function complianceRequirement()
    {
        return $this->belongsTo(ComplianceRequirement::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function needsReview()
    {
        if ($this->review_date) {
            $reviewDate = \Carbon\Carbon::parse($this->review_date);
            $now = \Carbon\Carbon::now();
            return $reviewDate->isPast() || $reviewDate->diffInDays($now) <= 7;
        }
        return false;
    }
}