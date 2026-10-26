<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'student_answer',
        'recorded_audio_path',
        'speech_accuracy',
        'is_correct',
        'points_awarded',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id');
    }
}
