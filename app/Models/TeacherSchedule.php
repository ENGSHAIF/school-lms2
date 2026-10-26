<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSchedule extends Model
{
    protected $fillable = [
        'teacher_id',
        'session_date',
        'start_time',
        'end_time',
        'is_booked',
    ];

    protected $casts = [
        'session_date' => 'date',
        'is_booked' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function booking()
    {
        return $this->hasOne(Booking::class, 'schedule_id');
    }
}
