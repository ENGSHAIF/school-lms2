<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class GraduationEventsSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'حفل تخرج وتكريم طلاب الدفعة 38 - دبلوم اللغة الإنجليزية الشامل',
                'title_en' => 'Graduation Ceremony of Batch 38 - Comprehensive English Diploma',
                'category' => 'event_gallery',
                'batch_name' => 'الدفعة 38 - دبلوم الإنجليزية',
                'event_date' => '2026-10-15',
                'event_location' => 'صنعاء - القاعة الكبرى للمؤتمرات والاحتفالات',
                'subtitle' => 'احتفالية بهيجة بتخريج كوكبة من طلاب وطالبات دبلوم اللغة الإنجليزية بعد إتمام كافة المستويات واجتياز اختبارات الطلاقة والتحدث 1-on-1.',
                'content' => "أقام مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS) حفلاً تكريمياً مهيباً بمناسبة تخرج الدفعة 38 من برنامج دبلوم اللغة الإنجليزية الشامل.\n\nتخلل الحفل عروض لمشاريع الطلاب، كلمات ملهمة من إدارة المركز والمدربين، وتوزيع الشهادات المعتمدة والدروع التذكارية على الخريجين تقديراً لجهودهم وتميزهم.",
                'icon' => '🎓',
                'image_path' => 'uploads/banners/ad_1793101066_Gg5EKVTX.jpeg',
                'link_url' => '#gallery',
                'button_text' => 'مشاهدة ألبوم الحفل 📸',
                'placement' => 'hero_slider',
                'theme_style' => 'indigo',
                'device_target' => 'all',
                'starts_at' => now()->subDays(15),
                'ends_at' => now()->addDays(365),
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'الملتقى السنوي لتكريم فرسان التحدث بالإنجليزية وأوائل TOEFL',
                'title_en' => 'Annual Recognition Event for TOEFL High Achievers & Fluent Speakers',
                'category' => 'event_gallery',
                'batch_name' => 'دفعة فرسان التوفل والتميز',
                'event_date' => '2026-09-20',
                'event_location' => 'مقر مكتب النجم الآسيوي (ASOTS) - القاعة التدريبية',
                'subtitle' => 'تكريم الطلاب المتفوقين في جلسات المحادثة الحية والحاصلين على درجات قياسية في اختبارات TOEFL iBT المحاكية.',
                'content' => 'في إطار اهتمام مكتب النجم الآسيوي (ASOTS) برعاية المواهب وتشجيع الطلاقة اللغوية، نظمت إدارة التدريب فعالية تكريمية خاصة للطلاب المتميزين في برامج التوفل ونطق الذكاء الاصطناعي، وتسليمهم منحاً تدريبية وتسهيلات متقدمة.',
                'icon' => '🌟',
                'image_path' => 'uploads/banners/ad_1793102072_qBidjMEQ.jpg',
                'link_url' => '#gallery',
                'button_text' => 'استعراض صور التكريم 🏆',
                'placement' => 'hero_slider',
                'theme_style' => 'amber',
                'device_target' => 'all',
                'starts_at' => now()->subDays(30),
                'ends_at' => now()->addDays(365),
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'ورشة اليوم المفتوح وتخريج كوادر الترجمة الفورية والتحريرية المعتمدة',
                'title_en' => 'Graduation of Certified Translators & Interpreters Workshop',
                'category' => 'event_gallery',
                'batch_name' => 'دفعة المترجمين المعتمدين',
                'event_date' => '2026-08-30',
                'event_location' => 'مركز التدريب والاستشارات - ASOTS',
                'subtitle' => 'توثيق واختتام الورشة التدريبية المتقدمة لمهارات الترجمة الدبلوماسية والتجارية والفورية بحضور نخبة من الخبراء الأكاديميين.',
                'content' => 'اختتم مكتب النجم الآسيوي لخدمات الترجمة (ASOTS) البرنامج التدريبي التخصصي في مجال الترجمة بجميع أنواعها (التجارية، الطبية، القانونية، والأكاديمية) بمشاركة واسعة من المتدربين والباحثين، وتتويجهم بالشهادات الرسمية الصادرة عن المكتب.',
                'icon' => '📜',
                'image_path' => 'uploads/licenses/license_1793102716_K5Hcdtxc.jpg',
                'link_url' => '#gallery',
                'button_text' => 'ألبوم صور الفعالية 📸',
                'placement' => 'hero_slider',
                'theme_style' => 'emerald',
                'device_target' => 'all',
                'starts_at' => now()->subDays(60),
                'ends_at' => now()->addDays(365),
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($events as $ev) {
            Banner::updateOrCreate(
                ['title' => $ev['title']],
                $ev
            );
        }
    }
}
