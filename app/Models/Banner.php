<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'title_en',
        'subtitle',
        'content',
        'icon',
        'image_path',
        'link_url',
        'button_text',
        'placement',
        'category',
        'event_date',
        'event_location',
        'batch_name',
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
        'event_date' => 'date',
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

    // نطاق الإعلانات العادية والحملات الترويجية
    public function scopeAds($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'ad')
                ->orWhereNull('category');
        });
    }

    // نطاق معرض صور الفعاليات وحفلات التخرج
    public function scopeEvents($query)
    {
        return $query->where('category', 'event_gallery');
    }

    // التحقق من نوع المحتوى
    public function isEvent(): bool
    {
        return $this->category === 'event_gallery';
    }

    public function isAd(): bool
    {
        return $this->category !== 'event_gallery';
    }
}
