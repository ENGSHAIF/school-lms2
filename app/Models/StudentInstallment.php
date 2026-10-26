<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentInstallment extends Model
{
    protected $fillable = [
        'user_id',
        'level_id',
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

    public function getCurrencyNameAttribute(): string
    {
        return \App\Helpers\ArabicCurrencyHelper::currencyName($this->currency);
    }

    public function getCurrencySymbolAttribute(): string
    {
        return \App\Helpers\ArabicCurrencyHelper::currencySymbol($this->currency);
    }

    public function getFormattedAmountAttribute(): string
    {
        return \App\Helpers\ArabicCurrencyHelper::format($this->amount, $this->currency);
    }
}
