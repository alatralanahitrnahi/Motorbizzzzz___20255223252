<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceAuditFinding extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'compliance_audit_id',
        'compliance_requirement_id',
        'description',
        'evidence',
        'severity',
        'status',
        'assigned_to',
        'due_date',
        'corrective_action',
        'resolution_date',
        'resolution_notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'resolution_date' => 'date'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function complianceAudit()
    {
        return $this->belongsTo(ComplianceAudit::class);
    }

    public function complianceRequirement()
    {
        return $this->belongsTo(ComplianceRequirement::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isOverdue()
    {
        if ($this->due_date && $this->status !== 'resolved' && $this->status !== 'closed') {
            return \Carbon\Carbon::parse($this->due_date)->isPast();
        }
        return false;
    }

    public function isOpen()
    {
        return $this->status === 'open' || $this->status === 'in_progress';
    }
}