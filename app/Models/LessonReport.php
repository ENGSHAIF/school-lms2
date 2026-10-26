<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonReport extends Model
{
    protected $fillable = [
        'booking_id',
        'teacher_id',
        'student_id',
        'pronunciation_score',
        'fluency_score',
        'grammar_score',
        'stars_awarded',
        'teacher_comment',
        'vocabulary_learned',
        'homework',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
