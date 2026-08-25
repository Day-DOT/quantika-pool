<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'is_active',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function pools()
    {
        return $this->hasMany(Pool::class);
    }

    public function scheduleBlocks()
    {
        return $this->hasMany(ScheduleBlock::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function classes()
    {
        return $this->hasMany(ClassSession::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function paymentConcepts()
    {
        return $this->hasMany(PaymentConcept::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function instructors()
    {
        return $this->belongsToMany(
            Instructor::class,
            'branch_instructor'
        )->withTimestamps();
    }
}