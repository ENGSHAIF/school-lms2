<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    protected $fillable = [
        'exam_id',
        'type',
        'question_text',
        'context_passage',
        'audio_url',
        'options',
        'correct_answer',
        'expected_speech_text',
        'points',
        'order_index',
    ];

    protected $casts = [
        'options' => 'array',
        'points' => 'float',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
}
