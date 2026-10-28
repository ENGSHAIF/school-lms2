<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashInventoryCount extends Model
{
    protected $fillable = [
        'count_no',
        'cash_box_id',
        'count_date',
        'currency',
        'system_balance',
        'counted_balance',
        'variance',
        'status',
        'denominations',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'count_date' => 'date',
        'system_balance' => 'decimal:2',
        'counted_balance' => 'decimal:2',
        'variance' => 'decimal:2',
        'denominations' => 'array',
    ];

    public function cashBox()
    {
        return $this->belongsTo(FinancialAccount::class, 'cash_box_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'balanced' => 'مطابق تماماً',
            'surplus' => 'فائض نقدي',
            'deficit' => 'عجز نقدي',
            default => $this->status,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'balanced' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/30 dark:text-emerald-300',
            'surplus' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/30 dark:text-blue-300',
            'deficit' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-900/30 dark:text-rose-300',
            default => 'bg-slate-100 text-slate-800',
        };
    }
}
