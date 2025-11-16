<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceResponsibility extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'compliance_requirement_id',
        'user_id',
        'role',
        'responsibilities'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function complianceRequirement()
    {
        return $this->belongsTo(ComplianceRequirement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}