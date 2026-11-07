@php
    $splashEnabled = site_setting('splash_enabled', '1') === '1';
    $isForcePreview = request()->has('preview_splash');
    $isHome = request()->routeIs('home') || request()->is('/');
    
    // إعدادات الشاشة من قاعدة البيانات
    $splashTitle = site_setting('splash_title_ar', 'مرحباً بكم في منصة مكتب النجم الآسيوي (ASOTS)');
    $splashSubtitle = site_setting('splash_subtitle_ar', config('asots.promise_ar', 'نُمكّن الإنسان. نصل الثقافات. ونصوغ مستقبلاً عالمياً واعداً.'));
    $splashDuration = (float) site_setting('splash_duration', '3');
    $splashBlur = site_setting('splash_blur', '2xl');
    $splashOpacityLight = site_setting('splash_opacity_light', '0.18');
    $splashOpacityDark = site_setting('splash_opacity_dark', '0.12');
    $isNightMode = ($activeThemeId ?? 'professional_navy') === 'night_mode';
    $customLogo = site_setting('site_logo');
    $logoSrc = $customLogo ? (str_starts_with($customLogo, 'http') ? $customLogo : asset($customLogo)) : asset('images/logo.png');
    $logoText = site_setting('logo_text', 'ASOTS');

    $blurClass = match($splashBlur) {
        'sm' => 'blur-xs',
        'md' => 'blur-sm',
        'lg' => 'blur-md',
        'xl' => 'blur-lg',
        '3xl' => 'blur-3xl',
        default => 'blur-2xl',
    };
@endphp

@if(($splashEnabled && $isHome) || $isForcePreview)
<div id="asotsSplashScreen" 
    class="fixed inset-0 z-[99999] flex flex-col items-center justify-center p-4 sm:p-6 overflow-hidden transition-all duration-700 ease-out opacity-100 select-none {{ $isNightMode ? 'bg-[#060b14]' : 'bg-[#f4f8fb]' }}"
    style="display: none;"
    role="dialog"
    aria-modal="true"
    aria-label="شاشة الترحيب الرسمية لمنصة ASOTS">

    <!-- طبقة الشعار كخلفية ضبابية ناعمة تحافظ على ألوان الشعار الأصلية وبدون تكرار في بقية الصفحات -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden flex items-center justify-center">
        <!-- صورة الشعار الأصلية مكبرة ومضببة بدقة متناهية وبألوانها الأصلية -->
        <div class="relative w-[500px] sm:w-[650px] lg:w-[850px] aspect-square flex items-center justify-center transform scale-110 {{ $blurClass }}"
             style="opacity: {{ $isNightMode ? $splashOpacityDark : $splashOpacityLight }};">
            <img src="{{ $logoSrc }}" alt="ASOTS Watermark Backdrop" class="w-full h-full object-contain filter drop-shadow-2xl">
        </div>

        <!-- تدرج ناعم يحافظ على هدوء الشاشة وحماية تباين النصوص -->
        <div class="absolute inset-0 {{ $isNightMode ? 'bg-gradient-to-b from-[#060b14]/70 via-transparent to-[#060b14]/85' : 'bg-gradient-to-b from-white/60 via-transparent to-white/70' }}"></div>
        <div class="absolute inset-0 bg-radial from-transparent {{ $isNightMode ? 'via-transparent to-[#060b14]' : 'via-transparent to-[#f4f8fb]' }}"></div>
    </div>

    <!-- البطاقة المركزية المتحركة بظهور تدريجي (Foreground Welcome Card) -->
    <div class="relative z-10 max-w-xl w-full text-center space-y-6 px-4 py-8 sm:p-10 rounded-3xl {{ $isNightMode ? 'bg-slate-900/80 border border-slate-700/60 shadow-2xl backdrop-blur-xl text-white' : 'bg-white/85 border border-sky-100 shadow-2xl backdrop-blur-xl text-slate-900' }} animate-splashFadeIn transform transition-all duration-500">
        
        <!-- الشعار الأصلي بدون تغيير ألوانه داخل إطار ناعم وأنيق -->
        <div class="inline-flex items-center justify-center p-3 rounded-3xl {{ $isNightMode ? 'bg-slate-800/90 ring-4 ring-[#F58220]/30' : 'bg-white ring-4 ring-[#F58220]/25' }} shadow-xl transform transition-transform duration-700 hover:scale-105">
            <img src="{{ $logoSrc }}" alt="ASOTS Original Logo" class="w-20 h-20 sm:w-24 sm:h-24 object-contain">
        </div>

        <!-- اسم المنصة والعنوان الترحيبي بالخط الديواني المعتمد -->
        <div class="space-y-2">
            <div class="inline-block px-3.5 py-1 rounded-full {{ $isNightMode ? 'bg-[#F58220]/20 text-[#F58220] border border-[#F58220]/40' : 'bg-amber-100 text-[#d96e14] border border-amber-300' }} text-xs font-black tracking-wider uppercase">
                ⭐ {{ $logoText }}
            </div>
            
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black leading-tight tracking-tight font-diwani {{ $isNightMode ? 'text-white' : 'text-[#003859]' }}">
                {{ $splashTitle }}
            </h1>

            <p class="text-xs sm:text-sm font-semibold max-w-lg mx-auto leading-relaxed {{ $isNightMode ? 'text-sky-200/90' : 'text-slate-600' }}">
                {{ $splashSubtitle }}
            </p>
        </div>

        <!-- شعار الاعتماد والركائز الأساسية -->
        <div class="pt-2 flex items-center justify-center gap-2 text-[11px] font-bold flex-wrap {{ $isNightMode ? 'text-slate-300' : 'text-slate-500' }}">
            <span class="flex items-center gap-1">🌐 تعليم لغوي دولي</span>
            <span>•</span>
            <span class="flex items-center gap-1">📜 ترجمة معتمدة</span>
            <span>•</span>
            <span class="flex items-center gap-1">🎯 تدريب واستشارات</span>
        </div>

        <!-- مؤشر التقدم الزمني والشريط التفاعلي للانتقال التلقائي السلس -->
        <div class="space-y-3 pt-3">
            <div class="w-full bg-slate-200/60 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                <div id="splashProgressBar" class="h-full bg-gradient-to-r from-[#0066A1] via-[#F58220] to-[#0066A1] rounded-full transition-all duration-100" style="width: 0%;"></div>
            </div>

            <div class="flex items-center justify-between text-[11px] text-slate-400">
                <span id="splashTimerText" class="font-mono">جاري فتح الواجهة...</span>
                <button type="button" onclick="dismissAsotsSplash()" class="text-[#F58220] hover:text-[#d96e14] font-black underline flex items-center gap-1 transition">
                    <span>دخول المنصة مباشرة 🚀</span>
                </button>
            </div>
        </div>

    </div>

</div>

<style>
    @keyframes splashFadeIn {
        0% {
            opacity: 0;
            transform: scale(0.92) translateY(12px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    .animate-splashFadeIn {
        animation: splashFadeIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>

<script>
    (function() {
        const splash = document.getElementById('asotsSplashScreen');
        const isForce = {{ $isForcePreview ? 'true' : 'false' }};
        const durationSec = {{ $splashDuration }};
        const hasSeen = sessionStorage.getItem('asots_splash_shown');

        if (splash && (!hasSeen || isForce)) {
            // إظهار الشاشة فوراً
            splash.style.display = 'flex';
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';

            const bar = document.getElementById('splashProgressBar');
            const totalMs = durationSec * 1000;
            const intervalMs = 25;
            let elapsed = 0;

            const timer = setInterval(function() {
                elapsed += intervalMs;
                const pct = Math.min(100, (elapsed / totalMs) * 100);
                if (bar) bar.style.width = pct + '%';

                if (elapsed >= totalMs) {
                    clearInterval(timer);
                    dismissAsotsSplash();
                }
            }, intervalMs);

            window._splashTimer = timer;
        }

        window.dismissAsotsSplash = function() {
            if (window._splashTimer) clearInterval(window._splashTimer);
            if (!splash) return;
            sessionStorage.setItem('asots_splash_shown', '1');
            splash.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(function() {
                splash.style.display = 'none';
                document.documentElement.style.overflow = '';
                document.body.style.overflow = '';
            }, 650);
        };
    })();
</script>
@endif
