@extends('layouts.app')

@section('content')
<style>
    /* منع الطباعة نهائياً وتفريغ المحتوى لحماية الدليل */
    @media print {
        body, html, * {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }
    }

    /* حماية من التحديد والنسخ والسحب */
    .protected-manual {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* علامة مائية جنائية رقمية في خلفية الدليل */
    .manual-watermark-overlay {
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 40;
        opacity: 0.045;
        background-repeat: repeat;
        background-size: 420px 220px;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='420' height='220' viewBox='0 0 420 220'><text x='20' y='60' fill='%23000' font-family='sans-serif' font-size='12' font-weight='bold' transform='rotate(-22 20 60)'>ASOTS PROPRIETARY &amp; CONFIDENTIAL</text><text x='30' y='90' fill='%23000' font-family='sans-serif' font-size='10' transform='rotate(-22 30 90)'>{{ auth()->check() ? auth()->user()->name : 'CONFIDENTIAL DEMO' }} • {{ request()->ip() }}</text></svg>");
    }
</style>

<div class="manual-watermark-overlay"></div>

<div class="min-h-screen bg-slate-50/50 py-8 px-4 sm:px-6 lg:px-8 protected-manual" dir="rtl">
    <div class="max-w-6xl mx-auto space-y-8">

        <!-- 1. ترويسة مركز المساعدة والبحث بتدرج الهوية الرسمية -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1] text-white p-8 sm:p-10 shadow-2xl border border-[#0066A1]/40">
            <!-- خلفية إشعاعية تجميلية -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-[#F58220]/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#F58220]/20 border border-[#F58220]/40 text-[#F58220] text-xs font-black">
                    <span>📖 مركز المساعدة والتوثيق التشغيلي المحمي (DRM Protected)</span>
                    <span>•</span>
                    @if($isStaff)
                        <span class="text-white">كادر الإدارة والتعليم الكامل</span>
                    @elseif($userRole === 'student')
                        <span class="text-emerald-300">دليل الطالب الأكاديمي</span>
                    @elseif($userRole === 'parent')
                        <span class="text-amber-200">دليل ولي الأمر</span>
                    @else
                        <span class="text-slate-100">دليل الزوار والطلاب الجدد</span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight">
                    كيف تستخدم نظام ومنصة ASOTS بكفاءة واحترافية؟
                </h1>

                <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                    @if($isStaff)
                        دليلك المعتمد لكافة شاشات الإدارة، المناهج، الصلاحيات، المجموعات، الاختبارات، والشهائد الأكاديمية خطوة بخطوة.
                    @elseif($userRole === 'student')
                        دليلك الأكاديمي الشامل لاستعراض مناهجك، خوض الكويزات والاختبارات، ومتابعة رصيدك المالي وسنداتك.
                    @elseif($userRole === 'parent')
                        دليلك لمتابعة الأبناء، سجلات الحضور والغياب، التقييم الأسبوعي، والنتائج الأكاديمية والمالية.
                    @else
                        دليلك لاستكشاف المنصة، خوض اختبار تحديد المستوى المجاني، والتعرف على مستويات دبلوم الإنجليزية وأسعار الدورات.
                    @endif
                </p>

                <!-- شريط البحث اللحظي -->
                <div class="pt-2">
                    <div class="relative max-w-2xl">
                        <input type="text" id="helpSearchInput" onkeyup="filterHelpGuides()" 
                            placeholder="ابحث عن أي مهمة أو شاشة... (مثال: إضافة دورة، كويز، حضور، رصيد...)"
                            class="w-full bg-white/10 hover:bg-white/15 focus:bg-white text-white focus:text-slate-900 placeholder:text-slate-400 border border-white/20 focus:border-indigo-500 rounded-2xl py-3.5 pr-11 pl-4 text-sm font-medium outline-none transition-all shadow-lg backdrop-blur-md">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-lg text-slate-400 pointer-events-none">
                            🔍
                        </span>
                        <button type="button" onclick="clearHelpSearch()" id="clearSearchBtn" class="hidden absolute left-4 top-1/2 -translate-y-1/2 text-xs bg-white/20 hover:bg-white/30 text-white px-2 py-0.5 rounded-lg transition">
                            مسح
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- لافتة التوجيه والصلاحيات بحسب الدور -->
        @if(!$currentUser)
            <div class="bg-amber-500/10 border border-amber-400/30 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3 text-xs">
                    <span class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center text-xl shrink-0">🔒</span>
                    <div>
                        <strong class="font-black text-slate-800 text-sm block">تنبيه الصلاحيات: أنت تتصفح حالياً دليل الزوار العام.</strong>
                        <span class="text-slate-600 text-xs">أدلة تشغيل النظام وإدارة الدورات والصلاحيات والكويزات مخصصة لكادر الإدارة والمعلمين المصرح لهم.</span>
                    </div>
                </div>
                <a href="{{ route('login') }}" class="px-4 py-2.5 bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-slate-950 font-black rounded-xl text-xs transition shrink-0 shadow-md flex items-center gap-1.5">
                    <span>🔐</span>
                    <span>تسجيل الدخول للإدارة والتعليم</span>
                </a>
            </div>
        @elseif($isStaff)
            <div class="bg-indigo-50 border border-indigo-200/80 rounded-2xl p-4 flex items-center gap-3 text-xs text-indigo-900">
                <span class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg shrink-0">👑</span>
                <div class="flex-1">
                    <strong class="font-black block">مرحباً بك يا {{ $currentUser->name }} (كادر الإدارة والتعليم)!</strong>
                    <span class="text-indigo-700">لك حق استعراض كامل الدليل التشغيلي والشاشات. لاحظ أن فتح أي شاشة يتقيد دائماً بالصلاحيات الممنوحة لحسابك.</span>
                </div>
            </div>
        @elseif($currentUser->isStudent())
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3 text-xs text-emerald-900">
                <span class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg shrink-0">🎓</span>
                <div class="flex-1">
                    <strong class="font-black block">أهلاً بك يا {{ $currentUser->name }} في دليل الطالب الأكاديمي!</strong>
                    <span class="text-emerald-700">هذا الدليل مخصص لمساعدتك في استعراض دوراتك، أداء الاختبارات، ومتابعة رصيدك المالي وسنداتك.</span>
                </div>
            </div>
        @elseif($currentUser->isParent())
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center gap-3 text-xs text-amber-900">
                <span class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center text-lg shrink-0">👨‍👧‍👦</span>
                <div class="flex-1">
                    <strong class="font-black block">أهلاً بك يا {{ $currentUser->name }} في دليل ولي الأمر!</strong>
                    <span class="text-amber-800">هذا الدليل مخصص لمتابعة تقارير أبنائك الأكاديمية، سجلات الحضور، نتائج الكويزات، والوضع المالي.</span>
                </div>
            </div>
        @endif

        <!-- 2. أزرار تصفية الأدوار (تظهر لكادر الإدارة والمعلمين) -->
        @if($isStaff)
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <button type="button" onclick="filterByRole('all')" class="role-filter-btn active px-4 py-2 rounded-xl text-xs font-black bg-indigo-600 text-white shadow-sm transition shrink-0" data-role="all">
                    🌟 جميع الأدلة المتاحة لك ({{ count($guides) }})
                </button>
                <button type="button" onclick="filterByRole('admin')" class="role-filter-btn px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 transition shrink-0" data-role="admin">
                    👑 دليل الإدارة والنظام
                </button>
                <button type="button" onclick="filterByRole('teacher')" class="role-filter-btn px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 transition shrink-0" data-role="teacher">
                    👨‍🏫 دليل المعلم
                </button>
                <button type="button" onclick="filterByRole('accountant')" class="role-filter-btn px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 transition shrink-0" data-role="accountant">
                    💼 دليل المحاسبة
                </button>
                <button type="button" onclick="filterByRole('student')" class="role-filter-btn px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 transition shrink-0" data-role="student">
                    🧑‍🎓 دليل الطالب
                </button>
                <button type="button" onclick="filterByRole('parent')" class="role-filter-btn px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 transition shrink-0" data-role="parent">
                    👨‍👧‍👦 دليل ولي الأمر
                </button>
                <button type="button" onclick="filterByRole('guest')" class="role-filter-btn px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 transition shrink-0" data-role="guest">
                    🌐 دليل الزوار والتسجيل
                </button>
            </div>
        @endif

        <!-- 3. شبكة بطاقات الشروحات التفاعلية -->
        <div id="guidesContainer" class="space-y-6">
            @foreach($guides as $guide)
                @php
                    $canOpenScreen = false;
                    if ($currentUser) {
                        if ($isStaff) {
                            $canOpenScreen = true;
                        } elseif ($currentUser->isStudent() && str_contains($guide['route'], 'student')) {
                            $canOpenScreen = true;
                        } elseif ($currentUser->isParent() && str_contains($guide['route'], 'parent')) {
                            $canOpenScreen = true;
                        }
                    } elseif (in_array('guest', $guide['target_audience'] ?? [])) {
                        $canOpenScreen = true;
                    }
                @endphp

                <div class="guide-card bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs hover:shadow-md transition duration-200 space-y-4"
                     data-id="{{ $guide['id'] }}"
                     data-role="{{ $guide['role'] }}"
                     data-keywords="{{ implode(' ', $guide['keywords']) }} {{ $guide['title'] }} {{ $guide['summary'] }} {{ $guide['category'] }} {{ $guide['screen'] }}">
                    
                    <!-- الترويسة والتصنيف والشاشة المسؤولة -->
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                {{ $guide['category'] }}
                            </span>
                            <span class="text-xs text-slate-400">•</span>
                            @if($canOpenScreen)
                                <a href="{{ $guide['route'] }}" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                                    <span>الشاشة: {{ $guide['screen'] }}</span>
                                    <span class="font-mono text-[10px]" dir="ltr">↗</span>
                                </a>
                            @else
                                <span class="text-xs text-slate-500 font-bold flex items-center gap-1">
                                    <span>🔒 الشاشة: {{ $guide['screen'] }}</span>
                                    <span class="text-[10px] text-slate-400 font-normal">(صلاحيات مقيدة)</span>
                                </span>
                            @endif
                        </div>

                        <!-- زر سؤال المساعد الآلي السريع -->
                        <button type="button" onclick="askBotFromGuide('{{ $guide['title'] }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-2xs">
                            <span>🤖</span>
                            <span>اسأل المساعد الذكي عن هذا الشرح</span>
                        </button>
                    </div>

                    <!-- العنوان والنبذة -->
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900">
                            {{ $guide['title'] }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                            {{ $guide['summary'] }}
                        </p>
                    </div>

                    <!-- الخطوات المرقمة العملية -->
                    <div class="bg-slate-50/80 rounded-2xl p-4 sm:p-5 border border-slate-200/60 space-y-2.5 text-xs text-slate-800">
                        <span class="block font-black text-indigo-950 mb-2">📋 الخطوات التفصيلية للتنفيذ:</span>
                        <ol class="space-y-2 pr-1">
                            @foreach($guide['steps'] as $index => $step)
                                <li class="flex items-start gap-2.5 leading-relaxed">
                                    <span class="w-5 h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5 shadow-2xs">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="flex-1">
                                        {!! preg_replace('/\*\*(.*?)\*\*/', '<strong class="font-black text-slate-900">$1</strong>', e($step)) !!}
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </div>

                    <!-- تلميح مهم إن وجد -->
                    @if(!empty($guide['tip']))
                        <div class="flex items-start gap-2.5 bg-amber-50 text-amber-950 p-3.5 rounded-2xl border border-amber-200/80 text-xs">
                            <span class="text-base shrink-0 mt-0.5">💡</span>
                            <div>
                                <span class="font-black">تلميح مهم:</span>
                                <span class="text-amber-900">{{ $guide['tip'] }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- زر الذهاب للشاشة لتطبيق الخطوات -->
                    <div class="pt-1 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">
                            الكلمات الدالة: {{ implode(' • ', array_slice($guide['keywords'], 0, 4)) }}
                        </span>
                        @if($canOpenScreen)
                            <a href="{{ $guide['route'] }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-black transition">
                                <span>فتح {{ $guide['screen'] }} لتطبيق الخطوات</span>
                                <span class="font-mono text-xs" dir="ltr">←</span>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-500 rounded-xl text-[11px] font-bold border border-slate-200">
                                <span>🔒</span>
                                <span>الشاشة مقفلة (يلزم صلاحية وظيفية لفتحها)</span>
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- رسالة عند عدم وجود نتائج بحث -->
        <div id="noResultsBox" class="hidden text-center py-16 bg-white rounded-3xl border border-slate-200 p-8 space-y-3">
            <span class="text-4xl block">🔍</span>
            <h3 class="font-black text-base text-slate-800">لم يتم العثور على شروحات مطابقة</h3>
            <p class="text-xs text-slate-500">جرب البحث بكلمة أخرى، أو اسأل المساعد الذكي مباشرة في الزر العائم أسفل يسار الصفحة.</p>
        </div>

        <!-- 4. قسم الأسئلة الشائعة والمساعدة السريعة (FAQ) -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-4">
                <span class="text-2xl">❓</span>
                <div>
                    <h3 class="font-black text-slate-900 text-base">أسئلة شائعة وإجابات فورية</h3>
                    <p class="text-xs text-slate-500">أكثر الاستفسارات تكراراً وطريقة معالجتها بالنظام</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-1.5">
                    <h4 class="font-black text-xs text-slate-900 flex items-center gap-1.5">
                        <span class="text-indigo-600">Q:</span>
                        <span>كيف يتم التحقق من مصداقية الشهادة الصادرة؟</span>
                    </h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        تحمل كل شهادة باركود QR فريد ورقم سيريال رسمي. بمجرد مسح الكود بكاميرا الجوال، تُفتح صفحة التحقق الرسمية <code>/verify/certificate</code> لتأكيد صحة الشهادة والدرجات للجامعات وجهات التوظيف.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-1.5">
                    <h4 class="font-black text-xs text-slate-900 flex items-center gap-1.5">
                        <span class="text-indigo-600">Q:</span>
                        <span>طالب نسي كلمة المرور الخاصة به، كيف يساعده المشرف؟</span>
                    </h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        من شاشة <strong>إدارة المستخدمين (`/admin/users`)</strong>، يضغط المشرف أو المدير على زر المفتاح 🔑 (تغيير كلمة المرور) ويعين كلمة مرور جديدة أو يرسل له كود إعادة التعيين.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-1.5">
                    <h4 class="font-black text-xs text-slate-900 flex items-center gap-1.5">
                        <span class="text-indigo-600">Q:</span>
                        <span>ماذا يشمل اختبار تحديد المستوى المجاني؟</span>
                    </h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        يستغرق 25 دقيقة فقط، ويقيس مهارات الاستماع، القواعد، وفحص النطق الصوتي المباشر بالذكاء الاصطناعي (AI Speech Assessment) بدون أي رسوم.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-1.5">
                    <h4 class="font-black text-xs text-slate-900 flex items-center gap-1.5">
                        <span class="text-indigo-600">Q:</span>
                        <span>هل يستطيع المعلم رصد الحضور من جواله؟</span>
                    </h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        نعم، شاشة <strong>التقييم والحضور (`/admin/groups/evaluations`)</strong> متجاوبة 100% مع الجوال، ويستطيع المعلم رصد الحضور والمشاركة وطباعة الكشف مباشرة بنقرة زر.
                    </p>
                </div>
            </div>
        </div>

        <!-- 5. بانل الدعم والمساعد الذكي -->
        <div class="rounded-3xl bg-gradient-to-r from-indigo-900 to-purple-900 text-white p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl">
            <div class="space-y-1.5 text-center sm:text-right">
                <h3 class="font-black text-lg text-white">هل لديك استفسار آخر لم تجده في الدليل؟</h3>
                <p class="text-xs text-indigo-200">
                    المساعد الذكي للمنصة 🤖 متصل بقاعدة البيانات وجاهز للرد على استفساراتك بحسب صلاحيات حسابك على مدار 24 ساعة.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button type="button" onclick="toggleSmartChat()" class="px-5 py-3 rounded-2xl bg-white hover:bg-slate-100 text-indigo-900 font-black text-xs shadow-lg transition flex items-center gap-2">
                    <span>🤖</span>
                    <span>سؤال المساعد الذكي الآن</span>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    let currentRoleFilter = 'all';

    function filterByRole(role) {
        currentRoleFilter = role;
        
        // تحديث حالة الأزرار
        document.querySelectorAll('.role-filter-btn').forEach(btn => {
            if (btn.getAttribute('data-role') === role) {
                btn.className = 'role-filter-btn active px-4 py-2 rounded-xl text-xs font-black bg-indigo-600 text-white shadow-sm transition shrink-0';
            } else {
                btn.className = 'role-filter-btn px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 transition shrink-0';
            }
        });

        applyFilters();
    }

    function filterHelpGuides() {
        const clearBtn = document.getElementById('clearSearchBtn');
        const inputVal = document.getElementById('helpSearchInput').value.trim();
        if (inputVal !== '') {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
        applyFilters();
    }

    function clearHelpSearch() {
        document.getElementById('helpSearchInput').value = '';
        document.getElementById('clearSearchBtn').classList.add('hidden');
        applyFilters();
    }

    function applyFilters() {
        const query = document.getElementById('helpSearchInput').value.trim().toLowerCase();
        const cards = document.querySelectorAll('.guide-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const role = card.getAttribute('data-role');
            const keywords = (card.getAttribute('data-keywords') || '').toLowerCase();

            const matchesRole = (currentRoleFilter === 'all' || role === currentRoleFilter || role === 'all');
            const matchesQuery = (query === '' || keywords.includes(query));

            if (matchesRole && matchesQuery) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        const noResults = document.getElementById('noResultsBox');
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }

    function askBotFromGuide(title) {
        // فتح ودجت الشات وإرسال السؤال تلقائياً
        if (typeof toggleSmartChat === 'function') {
            const win = document.getElementById('asotsChatWindow');
            if (win && win.classList.contains('hidden')) {
                toggleSmartChat();
            }
            if (typeof sendQuickMessage === 'function') {
                sendQuickMessage(title);
            }
        }
    }

    // =========================================================================
    // حماية الدليل من النسخ والاستنساخ والذكاء الاصطناعي (Anti-Theft Protection)
    // =========================================================================
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    });

    document.addEventListener('copy', function(e) {
        e.preventDefault();
        const notice = '⚠️ تنبيه أمني وقانوني: محتوى ودليل نظام وتشغيل مكتب النجم الآسيوي (ASOTS) محمي بموجب قوانين حماية المصنفات والملكية الفكرية. يمنع منعاً باتاً النسخ أو التداول أو التغذية لأنظمة وبرمجيات الذكاء الاصطناعي.';
        if (e.clipboardData) {
            e.clipboardData.setData('text/plain', notice);
        }
        alert('⚠️ محتوى الدليل التشغيلي محمي ضد النسخ والاستنساخ حفاظاً على حقوق الملكية الفكرية.');
        return false;
    });

    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            const key = (e.key || '').toLowerCase();
            // منع Ctrl+C, Ctrl+X, Ctrl+U (عرض المصدر), Ctrl+P (طباعة), Ctrl+S (حفظ), Ctrl+A (تحديد الكل)
            if (['c', 'x', 'u', 'p', 's', 'a'].includes(key) || (e.shiftKey && ['i', 'j', 'c'].includes(key))) {
                e.preventDefault();
                return false;
            }
        }
        // منع F12 أدوات المطورين
        if (e.keyCode === 123) {
            e.preventDefault();
            return false;
        }
    });
</script>
@endsection
