<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyPortfolio extends Model
{
    protected $fillable = [
        'portfolio_no',
        'portfolio_type',
        'portfolio_date',
        'cash_box_id',
        'currency',
        'total_amount',
        'status',
        'voucher_id',
        'approved_by',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'portfolio_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(DailyPortfolioItem::class, 'portfolio_id');
    }

    public function cashBox()
    {
        return $this->belongsTo(FinancialAccount::class, 'cash_box_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'مسودة غير معتمدة',
            'approved' => 'معتمدة جاهزة للترحيل',
            'posted_to_voucher' => 'مرحلة بسند رسمي',
            default => $this->status,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/30 dark:text-amber-300',
            'approved' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/30 dark:text-blue-300',
            'posted_to_voucher' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/30 dark:text-emerald-300',
            default => 'bg-slate-100 text-slate-800',
        };
    }

    public function getPortfolioTypeLabelAttribute(): string
    {
        return $this->portfolio_type === 'receipt' ? 'حافظة قبض يومية' : 'حافظة صرف يومية';
    }
}
