<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. مزامنة كافة إعلانات وفعاليات المنصة الـ 21 (12 إعلان + 8 حفلات تخرج + 1 غير مفعل)
        $banners = [
            0 => [
                'id' => 1,
                'title' => 'عرض تعلم اللغة الإنجليزية: حصة تجريبية مجانية 1-on-1 + فحص النطق بالذكاء الاصطناعي اليوم!',
                'title_en' => 'English Learning Offer: Free 1-on-1 Trial Lesson + AI Speech Assessment Today!',
                'subtitle' => 'انضم الآن لبرنامج تعلم اللغة الإنجليزية الشامل من الصفر حتى الاحتراف واكتشف مستواك الحقيقي فوراً مع حصة تجريبية مجانية.',
                'content' => null,
                'icon' => '🎁',
                'image_path' => null,
                'link_url' => '/onboarding',
                'button_text' => 'ابدأ مجاناً 🚀',
                'placement' => 'top_announcement',
                'category' => 'ad',
                'event_date' => null,
                'event_location' => null,
                'batch_name' => null,
                'theme_style' => 'amber',
                'device_target' => 'all',
                'starts_at' => '2026-10-12 23:29:27',
                'ends_at' => '2026-11-12 23:29:27',
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => '2026-10-13 23:29:27',
                'updated_at' => '2026-10-27 14:51:48',
            ],
            1 => [
                'id' => 2,
                'title' => 'باقات الحصص الفردية وسندات القبض المعتمدة وفق المعايير المحاسبية المعتمدة',
                'title_en' => 'Credit Packages with Standard YemenSoft Financial Receipts',
                'subtitle' => 'سند قبض رسمي مرقم ومفقط باللغة العربية لكل عملية شحن مع كشف حساب تراكمي للطالب.',
                'content' => null,
                'icon' => '💳',
                'image_path' => null,
                'link_url' => '/packages',
                'button_text' => 'استعراض الباقات 💳',
                'placement' => 'hero_slider',
                'category' => 'ad',
                'event_date' => null,
                'event_location' => null,
                'batch_name' => null,
                'theme_style' => 'indigo',
                'device_target' => 'all',
                'starts_at' => '2026-10-12 23:29:27',
                'ends_at' => '2026-12-12 23:29:27',
                'sort_order' => 7,
                'is_active' => 1,
                'created_at' => '2026-10-13 23:29:27',
                'updated_at' => '2026-10-27 13:02:49',
            ],
            2 => [
                'id' => 3,
                'title' => 'برنامج تأسيس الأطفال (3 - 11 سنة): ألعاب تفاعلية ونظام مكافآت النجوم ⭐',
                'title_en' => 'Kids English Program: Interactive Games and Star Rewards',
                'subtitle' => 'جلسات 25 دقيقة فردية تناسب تركيز الطفل مع معلم صبور وتقارير تقييم فورية لولي الأمر عبر الواتساب.',
                'content' => null,
                'icon' => '🧸',
                'image_path' => null,
                'link_url' => '/onboarding',
                'button_text' => 'سجل في مسار الأطفال 🎈',
                'placement' => 'popup_modal',
                'category' => 'ad',
                'event_date' => null,
                'event_location' => null,
                'batch_name' => null,
                'theme_style' => 'emerald',
                'device_target' => 'all',
                'starts_at' => '2026-10-12 23:29:27',
                'ends_at' => '2026-11-27 23:29:27',
                'sort_order' => 3,
                'is_active' => 0,
                'created_at' => '2026-10-13 23:29:27',
                'updated_at' => '2026-10-14 00:31:38',
            ],
            3 => [
                'id' => 4,
                'title' => 'افتتاح قناة ASOTS الرسمية ودبلوم اللغة الإنجليزية الشامل من الصفر حتى الاحتراف',
                'title_en' => 'Official ASOTS Channel & Comprehensive English Diploma Launch',
                'subtitle' => 'فتح قناة تهتم بتعليم اللغات والتواصل والترجمة والاستشارات والبحوث ونشر الفرص الخاصة بالمنح والوظائف والتطوير الذاتي بقيادة د. فخر الدين الصوفي',
                'content' => '*يعلن مكتب النجم الآسيوي لخدمات الترجمة (ASOTS)*
فتح قناة تهتم بتعليم اللغات والتواصل والترجمة والاستشارات والبحوث ونشر الفرص الخاصة بالمنح والوظائف والتدريب والتطوير الذاتي، ويقدم المكتب الخدمات التالية:

✨ *الخدمات الأكاديمية والتدريبية:*
1. *برنامج دبلوم اللغة الإنجليزية الشامل:*
   - من الصفر حتى الاحتراف والطلاقة.
   - يعتمد على أحدث النظريات اللسانية والتربوية والتطبيقية، وتجارب تعليم اللغات عالمياً.
   - إعداد وتقديم وإشراف: نخبة من الأساتذة والخبراء المتميزين بقيادة وتصميم وإشراف الدكتور/ فخر الدين الصوفي (أستاذ اللسانيات التطبيقية الحديثة - خبير التواصل اللغوي والترجمة وتدريس الإنجليزية لأكثر من 26 عاماً).

2. *برامج التوفل (TOEFL) والآيلتس (IELTS):*
   - تدريب مكثف واحترافي لاجتياز الاختبارات الدولية بأعلى الدرجات وبأقصر وقت.

3. *دورات مهارية تخصصية (Specialized Courses):*
   - المحادثة المتقدمة والنطق السليم، الكتابة الأكاديمية والمهنية، اللغة الإنجليزية للأغراض الخاصة (الطبية، التجارية، الدبلوماسية، الأكاديمية، القانونية، الصحفية والإعلامية، المنظمات الدولية).

4. *برنامج تعليم اللغة العربية لغير الناطقين بها:*
   - منهج متميز ومبتكر يعتمد على مناهج ومداخل لسانية وتربوية حديثة وبإشراف مباشر من خبراء متخصصين.

✨ *الخدمات المهنية والاستشارية:*
1. *الترجمة التحريرية والفورية المعتمدة:*
   - ترجمة دقيقة واحترافية للوثائق الرسمية والأكاديمية، والتقارير الطبية، والمشاريع الهندسية، والعقود والاتفاقيات، والأوراق العلمية والبحوث.

2. *التدقيق اللغوي والتحرير المهني:*
   - تصحيح وإعادة صياغة الأبحاث والرسائل العلمية باللغتين العربية والإنجليزية، وتنسيق الأوراق للنشر في مجلات دولية مرموقة.

3. *الاستشارات الأكاديمية والبحثية:*
   - إرشاد الطلاب والباحثين في إعداد مشاريع التخرج، واختيار مواضيع الرسائل العلمية (ماجستير ودكتوراه)، وتصميم الأدوات البحثية.

4. *تأهيل المترجمين والمعلمين:*
   - ورش عمل وتدريب عملي لإعداد مترجمين فوريين ومحررين متمكنين، وتأهيل معلمي ومعلمات اللغة الإنجليزية وفق المعايير الدولية المعاصرة.

📞 *للتواصل والاستفسار والتسجيل:*
- واتساب / هاتف: +967 735 999 101
- واتساب إداري إضافي: +967 775 342 886
- البريد الإلكتروني: info@asots.edu.ye',
                'icon' => '📢',
                'image_path' => 'uploads/branding/asots_logo.png',
                'link_url' => 'https://wa.me/967735999101?text=%D9%85%D8%B1%D8%AD%D8%A8%D8%A7%D9%8B+%D9%85%D9%83%D8%AA%D8%A8+ASOTS%D8%8C+%D8%A3%D8%B1%D8%BA%D8%A8+%D9%81%D9%8A+%D8%A7%D9%84%D8%A7%D8%B3%D8%AA%D9%81%D8%B3%D8%A7%D8%B1+%D9%88%D8%A7%D9%84%D8%AA%D8%B3%D8%AC%D9%8A%D9%84+%D9%81%D9%8A+%D8%A8%D8%B1%D8%A7%D9%85%D8%AC+%D9%88%D8%AF%D8%A8%D9%84%D9%88%D9%85%D8%A7%D8%AA+%D8%A7%D9%84%D9%84%D8%BA%D8%A9+%D8%A7%D9%84%D8%A5%D9%86%D8%AC%D9%84%D9%8A%D8%B2%D9%8A%D8%A9+%D9%88%D8%AE%D8%AF%D9%85%D8%A7%D8%AA+%D8%A7%D9%84%D9%85%D9%83%D8%AA%D8%A8',
                'button_text' => 'التسجيل والاستفسار عبر واتساب 💬',
                'placement' => 'hero_slider',
                'category' => 'ad',
                'event_date' => null,
                'event_location' => null,
                'batch_name' => null,
                'theme_style' => 'amber',
                'device_target' => 'all',
                'starts_at' => null,
                'ends_at' => null,
                'sort_order' => 6,
                'is_active' => 1,
                'created_at' => '2026-10-26 23:13:05',
                'updated_at' => '2026-10-27 13:02:49',
            ],
            4 => [
                'id' => 5,
                'title' => 'فتح باب التسجيل للترم 39 لتعلّم اللغة الإنجليزية من الصفر حتى الاحتراف',
                'title_en' => 'Registration Open for Term 39: English from Scratch to Proficiency',
                'subtitle' => 'معنا… انطلق بثقة نحو غدٍ مشرق! تخفيضات كبرى تصل لأكثر من 75% ومجموعات خاصة بالطلاب وأخرى بالطالبات',
                'content' => 'يعلن مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS) عن فتح باب التسجيل للترم 39 لتعلّم اللغة الإنجليزية من الصفر حتى الاحتراف.

تخفيضات تصل إلى أكثر من 75%، حقيبة كتب كاملة مجاناً، شهادات معتمدة برمز QR، وخبرة أكثر من 26 عاماً.',
                'icon' => '🌟',
                'image_path' => 'uploads/branding/asots_logo.png',
                'link_url' => '/student-registration',
                'button_text' => 'سجل الآن في الترم 39 📝',
                'placement' => 'hero_slider',
                'category' => 'ad',
                'event_date' => null,
                'event_location' => null,
                'batch_name' => null,
                'theme_style' => 'amber',
                'device_target' => 'all',
                'starts_at' => null,
                'ends_at' => null,
                'sort_order' => 5,
                'is_active' => 1,
                'created_at' => '2026-10-26 23:22:26',
                'updated_at' => '2026-10-27 14:51:48',
            ],
            5 => [
                'id' => 6,
                'title' => 'طوّر لغتك… وابنِ مستقبلك مع ASOTS (البرنامج الدولي لتعليم الإنجليزية عن بُعد)',
                'title_en' => 'Develop Your Language & Build Your Future with ASOTS',
                'subtitle' => 'سارع بالتسجيل واحجز مقعدك الآن - بيئة تفاعلية داعمة ومجموعات للطلاب وأخرى للطالبات مع تحديد مستوى مجاني',
                'content' => '🌟 *طوّر لغتك… وابنِ مستقبلك مع ASOTS!* 🌟

📩 *سارع بالتسجيل واحجز مقعدك الآن.*

هل تبحث عن برنامج متكامل يساعدك على *تطوير الإنجليزية، وتعزيز الثقة، وتحسين قدرتك على التواصل في الدراسة والعمل والحياة اليومية؟*

يسرّ *مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS)* دعوتكم للانضمام إلى *برنامجنا الدولي لتعليم اللغة الإنجليزية عن بُعد*، من المستوى التمهيدي إلى المستويات المتقدمة.

🎯 *ابدأ من مستواك الحقيقي!*
يقدّم ASOTS *امتحان تحديد مستوى مجاني* لمساعدتك على الالتحاق بالمستوى المناسب لاحتياجاتك وقدراتك.

👥 *بيئة تعليمية متميزة:*
🔹 مجموعات تعليمية خاصة *بالطلاب* وأخرى *بالطالبات*.
🔹 فصول إلكترونية تفاعلية عبر الإنترنت.
🔹 معلمون *مؤهلون وذوو خبرة*.
🔹 متابعة وتقييم مستمر لمستوى التقدم.
🔹 بيئة تعليمية داعمة تشجع على المشاركة والتفاعل.

📚 *ماذا يقدم لك برنامج ASOTS؟*
🔹 تطوير متوازن لمهارات *الاستماع والتحدث والقراءة والكتابة*.
🔹 التركيز على *المحادثة والتواصل والاستخدام الحقيقي للغة*.
🔹 أنشطة وممارسات تعليمية تتناسب مع مستوى المتعلم واحتياجاته.
🔹 برامج متخصصة للتحضير لاختباري *TOEFL & IELTS*.
🔹 توجيه أكاديمي يساعدك على تطوير مهاراتك وتحقيق أهدافك.
🔹 مهارات لغوية عملية تدعمك في *الدراسة والعمل والتواصل والحياة اليومية*.

🌟 *في ASOTS، لا نعلّمك الإنجليزية فقط… بل نساعدك على استخدامها بثقة وفاعلية.*

✨ *حدّد مستواك مجانًا… اختر مجموعتك… وابدأ رحلتك مع ASOTS!*
━━━━━━━━━━━━━━
👨‍🏫 *الدكتور/ فخر الدين الصوفي، PhD*
دكتوراه في *اللسانيات التطبيقية الحديثة، والتواصل بين الثقافات والتداولية ودراسات الخطاب والترجمة*
من *جامعة محمد الخامس بالرباط – المملكة المغربية*.
*مؤسس ومدير – ASOTS*

🏆 *أكثر من 26 عامًا من الخبرة في التعليم والتدريب والتأهيل والترجمة.*

📞 *أرقام التواصل المباشر والتسجيل:*
+967 735 999 101
+967 775 342 886

📌 *قسم الجودة والمتابعة وشؤون الطلاب:*
+967 738 837 887

📢 *تابعوا قناة ASOTS لطلابنا على WhatsApp:*
https://whatsapp.com/channel/0029Vajnq1y7DAWxCf429S2T
━━━━━━━━━━━━━━
🌟 *ASOTS*
*We Don’t Just Teach English... We Build Futures.* 🚀',
                'icon' => '🚀',
                'image_path' => 'uploads/branding/asots_logo.png',
                'link_url' => 'https://wa.me/967735999101?text=%D9%85%D8%B1%D8%AD%D8%A8%D8%A7%D9%8B+%D9%85%D9%83%D8%AA%D8%A8+ASOTS%D8%8C+%D8%A3%D8%B1%D8%BA%D8%A8+%D9%81%D9%8A+%D8%AD%D8%AC%D8%B2+%D9%85%D9%82%D8%B9%D8%AF+%D9%88%D8%AA%D8%AD%D8%AF%D9%8A%D8%AF+%D9%85%D8%B3%D8%AA%D9%88%D8%A7%D9%8A+%D9%81%D9%8A+%D8%A7%D9%84%D8%A8%D8%B1%D9%86%D8%A7%D9%85%D8%AC+%D8%A7%D9%84%D8%AF%D9%88%D9%84%D9%8A+%D9%84%D8%AA%D8%B9%D9%84%D9%8A%D9%85+%D8%A7%D9%84%D9%84%D8%BA%D8%A9+%D8%A7%D9%84%D8%A5%D9%86%D8%AC%D9%84%D9%8A%D8%B2%D9%8A%D8%A9',
                'button_text' => 'احجز مقعدك وحدد مستواك مجاناً 🎯',
                'placement' => 'hero_slider',
                'category' => 'ad',
                'event_date' => null,
                'event_location' => null,
                'batch_name' => null,
                'theme_style' => 'indigo',
                'device_target' => 'all',
                'starts_at' => null,
                'ends_at' => null,
                'sort_order' => 4,
                'is_active' => 1,
                'created_at' => '2026-10-26 23:22:26',
                'updated_at' => '2026-10-27 13:02:49',
            ],
            6 => [
                'id' => 7,
                'title' => 'بدء التسجيل في الترم 38 لبرنامج اللغة الإنجليزية من المستوى التمهيدي حتى المتقدم',
                'title_en' => 'Registration Open for Term 38: English from Introductory to Advanced',
                'subtitle' => 'فرصتك لتبدأ وتتطور وتحقق أهدافك مع خصومات مميزة تصل لأكثر من 75% وبإشراف مباشر من الخبراء',
                'content' => '🌟 *معنا… انطلق بثقة نحو غدٍ مشرق* 🌟

يعلن *مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS)* عن بدء التسجيل في *الترم 38* لبرنامج اللغة الإنجليزية، من المستوى التمهيدي حتى المستويات المتقدمة.

🚀 *الآن فرصتك لتبدأ… وتتطور… وتحقق أهدافك!*

✨ *لماذا ASOTS؟*
🔹 تعليم تفاعلي مباشر عبر الإنترنت.
🔹 مناهج حديثة وأساليب تعليمية متطورة.
🔹 مجموعات تعليمية خاصة بالطلاب وأخرى بالطالبات.
🔹 أوقات دراسية مرنة.
🔹 اختبار تحديد مستوى مجانًا.
🔹 حقيبة تعليمية متكاملة بصيغة PDF.
🔹 متابعة وتقييم مستمر وتحفيز على ممارسة اللغة.
🔹 مدربون ذوو كفاءة وخبرة.
🔹 خصومات مميزة تصل إلى أكثر من 75%.

🏆 *26 عامًا من العطاء والخبرة في التعليم والتدريب.*

*تحت إدارة وإشراف:*
*Prof. Fakher Aldeen Alsofi, PhD*
*Founder & Director – ASOTS*

📲 *للتسجيل، يرجى إرسال استمارة التسجيل عبر الواتساب:*
📞 +967 735 999 101
📞 +967 738 837 887
📞 +967 775 342 886

📢 *انضم إلى قناة الطلاب لمتابعة كل جديد:*
https://whatsapp.com/channel/0029Vajnq1y7DAWxCf429S2T

💫 *We Don’t Just Teach English... We Build Futures.*',
                'icon' => '💎',
                'image_path' => 'uploads/branding/asots_logo.png',
                'link_url' => 'https://wa.me/967735999101?text=%D9%85%D8%B1%D8%AD%D8%A8%D8%A7%D9%8B+%D9%85%D9%83%D8%AA%D8%A8+ASOTS%D8%8C+%D8%A3%D8%B1%D8%BA%D8%A8+%D9%81%D9%8A+%D8%A5%D8%B1%D8%B3%D8%A7%D9%84+%D8%A7%D8%B3%D8%AA%D9%85%D8%A7%D8%B1%D8%A9+%D8%A7%D9%84%D8%AA%D8%B3%D8%AC%D9%8A%D9%84+%D9%81%D9%8A+%D8%A7%D9%84%D8%AA%D8%B1%D9%85+38+%D9%84%D8%A8%D8%B1%D9%86%D8%A7%D9%85%D8%AC+%D8%A7%D9%84%D9%84%D8%BA%D8%A9+%D8%A7%D9%84%D8%A5%D9%86%D8%AC%D9%84%D9%8A%D8%B2%D9%8A%D8%A9',
                'button_text' => 'إرسال استمارة الترم 38 عبر واتساب 💬',
                'placement' => 'hero_slider',
                'category' => 'ad',
                'event_date' => null,
                'event_location' => null,
                'batch_name' => null,
                'theme_style' => 'emerald',
                'device_target' => 'all',
                'starts_at' => null,
                'ends_at' => null,
                'sort_order' => 5,
                'is_active' => 1,
                'created_at' => '2026-10-26 23:22:26',
                'updated_at' => '2026-10-27 13:02:49',
            ],
            7 => [
                'id' => 11,
                'title' => 'استمرار التسجيل',
                'title_en' => null,
                'subtitle' => null,
                'content' => null,
                'icon' => '⭐',
                'image_path' => 'uploads/banners/ad_1793101066_Gg5EKVTX.jpeg',
                'link_url' => '/onboarding',
                'button_text' => 'احجز حصتك الآن 🚀',
                'placement' => 'hero_slider',
                'category' => 'ad',
                'event_date' => null,
                'event_location' => null,
                'batch_name' => null,
                'theme_style' => 'amber',
                'device_target' => 'all',
                'starts_at' => '2026-10-27 00:00:00',
                'ends_at' => '2030-12-26 00:00:00',
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => '2026-10-27 11:37:46',
                'updated_at' => '2026-10-27 13:02:49',
            ],
            8 => [
                'id' => 12,
                'title' => 'خدماتنا',
                'title_en' => null,
                'subtitle' => null,
                'content' => null,
                'icon' => '⭐',
                'image_path' => 'uploads/banners/ad_1793102072_qBidjMEQ.jpg',
                'link_url' => '/onboarding',
                'button_text' => 'احجز حصتك الآن 🚀',
                'placement' => 'hero_slider',
                'category' => 'ad',
                'event_date' => null,
                'event_location' => null,
                'batch_name' => null,
                'theme_style' => 'amber',
                'device_target' => 'all',
                'starts_at' => '2026-10-27 00:00:00',
                'ends_at' => '2030-12-26 00:00:00',
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => '2026-10-27 11:54:32',
                'updated_at' => '2026-10-27 13:02:49',
            ],
            9 => [
                'id' => 17,
                'title' => 'حفل تخرج - منصة القيادة والأكاديميين في فعاليات اليوم العالمي للترجمة',
                'title_en' => 'Graduation Event - Academic Leadership & International Translation Day',
                'subtitle' => 'منصة الحفل وكلمة الإدارة التوجيهية وتوزيع دروع التميز على المتفوقين.',
                'content' => 'كلمات ملهمة وتكريم للطلاب المتفوقين في مهارات المحادثة والترجمة الفورية وتوزيع الشهائد المعتمدة.',
                'icon' => '🌟',
                'image_path' => 'uploads/banners/event_graduation_1.jpg',
                'link_url' => '/announcements',
                'button_text' => 'مشاهدة الصور 🏆',
                'placement' => 'hero_slider',
                'category' => 'event_gallery',
                'event_date' => '2024-10-01',
                'event_location' => 'قاعة المؤتمرات الرسمية',
                'batch_name' => 'حفل تخرج - اليوم العالمي للترجمة',
                'theme_style' => 'amber',
                'device_target' => 'all',
                'starts_at' => '2026-09-27 12:41:37',
                'ends_at' => '2027-10-27 12:41:37',
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => '2026-10-27 12:41:37',
                'updated_at' => '2026-10-27 14:51:48',
            ],
            10 => [
                'id' => 18,
                'title' => 'حفل تخرج - تكريم طالبات وخريجات دبلوم الترجمة واللغات',
                'title_en' => 'Honoring Female Graduates in Translation & Language Diplomas',
                'subtitle' => 'تكريم خاص لخريجات برامج اللغات والترجمة وتسليمهن شهادات التخرج الرسمية.',
                'content' => 'لحظات بهيجة وفرحة التخرج والنجاح الأكاديمي وتتويج ثمرة الجهد والاجتهاد في كافة المستويات.',
                'icon' => '🎉',
                'image_path' => 'uploads/banners/event_graduation_2.jpg',
                'link_url' => '/announcements',
                'button_text' => 'صور التكريم 🎓',
                'placement' => 'hero_slider',
                'category' => 'event_gallery',
                'event_date' => '2024-10-01',
                'event_location' => 'قاعة الاحتفالات الكبرى',
                'batch_name' => 'دفعة خريجات الترجمة',
                'theme_style' => 'pink',
                'device_target' => 'all',
                'starts_at' => '2026-09-27 12:41:37',
                'ends_at' => '2027-10-27 12:41:37',
                'sort_order' => 5,
                'is_active' => 1,
                'created_at' => '2026-10-27 12:41:37',
                'updated_at' => '2026-10-27 14:51:48',
            ],
            11 => [
                'id' => 19,
                'title' => 'حفل تخرج - بدء حفلة التخرج والنجاح الأكاديمي',
                'title_en' => 'Commencement Ceremony & Academic Success Celebration',
                'subtitle' => 'افتتاح فقرات الحفل وسط حضور غفير من أولياء الأمور والطلاب والضيوف الكرام.',
                'content' => 'انطلاق فقرات الحفل التكريمي والاحتفاء بكوكبة الخريجين مع عروض مرئية لرحلة الطلاب التعليمية.',
                'icon' => '🎊',
                'image_path' => 'uploads/banners/event_graduation_3.jpg',
                'link_url' => '/announcements',
                'button_text' => 'ألبوم الحفل 📸',
                'placement' => 'hero_slider',
                'category' => 'event_gallery',
                'event_date' => '2024-10-01',
                'event_location' => 'القاعة الكبرى',
                'batch_name' => 'حفل التخرج السنوي',
                'theme_style' => 'indigo',
                'device_target' => 'all',
                'starts_at' => '2026-09-27 00:00:00',
                'ends_at' => '2027-10-27 00:00:00',
                'sort_order' => 6,
                'is_active' => 1,
                'created_at' => '2026-10-27 12:41:37',
                'updated_at' => '2026-10-27 14:51:48',
            ],
            12 => [
                'id' => 20,
                'title' => 'حفل تخرج - المعرض الثقافي والتراثي وورشة التواصل بين الثقافات',
                'title_en' => 'Graduation Event - Cultural Heritage & Intercultural Communication',
                'subtitle' => 'معرض ثقافي متميز يربط تعلم اللغات بفهم الثقافات والتواصل الحضاري الفعال.',
                'content' => 'فعالية متميزة ضمن برنامج الحفل تبرز أهمية التواصل الثقافي كركيزة أساسية من ركائز مكتب ASOTS.',
                'icon' => '🌍',
                'image_path' => 'uploads/banners/event_graduation_4.jpg',
                'link_url' => '/announcements',
                'button_text' => 'معرض الثقافات 🎨',
                'placement' => 'hero_slider',
                'category' => 'event_gallery',
                'event_date' => '2024-10-01',
                'event_location' => 'صالة العرض الثقافي',
                'batch_name' => 'معرض التواصل الحضاري',
                'theme_style' => 'purple',
                'device_target' => 'all',
                'starts_at' => '2026-09-27 12:41:37',
                'ends_at' => '2027-10-27 12:41:37',
                'sort_order' => 4,
                'is_active' => 1,
                'created_at' => '2026-10-27 12:41:37',
                'updated_at' => '2026-10-27 14:51:48',
            ],
            13 => [
                'id' => 21,
                'title' => 'حفل تخرج - لقطة تذكارية مع كوكبة من أوائل الخريجين والمتميزين',
                'title_en' => 'Commemorative Photo with Top Achievers & Graduates',
                'subtitle' => 'صورة تذكارية تجمع الدكتور فخر الدين الصوفي مع أوائل دفعات دبلوم اللغة الإنجليزية.',
                'content' => 'لقطة تذكارية لتوثيق النجاح والتفوق ومسيرة التعلم والطلاقة اللغوية المستمرة.',
                'icon' => '⭐',
                'image_path' => 'uploads/banners/event_graduation_5.jpg',
                'link_url' => '/announcements',
                'button_text' => 'الصورة التذكارية 📸',
                'placement' => 'hero_slider',
                'category' => 'event_gallery',
                'event_date' => '2024-10-01',
                'event_location' => 'منصة التكريم',
                'batch_name' => 'أوائل دبلوم الإنجليزية',
                'theme_style' => 'amber',
                'device_target' => 'all',
                'starts_at' => '2026-09-27 12:41:37',
                'ends_at' => '2027-10-27 12:41:37',
                'sort_order' => 7,
                'is_active' => 1,
                'created_at' => '2026-10-27 12:41:37',
                'updated_at' => '2026-10-27 14:51:48',
            ],
            14 => [
                'id' => 23,
                'title' => 'حفل تخرج - مراسم قص شريط الافتتاح وتدشين فعاليات المعرض',
                'title_en' => 'Graduation Ceremony - Ribbon Cutting & Exhibition Launch',
                'subtitle' => 'مراسم افتتاح معرض الفعاليات وتكريم خريجي وخريجات دبلوم اللغة الإنجليزية والترجمة.',
                'content' => 'تدشين فعاليات المعرض بحضور الدكتور فخر الدين الصوفي ونخبة من الأكاديميين والمدربين، وتكريم المشاريع الطلابية.',
                'icon' => '🎓',
                'image_path' => 'uploads/banners/event_graduation_6.jpg',
                'link_url' => '/announcements',
                'button_text' => 'استعراض ألبوم الحفل 📸',
                'placement' => 'hero_slider',
                'category' => 'event_gallery',
                'event_date' => '2024-10-01',
                'event_location' => 'القاعة الكبرى للمؤتمرات والمعارض',
                'batch_name' => 'حفل تخرج - الدفعة المتميزة',
                'theme_style' => 'indigo',
                'device_target' => 'all',
                'starts_at' => '2026-09-27 12:45:10',
                'ends_at' => '2027-10-27 12:45:10',
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => '2026-10-27 12:45:10',
                'updated_at' => '2026-10-27 14:51:48',
            ],
            15 => [
                'id' => 24,
                'title' => 'حفل تخرج - الجولة الاستطلاعية ومناقشة مشاريع الخريجات بالمعرض',
                'title_en' => 'Graduation Tour - Reviewing Student Graduation Projects',
                'subtitle' => 'مناقشة المشاريع التطبيقية واستعراض أعمال الخريجين في الترجمة والتواصل الدولي.',
                'content' => 'جولة استطلاعية للدكتور فخر الدين الصوفي بين أجنحة المعرض للاطلاع على إبداعات الخريجات ومشاريعهن الأكاديمية.',
                'icon' => '📜',
                'image_path' => 'uploads/banners/event_graduation_7.jpg',
                'link_url' => '/announcements',
                'button_text' => 'صور المشاريع 📸',
                'placement' => 'hero_slider',
                'category' => 'event_gallery',
                'event_date' => '2024-10-01',
                'event_location' => 'معرض الأنشطة ومشاريع التخرج',
                'batch_name' => 'معرض مشاريع التخرج',
                'theme_style' => 'emerald',
                'device_target' => 'all',
                'starts_at' => '2026-09-27 12:45:10',
                'ends_at' => '2027-10-27 12:45:10',
                'sort_order' => 3,
                'is_active' => 1,
                'created_at' => '2026-10-27 12:45:10',
                'updated_at' => '2026-10-27 14:51:48',
            ],
            16 => [
                'id' => 26,
                'title' => 'برنامج التأهيل والتدريب عن بُعد (دبلومات اللغة الإنجليزية والترجمة المعتمدة)',
                'title_en' => 'Distance Training Program - Certified English & Translation Diplomas',
                'subtitle' => 'تخفيضات كبرى تصل إلى 75% وخصم خاص للأشقاء (3+) مع مرونة كاملة للدراسة أونلاين من أي مكان.',
                'content' => 'يعلن مكتب النجم الآسيوي (ASOTS) تحت إشراف البروفيسور فخر الدين الصوفي عن فتح باب التسجيل في برنامج التأهيل والتدريب عن بُعد لدبلومات اللغة الإنجليزية (مبتدئ - متوسط - متقدم) ودورات الترجمة المعتمدة في جميع المجالات والدورات النوعية (Reading, Writing, Speaking, Listening, English Grammar).

مدة الدراسة 4 أسابيع من السبت إلى الأربعاء.
متطلبات سهلة: هاتف أو تابلت، سماعة ومايكروفون، وإنترنت.
للتسجيل والاستفسار: +967 735999101 / +967 775342886.',
                'icon' => '🌐',
                'image_path' => 'uploads/banners/ad_distance_training_page_2.jpg',
                'link_url' => '/student-registration',
                'button_text' => 'سجّل الآن في البرنامج 🚀',
                'placement' => 'hero_slider',
                'category' => 'ad',
                'event_date' => null,
                'event_location' => null,
                'batch_name' => null,
                'theme_style' => 'amber',
                'device_target' => 'all',
                'starts_at' => null,
                'ends_at' => null,
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => '2026-10-27 13:02:49',
                'updated_at' => '2026-10-27 14:51:48',
            ],
            17 => [
                'id' => 27,
                'title' => 'خريطة مستويات ودورات التأهيل عن بُعد (من الصفر حتى التوفل والبرامج النوعية)',
                'title_en' => 'Distance Training Course Roadmap - From Scratch to TOEFL & Specialized Programs',
                'subtitle' => 'جدول الساعات المعتمدة لكافة المستويات من Beginners حتى Advanced و TOEFL/IELTS، إضافة لبرامج الذكاء الاصطناعي والتوفل.',
                'content' => 'استعرض الخريطة الشاملة لبرنامج التأهيل والتدريب عن بُعد لدى ASOTS: حصص تفاعلية مباشرة بالصوت والصورة، مجموعات خاصة بالطلاب وأخرى بالطالبات، حقيبة كتب متكاملة (PDF) مجاناً، وامتحان تحديد مستوى مجاناً.

يشمل البرنامج دورات Beginners (1 & 2)، Intermediate (1 & 2)، Advanced (1 & 2)، وتحضير TOEFL/IELTS، والبرامج النوعية: Business English, AI for All, Medical Terminology, Communication Skills, Translation, TESOL.',
                'icon' => '📚',
                'image_path' => 'uploads/banners/ad_distance_training_page_1.jpg',
                'link_url' => '/announcements',
                'button_text' => 'استعراض تفاصيل الخطة والمستويات 📋',
                'placement' => 'hero_slider',
                'category' => 'ad',
                'event_date' => null,
                'event_location' => null,
                'batch_name' => null,
                'theme_style' => 'amber',
                'device_target' => 'all',
                'starts_at' => null,
                'ends_at' => null,
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => '2026-10-27 13:02:49',
                'updated_at' => '2026-10-27 14:51:48',
            ],
            18 => [
                'id' => 31,
                'title' => 'استمرار التسجيل في دبلومات وبرامج اللغة الإنجليزية الشاملة',
                'title_en' => 'Registration Ongoing for English Diploma Programs',
                'subtitle' => 'مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات - مقاعد محدودة وتخفيضات خاصة',
                'content' => 'استمرار التسجيل في كافة مستويات دبلوم اللغة الإنجليزية من الصفر وحتى الاحتراف مع اختبار تحديد مستوى مجاني.',
                'icon' => '⭐',
                'image_path' => 'uploads/banners/ad_1793101066_Gg5EKVTX.jpeg',
                'link_url' => '/student-registration',
                'button_text' => 'احجز مقعدك الآن 🚀',
                'placement' => 'hero_slider',
                'category' => 'ad',
                'event_date' => null,
                'event_location' => null,
                'batch_name' => null,
                'theme_style' => 'amber',
                'device_target' => 'all',
                'starts_at' => null,
                'ends_at' => null,
                'sort_order' => 3,
                'is_active' => 1,
                'created_at' => '2026-10-27 14:51:48',
                'updated_at' => '2026-10-27 14:51:48',
            ],
            19 => [
                'id' => 32,
                'title' => 'خدماتنا الأكاديمية والتدريبية والاستشارية المتكاملة',
                'title_en' => 'Comprehensive Academic, Training & Consultancy Services',
                'subtitle' => 'تعليم لغات، ترجمة معتمدة، استشارات أكاديمية، وتأهيل لاختبارات التوفل الدولية',
                'content' => 'يقدم مكتب النجم الآسيوي باقة متكاملة من الخدمات التعليمية والاستشارية والترجمة المعتمدة لجميع الهيئات والجامعات.',
                'icon' => '🌟',
                'image_path' => 'uploads/banners/ad_1793102072_qBidjMEQ.jpg',
                'link_url' => '/services',
                'button_text' => 'تعرّف على كافة الخدمات 🔍',
                'placement' => 'hero_slider',
                'category' => 'ad',
                'event_date' => null,
                'event_location' => null,
                'batch_name' => null,
                'theme_style' => 'amber',
                'device_target' => 'all',
                'starts_at' => null,
                'ends_at' => null,
                'sort_order' => 4,
                'is_active' => 1,
                'created_at' => '2026-10-27 14:51:48',
                'updated_at' => '2026-10-27 14:51:48',
            ],
            20 => [
                'id' => 33,
                'title' => 'حفل تخرج الدفعة 40 تجريبي',
                'title_en' => 'Graduation Batch 40 Test',
                'subtitle' => 'حفل تخرج وتكريم الأوائل',
                'content' => 'تفاصيل الحفل والتكريم وتوزيع الشهادات الرسمية.',
                'icon' => '🎓',
                'image_path' => null,
                'link_url' => '#gallery',
                'button_text' => 'مشاهدة الألبوم 📸',
                'placement' => 'hero_slider',
                'category' => 'event_gallery',
                'event_date' => '2026-11-01',
                'event_location' => 'القاعة الملكية للاحتفالات',
                'batch_name' => 'الدفعة 40 - دبلوم الإنجليزية',
                'theme_style' => 'indigo',
                'device_target' => 'all',
                'starts_at' => '2026-10-27 14:54:09',
                'ends_at' => '2027-10-27 14:54:09',
                'sort_order' => 0,
                'is_active' => 1,
                'created_at' => '2026-10-27 14:54:09',
                'updated_at' => '2026-10-27 14:54:09',
            ],
        ];

        foreach ($banners as $b) {
            DB::table('banners')->updateOrInsert(
                ['id' => $b['id']],
                $b
            );
        }

        // 2. مزامنة الفترات المحاسبية ومجموعات الحسابات الخاصة بيمن سوفت
        $arabicMonths = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ];
        for ($m = 1; $m <= 12; $m++) {
            $startDate = Carbon::create(2026, $m, 1)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::create(2026, $m, 1)->endOfMonth()->format('Y-m-d');
            $monthName = $arabicMonths[$m].' 2026';
            DB::table('accounting_periods')->updateOrInsert(
                ['fiscal_year' => 2026, 'period_number' => $m],
                [
                    'name' => $monthName,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => 'open',
                    'notes' => "فترة شهر {$arabicMonths[$m]} المالية للعام 2026",
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // 3. مجموعات الحسابات ومراكز التكلفة
        $accountGroups = [
            ['code' => 'CASH_BANKS', 'name' => 'مجموعة الصناديق النقدية والحسابات البنكية', 'description' => 'حسابات الصناديق بالعملات المختلفة والبنوك التجارية'],
            ['code' => 'STUDENTS', 'name' => 'مجموعة ذمم الطلاب والعملاء المدينين', 'description' => 'المدينون من رسوم الدورات والتأهيل'],
            ['code' => 'TEACHERS', 'name' => 'مجموعة مستحقات المدربين والموردين الدائنين', 'description' => 'المستحقات الشهرية وعقود التدريس'],
            ['code' => 'REVENUES', 'name' => 'مجموعة إيرادات الدورات والخدمات التعليمية', 'description' => 'إيرادات دبلومات اللغات والشهادات المعتمدة'],
            ['code' => 'EXPENSES', 'name' => 'مجموعة المصروفات الإدارية والتشغيلية', 'description' => 'الإيجارات والرواتب والاتصالات والإنترنت'],
        ];
        foreach ($accountGroups as $grp) {
            DB::table('account_groups')->updateOrInsert(
                ['code' => $grp['code']],
                ['name' => $grp['name'], 'description' => $grp['description'], 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        $costCenterGroups = [
            ['code' => 'CC_COURSES', 'name' => 'مجموعة دورات اللغات والتأهيل الأكاديمي', 'description' => 'مراكز تكلفة الدورات التدريبية المباشرة والأونلاين'],
            ['code' => 'CC_ADMIN', 'name' => 'مجموعة الإدارة العامة والخدمات المشتركة', 'description' => 'تكاليف المقر الرئيسي والإدارة والتسويق'],
        ];
        foreach ($costCenterGroups as $ccg) {
            DB::table('cost_center_groups')->updateOrInsert(
                ['code' => $ccg['code']],
                ['name' => $ccg['name'], 'description' => $ccg['description'], 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        // 4. خيارات نظام المتكامل بلاس في الإعدادات
        $settings = [
            'accounting.prevent_overdraft' => '1',
            'accounting.allow_suspended_entries' => '1',
            'accounting.strict_period_lock' => '1',
            'accounting.allow_daily_portfolios' => '1',
            'accounting.f9_statement_history' => '1',
        ];
        foreach ($settings as $key => $val) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $val, 'group' => 'accounting', 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void {}
};
