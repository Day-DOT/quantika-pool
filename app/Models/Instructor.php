<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'specialty',
        'is_active',
    ];

    public function branches()
    {
        return $this->belongsToMany(
            Branch::class,
            'branch_instructor'
        )->withTimestamps();
    }

    public function availabilities()
    {
        return $this->hasMany(InstructorAvailability::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function classes()
    {
        return $this->hasMany(ClassSession::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}