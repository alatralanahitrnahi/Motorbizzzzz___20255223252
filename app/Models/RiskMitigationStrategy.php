<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskMitigationStrategy extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'risk_id',
        'strategy_name',
        'description',
        'actions',
        'responsible_person_id',
        'cost',
        'start_date',
        'end_date',
        'status',
        'effectiveness',
        'notes'
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'effectiveness' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function risk()
    {
        return $this->belongsTo(Risk::class);
    }

    public function responsiblePerson()
    {
        return $this->belongsTo(User::class, 'responsible_person_id');
    }

    public function isOverdue()
    {
        if ($this->end_date && $this->status !== 'completed') {
            return \Carbon\Carbon::parse($this->end_date)->isPast();
        }
        return false;
    }

    public function isUpcoming()
    {
        if ($this->start_date && $this->status === 'planned') {
            $startDate = \Carbon\Carbon::parse($this->start_date);
            $now = \Carbon\Carbon::now();
            return $startDate->isFuture() && $startDate->diffInDays($now) <= 7;
        }
        return false;
    }
}