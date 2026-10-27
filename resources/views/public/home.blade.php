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
        $heroSubtitle = site_setting('hero_subtitle_ar', $info['definition_ar'] . ' نوفر تعليماً تفاعلياً حياً يربطك بأفضل المعلمين مع فحص فوري لمخارج الحروف والطلاقة بنظام الذكاء الاصطناعي ونظام محاسبي معتمد.');
        $heroCtaPrimary = site_setting('hero_cta_primary', '🚀 ابدأ باختبار تحديد المستوى وحصتك المجانية');
        $heroCtaSecondary = site_setting('hero_cta_secondary', '💳 استعراض باقات الحصص والأسعار');
    @endphp

    <section class="rounded-3xl p-8 sm:p-14 shadow-2xl relative overflow-hidden border border-[#0066A1]/40 text-white transition-all duration-300 {{ $applyHeroBg ? 'bg-[#004e7c]' : 'bg-gradient-to-r from-[#004e7c] via-[#005586] to-[#0066A1]' }}"
             @if($applyHeroBg && $bgImageUrl)
                 style="background-image: linear-gradient(rgba(0, 78, 124, {{ $overlayOpacity }}), rgba(0, 102, 161, {{ $overlayOpacity }})), url('{{ $bgImageUrl }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"
             @endif>
        <div class="max-w-3xl space-y-6 relative z-10">
            <div class="inline-flex items-center gap-2 bg-gradient-to-r from-[#F58220]/25 to-[#0066A1]/30 text-[#F58220] text-xs px-4 py-1.5 rounded-full font-black border border-[#F58220]/40">
                <span>{{ $heroBadge }}</span>
            </div>

            <h1 class="text-3xl sm:text-5xl font-black leading-tight tracking-wide">
                {{ $heroTitle }}
                <span class="block text-2xl sm:text-3xl text-[#F58220] font-extrabold mt-2 font-sans" dir="ltr">
                    {{ $siteNameEn }}
                </span>
            </h1>

            <div class="space-y-1">
                <p class="text-xs sm:text-sm font-bold text-amber-200">
                    {{ $siteSlogan }}
                </p>
                <p class="text-sm sm:text-base text-slate-100 leading-relaxed font-medium">
                    {{ $heroSubtitle }}
                </p>
            </div>

            <!-- أزرار الإجراءات الرئيسية المنسجمة مع هوية الشعار -->
            <div class="flex flex-wrap items-center gap-4 pt-2">
                <a href="{{ route('onboarding') }}" 
                    class="bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] hover:to-[#b85807] text-white font-black text-sm px-8 py-4 rounded-2xl shadow-xl shadow-[#F58220]/30 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                    <span>{{ $heroCtaPrimary }}</span>
                </a>

                <a href="{{ route('packages.index') }}" 
                    class="bg-white/10 hover:bg-white/20 text-white border border-white/30 font-bold text-sm px-6 py-4 rounded-2xl backdrop-blur-md transition flex items-center gap-2">
                    <span>{{ $heroCtaSecondary }}</span>
                </a>
            </div>

            <!-- شريط مؤشرات الثقة والأرقام القياسية -->
            @php
                $cleanStats = array_values(array_filter($info['stats'] ?? [], function($st) {
                    $txt = ($st['value'] ?? '') . ' ' . ($st['label'] ?? '');
                    return !str_contains($txt, '25 دقيقة') && !str_contains($txt, '15,000') && !str_contains($txt, 'حصة تدريبية');
                }));
            @endphp
            @if(count($cleanStats) > 0)
                <div class="pt-6 border-t border-white/20 grid grid-cols-2 sm:grid-cols-{{ min(count($cleanStats), 4) }} gap-4">
                    @foreach($cleanStats as $st)
                        <div class="bg-black/25 backdrop-blur-sm p-3 rounded-xl border border-white/15">
                            <span class="block text-xl font-black text-[#F58220] font-mono">{{ $st['value'] }}</span>
                            <span class="text-[11px] text-slate-200 font-medium">{{ $st['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- أشكال زخرفية خفية في الخلفية بتدرجات الأزرق والبرتقالي -->
        <div class="absolute -bottom-10 -left-10 w-96 h-96 bg-[#0066A1]/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -top-10 -right-10 w-80 h-80 bg-[#F58220]/25 rounded-full blur-3xl pointer-events-none"></div>
    </section>

    <!-- ==================== بانر العروض الخاصة المجدولة (Visual CMS Slider Banner) ==================== -->
    <!-- ==================== بانر وسلايدر الإعلانات التفاعلي المتحرك (Dynamic Visual Announcements Carousel - الحجم المضاعف) ==================== -->
    @php
        $heroBanners = \App\Models\Banner::active()->ads()->placement('hero_slider')->orderBy('sort_order')->get();
        if ($heroBanners->isEmpty()) {
            $heroBanners = \App\Models\Banner::active()->ads()->where(function ($q) {
                $q->whereNull('placement')->orWhere('placement', '!=', 'top_announcement');
            })->orderBy('sort_order')->get();
        }
    @endphp
    @if($heroBanners->count() > 0)
        <div class="space-y-4" id="adsCarouselSection">
            <!-- ترويسة شريط الإعلانات -->
            <div class="flex items-center justify-between gap-3 px-2">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-[#F58220] animate-ping"></span>
                    <span class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <span class="text-base">📢</span>
                        <span>مركز الإعلانات والحملات الترويجية المعتمدة</span>
                    </span>
                    <span class="text-xs bg-amber-100 text-amber-900 font-black px-3 py-0.5 rounded-full border border-amber-300">
                        {{ $heroBanners->count() }} إعلانات نشطة
                    </span>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('announcements.index') }}" class="text-xs font-bold text-[#0066A1] hover:underline flex items-center gap-1">
                        <span>استعراض كافة الإعلانات</span>
                        <span>←</span>
                    </a>
                    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor()))
                        <a href="{{ route('admin.banners.index') }}" class="px-3 py-1.5 rounded-xl bg-amber-500/20 text-amber-900 border border-amber-300 text-xs font-black hover:bg-amber-500/30 transition">
                            ✏️ إدارة الإعلانات
                        </a>
                    @endif
                </div>
            </div>

            <!-- حاوية السلايدر التفاعلي الدوّار بحجم مضاعف كبير وواضح -->
            <div class="relative overflow-hidden rounded-3xl sm:rounded-[36px] shadow-2xl border-2 border-[#0066A1]/40 bg-gradient-to-r from-[#002e4a] via-[#004e7c] to-[#0066A1] text-white group min-h-[460px] sm:min-h-[500px] lg:min-h-[520px] flex items-center" id="mainBannerCarousel">
                
                @foreach($heroBanners as $idx => $hb)
                    <div class="banner-slide w-full {{ $idx === 0 ? 'block' : 'hidden' }} transition-all duration-700 ease-in-out p-6 sm:p-10 lg:p-12" data-index="{{ $idx }}">
                        <div class="flex flex-col lg:flex-row items-center justify-between gap-8 lg:gap-12">
                            
                            <!-- صورة البوستر المرفقة بحجم كبير مضاعف وواضح جداً -->
                            @if($hb->image_path)
                                <div class="w-full lg:w-5/12 h-64 sm:h-80 md:h-[400px] lg:h-[440px] rounded-3xl overflow-hidden border-2 border-white/25 shadow-2xl bg-black/40 flex items-center justify-center flex-shrink-0 group-hover:scale-[1.01] transition duration-300 relative">
                                    <img src="{{ asset($hb->image_path) }}" alt="{{ $hb->title }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent pointer-events-none"></div>
                                    <span class="absolute bottom-3 right-3 bg-[#0066A1]/90 backdrop-blur-md text-white text-[10px] font-black px-3 py-1 rounded-full border border-white/20">
                                        {{ $hb->icon }} بوستر رسمي
                                    </span>
                                </div>
                            @endif

                            <div class="flex-1 space-y-4 text-center lg:text-right">
                                <div class="flex items-center justify-center lg:justify-start gap-2.5 flex-wrap">
                                    <span class="inline-block text-xs font-black uppercase text-[#F58220] bg-white/10 px-4 py-1 rounded-full border border-[#F58220]/40 shadow-sm">
                                        حملة رسمية معتمدة ⭐
                                    </span>
                                    <span class="text-xs text-sky-200 font-mono bg-black/20 px-3 py-0.5 rounded-full">
                                        إعلان #{{ $idx + 1 }} من {{ $heroBanners->count() }}
                                    </span>
                                </div>

                                <h3 class="text-xl sm:text-3xl lg:text-4xl font-black text-white leading-tight tracking-tight">
                                    <span class="inline-block mr-1">{{ $hb->icon }}</span>
                                    {{ $hb->title }}
                                </h3>

                                @if($hb->subtitle)
                                    <p class="text-sm sm:text-base lg:text-lg text-sky-100 leading-relaxed font-semibold max-w-3xl">
                                        {{ $hb->subtitle }}
                                    </p>
                                @endif

                                @if($hb->content)
                                    <div class="text-xs sm:text-sm text-amber-100/95 leading-relaxed max-w-3xl bg-black/30 backdrop-blur-sm p-4 sm:p-5 rounded-2xl border border-white/15 line-clamp-3 font-medium">
                                        {{ Str::limit(strip_tags($hb->content), 260) }}
                                    </div>
                                @endif

                                <!-- أزرار الإجراءات التفاعلية الواضحة والكبيرة -->
                                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5 pt-3">
                                    <a href="{{ $hb->link_url }}" class="w-full sm:w-auto bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] hover:to-[#b85807] text-white font-black text-xs sm:text-sm px-8 py-4 rounded-2xl shadow-xl shadow-[#F58220]/30 transition transform hover:-translate-y-0.5 text-center whitespace-nowrap flex items-center justify-center gap-2">
                                        <span>{{ $hb->button_text }}</span>
                                    </a>
                                    @if($hb->content || $hb->image_path)
                                        <a href="{{ route('announcements.show', $hb->id) }}" class="w-full sm:w-auto bg-white/15 hover:bg-white/25 text-white border border-white/30 text-xs sm:text-sm font-bold px-6 py-4 rounded-2xl backdrop-blur-md transition text-center whitespace-nowrap flex items-center justify-center gap-1.5">
                                            <span>تفاصيل الإعلان كاملة 📄</span>
                                        </a>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach

                <!-- أسهم التنقل الكبيرة بين الإعلانات (Next / Prev) -->
                @if($heroBanners->count() > 1)
                    <button type="button" id="carouselPrevBtn" class="absolute top-1/2 -translate-y-1/2 right-4 w-12 h-12 rounded-2xl bg-black/45 hover:bg-[#F58220] text-white flex items-center justify-center text-2xl font-black transition opacity-0 group-hover:opacity-100 focus:opacity-100 shadow-xl z-20 backdrop-blur-md border border-white/20" aria-label="السابق">
                        ›
                    </button>
                    <button type="button" id="carouselNextBtn" class="absolute top-1/2 -translate-y-1/2 left-4 w-12 h-12 rounded-2xl bg-black/45 hover:bg-[#F58220] text-white flex items-center justify-center text-2xl font-black transition opacity-0 group-hover:opacity-100 focus:opacity-100 shadow-xl z-20 backdrop-blur-md border border-white/20" aria-label="التالي">
                        ‹
                    </button>

                    <!-- نقاط المؤشرات الدائرية الكبيرة (Dots Indicators) -->
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20 bg-black/30 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/15">
                        @foreach($heroBanners as $idx => $hb)
                            <button type="button" class="carousel-dot h-3 rounded-full transition-all duration-300 {{ $idx === 0 ? 'bg-[#F58220] w-8' : 'bg-white/40 hover:bg-white/80 w-3' }}" data-index="{{ $idx }}" aria-label="شريحة {{ $idx + 1 }}"></button>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>

        @if($heroBanners->count() > 1)
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const slides = document.querySelectorAll('.banner-slide');
                    const dots = document.querySelectorAll('.carousel-dot');
                    const prevBtn = document.getElementById('carouselPrevBtn');
                    const nextBtn = document.getElementById('carouselNextBtn');
                    let current = 0;
                    let timer = null;

                    function showSlide(index) {
                        slides.forEach((s, i) => {
                            if (i === index) {
                                s.classList.remove('hidden');
                                s.classList.add('block');
                            } else {
                                s.classList.add('hidden');
                                s.classList.remove('block');
                            }
                        });

                        dots.forEach((d, i) => {
                            if (i === index) {
                                d.classList.remove('bg-white/40', 'w-2.5');
                                d.classList.add('bg-[#F58220]', 'w-6');
                            } else {
                                d.classList.add('bg-white/40', 'w-2.5');
                                d.classList.remove('bg-[#F58220]', 'w-6');
                            }
                        });
                        current = index;
                    }

                    function nextSlide() {
                        let next = (current + 1) % slides.length;
                        showSlide(next);
                    }

                    function prevSlide() {
                        let prev = (current - 1 + slides.length) % slides.length;
                        showSlide(prev);
                    }

                    function startAutoPlay() {
                        if (!timer) {
                            timer = setInterval(nextSlide, 5000);
                        }
                    }

                    function stopAutoPlay() {
                        if (timer) {
                            clearInterval(timer);
                            timer = null;
                        }
                    }

                    if (nextBtn) nextBtn.addEventListener('click', () => { stopAutoPlay(); nextSlide(); startAutoPlay(); });
                    if (prevBtn) prevBtn.addEventListener('click', () => { stopAutoPlay(); prevSlide(); startAutoPlay(); });

                    dots.forEach(d => {
                        d.addEventListener('click', function () {
                            stopAutoPlay();
                            showSlide(parseInt(this.getAttribute('data-index')));
                            startAutoPlay();
                        });
                    });

                    const carouselBox = document.getElementById('mainBannerCarousel');
                    if (carouselBox) {
                        carouselBox.addEventListener('mouseenter', stopAutoPlay);
                        carouselBox.addEventListener('mouseleave', startAutoPlay);
                    }

                    startAutoPlay();
                });
            </script>
        @endif
    @endif

    <!-- ==================== معرض صور الفعاليات وحفلات التخرج والتكريم (Events & Graduation Photo Gallery Slider) ==================== -->
    @php
        $eventGalleries = \App\Models\Banner::active()->events()->orderBy('sort_order')->latest()->get();
        if ($eventGalleries->isEmpty()) {
            $eventGalleries = \App\Models\Banner::active()->where(function ($q) {
                $q->where('category', 'event_gallery')->orWhere('title', 'like', '%حفل تخرج%');
            })->whereNotNull('image_path')->orderBy('sort_order')->latest()->get();
        }
    @endphp
    @if($eventGalleries->count() > 0)
        <section class="space-y-6 pt-2 pb-4" id="graduationGallerySection">
            <!-- ترويسة معرض الفعاليات والتخرج -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 px-2">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-purple-100 text-purple-900 text-xs font-black border border-purple-200 shadow-sm">
                        <span class="animate-pulse">🎓</span>
                        <span>أرشيف الذكريات واللحظات الخالدة • Moments of Excellence</span>
                        <span class="bg-purple-700 text-white px-2 py-0.5 rounded-full text-[10px] font-mono">
                            {{ $eventGalleries->count() }} فعاليات موثقة
                        </span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 flex items-center gap-2">
                        <span>معرض صور الفعاليات وحفلات التخرج والتكريم</span>
                        <span class="text-xl">📸</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
                        توثيق رسمي لمراسم تخرج دفعات دبلوم اللغة الإنجليزية الشامل، ورشات الترجمة المعتمدة، وتكريم أوائل اختبارات TOEFL بمكتب النجم الآسيوي (ASOTS).
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor()))
                        <a href="{{ route('admin.banners.index', ['category' => 'event_gallery']) }}" 
                            class="px-4 py-2 rounded-2xl bg-purple-50 text-purple-800 border border-purple-200 text-xs font-black hover:bg-purple-100 transition flex items-center gap-1.5 shadow-sm">
                            <span>➕</span>
                            <span>رفع صور حفل تخرج</span>
                        </a>
                    @endif

                    <a href="{{ route('announcements.index', ['tab' => 'events']) }}" class="text-xs font-bold text-[#0066A1] hover:underline flex items-center gap-1">
                        <span>تصفح الأرشيف كاملاً</span>
                        <span>←</span>
                    </a>

                    <!-- أزرار التنقل السريع للسلايدر -->
                    @if($eventGalleries->count() > 1)
                        <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-2xl border border-slate-200">
                            <button type="button" id="eventsPrevBtn" class="w-9 h-9 rounded-xl bg-white hover:bg-purple-600 hover:text-white text-slate-700 flex items-center justify-center font-black transition shadow-sm" aria-label="السابق">
                                ›
                            </button>
                            <button type="button" id="eventsNextBtn" class="w-9 h-9 rounded-xl bg-white hover:bg-purple-600 hover:text-white text-slate-700 flex items-center justify-center font-black transition shadow-sm" aria-label="التالي">
                                ‹
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- حاوية السلايدر المتحرك التفاعلي لصور الحفلات والفعاليات -->
            <div class="relative overflow-hidden rounded-3xl p-1" id="eventsCarouselViewport">
                <div class="flex transition-transform duration-500 ease-out" id="eventsCarouselTrack">
                    @foreach($eventGalleries as $idx => $ev)
                        <div class="events-carousel-slide flex-shrink-0 w-full sm:w-1/2 lg:w-1/3 p-2.5">
                            <div class="bg-white rounded-3xl border-2 border-slate-200 hover:border-purple-500 overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-300 flex flex-col justify-between h-full group">
                                
                                <!-- صورة الفعالية مع طبقات العرض وبادج الدفعة -->
                                <div class="relative h-60 sm:h-64 bg-slate-950 overflow-hidden cursor-pointer" onclick="openGalleryLightbox({{ json_encode($ev) }})">
                                    @if($ev->image_path)
                                        <img src="{{ asset($ev->image_path) }}" alt="{{ $ev->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-indigo-950 via-purple-950 to-slate-900 text-purple-200 p-6 text-center">
                                            <span class="text-5xl mb-2">🎓</span>
                                            <span class="text-xs font-bold">{{ $ev->title }}</span>
                                        </div>
                                    @endif
                                    
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-black/20 to-transparent"></div>

                                    <!-- بادج الدفعة في الأعلى -->
                                    <div class="absolute top-3.5 right-3.5 flex items-center gap-1.5 flex-wrap">
                                        <span class="bg-purple-900/90 backdrop-blur-md text-white text-[10px] font-black px-3 py-1 rounded-full border border-purple-400/40 shadow">
                                            🎓 {{ $ev->batch_name ?: 'حفلة تخرج معتمدة' }}
                                        </span>
                                    </div>

                                    <!-- زر تكبير الصورة عند التحويم -->
                                    <button type="button" class="absolute top-3.5 left-3.5 w-8 h-8 rounded-full bg-black/60 hover:bg-purple-600 text-white flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition shadow" title="تكبير الصورة">
                                        🔍
                                    </button>

                                    <!-- بيانات التاريخ والمكان في أسفل الصورة -->
                                    <div class="absolute bottom-3 right-3 left-3 flex items-center justify-between text-[11px] text-purple-100 font-bold">
                                        <span class="flex items-center gap-1 bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded-lg border border-white/10">
                                            <span>📅</span>
                                            <span>{{ $ev->event_date ? $ev->event_date->translatedFormat('d F Y') : ($ev->created_at ? $ev->created_at->translatedFormat('d F Y') : 'مستمر') }}</span>
                                        </span>
                                        @if($ev->event_location)
                                            <span class="flex items-center gap-1 bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded-lg border border-white/10 truncate max-w-[150px]" title="{{ $ev->event_location }}">
                                                <span>📍</span>
                                                <span class="truncate">{{ $ev->event_location }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- محتوى البطاقة ونصوص التكريم -->
                                <div class="p-5 sm:p-6 space-y-3 flex-1 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <h3 class="font-black text-base text-slate-900 group-hover:text-purple-700 transition leading-snug line-clamp-2">
                                            {{ $ev->title }}
                                        </h3>

                                        @if($ev->subtitle)
                                            <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">
                                                {{ $ev->subtitle }}
                                            </p>
                                        @endif
                                    </div>

                                    <!-- أزرار الإجراءات السفلية -->
                                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                        <button type="button" onclick="openGalleryLightbox({{ json_encode($ev) }})" 
                                            class="flex-1 bg-purple-50 hover:bg-purple-100 text-purple-900 font-black text-xs py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-1.5 border border-purple-200">
                                            <span>معاينة الصورة 📸</span>
                                        </button>

                                        @if($ev->content || $ev->link_url)
                                            <a href="{{ $ev->link_url && $ev->link_url !== '#gallery' ? $ev->link_url : route('announcements.show', $ev->id) }}" 
                                                class="bg-slate-100 hover:bg-[#0066A1] hover:text-white text-slate-700 font-bold text-xs py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-1" title="تفاصيل كاملة">
                                                <span>التفاصيل 📄</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- مؤشرات السلايدر السفلية (Indicators) -->
            @if($eventGalleries->count() > 3)
                <div class="flex items-center justify-center gap-2 pt-2" id="eventsCarouselDots"></div>
            @endif
        </section>

        <!-- ================= مودال تكبير وعرض الصورة بدقة عالية (Lightbox Modal) ================= -->
        <!-- ================= مودال تكبير وعرض الصورة بدقة عالية مع تصفح كامل الألبوم (Album Lightbox Modal) ================= -->
        <div id="galleryLightboxModal" class="fixed inset-0 bg-slate-950/90 backdrop-blur-md z-50 flex items-center justify-center p-3 sm:p-6 hidden" onclick="closeGalleryLightbox()">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-5xl w-full overflow-hidden shadow-2xl relative text-white flex flex-col max-h-[92vh]" onclick="event.stopPropagation()">
                <!-- ترويسة المودال -->
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-800 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <span class="text-2xl">🎓</span>
                        <div>
                            <h3 class="font-black text-sm sm:text-base text-white line-clamp-1" id="lightboxTitle"></h3>
                            <span class="text-[11px] text-purple-300 block" id="lightboxMeta"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-purple-200 font-mono font-bold px-3 py-1 rounded-xl bg-purple-900/60 border border-purple-500/40" id="lightboxCounter">1 / 1</span>
                        <button type="button" onclick="closeGalleryLightbox()" class="w-10 h-10 rounded-2xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xl font-bold transition">✕</button>
                    </div>
                </div>
                
                <!-- عرض الصورة مع أزرار التقليب في الألبوم -->
                <div class="relative flex-1 bg-black flex items-center justify-center overflow-hidden min-h-[350px]">
                    <img id="lightboxImage" src="" alt="Event" class="w-full h-full max-h-[62vh] object-contain transition-opacity duration-200">
                    
                    <!-- أزرار التقليب بين صور الألبوم -->
                    <button type="button" onclick="lightboxPrevPhoto()" class="absolute right-3 top-1/2 -translate-y-1/2 w-12 h-12 rounded-2xl bg-black/60 hover:bg-purple-600 text-white flex items-center justify-center text-2xl font-black transition backdrop-blur-md border border-white/20 shadow-xl" aria-label="السابق" title="الصورة السابقة">
                        ›
                    </button>
                    <button type="button" onclick="lightboxNextPhoto()" class="absolute left-3 top-1/2 -translate-y-1/2 w-12 h-12 rounded-2xl bg-black/60 hover:bg-purple-600 text-white flex items-center justify-center text-2xl font-black transition backdrop-blur-md border border-white/20 shadow-xl" aria-label="التالي" title="الصورة التالية">
                        ‹
                    </button>
                </div>

                <!-- شريط معلومات الصورة وزر التفاصيل -->
                <div class="p-4 sm:p-5 bg-slate-900/95 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-t border-slate-800 text-xs shrink-0">
                    <p class="text-slate-300 leading-relaxed text-xs max-w-2xl" id="lightboxDesc"></p>
                    <div class="flex items-center gap-2">
                        <a id="lightboxActionBtn" href="#" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 text-white font-black px-6 py-2.5 rounded-xl shadow transition whitespace-nowrap">
                            قراءة التفاصيل كاملة 📄
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            let allEventsList = @json($eventGalleries);
            let lightboxCurrentIdx = 0;

            document.addEventListener('DOMContentLoaded', function () {
                const track = document.getElementById('eventsCarouselTrack');
                const prevBtn = document.getElementById('eventsPrevBtn');
                const nextBtn = document.getElementById('eventsNextBtn');
                const slides = document.querySelectorAll('.events-carousel-slide');
                const dotsContainer = document.getElementById('eventsCarouselDots');
                
                if (!track || slides.length === 0) return;

                let currentIndex = 0;
                let autoTimer = null;

                function getVisibleCount() {
                    if (window.innerWidth >= 1024) return 3;
                    if (window.innerWidth >= 640) return 2;
                    return 1;
                }

                function getMaxIndex() {
                    return Math.max(0, slides.length - getVisibleCount());
                }

                function updateCarousel() {
                    const visible = getVisibleCount();
                    const maxIdx = getMaxIndex();
                    if (currentIndex > maxIdx) currentIndex = maxIdx;
                    if (currentIndex < 0) currentIndex = 0;

                    const slideWidth = 100 / visible;
                    // RTL shift (in RTL, translateX positive moves forward)
                    const offset = currentIndex * slideWidth;
                    track.style.transform = `translateX(${offset}%)`;

                    renderDots();
                }

                function renderDots() {
                    if (!dotsContainer) return;
                    const maxIdx = getMaxIndex();
                    dotsContainer.innerHTML = '';
                    for (let i = 0; i <= maxIdx; i++) {
                        const dot = document.createElement('button');
                        dot.type = 'button';
                        dot.className = `h-2.5 rounded-full transition-all duration-300 ${i === currentIndex ? 'bg-purple-600 w-8' : 'bg-slate-300 hover:bg-slate-400 w-2.5'}`;
                        dot.addEventListener('click', () => {
                            stopAuto();
                            currentIndex = i;
                            updateCarousel();
                            startAuto();
                        });
                        dotsContainer.appendChild(dot);
                    }
                }

                function nextSlide() {
                    const maxIdx = getMaxIndex();
                    if (currentIndex >= maxIdx) {
                        currentIndex = 0;
                    } else {
                        currentIndex++;
                    }
                    updateCarousel();
                }

                function prevSlide() {
                    const maxIdx = getMaxIndex();
                    if (currentIndex <= 0) {
                        currentIndex = maxIdx;
                    } else {
                        currentIndex--;
                    }
                    updateCarousel();
                }

                function startAuto() {
                    if (!autoTimer && slides.length > getVisibleCount()) {
                        autoTimer = setInterval(nextSlide, 4500);
                    }
                }

                function stopAuto() {
                    if (autoTimer) {
                        clearInterval(autoTimer);
                        autoTimer = null;
                    }
                }

                if (nextBtn) nextBtn.addEventListener('click', () => { stopAuto(); nextSlide(); startAuto(); });
                if (prevBtn) prevBtn.addEventListener('click', () => { stopAuto(); prevSlide(); startAuto(); });

                const viewport = document.getElementById('eventsCarouselViewport');
                if (viewport) {
                    viewport.addEventListener('mouseenter', stopAuto);
                    viewport.addEventListener('mouseleave', startAuto);
                }

                window.addEventListener('resize', () => {
                    updateCarousel();
                });

                updateCarousel();
                startAuto();
            });

            function renderLightboxItem() {
                const item = allEventsList[lightboxCurrentIdx];
                if (!item) return;

                document.getElementById('lightboxTitle').innerText = item.title || 'حفل تخرج';
                let meta = (item.batch_name ? '🏷️ ' + item.batch_name + ' • ' : '') + 
                           (item.event_date ? '📅 ' + item.event_date.substring(0, 10) + ' ' : '') +
                           (item.event_location ? '• 📍 ' + item.event_location : '');
                document.getElementById('lightboxMeta').innerText = meta;
                document.getElementById('lightboxCounter').innerText = (lightboxCurrentIdx + 1) + ' / ' + allEventsList.length;
                document.getElementById('lightboxImage').src = item.image_path ? '/' + item.image_path : '';
                document.getElementById('lightboxDesc').innerText = item.subtitle || (item.content ? item.content.substring(0, 200) : '');
                
                const actionBtn = document.getElementById('lightboxActionBtn');
                actionBtn.href = '/announcements/' + item.id;
            }

            function openGalleryLightbox(eventItem) {
                lightboxCurrentIdx = allEventsList.findIndex(e => e.id === eventItem.id);
                if (lightboxCurrentIdx === -1) lightboxCurrentIdx = 0;
                renderLightboxItem();
                document.getElementById('galleryLightboxModal').classList.remove('hidden');
            }

            function lightboxNextPhoto() {
                if (allEventsList.length <= 1) return;
                lightboxCurrentIdx = (lightboxCurrentIdx + 1) % allEventsList.length;
                renderLightboxItem();
            }

            function lightboxPrevPhoto() {
                if (allEventsList.length <= 1) return;
                lightboxCurrentIdx = (lightboxCurrentIdx - 1 + allEventsList.length) % allEventsList.length;
                renderLightboxItem();
            }

            function closeGalleryLightbox() {
                document.getElementById('galleryLightboxModal').classList.add('hidden');
            }

            document.addEventListener('keydown', function(e) {
                const modal = document.getElementById('galleryLightboxModal');
                if (!modal.classList.contains('hidden')) {
                    if (e.key === 'Escape') {
                        closeGalleryLightbox();
                    } else if (e.key === 'ArrowLeft') {
                        lightboxNextPhoto();
                    } else if (e.key === 'ArrowRight') {
                        lightboxPrevPhoto();
                    }
                }
            });
        </script>
    @endif

    <!-- ==================== 2. شريط الثقة والمميزات الحصرية (Trust Strip) ==================== -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-start gap-4">
            <div class="w-12 h-12 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0 shadow-inner">
                🎯
            </div>
            <div>
                <h3 class="font-black text-sm text-slate-900 mb-1">جلسات 1-on-1 فردية</h3>
                <p class="text-xs text-slate-500 leading-relaxed">{{ site_setting('session_duration_value', '25 دقيقة') }} مكثفة من التفاعل المباشر مع المعلم لضمان أقصى تركيز وسرعة تعلم.</p>
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
            <span class="text-xs font-bold text-[#0066A1] bg-sky-50 px-3 py-1 rounded-full uppercase tracking-wider border border-sky-200">
                مجالات الاختصاص والتميز
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">أعمدة مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS)</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">حلول متكاملة تجمع بين التعليم التفاعلي والترجمة المعتمدة والاستشارات الدولية</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($info['services'] as $srv)
                @php
                    $isPrimaryFoundation = ($srv['id'] === 'english_foundation');
                @endphp
                <div class="bg-white rounded-3xl border {{ $isPrimaryFoundation ? 'border-[#0066A1] ring-2 ring-[#0066A1]/20 shadow-md' : 'border-slate-200' }} p-8 shadow-sm hover:shadow-xl hover:border-[#0066A1] transition flex flex-col justify-between group relative overflow-hidden">
                    @if($isPrimaryFoundation)
                        <div class="absolute top-0 left-0 bg-gradient-to-r from-[#0066A1] to-[#F58220] text-white text-[10px] font-black px-4 py-1 rounded-br-2xl shadow">
                            الركيزة الأساسية للمنصة ⭐
                        </div>
                    @endif
                    <div>
                        <div class="w-14 h-14 {{ $isPrimaryFoundation ? 'bg-amber-50 border border-amber-200' : 'bg-slate-50 border border-slate-100' }} rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-inner group-hover:scale-110 transition">
                            {{ $srv['icon'] }}
                        </div>
                        <h3 class="text-lg font-black text-slate-900 mb-3">{{ $srv['title'] }}</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $srv['desc'] }}</p>
                    </div>

                    <div class="pt-6 mt-6 border-t border-slate-100">
                        @if($isPrimaryFoundation)
                            <div class="flex items-center justify-between gap-2">
                                <a href="{{ route('general_english') }}" class="text-xs font-black text-[#F58220] hover:text-[#d96e14] flex items-center gap-1">
                                    <span>سجل وابدأ من الصفر 🚀</span>
                                    <span>←</span>
                                </a>
                                <a href="{{ route('onboarding') }}" class="text-[11px] font-bold text-slate-500 hover:text-slate-800">
                                    اختبار القبول
                                </a>
                            </div>
                        @elseif($srv['id'] === 'arabic_for_non_native')
                            <div class="flex items-center justify-between gap-2">
                                <a href="{{ route('curriculum') }}" class="text-xs font-black text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                                    <span>المناهج والمسارات 📖</span>
                                    <span>←</span>
                                </a>
                                <a href="{{ route('onboarding') }}" class="text-[11px] font-bold text-slate-500 hover:text-slate-800">
                                    التسجيل
                                </a>
                            </div>
                        @elseif($srv['id'] === 'training')
                            <a href="{{ route('student.booking') }}" class="text-xs font-bold text-[#0066A1] hover:text-[#004e7c] flex items-center gap-1">
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
    <section class="bg-gradient-to-br from-[#003859] via-[#004e7c] to-[#0066A1] text-white rounded-3xl p-8 sm:p-12 shadow-2xl border border-[#0066A1]/40 space-y-8">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="text-xs font-black text-[#F58220] bg-[#F58220]/20 px-3.5 py-1 rounded-full border border-[#F58220]/40">
                الضمانة الأكاديمية والنزاهة المؤسسية
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white">القيادة الأكاديمية وقسم الجودة والتميز (AQED)</h2>
            <p class="text-xs sm:text-sm text-slate-200">
                تعليم لغوي وترجمة واستشارات يقودها أستاذ لسانيات مرموق بنموذج جودة حازم لا يقبل الادعاء بدون برهان.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
            <!-- بطاقة القيادة الأكاديمية: الدكتور/ فخر الدين ناجي ردمان عبده الصوفي -->
            <div class="bg-[#002b45]/85 rounded-2xl border border-white/15 p-6 sm:p-8 flex flex-col justify-between space-y-6 hover:border-[#F58220]/60 transition shadow-lg">
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-[#0066A1] flex items-center justify-center text-3xl font-black shadow-lg text-white border border-white/20">
                            👨‍🏫
                        </div>
                        <div>
                            <span class="text-[11px] font-black text-[#F58220] block uppercase">مؤسس ومدير ASOTS</span>
                            <h3 class="text-lg sm:text-xl font-black text-white">{{ $info['leadership']['name_ar'] }}</h3>
                            <span class="text-xs text-amber-200 font-bold block">{{ $info['leadership']['title_ar'] }}</span>
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
            <div class="bg-[#002b45]/85 rounded-2xl border border-white/15 p-6 sm:p-8 flex flex-col justify-between space-y-6 hover:border-[#F58220]/60 transition shadow-lg">
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-[#004e7c] flex items-center justify-center text-3xl font-black shadow-lg text-white border border-white/20">
                            🛡️
                        </div>
                        <div>
                            <span class="text-[11px] font-black text-[#F58220] block uppercase">{{ $info['aqed']['name_en'] }}</span>
                            <h3 class="text-lg sm:text-xl font-black text-white">{{ $info['aqed']['name_ar'] }}</h3>
                            <span class="text-xs font-mono font-bold text-amber-200 block">المبدأ: {{ $info['aqed']['motto_ar'] }}</span>
                        </div>
                    </div>

                    <p class="text-xs text-slate-200 leading-relaxed">
                        نظام تدقيق أكاديمي وإداري مستقل يراقب خطط الدروس، وتقارير المعلمين بعد كل حصة، وسجلات الحضور والتقييم، مع دورة تحسين مستمر مبنية بالكامل على البيانات.
                    </p>

                    <!-- كبسولات توزيع الـ 100 درجة -->
                    <div class="bg-[#001c2e] p-3.5 rounded-xl border border-white/10 space-y-2">
                        <span class="text-[11px] font-black text-slate-300 block">توزيع درجات المقرر المعتمد (100 نقطة):</span>
                        <div class="grid grid-cols-3 gap-2 text-center text-[10px] font-bold">
                            <div class="bg-[#002b45] p-1.5 rounded-lg border border-white/10 text-sky-200">
                                <span class="block text-white font-mono text-xs">10</span>
                                اختبارات قصيرة
                            </div>
                            <div class="bg-[#002b45] p-1.5 rounded-lg border border-white/10 text-[#F58220]">
                                <span class="block text-white font-mono text-xs">10</span>
                                استماع وتحدث
                            </div>
                            <div class="bg-[#002b45] p-1.5 rounded-lg border border-white/10 text-emerald-300">
                                <span class="block text-white font-mono text-xs">50</span>
                                حضور ومشاركة
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-white/15 flex items-center justify-between">
                    <a href="{{ route('quality') }}" class="text-xs font-black text-[#F58220] hover:text-amber-200 flex items-center gap-1.5">
                        <span>معايير الجودة وتوزيع الدرجات الكامل</span>
                        <span>←</span>
                    </a>
                    <a href="{{ route('contact') }}" class="text-[11px] font-bold text-slate-300 hover:text-white">
                        شؤون الطلاب
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== 4. رحلة التعلم في 4 خطوات (ASOTS Learning Journey) ==================== -->
    <section class="bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1] text-white rounded-3xl p-8 sm:p-12 shadow-2xl border border-[#0066A1]/40">
        <div class="text-center max-w-xl mx-auto mb-10">
            <span class="text-xs font-black text-[#F58220] bg-[#F58220]/20 px-3.5 py-1 rounded-full border border-[#F58220]/40">
                تجربة التعلم الذكي (ASOTS Model)
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-white mt-2">كيف تدرس معنا خطوة بخطوة؟</h2>
            <p class="text-xs text-slate-200 mt-1">رحلة سهلة وسريعة تمكنك من بدء التحدث مع معلمك في دقائق</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-right">
            <div class="bg-[#002b45]/85 p-6 rounded-2xl border border-white/15 relative group hover:border-[#F58220] transition shadow-lg">
                <span class="text-3xl font-black text-[#F58220] font-mono block mb-3">01</span>
                <h4 class="font-bold text-sm text-white mb-2">اختبار القبول وتحديد المستوى</h4>
                <p class="text-xs text-slate-200 leading-relaxed">تقييم سريع للقواعد مع فحص دقيق لمخارج الحروف بالذكاء الاصطناعي لتحديد نقطة انطلاقك الصحيحة.</p>
            </div>

            <div class="bg-[#002b45]/85 p-6 rounded-2xl border border-white/15 relative group hover:border-[#F58220] transition shadow-lg">
                <span class="text-3xl font-black text-[#F58220] font-mono block mb-3">02</span>
                <h4 class="font-bold text-sm text-white mb-2">شحن باقة الحصص وسند القبض</h4>
                <p class="text-xs text-slate-200 leading-relaxed">اشترك في الباقة الملائمة لتحصل على سند قبض رسمي مرقم ومفقط آلياً وفق المعايير المحاسبية المعتمدة.</p>
            </div>

            <div class="bg-[#002b45]/85 p-6 rounded-2xl border border-white/15 relative group hover:border-[#F58220] transition shadow-lg">
                <span class="text-3xl font-black text-[#F58220] font-mono block mb-3">03</span>
                <h4 class="font-bold text-sm text-white mb-2">حجز 25 دقيقة في الفصل الحي</h4>
                <p class="text-xs text-slate-200 leading-relaxed">اختر معلمك المفضل والموعد الملائم لتدخل غرفة الفصل التفاعلي المباشر (Air Class) من أي متصفح.</p>
            </div>

            <div class="bg-[#002b45]/85 p-6 rounded-2xl border border-white/15 relative group hover:border-[#F58220] transition shadow-lg">
                <span class="text-3xl font-black text-[#F58220] font-mono block mb-3">04</span>
                <h4 class="font-bold text-sm text-white mb-2">تقرير الحصة عبر الواتساب</h4>
                <p class="text-xs text-slate-200 leading-relaxed">فور انتهاء الجلسة، يصلك تقرير تفصيلي مع مكافآت النجوم والواجب المنزلي والتسجيل لمراجعة تقدمك.</p>
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
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">نخبة معلمي مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات</h2>
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
    <section class="bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1] text-white rounded-3xl p-6 sm:p-10 shadow-2xl border border-[#0066A1]/40 space-y-6 overflow-hidden relative">
        <!-- شريط الرأس والأزرار -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-white/15 pb-6">
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-2 bg-[#F58220]/20 text-[#F58220] text-xs px-3.5 py-1 rounded-full font-black border border-[#F58220]/40">
                    <span>🌟</span>
                    <span>تجارب حقيقية ملهمة ومقالات المستفيدين</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-white">ماذا يقول طلابنا وأولياء الأمور والمعلمون؟</h2>
                <p class="text-xs text-slate-200">آراء ومقالات وتجارب حية من الميدان التعليمي تدور وتتجدد تلقائياً من اليمين إلى اليسار.</p>
            </div>

            <!-- زر المشاركة المخصص للمستخدمين -->
            <div>
                @auth
                    <button type="button" onclick="openTestimonialModal()" 
                        class="bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] hover:to-[#b85807] text-white font-black text-xs sm:text-sm px-6 py-3.5 rounded-2xl shadow-xl transition flex items-center gap-2 whitespace-nowrap transform hover:-translate-y-0.5 hover:shadow-[#F58220]/30">
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
            <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-16 sm:w-28 bg-gradient-to-l from-[#003859] via-[#003859]/80 to-transparent z-10"></div>
            <div class="pointer-events-none absolute left-0 top-0 bottom-0 w-16 sm:w-28 bg-gradient-to-r from-[#003859] via-[#003859]/80 to-transparent z-10"></div>

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
    <section class="bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1] text-white rounded-3xl p-8 sm:p-14 shadow-2xl border border-[#0066A1]/50 flex flex-col md:flex-row justify-between items-center gap-8">
        <div class="space-y-2 text-center md:text-right max-w-xl">
            <span class="inline-block bg-[#F58220] text-white text-xs font-black px-3.5 py-1 rounded-full shadow-sm">
                ابدأ رحلتك اليوم مجاناً 🎁
            </span>
            <h3 class="text-2xl sm:text-3xl font-black text-white">احجز حصتك التجريبية الفردية واختبر نطقك الآن</h3>
            <p class="text-xs sm:text-sm font-medium text-slate-100 leading-relaxed">
                انضم لمئات الطلاب الذين يطورون لغتهم الإنجليزية ويستعدون للتوفل مع نخبة المعلمين في مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS).
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 whitespace-nowrap">
            <a href="{{ route('onboarding') }}" class="bg-gradient-to-r from-[#F58220] to-[#d96e14] hover:from-[#d96e14] hover:to-[#b85807] text-white font-black text-xs sm:text-sm px-8 py-4 rounded-2xl shadow-xl transition transform hover:-translate-y-0.5">
                ابدأ التسجيل الذكي 1-on-1 🚀
            </a>
            <a href="{{ route('packages.index') }}" class="bg-white/10 hover:bg-white/20 text-white border border-white/25 font-bold text-xs sm:text-sm px-6 py-4 rounded-2xl shadow-md transition backdrop-blur-sm">
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
