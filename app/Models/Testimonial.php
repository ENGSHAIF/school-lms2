<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'role',
        'user_type',
        'avatar',
        'title',
        'content',
        'score',
        'rating',
        'is_approved',
        'is_featured',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStarsStringAttribute(): string
    {
        return str_repeat('⭐', max(1, min(5, (int)$this->rating)));
    }

    public function getUserTypeLabelAttribute(): string
    {
        return match ($this->user_type) {
            'parent' => 'ولي أمر 👨‍👧‍👦',
            'student' => 'طالب في المنصة 🎓',
            'teacher' => 'معلم لغات 👨‍🏫',
            'supervisor' => 'مشرف أكاديمي 📋',
            default => 'عضو في المنصة 🌟',
        };
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->user_type) {
            'parent' => 'bg-amber-500/20 text-amber-300 border-amber-400/30',
            'student' => 'bg-indigo-500/20 text-indigo-300 border-indigo-400/30',
            'teacher' => 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30',
            default => 'bg-purple-500/20 text-purple-300 border-purple-400/30',
        };
    }
}
