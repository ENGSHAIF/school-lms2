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
        'lesson_objectives',
        'curriculum_objectives',
        'lesson_summary',
        'lesson_summary_file',
        'lesson_summary_file_name',
        'homework_assigned',
        'homework_file',
        'homework_file_name',
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

    public function hasSummaryFile(): bool
    {
        return ! empty($this->lesson_summary_file);
    }

    public function hasHomeworkFile(): bool
    {
        return ! empty($this->homework_file);
    }

    public function getSummaryFileUrlAttribute(): ?string
    {
        return $this->lesson_summary_file ? asset($this->lesson_summary_file) : null;
    }

    public function getHomeworkFileUrlAttribute(): ?string
    {
        return $this->homework_file ? asset($this->homework_file) : null;
    }

    public function getSummaryFileIconAttribute(): string
    {
        if (! $this->lesson_summary_file) {
            return '📄';
        }
        $ext = strtolower(pathinfo($this->lesson_summary_file, PATHINFO_EXTENSION));

        return match ($ext) {
            'pdf' => '📕',
            'doc', 'docx' => '📘',
            'xls', 'xlsx' => '📊',
            'ppt', 'pptx' => '📙',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => '🖼️',
            'mp3', 'wav', 'm4a', 'ogg' => '🎙️',
            'mp4', 'mov', 'avi' => '🎥',
            'zip', 'rar' => '📦',
            default => '📎',
        };
    }

    public function getHomeworkFileIconAttribute(): string
    {
        if (! $this->homework_file) {
            return '📝';
        }
        $ext = strtolower(pathinfo($this->homework_file, PATHINFO_EXTENSION));

        return match ($ext) {
            'pdf' => '📕',
            'doc', 'docx' => '📘',
            'xls', 'xlsx' => '📊',
            'ppt', 'pptx' => '📙',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => '🖼️',
            'mp3', 'wav', 'm4a', 'ogg' => '🎙️',
            'mp4', 'mov', 'avi' => '🎥',
            'zip', 'rar' => '📦',
            default => '📎',
        };
    }
}
