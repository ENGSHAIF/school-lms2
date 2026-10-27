@extends('layouts.app', ['title' => 'برنامج اللغة الإنجليزية العامة - The Asian Star Office (ASOTS)'])

@section('content')
<div class="space-y-12 max-w-5xl mx-auto my-6">

    <!-- رأس الصفحة -->
    <div class="text-center space-y-4">
        <div class="inline-flex items-center gap-2 text-xs font-black text-emerald-800 bg-emerald-50 px-4 py-1.5 rounded-full border border-emerald-200 shadow-sm">
            <span>🚀</span>
            <span>برنامج الإطلاق التشغيلي الأول • Initial Launch Programme (Section 10)</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
            برنامج اللغة الإنجليزية العامة (General English)
        </h1>
        <p class="text-xs sm:text-sm font-semibold text-slate-600 font-sans tracking-wide" dir="ltr">
            General English Programme • The Asian Star Office (ASOTS)
        </p>

        <!-- شعار البرنامج المعتمد -->
        <div class="max-w-xl mx-auto bg-gradient-to-r from-amber-500/10 via-indigo-500/10 to-amber-500/10 border border-amber-300 p-3 rounded-2xl">
            <strong class="text-xs sm:text-sm font-black text-slate-900 block">
                "{{ $info['english_tagline_ar'] ?? 'نحن لا نعلّم الإنجليزية فحسب... بل نبني المستقبل.' }}"
            </strong>
            <span class="text-[11px] text-indigo-700 font-sans font-bold block mt-0.5" dir="ltr">
                "{{ $info['english_tagline_en'] ?? "We Don't Just Teach English... We Build Futures." }}"
            </span>
        </div>
    </div>

    <!-- البطاقة الرئيسية التعاريف والمقرر المعتمد -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-sm space-y-10">

        <!-- نظرة عامة والمنهج المعتمد -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
            <div class="lg:col-span-2 space-y-4">
                <span class="text-xs font-black text-indigo-600 uppercase tracking-wider block">نظرة عامة على البرنامج</span>
                <h2 class="text-2xl font-black text-slate-900 leading-snug">
                    تعليم لغوي متكامل يوازن بين الدقة اللغوية والطلاقة التواصلية
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    صُمم برنامج اللغة الإنجليزية العامة في <strong>ASOTS</strong> بإشراف الدكتور/ فخر الدين ناجي ردمان عبده الصوفي لتمكين المتعلمين من جميع المستويات من التحدث والاستماع والقراءة والكتابة بثقة كاملة، بالاعتماد على نموذج الحصص التفاعلية الفردية (25 دقيقة) وتقييم مخارج الحروف بالذكاء الاصطناعي.
                </p>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 text-center text-xs font-bold text-slate-700">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-xl block mb-1">🎧</span>
                        الاستماع (Listening)
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-xl block mb-1">🗣️</span>
                        التحدث (Speaking)
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-xl block mb-1">📖</span>
                        القراءة (Reading)
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-xl block mb-1">✍️</span>
                        الكتابة (Writing)
                    </div>
                </div>
            </div>

            <!-- بطاقة الكتاب المعتمد Interchange -->
            <div class="bg-gradient-to-br from-[#003859] via-[#004e7c] to-[#0066A1] text-white rounded-3xl p-6 border border-white/20 text-center space-y-3 shadow-lg">
                <span class="text-[10px] font-black uppercase text-[#F58220] bg-[#F58220]/20 px-3 py-1 rounded-full border border-[#F58220]/30">
                    المنهج الدراسي المعتمد
                </span>
                <div class="text-4xl my-2">📚</div>
                <h3 class="text-base font-black text-white font-sans" dir="ltr">Interchange – Fifth Edition</h3>
                <p class="text-[11px] text-sky-100 leading-relaxed">
                    جامعة كامبريدج (Cambridge University Press) — السلسلة الأوسع انتشاراً عالمياً لتنمية مهارات التحدث والتواصل الواقعي.
                </p>
                <div class="pt-2 text-[10px] text-amber-200 font-mono">
                    مستويات متدرجة من Pre-A1 إلى C1
                </div>
            </div>
        </div>

        <!-- رحلة المتعلم في 8 خطوات (Section 10.1 Learner Journey) -->
        <div class="pt-8 border-t border-slate-100 space-y-6">
            <div class="text-center max-w-xl mx-auto space-y-1">
                <span class="text-xs font-black text-indigo-600 uppercase tracking-wider">رحلة التعلم المنظمة</span>
                <h3 class="text-xl font-black text-slate-900">كيف تنضم وتبدأ الدراسة في 8 خطوات ميسرة؟</h3>
                <p class="text-xs text-slate-500">مسار رقمي ميسر يعمل بكفاءة عبر الهواتف الذكية والحواسيب دون تكرار إدخال البيانات</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                @foreach($info['general_english_spec']['learner_journey'] as $step)
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2 hover:border-indigo-300 hover:bg-indigo-50/20 transition">
                        <div class="flex items-center justify-between">
                            <span class="w-7 h-7 rounded-xl bg-indigo-600 text-white font-mono font-black text-xs flex items-center justify-center">
                                {{ $step['step'] }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">خطوة {{ $step['step'] }}</span>
                        </div>
                        <strong class="text-slate-900 font-black block text-sm">{{ $step['title_ar'] }}</strong>
                        <span class="text-[10px] text-slate-500 font-sans block" dir="ltr">{{ $step['title_en'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- التقديم التقني: Zoom والفصول الذكية Air Class ونظام الحصص 25 دقيقة -->
        <div class="pt-8 border-t border-slate-100 space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <span>💻</span>
                <span>آلية تقديم الدروس والتقنيات التعليمية المعتمدة</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-xs">
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <span class="text-3xl">🎙️</span>
                    <strong class="text-slate-900 font-black block text-sm">حصص فردية 25 دقيقة (1-on-1)</strong>
                    <p class="text-slate-600 leading-relaxed">
                        تركيز مكثف بنسبة 100% مع المعلم يتيح للمتعلم ممارسة التحدث المباشر وتصحيح الأخطاء الصوتية فوراً.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <span class="text-3xl">📹</span>
                    <strong class="text-slate-900 font-black block text-sm">الفصول المباشرة عبر Zoom & المتصفح</strong>
                    <p class="text-slate-600 leading-relaxed">
                        دخول بضغطة زر واحدة إلى غرفة الصف الافتراضية مع مشاركة تفاعلية لشرائح كتاب Interchange وتمارين النطق.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <span class="text-3xl">📲</span>
                    <strong class="text-slate-900 font-black block text-sm">تقارير المتابعة والواتساب الفورية</strong>
                    <p class="text-slate-600 leading-relaxed">
                        تقرير أكاديمي ومكافآت النجوم وقائمة الكلمات الجديدة تصل للمتعلم أو ولي أمره فور انتهاء الحصة مباشرة.
                    </p>
                </div>
            </div>
        </div>

        <!-- متطلبات إتمام البرنامج والشهادة المعتمدة -->
        <div class="pt-8 border-t border-slate-100 space-y-4 bg-emerald-50/60 p-6 sm:p-8 rounded-3xl border border-emerald-200">
            <div class="flex items-center gap-2 text-emerald-900 font-black text-sm">
                <span>🎓</span>
                <span>متطلبات إتمام المستوى واستحقاق شهادة ASOTS</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-emerald-950">
                <div class="p-4 rounded-xl bg-white/80 border border-emerald-200">
                    <strong class="block mb-1">1. الحضور والانضباط</strong>
                    <span>تحقيق نسبة حضور لا تقل عن 80% من إجمالي جلسات المستوى المقررة.</span>
                </div>
                <div class="p-4 rounded-xl bg-white/80 border border-emerald-200">
                    <strong class="block mb-1">2. التقييمات والاختبارات</strong>
                    <span>اجتياز الاختبارات القصيرة والاختبار النهائي بمعدل إجمالي لا يقل عن 70% (وفق نظام AQED).</span>
                </div>
                <div class="p-4 rounded-xl bg-white/80 border border-emerald-200">
                    <strong class="block mb-1">3. الشهادة الرقمية الموثقة</strong>
                    <span>إصدار شهادة رسمية برقم تسلسلي فريد يمكن التحقق منه إلكترونياً من بوابة المنصة.</span>
                </div>
            </div>
        </div>

        <!-- أزرار الإجراءات الفورية -->
        <div class="pt-8 border-t border-slate-100 flex flex-wrap gap-4 items-center justify-between">
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('onboarding') }}" class="px-6 py-3.5 rounded-xl bg-[#F58220] hover:bg-[#d96e14] text-white font-black text-xs shadow-lg transition flex items-center gap-2">
                    <span>بدء اختبار تحديد المستوى المجاني والتسجيل</span>
                    <span>🚀</span>
                </a>
                <a href="{{ route('packages.index') }}" class="px-6 py-3.5 rounded-xl bg-[#0066A1] hover:bg-[#004e7c] text-white font-bold text-xs transition">
                    استعراض باقات الحصص والأسعار
                </a>
            </div>

            <a href="{{ route('policies') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 underline">
                الاطلاع على سياسات القبول والحضور والشهادات ←
            </a>
        </div>

    </div>

</div>
@endsection
