<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'title',
        'description',
        'objectives',
        'trainer',
        'duration_hours',
        'difficulty_level',
        'is_active'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function trainingMaterials()
    {
        return $this->hasMany(TrainingMaterial::class);
    }

    public function employeeTrainings()
    {
        return $this->hasMany(EmployeeTraining::class);
    }
}