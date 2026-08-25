<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'sort_order',
        'is_active',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function evaluationCriteria()
    {
        return $this->hasMany(EvaluationCriterion::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}