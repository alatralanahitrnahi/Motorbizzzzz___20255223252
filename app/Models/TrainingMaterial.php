<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_program_id',
        'title',
        'description',
        'file_path',
        'file_type',
        'sort_order'
    ];

    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class);
    }
}