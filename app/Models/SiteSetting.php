<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    // جلب كل الإعدادات دفعة واحدة من الكاش فائق السرعة
    public static function allCached(): array
    {
        try {
            return Cache::remember('site_settings_all', 86400, function () {
                return static::pluck('value', 'key')->toArray();
            });
        } catch (\Exception $e) {
            return [];
        }
    }

    // جلب قيمة إعداد محدد مع قيمة افتراضية اختيارية
    public static function get(string $key, $default = null)
    {
        $all = static::allCached();
        return $all[$key] ?? $default;
    }

    // حفظ أو تحديث قيمة إعداد
    public static function set(string $key, $value, string $group = 'general'): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget('site_settings_all');

        return $setting;
    }

    // حذف إعداد محدد واستعادة الافتراضي
    public static function forget(string $key): bool
    {
        $deleted = static::where('key', $key)->delete();
        Cache::forget('site_settings_all');
        return $deleted > 0;
    }

    // مسح وتفريغ كل إعدادات مجموعة معينة
    public static function clearGroup(string $group): void
    {
        static::where('group', $group)->delete();
        Cache::forget('site_settings_all');
    }
}
