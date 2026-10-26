<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'voucher_no',
        'manual_no',
        'voucher_type',
        'is_compound',
        'account_id',
        'cash_or_bank_account_id',
        'amount',
        'currency',
        'exchange_rate',
        'amount_local',
        'amount_in_words',
        'payment_method',
        'statement',
        'pdf_path',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(VoucherItem::class, 'voucher_id');
    }

    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }

    public function cashOrBankAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'cash_or_bank_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalEntry()
    {
        return $this->hasOne(JournalEntry::class, 'reference_id')->where('reference_type', 'voucher');
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
