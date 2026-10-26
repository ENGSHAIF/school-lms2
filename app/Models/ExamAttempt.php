<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'exam_id',
        'study_group_id',
        'total_score',
        'speech_score',
        'speech_feedback',
        'status',
        'is_published',
        'reviewed_by',
        'reviewed_at',
        'teacher_notes',
        'questions_order',
        'suggested_level_id',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'speech_feedback' => 'array',
        'questions_order' => 'array',
        'is_published' => 'boolean',
        'total_score' => 'float',
        'speech_score' => 'float',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function suggestedLevel()
    {
        return $this->belongsTo(Level::class, 'suggested_level_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'attempt_id');
    }

    // هل النتيجة ظاهرة ومتاحة للطالب؟
    public function isResultVisibleToStudent(): bool
    {
        if ($this->is_published) {
            return true;
        }

        // إذا كان الاختبار مضبوط لإظهار النتيجة فوراً وحالة المحاولة مكتملة
        if ($this->exam && $this->exam->show_result_immediately && in_array($this->status, ['submitted', 'graded', 'reviewed'])) {
            return true;
        }

        return false;
    }
}
