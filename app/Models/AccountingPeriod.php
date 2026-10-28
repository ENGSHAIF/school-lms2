<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AccountingPeriod extends Model
{
    protected $fillable = [
        'fiscal_year',
        'period_number',
        'name',
        'start_date',
        'end_date',
        'status',
        'closed_at',
        'closed_by',
        'notes',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'period_number' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'closed_at' => 'datetime',
    ];

    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'مفتوحة (تقبل العمليات)',
            'suspended' => 'موقوفة مؤقتاً',
            'closed' => 'مقفلة نهائياً',
            default => $this->status,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'open' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/30 dark:text-emerald-400',
            'suspended' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/30 dark:text-amber-400',
            'closed' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-900/30 dark:text-rose-400',
            default => 'bg-slate-100 text-slate-800',
        };
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeForDate($query, $date)
    {
        $d = Carbon::parse($date)->format('Y-m-d');

        return $query->where('start_date', '<=', $d)->where('end_date', '>=', $d);
    }

    /**
     * التحقق الصارم مما إذا كان التاريخ يقع ضمن فترة محاسبية مفتوحة (معيار يمن سوفت)
     */
    public static function isDateAllowed($date): bool
    {
        $period = static::forDate($date)->first();
        if (! $period) {
            return true; // إذا لم تكن الفترات مهيأة بعد نسمح بالعمليات
        }

        return $period->isOpen();
    }
}
