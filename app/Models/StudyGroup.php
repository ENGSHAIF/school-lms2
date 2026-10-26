<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StudyGroup extends Model
{
    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'course_id',
        'level_id',
        'term',
        'batch',
        'program_type',
        'duration_months',
        'start_date',
        'end_date',
        'teacher_id',
        'supervisor_id',
        'max_students',
        'schedule_days',
        'schedule_time',
        'room_id',
        'is_live',
        'live_status',
        'live_requested_by',
        'live_requested_at',
        'live_approved_by',
        'live_started_at',
        'status',
        'description',
    ];

    public function getDisplayNameAttribute(): string
    {
        if ($this->name_ar && $this->name_en) {
            return "{$this->name_ar} ({$this->name_en})";
        }
        return $this->name_ar ?: ($this->name ?: ($this->name_en ?: ''));
    }

    protected $casts = [
        'is_live' => 'boolean',
        'live_requested_at' => 'datetime',
        'live_started_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'duration_months' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($group) {
            if (empty($group->room_id)) {
                $group->room_id = 'asots-grp-' . strtolower(Str::random(8));
            }
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // المعلمون المتعددون للمجموعة (رئيسي ومساعدون)
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'study_group_teachers', 'study_group_id', 'user_id')
            ->withPivot('is_primary', 'role_title')
            ->withTimestamps();
    }

    // المشرفون المتعددون للمجموعة
    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'study_group_supervisors', 'study_group_id', 'user_id')
            ->withPivot('is_primary', 'notes')
            ->withTimestamps();
    }

    // جلب المعلم الرئيسي
    public function primaryTeacher()
    {
        $primary = $this->teachers->where('pivot.is_primary', true)->first();
        return $primary ?: $this->teacher;
    }

    // جلب المعلمين المساعدين أو الإضافيين
    public function assistantTeachers()
    {
        return $this->teachers->where('pivot.is_primary', false);
    }

    // جلب المشرف الرئيسي
    public function primarySupervisor()
    {
        $primary = $this->supervisors->where('pivot.is_primary', true)->first();
        return $primary ?: $this->supervisor;
    }

    // جلب المشرفين المساعدين
    public function assistantSupervisors()
    {
        return $this->supervisors->where('pivot.is_primary', false);
    }

    public function groupStudents()
    {
        return $this->hasMany(StudyGroupStudent::class, 'study_group_id');
    }

    public function entitlements()
    {
        return $this->hasMany(TeacherEntitlement::class, 'study_group_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'study_group_students', 'study_group_id', 'student_id')
            ->withPivot('status', 'enrolled_at', 'notes')
            ->withTimestamps();
    }

    public function activeStudents()
    {
        return $this->students()->wherePivot('status', 'active');
    }

    public function getActiveCountAttribute()
    {
        return $this->groupStudents()->where('status', 'active')->count();
    }

    public function isFull(): bool
    {
        return $this->active_count >= $this->max_students;
    }

    public function getAvailableSeatsAttribute(): int
    {
        return max(0, $this->max_students - $this->active_count);
    }

    // أنشطة وصوتيات وأوراق عمل المجموعة
    public function activities()
    {
        return $this->hasMany(GroupActivity::class, 'study_group_id')->latest();
    }

    // الطالب أو المستخدم الذي طلب فتح البث
    public function requester()
    {
        return $this->belongsTo(User::class, 'live_requested_by');
    }

    // الإداري أو المشرف الذي اعتمد فتح البث
    public function approver()
    {
        return $this->belongsTo(User::class, 'live_approved_by');
    }

    // التحقق هل البث الحي مفتوح حالياً
    public function isLive(): bool
    {
        return (bool) $this->is_live;
    }

    // التحقق هل هناك طلب معلق لفتح البث
    public function isPendingApproval(): bool
    {
        return $this->live_status === 'pending_approval';
    }

    // جلسات وحصص المجموعة اليومية
    public function sessions()
    {
        return $this->hasMany(GroupSession::class, 'study_group_id')->latest('session_date');
    }

    // كافة تقييمات طلاب المجموعة
    public function evaluations()
    {
        return $this->hasMany(GroupStudentEvaluation::class, 'study_group_id');
    }

    // حساب متوسط نسبة الحضور العام للمجموعة
    public function getAverageAttendanceRateAttribute(): float
    {
        return round($this->sessions()->avg('attendance_rate') ?? 100, 1);
    }

    public function gradeScale()
    {
        return $this->hasOne(CourseGradeScale::class, 'study_group_id');
    }

    public function studentGrades()
    {
        return $this->hasMany(StudentCourseGrade::class, 'study_group_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'study_group_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'study_group_id');
    }
}
