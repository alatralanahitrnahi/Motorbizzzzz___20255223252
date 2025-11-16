<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskIncident extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'risk_id',
        'title',
        'description',
        'incident_date',
        'incident_type',
        'reported_by',
        'affected_areas',
        'financial_loss',
        'affected_people',
        'severity',
        'status',
        'immediate_actions',
        'root_cause',
        'corrective_actions',
        'resolution_date',
        'lessons_learned',
        'notes'
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'financial_loss' => 'decimal:2',
        'affected_people' => 'integer',
        'resolution_date' => 'date'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function risk()
    {
        return $this->belongsTo(Risk::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function isHighSeverity()
    {
        return $this->severity === 'high' || $this->severity === 'critical';
    }

    public function isOpen()
    {
        return $this->status !== 'resolved' && $this->status !== 'closed';
    }

    public function daysOpen()
    {
        if ($this->resolution_date) {
            return \Carbon\Carbon::parse($this->incident_date)->diffInDays(\Carbon\Carbon::parse($this->resolution_date));
        }
        return \Carbon\Carbon::parse($this->incident_date)->diffInDays(\Carbon\Carbon::now());
    }
}