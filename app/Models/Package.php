<?php

namespace App\Models;

use App\Helpers\ArabicCurrencyHelper;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'title',
        'description',
        'lessons_count',
        'price',
        'currency',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getCurrencyNameAttribute(): string
    {
        return ArabicCurrencyHelper::currencyName($this->currency);
    }

    public function getCurrencySymbolAttribute(): string
    {
        return ArabicCurrencyHelper::currencySymbol($this->currency);
    }

    public function getFormattedPriceAttribute(): string
    {
        return ArabicCurrencyHelper::format($this->price, $this->currency, 0);
    }
}
