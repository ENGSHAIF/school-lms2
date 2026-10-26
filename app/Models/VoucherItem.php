<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherItem extends Model
{
    protected $fillable = [
        'voucher_id',
        'account_id',
        'amount',
        'statement',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }

    public function getFormattedAmountAttribute(): string
    {
        $currency = $this->voucher?->currency ?? 'YER';
        return \App\Helpers\ArabicCurrencyHelper::format($this->amount, $currency);
    }
}
