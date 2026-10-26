<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('banners')->insert([
            [
                'title' => 'عرض التوفل الخاص: حصة تجريبية مجانية 1-on-1 + فحص النطق بالذكاء الاصطناعي اليوم!',
                'title_en' => 'Special TOEFL Offer: Free 1-on-1 Trial Lesson + AI Speech Assessment Today!',
                'subtitle' => 'انضم الآن لمسار التدريب الفردي المكثف لاجتياز اختبار TOEFL iBT واكتشف مستواك الحقيقي فوراً.',
                'icon' => '🎁',
                'link_url' => '/onboarding',
                'button_text' => 'ابدأ مجاناً 🚀',
                'placement' => 'top_announcement',
                'theme_style' => 'amber',
                'device_target' => 'all',
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addDays(30),
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'باقات الحصص الفردية وسندات القبض المعتمدة وفق المعايير المحاسبية المعتمدة',
                'title_en' => 'Credit Packages with Standard YemenSoft Financial Receipts',
                'subtitle' => 'سند قبض رسمي مرقم ومفقط باللغة العربية لكل عملية شحن مع كشف حساب تراكمي للطالب.',
                'icon' => '💳',
                'link_url' => '/packages',
                'button_text' => 'استعراض الباقات 💳',
                'placement' => 'hero_slider',
                'theme_style' => 'indigo',
                'device_target' => 'all',
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addDays(60),
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'برنامج تأسيس الأطفال (3 - 11 سنة): ألعاب تفاعلية ونظام مكافآت النجوم ⭐',
                'title_en' => 'Kids English Program: Interactive Games and Star Rewards',
                'subtitle' => 'جلسات 25 دقيقة فردية تناسب تركيز الطفل مع معلم صبور وتقارير تقييم فورية لولي الأمر عبر الواتساب.',
                'icon' => '🧸',
                'link_url' => '/onboarding',
                'button_text' => 'سجل في مسار الأطفال 🎈',
                'placement' => 'popup_modal',
                'theme_style' => 'emerald',
                'device_target' => 'all',
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addDays(45),
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('banners')->truncate();
    }
};
