<?php

namespace App\Models;

use App\Helpers\ArabicCurrencyHelper;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = [
        'course_id',
        'name',
        'name_ar',
        'name_en',
        'description',
        'level_order',
        'fee_amount',
        'original_fee',
        'currency',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'original_fee' => 'decimal:2',
        'level_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->name_ar && $this->name_en) {
            return "{$this->name_ar} ({$this->name_en})";
        }

        return $this->name_ar ?: ($this->name ?: ($this->name_en ?: ''));
    }

    public function getTitleAttribute(): string
    {
        return $this->display_name ?: ($this->name ?: ($this->name_ar ?: ($this->name_en ?: '')));
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'level_id')->orderBy('course_order');
    }

    public function activeCourses()
    {
        return $this->courses()->where('is_active', true);
    }

    public function getTotalHoursAttribute(): int
    {
        return (int) $this->courses->sum('hours');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'level_id')->orderBy('order_index');
    }

    public function books()
    {
        return $this->hasMany(Book::class, 'level_id');
    }

    // بنك موارد ومناهج المستوى التعليمي المركزي
    public function resources()
    {
        return $this->hasMany(LevelResource::class, 'level_id')->orderBy('order_index')->latest();
    }

    public function bookResources()
    {
        return $this->resources()->where('type', 'book');
    }

    public function audioResources()
    {
        return $this->resources()->where('type', 'audio');
    }

    public function videoResources()
    {
        return $this->resources()->where('type', 'video');
    }

    public function worksheetResources()
    {
        return $this->resources()->where('type', 'worksheet');
    }

    public function quizResources()
    {
        return $this->resources()->where('type', 'quiz');
    }

    public function getCurrencyNameAttribute(): string
    {
        return ArabicCurrencyHelper::currencyName($this->currency);
    }

    public function getCurrencySymbolAttribute(): string
    {
        return ArabicCurrencyHelper::currencySymbol($this->currency);
    }

    public function getFormattedFeeAttribute(): string
    {
        return ArabicCurrencyHelper::format($this->fee_amount, $this->currency);
    }

    public function getFormattedOriginalFeeAttribute(): ?string
    {
        return $this->original_fee ? ArabicCurrencyHelper::format($this->original_fee, $this->currency) : null;
    }
}
