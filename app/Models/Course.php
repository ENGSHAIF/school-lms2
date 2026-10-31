<?php

namespace App\Models;

use App\Helpers\ArabicCurrencyHelper;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'level_id',
        'name',
        'name_ar',
        'name_en',
        'hours',
        'fee_amount',
        'original_fee',
        'teacher_fee',
        'teacher_hourly_rate',
        'currency',
        'course_order',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'hours' => 'integer',
        'fee_amount' => 'decimal:2',
        'original_fee' => 'decimal:2',
        'teacher_fee' => 'decimal:2',
        'teacher_hourly_rate' => 'decimal:2',
        'course_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // المستوى الأكاديمي الحاضن لهذه الدورة
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    // التوافق العكسي في حال تم استدعاء levels
    public function levels()
    {
        return $this->hasMany(Level::class, 'course_id')->orderBy('level_order');
    }

    // الأقسام والوحدات والموضوعات / الأيام الدراسية التابعة للدورة
    public function sections()
    {
        return $this->hasMany(CourseSection::class, 'course_id')->orderBy('order_index');
    }

    public function studyGroups()
    {
        return $this->hasMany(StudyGroup::class, 'course_id');
    }

    public function gradeScale()
    {
        return $this->hasOne(CourseGradeScale::class, 'course_id');
    }

    public function studentGrades()
    {
        return $this->hasMany(StudentCourseGrade::class, 'course_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'course_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'course_id');
    }

    // بنك مناهج وموارد الدورة التعليمية (كتب، صوتيات، فيديوهات، صور، أنشطة، كويزات)
    public function resources()
    {
        return $this->hasMany(LevelResource::class, 'course_id')->orderBy('order_index')->latest();
    }

    public function bookResources()
    {
        return $this->resources()->where('type', 'book');
    }

    public function audioResources()
    {
        return $this->resources()->where('type', 'audio');
    }

    public function videoResources()
    {
        return $this->resources()->where('type', 'video');
    }

    public function imageResources()
    {
        return $this->resources()->where('type', 'image');
    }

    public function worksheetResources()
    {
        return $this->resources()->where('type', 'worksheet');
    }

    public function quizResources()
    {
        return $this->resources()->where('type', 'quiz');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->name_ar && $this->name_en) {
            return "{$this->name_ar} ({$this->name_en})";
        }

        return $this->name_ar ?: ($this->name ?: ($this->name_en ?: ''));
    }

    public function getTitleAttribute(): string
    {
        return $this->display_name ?: ($this->name ?: ($this->name_ar ?: ($this->name_en ?: '')));
    }

    public function getCurrencyNameAttribute(): string
    {
        return ArabicCurrencyHelper::currencyName($this->currency);
    }

    public function getCurrencySymbolAttribute(): string
    {
        return ArabicCurrencyHelper::currencySymbol($this->currency);
    }

    public function getFormattedFeeAttribute(): string
    {
        return ArabicCurrencyHelper::format($this->fee_amount, $this->currency);
    }

    public function getFormattedTeacherFeeAttribute(): string
    {
        return ArabicCurrencyHelper::format($this->teacher_fee, $this->currency);
    }

    public function getFormattedOriginalFeeAttribute(): ?string
    {
        return $this->original_fee ? ArabicCurrencyHelper::format($this->original_fee, $this->currency) : null;
    }
}
