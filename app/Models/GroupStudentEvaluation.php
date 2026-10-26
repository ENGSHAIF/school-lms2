<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupStudentEvaluation extends Model
{
    protected $fillable = [
        'group_session_id',
        'study_group_id',
        'student_id',
        'attendance_status',
        'participation_score',
        'understanding_score',
        'homework_score',
        'spoke_voice',
        'wrote_chat',
        'submitted_task',
        'stars_awarded',
        'daily_total_score',
        'teacher_notes',
    ];

    protected $casts = [
        'spoke_voice' => 'boolean',
        'wrote_chat' => 'boolean',
        'submitted_task' => 'boolean',
        'daily_total_score' => 'float',
    ];

    public function session()
    {
        return $this->belongsTo(GroupSession::class, 'group_session_id');
    }

    public function group()
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // تسمية حالة الحضور بالعربية
    public function getAttendanceLabelAttribute(): string
    {
        return match ($this->attendance_status) {
            'present' => 'حاضر 🟢',
            'absent' => 'غائب 🔴',
            'late' => 'متأخر 🟡',
            'excused' => 'معذور ⚪',
            default => 'حاضر',
        };
    }

    // شارة الحالة اللونية
    public function getAttendanceBadgeClassAttribute(): string
    {
        return match ($this->attendance_status) {
            'present' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'absent' => 'bg-rose-100 text-rose-800 border-rose-300',
            'late' => 'bg-amber-100 text-amber-800 border-amber-300',
            'excused' => 'bg-slate-100 text-slate-800 border-slate-300',
            default => 'bg-emerald-100 text-emerald-800 border-emerald-300',
        };
    }
}
