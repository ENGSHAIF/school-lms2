@extends('layouts.app', ['title' => 'الرئيسية - ' . config('asots.name_ar') . ' (ASOTS)'])

@section('content')
<div class="space-y-20 py-4">

    <!-- ==================== 1. القسم الرئيسي (Hero Section) ==================== -->
    @php
        $siteBg = site_setting('site_background');
        $isBgEnabled = site_setting('site_background_enabled', '0') === '1';
        $bgMode = site_setting('background_mode', 'hero');
        $overlayOpacity = site_setting('overlay_opacity', '0.75');
        $bgBlur = site_setting('background_blur', 'none');
        $applyHeroBg = $isBgEnabled && !empty($siteBg) && ($bgMode === 'hero' || $bgMode === 'body' || $bgMode === 'all_pages');
        $bgImageUrl = $siteBg ? (str_starts_with($siteBg, 'http') ? $siteBg : asset($siteBg)) : null;

        $heroBadge = site_setting('hero_badge_text', '⭐ The Asian Star Office for Translation, Training, & Consultancy Services');
        $heroTitle = site_setting('hero_title_ar', $info['name_ar']);
        $siteNameEn = site_setting('site_name_en', 'The Asian Star Office (ASOTS)');
        $siteSlogan = site_setting('site_slogan_ar', $info['motto_ar'] . ' • ' . $info['promise_ar']);
        $heroSubtitle = site_setting('hero_subtitle_ar', $info['definition_ar'] . ' نوفر تعليماً تفاعلياً حياً وفردياً (1-on-1 جلسات 25 دقيقة) يربطك بأفضل المعلمين مع فحص فوري لمخارج الحروف والطلاقة بنظام الذكاء الاصطناعي ونظام محاسبي معتمد.');
        $heroCtaPrimary = site_setting('hero_cta_primary', '🚀 ابدأ باختبار تحديد المستوى وحصتك المجانية 1-on-1');
        $heroCtaSecondary = site_setting('hero_cta_secondary', '💳 استعراض باقات الحصص والأسعار');
    @endphp

    <section class="rounded-3xl p-8 sm:p-14 shadow-2xl relative overflow-hidden border border-slate-800 text-white transition-all duration-300 {{ $applyHeroBg ? 'bg-slate-900' : 'bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900' }}"
             @if($applyHeroBg && $bgImageUrl)
                 style="background-image: linear-gradient(rgba(15, 23, 42, {{ $overlayOpacity }}), rgba(15, 23, 42, {{ $overlayOpacity }})), url('{{ $bgImageUrl }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"
             @endif>
        <div class="max-w-3xl space-y-6 relative z-10">
            <div class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500/20 to-indigo-500/20 text-amber-300 text-xs px-4 py-1.5 rounded-full font-black border border-amber-400/30">
                <span>{{ $heroBadge }}</span>
            </div>

            <h1 class="text-3xl sm:text-5xl font-black leading-tight tracking-wide">
                {{ $heroTitle }}
                <span class="block text-2xl sm:text-3xl text-amber-400 font-extrabold mt-2 font-sans" dir="ltr">
                    {{ $siteNameEn }}
                </span>
            </h1>

            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-amber-300">
                    {{ $siteSlogan }}
                </p>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-medium">
                    {{ $heroSubtitle }}
                </p>
            </div>

            <!-- أزرار الإجراءات الرئيسية -->
            <div class="flex flex-wrap items-center gap-4 pt-2">
                <a href="{{ route('onboarding') }}" 
                    class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-sm px-8 py-4 rounded-2xl shadow-xl shadow-amber-500/20 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                    <span>{{ $heroCtaPrimary }}</span>
                </a>

                <a href="{{ route('packages.index') }}" 
                    class="bg-white/10 hover:bg-white/20 text-white border border-white/20 font-bold text-sm px-6 py-4 rounded-2xl backdrop-blur-md transition flex items-center gap-2">
                    <span>{{ $heroCtaSecondary }}</span>
                </a>
            </div>

            <!-- شريط مؤشرات الثقة والأرقام القياسية -->
            <div class="pt-6 border-t border-slate-800/80 grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($info['stats'] as $st)
                    <div class="bg-slate-800/50 p-3 rounded-xl border border-slate-700/50">
                        <span class="block text-xl font-black text-amber-400 font-mono">{{ $st['value'] }}</span>
                        <span class="text-[11px] text-slate-400 font-medium">{{ $st['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- أشكال زخرفية خفية في الخلفية -->
        <div class="absolute -bottom-10 -left-10 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -top-10 -right-10 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </section>

    <!-- ==================== بانر العروض الخاصة المجدولة (Visual CMS Slider Banner) ==================== -->
    @php
        $heroBanners = \App\Models\Banner::active()->placement('hero_slider')->orderBy('sort_order')->get();
    @endphp
    @if($heroBanners->count() > 0)
        <div class="space-y-4">
            @foreach($heroBanners as $hb)
                <div class="bg-gradient-to-r from-indigo-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 border border-indigo-500/30 text-white shadow-xl flex flex-col sm:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-4 text-center sm:text-right">
                        <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-3xl border border-white/20 flex-shrink-0">
                            {{ $hb->icon }}
                        </div>
                        <div class="space-y-1">
                            <span class="inline-block text-[10px] font-black uppercase text-amber-300 bg-amber-400/20 px-2.5 py-0.5 rounded-full border border-amber-300/30">حملة معتمدة خاصة</span>
                            <h3 class="text-lg sm:text-xl font-black text-white">{{ $hb->title }}</h3>
                            <p class="text-xs text-slate-300 leading-relaxed max-w-2xl">{{ $hb->subtitle }}</p>
                        </div>
                    </div>
                    <a href="{{ $hb->link_url }}" class="bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-600 hover:to-amber-500 text-slate-950 font-black text-xs px-6 py-3.5 rounded-xl shadow-lg transition whitespace-nowrap">
                        {{ $hb->button_text }}
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    <!-- ==================== 2. شريط الثقة والمميزات الحصرية (Trust Strip) ==================== -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-start gap-4">
            <div class="w-12 h-12 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0 shadow-inner">
                🎯
            </div>
            <div>
                <h3 class="font-black text-sm text-slate-900 mb-1">جلسات 1-on-1 فردية</h3>
                <p class="text-xs text-slate-500 leading-relaxed">25 دقيقة مكثفة من التفاعل المباشر مع المعلم لضمان أقصى تركيز وسرعة تعلم.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-start gap-4">
            <div class="w-12 h-12 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0 shadow-inner">
                🗺️
            </div>
            <div>
                <h3 class="font-black text-sm text-slate-900 mb-1">خارطة طريق معتمدة</h3>
                <p class="text-xs text-slate-500 leading-relaxed">مستويات أكاديمية متوافقة مع الإطار الأوروبي CEFR وتأهيل متخصص لاختبار TOEFL.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-start gap-4">
            <div class="w-12 h-12 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0 shadow-inner">
                📲
            </div>
            <div>
                <h3 class="font-black text-sm text-slate-900 mb-1">تقارير واتساب فورية</h3>
                <p class="text-xs text-slate-500 leading-relaxed">تصلك بعد كل حصة مباشرة تقارير تفصيلية عن النطق والمفردات والواجب ونقاط التحسين.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-start gap-4">
            <div class="w-12 h-12 bg-rose-50 border border-rose-200 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0 shadow-inner">
                ⏰
            </div>
            <div>
                <h3 class="font-black text-sm text-slate-900 mb-1">مرونة تامة في الحجز</h3>
                <p class="text-xs text-slate-500 leading-relaxed">اختر معلمك المفضل والموعد الملائم لجدولك، مع إمكانية التعديل والإلغاء بسهولة.</p>
            </div>
        </div>
    </section>

    <!-- ==================== 3. أعمدة مكتب النجم الآسيوي الثلاثة (Three Service Pillars) ==================== -->
    <section class="space-y-8">
        <div class="text-center max-w-2xl mx-auto">
            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase tracking-wider">
                مجالات الاختصاص والتميز
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">أعمدة مكتب النجم الآسيوي (ASOTS)</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">حلول متكاملة تجمع بين التعليم التفاعلي والترجمة المعتمدة والاستشارات الدولية</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($info['services'] as $srv)
                <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm hover:shadow-xl hover:border-indigo-400 transition flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-inner group-hover:scale-110 transition">
                            {{ $srv['icon'] }}
                        </div>
                        <h3 class="text-lg font-black text-slate-900 mb-3">{{ $srv['title'] }}</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $srv['desc'] }}</p>
                    </div>

                    <div class="pt-6 mt-6 border-t border-slate-100">
                        @if($srv['id'] === 'training')
                            <a href="{{ route('student.booking') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                <span>احجز حصتك التفاعلية 1-on-1</span>
                                <span>←</span>
                            </a>
                        @else
                            <a href="{{ route('services') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1">
                                <span>تفاصيل الخدمة والطلب</span>
                                <span>←</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- ==================== 3.1 القيادة الأكاديمية وضمان الجودة (Academic Leadership & AQED) ==================== -->
    <section class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-8 sm:p-12 shadow-2xl border border-indigo-500/20 space-y-8">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="text-xs font-black text-amber-400 bg-amber-400/10 px-3.5 py-1 rounded-full border border-amber-400/20">
                الضمانة الأكاديمية والنزاهة المؤسسية
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white">القيادة الأكاديمية وقسم الجودة والتميز (AQED)</h2>
            <p class="text-xs sm:text-sm text-slate-300">
                تعليم لغوي وترجمة واستشارات يقودها أستاذ لسانيات مرموق بنموذج جودة حازم لا يقبل الادعاء بدون برهان.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
            <!-- بطاقة القيادة الأكاديمية: أ.د. فخر الدين الصوفي -->
            <div class="bg-slate-800/80 rounded-2xl border border-slate-700 p-6 sm:p-8 flex flex-col justify-between space-y-6 hover:border-amber-400/50 transition">
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-600 flex items-center justify-center text-3xl font-black shadow-lg">
                            👨‍🏫
                        </div>
                        <div>
                            <span class="text-[11px] font-black text-amber-400 block uppercase">مؤسس ومدير ASOTS</span>
                            <h3 class="text-lg sm:text-xl font-black text-white">{{ $info['leadership']['name_ar'] }}</h3>
                            <span class="text-xs text-indigo-300 font-bold block">{{ $info['leadership']['title_ar'] }}</span>
                        </div>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed">
                        {{ $info['leadership']['bio_summary_ar'] }}
                    </p>

                    <div class="space-y-1.5 pt-2">
                        @foreach(array_slice($info['leadership']['specialties_ar'], 0, 3) as $spec)
                            <div class="flex items-center gap-2 text-xs text-slate-300">
                                <span class="text-amber-400 text-sm">✦</span>
                                <span>{{ $spec }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-700/80 flex items-center justify-between">
                    <a href="{{ route('leadership') }}" class="text-xs font-black text-amber-400 hover:text-amber-300 flex items-center gap-1.5">
                        <span>السيرة والبيان الأكاديمي الكامل</span>
                        <span>←</span>
                    </a>
                    <a href="{{ route('consultancy_services') }}" class="text-[11px] font-bold text-slate-400 hover:text-white">
                        طلب استشارة
                    </a>
                </div>
            </div>

            <!-- بطاقة قسم الجودة والتميز AQED -->
            <div class="bg-slate-800/80 rounded-2xl border border-slate-700 p-6 sm:p-8 flex flex-col justify-between space-y-6 hover:border-emerald-400/50 transition">
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-600 flex items-center justify-center text-3xl font-black shadow-lg">
                            🛡️
                        </div>
                        <div>
                            <span class="text-[11px] font-black text-emerald-400 block uppercase">{{ $info['aqed']['name_en'] }}</span>
                            <h3 class="text-lg sm:text-xl font-black text-white">{{ $info['aqed']['name_ar'] }}</h3>
                            <span class="text-xs font-mono font-bold text-rose-300 block">المبدأ: {{ $info['aqed']['motto_ar'] }}</span>
                        </div>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed">
                        نظام تدقيق أكاديمي وإداري مستقل يراقب خطط الدروس، وتقارير المعلمين بعد كل حصة، وسجلات الحضور والتقييم، مع دورة تحسين مستمر مبنية بالكامل على البيانات.
                    </p>

                    <!-- كبسولات توزيع الـ 100 درجة -->
                    <div class="bg-slate-900/80 p-3.5 rounded-xl border border-slate-700/80 space-y-2">
                        <span class="text-[11px] font-black text-slate-300 block">توزيع درجات المقرر المعتمد (100 نقطة):</span>
                        <div class="grid grid-cols-3 gap-2 text-center text-[10px] font-bold">
                            <div class="bg-slate-800 p-1.5 rounded-lg border border-slate-700 text-indigo-300">
                                <span class="block text-white font-mono text-xs">10</span>
                                اختبارات قصيرة
                            </div>
                            <div class="bg-slate-800 p-1.5 rounded-lg border border-slate-700 text-amber-300">
                                <span class="block text-white font-mono text-xs">10</span>
                                استماع وتحدث
                            </div>
                            <div class="bg-slate-800 p-1.5 rounded-lg border border-slate-700 text-emerald-300">
                                <span class="block text-white font-mono text-xs">50</span>
                                حضور ومشاركة
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-700/80 flex items-center justify-between">
                    <a href="{{ route('quality') }}" class="text-xs font-black text-emerald-400 hover:text-emerald-300 flex items-center gap-1.5">
                        <span>معايير الجودة وتوزيع الدرجات الكامل</span>
                        <span>←</span>
                    </a>
                    <a href="{{ route('contact') }}" class="text-[11px] font-bold text-slate-400 hover:text-white">
                        شؤون الطلاب
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== 4. رحلة التعلم في 4 خطوات (ASOTS Learning Journey) ==================== -->
    <section class="bg-slate-900 text-white rounded-3xl p-8 sm:p-12 shadow-xl border border-slate-800">
        <div class="text-center max-w-xl mx-auto mb-10">
            <span class="text-xs font-bold text-amber-400 bg-amber-400/10 px-3 py-1 rounded-full border border-amber-400/20">
                تجربة التعلم الذكي (ASOTS Model)
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white mt-2">كيف تدرس معنا خطوة بخطوة؟</h2>
            <p class="text-xs text-slate-400 mt-1">رحلة سهلة وسريعة تمكنك من بدء التحدث مع معلمك في دقائق</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-right">
            <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700 relative group hover:border-amber-400 transition">
                <span class="text-3xl font-black text-amber-400 font-mono block mb-3">01</span>
                <h4 class="font-bold text-sm text-white mb-2">اختبار القبول وتحديد المستوى</h4>
                <p class="text-xs text-slate-400 leading-relaxed">تقييم سريع للقواعد مع فحص دقيق لمخارج الحروف بالذكاء الاصطناعي لتحديد نقطة انطلاقك الصحيحة.</p>
            </div>

            <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700 relative group hover:border-amber-400 transition">
                <span class="text-3xl font-black text-amber-400 font-mono block mb-3">02</span>
                <h4 class="font-bold text-sm text-white mb-2">شحن باقة الحصص وسند القبض</h4>
                <p class="text-xs text-slate-400 leading-relaxed">اشترك في الباقة الملائمة لتحصل على سند قبض رسمي مرقم ومفقط آلياً وفق المعايير المحاسبية المعتمدة.</p>
            </div>

            <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700 relative group hover:border-amber-400 transition">
                <span class="text-3xl font-black text-amber-400 font-mono block mb-3">03</span>
                <h4 class="font-bold text-sm text-white mb-2">حجز 25 دقيقة في الفصل الحي</h4>
                <p class="text-xs text-slate-400 leading-relaxed">اختر معلمك المفضل والموعد الملائم لتدخل غرفة الفصل التفاعلي المباشر (Air Class) من أي متصفح.</p>
            </div>

            <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700 relative group hover:border-amber-400 transition">
                <span class="text-3xl font-black text-amber-400 font-mono block mb-3">04</span>
                <h4 class="font-bold text-sm text-white mb-2">تقرير الحصة عبر الواتساب</h4>
                <p class="text-xs text-slate-400 leading-relaxed">فور انتهاء الجلسة، يصلك تقرير تفصيلي مع مكافآت النجوم والواجب المنزلي والتسجيل لمراجعة تقدمك.</p>
            </div>
        </div>
    </section>

    <!-- ==================== 5. مسارات التعلم التفاعلية حسب المرحلة (Learning Paths Tabs) ==================== -->
    <section class="space-y-8">
        <div class="text-center max-w-2xl mx-auto">
            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase tracking-wider">
                مناهج مخصصة لكل مرحلة عمرية
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">مسارات التعلم المتخصصة</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">برامج دراسية صُممت بعناية لتناسب أهداف واهتمامات كل فئة</p>
        </div>

        <!-- أزرار التبويبات التفاعلية (Tabs Navigation) -->
        <div class="flex flex-wrap justify-center gap-2 sm:gap-3 p-1.5 bg-slate-200/60 rounded-2xl max-w-3xl mx-auto border border-slate-200">
            @foreach($info['learning_paths'] as $idx => $path)
                <button type="button" onclick="switchPathTab('{{ $path['id'] }}')" 
                    id="pathTabBtn_{{ $path['id'] }}"
                    class="path-tab-btn px-4 sm:px-6 py-2.5 rounded-xl text-xs font-black transition flex items-center gap-2 {{ $idx === 0 ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>{{ $path['icon'] }}</span>
                    <span>{{ $path['name'] }}</span>
                </button>
            @endforeach
        </div>

        <!-- محتوى التبويبات (Tab Panes) -->
        <div class="max-w-4xl mx-auto">
            @foreach($info['learning_paths'] as $idx => $path)
                <div id="pathPane_{{ $path['id'] }}" class="path-pane {{ $idx === 0 ? '' : 'hidden' }} bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-sm transition">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        <div class="space-y-4">
                            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                                {{ $path['name'] }}
                            </span>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900">{{ $path['headline'] }}</h3>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">{{ $path['description'] }}</p>

                            <div class="pt-4 space-y-2.5">
                                @foreach($path['features'] as $f)
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                                        <span class="text-emerald-500">✔</span>
                                        <span>{{ $f }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="pt-6">
                                <a href="{{ route('onboarding') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs px-6 py-3.5 rounded-xl shadow-md transition">
                                    <span>سجل في مسار {{ $path['name'] }}</span>
                                    <span>←</span>
                                </a>
                            </div>
                        </div>

                        <div class="bg-slate-50 border border-slate-200 rounded-3xl p-8 text-center space-y-4">
                            <div class="text-6xl">{{ $path['icon'] }}</div>
                            <h4 class="font-bold text-sm text-slate-800">حصة تجريبية مجانية 25 دقيقة</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">جرب طريقة التدريس التفاعلية مع معلم متخصص في هذا المسار دون أي التزام مالي مسبق.</p>
                            <a href="{{ route('onboarding') }}" class="block text-xs font-black text-amber-600 hover:text-amber-700 underline">
                                احجز حصتك التجريبية الآن 🎁
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- ==================== 6. خريطة المستويات الأكاديمية (CEFR Roadmap) ==================== -->
    <section class="space-y-8">
        <div class="text-center max-w-2xl mx-auto">
            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase tracking-wider">
                المعايير الدولية الأوروبية
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">خارطة طريق المستويات (CEFR Roadmap)</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">تدرج أكاديمي واضح من مستوى التأسيس حتى الإتقان الكامل والتحضير للـ TOEFL</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($info['cefr_roadmap'] as $lvl)
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-indigo-400 transition flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-xl text-xs font-black font-mono border border-indigo-200">
                                {{ $lvl['code'] }}
                            </span>
                            <span class="text-[11px] font-bold text-slate-400 font-sans">{{ $lvl['cefr'] }}</span>
                        </div>
                        <h3 class="font-black text-base text-slate-900">{{ $lvl['title'] }}</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $lvl['outcomes'] }}</p>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100 flex justify-between items-center text-xs">
                        <span class="text-slate-400">المدة المقدرة:</span>
                        <strong class="text-slate-700 font-mono">{{ $lvl['lessons'] }}</strong>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center pt-2">
            <a href="{{ route('curriculum') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline">
                استعرض تفاصيل المناهج والكتب الدراسية المعتمدة ←
            </a>
        </div>
    </section>

    <!-- ==================== 7. نخبة المعلمين المعتمدين (Featured Teachers) ==================== -->
    <section class="space-y-8">
        <div class="text-center max-w-2xl mx-auto">
            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase tracking-wider">
                كادر تدريسي معتمد ومتميز
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">نخبة معلمي مكتب النجم الآسيوي</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">معلمون أكفاء حاصلون على شهادات دولية ومتخصصون في التوفل وتدريس جميع الأعمار</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($teachers as $tch)
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm hover:shadow-xl hover:border-indigo-400 transition flex flex-col justify-between text-center group">
                    <div class="space-y-3">
                        <div class="w-20 h-20 bg-slate-50 border-2 border-slate-100 rounded-full flex items-center justify-center text-4xl mx-auto shadow-inner group-hover:scale-105 transition">
                            {{ $tch['avatar'] }}
                        </div>
                        <div>
                            <span class="inline-block text-[10px] font-black bg-amber-50 text-amber-700 px-2.5 py-0.5 rounded-full border border-amber-200 mb-1">
                                {{ $tch['badge'] }}
                            </span>
                            <h3 class="font-black text-base text-slate-900">{{ $tch['name'] }}</h3>
                            <p class="text-[11px] text-slate-500 font-medium">{{ $tch['title'] }}</p>
                        </div>

                        <div class="flex items-center justify-center gap-3 text-xs py-2 bg-slate-50 rounded-xl">
                            <span class="text-amber-500 font-black">⭐ {{ $tch['rating'] }}</span>
                            <span class="text-slate-300">•</span>
                            <span class="text-slate-600 font-mono text-[11px]">{{ $tch['lessons_count'] }} حصة</span>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed text-right line-clamp-3">
                            {{ $tch['bio'] }}
                        </p>

                        <div class="flex flex-wrap gap-1 justify-center pt-1">
                            @foreach($tch['tags'] as $tg)
                                <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-medium">
                                    {{ $tg }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-5 mt-5 border-t border-slate-100">
                        <a href="{{ route('student.booking') }}" class="block w-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs py-2.5 rounded-xl transition shadow-sm">
                            حجز موعد مع المعلم 🎙️
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- ==================== 8. تجارب حقيقية ملهمة (Moving Testimonials Marquee) ==================== -->
    <section class="bg-gradient-to-r from-slate-950 via-indigo-950 to-slate-950 text-white rounded-3xl p-6 sm:p-10 shadow-2xl border border-indigo-900/60 space-y-6 overflow-hidden relative">
        <!-- شريط الرأس والأزرار -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-800/80 pb-6">
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-2 bg-amber-400/10 text-amber-400 text-xs px-3.5 py-1 rounded-full font-bold border border-amber-400/20">
                    <span>🌟</span>
                    <span>تجارب حقيقية ملهمة ومقالات المستفيدين</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-white">ماذا يقول طلابنا وأولياء الأمور والمعلمون؟</h2>
                <p class="text-xs text-slate-400">آراء ومقالات وتجارب حية من الميدان التعليمي تدور وتتجدد تلقائياً من اليمين إلى اليسار.</p>
            </div>

            <!-- زر المشاركة المخصص للمستخدمين -->
            <div>
                @auth
                    <button type="button" onclick="openTestimonialModal()" 
                        class="bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 hover:from-amber-500 hover:to-amber-700 text-slate-950 font-black text-xs sm:text-sm px-6 py-3.5 rounded-2xl shadow-xl transition flex items-center gap-2 whitespace-nowrap transform hover:-translate-y-0.5 hover:shadow-amber-500/20">
                        <span class="text-base">✍️</span>
                        <span>شاركنا برأيك وتجربتك بالمنصة</span>
                    </button>
                @else
                    <a href="{{ route('login') }}" 
                        class="bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm px-5 py-3 rounded-2xl border border-white/20 transition flex items-center gap-2 whitespace-nowrap">
                        <span>✍️</span>
                        <span>سجّل دخولك بحسابك وشاركنا تجربتك</span>
                    </a>
                @endauth
            </div>
        </div>

        <!-- شريط البانل المتحرك بسلاسة من اليمين إلى اليسار بنفس الطول لجميع المقالات -->
        <div class="relative overflow-hidden w-full py-2" id="testimonialsSliderWrapper">
            <!-- ظلال تدرجية على الحواف لجمالية الحركة واختفاء البطاقات بسلاسة -->
            <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-16 sm:w-28 bg-gradient-to-l from-slate-950 via-slate-950/80 to-transparent z-10"></div>
            <div class="pointer-events-none absolute left-0 top-0 bottom-0 w-16 sm:w-28 bg-gradient-to-r from-slate-950 via-slate-950/80 to-transparent z-10"></div>

            <!-- مسار الحركة اللانهائية المستمرة من اليمين إلى اليسار -->
            <div class="testimonials-infinite-track flex gap-5 py-2" dir="ltr" onmouseenter="pauseMarquee()" onmouseleave="resumeMarquee()">
                {{-- المجموعة الأولى --}}
                @foreach($testimonials as $tst)
                    <div class="w-[330px] sm:w-[400px] h-[280px] shrink-0 bg-slate-800/80 hover:bg-slate-800 border border-slate-700/80 hover:border-amber-400/80 rounded-2xl p-6 shadow-xl backdrop-blur-md flex flex-col justify-between transition-all duration-300 relative group text-right" dir="rtl">
                        <div class="space-y-2.5">
                            <div class="flex justify-between items-start gap-2">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-3xl shrink-0">{{ $tst->avatar ?? '🎓' }}</span>
                                    <div>
                                        <strong class="text-xs sm:text-sm text-white font-black block leading-tight">{{ $tst->name }}</strong>
                                        <span class="text-[10px] sm:text-[11px] text-slate-400 block">{{ $tst->role }}</span>
                                    </div>
                                </div>
                                @if(!empty($tst->score))
                                    <span class="text-[10px] font-bold text-amber-300 bg-amber-400/10 px-2.5 py-1 rounded-lg border border-amber-400/20 whitespace-nowrap font-mono">
                                        {{ $tst->score }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-1 text-amber-400 text-xs">
                                <span>{{ $tst->stars_string ?? str_repeat('⭐', $tst->rating ?? 5) }}</span>
                            </div>

                            @if(!empty($tst->title))
                                <h4 class="text-xs sm:text-sm font-black text-amber-300 truncate">
                                    {{ $tst->title }}
                                </h4>
                            @endif

                            <p class="text-xs text-slate-300 leading-relaxed line-clamp-3 italic">
                                "{{ $tst->content }}"
                            </p>
                        </div>

                        <div class="pt-3 border-t border-slate-700/80 flex items-center justify-between text-[11px]">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $tst->badge_class ?? 'bg-indigo-500/20 text-indigo-300 border-indigo-400/30' }}">
                                {{ $tst->user_type_label ?? 'عضو المنصة' }}
                            </span>
                            <span class="text-slate-400 font-mono text-[10px]">
                                {{ $tst->created_at ? $tst->created_at->diffForHumans() : 'مؤخراً' }}
                            </span>
                        </div>
                    </div>
                @endforeach

                {{-- المجموعة الثانية المكررة لضمان التدفق والانتقال المستمر واللانهائي بدون أي انقطاع --}}
                @foreach($testimonials as $tst)
                    <div class="w-[330px] sm:w-[400px] h-[280px] shrink-0 bg-slate-800/80 hover:bg-slate-800 border border-slate-700/80 hover:border-amber-400/80 rounded-2xl p-6 shadow-xl backdrop-blur-md flex flex-col justify-between transition-all duration-300 relative group text-right" dir="rtl">
                        <div class="space-y-2.5">
                            <div class="flex justify-between items-start gap-2">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-3xl shrink-0">{{ $tst->avatar ?? '🎓' }}</span>
                                    <div>
                                        <strong class="text-xs sm:text-sm text-white font-black block leading-tight">{{ $tst->name }}</strong>
                                        <span class="text-[10px] sm:text-[11px] text-slate-400 block">{{ $tst->role }}</span>
                                    </div>
                                </div>
                                @if(!empty($tst->score))
                                    <span class="text-[10px] font-bold text-amber-300 bg-amber-400/10 px-2.5 py-1 rounded-lg border border-amber-400/20 whitespace-nowrap font-mono">
                                        {{ $tst->score }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-1 text-amber-400 text-xs">
                                <span>{{ $tst->stars_string ?? str_repeat('⭐', $tst->rating ?? 5) }}</span>
                            </div>

                            @if(!empty($tst->title))
                                <h4 class="text-xs sm:text-sm font-black text-amber-300 truncate">
                                    {{ $tst->title }}
                                </h4>
                            @endif

                            <p class="text-xs text-slate-300 leading-relaxed line-clamp-3 italic">
                                "{{ $tst->content }}"
                            </p>
                        </div>

                        <div class="pt-3 border-t border-slate-700/80 flex items-center justify-between text-[11px]">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $tst->badge_class ?? 'bg-indigo-500/20 text-indigo-300 border-indigo-400/30' }}">
                                {{ $tst->user_type_label ?? 'عضو المنصة' }}
                            </span>
                            <span class="text-slate-400 font-mono text-[10px]">
                                {{ $tst->created_at ? $tst->created_at->diffForHumans() : 'مؤخراً' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- تنبيه توضيحي أسفل البانل مع أزرار تحكم سلسة -->
        <div class="flex flex-col sm:flex-row justify-between items-center text-xs text-slate-400 pt-2 border-t border-slate-800/60 gap-2">
            <span class="flex items-center gap-2">
                <span>💡</span>
                <span>تتحرك المقالات والتجارب تلقائياً من اليمين إلى اليسار، وتتوقف مؤقتاً عند تمرير مؤشر الفأرة عليها لقراءتها بارتياح.</span>
            </span>
            <div class="flex items-center gap-2">
                <button type="button" onclick="pauseMarquee()" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-bold transition">⏸️ إيقاف مؤقت</button>
                <button type="button" onclick="resumeMarquee()" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-bold transition">▶️ متابعة الحركة</button>
            </div>
        </div>
    </section>

    <!-- ==================== 9. المصادر التعليمية والمقالات الإثرائية (Educational Resources) ==================== -->
    <section class="space-y-8">
        <div class="text-center max-w-2xl mx-auto">
            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase tracking-wider">
                مكتبة المعرفة والإثراء
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">مقالات وإرشادات لاجتياز التوفل</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">نصائح واستراتيجيات مجانية يقدمها خبراؤنا لتسريع إتقانك للغة</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($info['articles'] as $art)
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-[11px] text-slate-400">
                            <span class="bg-indigo-50 text-indigo-700 font-bold px-2.5 py-0.5 rounded-full">{{ $art['category'] }}</span>
                            <span>{{ $art['read_time'] }}</span>
                        </div>
                        <h3 class="font-black text-sm text-slate-900 leading-snug hover:text-indigo-600 transition">{{ $art['title'] }}</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $art['snippet'] }}</p>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100 flex justify-between items-center text-xs">
                        <span class="text-slate-400 text-[11px] font-mono">{{ $art['date'] }}</span>
                        <a href="{{ route('onboarding') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                            اقرأ الدليل ←
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- ==================== 10. الأسئلة الشائعة (Frequently Asked Questions - Accordion) ==================== -->
    <section class="space-y-8 max-w-3xl mx-auto">
        <div class="text-center">
            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full uppercase tracking-wider">
                إجابات واضحة لاستفساراتك
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">الأسئلة الأكثر شيوعاً</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">كل ما تحتاج معرفته عن نظام الحصص الفردية وسندات القبض المعتمدة</p>
        </div>

        <div class="space-y-3">
            @foreach($info['faqs'] as $fIdx => $faq)
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <button type="button" onclick="toggleFaq({{ $fIdx }})" class="w-full text-right p-5 font-bold text-xs sm:text-sm text-slate-900 flex justify-between items-center hover:bg-slate-50 transition">
                        <span>{{ $faq['q'] }}</span>
                        <span id="faqIcon_{{ $fIdx }}" class="text-slate-400 text-base transform transition duration-200">▼</span>
                    </button>
                    <div id="faqAns_{{ $fIdx }}" class="hidden p-5 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                        {{ $faq['a'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- ==================== 11. شريط الدعوة للعمل الختامي (Final Call to Action) ==================== -->
    <section class="bg-gradient-to-r from-amber-500 via-amber-400 to-indigo-600 text-slate-950 rounded-3xl p-8 sm:p-14 shadow-2xl flex flex-col md:flex-row justify-between items-center gap-8">
        <div class="space-y-2 text-center md:text-right max-w-xl">
            <span class="inline-block bg-slate-950 text-white text-xs font-black px-3.5 py-1 rounded-full">
                ابدأ رحلتك اليوم مجاناً 🎁
            </span>
            <h3 class="text-2xl sm:text-3xl font-black">احجز حصتك التجريبية الفردية واختبر نطقك الآن</h3>
            <p class="text-xs sm:text-sm font-semibold text-slate-900 leading-relaxed">
                انضم لمئات الطلاب الذين يطورون لغتهم الإنجليزية ويستعدون للتوفل مع نخبة المعلمين في مكتب النجم الآسيوي (ASOTS).
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 whitespace-nowrap">
            <a href="{{ route('onboarding') }}" class="bg-slate-950 hover:bg-slate-900 text-white font-black text-xs sm:text-sm px-8 py-4 rounded-2xl shadow-xl transition transform hover:-translate-y-0.5">
                ابدأ التسجيل الذكي 1-on-1 🚀
            </a>
            <a href="{{ route('packages.index') }}" class="bg-white/90 hover:bg-white text-slate-950 font-black text-xs sm:text-sm px-6 py-4 rounded-2xl shadow-md transition">
                استعراض الأسعار والباقات
            </a>
        </div>
    </section>

</div>

<!-- مودال مشاركة الرأي والتجربة (Add Testimonial Modal) -->
<div id="modalAddTestimonial" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-5 text-right" dir="rtl">
        <div class="flex justify-between items-start border-b border-slate-100 pb-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200 mb-1">
                    <span>✍️</span>
                    <span>شاركنا برأيك وتجربتك ومقالك بالمنصة</span>
                </div>
                <h3 class="font-black text-lg text-slate-900">نشر تجربة ومقال حقيقي في الواجهة</h3>
                <p class="text-xs text-slate-500">سيظهر مقالك فوراً في شريط التجارب الملهمة المتحرك ليشاهده كافة أعضاء وزوار المنصة.</p>
            </div>
            <button type="button" onclick="closeTestimonialModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 font-bold flex items-center justify-center transition">✕</button>
        </div>

        <form action="{{ route('testimonials.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            @auth
                <div class="p-3.5 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center gap-3">
                    <span class="text-3xl">{{ auth()->user()->gender === 'female' ? (auth()->user()->isParent() ? '👩‍👧‍👦' : (auth()->user()->isTeacher() ? '👩‍🏫' : '👧')) : (auth()->user()->isParent() ? '👨‍👧‍👦' : (auth()->user()->isTeacher() ? '👨‍🏫' : '🎓')) }}</span>
                    <div>
                        <strong class="text-xs font-black text-slate-900 block">{{ auth()->user()->name }}</strong>
                        <span class="text-[11px] text-indigo-700 font-bold">
                            الصفة بالمنصة: {{ auth()->user()->isParent() ? 'ولي أمر 👨‍👧‍👦' : (auth()->user()->isTeacher() ? 'معلم لغات 👨‍🏫' : (auth()->user()->isStudent() ? 'طالب في المنصة 🎓' : 'عضو في المنصة 🌟')) }}
                        </span>
                    </div>
                </div>
            @else
                <div>
                    <label class="block font-bold text-slate-700 mb-1">اسمك الكريم *</label>
                    <input type="text" name="name" required placeholder="مثال: فهد محمد أو ولي أمر الطالب..." 
                        class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>
            @endauth

            <!-- تقييم النجوم -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">تقييمك لتجربتك بالمنصة *</label>
                <select name="rating" id="ratingSelect" class="w-full border border-slate-300 rounded-xl p-2.5 text-xs font-bold outline-none bg-slate-50">
                    <option value="5" selected>⭐⭐⭐⭐⭐ ممتاز ومميز جداً (5 نجوم)</option>
                    <option value="4">⭐⭐⭐⭐ جيد جداً وأنصح به (4 نجوم)</option>
                    <option value="3">⭐⭐⭐ جيد ومفيد (3 نجوم)</option>
                    <option value="2">⭐⭐ متوسط (نجمتان)</option>
                    <option value="1">⭐ مقبول (نجمة واحدة)</option>
                </select>
            </div>

            <!-- عنوان المقال أو التجربة -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">عنوان المقال أو خلاصة التجربة (اختياري)</label>
                <input type="text" name="title" placeholder="مثال: تجربتي مع كورس التوفل، أو متابعة طفلي بالمنصة..." 
                    class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-indigo-500 text-xs">
            </div>

            <!-- نص المقال والتجربة -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">مقالك أو رأيك وتجربتك بالتفصيل *</label>
                <textarea name="content" rows="4" required placeholder="اكتب هنا رأيك وتجربتك: ما الذي أعجبك في المنصة، تفاعل المعلمين، الحصص الفردية، تقارير الحضور والتقييمات، رصيد النجوم، أو النتيجة التي حققتها..." 
                    class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-indigo-500 text-xs leading-relaxed"></textarea>
                <span class="text-[10px] text-slate-400 mt-1 block">اكتب مقالاً واضحاً وصريحاً يستفيد منه زوار وأعضاء المنصة.</span>
            </div>

            <!-- إنجاز حققته (اختياري) -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">إنجاز حققته أو نتيجة ملموسة (اختياري)</label>
                <input type="text" name="score" placeholder="مثال: اجتياز المستوى الأول بنجاح، أو الحصول على 95 في الكويز..." 
                    class="w-full border border-slate-300 rounded-xl p-3 outline-none text-xs">
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeTestimonialModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold transition">
                    إلغاء ✕
                </button>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black rounded-xl shadow-md transition flex items-center gap-2">
                    <span>🚀</span>
                    <span>نشر التجربة في المنصة</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* حركة الماركي السلسة اللانهائية من اليمين إلى اليسار */
    @keyframes marqueeScrollRTL {
        0% {
            transform: translateX(0%);
        }
        100% {
            transform: translateX(-50%);
        }
    }
    .testimonials-infinite-track {
        display: flex;
        width: max-content;
        animation: marqueeScrollRTL 45s linear infinite;
    }
    .testimonials-infinite-track:hover {
        animation-play-state: paused;
    }
</style>

<!-- سكريبت التبويبات التفاعلية والماركي ومربعات الأسئلة الشائعة -->
<script>
function switchPathTab(selectedId) {
    document.querySelectorAll('.path-pane').forEach(el => el.classList.add('hidden'));
    document.getElementById(`pathPane_${selectedId}`).classList.remove('hidden');

    document.querySelectorAll('.path-tab-btn').forEach(btn => {
        btn.classList.remove('bg-white', 'text-indigo-600', 'shadow-sm');
        btn.classList.add('text-slate-600');
    });

    const activeBtn = document.getElementById(`pathTabBtn_${selectedId}`);
    if (activeBtn) {
        activeBtn.classList.add('bg-white', 'text-indigo-600', 'shadow-sm');
        activeBtn.classList.remove('text-slate-600');
    }
}

function toggleFaq(index) {
    const ans = document.getElementById(`faqAns_${index}`);
    const icon = document.getElementById(`faqIcon_${index}`);

    if (ans.classList.contains('hidden')) {
        ans.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        ans.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}

// التحكم بنافذة إضافة المقال والتجربة
function openTestimonialModal() {
    document.getElementById('modalAddTestimonial').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeTestimonialModal() {
    document.getElementById('modalAddTestimonial').classList.add('hidden');
    document.body.style.overflow = '';
}

// التحكم بحركة الماركي
function pauseMarquee() {
    const track = document.querySelector('.testimonials-infinite-track');
    if (track) track.style.animationPlayState = 'paused';
}

function resumeMarquee() {
    const track = document.querySelector('.testimonials-infinite-track');
    if (track) track.style.animationPlayState = 'running';
}

// إغلاق النافذة بزر Escape
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTestimonialModal();
    }
});
</script>
@endsection
