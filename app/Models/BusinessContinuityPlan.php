<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessContinuityPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'title',
        'description',
        'scope',
        'objectives',
        'owner_id',
        'critical_functions',
        'recovery_strategies',
        'resource_requirements',
        'contact_information',
        'communication_plan',
        'last_tested_date',
        'next_test_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'last_tested_date' => 'date',
        'next_test_date' => 'date'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function isDueForTesting()
    {
        if ($this->next_test_date) {
            $nextTestDate = \Carbon\Carbon::parse($this->next_test_date);
            $now = \Carbon\Carbon::now();
            return $nextTestDate->isPast() || $nextTestDate->diffInDays($now) <= 30;
        }
        return false;
    }

    public function daysUntilNextTest()
    {
        if ($this->next_test_date) {
            $now = \Carbon\Carbon::now();
            $nextTestDate = \Carbon\Carbon::parse($this->next_test_date);
            return $now->diffInDays($nextTestDate, false);
        }
        return null;
    }
}