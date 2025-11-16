<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'title',
        'description',
        'audit_type',
        'auditor_id',
        'planned_date',
        'actual_date',
        'start_time',
        'end_time',
        'status',
        'scope',
        'objectives',
        'findings_summary',
        'recommendations',
        'action_items',
        'follow_up_date',
        'notes'
    ];

    protected $casts = [
        'planned_date' => 'date',
        'actual_date' => 'date',
        'start_time' => 'time',
        'end_time' => 'time',
        'follow_up_date' => 'date'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function findings()
    {
        return $this->hasMany(ComplianceAuditFinding::class);
    }

    public function isOverdue()
    {
        if ($this->planned_date && $this->status !== 'completed') {
            return \Carbon\Carbon::parse($this->planned_date)->isPast();
        }
        return false;
    }

    public function isUpcoming()
    {
        if ($this->planned_date && $this->status === 'planned') {
            $plannedDate = \Carbon\Carbon::parse($this->planned_date);
            $now = \Carbon\Carbon::now();
            return $plannedDate->isFuture() && $plannedDate->diffInDays($now) <= 7;
        }
        return false;
    }
}