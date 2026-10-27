<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_type',
        'survey_date',
        'student_id',
        'student_name',
        'student_phone',
        'is_anonymous',
        'teacher_id',
        'study_group_id',
        'course_id',
        'teacher_rating',
        'teacher_clarity',
        'teacher_competence',
        'teacher_interaction',
        'teacher_punctuality',
        'curriculum_rating',
        'curriculum_suitability',
        'curriculum_alignment',
        'support_rating',
        'admin_communication',
        'management_organization',
        'remote_learning_ease',
        'remote_learning_difficulties',
        'daily_benefited',
        'daily_level_appropriate',
        'learning_challenges',
        'overall_score',
        'recommend_institute',
        'recommend_reason',
        'best_feature',
        'notes_suggestions',
        'raw_answers',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'survey_date' => 'date',
            'is_anonymous' => 'boolean',
            'learning_challenges' => 'array',
            'raw_answers' => 'array',
            'teacher_rating' => 'float',
            'curriculum_rating' => 'float',
            'support_rating' => 'float',
            'overall_score' => 'float',
            'teacher_clarity' => 'integer',
            'teacher_competence' => 'integer',
            'teacher_interaction' => 'integer',
            'teacher_punctuality' => 'integer',
            'curriculum_suitability' => 'integer',
            'curriculum_alignment' => 'integer',
            'admin_communication' => 'integer',
            'management_organization' => 'integer',
            'remote_learning_ease' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (StudentSurvey $survey) {
            $survey->autoCalculateScores();
        });
    }

    /**
     * حساب المتوسطات الرقمية والدرجات الإجمالية بشكل آلي
     */
    public function autoCalculateScores(): void
    {
        // 1. حساب متوسط المدرس
        $teacherScores = array_filter([
            $this->teacher_clarity,
            $this->teacher_competence,
            $this->teacher_interaction,
            $this->teacher_punctuality,
        ], fn ($val) => ! is_null($val) && $val > 0);

        if (! empty($teacherScores)) {
            $this->teacher_rating = round(array_sum($teacherScores) / count($teacherScores), 2);
        }

        // 2. حساب متوسط المنهج
        $curriculumScores = array_filter([
            $this->curriculum_suitability,
            $this->curriculum_alignment,
        ], fn ($val) => ! is_null($val) && $val > 0);

        if (! empty($curriculumScores)) {
            $this->curriculum_rating = round(array_sum($curriculumScores) / count($curriculumScores), 2);
        }

        // 3. حساب متوسط الدعم والإدارة
        $supportScores = array_filter([
            $this->admin_communication,
            $this->management_organization,
            $this->remote_learning_ease,
        ], fn ($val) => ! is_null($val) && $val > 0);

        if (! empty($supportScores)) {
            $this->support_rating = round(array_sum($supportScores) / count($supportScores), 2);
        }

        // 4. حساب النتيجة الإجمالية الموزونة للاستبيان
        $allNumericScores = array_merge($teacherScores, $curriculumScores, $supportScores);

        // إذا كان تقييماً يومياً سريعاً وحوى أسئلة نعم/لا/جزئياً
        if ($this->survey_type === 'daily') {
            $dailyScores = [];
            if ($this->teacher_clarity) {
                $dailyScores[] = (float) $this->teacher_clarity;
            }
            if ($this->teacher_interaction) {
                $dailyScores[] = (float) $this->teacher_interaction;
            }
            if ($this->daily_benefited === 'yes') {
                $dailyScores[] = 5.0;
            } elseif ($this->daily_benefited === 'partial') {
                $dailyScores[] = 3.0;
            } elseif ($this->daily_benefited === 'no') {
                $dailyScores[] = 1.0;
            }
            if ($this->daily_level_appropriate === 'yes') {
                $dailyScores[] = 5.0;
            } elseif ($this->daily_level_appropriate === 'no') {
                $dailyScores[] = 2.0;
            }

            if (! empty($dailyScores)) {
                $this->overall_score = round(array_sum($dailyScores) / count($dailyScores), 2);
            }
        } elseif (! empty($allNumericScores)) {
            $this->overall_score = round(array_sum($allNumericScores) / count($allNumericScores), 2);
        }
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function studyGroup(): BelongsTo
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // Scopes
    public function scopeDaily($query)
    {
        return $query->where('survey_type', 'daily');
    }

    public function scopeWeekly($query)
    {
        return $query->where('survey_type', 'weekly');
    }

    public function scopeDiplomaFinal($query)
    {
        return $query->where('survey_type', 'diploma_final');
    }

    public function scopeForTeacher($query, $teacherId)
    {
        if (! empty($teacherId)) {
            return $query->where('teacher_id', $teacherId);
        }

        return $query;
    }

    // Accessors
    public function getTypeLabelAttribute(): string
    {
        return match ($this->survey_type) {
            'daily' => 'التقييم اليومي السريع',
            'weekly' => 'التقييم الأسبوعي الشامل',
            'diploma_final' => 'التقييم النهائي للدبلوم',
            default => 'استبيان تقييم',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->survey_type) {
            'daily' => '⚡',
            'weekly' => '📊',
            'diploma_final' => '🎓',
            default => '📝',
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->survey_type) {
            'daily' => 'bg-amber-100 text-amber-800 border-amber-200',
            'weekly' => 'bg-blue-100 text-blue-800 border-blue-200',
            'diploma_final' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'طالب (هوية سرية)';
        }

        return $this->student_name ?: ($this->student?->name ?: 'طالب مسجل');
    }
}
