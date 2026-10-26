<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalService extends Model
{
    protected $fillable = [
        'service_code',
        'name',
        'service_type',
        'default_fee',
        'currency',
        'unit_type',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'default_fee' => 'decimal:2',
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

    public function entitlements()
    {
        return $this->hasMany(TeacherEntitlement::class, 'educational_service_id');
    }

    public static function serviceTypes(): array
    {
        return [
            'exam_prep' => '📝 إعداد بنك أسئلة واختبارات',
            'grading' => '🔍 تصحيح ومراجعة نتائج وكشوفات',
            'supervision' => '👨‍🏫 إشراف ومتابعة أكاديمية لمجموعة',
            'activity' => '🎯 ورشة عمل / نشاط تدريبي',
            'tutoring' => '⏱️ ساعات تدريب خاصة / فردية',
            'consulting' => '💼 استشارات وترجمة متخصصة',
            'other' => '⚙️ خدمات تعليمية وإدارية أخرى',
        ];
    }

    public static function unitTypes(): array
    {
        return [
            'fixed' => 'مبلغ مقطوع',
            'per_course' => 'لكل دورة',
            'per_group' => 'لكل مجموعة دراسية',
            'per_exam' => 'لكل اختبار / كويز',
            'per_hour' => 'لكل ساعة تدريبية',
            'per_student' => 'لكل طالب',
        ];
    }

    public function getServiceTypeLabelAttribute(): string
    {
        return self::serviceTypes()[$this->service_type] ?? 'خدمة تعليمية';
    }

    public function getUnitTypeLabelAttribute(): string
    {
        return self::unitTypes()[$this->unit_type] ?? 'مقطوع';
    }

    public function getCurrencySymbolAttribute(): string
    {
        return \App\Helpers\ArabicCurrencyHelper::currencySymbol($this->currency);
    }

    public function getFormattedFeeAttribute(): string
    {
        return \App\Helpers\ArabicCurrencyHelper::format($this->default_fee, $this->currency);
    }
}
