<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'title_en',
        'subtitle',
        'icon',
        'link_url',
        'button_text',
        'placement',
        'theme_style',
        'device_target',
        'starts_at',
        'ends_at',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // النطاق للبانرات النشطة زمنياً
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function scopePlacement($query, $placement)
    {
        return $query->where('placement', $placement);
    }
}
