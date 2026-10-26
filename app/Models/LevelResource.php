<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelResource extends Model
{
    protected $fillable = [
        'course_id',
        'level_id',
        'title',
        'type',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'external_url',
        'duration_minutes',
        'exam_id',
        'order_index',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_minutes' => 'integer',
        'order_index' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function groupActivities()
    {
        return $this->hasMany(GroupActivity::class, 'level_resource_id');
    }

    // التحقق هل تم نشر هذا المورد لمجموعة محددة
    public function isSharedWithGroup($groupId): bool
    {
        return $this->groupActivities()->where('study_group_id', $groupId)->exists();
    }

    // جلب النشاط المشترك في مجموعة محددة
    public function getSharedActivityForGroup($groupId)
    {
        return $this->groupActivities()->where('study_group_id', $groupId)->latest()->first();
    }

    // أيقونة النوع
    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'book' => '📚',
            'audio' => '🎧',
            'video' => '🎥',
            'image' => '🖼️',
            'worksheet' => '📝',
            'quiz' => '🎯',
            default => '📄',
        };
    }

    // التسمية العربية للنوع
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'book' => 'كتاب ومقرر دراسي',
            'audio' => 'تسجيل صوتي',
            'video' => 'محاضرة فيديو تعليمية',
            'image' => 'صورة نشاط وبطاقة تعليمية',
            'worksheet' => 'ورقة نشاط وتطبيق',
            'quiz' => 'كويز واختبار قصير',
            default => 'مورد تعليمي',
        };
    }

    // كلاسات الألوان للبادج
    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'book' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'audio' => 'bg-purple-50 text-purple-800 border-purple-200',
            'video' => 'bg-blue-50 text-blue-800 border-blue-200',
            'image' => 'bg-pink-50 text-pink-800 border-pink-200',
            'worksheet' => 'bg-amber-50 text-amber-800 border-amber-200',
            'quiz' => 'bg-rose-50 text-rose-800 border-rose-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }
}
