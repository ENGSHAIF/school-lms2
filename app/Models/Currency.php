<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'currency_code',
        'name',
        'name_en',
        'symbol',
        'fraction_name',
        'is_local',
        'exchange_rate',
        'max_exchange_rate',
        'min_exchange_rate',
        'valuation_rate',
        'buy_rate',
        'max_buy_rate',
        'sell_rate',
        'min_sell_rate',
        'is_default_transfer',
        'is_active',
        'is_stopped',
        'denominations',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_local' => 'boolean',
        'is_default_transfer' => 'boolean',
        'is_active' => 'boolean',
        'is_stopped' => 'boolean',
        'exchange_rate' => 'float',
        'max_exchange_rate' => 'float',
        'min_exchange_rate' => 'float',
        'valuation_rate' => 'float',
        'buy_rate' => 'float',
        'max_buy_rate' => 'float',
        'sell_rate' => 'float',
        'min_sell_rate' => 'float',
        'denominations' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // جلب العملة المحلية الرئيسية للمنشأة (سعرها دائمًا = 1.0000)
    public static function localCurrency(): ?self
    {
        return static::where('is_local', true)->first()
            ?? static::where('currency_code', 'YER')->first();
    }

    // جلب سعر الصرف لعملة محددة مقابل العملة المحلية
    public static function getRate(string $currencyCode): float
    {
        $code = strtoupper(trim($currencyCode));
        $currency = static::where('currency_code', $code)->first();

        if (! $currency) {
            return match ($code) {
                'SAR' => 142.0000,
                'USD' => 535.0000,
                default => 1.0000,
            };
        }

        return (float) $currency->exchange_rate ?: 1.0000;
    }

    /**
     * المصارفة الآلية بين أي عملتين مسجلتين في النظام
     *
     * @param  float  $amount  المبلغ المراد تحويله
     * @param  string  $fromCode  العملة المصدرية (مثلاً: SAR أو USD أو YER)
     * @param  string  $toCode  العملة الهدف (مثلاً: YER أو SAR أو USD)
     * @return float المبلغ المحول مقرباً لمنزلتين عشريتين
     */
    public static function convert(float $amount, string $fromCode, string $toCode): float
    {
        $from = strtoupper(trim($fromCode));
        $to = strtoupper(trim($toCode));

        if ($from === $to || $amount == 0) {
            return round($amount, 2);
        }

        $rateFrom = static::getRate($from);
        $rateTo = static::getRate($to);

        // تحويل المبلغ أولاً إلى العملة المحلية (الريال اليمني)
        $amountInLocal = $amount * $rateFrom;

        // ثم تحويل المبلغ من العملة المحلية إلى العملة الهدف
        if ($rateTo <= 0) {
            return round($amountInLocal, 2);
        }

        return round($amountInLocal / $rateTo, 2);
    }

    // تسمية نوع العملة بالعربية
    public function getTypeLabelAttribute(): string
    {
        return $this->is_local ? 'عملة محلية (رئيسية)' : 'عملة أجنبية';
    }

    // شارة نوع العملة
    public function getTypeBadgeClassAttribute(): string
    {
        return $this->is_local
            ? 'bg-emerald-100 text-emerald-800 border-emerald-300'
            : 'bg-indigo-100 text-indigo-800 border-indigo-300';
    }

    // حالة العملة
    public function getStatusLabelAttribute(): string
    {
        if ($this->is_stopped) {
            return 'موقفة 🚫';
        }

        return $this->is_active ? 'نشطة ومستخدمة 🟢' : 'غير مفعلة ⚪';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->is_stopped) {
            return 'bg-rose-100 text-rose-800 border-rose-300';
        }

        return $this->is_active
            ? 'bg-emerald-100 text-emerald-800 border-emerald-300'
            : 'bg-slate-100 text-slate-700 border-slate-300';
    }
}
