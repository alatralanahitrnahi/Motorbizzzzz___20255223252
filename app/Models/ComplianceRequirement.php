<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'category',
        'authority',
        'reference_number',
        'effective_date',
        'expiry_date',
        'status',
        'priority',
        'notes'
    ];

    protected $casts = [
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'priority' => 'integer'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function documents()
    {
        return $this->hasMany(ComplianceDocument::class);
    }

    public function auditFindings()
    {
        return $this->hasMany(ComplianceAuditFinding::class);
    }

    public function responsibilities()
    {
        return $this->hasMany(ComplianceResponsibility::class);
    }

    public function responsibleUsers()
    {
        return $this->belongsToMany(User::class, 'compliance_responsibilities');
    }

    public function isExpiringSoon()
    {
        if ($this->expiry_date) {
            $expiryDate = \Carbon\Carbon::parse($this->expiry_date);
            $now = \Carbon\Carbon::now();
            return $expiryDate->diffInDays($now) <= 30 && $expiryDate->isFuture();
        }
        return false;
    }

    public function isExpired()
    {
        if ($this->expiry_date) {
            return \Carbon\Carbon::parse($this->expiry_date)->isPast();
        }
        return false;
    }
}