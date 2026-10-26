<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'entry_no',
        'entry_date',
        'reference_type',
        'reference_id',
        'description',
        'total_debit',
        'total_credit',
        'is_posted',
        'posted_at',
        'posted_by',
        'created_by',
        'reversed_by_entry_id',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'is_posted' => 'boolean',
        'posted_at' => 'datetime',
    ];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class, 'entry_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function reversedBy()
    {
        return $this->belongsTo(JournalEntry::class, 'reversed_by_entry_id');
    }

    // هل القيد متزن محاسبياً؟
    public function getIsBalancedAttribute(): bool
    {
        return abs((float)$this->total_debit - (float)$this->total_credit) < 0.001;
    }

    public function getReferenceTypeLabelAttribute(): string
    {
        return match($this->reference_type) {
            'voucher'  => 'سند صرف / قبض',
            'billing'  => 'استحقاق رسوم طالب',
            'payroll'  => 'استحقاق رواتب كادر',
            'manual'   => 'قيد يومية يدوي',
            'reversal' => 'قيد عكسي / تسوية',
            default    => $this->reference_type ?? 'قيد محاسبي',
        };
    }
}
