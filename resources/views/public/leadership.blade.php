@extends('layouts.app', ['title' => 'القيادة الأكاديمية - The Asian Star Office (ASOTS)'])

@section('content')
<div class="space-y-12 max-w-5xl mx-auto my-6">

    <!-- رأس الصفحة -->
    <div class="text-center space-y-4">
        <span class="inline-flex items-center gap-2 text-xs font-black text-indigo-700 bg-indigo-50 px-4 py-1.5 rounded-full border border-indigo-200 shadow-sm">
            <span>🎓</span>
            <span>القيادة الأكاديمية والمؤسسية • Academic Leadership</span>
        </span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
            {{ $info['leadership']['name_ar'] }}
        </h1>
        <p class="text-xs sm:text-sm font-semibold text-slate-600 font-sans tracking-wide" dir="ltr">
            {{ $info['leadership']['name_en'] }}
        </p>
        <div class="inline-flex items-center gap-3 bg-amber-500/10 text-amber-900 px-5 py-2 rounded-2xl border border-amber-300 font-black text-sm">
            <span>⭐</span>
            <span>{{ $info['leadership']['title_ar'] }}</span>
            <span class="text-amber-400 font-light">|</span>
            <span class="font-sans text-xs" dir="ltr">{{ $info['leadership']['title_en'] }}</span>
        </div>
    </div>

    <!-- البطاقة القيادية الأكاديمية الكبرى -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-sm space-y-10">
        
        <!-- ملخص الدور والصفة المؤسسية -->
        <div class="flex flex-col lg:flex-row gap-8 items-center border-b border-slate-100 pb-8">
            <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-3xl bg-gradient-to-tr from-indigo-700 via-indigo-600 to-amber-500 flex items-center justify-center text-6xl shadow-xl ring-4 ring-indigo-100 flex-shrink-0">
                👨‍🏫
            </div>
            <div class="space-y-3 text-center lg:text-right">
                <span class="text-xs font-black text-indigo-600 uppercase tracking-widest block">
                    {{ $info['leadership']['role_ar'] }}
                </span>
                <h2 class="text-2xl font-black text-slate-900 leading-snug">
                    قيادة بحثية وأكاديمية تصنع فارقاً نوعياً في التعليم اللغوي والترجمة
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-sans" dir="ltr">
                    {{ $info['leadership']['role_en'] }}
                </p>
                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed pt-2">
                    {{ $info['leadership']['bio_summary_ar'] }}
                </p>
            </div>
        </div>

        <!-- بيان الرؤية الأكاديمية -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-8 sm:p-10 shadow-lg space-y-4 border border-indigo-500/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-400/20 text-amber-400 flex items-center justify-center text-xl font-black">
                    💬
                </div>
                <h3 class="text-lg font-black text-amber-300">بيان الرؤية الأكاديمية للقيادة</h3>
            </div>
            <blockquote class="text-sm sm:text-base text-slate-200 leading-loose italic border-r-4 border-amber-400 pr-4">
                "{{ $info['leadership']['vision_statement_ar'] }}"
            </blockquote>
            <p class="text-[11px] text-slate-400 pt-2 font-mono">
                — الأستاذ الدكتور/ فخر الدين ناجي ردمان عبده الصوفي، دكتوراه
            </p>
        </div>

        <!-- مجالات التخصص والخبرات الأكاديمية -->
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-sm">
                    📚
                </span>
                <h3 class="text-xl font-black text-slate-900">المجالات والتخصصات الأكاديمية والمهنية</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($info['leadership']['specialties_ar'] as $specialty)
                    <div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-indigo-300 hover:bg-indigo-50/30 transition">
                        <span class="text-amber-500 font-bold text-lg mt-0.5">✦</span>
                        <div class="text-xs sm:text-sm font-bold text-slate-800 leading-relaxed">
                            {{ $specialty }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- دور القيادة الأكاديمية في المنصة -->
        <div class="pt-8 border-t border-slate-100 space-y-6">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-sm">
                    ⚖️
                </span>
                <h3 class="text-xl font-black text-slate-900">المسؤوليات الأكاديمية والنزاهة المؤسسية</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 text-xs">
                <div class="p-5 rounded-2xl bg-indigo-50/50 border border-indigo-100 space-y-2">
                    <div class="text-2xl">📋</div>
                    <strong class="text-indigo-950 font-black block text-sm">مراجعة واعتماد المناهج</strong>
                    <p class="text-slate-600 leading-relaxed">
                        الإشراف المباشر على هيكلية المقررات وتوافقها التام مع الإطار الأوروبي المرجعي المشترك (CEFR) ومعايير اختبار TOEFL iBT.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-amber-50/50 border border-amber-100 space-y-2">
                    <div class="text-2xl">👨‍🏫</div>
                    <strong class="text-amber-950 font-black block text-sm">تأهيل كادر المعلمين</strong>
                    <p class="text-slate-600 leading-relaxed">
                        تدريب واختيار المعلمين والمدربين بناءً على الكفاءة اللغوية والأكاديمية واستراتيجيات TEFL الحديثة والتغذية الراجعة الفعالة.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-emerald-50/50 border border-emerald-100 space-y-2">
                    <div class="text-2xl">🔍</div>
                    <strong class="text-emerald-950 font-black block text-sm">الإشراف على الجودة AQED</strong>
                    <p class="text-slate-600 leading-relaxed">
                        تطبيق المبدأ الصارم «لا ادعاء بالجودة دون دليل»، وتدقيق تقارير الدروس ومخرجات الطلاب وسجلات الاختبارات والتقييمات.
                    </p>
                </div>
            </div>
        </div>

        <!-- أزرار الإجراءات التفاعلية -->
        <div class="pt-8 border-t border-slate-100 flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('quality') }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                    <span>قسم الجودة والتميز (AQED)</span>
                    <span>←</span>
                </a>
                <a href="{{ route('curriculum') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-100 font-bold text-xs transition">
                    استكشف المناهج وخريطة CEFR
                </a>
            </div>

            <a href="{{ route('contact') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition">
                طلب استشارة أكاديمية معتمدة
            </a>
        </div>

    </div>

</div>
@endsection
