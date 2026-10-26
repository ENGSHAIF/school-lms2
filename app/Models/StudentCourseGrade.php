<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCourseGrade extends Model
{
    protected $fillable = [
        'student_id',
        'study_group_id',
        'course_id',
        'level_id',
        'attendance_score',
        'participation_score',
        'activities_score',
        'midterm_score',
        'practical_score',
        'final_exam_score',
        'total_score',
        'percentage',
        'grade_letter',
        'status',
        'teacher_notes',
        'reviewed_by',
        'is_finalized',
    ];

    protected $casts = [
        'attendance_score' => 'float',
        'participation_score' => 'float',
        'activities_score' => 'float',
        'midterm_score' => 'float',
        'practical_score' => 'float',
        'final_exam_score' => 'float',
        'total_score' => 'float',
        'percentage' => 'float',
        'is_finalized' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // حساب المجموع والنسبة والتقدير والحالة تلقائياً
    public function recalculate(?CourseGradeScale $scale = null): self
    {
        $totalMax = $scale ? $scale->total_max : 100.00;
        $passPercentage = $scale ? $scale->passing_percentage : 60.00;

        $total = (float)$this->attendance_score
               + (float)$this->participation_score
               + (float)$this->activities_score
               + (float)$this->midterm_score
               + (float)$this->practical_score
               + (float)$this->final_exam_score;

        $pct = $totalMax > 0 ? round(($total / $totalMax) * 100, 1) : 0;

        $letter = self::computeGradeLetter($pct);
        $status = $pct >= $passPercentage ? 'passed' : 'failed';

        $this->total_score = $total;
        $this->percentage = $pct;
        $this->grade_letter = $letter;
        $this->status = $status;

        return $this;
    }

    public static function computeGradeLetter(float $pct): string
    {
        if ($pct >= 90) return 'ممتاز (Excellent)';
        if ($pct >= 80) return 'جيد جداً (Very Good)';
        if ($pct >= 70) return 'جيد (Good)';
        if ($pct >= 60) return 'مقبول (Pass)';
        return 'راسب (Fail)';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'passed' => 'ناجح ومؤهل للشهادة 🎓',
            'failed' => 'راسب ❌',
            'incomplete' => 'غير مكتمل ⏳',
            default => 'قيد الدراسة والتقييم 📝',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'passed' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'failed' => 'bg-rose-100 text-rose-800 border-rose-300',
            'incomplete' => 'bg-amber-100 text-amber-800 border-amber-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    public function getGradeBadgeClassAttribute(): string
    {
        if (str_contains($this->grade_letter, 'ممتاز')) {
            return 'bg-purple-100 text-purple-900 border-purple-300 font-black';
        }
        if (str_contains($this->grade_letter, 'جيد جداً')) {
            return 'bg-blue-100 text-blue-900 border-blue-300 font-bold';
        }
        if (str_contains($this->grade_letter, 'جيد')) {
            return 'bg-emerald-100 text-emerald-900 border-emerald-300 font-bold';
        }
        if (str_contains($this->grade_letter, 'مقبول')) {
            return 'bg-amber-100 text-amber-900 border-amber-300 font-bold';
        }
        return 'bg-rose-100 text-rose-900 border-rose-300 font-bold';
    }
}
