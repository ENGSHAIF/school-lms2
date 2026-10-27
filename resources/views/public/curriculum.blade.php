@extends('layouts.app', ['title' => 'المناهج الدراسية وخريطة المستويات CEFR - ' . config('asots.name_ar')])

@section('content')
<div class="space-y-16 py-4">

    <!-- بطل الصفحة (Curriculum Hero) -->
    <section class="bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1] text-white rounded-3xl p-8 sm:p-12 shadow-xl border border-white/20 relative overflow-hidden">
        <div class="max-w-3xl space-y-4 relative z-10">
            <span class="inline-block bg-white/10 text-white text-xs px-3.5 py-1 rounded-full font-bold border border-white/20">
                المعايير الدولية الأوروبية (CEFR Framework)
            </span>
            <h1 class="text-3xl sm:text-4xl font-black">المناهج الدراسية وخريطة المستويات الأكاديمية</h1>
            <p class="text-xs sm:text-sm text-sky-100 leading-relaxed">
                في مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS)، قمنا بتصميم مناهجنا وفق أحدث المعايير الدولية للإطار الأوروبي المرجعي المشترك للغات (CEFR) ونموذج ASOTS المعتمد عالمياً، مع مسار خاص ومتقدم للاستعداد لاختبار التوفل TOEFL iBT.
            </p>
            <div class="pt-2 flex flex-wrap gap-3">
                <a href="{{ route('onboarding') }}" class="bg-[#F58220] hover:bg-[#d96e14] text-white font-black text-xs px-6 py-3 rounded-xl shadow-md transition">
                    حدد مستواك مجاناً الآن 🎯
                </a>
                <a href="{{ route('packages.index') }}" class="bg-white/10 hover:bg-white/20 text-white font-bold text-xs px-6 py-3 rounded-xl border border-white/20 transition">
                    استعراض باقات الحصص 💳
                </a>
            </div>
        </div>
        <div class="absolute -bottom-10 -left-10 w-80 h-80 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
    </section>

    <!-- خريطة المستويات الستة المتدرجة (CEFR Roadmap Cards) -->
    <section class="space-y-8">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-2xl font-black text-slate-900">التدرج الأكاديمي من Pre-A1 إلى C1</h2>
            <p class="text-xs text-slate-500 mt-1">كل مستوى يركز على مهارات تواصل حقيقية ومخرجات واضحة مع تقييم مستمر</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($info['cefr_roadmap'] as $idx => $lvl)
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm hover:shadow-lg hover:border-indigo-500 transition flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 font-mono font-black text-sm rounded-xl border border-indigo-200 group-hover:bg-indigo-600 group-hover:text-white transition">
                                {{ $lvl['code'] }}
                            </span>
                            <span class="text-xs font-bold text-slate-400 font-sans">{{ $lvl['cefr'] }}</span>
                        </div>

                        <div>
                            <h3 class="text-lg font-black text-slate-900">{{ $lvl['title'] }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed mt-2">{{ $lvl['outcomes'] }}</p>
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-xs space-y-2">
                            <div class="flex justify-between text-slate-500">
                                <span>عدد الحصص المقترحة:</span>
                                <strong class="text-slate-800 font-mono">{{ $lvl['lessons'] }}</strong>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>نوع الاختبار:</span>
                                <strong class="text-indigo-600 font-bold">تقييم شفوي وكتابي</strong>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100">
                        <a href="{{ route('onboarding') }}" class="block text-center text-xs font-bold text-indigo-600 hover:text-indigo-800">
                            سجل لبدء هذا المستوى ←
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- البرامج والمستويات المعتمدة في النظام (Database Academic Levels & Courses) -->
    <section class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-10 shadow-sm space-y-6">
        <div>
            <span class="text-xs font-bold text-indigo-600 uppercase">المستويات والدورات المسجلة رسمياً</span>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">المستويات الأكاديمية والدورات النشطة في ASOTS</h2>
            <p class="text-xs text-slate-500">كل مستوى أكاديمي يضم باقة من الدورات المتخصصة بساعات معتمدة وبنك مناهج رقمي شامل.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($levels as $lvl)
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-black flex items-center justify-center text-xs font-mono">
                                #{{ $lvl->level_order }}
                            </span>
                            <span class="text-xs font-mono font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                                {{ $lvl->formatted_fee }}
                            </span>
                        </div>

                        <div>
                            <h3 class="font-black text-base text-slate-900">{{ $lvl->name_en }}</h3>
                            <span class="text-xs text-slate-500 font-bold block">{{ $lvl->name_ar }}</span>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">{{ $lvl->description }}</p>

                        <div class="flex items-center gap-2 text-[11px] font-mono text-slate-500">
                            <span>📚 {{ $lvl->courses->count() }} دورات تابعة</span>
                            <span>•</span>
                            <span>⏱️ {{ $lvl->total_hours }} ساعة تدريبية</span>
                        </div>
                    </div>

                    <div class="space-y-2 pt-3 border-t border-slate-200">
                        <span class="text-[11px] font-bold text-slate-400 block">الدورات التابعة لهذا المستوى:</span>
                        @foreach($lvl->courses as $crs)
                            <div class="flex justify-between items-center text-xs bg-white p-2.5 rounded-xl border border-slate-200">
                                <div>
                                    <span class="font-bold text-slate-800">{{ $crs->name_en }}</span>
                                    @if($crs->name_ar && $crs->name_ar !== $crs->name_en)
                                        <span class="text-[11px] text-slate-400 block">{{ $crs->name_ar }}</span>
                                    @endif
                                </div>
                                <div class="text-left font-mono">
                                    <span class="font-bold text-indigo-600 block text-[11px]">⏱️ {{ $crs->hours }} ساعة</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $crs->formatted_fee }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- شريط الختام -->
    <section class="bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1] text-white rounded-3xl p-8 sm:p-10 shadow-xl border border-white/20 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="space-y-1 text-center md:text-right">
            <h3 class="text-xl sm:text-2xl font-black">لست متأكداً من مستواك المناسب؟</h3>
            <p class="text-xs sm:text-sm font-semibold text-sky-100">خض اختبار القبول الذكي وفحص النطق الآن وسيقوم مشرفنا الأكاديمي بتوجيهك فوراً للمنهج الصحيح.</p>
        </div>
        <a href="{{ route('onboarding') }}" class="bg-[#F58220] hover:bg-[#d96e14] text-white font-black text-xs px-8 py-3.5 rounded-2xl shadow-md transition whitespace-nowrap">
            ابدأ اختبار القبول مجاناً 🚀
        </a>
    </section>

</div>
@endsection
