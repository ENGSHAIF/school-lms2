<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Level;
use App\Models\Package;
use App\Models\User;
use App\Models\SiteSetting;

class SmartBotService
{
    /**
     * معالجة استفسار المستخدم والرد التلقائي الذكي من قاعدة البيانات
     *
     * @param string $message
     * @param User|null $user
     * @return array
     */
    public static function handleMessage(string $message, ?User $user = null): array
    {
        $raw = trim($message);
        if ($raw === '') {
            return self::defaultWelcomeResponse($user);
        }

        $normalized = self::normalizeText($raw);

        // 1. الاستعلام عن كود طالب أو رصيد مالي (إذا أدخل الطالب كوده STU-XXXX)
        if (preg_match('/(STU-[\w\-]+|PAR-[\w\-]+)/i', $raw, $matches)) {
            return self::handleUserCodeLookup($matches[1], $user);
        }

        // 2. التحقق من نية الاستعلام عن رصيد حساب الطالب الحالي
        if (self::matchesAny($normalized, ['رصيدي', 'رصيد', 'كم باقي', 'كم متبقي', 'سنداتي', 'سنداتي المالية', 'كشف حساب', 'كم علي فلوس', 'حسابي المالي'])) {
            return self::handleStudentFinancialInquiry($user);
        }

        // 3. الاستفسار العام عن أسعار الدورات أو قائمة الكورسات
        if (self::matchesAny($normalized, ['اسعار الدورات', 'كم اسعار', 'قائمة الاسعار', 'اسعار الكورسات', 'تكلفة الدورات', 'اسعاركم', 'بكم الكورسات', 'قائمة الدورات', 'الدورات المتاحة', 'ايش الدورات', 'ما هي الدورات', 'جميع الدورات', 'كل الدورات'])) {
            return self::handleAllCoursesPricing();
        }

        // 4. دليل تشغيل النظام والمساعدة التفاعلية بحسب الصلاحيات (System Handbook & How-To Guides)
        $helpGuide = SystemHandbookService::searchHelp($raw, $user);
        if ($helpGuide) {
            return $helpGuide;
        }

        // 4.1 البحث عن دورة محددة وسعرها في قاعدة البيانات
        $courseMatch = self::searchSpecificCourse($raw, $normalized);
        if ($courseMatch) {
            return $courseMatch;
        }

        // 5. الاستفسار عن المستويات الأكاديمية
        if (self::matchesAny($normalized, ['المستويات', 'مستويات', 'المستوى التمهيدي', 'المستوى الاول', 'المستوى الثاني', 'المستوى الثالث', 'نظام المستويات', 'الدبلوم'])) {
            return self::handleLevelsInquiry($normalized);
        }

        // 6. الاستفسار عن باقات الحصص والتدريب
        if (self::matchesAny($normalized, ['باقة', 'باقات', 'باقات الحصص', 'حزم الحصص', 'باقه', 'اسعار الباقات'])) {
            return self::handlePackagesInquiry();
        }

        // 7. الاستفسار عن اختبار تحديد المستوى والقبول
        if (self::matchesAny($normalized, ['تحديد مستوى', 'اختبار القبول', 'تسجيل جديد', 'كيف اسجل', 'طريقة التسجيل', 'فحص النطق', 'حصة تجريبية', 'تجريبي'])) {
            return self::handlePlacementTestInquiry();
        }

        // 8. الاستفسار عن الشهائد المعتمدة وكود التحقق
        if (self::matchesAny($normalized, ['شهادة', 'شهائد', 'الشهادات', 'معتمدة', 'كود التحقق', 'باركود الشهادة', 'توثيق الشهادة'])) {
            return self::handleCertificatesInquiry();
        }

        // 9. الاستفسار عن بيانات التواصل، رقم الهاتف، والواتساب
        if (self::matchesAny($normalized, ['تواصل', 'رقم', 'واتس', 'واتساب', 'موقعكم', 'عنوانكم', 'اتصال', 'ادارة', 'خدمة العملاء', 'اكلم موظف', 'رقم الهاتف'])) {
            return self::handleContactInquiry();
        }

        // 10. التحية والترحيب
        if (self::matchesAny($normalized, ['مرحبا', 'السلام عليكم', 'سلام', 'صباح الخير', 'مساء الخير', 'هلا', 'يا هلا', 'اهلين', 'أهلا', 'الو', 'هاي', 'hello', 'hi'])) {
            return self::defaultWelcomeResponse($user);
        }

        // 11. رد ذكي افتراضي عند عدم التعرف على النية، مع اقتراح الخيارات المتاحة
        return self::handleFallbackResponse($raw);
    }

    /**
     * البحث الذكي عن دورة محددة وسعرها
     */
    protected static function searchSpecificCourse(string $raw, string $normalized): ?array
    {
        // إذا كان السؤال استفساراً إجرائياً أو تعليمياً (كيف، طريقة، خطوات)، نتركه لمركز المساعدة وليس أسعار الدورات
        $isHowTo = self::matchesAny($normalized, ['كيف', 'طريقة', 'طريقه', 'خطوات', 'شرح', 'تعليمات', 'ازاي', 'شلون']);
        if ($isHowTo) {
            return null;
        }

        // الكلمات الدالة على الاستفسار الصريح عن السعر والرسوم
        $isAskingPrice = self::matchesAny($normalized, ['سعر', 'اسعار', 'بكم', 'تكلفة', 'رسوم', 'كم سعر', 'كم تكلفة', 'سعرها', 'بكام']);

        $courses = Course::with('level')->where('is_active', true)->get();
        if ($courses->isEmpty()) {
            $courses = Course::with('level')->get();
        }

        $matchedCourse = null;
        $highestScore = 0;

        foreach ($courses as $c) {
            $score = 0;
            $cName = self::normalizeText($c->name);
            $cNameAr = self::normalizeText($c->name_ar ?? '');
            $cNameEn = strtolower($c->name_en ?? '');
            $rawLower = strtolower($raw);

            // فحص دقيق للكلمات المفتاحية الإنجليزية (مثل Access A, Intro B, TESOL, AI)
            if (!empty($c->name_en) && preg_match('/\b' . preg_quote(strtolower($c->name_en), '/') . '\b/i', $rawLower)) {
                $score += 50;
            }

            // استبعاد الكلمات العامة الشائعة من المطابقة العشوائية (بصيغتي التاء المربوطة والهاء)
            $stopWords = [
                'دورة', 'دوره', 'دورات', 'كورس', 'كورسات', 'مستوى', 'مستويات',
                'برنامج', 'برامج', 'دبلوم', 'المرحله', 'مرحله', 'المرحلة', 'مرحلة',
                'course', 'courses', 'level', 'levels', 'program', 'diploma'
            ];

            // فحص أجزاء اسم الدورة (مثل "Access A" أو "Intro A" أو "الذكاء الاصطناعي")
            $tokens = preg_split('/[\s\(\)\-\_\/]+/', $c->name);
            foreach ($tokens as $token) {
                $tokenClean = trim(self::normalizeText($token));
                if (in_array($tokenClean, $stopWords, true) || in_array($token, $stopWords, true)) {
                    continue;
                }
                if (mb_strlen($tokenClean) >= 3 && (str_contains($normalized, $tokenClean) || str_contains($rawLower, strtolower($token)))) {
                    $score += 15;
                }
            }

            if (!empty($cNameAr) && str_contains($normalized, $cNameAr)) {
                $score += 35;
            }

            if ($score > $highestScore) {
                $highestScore = $score;
                $matchedCourse = $c;
            }
        }

        // إذا تحقق تطابق ملحوظ
        if ($matchedCourse && ($highestScore >= 30 || ($isAskingPrice && $highestScore >= 15))) {
            $currencyLabel = match ($matchedCourse->currency) {
                'YER' => 'ريال يمني',
                'SAR' => 'ريال سعودي',
                'USD' => 'دولار أمريكي',
                default => $matchedCourse->currency,
            };

            $formattedPrice = number_format($matchedCourse->fee_amount, 0);
            $levelName = $matchedCourse->level ? $matchedCourse->level->name : 'برنامج تدريبي متخصص';
            $hours = $matchedCourse->hours > 0 ? "{$matchedCourse->hours} ساعة تدريبية" : "مكثفة";

            $reply = "✨ **تفاصيل ورسوم دورة: {$matchedCourse->name_ar} ({$matchedCourse->name_en})**\n\n";
            $reply .= "💰 **سعر الدورة:** `{$formattedPrice} {$currencyLabel}`\n";
            $reply .= "⏱️ **عدد الساعات:** {$hours}\n";
            $reply .= "🎓 **المستوى الأكاديمي:** {$levelName}\n";

            if (!empty($matchedCourse->description)) {
                $reply .= "📝 **نبذة عن الدورة:** " . \Illuminate\Support\Str::limit($matchedCourse->description, 140) . "\n";
            }

            $reply .= "\n📌 **المزايا المضمنة:**\n";
            $reply .= "• حصص تفاعلية مباشرة 1-on-1 عبر قاعة Air Class الصوتية والمرئية.\n";
            $reply .= "• بنك مناهج رقمي (كتب، صوتيات، فيديوهات، وكويزات دورية).\n";
            $reply .= "• شهادة معتمدة مزودة برمز التحقق الفوري QR Code عند إتمام الدورة بنجاح.\n\n";
            $reply .= "🚀 يمكنك حجز مقعدك فوراً أو إجراء اختبار تحديد المستوى المجاني.";

            return [
                'reply' => $reply,
                'matched_intent' => 'course_price',
                'course' => [
                    'id' => $matchedCourse->id,
                    'name' => $matchedCourse->name,
                    'price' => $matchedCourse->fee_amount,
                    'currency' => $matchedCourse->currency,
                ],
                'suggestions' => [
                    "🚀 بدء اختبار تحديد المستوى المجاني",
                    "📚 ما هي المستويات الأكاديمية؟",
                    "💰 قائمة أسعار بقية الدورات",
                    "💬 حجز الدورة عبر الواتساب مع الإدارة",
                ],
            ];
        }

        return null;
    }

    /**
     * الاستفسار الشامل عن أسعار جميع الدورات
     */
    protected static function handleAllCoursesPricing(): array
    {
        $courses = Course::with('level')->where('is_active', true)->orderBy('course_order')->get();
        if ($courses->isEmpty()) {
            $courses = Course::with('level')->take(10)->get();
        }

        $reply = "📋 **قائمة أسعار الدورات التدريبية المعتمدة في منصة ASOTS:**\n\n";

        $grouped = $courses->groupBy(function ($c) {
            return $c->level ? $c->level->name : 'دورات وبرامج عامة';
        });

        foreach ($grouped as $levelTitle => $items) {
            $reply .= "🏷️ **{$levelTitle}:**\n";
            foreach ($items->take(5) as $item) {
                $curr = match ($item->currency) {
                    'YER' => 'ريال يمني',
                    'SAR' => 'ر.س',
                    'USD' => '$',
                    default => $item->currency,
                };
                $price = number_format($item->fee_amount, 0);
                $reply .= "  • **{$item->name_ar}** ({$item->name_en}): `{$price} {$curr}` ({$item->hours} س)\n";
            }
            $reply .= "\n";
        }

        $reply .= "💡 *جميع الدورات تشمل تدريباً تفاعلياً حياً واختبارات دورية وشهادة معتمدة.*\n";
        $reply .= "👉 هل ترغب بمعرفة تفاصيل دورة محددة؟ اكتب اسمها فقط!";

        return [
            'reply' => $reply,
            'matched_intent' => 'all_courses_pricing',
            'suggestions' => [
                "كم سعر دورة Access A؟",
                "كم سعر دورة Intro A؟",
                "🚀 اختبار تحديد المستوى المجاني",
                "💬 استفسار مباشر عبر الواتساب",
            ],
        ];
    }

    /**
     * الاستفسار عن المستويات الأكاديمية
     */
    protected static function handleLevelsInquiry(string $normalized): array
    {
        $levels = Level::withCount('courses')->orderBy('level_order')->get();

        $reply = "🏛️ **المستويات الأكاديمية في منصة ASOTS:**\n\n";

        foreach ($levels as $lvl) {
            $feeStr = $lvl->monthly_fee > 0 ? number_format($lvl->monthly_fee, 0) . ' ' . ($lvl->currency ?? 'YER') : 'حسب الدورات';
            $reply .= "🎓 **{$lvl->name}** (كود: {$lvl->code})\n";
            $reply .= "• الرسوم الشهرية التقديرية: `{$feeStr}`\n";
            if ($lvl->courses_count > 0) {
                $reply .= "• يتضمن {$lvl->courses_count} دورات تدريبية متسلسلة وفق الإطار الأوروبي CEFR.\n";
            }
            if (!empty($lvl->description)) {
                $reply .= "• " . \Illuminate\Support\Str::limit($lvl->description, 90) . "\n";
            }
            $reply .= "\n";
        }

        $reply .= "🎯 يتم تسكين الطالب في المستوى الأنسب له بناءً على **اختبار تحديد المستوى المجاني** (25 دقيقة).";

        return [
            'reply' => $reply,
            'matched_intent' => 'levels_info',
            'suggestions' => [
                "🚀 خوض اختبار تحديد المستوى الآن",
                "💰 كم أسعار الدورات؟",
                "💬 حجز موعد مع المشرف الأكاديمي",
            ],
        ];
    }

    /**
     * الاستفسار عن باقات الحصص
     */
    protected static function handlePackagesInquiry(): array
    {
        $packages = Package::where('is_active', true)->orderBy('price')->get();

        if ($packages->isEmpty()) {
            return [
                'reply' => "🎁 نوفر باقات حصص فردية مرنة (1-on-1) تناسب جدول الطالب، بدءاً من 10 حصص وحتى باقات مكثفة. لمزيد من التفاصيل والخصومات المتاحة حالياً، يرجى التواصل مع الإدارة عبر الواتساب.",
                'matched_intent' => 'packages_info',
                'suggestions' => ["💬 استفسر عن باقات الحصص عبر الواتساب", "💰 أسعار الدورات الرسمية"],
            ];
        }

        $reply = "🎁 **باقات الحصص الفردية المرنة (1-on-1 Air Class):**\n\n";
        foreach ($packages as $pkg) {
            $price = number_format($pkg->price, 0);
            $curr = $pkg->currency === 'YER' ? 'ريال يمني' : $pkg->currency;
            $reply .= "⭐ **{$pkg->title}:**\n";
            $reply .= "• عدد الحصص: **{$pkg->lessons_count} حصة** (25 دقيقة للحصة)\n";
            $reply .= "• السعر: `{$price} {$curr}`\n";
            if (!empty($pkg->description)) {
                $reply .= "• المميزات: {$pkg->description}\n";
            }
            $reply .= "\n";
        }

        return [
            'reply' => $reply,
            'matched_intent' => 'packages_info',
            'suggestions' => [
                "🚀 حجز حصة تجريبية مجانية",
                "💰 أسعار الدورات الدراسية",
                "💬 شراء باقة عبر الواتساب",
            ],
        ];
    }

    /**
     * الاستفسار عن اختبار تحديد المستوى
     */
    protected static function handlePlacementTestInquiry(): array
    {
        $reply = "🚀 **اختبار تحديد المستوى المجاني (Placement Test & 1-on-1 Trial):**\n\n";
        $reply .= "• **المدة:** 25 دقيقة فقط عبر المنصة الذكية.\n";
        $reply .= "• **المحتوى:** فحص الاستماع، المفردات، القواعد، وتدقيق النطق الفوري عبر الذكاء الاصطناعي (AI Speech Assessment).\n";
        $reply .= "• **النتيجة:** فورية ومجانية بالكامل، وتحدد المستوى والدورة المناسبة لقدراتك بدون أي رسوم.\n\n";
        $reply .= "👉 يمكنك البدء في الاختبار الآن مباشرة بالنقر على الرابط التالي:\n";
        $reply .= "[اضغط هنا لبدء اختبار تحديد المستوى والقبول 🚀](/onboarding)";

        return [
            'reply' => $reply,
            'matched_intent' => 'placement_test',
            'suggestions' => [
                "🚀 الذهاب لاختبار تحديد المستوى",
                "💰 كم أسعار الدورات بعد الاختبار؟",
                "🏛️ المستويات الأكاديمية في المنصة",
            ],
        ];
    }

    /**
     * الاستفسار عن الشهائد المعتمدة
     */
    protected static function handleCertificatesInquiry(): array
    {
        $reply = "🎓 **الشهائد الأكاديمية والاعتماد في منصة ASOTS:**\n\n";
        $reply .= "• تصدر المنصة شهائد إتمام معتمدة لجميع الدورات ومستويات الدبلوم باللغتين العربية والإنجليزية.\n";
        $reply .= "• كل شهادة تحمل **رقماً تسلسلياً فريداً ورمز استجابة سريعة (QR Code)** رسمي.\n";
        $reply .= "• يمكن لأي جهة توظيف أو جامعة التحقق المباشر من صحة الشهادة وسجل درجات الطالب عبر صفحة التحقق العامة.\n\n";
        $reply .= "🔍 يمكنك فحص أي شهادة من خلال رابط التحقق: [صفحة التحقق من الشهائد](/verify/certificate/demo)";

        return [
            'reply' => $reply,
            'matched_intent' => 'certificates_info',
            'suggestions' => [
                "💰 أسعار الدورات التي تمنح شهائد",
                "🏛️ مستويات الدبلوم المعتمدة",
                "💬 تواصل مع الإدارة للاستفسار",
            ],
        ];
    }

    /**
     * الاستفسار عن بيانات التواصل والواتساب
     */
    protected static function handleContactInquiry(): array
    {
        $phone = SiteSetting::get('phone_number', '+967 735 999 101');
        $whatsapp = SiteSetting::get('whatsapp_platform_number') ?: SiteSetting::get('whatsapp_number', '+967 735 999 101');
        $cleanWa = preg_replace('/[^0-9]/', '', $whatsapp);
        $email = SiteSetting::get('contact_email', 'info@asots.edu.ye');
        $address = SiteSetting::get('school_address', 'الجمهورية اليمنية - صنعاء / خدمات التعليم عن بعد لكافة المحافظات والدول');

        $reply = "📞 **قنوات التواصل وخدمة العملاء الرسمية (منصة ASOTS):**\n\n";
        $reply .= "• 💬 **واتساب المباشر للإدارة:** `{$whatsapp}`\n";
        $reply .= "• 📱 **الهاتف:** `{$phone}`\n";
        $reply .= "• ✉️ **البريد الإلكتروني:** `{$email}`\n";
        $reply .= "• 📍 **المقر:** {$address}\n";
        $reply .= "• ⏰ **ساعات الدوام:** يومياً من 8:00 صباحاً حتى 10:00 مساءً.\n\n";
        $reply .= "👉 اضغط على الرابط للمراسلة الفورية عبر واتساب: https://wa.me/{$cleanWa}";

        return [
            'reply' => $reply,
            'matched_intent' => 'contact_info',
            'suggestions' => [
                "💰 قائمة أسعار الدورات",
                "🚀 اختبار القبول وتحديد المستوى",
                "💬 فتح محادثة واتساب الآن",
            ],
        ];
    }

    /**
     * الاستعلام المالي للطالب المسجل
     */
    protected static function handleStudentFinancialInquiry(?User $user): array
    {
        if (!$user) {
            return [
                'reply' => "🔒 للاستعلام عن رصيدك المالي وسنداتك المسجلة، يرجى **تسجيل الدخول** بحسابك، أو اكتب كود الطالب الخاص بك (مثال: `STU-2026-0001`) وسأستعلم لك فوراً عن حسابك المالي.",
                'matched_intent' => 'student_balance_auth_required',
                'suggestions' => [
                    "🔐 تسجيل الدخول للمنصة",
                    "💰 أسعار الدورات العامة",
                    "💬 التواصل مع المحاسب عبر الواتساب",
                ],
            ];
        }

        $acc = $user->financialAccount;
        $name = $user->name;
        $code = $user->user_code ?? $user->student_code ?? ('USR-' . $user->id);

        if (!$acc) {
            return [
                'reply' => "🎓 أهلاً بك يا {$name} (كود: {$code})!\nحسابك الأكاديمي مفعل، ولا توجد مستحقات مالية معلقة عليك حالياً في شجرة الحسابات. إذا قمت بسداد أي رسوم مؤخراً ولم يظهر سندك، يرجى إرسال الإشعار للإدارة.",
                'matched_intent' => 'student_balance_clear',
                'suggestions' => ["📜 السجل الأكاديمي والمالي الشامل", "💬 تواصل مع المحاسب"],
            ];
        }

        $bal = (float)$acc->current_balance;
        $curr = $acc->currency ?? 'YER';
        $currLabel = $curr === 'YER' ? 'ريال يمني' : $curr;
        $formattedBal = number_format(abs($bal), 0);

        if ($bal > 0.01) {
            $status = "⚠️ **المبلغ المتبقي عليك:** `{$formattedBal} {$currLabel}`";
        } elseif ($bal < -0.01) {
            $status = "✅ **رصيدك دائن (فائض مسدد مقدماً):** `{$formattedBal} {$currLabel}`";
        } else {
            $status = "🎉 **حسابك خالص تماماً!** رصيدك المالي الحالي: `0.00 {$currLabel}` ولا توجد أي متأخرات.";
        }

        $reply = "📊 **كشف الحساب المالي للطالب:** [{$name}] (كود: {$code})\n\n";
        $reply .= "• {$status}\n";
        $reply .= "• ⭐ **رصيد النجوم المكافأة:** {$user->stars_balance} نجمة.\n\n";
        $reply .= "يمكنك استعراض تفاصيل كافة سندات القبض ورسوم الدبلوم من خلال صفحة حسابك.";

        return [
            'reply' => $reply,
            'matched_intent' => 'student_balance_result',
            'suggestions' => [
                "📜 استعراض كشف الحساب وسندات القبض",
                "💬 إرسال إشعار سداد عبر الواتساب",
            ],
        ];
    }

    /**
     * الاستعلام عبر كود المستخدم مع حماية كاملة وسرية تامة للبيانات
     */
    protected static function handleUserCodeLookup(string $code, ?User $currentUser = null): array
    {
        $u = User::where('user_code', $code)->orWhere('student_code', $code)->first();
        if (!$u) {
            return [
                'reply' => "⚠️ لم يتم العثور على أي مستخدم مسجل بالكود [{$code}]. يرجى التأكد من صحة الكود أو التواصل مع إدارة المنصة.",
                'matched_intent' => 'code_not_found',
                'suggestions' => ["💬 استعادة كود الدخول عبر الواتساب", "💰 أسعار الدورات"],
            ];
        }

        // التحقق من الصلاحية: هل السائل هو صاحب الحساب نفسه أو ولي أمره أو الإدارة؟
        $isAuthorized = false;
        if ($currentUser) {
            if ($currentUser->id === $u->id || $currentUser->isAdmin() || $currentUser->isSupervisor()) {
                $isAuthorized = true;
            } elseif ($currentUser->isParent() && method_exists($currentUser, 'children') && $currentUser->children->contains('id', $u->id)) {
                $isAuthorized = true;
            }
        }

        // إذا كان مخولاً ومسجلاً الدخول، يعرض كشف الحساب
        if ($isAuthorized) {
            return self::handleStudentFinancialInquiry($u);
        }

        // للزوار غير المسجلين: حماية تامة للخصوصية والسرية المالية
        $maskedName = mb_substr($u->name, 0, 3) . '*** ' . mb_substr($u->name, -3);
        $roleLabel = match ($u->role) {
            'student' => 'طالب مسجل',
            'parent' => 'ولي أمر',
            'teacher' => 'كادر تعليمي',
            default => 'مستخدم بالنظام',
        };

        $reply = "👤 **التحقق من الكود [{$code}]:**\n\n";
        $reply .= "• الحساب: **مسجل ونشط بالمنصة** ({$roleLabel} - {$maskedName}).\n";
        $reply .= "🔒 **سياسة حماية الخصوصية والسرية المالية:**\n";
        $reply .= "لا يُسمح إطلاقاً بعرض السندات أو الرصيد المالي أو نتائج الاختبارات لأي طرف خارجي. يُرجى **تسجيل الدخول** بحسابك الرسمي للاطلاع على كافة تفاصيلك الأكاديمية والمالية بأمان.";

        return [
            'reply' => $reply,
            'matched_intent' => 'code_lookup_privacy_protected',
            'suggestions' => ["🔐 تسجيل الدخول لحسابي", "💬 تواصل مع الإدارة"],
        ];
    }

    /**
     * رد الترحيب الافتراضي
     */
    protected static function defaultWelcomeResponse(?User $user = null): array
    {
        $greeting = $user ? "أهلاً بك يا {$user->name} 🎓" : "أهلاً بك في منصة ASOTS التعليمية 🌟!";

        $reply = "{$greeting}\nأنا **المساعد الآلي الذكي للمنصة** 🤖.\nأنا هنا لمساعدتك على مدار الساعة في الاستفسار الفوري عن:\n\n";
        $reply .= "💰 **أسعار وتفاصيل الدورات التدريبية** (مثال: *كم سعر دورة Access A؟*)\n";
        $reply .= "🏛️ **المستويات الأكاديمية والدبلوم المعتمد**\n";
        $reply .= "🚀 **اختبار تحديد المستوى المجاني** (25 دقيقة مع تقييم نطق الذكاء الاصطناعي)\n";
        $reply .= "📊 **الاستعلام المالي للطلاب والأقساط المسددة**\n";
        $reply .= "💬 **التواصل المباشر مع إدارة المدرسة**\n\n";
        $reply .= "اختر من الخيارات السريعة أدناه أو اكتب سؤالك مباشرة!";

        return [
            'reply' => $reply,
            'matched_intent' => 'welcome',
            'suggestions' => [
                "كم سعر دورة Access A؟",
                "💰 قائمة أسعار جميع الدورات",
                "🏛️ المستويات الأكاديمية",
                "🚀 اختبار تحديد المستوى المجاني",
                "💬 تحدث مع موظف الإدارة عبر الواتساب",
            ],
        ];
    }

    /**
     * الرد الافتراضي الذكي عند عدم مطابقة نية محددة
     */
    protected static function handleFallbackResponse(string $raw): array
    {
        $whatsapp = SiteSetting::get('whatsapp_platform_number') ?: SiteSetting::get('whatsapp_number', '+967735999101');
        $cleanWa = preg_replace('/[^0-9]/', '', $whatsapp);

        $reply = "عذراً، لم أفهم سؤالك بدقة 💡، ولكن يسعدني مساعدتك!\n\n";
        $reply .= "يمكنك سؤالي مباشرة عن:\n";
        $reply .= "• سعر أي دورة، مثل: `كم سعر دورة Access A؟` أو `سعر دورة الذكاء الاصطناعي`.\n";
        $reply .= "• `أسعار الدورات` لعرض القائمة الكاملة.\n";
        $reply .= "• `المستويات الأكاديمية` أو `اختبار تحديد المستوى`.\n\n";
        $reply .= "أو يمكنك التحدث مباشرة مع موظف خدمة العملاء عبر الواتساب بالنقر أدناه.";

        return [
            'reply' => $reply,
            'matched_intent' => 'fallback',
            'suggestions' => [
                "كم سعر دورة Access A؟",
                "💰 قائمة أسعار جميع الدورات",
                "🚀 بدء اختبار تحديد المستوى",
                "💬 محادثة مباشرة مع الإدارة عبر الواتساب",
            ],
            'whatsapp_url' => "https://wa.me/{$cleanWa}?text=" . urlencode("مرحباً، أود الاستفسار عن: " . $raw),
        ];
    }

    /**
     * تنظيف وتوحيد الحروف العربية لتسهيل المطابقة الدقيقة
     */
    public static function normalizeText(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        // توحيد الألف
        $text = preg_replace('/[أإآ]/u', 'ا', $text);
        // توحيد الياء
        $text = preg_replace('/[ىئ]/u', 'ي', $text);
        // توحيد التاء المربوطة
        $text = preg_replace('/[ة]/u', 'ه', $text);
        // إزالة التشكيل
        $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text);
        // استبدال علامات الترقيم بمسافة
        $text = preg_replace('/[؟!\?\.,،\-_#@\*\(\)]/u', ' ', $text);
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * فحص وجود أي من الكلمات المفتاحية في النص
     */
    public static function matchesAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            $kwNorm = self::normalizeText($kw);
            if (str_contains($text, $kwNorm)) {
                return true;
            }
        }
        return false;
    }
}
