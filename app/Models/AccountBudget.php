<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountBudget extends Model
{
    protected $fillable = [
        'account_id',
        'cost_center_id',
        'fiscal_year',
        'month',
        'budget_amount',
        'actual_amount',
        'variance',
        'currency',
        'notes',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'month' => 'integer',
        'budget_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'variance' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }

    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ];

        return $months[$this->month] ?? "شهر {$this->month}";
    }

    public function getVarianceStatusAttribute(): string
    {
        if ($this->variance > 0) {
            return 'وفر'; // Under budget
        } elseif ($this->variance < 0) {
            return 'تجاوز'; // Over budget
        }

        return 'مطابق';
    }
}
