@extends('layouts.app', ['title' => 'إدارة الهوية البصرية وخلفيات وشعارات الموقع'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto my-4">

    <!-- شريط الترويسة الرئيسي -->
    <div class="bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1] rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border border-white/20">
        <div>
            <div class="inline-flex items-center gap-2 bg-[#F58220]/20 text-[#F58220] text-xs px-3.5 py-1 rounded-full font-bold border border-[#F58220]/30 mb-2">
                <span>🎨</span>
                <span>تخصيص الهوية البصرية • Visual Identity & Backgrounds</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">إدارة خلفيات وشعارات ونصوص المنصة</h1>
            <p class="text-xs sm:text-sm text-sky-100 mt-1 max-w-2xl leading-relaxed">
                تحكم كامل في خلفية الموقع (إضافة صور تعبر عن المنصة أو إزالتها والعودة للشكل الطبيعي)، تخصيص شعار المنصة، وتعديل النصوص والشعارات الترويجية بسهولة.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" target="_blank" class="px-5 py-3 rounded-2xl bg-[#F58220] hover:bg-[#d96e14] text-white font-black text-xs sm:text-sm shadow-lg transition flex items-center gap-2">
                <span>👁️</span>
                <span>معاينة الموقع الآن ↗</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/10">
                ← لوحة الإدارة
            </a>
        </div>
    </div>

    <!-- رسائل النجاح والتنبيهات -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-lg">✅</span>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-bold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-lg">⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
    @endif

    @php
        $currentBg = $settings['site_background'] ?? null;
        $isBgEnabled = ($settings['site_background_enabled'] ?? '0') === '1';
        $currentBgMode = $settings['background_mode'] ?? 'hero';
        $currentOverlay = $settings['overlay_opacity'] ?? '0.75';
        $currentBlur = $settings['background_blur'] ?? 'none';
        $currentLogo = $settings['site_logo'] ?? null;
        $currentLogoText = $settings['logo_text'] ?? 'ASOTS';
        $splashEnabled = ($settings['splash_enabled'] ?? '1') === '1';
        $splashTitleAr = $settings['splash_title_ar'] ?? 'مرحباً بكم في منصة مكتب النجم الآسيوي (ASOTS)';
        $splashSubtitleAr = $settings['splash_subtitle_ar'] ?? config('asots.promise_ar');
        $splashDuration = $settings['splash_duration'] ?? '3';
        $splashBlur = $settings['splash_blur'] ?? '2xl';
        $splashOpacityLight = $settings['splash_opacity_light'] ?? '0.18';
        $splashOpacityDark = $settings['splash_opacity_dark'] ?? '0.12';
        $currentFont = $settings['site_font_family'] ?? 'Cairo';
        $currentFontColor = $settings['site_font_color'] ?? '';
        $currentFontColorMuted = $settings['site_font_color_muted'] ?? '';
        $currentHeadingColor = $settings['site_heading_color'] ?? '';
        $currentFontSize = $settings['site_font_size'] ?? '16px';
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- العمود الأيمن (2 أعمدة): إدارة الخلفية وإدارة الشعار -->
        <div class="lg:col-span-2 space-y-8">

            <!-- ==================== 1. بطاقة خلفية الموقع ==================== -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span>🖼️</span>
                            <span>خلفية الموقع (Site Background)</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            يمكنك رفع صورة خلفية تعبر عن نشاط المنصة، أو اختيار صورة جاهزة، أو حذفها والعودة للشكل الطبيعي في أي وقت.
                        </p>
                    </div>

                    <!-- شارة الحالة الحالية للخلفية -->
                    <div>
                        @if($isBgEnabled && $currentBg)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>خلفية مخصصة مفعلة</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                <span>🌿 الشكل الطبيعي الافتراضي (بدون صورة)</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- معاينة الخلفية الحالية مع خيار الحذف السريع -->
                @if($isBgEnabled && $currentBg)
                    <div class="relative rounded-2xl overflow-hidden border border-slate-200 shadow-inner group">
                        @php
                            $fullBgUrl = str_starts_with($currentBg, 'http') ? $currentBg : asset($currentBg);
                        @endphp
                        <div class="h-44 w-full bg-cover bg-center" style="background-image: linear-gradient(rgba(15, 23, 42, {{ $currentOverlay }}), rgba(15, 23, 42, {{ $currentOverlay }})), url('{{ $fullBgUrl }}');">
                            <div class="h-full flex flex-col justify-between p-4 text-white">
                                <div class="flex justify-between items-start">
                                    <span class="text-[11px] font-bold bg-black/60 px-2.5 py-1 rounded-lg backdrop-blur">
                                        تطبيق الخلفية على: 
                                        <strong>
                                            {{ $currentBgMode === 'body' ? 'خلفية الموقع بالكامل' : ($currentBgMode === 'all_pages' ? 'كافة صفحات المنصة' : 'قسم الترويسة Hero فقط') }}
                                        </strong>
                                    </span>
                                    <span class="text-[11px] font-bold bg-black/60 px-2.5 py-1 rounded-lg backdrop-blur">
                                        التعتيم: {{ round((float)$currentOverlay * 100) }}%
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-amber-300 font-bold block">معاينة مباشرة لشكل التعتيم والنصوص فوق الخلفية</span>
                                    <h4 class="text-base font-black text-white">منصة ASOTS للتعليم التفاعلي والاستشارات</h4>
                                </div>
                            </div>
                        </div>

                        <!-- زر حذف الخلفية والعودة للشكل الافتراضي -->
                        <div class="p-3 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                            <span class="text-xs text-slate-600 font-medium">الخلفية مفعلة حالياً على واجهة الموقع.</span>
                            <form method="POST" action="{{ route('admin.branding.background.remove') }}" onsubmit="return confirm('هل تريد بالتأكيد إزالة صورة الخلفية والعودة إلى الشكل الطبيعي الافتراضي للقالب؟');">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                                    <span>✕</span>
                                    <span>حذف الخلفية والعودة للشكل الطبيعي الافتراضي</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- نموذج رفع صورة خلفية جديدة أو اختيار من الجاهز -->
                <form action="{{ route('admin.branding.background.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- 1. رفع صورة من الجهاز -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">
                            1. ارفع صورة خلفية جديدة من جهازك (JPG, PNG, WebP - حد أقصى 10MB):
                        </label>
                        <div class="border-2 border-dashed border-slate-300 hover:border-indigo-500 rounded-2xl p-6 text-center transition bg-slate-50/50">
                            <input type="file" name="background_file" id="bgFileInput" accept="image/*" class="hidden" onchange="previewUploadedBg(event)">
                            <label for="bgFileInput" class="cursor-pointer flex flex-col items-center gap-2">
                                <span class="text-3xl">📤</span>
                                <span class="text-xs font-bold text-indigo-600 hover:underline">اضغط هنا لاختيار صورة من جهازك</span>
                                <span id="bgFileName" class="text-[11px] text-slate-400">يفضل اختيار صور عالية الدقة (1920x1080 فأعلى) بتصميم أكاديمي هادئ</span>
                            </label>
                        </div>
                    </div>

                    <!-- 2. أو اختيار صورة جاهزة من مكتبة المنصة -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">
                            2. أو اختر صورة جاهزة ومعتمدة تعبر عن المنصة بنقرة واحدة:
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($presets as $p)
                                <label class="relative rounded-xl overflow-hidden border-2 cursor-pointer transition block group {{ ($currentBg === $p['url']) ? 'border-amber-500 ring-2 ring-amber-400' : 'border-slate-200 hover:border-indigo-400' }}">
                                    <input type="radio" name="preset_url" value="{{ $p['url'] }}" class="hidden preset-radio" {{ ($currentBg === $p['url']) ? 'checked' : '' }} onchange="selectPreset(this)">
                                    <img src="{{ $p['thumb'] }}" alt="{{ $p['name'] }}" class="w-full h-24 object-cover group-hover:scale-105 transition duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex items-end p-2">
                                        <span class="text-[10px] font-bold text-white leading-tight drop-shadow">{{ $p['name'] }}</span>
                                    </div>
                                    <div class="preset-check absolute top-1.5 right-1.5 bg-amber-500 text-slate-950 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black {{ ($currentBg === $p['url']) ? '' : 'hidden' }}">
                                        ✓
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 3. خيارات نطاق العرض والتعتيم لضمان وضوح النصوص -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs">
                        
                        <!-- نطاق تطبيق الخلفية -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-slate-700">تطبيق الخلفية على:</label>
                            <select name="background_mode" class="w-full p-2.5 rounded-xl border border-slate-300 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                                <option value="hero" {{ $currentBgMode === 'hero' ? 'selected' : '' }}>قسم الترويسة الرئيسي Hero فقط</option>
                                <option value="body" {{ $currentBgMode === 'body' ? 'selected' : '' }}>خلفية الموقع بالكامل (الصفحة الرئيسية)</option>
                                <option value="all_pages" {{ $currentBgMode === 'all_pages' ? 'selected' : '' }}>جميع صفحات الموقع والمنصة</option>
                            </select>
                        </div>

                        <!-- درجة تعتيم الشفافية الداكنة -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-slate-700">درجة التعتيم (لوضوح الخط):</label>
                            <select name="overlay_opacity" class="w-full p-2.5 rounded-xl border border-slate-300 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                                <option value="0.4" {{ $currentOverlay == '0.4' ? 'selected' : '' }}>40% تعتيم خفيف</option>
                                <option value="0.6" {{ $currentOverlay == '0.6' ? 'selected' : '' }}>60% تعتيم متوازن</option>
                                <option value="0.75" {{ $currentOverlay == '0.75' || !$currentOverlay ? 'selected' : '' }}>75% تعتيم داكن (موصى به للنصوص البيضاء)</option>
                                <option value="0.85" {{ $currentOverlay == '0.85' ? 'selected' : '' }}>85% تعتيم عميق وهادئ</option>
                            </select>
                        </div>

                        <!-- تأثير التمويه -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-slate-700">تأثير التمويه والنعومة:</label>
                            <select name="background_blur" class="w-full p-2.5 rounded-xl border border-slate-300 bg-white font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                                <option value="none" {{ $currentBlur === 'none' ? 'selected' : '' }}>بدون تمويه (صورة نقية حادة)</option>
                                <option value="sm" {{ $currentBlur === 'sm' ? 'selected' : '' }}>تمويه ناعم خفيف (Subtle Blur)</option>
                                <option value="md" {{ $currentBlur === 'md' ? 'selected' : '' }}>تمويه متوسط لتركيز النصوص</option>
                            </select>
                        </div>

                    </div>

                    <!-- زر الحفظ والتطبيق -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="submit" class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                            <span>🎨</span>
                            <span>حفظ وتطبيق خلفية الموقع الآن</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ==================== 2. بطاقة شعار المنصة (Site Logo) ==================== -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span>⭐</span>
                            <span>شعار المنصة (Platform Logo & Slogan)</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            يمكنك رفع شعار مخصص للمنصة بصيغة PNG أو SVG، أو حذفه لاستعادة الشعار المؤسسي الافتراضي المعتمد.
                        </p>
                    </div>

                    <!-- شارة حالة الشعار -->
                    <div>
                        @if($currentLogo)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                <span>⭐ شعار مخصص مرفوع</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                <span>⭐ الشعار المؤسسي الافتراضي (ASOTS)</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- استعراض الشعار الحالي وخيار الحذف -->
                <div class="p-4 bg-slate-900 text-white rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-400 font-bold">الشعار الحالي في شريط التنقل:</span>
                        <div class="flex items-center gap-2 bg-slate-800/80 px-4 py-2 rounded-xl border border-slate-700">
                            @if($currentLogo)
                                <img src="{{ asset($currentLogo) }}" alt="Logo" class="h-9 object-contain">
                            @else
                                <div class="w-8 h-8 bg-gradient-to-tr from-amber-500 to-indigo-600 rounded-xl flex items-center justify-center text-base shadow-md">
                                    ⭐
                                </div>
                            @endif
                            <span class="font-black text-lg text-amber-400 tracking-wider">{{ $currentLogoText }}</span>
                        </div>
                    </div>


                </div>

                <!-- نموذج رفع الشعار الجديد -->
                <form action="{{ route('admin.branding.logo.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                ملف الشعار (PNG بخلفية شفافة أو SVG أو JPG - حد أقصى 5MB):
                            </label>
                            <input type="file" name="logo_file" required accept="image/*" class="w-full text-xs p-2 rounded-xl border border-slate-300 bg-white">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                اسم أو رمز الشعار المكتوب بجانبه (مثال: ASOTS):
                            </label>
                            <input type="text" name="logo_text" value="{{ $currentLogoText }}" placeholder="ASOTS" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                            <span>⭐</span>
                            <span>رفع وتطبيق الشعار الجديد</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ==================== 2.3 بطاقة إعدادات الخطوط والطباعة وألوان وحجم الخط للموقع (Site Typography) ==================== -->
            <div id="typography-section" class="bg-white rounded-3xl border-2 border-indigo-200 shadow-sm p-6 sm:p-8 space-y-6 scroll-mt-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200 mb-1">
                            <span>✍️</span>
                            <span>إعدادات النظام والطباعة • Typography & Global Font Settings</span>
                        </div>
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span>🎨</span>
                            <span>نوع الخط وألوان الخط والحجم الافتراضي للموقع</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                            تحكم كامل في نوع الخط المعتمد للموقع بالكامل، ألوان النصوص والعناوين، والحجم الافتراضي للخط لجميع الزوار والطلاب مع معاينة حية مباشرة.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                            الخط الحالي: <strong>{{ $currentFont }}</strong>
                        </span>
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                            الحجم: <strong>{{ $currentFontSize }}</strong>
                        </span>
                        <form action="{{ route('admin.branding.typography.reset') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من استعادة نوع الخط ولون وحجم الخط الافتراضي الأصلي للمنصة؟');">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-slate-600 hover:text-red-600 bg-slate-100 hover:bg-red-50 border border-slate-200 px-3 py-1.5 rounded-xl transition flex items-center gap-1 shadow-2xs" title="استعادة الإعدادات الافتراضية للخطوط">
                                <span>🔄</span>
                                <span>استعادة الافتراضي</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- صندوق المعاينة الحية المباشرة (Live Interactive Preview Box) -->
                <div class="rounded-2xl border-2 border-indigo-100 bg-gradient-to-br from-slate-50 to-indigo-50/30 p-5 space-y-3 transition-all" id="typographyLivePreviewCard">
                    <div class="flex items-center justify-between border-b border-indigo-100 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-indigo-900">معاينة تفاعلية حية لشكل الخط والألوان والحجم (Live Typography Preview)</span>
                        </div>
                        <div class="flex items-center gap-2 text-[11px] font-mono text-slate-500">
                            <span id="previewFontBadge" class="bg-white px-2 py-0.5 rounded-md border border-slate-200 font-bold text-slate-700">{{ $currentFont }}</span>
                            <span id="previewSizeBadge" class="bg-white px-2 py-0.5 rounded-md border border-slate-200 font-bold text-slate-700">{{ $currentFontSize }}</span>
                            <span id="previewColorBadge" class="bg-white px-2 py-0.5 rounded-md border border-slate-200 font-bold text-slate-700">{{ $currentFontColor ?: '#0f172a' }}</span>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs space-y-2.5 transition-all" id="previewSampleContent" style="font-family: '{{ $currentFont }}', sans-serif;">
                        <h3 id="previewHeading" class="text-lg font-black transition-colors" style="color: {{ $currentHeadingColor ?: '#003859' }};">
                            مكتب النجم الآسيوي للخدمات التعليمية والاستشارية (ASOTS)
                        </h3>
                        <p id="previewBody" class="leading-relaxed transition-all" style="color: {{ $currentFontColor ?: '#0f172a' }}; font-size: {{ $currentFontSize }};">
                            نظام تعليمي واستشاري متكامل يوفر برامج تدريبية متطورة، اختبارات تحديد مستوى، وجلسات تفاعلية حية وفق أعلى المعايير الأكاديمية الدولية.
                        </p>
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span id="previewMuted" class="text-[11px] font-bold" style="color: {{ $currentFontColorMuted ?: '#475569' }};">
                                📅 التاريخ الأكاديمي: الفصل الدراسي الجديد • الاعتماد الدولي الموثق
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-[11px] border border-indigo-200">
                                <span>⚡</span>
                                <span>نموذج زر تفاعلي</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- نموذج حفظ وتطبيق الخطوط والألوان والحجم -->
                <form action="{{ route('admin.branding.typography.update') }}" method="POST" class="space-y-6" id="typographyForm">
                    @csrf

                    <!-- 1. اختيار نوع الخط العربي (8 خطوط معتمدة) -->
                    <div class="space-y-3">
                        <label class="block text-xs font-black text-slate-800 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <span>✍️</span>
                                <span>1. نوع الخط الافتراضي للموقع بالكامل (Site Font Family):</span>
                            </span>
                            <span class="text-[11px] text-slate-400 font-normal">اضغط على أي خط لاختياره ومعاينته فورياً</span>
                        </label>

                        @php
                            $availableFonts = [
                                'Cairo' => [
                                    'name_ar' => 'خط القاهرة (Cairo)',
                                    'name_en' => 'Cairo',
                                    'desc' => 'الخط الافتراضي الرصين، متوازن وواضح جداً في القراءة والعناوين الأكاديمية.',
                                    'family' => "'Cairo', sans-serif",
                                    'badge' => 'الافتراضي المعتمد ⭐',
                                ],
                                'Almarai' => [
                                    'name_ar' => 'خط المراعي (Almarai)',
                                    'name_en' => 'Almarai',
                                    'desc' => 'خط هندسي عصري فائق النقاء والأناقة ومناسب للمنصات الرقمية الحديثة.',
                                    'family' => "'Almarai', sans-serif",
                                    'badge' => 'عصري وأنيق ✨',
                                ],
                                'Tajawal' => [
                                    'name_ar' => 'خط تجوال (Tajawal)',
                                    'name_en' => 'Tajawal',
                                    'desc' => 'خط ناعم وانسيابي مريح جداً للعين أثناء جلسات الدراسة والقراءة الطويلة.',
                                    'family' => "'Tajawal', sans-serif",
                                    'badge' => 'ناعم ومريح 👁️',
                                ],
                                'IBM Plex Sans Arabic' => [
                                    'name_ar' => 'خط آي بي إم بلكس (IBM Plex)',
                                    'name_en' => 'IBM Plex Sans Arabic',
                                    'desc' => 'طابع تقني ومؤسسي عالي الدقة يعكس الاحترافية والانضباط الأكاديمي.',
                                    'family' => "'IBM Plex Sans Arabic', sans-serif",
                                    'badge' => 'مؤسسي تقني 💼',
                                ],
                                'Readex Pro' => [
                                    'name_ar' => 'خط ريدكس برو (Readex Pro)',
                                    'name_en' => 'Readex Pro',
                                    'desc' => 'خط حداثي فائق الوضوح ومقروء بسلاسة تامة على مختلف أحجام الشاشات.',
                                    'family' => "'Readex Pro', sans-serif",
                                    'badge' => 'حداثي واضح 🚀',
                                ],
                                'Amiri' => [
                                    'name_ar' => 'الخط الأميري (Amiri)',
                                    'name_en' => 'Amiri',
                                    'desc' => 'طابع عربي كلاسيكي عريق بنمط خط النسخ التقليدي الرصين والموثوق.',
                                    'family' => "'Amiri', serif",
                                    'badge' => 'نسخ أصيل 📜',
                                ],
                                'Noto Kufi Arabic' => [
                                    'name_ar' => 'خط نوتو كوفي (Noto Kufi)',
                                    'name_en' => 'Noto Kufi Arabic',
                                    'desc' => 'طابع كوفي هندسي متقن يجمع بين الأصالة العربية والتصميم الرقمي.',
                                    'family' => "'Noto Kufi Arabic', sans-serif",
                                    'badge' => 'كوفي هندسي 🏛️',
                                ],
                                'Changa' => [
                                    'name_ar' => 'خط تشانجا (Changa)',
                                    'name_en' => 'Changa',
                                    'desc' => 'خط عريض وجريء يمنح العناوين والواجهات حضوراً حيوياً ومميزاً.',
                                    'family' => "'Changa', sans-serif",
                                    'badge' => 'جريء ومبهج 🎯',
                                ],
                            ];
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            @foreach($availableFonts as $fontKey => $fontData)
                                @php $isCurrent = ($currentFont === $fontKey); @endphp
                                <label class="relative flex flex-col justify-between p-4 rounded-2xl border-2 cursor-pointer transition-all font-card select-none {{ $isCurrent ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-500/20 shadow-sm' : 'border-slate-200 hover:border-indigo-300 hover:bg-slate-50' }}">
                                    <input type="radio" name="site_font_family" value="{{ $fontKey }}" class="sr-only font-radio" {{ $isCurrent ? 'checked' : '' }} onchange="updateLiveFont('{{ $fontKey }}', '{{ $fontData['family'] }}')">
                                    
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-xs font-black text-slate-900 font-title">{{ $fontData['name_ar'] }}</span>
                                            <span class="font-check text-xs font-black text-indigo-600 {{ $isCurrent ? '' : 'hidden' }}">✓</span>
                                        </div>

                                        <div class="p-2.5 my-2 bg-white rounded-xl border border-slate-200 text-sm font-bold text-slate-800 text-center leading-relaxed" style="font-family: {{ $fontData['family'] }};">
                                            أبجد هوز حطي كلمن • ASOTS
                                        </div>

                                        <p class="text-[11px] text-slate-500 leading-relaxed">{{ $fontData['desc'] }}</p>
                                    </div>

                                    <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px]">
                                        <span class="font-bold text-slate-400 font-mono">{{ $fontData['name_en'] }}</span>
                                        <span class="font-black text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200">{{ $fontData['badge'] }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. ألوان الخطوط والنصوص والعناوين -->
                    <div class="space-y-3 pt-2">
                        <label class="block text-xs font-black text-slate-800 flex items-center gap-1.5">
                            <span>🎨</span>
                            <span>2. ألوان خطوط ونصوص وعناوين الموقع (Site Font Colors):</span>
                        </label>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- لون الخط الأساسي للمحتوى -->
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/60 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-800">لون النصوص الأساسية (Body Text)</span>
                                    <span class="text-[10px] text-slate-400">الافتراضي: #0f172a</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="siteFontColorPicker" value="{{ $currentFontColor ?: '#0f172a' }}" class="w-10 h-10 rounded-xl border border-slate-300 p-0.5 cursor-pointer shrink-0" oninput="syncColorInput('siteFontColorHex', this.value); updateLiveColors();">
                                    <input type="text" name="site_font_color" id="siteFontColorHex" value="{{ $currentFontColor }}" placeholder="#0f172a" class="w-full text-xs font-mono font-bold p-2.5 rounded-xl border border-slate-300 bg-white" oninput="syncColorPicker('siteFontColorPicker', this.value); updateLiveColors();">
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap pt-1">
                                    <span class="text-[10px] text-slate-400 font-bold">خيارات سريعة:</span>
                                    <button type="button" onclick="setQuickColor('siteFontColor', '#0f172a')" class="w-5 h-5 rounded-md border border-black/20 shadow-2xs" style="background:#0f172a;" title="كحلي فحمي داكن (الافتراضي)"></button>
                                    <button type="button" onclick="setQuickColor('siteFontColor', '#000000')" class="w-5 h-5 rounded-md border border-black/20 shadow-2xs" style="background:#000000;" title="أسود ملكي فاحم"></button>
                                    <button type="button" onclick="setQuickColor('siteFontColor', '#003859')" class="w-5 h-5 rounded-md border border-black/20 shadow-2xs" style="background:#003859;" title="كحلي بحري مؤسسي ASOTS"></button>
                                    <button type="button" onclick="setQuickColor('siteFontColor', '#1e293b')" class="w-5 h-5 rounded-md border border-black/20 shadow-2xs" style="background:#1e293b;" title="رمادي داكن أنيق"></button>
                                    <button type="button" onclick="setQuickColor('siteFontColor', '#292524')" class="w-5 h-5 rounded-md border border-black/20 shadow-2xs" style="background:#292524;" title="بني دافئ داكن"></button>
                                </div>
                            </div>

                            <!-- لون النصوص الثانوية والفرعية -->
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/60 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-800">النصوص الفرعية (Muted Text)</span>
                                    <span class="text-[10px] text-slate-400">الافتراضي: #475569</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="siteFontColorMutedPicker" value="{{ $currentFontColorMuted ?: '#475569' }}" class="w-10 h-10 rounded-xl border border-slate-300 p-0.5 cursor-pointer shrink-0" oninput="syncColorInput('siteFontColorMutedHex', this.value); updateLiveColors();">
                                    <input type="text" name="site_font_color_muted" id="siteFontColorMutedHex" value="{{ $currentFontColorMuted }}" placeholder="#475569" class="w-full text-xs font-mono font-bold p-2.5 rounded-xl border border-slate-300 bg-white" oninput="syncColorPicker('siteFontColorMutedPicker', this.value); updateLiveColors();">
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap pt-1">
                                    <span class="text-[10px] text-slate-400 font-bold">خيارات سريعة:</span>
                                    <button type="button" onclick="setQuickColor('siteFontColorMuted', '#475569')" class="w-5 h-5 rounded-md border border-black/20 shadow-2xs" style="background:#475569;" title="رمادي أردوازي (الافتراضي)"></button>
                                    <button type="button" onclick="setQuickColor('siteFontColorMuted', '#334155')" class="w-5 h-5 rounded-md border border-black/20 shadow-2xs" style="background:#334155;" title="رمادي داكن"></button>
                                    <button type="button" onclick="setQuickColor('siteFontColorMuted', '#64748b')" class="w-5 h-5 rounded-md border border-black/20 shadow-2xs" style="background:#64748b;" title="رمادي متوسط"></button>
                                    <button type="button" onclick="setQuickColor('siteFontColorMuted', '#52525b')" class="w-5 h-5 rounded-md border border-black/20 shadow-2xs" style="background:#52525b;" title="زنك متوازن"></button>
                                </div>
                            </div>

                            <!-- لون العناوين الرئيسية -->
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/60 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-800">لون العناوين (Headings Color)</span>
                                    <span class="text-[10px] text-slate-400">الافتراضي: #003859</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="color" id="siteHeadingColorPicker" value="{{ $currentHeadingColor ?: '#003859' }}" class="w-10 h-10 rounded-xl border border-slate-300 p-0.5 cursor-pointer shrink-0" oninput="syncColorInput('siteHeadingColorHex', this.value); updateLiveColors();">
                                    <input type="text" name="site_heading_color" id="siteHeadingColorHex" value="{{ $currentHeadingColor }}" placeholder="#003859" class="w-full text-xs font-mono font-bold p-2.5 rounded-xl border border-slate-300 bg-white" oninput="syncColorPicker('siteHeadingColorPicker', this.value); updateLiveColors();">
                                </div>
                                <div class="flex items-center gap-1.5 flex-wrap pt-1">
                                    <span class="text-[10px] text-slate-400 font-bold">خيارات سريعة:</span>
                                    <button type="button" onclick="setQuickColor('siteHeadingColor', '#003859')" class="w-5 h-5 rounded-md border border-black/20 shadow-2xs" style="background:#003859;" title="كحلي المنصة الرسمي (الافتراضي)"></button>
                                    <button type="button" onclick="setQuickColor('siteHeadingColor', '#0066A1')" class="w-5 h-5 rounded-md border border-black/20 shadow-2xs" style="background:#0066A1;" title="أزرق محيطي ASOTS"></button>
                                    <button type="button" onclick="setQuickColor('siteHeadingColor', '#F58220')" class="w-5 h-5 rounded-md border border-black/20 shadow-2xs" style="background:#F58220;" title="برتقالي الهوية النشط"></button>
                                    <button type="button" onclick="setQuickColor('siteHeadingColor', '#0f172a')" class="w-5 h-5 rounded-md border border-black/20 shadow-2xs" style="background:#0f172a;" title="فحمي داكن"></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. الحجم الافتراضي للخط (Base Font Size) -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-black text-slate-800 flex items-center gap-1.5">
                                <span>📏</span>
                                <span>3. الحجم الافتراضي للخط للموقع (Default Base Font Size):</span>
                            </label>
                            <span class="text-xs font-black text-indigo-700 bg-indigo-50 border border-indigo-200 px-3 py-1 rounded-xl" id="fontSizeDisplay">
                                {{ $currentFontSize }}
                            </span>
                        </div>

                        <!-- شريط التمرير التفاعلي لحجم الخط -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/60 space-y-3">
                            <input type="range" id="fontSizeRange" min="14" max="20" step="1" value="{{ (int) str_replace('px', '', $currentFontSize) }}" class="w-full accent-indigo-600 cursor-pointer" oninput="updateFontSizeFromRange(this.value)">

                            <!-- حبات الاختيار المباشر لأحجام الخطوط -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2 pt-1">
                                @php
                                    $sizePresets = [
                                        '14px' => ['label' => '14px', 'desc' => 'صغير ومضغوط (87.5%)'],
                                        '15px' => ['label' => '15px', 'desc' => 'متوسط مصغر (93.75%)'],
                                        '16px' => ['label' => '16px', 'desc' => 'الافتراضي القياسي (100%) ⭐'],
                                        '17px' => ['label' => '17px', 'desc' => 'مريح ومقروء (106.2%)'],
                                        '18px' => ['label' => '18px', 'desc' => 'كبير وواضح (112.5%)'],
                                        '19px' => ['label' => '19px', 'desc' => 'كبير جداً (118.7%)'],
                                        '20px' => ['label' => '20px', 'desc' => 'فائق الوضوح (125%)'],
                                    ];
                                @endphp
                                @foreach($sizePresets as $sizeKey => $sizeData)
                                    @php $isSizeActive = ($currentFontSize === $sizeKey); @endphp
                                    <label class="p-2.5 rounded-xl border text-center cursor-pointer transition-all size-preset-card select-none {{ $isSizeActive ? 'border-indigo-600 bg-indigo-50 text-indigo-900 ring-2 ring-indigo-500/20 font-black' : 'border-slate-200 bg-white hover:border-indigo-300 text-slate-700 font-bold' }}">
                                        <input type="radio" name="site_font_size" value="{{ $sizeKey }}" class="sr-only size-radio" {{ $isSizeActive ? 'checked' : '' }} onchange="selectFontSizePreset('{{ $sizeKey }}')">
                                        <span class="block text-xs font-black">{{ $sizeData['label'] }}</span>
                                        <span class="block text-[9px] text-slate-400 mt-0.5 truncate" title="{{ $sizeData['desc'] }}">{{ $sizeData['desc'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الإجراء والحفظ -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md shadow-indigo-200 transition flex items-center justify-center gap-2">
                            <span>💾</span>
                            <span>حفظ وتطبيق إعدادات الخط والألوان للموقع بالكامل ⚡</span>
                        </button>
                        <span class="text-[11px] text-slate-400">
                            يتم تطبيق الإعدادات فوراً على كافة صفحات المنصة وحسابات الطلاب والمعلمين والزوار.
                        </span>
                    </div>
                </form>
            </div>

            <!-- ==================== 2.5 بطاقة شاشة الترحيب الأولى وشعار المعهد الضبابي (Splash Screen) ==================== -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-amber-50 text-[#F58220] border border-amber-200 mb-1">
                            <span>✨</span>
                            <span>شاشة الدخول الأولى التفاعلية (First Welcome Screen)</span>
                        </div>
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span>🌟</span>
                            <span>شاشة الترحيب الأولى والشعار الضبابي</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                            تخصيص شاشة الترحيب التي تظهر للزائر عند فتح المنصة، مع شعار المعهد الأصلي كخلفية ضبابية ناعمة في الوضعين النهاري والليلي، والتحكم في درجة الضبابية، التعتيم، والمدة الزمنية.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="previewSplashScreen()" class="px-3.5 py-1.5 rounded-xl bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                            <span>👁️</span>
                            <span>معاينة الشاشة الآن</span>
                        </button>
                    </div>
                </div>

                <form action="{{ route('admin.branding.splash.update') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- تفعيل / تعطيل الشاشة -->
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <div>
                            <strong class="text-xs font-black text-slate-900 block">تفعيل شاشة الترحيب الأولى</strong>
                            <span class="text-[11px] text-slate-500">تظهر الشاشة الترحيبية تلقائياً عند فتح المنصة لأول مرة ثم تنتقل بسلاسة للواجهة.</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-bold text-slate-700">
                                <input type="radio" name="splash_enabled" value="1" {{ $splashEnabled ? 'checked' : '' }} class="text-[#0066A1]">
                                <span>مفعلة ✅</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-bold text-slate-700">
                                <input type="radio" name="splash_enabled" value="0" {{ ! $splashEnabled ? 'checked' : '' }} class="text-rose-600">
                                <span>معطلة ❌</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- العنوان الترحيبي -->
                        <div class="space-y-1.5 sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700">
                                العنوان الترحيبي الرئيسي (يظهر بالخط الديواني الأنيق):
                            </label>
                            <input type="text" name="splash_title_ar" value="{{ $splashTitleAr }}" placeholder="مرحباً بكم في منصة مكتب النجم الآسيوي (ASOTS)" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        </div>

                        <!-- العبارة الترحيبية والتحفيزية -->
                        <div class="space-y-1.5 sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700">
                                العبارة الترحيبية والتحفيزية الفرعية:
                            </label>
                            <input type="text" name="splash_subtitle_ar" value="{{ $splashSubtitleAr }}" placeholder="{{ config('asots.promise_ar') }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        </div>

                        <!-- مدة العرض بالثواني -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                مدة العرض التلقائي قبل الانتقال (بالثواني):
                            </label>
                            <select name="splash_duration" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold bg-white">
                                <option value="2" {{ $splashDuration == '2' ? 'selected' : '' }}>2 ثواني (سريع)</option>
                                <option value="2.5" {{ $splashDuration == '2.5' ? 'selected' : '' }}>2.5 ثانية (مثالي)</option>
                                <option value="3" {{ $splashDuration == '3' ? 'selected' : '' }}>3 ثواني (افتراضي متوازن)</option>
                                <option value="4" {{ $splashDuration == '4' ? 'selected' : '' }}>4 ثواني (مطول)</option>
                                <option value="5" {{ $splashDuration == '5' ? 'selected' : '' }}>5 ثواني</option>
                            </select>
                        </div>

                        <!-- درجة الضبابية للخلفية -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                درجة ضبابية الشعار في الخلفية (Blur Level):
                            </label>
                            <select name="splash_blur" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold bg-white">
                                <option value="sm" {{ $splashBlur == 'sm' ? 'selected' : '' }}>خفيف جداً (sm - 4px)</option>
                                <option value="md" {{ $splashBlur == 'md' ? 'selected' : '' }}>متوسط (md - 8px)</option>
                                <option value="lg" {{ $splashBlur == 'lg' ? 'selected' : '' }}>واضح (lg - 16px)</option>
                                <option value="xl" {{ $splashBlur == 'xl' ? 'selected' : '' }}>قوي (xl - 24px)</option>
                                <option value="2xl" {{ $splashBlur == '2xl' ? 'selected' : '' }}>ضبابي ناعم أنيق (2xl - 40px - مستحسن)</option>
                                <option value="3xl" {{ $splashBlur == '3xl' ? 'selected' : '' }}>فائق النعومة (3xl - 64px)</option>
                            </select>
                        </div>

                        <!-- شفافية الشعار في الوضع النهاري -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                شفافية الشعار في الوضع النهاري (مضيء وناعم):
                            </label>
                            <select name="splash_opacity_light" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold bg-white">
                                <option value="0.10" {{ $splashOpacityLight == '0.10' ? 'selected' : '' }}>10% (شفاف وهادئ جداً)</option>
                                <option value="0.15" {{ $splashOpacityLight == '0.15' ? 'selected' : '' }}>15%</option>
                                <option value="0.18" {{ $splashOpacityLight == '0.18' ? 'selected' : '' }}>18% (المعتمد المتناسق)</option>
                                <option value="0.25" {{ $splashOpacityLight == '0.25' ? 'selected' : '' }}>25% (أكثر وضوحاً)</option>
                                <option value="0.35" {{ $splashOpacityLight == '0.35' ? 'selected' : '' }}>35%</option>
                            </select>
                        </div>

                        <!-- شفافية الشعار في الوضع الليلي -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                شفافية الشعار في الوضع الليلي (أغمق وأنيق):
                            </label>
                            <select name="splash_opacity_dark" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold bg-white">
                                <option value="0.08" {{ $splashOpacityDark == '0.08' ? 'selected' : '' }}>8% (داكن ومميز)</option>
                                <option value="0.12" {{ $splashOpacityDark == '0.12' ? 'selected' : '' }}>12% (المعتمد المتناسق)</option>
                                <option value="0.16" {{ $splashOpacityDark == '0.16' ? 'selected' : '' }}>16%</option>
                                <option value="0.22" {{ $splashOpacityDark == '0.22' ? 'selected' : '' }}>22%</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 bg-amber-50 rounded-2xl border border-amber-200 text-amber-900 text-xs leading-relaxed flex items-start gap-2.5">
                        <span class="text-base">💡</span>
                        <div>
                            <strong>قاعدة الهوية المؤسسية المعتمدة:</strong>
                            يستخدم شعار المعهد الأصلي دون تغيير ألوانه، وتقتصر الخلفية الضبابية على شاشة الترحيب الأولى فقط لضمان النظافة البصرية، مع حظر تكرار الشعار كخلفية في بقية صفحات النظام للحفاظ على الوضوح وسرعة التصفح.
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] text-white font-black text-xs shadow-md transition flex items-center gap-1.5">
                            <span>💾</span>
                            <span>حفظ إعدادات شاشة الترحيب الأولى</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- العمود الأيسر (1 عمود): تعديل النصوص والشعارات الترويجية والمعاينة -->
        <div class="space-y-8">

            <!-- ==================== 3. بطاقة نصوص وشعارات المنصة ==================== -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
                    <div>
                        <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                            <span>📝</span>
                            <span>نصوص وشعارات المنصة</span>
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">تعديل العبارات الترويجية وعناوين الواجهة</p>
                    </div>
                    
                </div>

                <form action="{{ route('admin.branding.texts.update') }}" method="POST" class="space-y-4 text-xs">
                    @csrf

                    <!-- اسم المنصة بالعربية -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">اسم المنصة الرسمي (عربي):</label>
                        <input type="text" name="site_name_ar" value="{{ $settings['site_name_ar'] ?? config('asots.name_ar') }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                    </div>

                    <!-- اسم المنصة بالإنجليزية -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">اسم المنصة (إنجليزي):</label>
                        <input type="text" name="site_name_en" dir="ltr" value="{{ $settings['site_name_en'] ?? config('asots.name_en') }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                    </div>

                    <!-- الشعار اللفظي الترويجي -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">الشعار اللفظي الترويجي (Slogan / Motto):</label>
                        <input type="text" name="site_slogan_ar" value="{{ $settings['site_slogan_ar'] ?? (config('asots.motto_ar') . ' • ' . config('asots.promise_ar')) }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                    </div>

                    <!-- عنوان الترويسة الرئيسي في الصفحة الأولى -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">العنوان الرئيسي في واجهة الموقع (Hero Headline):</label>
                        <input type="text" name="hero_title_ar" value="{{ $settings['hero_title_ar'] ?? config('asots.name_ar') }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                    </div>

                    <!-- شارة الترويسة العلوية -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">شارة الإشعار العلوية (Top Badge Text):</label>
                        <input type="text" name="hero_badge_text" value="{{ $settings['hero_badge_text'] ?? '⭐ The Asian Star Office for Translation, Training, & Consultancy Services' }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                    </div>

                    <!-- النص التعريفي والوصف -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">النص التعريفي في الواجهة الرئيسية:</label>
                        <textarea name="hero_subtitle_ar" rows="3" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 leading-relaxed">{{ $settings['hero_subtitle_ar'] ?? (config('asots.definition_ar') . ' نوفر تعليماً تفاعلياً حياً وفردياً (1-on-1 جلسات 25 دقيقة) يربطك بأفضل المعلمين مع فحص فوري لمخارج الحروف والطلاقة بنظام الذكاء الاصطناعي ونظام محاسبي معتمد.') }}</textarea>
                    </div>

                    <!-- نصوص أزرار الإجراءات في الواجهة -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">نص الزر الرئيسي الأول (Primary CTA):</label>
                        <input type="text" name="hero_cta_primary" value="{{ $settings['hero_cta_primary'] ?? '🚀 ابدأ باختبار تحديد المستوى وحصتك المجانية 1-on-1' }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">نص الزر الثانوي الثاني (Secondary CTA):</label>
                        <input type="text" name="hero_cta_secondary" value="{{ $settings['hero_cta_secondary'] ?? '💳 استعراض باقات الحصص والأسعار' }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                    </div>

                    <!-- أرقام الواتساب المعتمدة للمنصة -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-emerald-50/60 p-3.5 rounded-2xl border border-emerald-200">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-800">رقم الواتساب الأول / الرئيسي (WhatsApp 1):</label>
                            <input type="text" name="whatsapp_number" dir="ltr" value="{{ $settings['whatsapp_platform_number'] ?? ($settings['whatsapp_number'] ?? '+967 775 342 886') }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold font-mono text-slate-800 text-right" placeholder="+967 775 342 886">
                            <span class="text-[10px] text-slate-500">يظهر في التذييل والأزرار السريعة</span>
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-800">رقم الواتساب الإضافي / الثاني (WhatsApp 2):</label>
                            <input type="text" name="whatsapp_number_2" dir="ltr" value="{{ $settings['whatsapp_number_2'] ?? '+967 735 999 101' }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold font-mono text-slate-800 text-right" placeholder="+967 735 999 101">
                            <span class="text-[10px] text-slate-500">يظهر كرقم إضافي في تذييل المنصة</span>
                        </div>
                    </div>

                    <!-- مؤشرات الثقة ومدة الحصص التفاعلية في الواجهة الرئيسية -->
                    <div class="bg-sky-50/70 p-4 rounded-2xl border border-sky-200 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="text-base">⏱️</span>
                            <strong class="text-xs text-sky-950 font-black">مدة الحصص التفاعلية ومؤشرات الأداء بالصفحة الرئيسية:</strong>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-800">مدة الحصة التفاعلية (القيمة الظاهرة):</label>
                                <input type="text" name="session_duration_value" value="{{ $settings['session_duration_value'] ?? '25 دقيقة' }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800" placeholder="مثال: 25 دقيقة أو 30 دقيقة">
                                <span class="text-[10px] text-slate-500">تظهر في بطاقات الحصص، مؤشرات الإحصائيات، ونصوص الواجهة</span>
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-slate-800">وصف مؤشر مدة الحصة:</label>
                                <input type="text" name="session_duration_label" value="{{ $settings['session_duration_label'] ?? 'مدة الحصة التفاعلية المركزة' }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800" placeholder="مثال: مدة الحصة التفاعلية المركزة">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1 border-t border-sky-200/60">
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-700 text-[11px]">عدد الحصص المنجزة:</label>
                                <input type="text" name="stats_sessions_value" value="{{ $settings['stats_sessions_value'] ?? '+15,000' }}" class="w-full p-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                            </div>
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-700 text-[11px]">نسبة رضا واجتياز التوفل:</label>
                                <input type="text" name="stats_satisfaction_value" value="{{ $settings['stats_satisfaction_value'] ?? '98.6%' }}" class="w-full p-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                            </div>
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-700 text-[11px]">تقييم الذكاء الاصطناعي للنطق:</label>
                                <input type="text" name="stats_ai_value" value="{{ $settings['stats_ai_value'] ?? 'AI Powered' }}" class="w-full p-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs shadow-md transition flex items-center justify-center gap-2">
                        <span>💾</span>
                        <span>حفظ كافة النصوص والشعارات</span>
                    </button>
                </form>
            </div>

            <!-- ==================== 3.5. بطاقة تعديل بيانات وسيرة القيادة الأكاديمية (Academic Leadership) ==================== -->
            <div id="leadership-section" class="bg-white rounded-3xl border-2 border-indigo-200 shadow-md p-6 sm:p-8 space-y-6 scroll-mt-6">
                <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🎓</span>
                            <h2 class="text-base font-black text-indigo-950">بيانات وسيرة القيادة الأكاديمية (Academic Leadership)</h2>
                            <span class="text-[10px] bg-amber-100 text-amber-800 font-black px-2 py-0.5 rounded-full border border-amber-300">خاص بمدير النظام 👑</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">تعديل الاسم واللقب الأكاديمي والنبذة التعريفية وأرقام التواصل المباشرة الخاصة بالقيادة الأكاديمية.</p>
                    </div>

                    <a href="{{ route('leadership') }}" target="_blank" class="px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-black border border-indigo-200 shadow-sm transition flex items-center gap-1.5 whitespace-nowrap">
                        <span>👁️</span>
                        <span>استعراض صفحة القيادة العامة</span>
                    </a>
                </div>

                <form action="{{ route('admin.branding.leadership.update') }}" method="POST" class="space-y-4 text-xs">
                    @csrf

                    <!-- الاسم بالعربي والإنجليزي -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">اسم القيادة الأكاديمية (عربي) *:</label>
                            <input type="text" name="leadership_name_ar" required value="{{ $settings['leadership_name_ar'] ?? config('asots.leadership.name_ar', 'الدكتور/ فخر الدين ناجي ردمان عبده الصوفي') }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">الاسم بالإنجليزية (English Name):</label>
                            <input type="text" name="leadership_name_en" dir="ltr" value="{{ $settings['leadership_name_en'] ?? config('asots.leadership.name_en', 'Prof. Dr. Fakhraddin Naji Radman Abdo Al-Sofi, PhD') }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold font-sans text-slate-800">
                        </div>
                    </div>

                    <!-- اللقب الأكاديمي بالعربي والإنجليزي -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">اللقب والدرجة الأكاديمية (عربي) *:</label>
                            <input type="text" name="leadership_title_ar" required value="{{ $settings['leadership_title_ar'] ?? config('asots.leadership.title_ar', 'أستاذ اللسانيات التطبيقية الحديثة') }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">اللقب الأكاديمي (English Title):</label>
                            <input type="text" name="leadership_title_en" dir="ltr" value="{{ $settings['leadership_title_en'] ?? config('asots.leadership.title_en', 'Professor of Modern Applied Linguistics') }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold font-sans text-slate-800">
                        </div>
                    </div>

                    <!-- الدور المؤسسي -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">الصفة المؤسسية والمنصب (عربي) *:</label>
                            <input type="text" name="leadership_role_ar" required value="{{ $settings['leadership_role_ar'] ?? config('asots.leadership.role_ar', 'مؤسس ومدير — مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS)') }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">الصفة المؤسسية (English Role):</label>
                            <input type="text" name="leadership_role_en" dir="ltr" value="{{ $settings['leadership_role_en'] ?? config('asots.leadership.role_en', 'Founder & Director — The Asian Star Office for Translation, Training, & Consultancy Services (ASOTS)') }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold font-sans text-slate-800">
                        </div>
                    </div>

                    <!-- شارة سنوات العطاء وأرقام التواصل المباشر -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">شارة الخبرة والعطاء:</label>
                            <input type="text" name="leadership_years_badge" value="{{ $settings['leadership_years_badge'] ?? 'أكثر من 26 عامًا من العطاء' }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-amber-700">
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">📞 هاتف التواصل المباشر (1):</label>
                            <input type="text" name="leadership_direct_phone_1" dir="ltr" value="{{ $settings['leadership_direct_phone_1'] ?? '+967 775342886' }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold font-mono text-slate-800 text-right">
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">📞 هاتف التواصل المباشر (2):</label>
                            <input type="text" name="leadership_direct_phone_2" dir="ltr" value="{{ $settings['leadership_direct_phone_2'] ?? '+967 735999101' }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold font-mono text-slate-800 text-right">
                        </div>
                    </div>

                    <!-- النبذة التعريفية والسيرة الأكاديمية المختصرة -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">النبذة التعريفية والسيرة الأكاديمية (Bio Summary) *:</label>
                        <textarea name="leadership_bio_summary_ar" rows="8" required class="w-full p-3 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 leading-relaxed font-sans">{{ $settings['leadership_bio_summary_ar'] ?? "الدكتور/ فخر الدين الصوفي، PhD\n\nأستاذ اللسانيات التطبيقية الحديثة، حاصل على الدكتوراه من جامعة محمد الخامس بالرباط، المملكة المغربية، ومتخصص في مجال التواصل بين الثقافات والتداولية وتحليل الخطاب والترجمة وتعليم اللغة الإنجليزية كلغة أجنبية (TEFL/TESOL).\n\nالمجالات المهنية والقيادية:\n\n• أستاذ جامعي لدى عدد من الجامعات الحكومية والخاصة.\n• مستشار أكاديمي وتربوي ومدرب معتمد للمعلمين لدى وزارة التربية والتعليم.\n• خبير في مجال التواصل والترجمة والتدريب والتطوير المهني.\n• مؤسس ومدير مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS).\n\nأكثر من 26 عامًا من العطاء\n\n📞 للتواصل المباشر:\n\n+967 775342886\n+967 735999101" }}</textarea>
                        <span class="text-[10px] text-slate-500">تظهر هذه السيرة بشكل بارز في صفحة القيادة الأكاديمية والبطاقة التعريفية بالرئيسية.</span>
                    </div>

                    <!-- بيان الرؤية الأكاديمية -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">بيان الرؤية الأكاديمية للقيادة:</label>
                        <textarea name="leadership_vision_statement_ar" rows="2" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 leading-relaxed">{{ $settings['leadership_vision_statement_ar'] ?? config('asots.leadership.vision_statement_ar') }}</textarea>
                    </div>

                    <!-- مجالات التخصص والخبرات (سطر لكل تخصص) -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">مجالات التخصص والخبرات الأكاديمية (سطر لكل مجال):</label>
                        @php
                            $defaultSpecs = implode("\n", config('asots.leadership.specialties_ar', []));
                        @endphp
                        <textarea name="leadership_specialties_text" rows="5" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 leading-relaxed font-mono">{{ $settings['leadership_specialties_text'] ?? $defaultSpecs }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md transition flex items-center justify-center gap-2">
                        <span>💾</span>
                        <span>حفظ بيانات وسيرة القيادة الأكاديمية</span>
                    </button>
                </form>
            </div>

            <!-- ==================== 4. بطاقة إرفاق تراخيص واعتمادات الموقع (خاص بمدير النظام) ==================== -->
            <div id="licenses-section" class="bg-white rounded-3xl border-2 border-[#0066A1]/30 shadow-md p-6 sm:p-8 space-y-6">
                <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl">📜</span>
                            <h2 class="text-base font-black text-[#0066A1]">تراخيص واعتمادات الموقع الرسمية</h2>
                            <span class="text-[10px] bg-amber-100 text-amber-800 font-black px-2 py-0.5 rounded-full border border-amber-300">خاص بمدير النظام 👑</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">رفع وإدارة صور وثائق التراخيص والاعتمادات لعرضها في العارض الآمن المحمي ضد التحميل والنسخ والتصوير.</p>
                    </div>

                    <a href="{{ route('licenses.viewer') }}" target="_blank" class="px-4 py-2 rounded-xl bg-[#0066A1] hover:bg-[#004e7c] text-white text-xs font-black shadow transition flex items-center gap-1.5 whitespace-nowrap">
                        <span>👁️</span>
                        <span>استعراض التراخيص بالعارض الآمن</span>
                    </a>
                </div>

                <!-- نموذج رفع ترخيص جديد -->
                <form action="{{ route('admin.branding.licenses.store') }}" method="POST" enctype="multipart/form-data" class="bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-200 space-y-4 text-xs">
                    @csrf
                    <span class="text-xs font-black text-slate-900 block">➕ إرفاق وثيقة ترخيص جديدة:</span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">عنوان الترخيص أو الوثيقة *:</label>
                            <input type="text" name="title" required placeholder="مثال: ترخيص وزارة التربية والتعليم / السجل التجاري" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">رقم الترخيص أو القيد (اختياري):</label>
                            <input type="text" name="license_number" placeholder="مثال: LIC-2026-9901" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-mono text-slate-800">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">الجهة المصدرة (اختياري):</label>
                            <input type="text" name="issuing_authority" placeholder="مثال: وزارة التربية والتعليم والبحث العلمي" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800">
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">تاريخ الإصدار (اختياري):</label>
                            <input type="date" name="issue_date" class="w-full p-2 rounded-xl border border-slate-300 text-xs text-slate-800 font-mono">
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">تاريخ الانتهاء (اختياري):</label>
                            <input type="date" name="expiry_date" class="w-full p-2 rounded-xl border border-slate-300 text-xs text-slate-800 font-mono">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">صورة وثيقة الترخيص (JPG, PNG, WEBP - حتى 12MB) *:</label>
                        <input type="file" name="license_image" accept="image/*" required class="w-full p-2 rounded-xl border border-dashed border-[#0066A1]/40 bg-white text-xs text-slate-700 file:ml-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#0066A1] file:text-white file:font-bold file:text-xs hover:file:bg-[#004e7c]">
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">ملاحظات أو بيان الاعتماد (اختياري):</label>
                        <textarea name="notes" rows="2" placeholder="أي تفاصيل أو نبذة عن الترخيص ومجالات اعتماده..." class="w-full p-2.5 rounded-xl border border-slate-300 text-xs text-slate-800"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-2xl bg-[#0066A1] hover:bg-[#004e7c] text-white font-black text-xs shadow-md transition flex items-center justify-center gap-2">
                        <span>📎</span>
                        <span>رفع وتوثيق وثيقة الترخيص الآن</span>
                    </button>
                </form>

                <!-- قائمة التراخيص المرفوعة مسبقاً -->
                <div class="space-y-3 pt-2">
                    <span class="text-xs font-black text-slate-900 block">📑 التراخيص المسجلة حالياً بالمنصة ({{ $licenses->count() }}):</span>

                    @if($licenses->isEmpty())
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center text-xs text-slate-500">
                            لم يتم رفع أي تراخيص بعد. استخدم النموذج أعلاه لرفع التراخيص الرسمية.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($licenses as $lic)
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center p-1">
                                            <img src="{{ asset($lic->image_path) }}" alt="{{ $lic->title }}" class="w-full h-full object-cover rounded-lg">
                                        </div>
                                        <div>
                                            <h4 class="font-black text-xs text-slate-900">{{ $lic->title }}</h4>
                                            <div class="text-[11px] text-slate-500 flex items-center gap-2 flex-wrap mt-0.5">
                                                @if($lic->license_number)
                                                    <span class="font-mono text-slate-700 font-bold">#{{ $lic->license_number }}</span>
                                                @endif
                                                @if($lic->issuing_authority)
                                                    <span>{{ $lic->issuing_authority }}</span>
                                                @endif
                                                <span class="text-[10px] text-slate-400 font-mono">{{ $lic->created_at->format('Y-m-d') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 self-end sm:self-center">
                                        <!-- تبديل الحالة -->
                                        <form action="{{ route('admin.branding.licenses.toggle', $lic->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-xl text-[11px] font-bold transition {{ $lic->status === 'active' ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}">
                                                {{ $lic->status === 'active' ? '👁️ مفعّل وظاهر' : '🙈 مخفي' }}
                                            </button>
                                        </form>

                                        <!-- حذف -->
                                        <form action="{{ route('admin.branding.licenses.destroy', $lic->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف وثيقة الترخيص هذه نهائياً؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 transition" title="حذف الترخيص">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="p-3.5 bg-amber-50 rounded-2xl border border-amber-200 text-[11px] text-amber-900 leading-relaxed flex items-center gap-2">
                    <span>🔒</span>
                    <span><strong>ملاحظة أمنية:</strong> العارض الرقمي مزود بنظام حماية مشدد لمنع التحميل أو الحفظ أو النسخ أو التعديل، مع تظليل تلقائي عند محاولة التقاط الشاشة لضمان أقصى حماية لوثائق التراخيص.</span>
                </div>
            </div>

            <!-- معلومات وإرشادات المظهر -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-3xl p-5 text-indigo-900 text-xs space-y-2">
                <span class="font-bold flex items-center gap-1.5 text-indigo-950">
                    <span>💡</span>
                    <span>معلومة هامة لإدارة المظهر:</span>
                </span>
                <p class="leading-relaxed text-[11px] text-indigo-800">
                    عند إزالة صورة الخلفية، يعود الموقع فوراً للشكل الطبيعي الهادئ المعتمد للقالب الافتراضي، دون أي تأثير على النصوص أو محتوى الدورات.
                </p>
                <div class="pt-2 border-t border-indigo-200/60">
                    <a href="{{ route('settings.appearance') }}" class="text-indigo-700 hover:underline font-bold text-[11px] flex items-center gap-1">
                        <span>🎨</span>
                        <span>تغيير ألوان القوالب الـ 10 والخطوط العربية ←</span>
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- سكريبتات تفاعلية للمعاينة السريعة واختيار الصور الجاهزة -->
<script>
    function selectPreset(radio) {
        document.querySelectorAll('.preset-check').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.preset-radio').forEach(r => r.closest('label').classList.remove('border-amber-500', 'ring-2', 'ring-amber-400'));
        
        radio.closest('label').classList.add('border-amber-500', 'ring-2', 'ring-amber-400');
        const check = radio.closest('label').querySelector('.preset-check');
        if (check) check.classList.remove('hidden');

        // تفريغ حقل الملف إن وُجد
        const fileInput = document.getElementById('bgFileInput');
        if (fileInput) fileInput.value = '';
        const fileName = document.getElementById('bgFileName');
        if (fileName) fileName.innerText = 'تم اختيار صورة جاهزة من مكتبة المنصة: ' + radio.value;
    }

    function previewUploadedBg(event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('bgFileName').innerText = 'تم تحديد الملف: ' + file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
            // إلغاء تحديد الصور الجاهزة
            document.querySelectorAll('.preset-radio').forEach(r => {
                r.checked = false;
                r.closest('label').classList.remove('border-amber-500', 'ring-2', 'ring-amber-400');
            });
            document.querySelectorAll('.preset-check').forEach(el => el.classList.add('hidden'));
        }
    }

    function previewSplashScreen() {
        sessionStorage.removeItem('asots_splash_shown');
        window.open("{{ route('home') }}?preview_splash=1", "_blank");
    }

    function updateLiveFont(fontKey, fontFamily) {
        document.querySelectorAll('.font-card').forEach(card => {
            card.classList.remove('border-indigo-600', 'bg-indigo-50/50', 'ring-2', 'ring-indigo-500/20', 'shadow-sm');
            card.classList.add('border-slate-200');
            const check = card.querySelector('.font-check');
            if (check) check.classList.add('hidden');
        });
        const selectedRadio = document.querySelector(`input[name="site_font_family"][value="${fontKey}"]`);
        if (selectedRadio) {
            const parentCard = selectedRadio.closest('label');
            if (parentCard) {
                parentCard.classList.remove('border-slate-200');
                parentCard.classList.add('border-indigo-600', 'bg-indigo-50/50', 'ring-2', 'ring-indigo-500/20', 'shadow-sm');
                const check = parentCard.querySelector('.font-check');
                if (check) check.classList.remove('hidden');
            }
        }
        const previewContent = document.getElementById('previewSampleContent');
        if (previewContent) {
            previewContent.style.fontFamily = fontFamily;
        }
        const badge = document.getElementById('previewFontBadge');
        if (badge) badge.innerText = fontKey;
    }

    function syncColorInput(hexInputId, value) {
        const hexInput = document.getElementById(hexInputId);
        if (hexInput) hexInput.value = value;
    }

    function syncColorPicker(pickerId, value) {
        const picker = document.getElementById(pickerId);
        if (picker && /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(value)) {
            picker.value = value;
        }
    }

    function setQuickColor(type, hex) {
        const picker = document.getElementById(type + 'Picker');
        const input = document.getElementById(type + 'Hex');
        if (picker) picker.value = hex;
        if (input) input.value = hex;
        updateLiveColors();
    }

    function updateLiveColors() {
        const fontColor = document.getElementById('siteFontColorHex').value || '#0f172a';
        const fontColorMuted = document.getElementById('siteFontColorMutedHex').value || '#475569';
        const headingColor = document.getElementById('siteHeadingColorHex').value || '#003859';

        const previewBody = document.getElementById('previewBody');
        if (previewBody) previewBody.style.color = fontColor;

        const previewHeading = document.getElementById('previewHeading');
        if (previewHeading) previewHeading.style.color = headingColor;

        const previewMuted = document.getElementById('previewMuted');
        if (previewMuted) previewMuted.style.color = fontColorMuted;

        const colorBadge = document.getElementById('previewColorBadge');
        if (colorBadge) colorBadge.innerText = fontColor;
    }

    function updateFontSizeFromRange(val) {
        const sizePx = val + 'px';
        const display = document.getElementById('fontSizeDisplay');
        if (display) display.innerText = sizePx;

        const sizeBadge = document.getElementById('previewSizeBadge');
        if (sizeBadge) sizeBadge.innerText = sizePx;

        const previewBody = document.getElementById('previewBody');
        if (previewBody) previewBody.style.fontSize = sizePx;

        document.querySelectorAll('.size-preset-card').forEach(c => {
            c.classList.remove('border-indigo-600', 'bg-indigo-50', 'text-indigo-900', 'ring-2', 'ring-indigo-500/20', 'font-black');
            c.classList.add('border-slate-200', 'bg-white', 'text-slate-700', 'font-bold');
        });
        const radio = document.querySelector(`input[name="site_font_size"][value="${sizePx}"]`);
        if (radio) {
            radio.checked = true;
            const parent = radio.closest('label');
            if (parent) {
                parent.classList.remove('border-slate-200', 'bg-white', 'text-slate-700', 'font-bold');
                parent.classList.add('border-indigo-600', 'bg-indigo-50', 'text-indigo-900', 'ring-2', 'ring-indigo-500/20', 'font-black');
            }
        }
    }

    function selectFontSizePreset(sizeKey) {
        const num = parseInt(sizeKey.replace('px', ''));
        const range = document.getElementById('fontSizeRange');
        if (range) range.value = num;

        const display = document.getElementById('fontSizeDisplay');
        if (display) display.innerText = sizeKey;

        const sizeBadge = document.getElementById('previewSizeBadge');
        if (sizeBadge) sizeBadge.innerText = sizeKey;

        const previewBody = document.getElementById('previewBody');
        if (previewBody) previewBody.style.fontSize = sizeKey;

        document.querySelectorAll('.size-preset-card').forEach(c => {
            c.classList.remove('border-indigo-600', 'bg-indigo-50', 'text-indigo-900', 'ring-2', 'ring-indigo-500/20', 'font-black');
            c.classList.add('border-slate-200', 'bg-white', 'text-slate-700', 'font-bold');
        });
        const radio = document.querySelector(`input[name="site_font_size"][value="${sizeKey}"]`);
        if (radio) {
            radio.checked = true;
            const parent = radio.closest('label');
            if (parent) {
                parent.classList.remove('border-slate-200', 'bg-white', 'text-slate-700', 'font-bold');
                parent.classList.add('border-indigo-600', 'bg-indigo-50', 'text-indigo-900', 'ring-2', 'ring-indigo-500/20', 'font-black');
            }
        }
    }
</script>
@endsection
