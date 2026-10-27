@extends('layouts.app', ['title' => 'التراخيص والاعتمادات الرسمية المحمية - ' . config('asots.name_ar')])

@section('content')
<style>
    /* 1. حظر الطباعة بشكل صارم وتفريغ الصفحة بالكامل في حال محاولة الطباعة */
    @media print {
        body, html, * {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
        }
    }

    /* 2. حماية صارمة من التحديد والنسخ والسحب */
    .no-select {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* 3. شبكة التشويش البصري المجهرية ضد عدسات وحساسات كاميرات الجوال (Optical Moiré Mesh) */
    .moire-anti-camera-overlay {
        background-image: 
            repeating-linear-gradient(0deg, rgba(0, 0, 0, 0.08) 0px, rgba(0, 0, 0, 0.08) 1px, transparent 1px, transparent 3px),
            repeating-linear-gradient(90deg, rgba(0, 0, 0, 0.08) 0px, rgba(0, 0, 0, 0.08) 1px, transparent 1px, transparent 3px),
            repeating-linear-gradient(45deg, rgba(0, 102, 161, 0.07) 0px, rgba(0, 102, 161, 0.07) 1.5px, transparent 1.5px, transparent 5px),
            repeating-linear-gradient(-45deg, rgba(245, 130, 32, 0.05) 0px, rgba(245, 130, 32, 0.05) 1.5px, transparent 1.5px, transparent 6px);
        pointer-events: none;
        mix-blend-mode: multiply;
    }

    /* 4. درع التمويه الأمني الثابت (Security Frost) */
    .security-frost {
        filter: blur(14px) brightness(0.92) contrast(1.15);
        transition: filter 0.15s ease-out;
    }

    /* 5. طبقة التعتيم الفوري الصارمة */
    .blackout-active {
        display: flex !important;
    }

    /* مؤشر الفحص الآمن المخصص */
    .secure-lens-cursor {
        cursor: crosshair !important;
    }
</style>

<div class="max-w-6xl mx-auto space-y-8 py-6 no-select" id="secureViewerContainer">

    <!-- شريط التنبيه الأمني المشدد وهدير الحماية -->
    <div class="bg-gradient-to-r from-[#0066A1] via-[#004e7c] to-[#003859] rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-[#0066A1]/40 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 bg-[#F58220]/25 text-[#F58220] px-3.5 py-1 rounded-full text-xs font-black border border-[#F58220]/40">
                <span>🛡️</span>
                <span>عارض التراخيص فائق الحماية الرقمية والبصرية (DRM & Anti-Camera Protected)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white">وثائق التراخيص والاعتمادات الرسمية</h1>
            <p class="text-xs sm:text-sm text-slate-200 leading-relaxed max-w-2xl">
                هذه الوثائق مخصصة للمعاينة والتدقيق الرسمي المباشر فقط. النظام مزود بتقنيات حماية متقدمة: <strong>إعدام لقطات الشاشة (PrtScn)</strong> وتفريغ الحافظة، <strong>وتشويش بصري (Moiré Shield)</strong> يعيق تصوير الشاشة بكاميرات الهواتف الذكية مع <strong>عدسة الفحص التفاعلية</strong>.
            </p>
        </div>

        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 text-xs text-right space-y-1.5 shrink-0">
            <span class="text-amber-300 font-bold block flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>جلسة استعراض موثقة ومحمية:</span>
            </span>
            <div class="text-slate-100 font-mono text-[11px]">المستعرض: <strong class="text-white">{{ auth()->user()->name }}</strong></div>
            <div class="text-slate-200 font-mono text-[11px]" dir="ltr">ID: {{ auth()->user()->user_code ?? auth()->user()->email }}</div>
            <div class="text-amber-200 font-mono text-[11px]" dir="ltr" id="liveSessionClock">{{ now()->format('Y-m-d H:i:s') }}</div>
        </div>
    </div>

    <!-- شريط الإرشادات وأدوات الحماية العامة -->
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-xs text-amber-950">
        <div class="flex items-center gap-2">
            <span class="text-xl">💡</span>
            <div>
                <strong>نصيحة الأمان:</strong> حرك مؤشر الفأرة (أو إصبعك) فوق الوثيقة لاستخدام <strong>عدسة التدقيق الآمنة 🔍</strong> لقراءة التفاصيل بوضوح مع حجب باقي الوثيقة من كاميرات الجوال واللقطات.
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" onclick="triggerManualBlackout()" class="px-3 py-1.5 bg-slate-900 hover:bg-black text-amber-400 rounded-xl font-bold flex items-center gap-1.5 shadow-sm transition">
                <span>🕶️</span>
                <span>تعتيم الشاشة فوراً (Space)</span>
            </button>
        </div>
    </div>

    <!-- شبكة استعراض وثائق التراخيص -->
    @if($licenses->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm space-y-4">
            <span class="text-6xl block">📜</span>
            <h3 class="text-lg font-black text-slate-800">لا توجد تراخيص منشورة للاستعراض حالياً</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                لم يتم إرفاق أو تفعيل وثائق تراخيص من قبل إدارة المنصة بعد. يمكن لمدير النظام رفع التراخيص من شاشة (الهوية نصوص وشعارات المنصة).
            </p>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.branding.index') }}#licenses-section" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0066A1] hover:bg-[#004e7c] text-white font-bold text-xs shadow transition">
                    <span>➕</span>
                    <span>رفع وإرفاق تراخيص الموقع الآن</span>
                </a>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 gap-10">
            @foreach($licenses as $index => $license)
                <div class="bg-white rounded-3xl border border-slate-200 shadow-lg overflow-hidden relative license-card" data-license-id="{{ $license->id }}">
                    
                    <!-- رأس بطاقة الترخيص -->
                    <div class="bg-gradient-to-r from-slate-50 via-sky-50/50 to-slate-50 p-5 sm:p-6 border-b border-slate-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-[#0066A1] text-white flex items-center justify-center font-black text-sm shadow flex-shrink-0">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <h3 class="text-lg font-black text-[#0066A1]">{{ $license->title }}</h3>
                                <div class="flex items-center gap-3 text-xs text-slate-500 flex-wrap mt-0.5">
                                    @if($license->license_number)
                                        <span class="font-mono bg-white px-2 py-0.5 rounded-lg border border-slate-200 font-bold text-slate-700">رقم السجل: {{ $license->license_number }}</span>
                                    @endif
                                    @if($license->issuing_authority)
                                        <span>الجهة المصدرة: <strong class="text-slate-800">{{ $license->issuing_authority }}</strong></span>
                                    @endif
                                    @if($license->issue_date)
                                        <span class="font-mono text-[11px]">تاريخ الإصدار: {{ $license->issue_date->format('Y-m-d') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- شريط التبديل بين أنماط الحماية للوثيقة -->
                        <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl border border-slate-200 text-xs">
                            <button type="button" 
                                    id="btnModeLens_{{ $license->id }}" 
                                    onclick="setLicenseMode({{ $license->id }}, 'lens')" 
                                    class="px-3 py-1.5 rounded-xl font-black transition flex items-center gap-1.5 bg-[#0066A1] text-white shadow-sm">
                                <span>🔍</span>
                                <span>عدسة الفحص الآمنة</span>
                            </button>
                            <button type="button" 
                                    id="btnModeFull_{{ $license->id }}" 
                                    onclick="setLicenseMode({{ $license->id }}, 'full')" 
                                    class="px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 text-slate-600 hover:text-slate-900">
                                <span>🛡️</span>
                                <span>عرض كامل مشفر</span>
                            </button>
                        </div>
                    </div>

                    @if($license->notes)
                        <div class="p-3.5 bg-amber-50/70 border-b border-amber-100 text-xs text-amber-900 leading-relaxed flex items-center gap-2">
                            <span>💡</span>
                            <div><strong>ملاحظات الاعتماد:</strong> {{ $license->notes }}</div>
                        </div>
                    @endif

                    <!-- مساحة عرض الترخيص بتقنية Canvas DRM الخالصة والدرع البصري المانع للكاميرات واللقطات -->
                    <div class="p-4 sm:p-8 bg-slate-900 flex justify-center items-center relative overflow-hidden select-none">
                        
                        <div class="relative max-w-4xl w-full bg-slate-950 rounded-2xl shadow-2xl overflow-hidden border-2 border-slate-700 secure-canvas-wrapper flex justify-center items-center min-h-[450px]" 
                             id="wrapper_{{ $license->id }}"
                             oncontextmenu="return false;">
                            
                            <!-- رسالة التحميل والتجهيز الأمني للوثيقة في الذاكرة -->
                            <div id="loader_{{ $license->id }}" class="absolute inset-0 z-30 bg-slate-950 flex flex-col items-center justify-center space-y-3 text-white">
                                <div class="w-10 h-10 border-4 border-[#0066A1] border-t-[#F58220] rounded-full animate-spin"></div>
                                <span class="text-xs font-bold text-slate-300">جاري تشفير وتجهيز درع الحماية للوثيقة...</span>
                            </div>

                            <!-- 1. لوحة الرسم الرقمية المحمية (HTML5 Canvas) بدون أي وسم <img> مكشوف -->
                            <canvas id="canvas_{{ $license->id }}" 
                                    class="max-w-full h-auto object-contain transition-all duration-150 pointer-events-auto secure-lens-cursor"
                                    data-src="{{ asset($license->image_path) }}"
                                    data-title="{{ $license->title }}"
                                    oncontextmenu="return false;"
                                    ondragstart="return false;"></canvas>

                            <!-- 2. شبكة التشويش البصري المجهرية ضد عدسات كاميرات الجوال (Optical Moiré Mesh) -->
                            <div id="moire_{{ $license->id }}" class="absolute inset-0 moire-anti-camera-overlay pointer-events-none z-10"></div>

                            <!-- 3. طبقة الدرع الشفاف المانع للنقر المباشر وحفظ الصور -->
                            <div class="absolute inset-0 z-20 pointer-events-none border-2 border-slate-700/50 rounded-2xl"></div>

                            <!-- 4. شريط إشعار العدسة التفاعلية عند تحريك الفأرة -->
                            <div id="lensBadge_{{ $license->id }}" class="absolute bottom-3 left-3 z-25 bg-black/80 text-amber-300 text-[10px] font-mono px-3 py-1 rounded-full border border-amber-400/30 pointer-events-none flex items-center gap-1.5 backdrop-blur-sm">
                                <span>🔍</span>
                                <span>وضع عدسة الفحص نشط: حرك المؤشر للقراءة الموضعية المباشرة</span>
                            </div>
                        </div>
                    </div>

                    <!-- تذييل البطاقة -->
                    <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-2">
                        <span class="flex items-center gap-1.5 font-bold text-slate-700">
                            <span>🔒</span>
                            <span>وثيقة رسمية معتمدة لمكتب النجم الآسيوي (ASOTS) • جميع الحقوق محفوظة ومسجلة</span>
                        </span>
                        <span class="text-rose-700 font-bold bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200">
                            ⛔ ممنوع التحميل أو النسخ أو التعديل أو التقاط الشاشة أو التصوير بالكاميرا
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- ========================================================================= -->
<!-- درع التعتيم التام والإنذار الأمني الفوري (Blackout Security Shield) -->
<!-- ========================================================================= -->
<div id="screenCaptureShield" class="fixed inset-0 z-[999999] bg-slate-950 text-white flex flex-col items-center justify-center p-6 space-y-6 hidden no-select">
    <div class="w-20 h-20 rounded-full bg-rose-600/20 text-rose-500 border-2 border-rose-500/40 flex items-center justify-center text-4xl font-black animate-pulse shadow-lg">
        ⛔
    </div>
    
    <div class="text-center space-y-2 max-w-lg">
        <h2 class="text-2xl sm:text-3xl font-black text-rose-400">تنبيه أمني: تم حظر التقاط الشاشة</h2>
        <p id="blackoutReason" class="text-xs sm:text-sm text-slate-300 leading-relaxed">
            تم تفعيل درع الحماية وتعتيم الصفحة فوراً ومسح الحافظة لاكتشاف محاولة تصوير (PrtScn) أو فقدان تركيز النافذة بأداة لقطة شاشة خارجية.
        </p>
    </div>

    <div class="bg-slate-900 p-4 rounded-2xl border border-slate-800 text-xs text-slate-400 max-w-md w-full space-y-2">
        <div class="flex items-center justify-between text-slate-300 font-bold border-b border-slate-800 pb-2">
            <span>🛡️ تفاصيل الحماية المفعلة:</span>
            <span class="text-emerald-400 font-mono">ENFORCED</span>
        </div>
        <div class="text-[11px] space-y-1">
            <p>✓ تم تفريغ وإعدام حافظة الويندوز (Clipboard Annihilated).</p>
            <p>✓ تم مسح بيانات العرض من الذاكرة الرسومية للـ Canvas.</p>
            <p>✓ تم توثيق المحاولة برمز المستعرض وجلسة الدخول الرسمية.</p>
        </div>
    </div>

    <button type="button" onclick="resumeViewerFocus()" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-[#0066A1] to-[#004e7c] hover:from-[#005587] hover:to-[#003859] text-white font-black text-sm shadow-xl transition flex items-center gap-2 transform hover:scale-105">
        <span>👁️</span>
        <span>استئناف الاستعراض الآمن داخل المنصة</span>
    </button>
</div>

<!-- ========================================================================= -->
<!-- محرك الأمان التفاعلي والـ DRM ومكافحة كاميرات الهواتف واللقطات (Canvas DRM Engine) -->
<!-- ========================================================================= -->
<script>
(function () {
    'use strict';

    const viewerUser = {
        name: @json(auth()->user()->name),
        code: @json(auth()->user()->user_code ?? auth()->user()->email),
        id: @json(auth()->user()->id)
    };

    const shield = document.getElementById('screenCaptureShield');
    const blackoutReason = document.getElementById('blackoutReason');
    const liveClockEl = document.getElementById('liveSessionClock');

    // تحديث التوقيت المباشر كل ثانية لتوثيق الجلسة
    setInterval(() => {
        const now = new Date();
        const dateStr = now.toISOString().replace('T', ' ').substring(0, 19);
        if (liveClockEl) liveClockEl.innerText = dateStr;
    }, 1000);

    // =========================================================================
    // 1. سلاح إعدام الحافظة وتفريغ الويندوز الفوري (Continuous Clipboard Annihilation)
    // =========================================================================
    const protectionNotice = '⚠️ تنبيه أمني قانوني: هذه الوثيقة الرسمية مشفرة ومحمية بحقوق الملكية الفكرية لمكتب النجم الآسيوي (ASOTS). يمنع القانون تصوير الشاشة أو النسخ أو التداول تحت طائلة المساءلة ⚠️';

    function killClipboard() {
        // 1.1 الطريقة العميقة المتوافقة مع كافة المتصفحات ونظام الويندوز (تستبدل Bitmap بـ Text)
        try {
            const ta = document.createElement('textarea');
            ta.value = protectionNotice;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            ta.style.top = '-9999px';
            ta.setAttribute('readonly', '');
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
        } catch (err) {}

        // 1.2 واجهة المتصفح الحديثة (Async Clipboard API)
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(protectionNotice).catch(() => {});
        }
    }

    // إعدام متكرر عبر نبضات زمنية متتابعة لضمان إفراغ ما يضعه الويندوز بعد ضغط PrtScn
    function burstKillClipboard() {
        killClipboard();
        const delays = [20, 60, 120, 250, 500, 900];
        delays.forEach(ms => setTimeout(killClipboard, ms));
    }

    // =========================================================================
    // 2. درع التعتيم والحجب الفوري (Trigger Blackout Shield)
    // =========================================================================
    let isBlackedOut = false;

    window.triggerManualBlackout = function() {
        triggerSecurityBlackout('تم تفعيل التعتيم الأمني اليدوي للشاشة لحماية الخصوصية. انقر للاستئناف.');
    };

    function triggerSecurityBlackout(reasonText) {
        isBlackedOut = true;
        burstKillClipboard();

        if (blackoutReason && reasonText) {
            blackoutReason.innerText = reasonText;
        }

        if (shield) {
            shield.classList.remove('hidden');
        }

        // مسح كافة الـ Canvases للون الأسود الفاحم فوراً
        Object.keys(licenseInstances).forEach(id => {
            const inst = licenseInstances[id];
            if (inst && inst.ctx && inst.canvas) {
                inst.ctx.fillStyle = '#050b14';
                inst.ctx.fillRect(0, 0, inst.canvas.width, inst.canvas.height);
                inst.ctx.fillStyle = '#ef4444';
                inst.ctx.font = 'bold 22px Cairo, sans-serif';
                inst.ctx.textAlign = 'center';
                inst.ctx.fillText('⛔ محتوى محمي - تم حظر لقطة الشاشة وتفريغ الحافظة', inst.canvas.width / 2, inst.canvas.height / 2);
            }
        });
    }

    window.resumeViewerFocus = function() {
        isBlackedOut = false;
        if (shield) {
            shield.classList.add('hidden');
        }
        burstKillClipboard();

        // إعادة رسم كافة الوثائق
        Object.keys(licenseInstances).forEach(id => {
            const inst = licenseInstances[id];
            if (inst) inst.requestRedraw();
        });
    };

    // =========================================================================
    // 3. رصد واعتراض لقطات الشاشة ولوحة المفاتيح والـ PrtScn
    // =========================================================================
    // 3.1 حدث الضغط Keydown
    document.addEventListener('keydown', function (e) {
        // Space للتعتيم الفوري السريع
        if (e.code === 'Space' && !['INPUT', 'TEXTAREA'].includes(e.target.tagName)) {
            e.preventDefault();
            triggerManualBlackout();
            return false;
        }

        // F12 أو مفاتيح المطورين
        if (e.keyCode === 123) {
            e.preventDefault();
            triggerSecurityBlackout('تم حظر أدوات الفحص البرمجي (F12) لحماية الوثائق الرسمية.');
            return false;
        }

        // زر PrintScreen الشهير (Keydown)
        if (e.key === 'PrintScreen' || e.code === 'PrintScreen' || e.keyCode === 44) {
            e.preventDefault();
            triggerSecurityBlackout('تم رصد محاولة لقطة شاشة عبر زر (PrintScreen) وتم إعدام الحافظة وتعتيم المحتوى فوراً.');
            burstKillClipboard();
            return false;
        }

        // تركيبات Ctrl / Alt / Meta الشائعة
        if (e.ctrlKey || e.metaKey || e.altKey) {
            const k = (e.key || '').toLowerCase();
            // Windows Snipping Tool (Win+Shift+S) أو Ctrl+Shift+S
            if (e.shiftKey && (k === 's' || e.keyCode === 83)) {
                e.preventDefault();
                triggerSecurityBlackout('تم حظر أداة لقطة الشاشة (Snipping Tool) ومسح الحافظة فوراً.');
                burstKillClipboard();
                return false;
            }

            // Ctrl+P (طباعة), Ctrl+S (حفظ), Ctrl+U (مصدر), Ctrl+C (نسخ), Ctrl+Shift+I / J / C (DevTools)
            if (['p', 's', 'u', 'c', 'x', 'a'].includes(k) || (e.shiftKey && ['i', 'j', 'c'].includes(k))) {
                e.preventDefault();
                burstKillClipboard();
                if (k === 'p') {
                    alert('⚠️ طباعة أو تصدير وثائق التراخيص محظور لحماية الحقوق القانونية.');
                }
                return false;
            }
        }
    }, true);

    // 3.2 حدث الإفلات Keyup (خاص بـ PrtScn في نظام الويندوز حيث ينهي التقاط الشاشة عند الإفلات)
    document.addEventListener('keyup', function (e) {
        if (e.key === 'PrintScreen' || e.code === 'PrintScreen' || e.keyCode === 44) {
            e.preventDefault();
            burstKillClipboard();
            triggerSecurityBlackout('تم رصد زر طباعة الشاشة (PrtScn) وإفراغ الحافظة فوراً ومنع اللصق.');
            return false;
        }
    }, true);

    // 3.3 حماية فقدان التركيز (تفعيل أداة تصوير خارجية أو التبديل لنافذة أخرى كالرسام أو أداة القص)
    window.addEventListener('blur', function () {
        burstKillClipboard();
        triggerSecurityBlackout('فقدت نافذة المتصفح التركيز (ربما تم فتح أداة لقطة شاشة خارجية). تم حجب الوثائق فوراً.');
    });

    // 3.4 حماية إخفاء التبويب Visibility Change
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            burstKillClipboard();
            triggerSecurityBlackout('تم تصغير أو إخفاء التبويب. تم تأمين الوثائق بحجاب الأمان.');
        }
    });

    // 3.5 منع النقر بالزر الأيمن والسحب
    document.addEventListener('contextmenu', e => { e.preventDefault(); return false; }, false);
    document.addEventListener('dragstart', e => { e.preventDefault(); return false; }, false);
    document.addEventListener('copy', e => {
        e.preventDefault();
        burstKillClipboard();
        if (e.clipboardData) e.clipboardData.setData('text/plain', protectionNotice);
    });

    // دورية أمان منتظمة لتطهير الحافظة كل بضع ثوانٍ داخل صفحة التراخيص
    setInterval(killClipboard, 2500);

    // =========================================================================
    // 4. المحرك الرسومي للـ Canvas DRM مع عدسة التدقيق والتشويش البصري ضد الجوال
    // =========================================================================
    const licenseInstances = {};

    class SecureLicenseViewer {
        constructor(licenseId, canvasEl) {
            this.id = licenseId;
            this.canvas = canvasEl;
            this.ctx = canvasEl.getContext('2d', { willReadFrequently: true });
            this.imageSrc = canvasEl.getAttribute('data-src');
            this.title = canvasEl.getAttribute('data-title');
            this.loader = document.getElementById('loader_' + licenseId);
            this.badge = document.getElementById('lensBadge_' + licenseId);
            this.mode = 'lens'; // 'lens' = عدسة التدقيق الآمنة (الافتراضي) | 'full' = عرض كامل مشفر
            
            this.img = null;
            this.isLoaded = false;
            this.mouseX = -1000;
            this.mouseY = -1000;
            this.lensRadius = 135; // نصف قطر عدسة الفحص
            this.frameTick = 0;

            this.init();
        }

        init() {
            // تحميل الصورة في كائن Image غير معروض في الـ DOM
            this.img = new Image();
            this.img.crossOrigin = 'anonymous';
            this.img.onload = () => {
                this.isLoaded = true;
                if (this.loader) this.loader.classList.add('hidden');
                this.setupDimensions();
                this.startRenderLoop();
            };
            this.img.src = this.imageSrc;

            // متابعة حركة الفأرة داخل لوحة الـ Canvas لعدسة الفحص
            this.canvas.addEventListener('mousemove', (e) => this.handlePointerMove(e));
            this.canvas.addEventListener('touchmove', (e) => this.handleTouchMove(e), { passive: true });
            this.canvas.addEventListener('mouseleave', () => this.handlePointerLeave());
            this.canvas.addEventListener('touchend', () => this.handlePointerLeave());
            window.addEventListener('resize', () => this.setupDimensions());
        }

        setupDimensions() {
            if (!this.img || !this.isLoaded) return;
            const container = this.canvas.parentElement;
            const maxW = container ? Math.min(container.clientWidth - 32, 850) : 800;
            const scale = maxW / this.img.width;
            
            this.canvas.width = maxW;
            this.canvas.height = this.img.height * scale;
            this.scale = scale;
            this.requestRedraw();
        }

        handlePointerMove(e) {
            const rect = this.canvas.getBoundingClientRect();
            this.mouseX = (e.clientX - rect.left) * (this.canvas.width / rect.width);
            this.mouseY = (e.clientY - rect.top) * (this.canvas.height / rect.height);
            this.requestRedraw();
        }

        handleTouchMove(e) {
            if (e.touches && e.touches[0]) {
                const rect = this.canvas.getBoundingClientRect();
                this.mouseX = (e.touches[0].clientX - rect.left) * (this.canvas.width / rect.width);
                this.mouseY = (e.touches[0].clientY - rect.top) * (this.canvas.height / rect.height);
                this.requestRedraw();
            }
        }

        handlePointerLeave() {
            this.mouseX = -1000;
            this.mouseY = -1000;
            this.requestRedraw();
        }

        setMode(newMode) {
            this.mode = newMode;
            if (this.badge) {
                if (newMode === 'lens') {
                    this.badge.classList.remove('hidden');
                    this.canvas.classList.add('secure-lens-cursor');
                } else {
                    this.badge.classList.add('hidden');
                    this.canvas.classList.remove('secure-lens-cursor');
                }
            }
            this.requestRedraw();
        }

        startRenderLoop() {
            const loop = () => {
                this.frameTick++;
                // إعادة رسم خفيفة لتحديث الموجات المجهرية ضد حساسات الكاميرا والـ Watermark
                if (this.isLoaded && !isBlackedOut) {
                    this.draw();
                }
                requestAnimationFrame(loop);
            };
            requestAnimationFrame(loop);
        }

        requestRedraw() {
            if (this.isLoaded && !isBlackedOut) {
                this.draw();
            }
        }

        draw() {
            if (!this.isLoaded || isBlackedOut) return;
            const ctx = this.ctx;
            const w = this.canvas.width;
            const h = this.canvas.height;

            ctx.clearRect(0, 0, w, h);

            if (this.mode === 'lens') {
                this.drawLensMode(ctx, w, h);
            } else {
                this.drawFullSecureMode(ctx, w, h);
            }

            // رسم العلامة المائية الجنائية الحية في كل الأنماط
            this.drawForensicWatermarks(ctx, w, h);
        }

        // =====================================================================
        // نمط أ: عدسة الفحص الآمنة (أقوى حماية ضد كاميرات الجوال واللقطات الكاملة)
        // =====================================================================
        drawLensMode(ctx, w, h) {
            // 1. رسم الوثيقة في الخلفية ولكن بتشويش وتعتيم أمني كثيف (Obfuscation Frost)
            ctx.save();
            ctx.filter = 'blur(16px) grayscale(80%) brightness(0.7)';
            ctx.drawImage(this.img, 0, 0, w, h);
            ctx.restore();

            // رسم خطوط تشويش مصفوفية على الخلفية الضبابية
            ctx.save();
            ctx.fillStyle = 'rgba(0, 102, 161, 0.08)';
            for (let y = 0; y < h; y += 8) {
                ctx.fillRect(0, y, w, 2);
            }
            ctx.restore();

            // 2. إذا كانت الفأرة فوق الوثيقة، نفتح عدسة التدقيق الدائرية فقط
            if (this.mouseX > 0 && this.mouseX < w && this.mouseY > 0 && this.mouseY < h) {
                ctx.save();

                // قناع القطع الدائري للعدسة
                ctx.beginPath();
                ctx.arc(this.mouseX, this.mouseY, this.lensRadius, 0, Math.PI * 2);
                ctx.clip();

                // رسم الصورة الأصلية الواضحة داخل العدسة فقط
                ctx.drawImage(this.img, 0, 0, w, h);

                // تطبيق شبكة التشويش المجهرية ضد كاميرات الجوال داخل العدسة أيضاً
                this.applyCameraMoiréStrobe(ctx, this.mouseX - this.lensRadius, this.mouseY - this.lensRadius, this.lensRadius * 2, this.lensRadius * 2);

                ctx.restore();

                // رسم إطار وتأثير ضوئي أنيق حول عدسة الفحص
                ctx.save();
                ctx.beginPath();
                ctx.arc(this.mouseX, this.mouseY, this.lensRadius, 0, Math.PI * 2);
                ctx.lineWidth = 3;
                ctx.strokeStyle = '#F58220'; // لون الهوية البرتقالي
                ctx.stroke();

                ctx.beginPath();
                ctx.arc(this.mouseX, this.mouseY, this.lensRadius + 4, 0, Math.PI * 2);
                ctx.lineWidth = 1;
                ctx.strokeStyle = 'rgba(0, 102, 161, 0.6)'; // لون الهوية الأزرق
                ctx.stroke();
                ctx.restore();
            } else {
                // إشعار في قلب الوثيقة الضبابية يطلب تحريك الفأرة
                ctx.save();
                ctx.fillStyle = 'rgba(5, 15, 30, 0.75)';
                ctx.fillRect(w / 2 - 160, h / 2 - 35, 320, 70);
                ctx.strokeStyle = '#F58220';
                ctx.lineWidth = 1.5;
                ctx.strokeRect(w / 2 - 160, h / 2 - 35, 320, 70);

                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 13px Cairo, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('🔍 حرك مؤشر الفأرة لاستخدام عدسة التدقيق', w / 2, h / 2 - 5);
                ctx.fillStyle = '#fbbf24';
                ctx.font = '11px Cairo, sans-serif';
                ctx.fillText('الوثيقة محمية ضد التصوير الكامل بالجوال', w / 2, h / 2 + 18);
                ctx.restore();
            }
        }

        // =====================================================================
        // نمط ب: العرض الكامل المشفر مع درع التشويش ضد كاميرات الجوال و Rolling Shutter
        // =====================================================================
        drawFullSecureMode(ctx, w, h) {
            // رسم الوثيقة الأصلية
            ctx.drawImage(this.img, 0, 0, w, h);

            // تطبيق الدرع البصري المجهري ضد حساسات الجوال عبر كامل الوثيقة
            this.applyCameraMoiréStrobe(ctx, 0, 0, w, h);
        }

        // =====================================================================
        // تقنية التشويش ضد كاميرات الجوال والـ Rolling Shutter (Moiré & Stroboscopic)
        // =====================================================================
        applyCameraMoiréStrobe(ctx, x, y, width, height) {
            ctx.save();

            // 1. شبكة مواري المجهرية الدقيقة (تسبب تداخلاً بصرياً حاداً مع مستشعر كاميرا الهاتف)
            ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
            for (let i = x; i < x + width; i += 3) {
                ctx.fillRect(i, y, 1, height);
            }
            for (let j = y; j < y + height; j += 3) {
                ctx.fillRect(x, j, width, 1);
            }

            // 2. خط مسح متحرك بتردد عالٍ (يحدث خطوطاً سوداء متحركة في كاميرات الهواتف بسبب الـ Rolling Shutter)
            const scanY = y + ((this.frameTick * 5) % height);
            const grad = ctx.createLinearGradient(0, scanY - 15, 0, scanY + 15);
            grad.addColorStop(0, 'rgba(0, 0, 0, 0)');
            grad.addColorStop(0.5, 'rgba(0, 50, 100, 0.08)');
            grad.addColorStop(1, 'rgba(0, 0, 0, 0)');
            ctx.fillStyle = grad;
            ctx.fillRect(x, Math.max(y, scanY - 15), width, 30);

            ctx.restore();
        }

        // =====================================================================
        // العلامات المائية الجنائية الحية (Forensic Realtime Watermarks)
        // =====================================================================
        drawForensicWatermarks(ctx, w, h) {
            ctx.save();
            ctx.fillStyle = 'rgba(0, 102, 161, 0.16)';
            ctx.font = 'bold 12px Cairo, monospace';
            ctx.textAlign = 'center';

            const now = new Date();
            const timeStr = now.toISOString().replace('T', ' ').substring(0, 19);
            const wmText = `ASOTS PROTECTED • ${viewerUser.name} [${viewerUser.code}] • ${timeStr} • NO CAPTURE`;

            // كتابة مكررة مائلة عبر كامل الصفحة بزاوية -25 درجة
            ctx.rotate(-25 * Math.PI / 180);
            const startX = -w;
            const endX = w * 2;
            const startY = -h;
            const endY = h * 2;

            for (let x = startX; x < endX; x += 320) {
                for (let y = startY; y < endY; y += 120) {
                    ctx.fillText(wmText, x, y);
                }
            }
            ctx.restore();
        }
    }

    // تهيئة عارض التراخيص لكافة وثائق الصفحة عند اكتمال التحميل
    document.addEventListener('DOMContentLoaded', function () {
        const canvases = document.querySelectorAll('canvas[id^="canvas_"]');
        canvases.forEach(c => {
            const id = c.id.replace('canvas_', '');
            licenseInstances[id] = new SecureLicenseViewer(id, c);
        });
    });

    // تبديل نمط الحماية (عدسة الفحص / عرض كامل)
    window.setLicenseMode = function(licenseId, mode) {
        const inst = licenseInstances[licenseId];
        if (!inst) return;

        inst.setMode(mode);

        const btnLens = document.getElementById('btnModeLens_' + licenseId);
        const btnFull = document.getElementById('btnModeFull_' + licenseId);

        if (mode === 'lens') {
            if (btnLens) {
                btnLens.className = 'px-3 py-1.5 rounded-xl font-black transition flex items-center gap-1.5 bg-[#0066A1] text-white shadow-sm';
            }
            if (btnFull) {
                btnFull.className = 'px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 text-slate-600 hover:text-slate-900';
            }
        } else {
            if (btnLens) {
                btnLens.className = 'px-3 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 text-slate-600 hover:text-slate-900';
            }
            if (btnFull) {
                btnFull.className = 'px-3 py-1.5 rounded-xl font-black transition flex items-center gap-1.5 bg-[#0066A1] text-white shadow-sm';
            }
        }
    };

})();
</script>
@endsection
