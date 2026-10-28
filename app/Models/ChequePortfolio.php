<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChequePortfolio extends Model
{
    protected $fillable = [
        'voucher_id',
        'account_id',
        'bank_id',
        'cheque_no',
        'bank_name',
        'cheque_type',
        'amount',
        'currency',
        'due_date',
        'status',
        'collected_at',
        'collected_by',
        'endorsed_to_account_id',
        'extension_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'extension_date' => 'date',
        'collected_at' => 'datetime',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'bank_id');
    }

    public function endorsedToAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'endorsed_to_account_id');
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'under_collection' => 'تحت التحصيل / الصرف',
            'collected' => 'تم التحصيل / الصرف',
            'returned' => 'مرتجع (مرفوض)',
            'endorsed' => 'مجيّر لطرف ثالث',
            'extended' => 'مؤجل / ممدد الأجل',
            default => $this->status,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'under_collection' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/30 dark:text-amber-300',
            'collected' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/30 dark:text-emerald-300',
            'returned' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-900/30 dark:text-rose-300',
            'endorsed' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/30 dark:text-blue-300',
            'extended' => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-900/30 dark:text-purple-300',
            default => 'bg-slate-100 text-slate-800',
        };
    }

    public function getChequeTypeLabelAttribute(): string
    {
        return $this->cheque_type === 'receivable' ? 'ورقة قبض (شيك وارد)' : 'ورقة دفع (شيك صادر)';
    }
}
