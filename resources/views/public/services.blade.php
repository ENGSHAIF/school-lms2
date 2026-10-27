@extends('layouts.app', ['title' => 'دليل البرامج والخدمات - The Asian Star Office (ASOTS)'])

@section('content')
<div class="space-y-12 max-w-6xl mx-auto my-6">

    <!-- رأس الصفحة والهوية المؤسسية -->
    <div class="text-center space-y-4">
        <span class="inline-flex items-center gap-2 text-xs font-black text-indigo-700 bg-indigo-50 px-4 py-1.5 rounded-full border border-indigo-200 shadow-sm">
            <span>🌐</span>
            <span>دليل البرامج والخدمات الشامل • Programmes & Services Directory</span>
        </span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
            برامج وخدمات مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS)
        </h1>
        <p class="text-xs sm:text-sm font-semibold text-slate-600 font-sans tracking-wide max-w-3xl mx-auto" dir="ltr">
            {{ $info['descriptive_title_en'] ?? $info['definition_en'] }}
        </p>

        <!-- الشعار العام والمبدأ الحاكم للمشروع -->
        <div class="max-w-2xl mx-auto space-y-2 pt-2">
            <div class="bg-gradient-to-r from-amber-500/10 via-indigo-500/10 to-amber-500/10 border border-amber-300/50 p-3 rounded-2xl text-xs sm:text-sm font-black text-slate-900">
                ⭐ {{ $info['general_tagline_ar'] ?? 'شريكك في التعلم اللغوي، والتطوير المهني، والتواصل العالمي.' }}
                <span class="block text-[11px] text-indigo-700 font-sans font-semibold mt-0.5" dir="ltr">
                    "{{ $info['general_tagline_en'] ?? 'Your Partner in Language Learning, Professional Development & Global Communication.' }}"
                </span>
            </div>
            <div class="text-xs text-slate-500 font-bold">
                ⚖️ مبدأ الإطلاق الاستراتيجي: <span class="text-indigo-950 font-mono">"{{ $info['guiding_principle_en'] }}"</span>
            </div>
        </div>
    </div>

    <!-- بانر برنامج الإطلاق التشغيلي الأول: اللغة الإنجليزية العامة (General English) -->
    <div class="bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1] text-white rounded-3xl p-8 sm:p-10 shadow-2xl border border-white/20 space-y-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 pb-6 border-b border-white/15">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-black bg-[#F58220] text-white px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                        برنامج الإطلاق التشغيلي الأول • Launched & Active
                    </span>
                    <span class="text-xs font-bold text-amber-200 bg-white/10 px-3 py-1 rounded-full border border-white/20 font-sans" dir="ltr">
                        Interchange – Fifth Edition
                    </span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-white">
                    برنامج اللغة الإنجليزية العامة (General English)
                </h2>
                <p class="text-xs sm:text-sm text-sky-100 leading-relaxed max-w-3xl">
                    "{{ $info['english_tagline_ar'] }}" — برنامج مكثف ومتدرج من المبتدئ إلى المتقدم يغطي المهارات الأربع (الاستماع، التحدث، القراءة، والكتابة) مع التركيز الكامل على النطق والطلاقة والتواصل التفاعلي بنظام 1-on-1 (جلسات 25 دقيقة) عبر الفصول الذكية وZoom.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row lg:flex-col gap-2 shrink-0 w-full sm:w-auto">
                <a href="{{ route('onboarding') }}" class="px-6 py-3.5 rounded-xl bg-[#F58220] hover:bg-[#d96e14] text-white font-black text-xs shadow-lg transition text-center whitespace-nowrap">
                    بدء اختبار تحديد المستوى والتسجيل 🚀
                </a>
                <a href="{{ route('packages.index') }}" class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition text-center whitespace-nowrap">
                    استعراض باقات الحصص والأسعار
                </a>
            </div>
        </div>

        <!-- رحلة المتعلم في 8 خطوات (Learner Journey - Section 10) -->
        <div class="space-y-3">
            <h3 class="text-xs font-black uppercase text-amber-400 tracking-wider">
                خارطة رحلة المتعلم في البرنامج (Learner Journey):
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2 text-center text-[10px]">
                @foreach($info['general_english_spec']['learner_journey'] as $step)
                    <div class="bg-slate-800/80 p-2.5 rounded-xl border border-slate-700/80 space-y-1">
                        <span class="w-5 h-5 rounded-full bg-indigo-600 text-white font-mono font-bold flex items-center justify-center mx-auto text-[10px]">
                            {{ $step['step'] }}
                        </span>
                        <strong class="block text-slate-200 font-bold leading-tight">{{ $step['title_ar'] }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- مصفوفة القطاعات الثمانية الكاملة للبرامج والخدمات (Section 8 Blueprint) -->
    <div class="space-y-8">
        <div class="flex flex-col sm:flex-row justify-between sm:items-end gap-3 border-b border-slate-200 pb-4">
            <div>
                <span class="text-xs font-black text-indigo-600 uppercase tracking-wider block">
                    الهيكل القطاعي الموحد (Unified Operational Architecture)
                </span>
                <h2 class="text-2xl font-black text-slate-900 mt-1">قطاعات البرامج والخدمات وحالاتها التشغيلية</h2>
            </div>
            <div class="flex flex-wrap gap-2 text-[10px] font-black">
                <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800">مفتوح للتسجيل 🚀</span>
                <span class="px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-800">متاح ✔️</span>
                <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800">قيد التطوير ⏳</span>
                <span class="px-2.5 py-1 rounded-lg bg-purple-100 text-purple-800">قريباً 🔜</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($info['programme_sectors'] as $sector)
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center text-xl shadow-inner">
                                    {{ $sector['icon'] }}
                                </span>
                                <div>
                                    <span class="text-[10px] font-mono font-bold text-slate-400 block">{{ $sector['code'] }}</span>
                                    <h3 class="text-base font-black text-slate-900">{{ $sector['title_ar'] }}</h3>
                                    <span class="text-[11px] text-slate-500 font-sans font-semibold" dir="ltr">{{ $sector['title_en'] }}</span>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">
                            {{ $sector['desc_ar'] }}
                        </p>

                        <!-- قائمة البرامج داخل هذا القطاع -->
                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            @foreach($sector['items'] as $item)
                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                    <div class="space-y-0.5">
                                        <strong class="text-slate-800 font-bold block">{{ $item['name_ar'] }}</strong>
                                        <span class="text-[10px] text-slate-500 font-sans block" dir="ltr">{{ $item['name_en'] }}</span>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black whitespace-nowrap {{ $item['badge'] === 'emerald' ? 'bg-emerald-100 text-emerald-800' : ($item['badge'] === 'amber' ? 'bg-amber-100 text-amber-800' : ($item['badge'] === 'purple' ? 'bg-purple-100 text-purple-800' : 'bg-indigo-100 text-indigo-800')) }}">
                                        {{ $item['status_ar'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- روابط سريعة للقطاع -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        @if($sector['id'] === 'language_education' || $sector['id'] === 'test_prep')
                            <a href="{{ route('onboarding') }}" class="text-indigo-600 font-bold hover:text-indigo-800 flex items-center gap-1">
                                <span>طلب التحاق أو اختبار مستوى</span>
                                <span>←</span>
                            </a>
                        @elseif($sector['id'] === 'translation')
                            <a href="{{ route('translation_services') }}" class="text-indigo-600 font-bold hover:text-indigo-800 flex items-center gap-1">
                                <span>تفاصيل خدمات الترجمة المعتمدة</span>
                                <span>←</span>
                            </a>
                        @elseif($sector['id'] === 'consultancy')
                            <a href="{{ route('consultancy_services') }}" class="text-indigo-600 font-bold hover:text-indigo-800 flex items-center gap-1">
                                <span>حجز استشارة أكاديمية</span>
                                <span>←</span>
                            </a>
                        @else
                            <a href="{{ route('contact') }}" class="text-slate-500 font-bold hover:text-slate-800 flex items-center gap-1">
                                <span>استفسار عن موعد إطلاق البرنامج</span>
                                <span>←</span>
                            </a>
                        @endif
                        <span class="text-[10px] text-slate-400 font-mono">{{ count($sector['items']) }} مسارات</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- القيم المؤسسية التسع (Section 6 Core Values) -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm space-y-6">
        <div class="text-center max-w-2xl mx-auto space-y-1">
            <span class="text-xs font-black text-amber-700 uppercase tracking-wider">
                المبادئ الحاكمة للعمليات والسياسات
            </span>
            <h3 class="text-xl font-black text-slate-900">القيم الجوهرية التسع لمنصة ASOTS (Core Values)</h3>
            <p class="text-xs text-slate-500">قيم عملية تتجسد في كل حصة دراسية، تقرير أكاديمي، ومعاملة محاسبية</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($info['core_values'] as $val)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 hover:border-indigo-300 transition">
                    <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white font-mono font-bold text-xs flex items-center justify-center shrink-0">
                        {{ $val['number'] }}
                    </span>
                    <div>
                        <strong class="text-xs font-black text-slate-900 block">{{ $val['title_ar'] }}</strong>
                        <span class="text-[10px] text-indigo-600 font-sans font-semibold block" dir="ltr">{{ $val['title_en'] }}</span>
                        <p class="text-[11px] text-slate-500 leading-snug mt-1">{{ $val['desc_ar'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- تنويهات النزاهة المؤسسية والاعتماد (Accreditation Integrity Box) -->
    <div class="bg-amber-50/70 border border-amber-200 rounded-3xl p-6 sm:p-8 space-y-3 text-xs text-amber-950">
        <div class="flex items-center gap-2 font-black text-sm text-amber-900">
            <span>⚖️</span>
            <span>تنويهات النزاهة الأكاديمية والمؤسسية المعتمدة (ASOTS Integrity Policy)</span>
        </div>
        <p class="leading-relaxed">
            <strong>1. الاختبارات الدولية (TOEFL & IELTS):</strong> تقدم منصة ASOTS برامج إعداد وتأهيل وتدريب مكثف على استراتيجيات الاختبارات الدولية، ولا تدعي المنصة منح شهادات اختبارات التوفل أو الآيلتس الرسمية؛ حيث تمنح هذه الشهادات حصراً عبر مراكزها وهيئاتها الدولية المعتمدة.
        </p>
        <p class="leading-relaxed">
            <strong>2. ضمان الجودة والشهادات:</strong> تصدر شهادات إتمام البرامج من ASOTS وفقاً لشروط وضوابط كل مقرر بإشراف قسم الجودة والتميز (AQED). وتلتزم المنصة بعدم نشر أي ادعاءات غير موثقة بشأن اعتمادات دولية خارج نطاق ما هو مثبت ورسمي.
        </p>
    </div>

</div>
@endsection
