<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherContract extends Model
{
    protected $fillable = [
        'teacher_id',
        'contract_type',
        'calculation_type',
        'rate',
        'monthly_salary',
        'default_hourly_rate',
        'currency',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'monthly_salary' => 'decimal:2',
        'default_hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public static function contractTypes(): array
    {
        return [
            'fixed_salary' => '🏢 موظف رسمي براتب شهري ثابت',
            'per_course' => '📚 متعاقد بنظام الدورة / المجموعة',
            'hourly' => '⏱️ متعاقد بنظام أجر الساعة',
            'percentage' => '📊 نسبة مئوية من رسوم الطلاب',
            'services_only' => '⚙️ متعاقد حسب الخدمات المنفذة',
        ];
    }

    public function getContractTypeLabelAttribute(): string
    {
        return self::contractTypes()[$this->contract_type] ?? self::contractTypes()[$this->calculation_type] ?? 'متعاقد بالدورة';
    }

    public function getCurrencySymbolAttribute(): string
    {
        return \App\Helpers\ArabicCurrencyHelper::currencySymbol($this->currency);
    }
}
