<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherEntitlement extends Model
{
    protected $fillable = [
        'entitlement_no',
        'manual_no',
        'teacher_id',
        'study_group_id',
        'course_id',
        'educational_service_id',
        'entitlement_type',
        'title',
        'amount',
        'currency',
        'due_date',
        'period',
        'statement',
        'status',
        'paid_amount',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function service()
    {
        return $this->belongsTo(EducationalService::class, 'educational_service_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function entitlementTypes(): array
    {
        return [
            'course_teaching' => '📚 أجر تدريس دورة / مجموعة',
            'fixed_salary' => '💼 راتب شهري ثابت',
            'exam_prep' => '📝 إعداد بنك أسئلة واختبار',
            'grading' => '🔍 تصحيح ومراجعة كشوفات درجات',
            'supervision' => '👨‍🏫 إشراف ومتابعة أكاديمية',
            'activity' => '🎯 ورشة عمل / نشاط تدريبي',
            'custom_service' => '⚙️ خدمة تعليمية متخصصة',
            'bonus' => '🎁 مكافأة / بدل تميز',
            'other' => '📌 مستحقات أخرى',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::entitlementTypes()[$this->entitlement_type] ?? 'مستحق مالي';
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float)$this->amount - (float)$this->paid_amount);
    }

    public function getCurrencySymbolAttribute(): string
    {
        return \App\Helpers\ArabicCurrencyHelper::currencySymbol($this->currency);
    }

    public function getCurrencyNameAttribute(): string
    {
        return \App\Helpers\ArabicCurrencyHelper::currencyName($this->currency);
    }

    public function getFormattedAmountAttribute(): string
    {
        return \App\Helpers\ArabicCurrencyHelper::format($this->amount, $this->currency);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'approved' => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'label' => 'معتمد وغير مسدد'],
            'partially_paid' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'مسدد جزئياً'],
            'paid' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'مسدد بالكامل ✅'],
            'cancelled' => ['bg' => 'bg-red-50 text-red-700 border-red-200', 'label' => 'ملغي ❌'],
            default => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'label' => $this->status],
        };
    }
}
