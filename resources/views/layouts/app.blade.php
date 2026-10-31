@php
    $currentLocale = session('locale', 'ar');
    $activeThemeId = session('theme') ?? (auth()->check() ? (auth()->user()->theme_preference ?? 'professional_navy') : 'professional_navy');
    $themeData = config('themes.profiles.' . $activeThemeId) ?? config('themes.profiles.professional_navy');

    $activeFont = session('font', 'Cairo');
    $fontFamilies = [
        'Cairo' => "'Cairo', sans-serif",
        'Almarai' => "'Almarai', sans-serif",
        'Tajawal' => "'Tajawal', sans-serif",
        'IBM Plex Sans Arabic' => "'IBM Plex Sans Arabic', sans-serif",
    ];
    $currentFontFamily = $fontFamilies[$activeFont] ?? "'Cairo', sans-serif";

    try {
        $topBanner = \App\Models\Banner::active()->placement('top_announcement')->orderBy('sort_order')->first();
    } catch (\Exception $e) {
        $topBanner = null;
    }

    // إعدادات الهوية البصرية وخلفية الموقع المخصصة
    $siteBg = site_setting('site_background');
    $isBgEnabled = site_setting('site_background_enabled', '0') === '1';
    $bgMode = site_setting('background_mode', 'hero');
    $overlayOpacity = site_setting('overlay_opacity', '0.75');
    $bgBlur = site_setting('background_blur', 'none');
    $applyBodyBg = $isBgEnabled && !empty($siteBg) && ($bgMode === 'body' || $bgMode === 'all_pages');
    $bgImageUrl = $siteBg ? (str_starts_with($siteBg, 'http') ? $siteBg : asset($siteBg)) : null;

    $customLogo = site_setting('site_logo');
    $customLogoText = site_setting('logo_text', 'ASOTS');
    $siteNameAr = site_setting('site_name_ar', config('asots.name_ar'));
    $whatsappNumber = site_setting('whatsapp_platform_number') ?: site_setting('whatsapp_number', '+967 775 342 886');
    $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsappNumber);
    $whatsappNumber2 = site_setting('whatsapp_number_2', '+967 735 999 101');
    $cleanWhatsapp2 = preg_replace('/[^0-9]/', '', $whatsappNumber2);
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}" dir="{{ $currentLocale === 'en' ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? ($siteNameAr . ' - المنصة التعليمية والاستشارية') }}</title>
    @if(auth()->check() || request()->is('admin*', 'help*', 'accounting*', 'student*', 'teacher*', 'parent*', 'classroom*', 'licenses*'))
        <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex, notranslate">
        <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
    @endif
    <meta name="ai-content-declaration" content="no-ai-train, no-scrape">
    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Cairo, Almarai, Tajawal, IBM Plex Sans Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Cairo:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --theme-primary: #0066A1;
            --theme-primary-hover: #004e7c;
            --theme-cta: #F58220;
            --theme-cta-hover: #d96e14;
            --theme-bg: {{ $themeData['bg'] ?? '#f4f8fb' }};
            --theme-card: {{ $themeData['card'] ?? '#ffffff' }};
            --theme-text-main: {{ $themeData['text_main'] ?? '#0f172a' }};
            --theme-text-muted: {{ $themeData['text_muted'] ?? '#475569' }};
            --theme-border: {{ $themeData['border'] ?? '#d4e5f0' }};
            --theme-badge: {{ $themeData['badge'] ?? '#e0f0f8' }};
            --theme-badge-text: {{ $themeData['badge_text'] ?? '#0066A1' }};
            --brand-blue: #0066A1;
            --brand-blue-hover: #004e7c;
            --brand-blue-dark: #003859;
            --brand-orange: #F58220;
            --brand-orange-hover: #d96e14;
        }
        body {
            font-family: {!! $currentFontFamily !!} !important;
            @if($applyBodyBg && $bgImageUrl)
                background-image: linear-gradient(rgba(0, 102, 161, {{ $overlayOpacity }}), rgba(0, 78, 124, {{ $overlayOpacity }})), url('{{ $bgImageUrl }}') !important;
                background-attachment: fixed !important;
                background-position: center !important;
                background-repeat: no-repeat !important;
                background-size: cover !important;
            @else
                background-color: var(--theme-bg) !important;
            @endif
            color: var(--theme-text-main) !important;
        }

        /* ربط ألوان الـ Theme ديناميكياً لتغيير المظهر بالكامل عبر الموقع بتناغم مع الهوية */
        .bg-indigo-600, .bg-indigo-700, .bg-blue-600, .bg-blue-700 { background-color: var(--theme-primary) !important; color: #FFFFFF !important; }
        .hover\:bg-indigo-600:hover, .hover\:bg-indigo-700:hover, .hover\:bg-blue-600:hover { background-color: var(--theme-primary-hover) !important; }
        .text-indigo-600, .text-indigo-700, .text-blue-600 { color: var(--theme-primary) !important; }
        .border-indigo-600, .border-indigo-500, .border-blue-600 { border-color: var(--theme-primary) !important; }
        .ring-indigo-600, .ring-indigo-500 { --tw-ring-color: var(--theme-primary) !important; }

        /* تناسق الأزرار واللمسات البرتقالية والذهبية الخاصة بهوية الشعار */
        .bg-amber-500, .bg-amber-600, .bg-orange-500, .bg-orange-600 { background-color: var(--theme-cta) !important; color: #FFFFFF !important; }
        .hover\:bg-amber-600:hover, .hover\:bg-orange-600:hover { background-color: var(--theme-cta-hover) !important; }
        .text-amber-400, .text-amber-500, .text-orange-500, .text-orange-600 { color: var(--theme-cta) !important; }
        .hover\:text-amber-300:hover, .hover\:text-orange-400:hover { color: #fbd38d !important; }
        .border-amber-400, .border-amber-500, .border-orange-500 { border-color: var(--theme-cta) !important; }

        /* توحيد وتطبيق هوية الشعار الرسمية (الأزرق الداكن والبرتقالي) على كافة البانرات والمقاطع بالمنصة */
        .bg-gradient-to-r.from-slate-900,
        .bg-gradient-to-r.from-slate-950,
        .bg-gradient-to-r.from-indigo-900,
        .bg-gradient-to-r.from-indigo-950,
        .bg-gradient-to-br.from-slate-900,
        .bg-gradient-to-br.from-slate-950,
        .bg-gradient-to-br.from-indigo-900,
        .bg-gradient-to-br.from-indigo-950 {
            background-image: linear-gradient(135deg, #003859 0%, #004E7C 55%, #0066A1 100%) !important;
            border-color: rgba(0, 102, 161, 0.45) !important;
            color: #FFFFFF !important;
        }

        /* كروت المقاطع الداكنة داخل البانرات تتناسق مع الكحلي الداكن المعتمد */
        .bg-slate-800\/80,
        .bg-slate-800 {
            background-color: rgba(0, 43, 69, 0.88) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }

        @if($activeThemeId === 'night_mode')
            .bg-white { background-color: #111827 !important; color: #f9fafb !important; }
            .bg-slate-50, .bg-slate-100 { background-color: #0b1120 !important; }
            .text-slate-900, .text-slate-800 { color: #f9fafb !important; }
            .text-slate-600, .text-slate-500 { color: #94a3b8 !important; }
            .border-slate-200, .border-slate-100 { border-color: #1e293b !important; }
        @endif
    </style>
</head>
<body class="min-h-screen flex flex-col transition-colors duration-200 overflow-x-hidden">

    @if(auth()->check() && auth()->user()->isReviewer())
        <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-amber-500 text-slate-950 font-black text-xs py-2 px-4 text-center shadow-md flex items-center justify-center gap-2 border-b border-amber-600">
            <span>👁️</span>
            <span>وضع الاستعراض والمشاهدة الشاملة (Read-Only) • يمكنك استعراض كافة أقسام النظام والتقارير والدورة المستندية بحرية، والعمليات التعديلية مقفلة للمشاهدة فقط.</span>
        </div>
        <!-- علامة مائية جنائية ديناميكية مشفرة لكامل جلسة الاستعراض -->
        <div class="fixed inset-0 pointer-events-none z-50 opacity-[0.035] bg-repeat" style="background-size: 380px 200px; background-image: url(&quot;data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='380' height='200' viewBox='0 0 380 200'><text x='20' y='50' fill='%23000' font-family='sans-serif' font-size='12' font-weight='bold' transform='rotate(-20 20 50)'>ASOTS DEMO • CONFIDENTIAL</text><text x='30' y='80' fill='%23000' font-family='sans-serif' font-size='10' transform='rotate(-20 30 80)'>{{ auth()->user()->name }} [{{ auth()->user()->user_code ?? auth()->user()->email }}] • {{ request()->ip() }}</text></svg>&quot;);"></div>
        <script>
            // حماية وضع الاستعراض من النسخ وفحص الأكواد وأدوات المطورين
            document.addEventListener('contextmenu', function(e) { e.preventDefault(); return false; });
            document.addEventListener('copy', function(e) {
                e.preventDefault();
                if (e.clipboardData) {
                    e.clipboardData.setData('text/plain', '⚠️ تنبيه: نظام ومنصة مكتب النجم الآسيوي (ASOTS) محمي بموجب قوانين الملكية الفكرية. يمنع نسخ البيانات أو استنساخ البنية الهيكلية.');
                }
                return false;
            });
            document.addEventListener('keydown', function(e) {
                if (e.keyCode === 123) { e.preventDefault(); return false; } // F12 DevTools
                if (e.ctrlKey || e.metaKey) {
                    const k = (e.key || '').toLowerCase();
                    if (['c', 'u', 'p', 's', 'x'].includes(k) || (e.shiftKey && ['i', 'j', 'c'].includes(k))) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
        </script>
    @endif

    <!-- شريط التنقل العلوي المتجاوب المتناسق مع هوية الشعار باللونين الأزرق والبرتقالي -->
    <nav class="text-white shadow-xl sticky top-0 z-50 border-b border-[#004e7c]" style="background: #0066A1;">
        <!-- الصف الأول: الشعار الرسمي + أدوات النظام والمستخدم ولوحات التحكم الإدارية -->
        <div class="w-full max-w-[1750px] mx-auto px-3 sm:px-4 lg:px-6">
            <div class="flex justify-between h-14 sm:h-16 items-center gap-2 sm:gap-3">
                <!-- الشعار واسم المؤسسة الموحد -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group">
                    @if($customLogo)
                        <img src="{{ asset($customLogo) }}" alt="{{ $siteNameAr }}" class="h-10 w-10 sm:h-11 sm:w-11 object-contain rounded-2xl bg-white p-1 shadow-md ring-2 ring-[#F58220]/50 group-hover:scale-105 transition shrink-0">
                    @else
                        <img src="{{ asset('images/logo.png') }}" alt="{{ $siteNameAr }}" class="h-10 w-10 sm:h-11 sm:w-11 object-contain rounded-2xl bg-white p-1 shadow-md ring-2 ring-[#F58220]/50 group-hover:scale-105 transition shrink-0">
                    @endif
                    <div class="flex items-baseline gap-1.5 shrink-0">
                        <span class="font-black text-lg sm:text-xl tracking-wider text-[#F58220] leading-tight">{{ $customLogoText }}</span>
                        <span class="text-xs text-white font-bold hidden md:inline whitespace-nowrap">• مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات</span>
                    </div>
                </a>

                <!-- أدوات النظام والمستخدم ولوحات التحكم الإدارية (الصف العلوي) -->
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <div class="flex items-center gap-1.5 sm:gap-2">
                                <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-black {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-800 text-indigo-200 hover:bg-slate-700' }} transition flex items-center gap-1.5 border border-slate-700" title="لوحة التحكم الرئيسية للمنصة">
                                    <span>🎛️</span>
                                    <span>لوحة الإدارة والتحكم</span>
                                </a>

                                <a href="{{ route('admin.students.index') }}" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('admin.students.*') ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-800 text-emerald-200 hover:bg-slate-700' }} transition border border-slate-700" title="إدارة الطلاب والقبول والتسجيل">
                                    <span>🎓</span>
                                    <span>الطلاب والقبول</span>
                                </a>

                                <a href="{{ route('admin.student_applications.index') }}" class="hidden md:flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('admin.student_applications.*') ? 'bg-[#F58220] text-slate-950 shadow-md font-black' : 'bg-slate-800 text-amber-200 hover:bg-slate-700' }} transition border border-slate-700" title="استعراض وإدارة استمارات تسجيل الطلاب المتقدمين">
                                    <span>📋</span>
                                    <span>استمارات القبول</span>
                                    @php
                                        try {
                                            $pendingAppsCount = \App\Models\StudentApplication::where('status', 'pending')->count();
                                        } catch(\Exception $e) { $pendingAppsCount = 0; }
                                    @endphp
                                    @if($pendingAppsCount > 0)
                                        <span class="bg-[#F58220] text-slate-950 text-[10px] font-black px-1.5 py-0.2 rounded-full">{{ $pendingAppsCount }}</span>
                                    @endif
                                </a>

                                <a href="{{ route('admin.surveys.index') }}" class="hidden lg:flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('admin.surveys.*') ? 'bg-amber-500 text-slate-950 shadow-md font-black' : 'bg-slate-800 text-amber-300 hover:bg-slate-700' }} transition border border-slate-700" title="استبيانات وتقييمات الطلاب للمدرسين والدبلوم">
                                    <span>📊</span>
                                    <span>استبيانات الطلاب</span>
                                </a>

                                @php
                                    try {
                                        $pendingReqCountBadge = \App\Models\PasswordResetRequest::where('status', 'pending')->count();
                                    } catch(\Exception $e) { $pendingReqCountBadge = 0; }
                                @endphp
                                @if($pendingReqCountBadge > 0)
                                    <a href="{{ route('admin.users.index') }}#requests" class="px-2.5 py-1.5 rounded-xl text-[11px] font-bold bg-rose-600/30 text-rose-200 border border-rose-500/30 hover:bg-rose-600/50 transition flex items-center gap-1 relative" title="طلبات استعادة كلمة المرور المعلقة">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                                        <span>{{ $pendingReqCountBadge }} طلبات</span>
                                    </a>
                                @endif
                            </div>
                        @elseif(auth()->user()->isSupervisor())
                            <div class="hidden lg:flex items-center gap-1 bg-slate-950/70 p-1 rounded-xl border border-slate-800">
                                <a href="{{ route('supervisor.dashboard') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold {{ request()->routeIs('supervisor.dashboard') ? 'bg-[#F58220] text-slate-950 font-black' : 'text-purple-200 hover:bg-slate-800' }}">لوحة الإشراف 🛡️</a>
                                @if(auth()->user()->hasPermission('curriculum.view') || auth()->user()->hasPermission('manage_courses') || auth()->user()->hasPermission('curriculum.create') || auth()->user()->hasPermission('curriculum.edit'))
                                    <a href="{{ route('admin.courses.index') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold {{ request()->routeIs('admin.courses.*') ? 'bg-[#F58220] text-slate-950 font-black' : 'text-emerald-300 hover:bg-slate-800' }}">المناهج والأسعار 📚</a>
                                @endif
                                @if(auth()->user()->hasPermission('students.view') || auth()->user()->hasPermission('students.create'))
                                    <a href="{{ route('admin.students.index') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold {{ request()->routeIs('admin.students.*') ? 'bg-[#F58220] text-slate-950 font-black' : 'text-emerald-200 hover:bg-slate-800' }}">الطلاب 🎓</a>
                                @endif
                                @if(auth()->user()->hasPermission('groups.view'))
                                    <a href="{{ route('admin.groups.index') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold {{ request()->routeIs('admin.groups.index') ? 'bg-[#F58220] text-slate-950 font-black' : 'text-indigo-200 hover:bg-slate-800' }}">المجموعات 👥</a>
                                @endif
                                @if(auth()->user()->hasPermission('evaluations.view'))
                                    <a href="{{ route('admin.groups.evaluations.index') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold {{ request()->routeIs('admin.groups.evaluations.*') ? 'bg-[#F58220] text-slate-950 font-black' : 'text-amber-300 hover:bg-slate-800' }}">التقييم 📋</a>
                                @endif
                                @if(auth()->user()->hasPermission('exams_management.view'))
                                    <a href="{{ route('admin.exams_management.index') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold {{ request()->routeIs('admin.exams_management.*') ? 'bg-[#F58220] text-slate-950 font-black' : 'text-rose-300 hover:bg-slate-800' }}">الاختبارات والشهائد 🎓</a>
                                @endif
                                @if(auth()->user()->hasPermission('exams.view'))
                                    <a href="{{ route('admin.exams.index') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold {{ request()->routeIs('admin.exams.index') ? 'bg-[#F58220] text-slate-950 font-black' : 'text-rose-200 hover:bg-slate-800' }}">الكويزات 🎯</a>
                                @endif
                                @if(auth()->user()->hasPermission('reports.academic') || auth()->user()->hasPermission('reports.financial'))
                                    <a href="{{ route('admin.reports.index') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold {{ request()->routeIs('admin.reports.*') ? 'bg-[#F58220] text-slate-950 font-black' : 'text-amber-300 hover:bg-slate-800' }}">التقارير 📊</a>
                                @endif
                                @if(auth()->user()->hasPermission('finance.view'))
                                    <a href="{{ route('accounting.dashboard') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold {{ request()->routeIs('accounting.*') ? 'bg-[#F58220] text-slate-950 font-black' : 'text-teal-300 hover:bg-slate-800' }}">المحاسبة 💼</a>
                                @endif
                                @if(auth()->user()->hasPermission('system.users_view'))
                                    <a href="{{ route('admin.users.index') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold {{ request()->routeIs('admin.users.*') ? 'bg-[#F58220] text-slate-950 font-black' : 'text-slate-200 hover:bg-slate-800' }}">المستخدمين 👥</a>
                                @endif
                            </div>
                        @elseif(auth()->user()->isAccountant())
                            <div class="hidden lg:flex items-center gap-1 bg-slate-950/70 p-1 rounded-xl border border-slate-800">
                                <a href="{{ route('accounting.dashboard') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-200 hover:bg-slate-800">المحاسبة والسندات</a>
                                <a href="{{ route('accounting.cash_boxes.index') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-300 hover:bg-slate-800">الصناديق والبنوك 🏦</a>
                                <a href="{{ route('admin.reports.index') }}" class="px-2.5 py-1 rounded-lg text-xs font-bold text-amber-300 hover:bg-slate-800">التقارير 📊</a>
                            </div>
                        @elseif(auth()->user()->isTeacher())
                            <div class="flex items-center gap-2">
                                <a href="{{ route('teacher.dashboard') }}" class="hidden sm:inline-block px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-600/30 text-blue-200 border border-blue-500/30 hover:bg-blue-600/50">لوحة المعلم 👨‍🏫</a>
                                <a href="{{ route('admin.groups.evaluations.index') }}" class="hidden sm:inline-block px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-400/30 hover:bg-amber-500/30">التقييم والحضور 📋</a>
                                <a href="{{ route('admin.exams_management.index') }}" class="hidden sm:inline-block px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-400/30 hover:bg-rose-500/30">الاختبارات والشهائد 🎓</a>
                            </div>
                        @elseif(auth()->user()->isStudent())
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('student.portal') }}" class="hidden sm:inline-block px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-600/30 text-indigo-200 border border-indigo-500/30 hover:bg-indigo-600/50">بوابة الطالب 🎓</a>
                                <a href="{{ route('student.surveys.index') }}" class="hidden sm:inline-block px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-400/30 hover:bg-amber-500/30" title="تقييم الحصة والأستاذ والدبلوم">تقييم الحصة والأستاذ ⭐</a>
                            </div>
                        @elseif(auth()->user()->isParent())
                            <a href="{{ route('parent.dashboard') }}" class="hidden sm:inline-block px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-500/30 text-amber-200 border border-amber-500/30 hover:bg-amber-500/50">لوحة ولي الأمر 👨‍👧‍👦</a>
                        @endif

                        <!-- شارة المستخدم مع الكود الفريد والجنس -->
                        <div class="flex items-center gap-1.5 bg-slate-800 px-2 sm:px-2.5 py-1 rounded-xl border border-slate-700 shrink-0">
                            <div class="text-right">
                                <div class="flex items-center gap-1 justify-end">
                                    <span class="text-xs font-bold leading-tight text-white max-w-[90px] sm:max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                                    <span class="text-[10px]">{{ auth()->user()->gender_icon }}</span>
                                </div>
                                <div class="flex items-center gap-1 justify-end mt-0.5">
                                    <span class="text-[10px] text-amber-400 font-medium">
                                        {{ auth()->user()->permissionGroup?->name ?? (auth()->user()->role === 'admin' ? 'مدير عام' : (auth()->user()->role === 'supervisor' ? 'المشرف' : (auth()->user()->role === 'accountant' ? 'المحاسب' : (auth()->user()->role === 'parent' ? 'ولي أمر' : (auth()->user()->role === 'teacher' ? 'معلم' : 'طالب'))))) }}
                                    </span>
                                    <span class="text-[9px] font-mono font-bold bg-slate-900 text-indigo-300 px-1 rounded border border-slate-700" title="كودك الفريد للدخول">
                                        {{ auth()->user()->user_code ?? auth()->user()->student_code }}
                                    </span>
                                </div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" title="تسجيل الخروج" class="p-1 text-slate-400 hover:text-red-400 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                </button>
                            </form>
                        </div>

                    @else
                        @if($currentLocale === 'en')
                            <a href="{{ route('login') }}" class="text-xs font-bold text-slate-300 hover:text-white px-2 sm:px-3 py-1.5 whitespace-nowrap">Sign In</a>
                            <a href="{{ route('onboarding') }}" class="text-xs font-bold bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-slate-950 font-black px-3 py-1.5 rounded-lg shadow-md transition whitespace-nowrap">Placement 🚀</a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs font-bold text-slate-300 hover:text-white px-2 sm:px-3 py-1.5 whitespace-nowrap">تسجيل الدخول</a>
                            <a href="{{ route('onboarding') }}" class="text-xs font-bold bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-slate-950 font-black px-3 py-1.5 rounded-lg shadow-md transition whitespace-nowrap">اختبار القبول 🚀</a>
                        @endif
                    @endauth

                    <!-- زر مركز المساعدة ودليل تشغيل النظام الشامل -->
                    <a href="{{ route('help.index') }}" title="مركز المساعدة ودليل استخدام النظام الشامل" class="p-1.5 sm:px-2.5 sm:py-1.5 rounded-lg {{ request()->routeIs('help.*') ? 'bg-amber-500 text-slate-950 font-black shadow-md' : 'bg-slate-800 hover:bg-slate-700 text-amber-300 border border-slate-700' }} text-xs font-bold flex items-center gap-1.5 transition shrink-0">
                        <span>📖</span>
                        <span class="hidden sm:inline">دليل النظام</span>
                    </a>

                    <!-- زر اختيار المظهر 10 Themes -->
                    <a href="{{ route('settings.appearance') }}" title="تخصيص المظهر (10 قوالب)" class="p-1.5 sm:px-2 sm:py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-400 border border-slate-700 text-xs font-bold flex items-center gap-1 transition shrink-0">
                        <span>🎨</span>
                        <span class="hidden 2xl:inline text-slate-200 font-bold">المظهر</span>
                    </a>

                    <!-- محول اللغة (Language Switcher) -->
                    @if($currentLocale === 'en')
                        <a href="{{ route('lang.switch', 'ar') }}" title="التحويل للعربية" class="px-2 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 text-xs font-bold text-slate-200 transition shrink-0">
                            🇾🇪 <span class="hidden sm:inline">العربية</span>
                        </a>
                    @else
                        <a href="{{ route('lang.switch', 'en') }}" title="Switch to English" class="px-2 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 text-xs font-bold text-slate-200 transition font-sans shrink-0">
                            🇺🇸 <span class="hidden sm:inline">English</span>
                        </a>
                    @endif

                    <!-- زر القائمة المتنقلة للشاشات المتوسطة والصغيرة (Mobile Hamburger) -->
                    <button type="button" id="mobileMenuBtn" aria-label="قائمة التصفح" class="lg:hidden p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 focus:outline-none transition">
                        <svg id="hamburgerIcon" class="w-5 h-5 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg id="closeIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- الصف الثاني: شريط التبويبات والمناهج والخدمات الكامل (مخصص بالكامل للكمبيوتر والشاشات الكبيرة) -->
        <div class="hidden lg:block border-t border-[#004e7c] shadow-inner" style="background: #005586;">
            <div class="w-full max-w-[1750px] mx-auto px-4 py-1.5">
                <div class="flex items-center justify-center gap-1 xl:gap-2 flex-wrap">
                    <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('home') ? 'bg-[#F58220] text-slate-950 font-black shadow-md' : 'text-white hover:text-[#F58220] hover:bg-[#004e7c]/70' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5">
                        <span class="text-xs">🏠</span>
                        <span>الرئيسية</span>
                    </a>
                    <a href="{{ route('leadership') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('leadership') ? 'bg-[#F58220] text-slate-950 font-black shadow-md' : 'text-amber-300 hover:text-amber-200 hover:bg-[#004e7c]/70' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5">
                        <span class="text-xs">🎓</span>
                        <span>القيادة الأكاديمية</span>
                    </a>
                    <a href="{{ route('quality') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('quality') ? 'bg-[#F58220] text-slate-950 font-black shadow-md' : 'text-emerald-300 hover:text-emerald-200 hover:bg-[#004e7c]/70' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5">
                        <span class="text-xs">🛡️</span>
                        <span>الجودة AQED</span>
                    </a>
                    <a href="{{ route('curriculum') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('curriculum') ? 'bg-[#F58220] text-slate-950 font-black shadow-md' : 'text-white hover:text-[#F58220] hover:bg-[#004e7c]/70' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5">
                        <span class="text-xs">📖</span>
                        <span>المناهج وCEFR</span>
                    </a>
                    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->hasPermission('curriculum.view') || auth()->user()->hasPermission('manage_courses')))
                        <a href="{{ route('admin.courses.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-black {{ request()->routeIs('admin.courses.*') ? 'bg-[#F58220] text-slate-950 shadow-md' : 'bg-emerald-600/40 text-emerald-100 border border-emerald-400/40 hover:bg-emerald-600/60' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5" title="إدارة المناهج والدورات والأسعار والمستويات">
                            <span class="text-xs">📚</span>
                            <span>إدارة المناهج والأسعار</span>
                        </a>
                    @endif
                    <a href="{{ route('adult_english') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('adult_english') ? 'bg-[#F58220] text-slate-950 font-black shadow-md' : 'text-white hover:text-[#F58220] hover:bg-[#004e7c]/70' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5">
                        <span class="text-xs">🎯</span>
                        <span>التوفل والكبار</span>
                    </a>
                    <a href="{{ route('translation_services') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('translation_services') ? 'bg-[#F58220] text-slate-950 font-black shadow-md' : 'text-white hover:text-[#F58220] hover:bg-[#004e7c]/70' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5">
                        <span class="text-xs">🌐</span>
                        <span>الترجمة</span>
                    </a>
                    <a href="{{ route('consultancy_services') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('consultancy_services') ? 'bg-[#F58220] text-slate-950 font-black shadow-md' : 'text-white hover:text-[#F58220] hover:bg-[#004e7c]/70' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5">
                        <span class="text-xs">💼</span>
                        <span>الاستشارات</span>
                    </a>
                    <a href="{{ route('packages.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('packages.index') ? 'bg-[#F58220] text-slate-950 font-black shadow-md' : 'text-white hover:text-[#F58220] hover:bg-[#004e7c]/70' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5">
                        <span class="text-xs">💳</span>
                        <span>الباقات والأسعار</span>
                    </a>
                    <a href="{{ route('student.booking') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('student.booking') ? 'bg-[#F58220] text-slate-950 font-black shadow-md' : 'text-white hover:text-[#F58220] hover:bg-[#004e7c]/70' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5">
                        <span class="text-xs">🎙️</span>
                        <span>حجز المعلمين</span>
                    </a>
                    <a href="{{ route('about') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('about') ? 'bg-[#F58220] text-slate-950 font-black shadow-md' : 'text-white hover:text-[#F58220] hover:bg-[#004e7c]/70' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5">
                        <span class="text-xs">🏛️</span>
                        <span>عن المكتب</span>
                    </a>
                    <a href="{{ route('student.registration') }}" class="px-3 py-1.5 rounded-xl text-xs font-black {{ request()->routeIs('student.registration') ? 'bg-[#F58220] text-slate-950 shadow-md font-black' : 'bg-emerald-600/40 text-emerald-100 border border-emerald-400/30 hover:bg-emerald-600/60' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5">
                        <span class="text-xs">📋</span>
                        <span>إستمارة التسجيل</span>
                    </a>
                    <a href="{{ route('contact') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('contact') ? 'bg-[#F58220] text-slate-950 font-black shadow-md' : 'text-white hover:text-[#F58220] hover:bg-[#004e7c]/70' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5">
                        <span class="text-xs">📞</span>
                        <span>اتصل بنا</span>
                    </a>
                    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->hasPermission('system.licenses_view') || auth()->user()->hasPermission('view_licenses')))
                        <a href="{{ route('licenses.viewer') }}" class="px-3 py-1.5 rounded-xl text-xs font-black {{ request()->routeIs('licenses.viewer') ? 'bg-[#F58220] text-slate-950 shadow-md' : 'bg-emerald-600/40 text-emerald-200 border border-emerald-400/30 hover:bg-emerald-600/60' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5" title="استعراض وثائق التراخيص الرسمية المحمية">
                            <span class="text-xs">📜</span>
                            <span>التراخيص والاعتمادات</span>
                        </a>
                    @endif
                    <a href="{{ route('announcements.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ request()->routeIs('announcements.*') ? 'bg-[#F58220] text-slate-950 font-black shadow-md' : 'text-amber-300 hover:text-amber-200 hover:bg-[#004e7c]/70' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5">
                        <span class="text-xs">📢</span>
                        <span>الإعلانات</span>
                    </a>
                    <a href="{{ route('help.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-black {{ request()->routeIs('help.*') ? 'bg-amber-400 text-slate-950 shadow-sm' : 'text-amber-300 hover:text-amber-200 hover:bg-[#004e7c]/70' }} transition-all duration-150 transform hover:scale-105 whitespace-nowrap flex items-center gap-1.5">
                        <span class="text-xs">📖</span>
                        <span>دليل النظام</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- القائمة المتنقلة للشاشات الصغيرة (Mobile Drawer) -->
        <div id="mobileMenu" class="hidden lg:hidden border-t border-[#004e7c] bg-[#005586] px-4 py-3 space-y-1">
            @auth
                <div class="pb-2 mb-2 border-b border-white/15 grid grid-cols-2 gap-1.5 text-xs">
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-white/20 text-white text-center">لوحة الإدارة</a>
                        <a href="{{ route('admin.student_applications.index') }}" class="px-2.5 py-1.5 rounded-lg font-black bg-[#F58220] text-slate-950 text-center">استمارات القبول 📋</a>
                        <a href="{{ route('admin.reports.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-[#F58220]/40 text-white text-center">التقارير 📊</a>
                        <a href="{{ route('admin.permission_groups.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-white/10 text-white text-center">الصلاحيات 🛡️</a>
                        <a href="{{ route('admin.branding.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-[#F58220] text-slate-950 text-center font-black">الهوية والتراخيص 📜</a>
                        <a href="{{ route('admin.users.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-white/20 text-white text-center">المستخدمين 👥</a>
                        <a href="{{ route('admin.courses.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-white/10 text-white text-center">المناهج والأسعار 📚</a>
                        <a href="{{ route('licenses.viewer') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-emerald-600/50 text-white text-center col-span-2">📜 عارض التراخيص المحمي</a>
                        <a href="{{ route('admin.exams.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-rose-600/30 text-rose-200 text-center">الكويزات 🎯</a>
                        <a href="{{ route('admin.exams_management.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-amber-500/30 text-amber-200 text-center">الاختبارات والشهائد 🎓</a>
                        <a href="{{ route('accounting.dashboard') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-teal-600/30 text-teal-200 text-center">المحاسبة 💼</a>
                        <a href="{{ route('admin.whatsapp.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-emerald-600/30 text-emerald-200 text-center">الواتساب 💬</a>
                        <a href="{{ route('help.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-amber-400 text-slate-950 text-center col-span-2 shadow-xs">دليل النظام الشامل 📖</a>
                    @elseif(auth()->user()->isSupervisor())
                        <a href="{{ route('supervisor.dashboard') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-[#F58220] text-slate-950 text-center font-black">لوحة الإشراف 🛡️</a>
                        @if(auth()->user()->hasPermission('curriculum.view') || auth()->user()->hasPermission('manage_courses') || auth()->user()->hasPermission('curriculum.create') || auth()->user()->hasPermission('curriculum.edit'))
                            <a href="{{ route('admin.courses.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-emerald-600/50 text-white text-center">المناهج والأسعار 📚</a>
                        @endif
                        @if(auth()->user()->hasPermission('students.view') || auth()->user()->hasPermission('students.create'))
                            <a href="{{ route('admin.students.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-blue-600/50 text-white text-center">الطلاب 🎓</a>
                        @endif
                        @if(auth()->user()->hasPermission('groups.view'))
                            <a href="{{ route('admin.groups.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-indigo-600/50 text-white text-center">المجموعات 👥</a>
                        @endif
                        @if(auth()->user()->hasPermission('evaluations.view'))
                            <a href="{{ route('admin.groups.evaluations.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-amber-500/50 text-white text-center">التقييم والحضور 📋</a>
                        @endif
                        @if(auth()->user()->hasPermission('exams_management.view'))
                            <a href="{{ route('admin.exams_management.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-rose-600/50 text-white text-center">الاختبارات والشهائد 🎓</a>
                        @endif
                        @if(auth()->user()->hasPermission('exams.view'))
                            <a href="{{ route('admin.exams.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-rose-500/50 text-white text-center">الكويزات 🎯</a>
                        @endif
                        @if(auth()->user()->hasPermission('reports.academic') || auth()->user()->hasPermission('reports.financial'))
                            <a href="{{ route('admin.reports.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-amber-500/50 text-white text-center">التقارير 📊</a>
                        @endif
                        @if(auth()->user()->hasPermission('finance.view'))
                            <a href="{{ route('accounting.dashboard') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-teal-600/50 text-white text-center">المحاسبة 💼</a>
                        @endif
                        @if(auth()->user()->hasPermission('system.users_view'))
                            <a href="{{ route('admin.users.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-slate-700/80 text-white text-center">المستخدمين 👥</a>
                        @endif
                        @if(auth()->user()->hasPermission('system.licenses_view') || auth()->user()->hasPermission('view_licenses'))
                            <a href="{{ route('licenses.viewer') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-emerald-600/50 text-white text-center col-span-2">📜 عارض التراخيص المحمي</a>
                        @endif
                        <a href="{{ route('help.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-amber-400 text-slate-950 text-center col-span-2 shadow-xs">دليل النظام 📖</a>
                    @elseif(auth()->user()->isTeacher())
                        <a href="{{ route('teacher.dashboard') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-blue-600/30 text-blue-200 text-center">لوحة المعلم 👨‍🏫</a>
                        <a href="{{ route('admin.groups.evaluations.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-amber-500/30 text-amber-200 text-center">التقييم والحضور 📋</a>
                        <a href="{{ route('admin.exams_management.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-rose-600/30 text-rose-200 text-center">الاختبارات والشهائد 🎓</a>
                        <a href="{{ route('help.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-amber-400 text-slate-950 text-center shadow-xs">دليل النظام 📖</a>
                    @elseif(auth()->user()->isAccountant())
                        <a href="{{ route('accounting.dashboard') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-emerald-600/30 text-emerald-200 text-center">المحاسبة والسندات</a>
                        <a href="{{ route('accounting.cash_boxes.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-teal-600/30 text-teal-200 text-center">الصناديق والبنوك 🏦</a>
                        <a href="{{ route('admin.reports.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-amber-500/30 text-amber-200 text-center">التقارير 📊</a>
                        <a href="{{ route('help.index') }}" class="px-2.5 py-1.5 rounded-lg font-bold bg-amber-400 text-slate-950 text-center col-span-2 shadow-xs">دليل النظام 📖</a>
                    @endif
                </div>
            @endauth
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('home') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">الرئيسية</a>
            <a href="{{ route('leadership') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('leadership') ? 'bg-indigo-600 text-white' : 'text-amber-300 hover:bg-slate-800' }}">القيادة الأكاديمية 🎓</a>
            <a href="{{ route('quality') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('quality') ? 'bg-indigo-600 text-white' : 'text-emerald-300 hover:bg-slate-800' }}">الجودة AQED 🛡️</a>
            <a href="{{ route('curriculum') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('curriculum') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">المناهج وخارطة مستويات CEFR</a>
            <a href="{{ route('adult_english') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('adult_english') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">التوفل وإعداد الكبار</a>
            <a href="{{ route('translation_services') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('translation_services') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">الترجمة والترجمة الفورية</a>
            <a href="{{ route('consultancy_services') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('consultancy_services') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">الاستشارات الأكاديمية والمهنية</a>
            <a href="{{ route('packages.index') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('packages.index') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">الباقات والأسعار</a>
            <a href="{{ route('student.booking') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('student.booking') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">حجز المعلمين 🎙️</a>
            <a href="{{ route('about') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('about') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">عن مكتب ASOTS</a>
            <a href="{{ route('announcements.index') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('announcements.*') ? 'bg-[#F58220] text-slate-950 font-black' : 'text-amber-300 hover:bg-slate-800' }}">📢 الإعلانات والعروض الترويجية</a>
            <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-lg text-sm font-bold {{ request()->routeIs('contact') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">اتصل بنا</a>
            <a href="{{ route('help.index') }}" class="block px-3 py-2 rounded-lg text-sm font-black {{ request()->routeIs('help.*') ? 'bg-amber-500 text-slate-950' : 'text-amber-300 hover:bg-slate-800' }}">📖 دليل النظام ومركز المساعدة</a>
        </div>
    </nav>

    <!-- شريط الإعلان الترويجي العلوي (Visual CMS Announcement) -->
    @if($topBanner)
        <div id="topBannerBar" class="bg-gradient-to-r {{ $topBanner->theme_style === 'amber' ? 'from-amber-600 via-amber-500 to-amber-600 text-slate-950' : ($topBanner->theme_style === 'emerald' ? 'from-emerald-700 via-emerald-600 to-emerald-700 text-white' : ($topBanner->theme_style === 'rose' ? 'from-rose-700 via-rose-600 to-rose-700 text-white' : 'from-indigo-800 via-indigo-700 to-indigo-800 text-white')) }} py-2 px-4 text-xs shadow-md border-b border-black/10 transition z-40 relative">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-base">{{ $topBanner->icon }}</span>
                    <strong class="font-black">{{ $currentLocale === 'en' && $topBanner->title_en ? $topBanner->title_en : $topBanner->title }}</strong>
                    @if($topBanner->subtitle)
                        <span class="hidden md:inline text-[11px] opacity-90">• {{ $topBanner->subtitle }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ $topBanner->link_url }}" class="bg-slate-950 hover:bg-slate-900 text-white font-black px-3.5 py-1 rounded-xl shadow-sm transition whitespace-nowrap text-[11px]">
                        {{ $topBanner->button_text }}
                    </a>
                    <button type="button" onclick="document.getElementById('topBannerBar').remove()" class="text-xs opacity-75 hover:opacity-100 p-1">✕</button>
                </div>
            </div>
        </div>
    @endif

    <!-- شريط تنبيه الزوار غير المسجلين (Visitor / Unregistered Banner) -->
    @guest
        @if(!request()->routeIs('onboarding') && !request()->routeIs('login') && !request()->routeIs('register'))
            <div class="bg-gradient-to-r from-indigo-950 via-slate-900 to-indigo-950 border-b border-indigo-500/20 py-2.5 px-4 text-xs shadow-inner">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
                    <div class="flex items-center gap-2 text-slate-200">
                        <span class="text-amber-400 text-sm">✨</span>
                        <span class="font-bold">
                            {{ $currentLocale === 'en' ? 'Welcome to ASOTS! Please sign up to unlock all features, schedule 1-on-1 trial lessons, and get AI speech reports.' : 'مرحباً بك في ASOTS! يرجى التسجيل للحصول على جميع المميزات وحجز حصة تجريبية مجانية 1-on-1 وتقارير النطق بالذكاء الاصطناعي.' }}
                        </span>
                    </div>
                    <a href="{{ route('onboarding') }}" class="bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-600 hover:to-amber-500 text-slate-950 font-black px-3.5 py-1.5 rounded-xl shadow-sm transition whitespace-nowrap text-[11px] flex items-center gap-1.5">
                        <span>{{ $currentLocale === 'en' ? 'Start Guided Onboarding' : 'ابدأ مسار الانضمام الذكي' }}</span>
                        <span>{{ $currentLocale === 'en' ? '→' : '←' }}</span>
                    </a>
                </div>
            </div>
        @endif
    @endguest

    <!-- الإشعارات والتنبيهات -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1 w-full min-w-0">
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border-r-4 border-emerald-500 p-4 rounded-xl shadow-sm flex items-center justify-between text-emerald-800">
                <div class="flex items-center gap-2">
                    <span class="text-xl">✅</span>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('whatsapp_direct_url'))
            <div class="mb-6 bg-gradient-to-r from-emerald-600 to-teal-700 text-white p-4 sm:p-5 rounded-2xl shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4 border border-emerald-400/40">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-2xl shrink-0">
                        💬
                    </div>
                    <div>
                        <div class="font-black text-sm text-emerald-100 flex items-center gap-2">
                            <span>إشعار الواتساب جاهز للإرسال الفوري</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-emerald-800">مجاناً بنقرة واحدة</span>
                        </div>
                        <p class="text-xs text-white/90 mt-0.5">
                            المستلم: <span class="font-bold underline">{{ session('whatsapp_recipient_name', 'العميل') }}</span>
                            @if(session('whatsapp_voucher_no'))
                                | المستند رقم: <span class="font-mono font-bold">{{ session('whatsapp_voucher_no') }}</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <a href="{{ session('whatsapp_direct_url') }}" target="_blank" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-white text-emerald-800 hover:bg-emerald-50 font-black text-xs shadow-md transition flex items-center justify-center gap-2">
                        <span>🚀</span>
                        <span>إرسال عبر الواتساب الآن</span>
                    </a>
                </div>
            </div>
        @endif

        @if(session('error_with_actions'))
            @php $act = session('error_with_actions'); @endphp
            <div class="mb-6 bg-gradient-to-r from-rose-50 to-amber-50 border-r-4 border-rose-500 p-5 rounded-2xl shadow-sm text-slate-800 border border-rose-200">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">🛡️</span>
                            <h4 class="font-black text-sm sm:text-base text-rose-900">{{ $act['title'] ?? 'قيد أمان: تعذر إتمام الحذف' }}</h4>
                            @if(!empty($act['user_code']))
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-rose-200 text-rose-900 font-bold">{{ $act['user_code'] }}</span>
                            @endif
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">{{ $act['message'] ?? session('error') }}</p>
                        @if(!empty($act['reasons']) && is_array($act['reasons']))
                            <div class="mt-2 pt-2 border-t border-rose-200/60">
                                <span class="text-[11px] font-bold text-rose-800 block mb-1">📋 العمليات والقيود المسجلة باسم الحساب:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($act['reasons'] as $reason)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white/90 border border-rose-200 text-slate-700 text-xs shadow-2xs">
                                            <span>🔹</span>
                                            <span>{{ $reason }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    @if(!empty($act['user_id']))
                        <div class="shrink-0 flex items-center gap-2">
                            <form action="{{ route('admin.users.toggle_status', $act['user_id']) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2.5 rounded-xl font-black text-xs bg-slate-900 hover:bg-slate-800 text-amber-300 shadow-md transition flex items-center gap-1.5 whitespace-nowrap">
                                    <span>⏸️</span>
                                    <span>إيقاف وتعطيل الحساب بدلاً من الحذف</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @elseif(session('error'))
            <div class="mb-6 bg-red-50 border-r-4 border-red-500 p-4 rounded-xl shadow-sm flex items-center justify-between text-red-800">
                <div class="flex items-center gap-2">
                    <span class="text-xl">⚠️</span>
                    <span class="font-bold text-sm">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- الفوتر المؤسسي الرسمي لمنصة ASOTS المتناسق مع ألوان الهوية -->
    <footer class="text-white border-t-2 border-[#004e7c] pt-12 pb-8 mt-auto text-xs" style="background: #0066A1;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- 1. الهوية المؤسسية والشعارات -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-12 h-12 bg-white rounded-2xl p-1 flex items-center justify-center shadow-md ring-2 ring-[#F58220]/50 shrink-0">
                            <img src="{{ asset('images/logo.png') }}" alt="ASOTS Logo" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <span class="font-extrabold text-base tracking-wider text-[#F58220] block leading-tight">ASOTS</span>
                            <span class="text-[11px] text-white font-bold">مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات</span>
                        </div>
                    </div>
                    <p class="text-sky-100 leading-relaxed text-[11px]">
                        منصة دولية للتعليم اللغوي، والتواصل، والترجمة، والتعليم المهني والاستشارات.
                    </p>
                    <div class="text-[11px] text-amber-200 font-bold bg-[#004e7c]/70 p-2.5 rounded-xl border border-white/20">
                        {{ config('asots.promise_ar') }}
                    </div>
                </div>

                <!-- 2. القيادة الأكاديمية والجودة AQED -->
                <div class="space-y-3">
                    <h4 class="font-black text-sm text-white flex items-center gap-2">
                        <span>🎓</span>
                        <span>القيادة الأكاديمية والجودة</span>
                    </h4>
                    <div class="space-y-1.5 text-[11px] text-sky-100">
                        <div>
                            <span class="text-sky-300 text-[10px] block font-semibold">القيادة:</span>
                            <strong class="text-[#F58220] block font-bold text-xs">{{ site_setting('leadership_name_ar', config('asots.leadership.name_ar', 'الدكتور/ فخر الدين ناجي ردمان عبده الصوفي')) }}</strong>
                        </div>
                        <span class="text-white/90 block font-semibold">{{ site_setting('leadership_title_ar', config('asots.leadership.title_ar', 'أستاذ اللسانيات التطبيقية الحديثة')) }}</span>
                        <span class="text-amber-200/90 block text-[10.5px] leading-snug">{{ site_setting('leadership_role_ar', config('asots.leadership.role_ar', 'مؤسس ومدير — مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS)')) }}</span>
                    </div>
                    <div class="pt-2 border-t border-white/15 text-[11px] space-y-1">
                        <a href="{{ route('leadership') }}" class="text-amber-300 hover:text-white block font-bold">← بيان القيادة الأكاديمية</a>
                        <a href="{{ route('quality') }}" class="text-emerald-300 hover:text-white block font-bold">← قسم الجودة والتميز (AQED)</a>
                    </div>
                </div>

                <!-- 3. روابط الخدمات والمناهج -->
                <div class="space-y-3">
                    <h4 class="font-black text-sm text-white flex items-center gap-2">
                        <span>🌐</span>
                        <span>الخدمات والبرامج</span>
                    </h4>
                    <ul class="space-y-1.5 text-[11px] text-sky-100">
                        <li><a href="{{ route('general_english') }}" class="text-[#F58220] hover:text-amber-200 font-bold transition">برنامج الإنجليزية العامة (Interchange) 🚀</a></li>
                        <li><a href="{{ route('curriculum') }}" class="hover:text-white transition">المناهج وخارطة مستويات CEFR</a></li>
                        <li><a href="{{ route('adult_english') }}" class="hover:text-white transition">برامج التوفل وإعداد الكبار</a></li>
                        <li><a href="{{ route('translation_services') }}" class="hover:text-white transition">خدمات الترجمة المعتمدة والشفوية</a></li>
                        <li><a href="{{ route('consultancy_services') }}" class="hover:text-white transition">الاستشارات الأكاديمية والمهنية</a></li>
                        <li><a href="{{ route('packages.index') }}" class="hover:text-white transition">باقات الحصص الفردية 25 دقيقة</a></li>
                    </ul>
                </div>

                <!-- 4. النزاهة والانضباط المالي والتواصل والتراخيص -->
                <div class="space-y-3">
                    <h4 class="font-black text-sm text-white flex items-center gap-2">
                        <span>🛡️</span>
                        <span>النزاهة والتواصل المؤسسي</span>
                    </h4>
                    <p class="text-[11px] text-sky-100 leading-relaxed">
                        نظام محاسبي معتمد (وفق المعايير المحاسبية المعتمدة) يوثق كل معاملة بسند قبض رسمي فوري. لا ادعاء بالجودة دون دليل.
                    </p>
                    <div class="pt-1 text-[11px] text-sky-100 space-y-1.5">
                        <div>📞 واتساب (1): <a href="https://wa.me/{{ $cleanWhatsapp }}?text={{ urlencode('مرحباً، أود الاستفسار عن برامج وخدمات منصة ASOTS') }}" target="_blank" class="text-amber-300 hover:text-white font-mono hover:underline inline-flex items-center gap-1" dir="ltr"><span>{{ $whatsappNumber }}</span> ↗</a></div>
                        @if(!empty($whatsappNumber2))
                            <div>📞 واتساب (2): <a href="https://wa.me/{{ $cleanWhatsapp2 }}?text={{ urlencode('مرحباً، أود الاستفسار عن برامج وخدمات منصة ASOTS') }}" target="_blank" class="text-amber-300 hover:text-white font-mono hover:underline inline-flex items-center gap-1" dir="ltr"><span>{{ $whatsappNumber2 }}</span> ↗</a></div>
                        @endif
                        <div>✉️ البريد: <a href="mailto:{{ site_setting('contact_email') ?: config('asots.contact.email') }}" class="text-white hover:underline font-mono">{{ site_setting('contact_email') ?: config('asots.contact.email') }}</a></div>
                    </div>
                    <div class="pt-2 border-t border-white/15 text-[11px] space-y-1.5">
                        <a href="{{ route('policies') }}" class="text-sky-200 hover:text-white block font-bold">← السياسات والشروط والنزاهة</a>
                        <a href="{{ route('verify_certificate') }}" class="text-emerald-300 hover:text-white block font-bold">← التحقق من صحة الشهادات الرقمية</a>
                        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->hasPermission('system.licenses_view') || auth()->user()->hasPermission('view_licenses')))
                            <a href="{{ route('licenses.viewer') }}" class="text-[#F58220] hover:text-amber-200 font-black block">📜 ← استعراض التراخيص والاعتمادات الرسمية (محمي)</a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- السطر الختامي للحقوق -->
            <div class="pt-8 border-t border-white/15 flex flex-col sm:flex-row justify-between items-center gap-3 text-sky-200 text-[11px]">
                <p>
                    {{ config('asots.name_en') }} • جميع الحقوق محفوظة © {{ date('Y') }}
                </p>
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="text-amber-400/80 font-bold">{{ config('asots.motto_ar') }}</span>
                    <span>•</span>
                    <a href="{{ route('about') }}" class="hover:text-slate-300">عن المكتب</a>
                    <span>•</span>
                    <a href="{{ route('policies') }}" class="hover:text-slate-300">السياسات والشروط</a>
                    <span>•</span>
                    <a href="{{ route('verify_certificate') }}" class="hover:text-slate-300">التحقق من الشهادات</a>
                    <span>•</span>
                    <a href="{{ route('contact') }}" class="hover:text-slate-300">اتصل بنا</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- سكريبت تشغيل القائمة المتنقلة للشاشات الصغيرة والمتوسطة -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('mobileMenuBtn');
            const menu = document.getElementById('mobileMenu');
            const hamburgerIcon = document.getElementById('hamburgerIcon');
            const closeIcon = document.getElementById('closeIcon');

            if (btn && menu) {
                btn.addEventListener('click', function () {
                    const isOpen = !menu.classList.contains('hidden');
                    menu.classList.toggle('hidden');
                    if (hamburgerIcon && closeIcon) {
                        if (isOpen) {
                            hamburgerIcon.classList.remove('hidden');
                            closeIcon.classList.add('hidden');
                        } else {
                            hamburgerIcon.classList.add('hidden');
                            closeIcon.classList.remove('hidden');
                        }
                    }
                });
            }
        });
    </script>

    <!-- زر الواتساب العائم للتواصل الفوري والمباشر مع إدارة ASOTS -->
    <div class="fixed bottom-6 left-6 z-50 flex items-center group">
        <!-- تلميح التوجيه عند التمرير -->
        <div class="hidden sm:block opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0 ml-3 bg-slate-900/95 backdrop-blur text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-xl pointer-events-none whitespace-nowrap border border-slate-700/50">
            <span>💬 تواصل مع إدارة {{ $customLogoText }} عبر الواتساب</span>
            <span class="block text-[10px] text-amber-400 font-mono mt-0.5" dir="ltr">{{ $whatsappNumber }}</span>
        </div>

        <!-- الأيقونة التفاعلية النابضة -->
        <a href="https://wa.me/{{ $cleanWhatsapp }}?text={{ urlencode('مرحباً، أود الاستفسار عن برامج وخدمات المنصة') }}"
           target="_blank"
           rel="noopener noreferrer"
           aria-label="تواصل عبر واتساب"
           class="relative flex items-center justify-center w-14 h-14 bg-[#25D366] hover:bg-[#20bd5a] text-white rounded-full shadow-2xl hover:shadow-emerald-500/50 transition-all duration-300 transform hover:scale-110 active:scale-95 focus:outline-none focus:ring-4 focus:ring-emerald-300">
            <!-- حلقة نبض ناعمة -->
            <span class="absolute inset-0 rounded-full bg-emerald-400 animate-ping opacity-30 pointer-events-none"></span>
            
            <!-- أيقونة الواتساب الرسمية SVG -->
            <svg class="w-8 h-8 fill-current relative z-10" viewBox="0 0 24 24">
                <path d="M12.031 2C6.516 2 2.025 6.49 2.025 12.008c0 1.954.561 3.782 1.534 5.334L2 22l4.825-1.524A9.957 9.957 0 0012.031 22c5.516 0 10.007-4.49 10.007-10.008C22.038 6.49 17.547 2 12.031 2zm0 18.232c-1.688 0-3.26-.47-4.606-1.284l-.33-.198-2.868.905.918-2.8-.216-.344a8.17 8.17 0 01-1.258-4.503c0-4.524 3.68-8.204 8.204-8.204 4.524 0 8.204 3.68 8.204 8.204 0 4.524-3.68 8.204-8.204 8.204zm4.508-6.155c-.247-.123-1.463-.722-1.69-.804-.226-.083-.391-.123-.556.123-.165.247-.64 1.05-.784 1.215-.144.165-.288.185-.535.062-.247-.123-1.043-.385-1.986-1.226-.734-.655-1.23-1.464-1.374-1.711-.144-.247-.015-.38.109-.503.111-.111.247-.288.371-.432.123-.144.165-.247.247-.412.082-.165.041-.309-.021-.432-.062-.123-.556-1.338-.762-1.832-.2-.482-.403-.417-.556-.425l-.473-.008c-.165 0-.432.062-.658.309-.226.247-.864.844-.864 2.058s.885 2.388 1.008 2.553c.123.165 1.742 2.66 4.22 3.731.59.255 1.05.407 1.41.522.593.188 1.133.161 1.56.098.477-.071 1.463-.598 1.669-1.174.206-.576.206-1.07.144-1.174-.062-.103-.227-.165-.474-.288z"/>
            </svg>
        </a>
    </div>

    <!-- ودجت المساعد الذكي والشات اللحظي المتصل بقاعدة البيانات -->
    @include('components.smart-chat-widget')
</body>
</html>
