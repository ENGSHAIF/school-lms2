<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyPortfolioItem extends Model
{
    protected $fillable = [
        'portfolio_id',
        'account_id',
        'cost_center_id',
        'amount',
        'statement',
        'payee_or_payer',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function portfolio()
    {
        return $this->belongsTo(DailyPortfolio::class, 'portfolio_id');
    }

    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }
}
