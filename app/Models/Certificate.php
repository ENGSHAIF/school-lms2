<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'certificate_number',
        'student_id',
        'study_group_id',
        'course_id',
        'level_id',
        'type',
        'title',
        'student_name_ar',
        'student_name_en',
        'course_name_ar',
        'course_name_en',
        'level_name_en',
        'hours',
        'total_score',
        'percentage',
        'grade_letter',
        'issue_date',
        'qr_code_hash',
        'is_verified',
        'issued_by',
    ];

    protected $casts = [
        'hours' => 'integer',
        'total_score' => 'float',
        'percentage' => 'float',
        'is_verified' => 'boolean',
        'issue_date' => 'date',
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

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function getVerificationUrlAttribute(): string
    {
        return url('/verify/certificate/' . $this->certificate_number);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'diploma' => 'شهادة دبلوم معتمد 🎓',
            'level' => 'شهادة إتمام مستوى 📜',
            default => 'شهادة إتمام دورة تدريبية 🏆',
        };
    }
}
