<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'category_type',
        'owner_id',
        'status',
        'notes'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function risks()
    {
        return $this->hasMany(Risk::class);
    }
}