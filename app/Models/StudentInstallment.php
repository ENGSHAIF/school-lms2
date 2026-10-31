<?php

namespace App\Models;

use App\Helpers\ArabicCurrencyHelper;
use Illuminate\Database\Eloquent\Model;

class StudentInstallment extends Model
{
    protected $fillable = [
        'user_id',
        'level_id',
        'course_id',
        'installment_number',
        'amount',
        'currency',
        'due_date',
        'paid_amount',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function getCurrencyNameAttribute(): string
    {
        return ArabicCurrencyHelper::currencyName($this->currency);
    }

    public function getCurrencySymbolAttribute(): string
    {
        return ArabicCurrencyHelper::currencySymbol($this->currency);
    }

    public function getFormattedAmountAttribute(): string
    {
        return ArabicCurrencyHelper::format($this->amount, $this->currency);
    }
}
