<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupSession extends Model
{
    protected $fillable = [
        'study_group_id',
        'teacher_id',
        'session_date',
        'session_number',
        'lesson_topic',
        'lesson_summary',
        'homework_assigned',
        'total_students',
        'present_count',
        'absent_count',
        'late_count',
        'excused_count',
        'attendance_rate',
        'general_notes',
    ];

    protected $casts = [
        'session_date' => 'date',
        'attendance_rate' => 'float',
    ];

    public function group()
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function evaluations()
    {
        return $this->hasMany(GroupStudentEvaluation::class, 'group_session_id');
    }

    // جلب الطلاب الأكثر تفاعلاً في هذه الجلسة
    public function topActiveEvaluations()
    {
        return $this->evaluations()
            ->orderByDesc('stars_awarded')
            ->orderByDesc('participation_score');
    }
}
