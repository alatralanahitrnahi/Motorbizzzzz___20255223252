<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTraining extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'training_program_id',
        'assigned_date',
        'start_date',
        'completion_date',
        'score',
        'status',
        'feedback'
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'start_date' => 'date',
        'completion_date' => 'date',
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

    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class);
    }
}