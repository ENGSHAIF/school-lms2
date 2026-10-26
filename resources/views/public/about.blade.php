@extends('layouts.app', ['title' => 'عن المكتب والهوية المؤسسية - The Asian Star Office (ASOTS)'])

@section('content')
<div class="space-y-12 max-w-5xl mx-auto my-6">

    <!-- رأس الصفحة والهوية المؤسسية -->
    <div class="text-center space-y-4">
        <span class="inline-flex items-center gap-2 text-xs font-black text-amber-700 bg-amber-50 px-4 py-1.5 rounded-full border border-amber-200 shadow-sm">
            <span>⭐</span>
            <span>الهوية المؤسسية والرؤية الأكاديمية • Institutional Profile</span>
        </span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
            {{ $info['name_ar'] }}
        </h1>
        <p class="text-xs sm:text-sm font-semibold text-slate-600 font-sans tracking-wide" dir="ltr">
            {{ $info['name_en'] }} ({{ $info['acronym'] }})
        </p>

        <!-- الشعار المؤسسي والوعد المؤسسي -->
        <div class="max-w-2xl mx-auto space-y-2 pt-2">
            <div class="bg-indigo-50 border border-indigo-200 px-4 py-2 rounded-2xl text-xs sm:text-sm font-black text-indigo-900">
                🎯 الشعار المؤسسي: {{ $info['motto_ar'] }}
            </div>
            <div class="text-xs text-slate-600 font-bold">
                🌟 الوعد المؤسسي: "{{ $info['promise_ar'] }}"
            </div>
        </div>
    </div>

    <!-- بطاقة التعريف والرؤية والرسالة والمبدأ الحاكم -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-sm space-y-10">

        <!-- التعريف المؤسسي الرسمي -->
        <div class="space-y-3">
            <h2 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <span>🏛️</span>
                <span>التعريف المؤسسي الرسمي (ASOTS)</span>
            </h2>
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <p class="text-sm font-black text-indigo-950 leading-relaxed">
                    {{ $info['definition_ar'] }}
                </p>
                <p class="text-xs font-bold text-slate-500 font-sans leading-relaxed" dir="ltr">
                    {{ $info['definition_en'] }}
                </p>
                <p class="text-xs text-slate-600 leading-relaxed pt-2 border-t border-slate-200">
                    يُعد <strong>مكتب النجم الآسيوي (ASOTS)</strong> منصة رقمية دولية متكاملة ذات قيادة أكاديمية رصينة، تجمع بين التعليم اللغوي التفاعلي الحديث (وفق نموذج ASOTS للحصص الفردية 25 دقيقة)، والتواصل بين الثقافات، والترجمة والترجمة الشفوية المعتمدة، وإعداد المعلمين وتأهيلهم (TEFL/TESOL)، والاستشارات الأكاديمية والمهنية، ضمن منظومة مالية وإدارية منضبطة ترتكز على معايير الأنظمة المالية القياسية.
                </p>
            </div>
        </div>

        <!-- الرؤية والرسالة والمبدأ الحاكم -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-indigo-50/60 border border-indigo-100 p-6 rounded-2xl space-y-2">
                <span class="text-xs font-black text-indigo-700 block">🔭 رؤية المنصة (Our Vision)</span>
                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                    {{ $info['about']['vision'] }}
                </p>
            </div>

            <div class="bg-emerald-50/60 border border-emerald-100 p-6 rounded-2xl space-y-2">
                <span class="text-xs font-black text-emerald-700 block">🎯 رسالة المنصة (Our Mission)</span>
                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                    {{ $info['about']['mission'] }}
                </p>
            </div>
        </div>

        <!-- المبدأ الحاكم للمشروع -->
        <div class="p-6 rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white space-y-2">
            <div class="flex items-center gap-2 text-amber-400 font-black text-xs uppercase">
                <span>⚖️</span>
                <span>المبدأ الحاكم للمشروع (The Guiding Principle)</span>
            </div>
            <p class="text-xs sm:text-sm text-slate-200 leading-relaxed font-bold">
                "{{ $info['core_principle_ar'] }}"
            </p>
        </div>

        <!-- القيادة الأكاديمية وقسم الجودة (بطاقتان بارزتان) -->
        <div class="pt-8 border-t border-slate-100 space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <span>👑</span>
                <span>أركان المنظومة الأكاديمية والمؤسسية</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- بطاقة القيادة الأكاديمية -->
                <div class="p-6 rounded-3xl bg-indigo-50/50 border border-indigo-200 space-y-4 hover:shadow-md transition">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-3xl font-black shadow-md">
                            👨‍🏫
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-indigo-600 block uppercase">القيادة الأكاديمية والمؤسسية</span>
                            <h4 class="text-base font-black text-slate-900">{{ $info['leadership']['name_ar'] }}</h4>
                            <span class="text-[11px] font-bold text-slate-500">{{ $info['leadership']['title_ar'] }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        {{ $info['leadership']['bio_summary_ar'] }}
                    </p>
                    <a href="{{ route('leadership') }}" class="inline-flex items-center gap-1.5 text-xs font-black text-indigo-700 hover:text-indigo-900 pt-1">
                        <span>عرض الملف والسيرة الأكاديمية الكاملة</span>
                        <span>←</span>
                    </a>
                </div>

                <!-- بطاقة قسم الجودة والتميز -->
                <div class="p-6 rounded-3xl bg-rose-50/50 border border-rose-200 space-y-4 hover:shadow-md transition">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-3xl font-black shadow-md">
                            🛡️
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-rose-600 block uppercase">شؤون الجودة والاعتماد</span>
                            <h4 class="text-base font-black text-slate-900">{{ $info['aqed']['name_ar'] }}</h4>
                            <span class="text-[11px] font-bold text-rose-800 font-mono">{{ $info['aqed']['motto_ar'] }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        نموذج جودة صارم يربط كل تقييم بنظام الـ 100 درجة المعتمد وتقارير المعلمين وسجلات الحضور والتحسين المستمر دون ادعاءات غير موثقة.
                    </p>
                    <a href="{{ route('quality') }}" class="inline-flex items-center gap-1.5 text-xs font-black text-rose-700 hover:text-rose-900 pt-1">
                        <span>استعراض معايير الجودة وتوزيع الـ 100 درجة</span>
                        <span>←</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- المجالات الستة الرئيسية لمنصة ASOTS -->
        <div class="pt-8 border-t border-slate-100 space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <span>🌐</span>
                <span>المجالات والركائز المؤسسية الستة</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($info['pillars'] as $pillar)
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 hover:border-indigo-300 hover:bg-indigo-50/20 transition">
                        <span class="text-2xl">{{ $pillar['icon'] }}</span>
                        <h4 class="text-xs sm:text-sm font-black text-slate-900">{{ $pillar['title'] }}</h4>
                        <p class="text-[11px] text-slate-600 leading-relaxed">{{ $pillar['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- أزرار الخدمات السريعة -->
        <div class="pt-8 border-t border-slate-100 flex flex-wrap gap-3 items-center justify-between">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('translation_services') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                    خدمات الترجمة المعتمدة
                </a>
                <a href="{{ route('consultancy_services') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                    الاستشارات الأكاديمية
                </a>
                <a href="{{ route('curriculum') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition">
                    دليل المناهج وCEFR
                </a>
            </div>

            <a href="{{ route('contact') }}" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-md transition">
                تواصل مع إدارة المكتب
            </a>
        </div>

    </div>

</div>
@endsection
