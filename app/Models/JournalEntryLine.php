<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntryLine extends Model
{
    protected $fillable = [
        'entry_id',
        'account_id',
        'cost_center_id',
        'debit',
        'credit',
        'line_description',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function entry()
    {
        return $this->belongsTo(JournalEntry::class, 'entry_id');
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
