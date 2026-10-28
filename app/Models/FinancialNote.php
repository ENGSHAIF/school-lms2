<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialNote extends Model
{
    protected $fillable = [
        'note_no',
        'note_type',
        'note_date',
        'account_id',
        'secondary_account_id',
        'cost_center_id',
        'amount',
        'currency',
        'exchange_rate',
        'statement',
        'status',
        'journal_entry_id',
        'created_by',
    ];

    protected $casts = [
        'note_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
    ];

    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }

    public function secondaryAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'secondary_account_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getNoteTypeLabelAttribute(): string
    {
        return $this->note_type === 'debit' ? 'إشعار مدين (قيد على العميل/المورد)' : 'إشعار دائن (إضافة للعميل/المورد)';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'posted' ? 'مرحل ومقيد' : 'مسودة غير مرحلة';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status === 'posted'
            ? 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/30 dark:text-emerald-300'
            : 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/30 dark:text-amber-300';
    }
}
