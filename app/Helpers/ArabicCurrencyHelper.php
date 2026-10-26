<?php

namespace App\Helpers;

class ArabicCurrencyHelper
{
    /**
     * اسم العملة باللغة العربية
     */
    public static function currencyName(?string $currency): string
    {
        return match (strtoupper($currency ?? 'YER')) {
            'YER' => 'ريال يمني',
            'SAR' => 'ريال سعودي',
            'USD' => 'دولار أمريكي',
            default => $currency ?? 'ريال يمني',
        };
    }

    /**
     * رمز العملة المختصر
     */
    public static function currencySymbol(?string $currency): string
    {
        return match (strtoupper($currency ?? 'YER')) {
            'YER' => 'ر.ي',
            'SAR' => 'ر.س',
            'USD' => '$',
            default => $currency ?? 'ر.ي',
        };
    }

    /**
     * تنسيق المبلغ مع رمز العملة المناسب ديناميكياً ودون تكرار الدولار
     */
    public static function format(?float $amount, ?string $currency = 'YER', int $decimals = 2): string
    {
        $cur = strtoupper($currency ?? 'YER');
        $formattedNumber = number_format((float)($amount ?? 0), $decimals);

        if ($cur === 'USD') {
            return '$' . $formattedNumber;
        }

        $symbol = self::currencySymbol($cur);
        return $formattedNumber . ' ' . $symbol;
    }

    /**
     * تفقيط المبالغ المالية باللغة العربية المعتمدة
     */
    public static function tafqeet(float $number, string $currency = 'YER'): string
    {
        $currencyName = self::currencyName($currency);
        $fractionName = match (strtoupper($currency)) {
            'YER' => 'فلس',
            'SAR' => 'هللة',
            'USD' => 'سنت',
            default => 'جزء',
        };

        $integerPart = (int) floor(abs($number));
        $decimalPart = (int) round((abs($number) - $integerPart) * 100);

        $words = self::convertNumberToArabicWords($integerPart);

        $result = 'فقط ' . $words . ' ' . $currencyName;

        if ($decimalPart > 0) {
            $decimalWords = self::convertNumberToArabicWords($decimalPart);
            $result .= ' و' . $decimalWords . ' ' . $fractionName;
        }

        $result .= ' لا غير';

        return $result;
    }

    private static function convertNumberToArabicWords(int $number): string
    {
        if ($number === 0) {
            return 'صفر';
        }

        $ones = [
            1 => 'واحد', 2 => 'اثنان', 3 => 'ثلاثة', 4 => 'أربعة', 5 => 'خمسة',
            6 => 'ستة', 7 => 'سبعة', 8 => 'ثمانية', 9 => 'تسعة', 10 => 'عشرة',
            11 => 'أحد عشر', 12 => 'اثنا عشر', 13 => 'ثلاثة عشر', 14 => 'أربعة عشر',
            15 => 'خمسة عشر', 16 => 'ستة عشر', 17 => 'سبعة عشر', 18 => 'ثمانية عشر',
            19 => 'تسعة عشر',
        ];

        $tens = [
            2 => 'عشرون', 3 => 'ثلاثون', 4 => 'أربعون', 5 => 'خمسون',
            6 => 'ستون', 7 => 'سبعون', 8 => 'ثمانون', 9 => 'تسعون',
        ];

        $hundreds = [
            1 => 'مائة', 2 => 'مائتان', 3 => 'ثلاثمائة', 4 => 'أربعمائة', 5 => 'خمسمائة',
            6 => 'ستمائة', 7 => 'سبعمائة', 8 => 'ثمانمائة', 9 => 'تسعمائة',
        ];

        $words = [];

        // Billions
        if ($number >= 1000000000) {
            $billions = (int) floor($number / 1000000000);
            $words[] = self::convertNumberToArabicWords($billions) . ' مليار';
            $number %= 1000000000;
        }

        // Millions
        if ($number >= 1000000) {
            $millions = (int) floor($number / 1000000);
            if ($millions === 1) {
                $words[] = 'مليون';
            } elseif ($millions === 2) {
                $words[] = 'مليونان';
            } elseif ($millions >= 3 && $millions <= 10) {
                $words[] = self::convertNumberToArabicWords($millions) . ' ملايين';
            } else {
                $words[] = self::convertNumberToArabicWords($millions) . ' مليون';
            }
            $number %= 1000000;
        }

        // Thousands
        if ($number >= 1000) {
            $thousands = (int) floor($number / 1000);
            if ($thousands === 1) {
                $words[] = 'ألف';
            } elseif ($thousands === 2) {
                $words[] = 'ألفان';
            } elseif ($thousands >= 3 && $thousands <= 10) {
                $words[] = self::convertNumberToArabicWords($thousands) . ' آلاف';
            } else {
                $words[] = self::convertNumberToArabicWords($thousands) . ' ألف';
            }
            $number %= 1000;
        }

        // Hundreds
        if ($number >= 100) {
            $h = (int) floor($number / 100);
            $words[] = $hundreds[$h];
            $number %= 100;
        }

        // Tens & Ones
        if ($number >= 20) {
            $t = (int) floor($number / 10);
            $o = $number % 10;
            if ($o > 0) {
                $words[] = $ones[$o] . ' و' . $tens[$t];
            } else {
                $words[] = $tens[$t];
            }
        } elseif ($number > 0) {
            $words[] = $ones[$number];
        }

        return implode(' و', $words);
    }
}
