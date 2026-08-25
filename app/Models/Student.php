<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'level_id',
        'first_name',
        'last_name',
        'birth_date',
        'phone',
        'email',
        'address',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function groups()
    {
        return $this->belongsToMany(
            Group::class,
            'group_student'
        )
            ->withPivot([
                'joined_at',
                'left_at',
                'is_active'
            ])
            ->withTimestamps();
    }
}