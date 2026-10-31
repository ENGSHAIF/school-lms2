<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupActivity extends Model
{
    protected $fillable = [
        'study_group_id',
        'level_resource_id',
        'user_id',
        'title',
        'type',
        'instructions',
        'file_path',
        'file_name',
        'file_size',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function group()
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function levelResource()
    {
        return $this->belongsTo(LevelResource::class, 'level_resource_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isStudentSubmission(): bool
    {
        return $this->publisher?->isStudent() || $this->type === 'student_submission';
    }

    public function isAudio(): bool
    {
        if ($this->type === 'audio') return true;
        if (!$this->file_path) return false;
        $ext = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        return in_array($ext, ['mp3', 'wav', 'ogg', 'webm', 'm4a', 'aac']);
    }

    public function isImage(): bool
    {
        if ($this->type === 'image') return true;
        if (!$this->file_path) return false;
        $ext = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
    }

    public function isVideo(): bool
    {
        if ($this->type === 'video') return true;
        if (!$this->file_path) return false;
        $ext = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        return in_array($ext, ['mp4', 'webm', 'mov', 'avi', 'mkv', 'm4v', '3gp']);
    }

    // أيقونة نوع النشاط
    public function getTypeIconAttribute(): string
    {
        if ($this->isStudentSubmission()) {
            return match ($this->type) {
                'audio' => '🎙️',
                'image' => '📷',
                default => '📝',
            };
        }

        return match ($this->type) {
            'book' => '📚',
            'audio' => '🎧',
            'video' => '🎥',
            'image' => '🖼️',
            'worksheet' => '📝',
            'quiz' => '🎯',
            'announcement' => '📢',
            'student_submission' => '📝',
            default => '📄',
        };
    }

    // التسمية العربية للنوع
    public function getTypeLabelAttribute(): string
    {
        if ($this->isStudentSubmission()) {
            return match ($this->type) {
                'audio' => 'تسجيل صوتي من الطالب',
                'image' => 'صورة إجابة الطالب',
                default => 'ورقة إجابة / تسليم واجب',
            };
        }

        return match ($this->type) {
            'book' => 'كتاب المنهج الدراسي',
            'audio' => 'تسجيل صوتي',
            'video' => 'محاضرة فيديو تعليمية',
            'image' => 'صورة نشاط وبطاقة تعليمية',
            'worksheet' => 'ورقة نشاط / واجب',
            'quiz' => 'كويز واختبار قصير',
            'announcement' => 'تنبيه وملاحظة',
            'student_submission' => 'تسليم إجابة طالب',
            default => 'ورقة نشاط / واجب',
        };
    }
}
