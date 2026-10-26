<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'level_id',
        'course_id',
        'created_by',
        'title',
        'description',
        'type',
        'quiz_period',
        'term',
        'duration_minutes',
        'total_points',
        'passing_score',
        'random_questions_count',
        'shuffle_questions',
        'shuffle_options',
        'show_result_immediately',
        'is_active',
        'study_group_id',
        'target_type',
        'assigned_student_ids',
        'weight_category',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_result_immediately' => 'boolean',
        'duration_minutes' => 'integer',
        'random_questions_count' => 'integer',
        'total_points' => 'float',
        'passing_score' => 'float',
        'assigned_student_ids' => 'array',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class, 'exam_id')->orderBy('order_index');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class, 'exam_id');
    }

    public function levelResource()
    {
        return $this->hasOne(LevelResource::class, 'exam_id');
    }

    public function getQuizPeriodLabelAttribute(): string
    {
        return match ($this->quiz_period) {
            'weekly' => 'كويز أسبوعي',
            'midterm' => 'اختبار منتصف الدورة (نصفي)',
            'final' => 'اختبار نهاية الدورة (نهائي)',
            'pop_quiz' => 'كويز مفاجئ / قصير',
            'placement' => 'اختبار تحديد مستوى',
            default => 'كويز تدريبي',
        };
    }

    public function getQuizPeriodBadgeAttribute(): string
    {
        return match ($this->quiz_period) {
            'weekly' => 'bg-purple-100 text-purple-800 border-purple-200',
            'midterm' => 'bg-blue-100 text-blue-800 border-blue-200',
            'final' => 'bg-rose-100 text-rose-800 border-rose-200',
            'pop_quiz' => 'bg-amber-100 text-amber-800 border-amber-200',
            'placement' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }
}
