<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'level',
        'is_parent',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_parent' => 'boolean',
        'is_active' => 'boolean',
        'level' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(CostCenter::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CostCenter::class, 'parent_id')->orderBy('code');
    }

    public function journalLines()
    {
        return $this->hasMany(JournalEntryLine::class, 'cost_center_id');
    }

    public function paymentRequests()
    {
        return $this->hasMany(PaymentRequest::class, 'cost_center_id');
    }

    // حساب إجمالي إيرادات مركز التكلفة
    public function getTotalRevenuesAttribute(): float
    {
        return (float) $this->journalLines()
            ->whereHas('account', fn($q) => $q->where('account_category', 'revenue'))
            ->sum('credit');
    }

    // حساب إجمالي مصروفات مركز التكلفة
    public function getTotalExpensesAttribute(): float
    {
        return (float) $this->journalLines()
            ->whereHas('account', fn($q) => $q->where('account_category', 'expense'))
            ->sum('debit');
    }

    // حساب صافي ربح أو خسارة مركز التكلفة
    public function getNetProfitAttribute(): float
    {
        return $this->total_revenues - $this->total_expenses;
    }
}
