<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'manager_id',
        'is_active'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function jobPositions()
    {
        return $this->hasMany(JobPosition::class);
    }

    public function employees()
    {
        return $this->hasMany(User::class, 'department_id');
    }
}