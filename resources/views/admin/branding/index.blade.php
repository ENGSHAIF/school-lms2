@extends('layouts.app', ['title' => 'إدارة الهوية البصرية وخلفيات وشعارات الموقع'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto my-4">

    <!-- شريط الترويسة الرئيسي -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border border-slate-800">
        <div>
            <div class="inline-flex items-center gap-2 bg-pink-500/20 text-pink-300 text-xs px-3.5 py-1 rounded-full font-bold border border-pink-400/30 mb-2">
                <span>🎨</span>
                <span>تخصيص الهوية البصرية • Visual Identity & Backgrounds</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">إدارة خلفيات وشعارات ونصوص المنصة</h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                تحكم كامل في خلفية الموقع (إضافة صور تعبر عن المنصة أو إزالتها والعودة للشكل الطبيعي)، تخصيص شعار المنصة، وتعديل النصوص والشعارات الترويجية بسهولة.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" target="_blank" class="px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs sm:text-sm shadow-lg shadow-amber-500/20 transition flex items-center gap-2">
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

                    @if($currentLogo)
                        <form method="POST" action="{{ route('admin.branding.logo.remove') }}" onsubmit="return confirm('هل تريد إزالة الشعار المخصص والعودة للشعار المؤسسي الافتراضي للمنصة؟');">
                            @csrf
                            <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-500/20 hover:bg-rose-500 text-rose-200 hover:text-white font-bold text-xs transition border border-rose-500/30">
                                ✕ إزالة الشعار واستعادة الافتراضي
                            </button>
                        </form>
                    @endif
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
                    
                    <form method="POST" action="{{ route('admin.branding.texts.reset') }}" onsubmit="return confirm('هل تريد استعادة كافة النصوص والشعارات الافتراضية الأصلية للمنصة؟');">
                        @csrf
                        <button type="submit" title="استعادة النصوص الافتراضية الأصلية" class="text-[10px] text-slate-400 hover:text-amber-600 font-bold underline">
                            استعادة الافتراضي ↩
                        </button>
                    </form>
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

                    <!-- رقم الواتساب المعتمد للمنصة -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">رقم الواتساب الرسمي المعتمد للتواصل:</label>
                        <input type="text" name="whatsapp_number" dir="ltr" value="{{ $settings['whatsapp_platform_number'] ?? ($settings['whatsapp_number'] ?? '+967735999101') }}" class="w-full p-2.5 rounded-xl border border-slate-300 text-xs font-bold font-mono text-slate-800 text-right">
                    </div>

                    <button type="submit" class="w-full py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs shadow-md transition flex items-center justify-center gap-2">
                        <span>💾</span>
                        <span>حفظ كافة النصوص والشعارات</span>
                    </button>
                </form>
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
</script>
@endsection
