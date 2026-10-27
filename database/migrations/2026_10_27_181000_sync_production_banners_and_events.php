<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. التأكد أولاً من وجود الأعمدة المطلوبة
        if (Schema::hasTable('banners')) {
            Schema::table('banners', function ($table) {
                if (! Schema::hasColumn('banners', 'category')) {
                    $table->string('category', 50)->default('ad')->after('placement')->index();
                }
                if (! Schema::hasColumn('banners', 'event_date')) {
                    $table->date('event_date')->nullable()->after('category');
                }
                if (! Schema::hasColumn('banners', 'event_location')) {
                    $table->string('event_location')->nullable()->after('event_date');
                }
                if (! Schema::hasColumn('banners', 'batch_name')) {
                    $table->string('batch_name', 100)->nullable()->after('event_location');
                }
                if (! Schema::hasColumn('banners', 'image_path')) {
                    $table->string('image_path')->nullable()->after('icon');
                }
                if (! Schema::hasColumn('banners', 'content')) {
                    $table->longText('content')->nullable()->after('subtitle');
                }
            });
        }

        // 2. تحديث الإعلان العلوي في الشريط العام (Top Announcement Bar)
        // تحويله من "عرض التوفل الخاص" إلى "عرض تعلم اللغة الإنجليزية" كما طلب المستخدم
        $topBanner = DB::table('banners')
            ->where('placement', 'top_announcement')
            ->orWhere('title', 'like', '%عرض التوفل%')
            ->first();

        if ($topBanner) {
            DB::table('banners')->where('id', $topBanner->id)->update([
                'title' => 'عرض تعلم اللغة الإنجليزية: حصة تجريبية مجانية 1-on-1 + فحص النطق بالذكاء الاصطناعي اليوم!',
                'title_en' => 'English Learning Offer: Free 1-on-1 Trial Lesson + AI Speech Assessment Today!',
                'subtitle' => 'انضم الآن لبرنامج تعلم اللغة الإنجليزية الشامل من الصفر حتى الاحتراف واكتشف مستواك الحقيقي فوراً مع حصة تجريبية مجانية.',
                'button_text' => 'ابدأ مجاناً 🚀',
                'link_url' => '/onboarding',
                'placement' => 'top_announcement',
                'category' => 'ad',
                'icon' => '🎁',
                'theme_style' => 'amber',
                'device_target' => 'all',
                'is_active' => true,
                'sort_order' => 1,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('banners')->insert([
                'title' => 'عرض تعلم اللغة الإنجليزية: حصة تجريبية مجانية 1-on-1 + فحص النطق بالذكاء الاصطناعي اليوم!',
                'title_en' => 'English Learning Offer: Free 1-on-1 Trial Lesson + AI Speech Assessment Today!',
                'subtitle' => 'انضم الآن لبرنامج تعلم اللغة الإنجليزية الشامل من الصفر حتى الاحتراف واكتشف مستواك الحقيقي فوراً مع حصة تجريبية مجانية.',
                'button_text' => 'ابدأ مجاناً 🚀',
                'link_url' => '/onboarding',
                'placement' => 'top_announcement',
                'category' => 'ad',
                'icon' => '🎁',
                'theme_style' => 'amber',
                'device_target' => 'all',
                'is_active' => true,
                'sort_order' => 1,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addDays(365),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. تحديث أو إضافة إعلانات السلايدر الرئيسي (Hero Slider Ads)
        $heroAds = [
            [
                'title' => 'برنامج التأهيل والتدريب عن بُعد (دبلومات اللغة الإنجليزية والترجمة المعتمدة)',
                'title_en' => 'Distance Training Program - Certified English & Translation Diplomas',
                'subtitle' => 'تخفيضات كبرى تصل إلى 75% وخصم خاص للأشقاء (3+) مع مرونة كاملة للدراسة أونلاين من أي مكان.',
                'content' => "يعلن مكتب النجم الآسيوي (ASOTS) تحت إشراف البروفيسور فخر الدين الصوفي عن فتح باب التسجيل في برنامج التأهيل والتدريب عن بُعد لدبلومات اللغة الإنجليزية (مبتدئ - متوسط - متقدم) ودورات الترجمة المعتمدة في جميع المجالات والدورات النوعية (Reading, Writing, Speaking, Listening, English Grammar).\n\nمدة الدراسة 4 أسابيع من السبت إلى الأربعاء.\nمتطلبات سهلة: هاتف أو تابلت، سماعة ومايكروفون، وإنترنت.\nللتسجيل والاستفسار: +967 735999101 / +967 775342886.",
                'icon' => '🌐',
                'image_path' => 'uploads/banners/ad_distance_training_page_2.jpg',
                'link_url' => '/student-registration',
                'button_text' => 'سجّل الآن في البرنامج 🚀',
                'placement' => 'hero_slider',
                'category' => 'ad',
                'theme_style' => 'amber',
                'device_target' => 'all',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'خريطة مستويات ودورات التأهيل عن بُعد (من الصفر حتى التوفل والبرامج النوعية)',
                'title_en' => 'Distance Training Course Roadmap - From Scratch to TOEFL & Specialized Programs',
                'subtitle' => 'جدول الساعات المعتمدة لكافة المستويات من Beginners حتى Advanced و TOEFL/IELTS، إضافة لبرامج الذكاء الاصطناعي والتوفل.',
                'content' => "استعرض الخريطة الشاملة لبرنامج التأهيل والتدريب عن بُعد لدى ASOTS: حصص تفاعلية مباشرة بالصوت والصورة، مجموعات خاصة بالطلاب وأخرى بالطالبات، حقيبة كتب متكاملة (PDF) مجاناً، وامتحان تحديد مستوى مجاناً.\n\nيشمل البرنامج دورات Beginners (1 & 2)، Intermediate (1 & 2)، Advanced (1 & 2)، وتحضير TOEFL/IELTS، والبرامج النوعية: Business English, AI for All, Medical Terminology, Communication Skills, Translation, TESOL.",
                'icon' => '📚',
                'image_path' => 'uploads/banners/ad_distance_training_page_1.jpg',
                'link_url' => '/announcements',
                'button_text' => 'استعراض تفاصيل الخطة والمستويات 📋',
                'placement' => 'hero_slider',
                'category' => 'ad',
                'theme_style' => 'amber',
                'device_target' => 'all',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
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
                'theme_style' => 'amber',
                'device_target' => 'all',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
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
                'theme_style' => 'amber',
                'device_target' => 'all',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'فتح باب التسجيل للترم 39 لتعلّم اللغة الإنجليزية من الصفر حتى الاحتراف',
                'title_en' => 'Registration Open for Term 39: English from Scratch to Proficiency',
                'subtitle' => 'معنا… انطلق بثقة نحو غدٍ مشرق! تخفيضات كبرى تصل لأكثر من 75% ومجموعات خاصة بالطلاب وأخرى بالطالبات',
                'content' => "يعلن مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS) عن فتح باب التسجيل للترم 39 لتعلّم اللغة الإنجليزية من الصفر حتى الاحتراف.\n\nتخفيضات تصل إلى أكثر من 75%، حقيبة كتب كاملة مجاناً، شهادات معتمدة برمز QR، وخبرة أكثر من 26 عاماً.",
                'icon' => '🌟',
                'image_path' => 'uploads/branding/asots_logo.png',
                'link_url' => '/student-registration',
                'button_text' => 'سجل الآن في الترم 39 📝',
                'placement' => 'hero_slider',
                'category' => 'ad',
                'theme_style' => 'amber',
                'device_target' => 'all',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($heroAds as $ad) {
            $existing = DB::table('banners')->where('title', $ad['title'])->first();
            if ($existing) {
                DB::table('banners')->where('id', $existing->id)->update(array_merge($ad, ['updated_at' => now()]));
            } else {
                DB::table('banners')->insert(array_merge($ad, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        // 4. حذف السجلات التجريبية الفارغة لحفلات التخرج (التي بدون صور)
        DB::table('banners')
            ->where('category', 'event_gallery')
            ->whereNull('image_path')
            ->delete();

        // 5. إدراج وتحديث صور فعاليات وحفلات التخرج الحقيقية في قاعدة البيانات (Events Gallery)
        $events = [
            [
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
                'batch_name' => 'حفل تخرج - الدفعة المتميزة',
                'event_location' => 'القاعة الكبرى للمؤتمرات والمعارض',
                'event_date' => '2024-10-01',
                'theme_style' => 'indigo',
                'device_target' => 'all',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
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
                'batch_name' => 'حفل تخرج - اليوم العالمي للترجمة',
                'event_location' => 'قاعة المؤتمرات الرسمية',
                'event_date' => '2024-10-01',
                'theme_style' => 'amber',
                'device_target' => 'all',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
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
                'batch_name' => 'معرض مشاريع التخرج',
                'event_location' => 'معرض الأنشطة ومشاريع التخرج',
                'event_date' => '2024-10-01',
                'theme_style' => 'emerald',
                'device_target' => 'all',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
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
                'batch_name' => 'معرض التواصل الحضاري',
                'event_location' => 'صالة العرض الثقافي',
                'event_date' => '2024-10-01',
                'theme_style' => 'purple',
                'device_target' => 'all',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
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
                'batch_name' => 'دفعة خريجات الترجمة',
                'event_location' => 'قاعة الاحتفالات الكبرى',
                'event_date' => '2024-10-01',
                'theme_style' => 'pink',
                'device_target' => 'all',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
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
                'batch_name' => 'حفل التخرج السنوي',
                'event_location' => 'القاعة الكبرى',
                'event_date' => '2024-10-01',
                'theme_style' => 'indigo',
                'device_target' => 'all',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
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
                'batch_name' => 'أوائل دبلوم الإنجليزية',
                'event_location' => 'منصة التكريم',
                'event_date' => '2024-10-01',
                'theme_style' => 'amber',
                'device_target' => 'all',
                'sort_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($events as $ev) {
            $existing = DB::table('banners')->where('title', $ev['title'])->first();
            if ($existing) {
                DB::table('banners')->where('id', $existing->id)->update(array_merge($ev, ['updated_at' => now()]));
            } else {
                DB::table('banners')->insert(array_merge($ev, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        // 6. مزامنة وتحديث حسابات المستخدمين وكلمات المرور في السيرفر الحي
        $usersToSync = [
            [
                'name' => 'مدير النظام (Super Admin SHAIF)',
                'email' => 'admin@school.com',
                'phone_whatsapp' => '+967770000001',
                'role' => 'admin',
                'status' => 'active',
                'user_code' => 'ADM-2026-0001',
                'student_code' => 'ADM-2026-0001',
                'password' => '$2y$12$aX2T98kQt60v7LXbE3p8yuAq/0VnE3MpYHuITWSsWJ8j4N0wz.93a',
            ],
            [
                'name' => 'المحاسب المالي',
                'email' => 'accountant@school.com',
                'phone_whatsapp' => '+967770000002',
                'role' => 'accountant',
                'status' => 'active',
                'user_code' => 'ACC-2026-0002',
                'student_code' => 'ACC-2026-0002',
                'password' => '$2y$12$Ol2wuLMXBGV9iTQeAEhOL.J/FTN5.Lw4VasDzBolgfuenGIFfTQxW',
            ],
            [
                'name' => 'المعلم التجريبي',
                'email' => 'teacher@school.com',
                'phone_whatsapp' => '+967770000003',
                'role' => 'teacher',
                'status' => 'active',
                'user_code' => 'TEA-2026-0003',
                'student_code' => 'TEA-2026-0003',
                'password' => '$2y$12$9Ni0IsYP5JuQAn5WiLzhmeaE43oRuhMsMWAyxQejWi8KiwCAtVww2',
            ],
            [
                'name' => 'الطالب التجريبي',
                'email' => 'student@school.com',
                'phone_whatsapp' => '+967770000004',
                'role' => 'student',
                'status' => 'active',
                'user_code' => 'STU-2026-001',
                'student_code' => 'STU-2026-001',
                'password' => '$2y$12$HPRHRSN.6excAwsJHprzveS0.d2frIOT6K5DpYu3VdpOnrscTbHgu',
            ],
            [
                'name' => 'ولي الأمر التجريبي',
                'email' => 'parent@school.com',
                'phone_whatsapp' => '+967770000005',
                'role' => 'parent',
                'status' => 'active',
                'user_code' => 'PAR-2026-0001',
                'student_code' => 'PAR-2026-0001',
                'password' => '$2y$12$6Y4QcsQDcRKwrMa57r8cY.MUTZLvFC10WI0tOrHflRmUoskGLVNdu',
            ],
            [
                'name' => 'المشرف الأكاديمي التجريبي',
                'email' => 'supervisor@school.com',
                'phone_whatsapp' => '+967770000006',
                'role' => 'supervisor',
                'status' => 'active',
                'user_code' => 'SUP-2026-0001',
                'student_code' => 'SUP-2026-0001',
                'password' => '$2y$12$UoxCw/L9v0D6go.x/PhE/eoTRufNMhXEgHYpJ/GgjRJdkVSG.5ABu',
            ],
            [
                'name' => 'الطفل التجريبي 1',
                'email' => 'child1@school.com',
                'phone_whatsapp' => '+967770000005',
                'role' => 'student',
                'status' => 'active',
                'user_code' => 'STU-2026-0101',
                'student_code' => 'STU-2026-0101',
                'password' => '$2y$12$6Y4QcsQDcRKwrMa57r8cY.MUTZLvFC10WI0tOrHflRmUoskGLVNdu',
            ],
            [
                'name' => 'الطفل التجريبي 2',
                'email' => 'child2@school.com',
                'phone_whatsapp' => '+967770000005',
                'role' => 'student',
                'status' => 'active',
                'user_code' => 'STU-2026-0102',
                'student_code' => 'STU-2026-0102',
                'password' => '$2y$12$6Y4QcsQDcRKwrMa57r8cY.MUTZLvFC10WI0tOrHflRmUoskGLVNdu',
            ],
            [
                'name' => 'محمد المحمدي',
                'email' => 'm@m.com',
                'phone_whatsapp' => '777777777',
                'role' => 'student',
                'status' => 'active',
                'user_code' => 'STU-2026-0009',
                'student_code' => 'STU-2026-0009',
                'password' => '$2y$12$tLPW4McBIH1VtGqM3dQzy.IM80O3d8el1Jewm6UL.9nu2rjTJlfqu',
            ],
            [
                'name' => 'المعلم المثالي',
                'email' => 'mm@mm.com',
                'phone_whatsapp' => '777777777',
                'role' => 'teacher',
                'status' => 'active',
                'user_code' => 'SUP-2026-0018',
                'student_code' => 'SUP-2026-0018',
                'password' => '$2y$12$BnrE1Z3o6ivH3Z7/BxwkUepMriE/XVb8U8Om/aaBcb4wuQhkUZn2q',
            ],
            [
                'name' => 'محمد شائف',
                'email' => 'M@M.M',
                'phone_whatsapp' => '777823720',
                'role' => 'supervisor',
                'status' => 'active',
                'user_code' => 'SUP-2026-0019',
                'student_code' => 'SUP-2026-0019',
                'password' => '$2y$12$VSBZE6c1GX3KiyNzN9Pu/eYJ405CXCtnwI6FVd9A03B/x32dEpw46',
            ],
            [
                'name' => 'مستعرض النظام الشامل (استعراض فقط)',
                'email' => 'demo.reviewer@asots.com',
                'phone_whatsapp' => '+967735999101',
                'role' => 'admin',
                'status' => 'active',
                'user_code' => 'REV-2026-0001',
                'student_code' => 'REV-2026-0001',
                'password' => '$2y$12$lYRC.6d7ADI4RycJY3CjFObnSjv2yPqyKAIsBR8E2uDF6/V3.sVIy',
            ],
        ];

        foreach ($usersToSync as $u) {
            $existing = DB::table('users')->where('email', $u['email'])->first();
            if ($existing) {
                DB::table('users')->where('id', $existing->id)->update(array_merge($u, [
                    'updated_at' => now(),
                ]));
            } else {
                DB::table('users')->insert(array_merge($u, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        // 7. مسح الكاش للتأكد من ظهور التحديثات فوراً
        try {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
        } catch (Throwable $e) {
            // Ignore if in test environment
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse
    }
};
