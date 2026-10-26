<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseGradeScale extends Model
{
    protected $fillable = [
        'course_id',
        'study_group_id',
        'level_id',
        'name',
        'attendance_max',
        'participation_max',
        'activities_max',
        'midterm_max',
        'practical_max',
        'final_exam_max',
        'total_max',
        'passing_percentage',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'attendance_max' => 'float',
        'participation_max' => 'float',
        'activities_max' => 'float',
        'midterm_max' => 'float',
        'practical_max' => 'float',
        'final_exam_max' => 'float',
        'total_max' => 'float',
        'passing_percentage' => 'float',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // جلب أو إنشاء السلم الافتراضي للمجموعة أو الدورة
    public static function getScaleForGroup(?int $groupId, ?int $courseId = null): self
    {
        if ($groupId) {
            $scale = self::where('study_group_id', $groupId)->first();
            if ($scale) return $scale;
        }

        if ($courseId) {
            $scale = self::where('course_id', $courseId)->whereNull('study_group_id')->first();
            if ($scale) return $scale;
        }

        // إرجاع سلم قياسي افتراضي
        return new self([
            'name' => 'السلم القياسي للدرجات (100 درجة)',
            'attendance_max' => 10.00,
            'participation_max' => 10.00,
            'activities_max' => 15.00,
            'midterm_max' => 20.00,
            'practical_max' => 15.00,
            'final_exam_max' => 30.00,
            'total_max' => 100.00,
            'passing_percentage' => 60.00,
        ]);
    }
}
