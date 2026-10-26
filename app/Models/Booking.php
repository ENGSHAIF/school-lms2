<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'student_id',
        'teacher_id',
        'schedule_id',
        'room_id',
        'status',
        'topic',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function schedule()
    {
        return $this->belongsTo(TeacherSchedule::class, 'schedule_id');
    }

    public function report()
    {
        return $this->hasOne(LessonReport::class, 'booking_id');
    }
}
