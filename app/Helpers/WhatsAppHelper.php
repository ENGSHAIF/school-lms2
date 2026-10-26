<?php

namespace App\Helpers;

class WhatsAppHelper
{
    /**
     * تنظيف وتهيئة رقم الهاتف ليكون رقماً دولياً صالحاً للواتساب
     */
    public static function formatNumber(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // إزالة أي مسافات أو رموز غير رقمية
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // إزالة الأصفار الدولية المزدوجة 00
        if (str_starts_with($clean, '00')) {
            $clean = substr($clean, 2);
        }

        // إذا كان الرقم 10 أرقام ويبدأ بـ 07 (مثال: 0777823720) نحذف الصفر ونضيف مفتاح اليمن 967
        if (strlen($clean) === 10 && str_starts_with($clean, '07')) {
            $clean = '967' . substr($clean, 1);
        }

        // إذا كان الرقم 9 أرقام ويبدأ بـ 7 (مثال يمني محلي: 777823720) نضيف المفتاح الدولي 967
        if (strlen($clean) === 9 && str_starts_with($clean, '7')) {
            $clean = '967' . $clean;
        }

        return $clean;
    }

    /**
     * بناء رابط إرسال الواتساب المباشر بنص ورابط معتمد
     */
    public static function buildUrl(?string $phone, string $message): string
    {
        $formatted = self::formatNumber($phone);

        if (!empty($formatted)) {
            return 'https://wa.me/' . $formatted . '?text=' . urlencode($message);
        }

        return 'https://wa.me/?text=' . urlencode($message);
    }
}
