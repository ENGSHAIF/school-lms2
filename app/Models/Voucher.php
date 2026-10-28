<?php

namespace App\Models;

use App\Helpers\ArabicCurrencyHelper;
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
        'cheque_no',
        'cheque_bank_name',
        'cheque_due_date',
        'is_posted',
        'is_suspended',
        'posted_at',
        'posted_by',
        'pdf_path',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'amount_local' => 'decimal:2',
        'is_compound' => 'boolean',
        'is_posted' => 'boolean',
        'is_suspended' => 'boolean',
        'cheque_due_date' => 'date',
        'posted_at' => 'datetime',
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
