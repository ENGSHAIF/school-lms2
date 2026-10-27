@extends('layouts.app', ['title' => 'القيادة الأكاديمية - The Asian Star Office (ASOTS)'])

@section('content')
<div class="space-y-10 max-w-5xl mx-auto my-6">

    <!-- زر تحكم خاص بمدير النظام لتعديل محتويات وبيانات القيادة -->
    @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->hasPermission('system.branding_edit') || auth()->user()->hasPermission('view_admin_dashboard')))
        <div class="bg-gradient-to-r from-amber-50 via-orange-50 to-amber-100 border-2 border-amber-300/80 p-4 sm:p-5 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3 text-amber-950">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-900 flex items-center justify-center text-2xl font-black shrink-0">
                    👑
                </div>
                <div>
                    <h3 class="font-black text-sm sm:text-base text-amber-950">لوحة مدير النظام: التحكم في نصوص وبيانات القيادة الأكاديمية</h3>
                    <p class="text-xs text-amber-800 mt-0.5">يمكنك تعديل السيرة الذاتية، التخصصات، الرؤية الأكاديمية، وأرقام التواصل المباشرة في أي وقت.</p>
                </div>
            </div>
            <a href="{{ route('admin.branding.index') }}#leadership-section" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#F58220] to-amber-500 hover:from-[#e0751a] hover:to-amber-600 text-white font-black text-xs shadow-md transition flex items-center gap-2 whitespace-nowrap">
                <span>✏️</span>
                <span>تعديل محتويات وبيانات القيادة الأكاديمية</span>
            </a>
        </div>
    @endif

    <!-- رأس الصفحة -->
    <div class="text-center space-y-4">
        <span class="inline-flex items-center gap-2 text-xs font-black text-[#0066A1] bg-sky-50 px-4 py-1.5 rounded-full border border-sky-200 shadow-sm">
            <span>🎓</span>
            <span>القيادة الأكاديمية والمؤسسية • Academic Leadership</span>
        </span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
            {{ $info['leadership']['name_ar'] }}
        </h1>
        <p class="text-xs sm:text-sm font-semibold text-slate-600 font-sans tracking-wide" dir="ltr">
            {{ $info['leadership']['name_en'] }}
        </p>
        <div class="inline-flex flex-wrap items-center justify-center gap-2 sm:gap-3 bg-amber-500/10 text-amber-950 px-5 py-2 rounded-2xl border border-amber-300 font-black text-xs sm:text-sm">
            <span>⭐</span>
            <span>{{ $info['leadership']['title_ar'] }}</span>
            <span class="text-amber-400 font-light hidden sm:inline">|</span>
            <span class="font-sans text-xs" dir="ltr">{{ $info['leadership']['title_en'] }}</span>
        </div>
    </div>

    <!-- البطاقة القيادية الأكاديمية الكبرى -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-10 shadow-sm space-y-10">
        
        <!-- ملخص الدور والصفة المؤسسية والسيرة -->
        <div class="space-y-6 border-b border-slate-100 pb-8">
            <div class="flex flex-col sm:flex-row gap-6 items-center sm:items-start text-center sm:text-right">
                <div class="relative">
                    <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-3xl bg-gradient-to-tr from-[#0066A1] via-sky-700 to-[#F58220] flex items-center justify-center text-5xl shadow-xl ring-4 ring-sky-100 flex-shrink-0">
                        👨‍🏫
                    </div>
                    <span class="absolute -bottom-2 -right-2 bg-[#F58220] text-white text-[10px] font-black px-2.5 py-0.5 rounded-full shadow-md whitespace-nowrap">
                        {{ $yearsBadge ?? 'أكثر من 26 عامًا من العطاء' }}
                    </span>
                </div>
                <div class="space-y-2 flex-1">
                    <span class="text-xs font-black text-[#0066A1] uppercase tracking-wide block">
                        {{ $info['leadership']['role_ar'] }}
                    </span>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                        {{ $info['leadership']['name_ar'] }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-sans" dir="ltr">
                        {{ $info['leadership']['role_en'] }}
                    </p>
                </div>
            </div>

            <!-- عرض السيرة الذاتية المعتمدة بتنسيق واضح -->
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/90 text-xs sm:text-sm text-slate-800 leading-loose whitespace-pre-line font-medium shadow-inner">
{!! nl2br(e($info['leadership']['bio_summary_ar'])) !!}
            </div>

            <!-- بطاقات التواصل المباشر مع القيادة -->
            @php
                $p1 = $directPhone1 ?? '+967 775342886';
                $p2 = $directPhone2 ?? '+967 735999101';
                $cleanP1 = preg_replace('/[^0-9]/', '', $p1);
                $cleanP2 = preg_replace('/[^0-9]/', '', $p2);
            @endphp
            <div class="bg-gradient-to-r from-sky-50 via-white to-amber-50 rounded-2xl p-5 border border-sky-200 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="text-lg">📞</span>
                    <h3 class="font-black text-sm text-[#0066A1]">أرقام التواصل المباشر مع القيادة الأكاديمية:</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="bg-white p-3.5 rounded-xl border border-sky-200/80 flex items-center justify-between gap-3 shadow-sm">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-sky-100 text-[#0066A1] flex items-center justify-center font-bold">1</span>
                            <div>
                                <span class="text-[11px] text-slate-500 block">هاتف وتواصل مباشر:</span>
                                <span class="font-mono font-black text-sm text-slate-900" dir="ltr">{{ $p1 }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <a href="tel:{{ $cleanP1 }}" class="p-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-bold transition" title="اتصال هاتفي">
                                📞
                            </a>
                            <a href="https://wa.me/{{ $cleanP1 }}?text={{ urlencode('مرحباً، أود التواصل مع الدكتور فخر الدين الصوفي') }}" target="_blank" class="p-2 rounded-lg bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold transition" title="مراسلة واتساب">
                                💬
                            </a>
                        </div>
                    </div>

                    <div class="bg-white p-3.5 rounded-xl border border-amber-200/80 flex items-center justify-between gap-3 shadow-sm">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold">2</span>
                            <div>
                                <span class="text-[11px] text-slate-500 block">هاتف وتواصل مباشر:</span>
                                <span class="font-mono font-black text-sm text-slate-900" dir="ltr">{{ $p2 }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <a href="tel:{{ $cleanP2 }}" class="p-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-bold transition" title="اتصال هاتفي">
                                📞
                            </a>
                            <a href="https://wa.me/{{ $cleanP2 }}?text={{ urlencode('مرحباً، أود التواصل مع الدكتور فخر الدين الصوفي') }}" target="_blank" class="p-2 rounded-lg bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold transition" title="مراسلة واتساب">
                                💬
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- بيان الرؤية الأكاديمية -->
        <div class="bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1] text-white rounded-3xl p-8 sm:p-10 shadow-lg space-y-4 border border-white/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#F58220]/20 text-[#F58220] flex items-center justify-center text-xl font-black">
                    💬
                </div>
                <h3 class="text-lg font-black text-[#F58220]">بيان الرؤية الأكاديمية للقيادة</h3>
            </div>
            <blockquote class="text-sm sm:text-base text-sky-100 leading-loose italic border-r-4 border-[#F58220] pr-4">
                "{{ $info['leadership']['vision_statement_ar'] }}"
            </blockquote>
            <p class="text-[11px] text-sky-200 pt-2 font-mono">
                — {{ $info['leadership']['name_ar'] }}
            </p>
        </div>

        <!-- مجالات التخصص والخبرات الأكاديمية -->
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-sky-100 text-[#0066A1] flex items-center justify-center font-black text-sm">
                    📚
                </span>
                <h3 class="text-xl font-black text-slate-900">المجالات والتخصصات الأكاديمية والمهنية</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($info['leadership']['specialties_ar'] as $specialty)
                    <div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-sky-300 hover:bg-sky-50/30 transition">
                        <span class="text-[#F58220] font-bold text-lg mt-0.5">✦</span>
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
                <div class="p-5 rounded-2xl bg-sky-50/60 border border-sky-100 space-y-2">
                    <div class="text-2xl">📋</div>
                    <strong class="text-sky-950 font-black block text-sm">مراجعة واعتماد المناهج</strong>
                    <p class="text-slate-600 leading-relaxed">
                        الإشراف المباشر على هيكلية المقررات وتوافقها التام مع الإطار الأوروبي المرجعي المشترك (CEFR) ومعايير اختبار TOEFL iBT.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-amber-50/60 border border-amber-100 space-y-2">
                    <div class="text-2xl">👨‍🏫</div>
                    <strong class="text-amber-950 font-black block text-sm">تأهيل كادر المعلمين</strong>
                    <p class="text-slate-600 leading-relaxed">
                        تدريب واختيار المعلمين والمدربين بناءً على الكفاءة اللغوية والأكاديمية واستراتيجيات TEFL الحديثة والتغذية الراجعة الفعالة.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-emerald-50/60 border border-emerald-100 space-y-2">
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
                <a href="{{ route('quality') }}" class="px-5 py-2.5 rounded-xl bg-[#0066A1] hover:bg-[#004e7c] text-white font-black text-xs shadow-md transition flex items-center gap-2">
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
