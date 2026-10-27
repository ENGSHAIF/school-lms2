@extends('layouts.app', ['title' => 'برامج الكبار والتوفل ورجال الأعمال - ' . config('asots.name_ar')])

@section('content')
<div class="space-y-16 py-4">

    <!-- بطل الصفحة (Adults & TOEFL Hero) -->
    <section class="bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1] text-white rounded-3xl p-8 sm:p-14 shadow-2xl relative overflow-hidden border border-white/20">
        <div class="max-w-3xl space-y-6 relative z-10">
            <div class="inline-flex items-center gap-2 bg-[#F58220]/20 text-[#F58220] text-xs px-4 py-1 rounded-full font-bold border border-[#F58220]/30">
                <span>💼</span>
                <span>برامج اللغة الإنجليزية المهنية، الأكاديمية، والتحضير للتوفل الدولي</span>
            </div>

            <h1 class="text-3xl sm:text-5xl font-black leading-tight">
                إنجليزية الكبار والتوفل الدولي
                <span class="block text-2xl sm:text-3xl text-[#F58220] font-extrabold mt-1">
                    TOEFL iBT & Professional Business English
                </span>
            </h1>

            <p class="text-sm sm:text-base text-sky-100 leading-relaxed font-medium">
                سواء كنت تسعى لاجتياز اختبار TOEFL iBT بدرجة عالية للحصول على قبول جامعي أو منحة دولية، أو ترغب في تطوير مهاراتك لمقابلات العمل والاجتماعات الدولية، صممنا لك في مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS) برامج فردية مكثفة 1-on-1 مع نخبة من المدربين المعتمدين دولياً.
            </p>

            <div class="flex flex-wrap items-center gap-4 pt-2">
                <a href="{{ route('onboarding') }}" class="bg-[#F58220] hover:bg-[#d96e14] text-white font-black text-xs sm:text-sm px-8 py-3.5 rounded-2xl shadow-xl transition flex items-center gap-2">
                    <span>🚀</span>
                    <span>احجز استشارتك وحصتك الفردية التجريبية</span>
                </a>
                <a href="{{ route('student.booking') }}" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 font-bold text-xs sm:text-sm px-6 py-3.5 rounded-2xl transition">
                    🎙️ حجز مواعيد المساء
                </a>
            </div>
        </div>
        <div class="absolute -top-10 -right-10 w-80 h-80 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
    </section>

    <!-- الأركان الأربعة لبرنامج الكبار (4 Core Pillars) -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-3">
            <div class="w-12 h-12 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                🎯
            </div>
            <h3 class="font-black text-base text-slate-900">التحضير لـ TOEFL iBT</h3>
            <p class="text-xs text-slate-600 leading-relaxed">تدريب استراتيجي مكثف على أقسام التحدث والاستماع والقراءة والكتابة مع محاكاة كاملة للاختبار الرسمي.</p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-3">
            <div class="w-12 h-12 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                💼
            </div>
            <h3 class="font-black text-base text-slate-900">إنجليزية الأعمال (Business)</h3>
            <p class="text-xs text-slate-600 leading-relaxed">كتابة المراسلات الرسمية والتقارير المهنية، وإدارة الاجتماعات والتفاوض بلغة احترافية راقية.</p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-3">
            <div class="w-12 h-12 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                👔
            </div>
            <h3 class="font-black text-base text-slate-900">المقابلات الوظيفية والسفارات</h3>
            <p class="text-xs text-slate-600 leading-relaxed">جلسات محاكاة حية لمقابلات العمل الدولية ومقابلات التأشيرات والسفارات للتخلص من التوتر تماماً.</p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-3">
            <div class="w-12 h-12 bg-rose-50 border border-rose-200 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                🌙
            </div>
            <h3 class="font-black text-base text-slate-900">مرونة كاملة للموظفين</h3>
            <p class="text-xs text-slate-600 leading-relaxed">مواعيد مسائية وليلة مرنة تناسب الموظفين ورجال الأعمال، مع إمكانية تعديل المواعيد وإلغائها بسلاسة.</p>
        </div>
    </section>

    <!-- كيف يساعدك نظام فحص النطق بالذكاء الاصطناعي في اجتياز قسم المحادثة Speaking؟ -->
    <section class="bg-slate-900 text-white rounded-3xl p-8 sm:p-12 shadow-xl border border-slate-800">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div class="space-y-4">
                <span class="text-xs font-bold text-amber-400 bg-amber-400/10 px-3.5 py-1 rounded-full border border-amber-400/20">
                    تقنية حصرية لطلاب ASOTS
                </span>
                <h2 class="text-2xl sm:text-3xl font-black">حل مشكلة الـ Speaking في التوفل بشكل جذري</h2>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                    قسم المحادثة في TOEFL iBT هو العائق الأكبر لدى أغلب المتقدمين. في منصة ASOTS، ندمج الذكاء الاصطناعي لفحص نبرة صوتك، سرعة تدفق الكلمات (Fluency)، وسلامة القواعد أثناء إجابتك، مع ملاحظات فورية من معلمك خلال الحصة.
                </p>
                <div class="space-y-2 pt-2 text-xs">
                    <div class="flex items-center gap-2 text-amber-300 font-bold">
                        <span>✔</span>
                        <span>فحص فوري لمخارج الحروف مع نسب مئوية دقيقة لكل سؤال.</span>
                    </div>
                    <div class="flex items-center gap-2 text-amber-300 font-bold">
                        <span>✔</span>
                        <span>مقارنة نطقك بنطق المتحدثين الأصليين (Native Pitch Benchmark).</span>
                    </div>
                    <div class="flex items-center gap-2 text-amber-300 font-bold">
                        <span>✔</span>
                        <span>تقارير تقدم يومية تحفظ في ملفك وتُرسل مباشرة إلى واتسابك.</span>
                    </div>
                </div>
            </div>

            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 text-center space-y-4">
                <div class="text-5xl">🎙️</div>
                <h4 class="font-bold text-white text-base">جرب الفحص الصوتي الآن</h4>
                <p class="text-xs text-slate-400 leading-relaxed">خض اختبار التحدث التجريبي القصير واكتشف درجتك التقديرية وملاحظات النطق الفورية مجاناً.</p>
                <a href="{{ route('onboarding') }}" class="inline-block bg-gradient-to-r from-amber-500 to-indigo-600 hover:from-amber-600 hover:to-indigo-700 text-slate-950 font-black text-xs px-6 py-3 rounded-xl shadow-md transition">
                    ابدأ الفحص الصوتي المجاني 🚀
                </a>
            </div>
        </div>
    </section>

    <!-- باقات الحصص المخصصة للكبار والتوفل -->
    <section class="space-y-6">
        <div class="text-center max-w-xl mx-auto">
            <h2 class="text-2xl font-black text-slate-900">باقات التوفل وإنجليزية الأعمال</h2>
            <p class="text-xs text-slate-500 mt-1">حصص فردية 1-on-1 مع سندات قبض وفواتير معتمدة رسمياً</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
            @foreach($packages as $pkg)
                <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm flex flex-col justify-between space-y-6 hover:shadow-lg transition">
                    <div>
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10px] font-black bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full border border-indigo-200">
                                    حصة فردية 25 دقيقة
                                </span>
                                <h3 class="text-xl font-black text-slate-900 mt-2">{{ $pkg->title }}</h3>
                            </div>
                            <div class="text-left font-mono">
                                <span class="text-2xl sm:text-3xl font-black text-slate-950 block">{{ $pkg->formatted_price }}</span>
                                <span class="text-[11px] text-slate-500 font-sans block">{{ $pkg->currency_name }}</span>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed mt-4">{{ $pkg->description }}</p>

                        <div class="mt-4 pt-4 border-t border-slate-100 text-xs space-y-2">
                            <div class="flex items-center gap-2 text-slate-700">
                                <span class="text-emerald-500">✔</span>
                                <span>{{ $pkg->lessons_count }} حصة تدريبية مباشرة 1-on-1</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-700">
                                <span class="text-emerald-500">✔</span>
                                <span>تقارير صوتية بالذكاء الاصطناعي وتقارير واتساب</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-700">
                                <span class="text-emerald-500">✔</span>
                                <span>سند قبض رسمي معتمد وفق المعايير المحاسبية المعتمدة</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('packages.index') }}" class="block text-center w-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs py-3 rounded-xl transition shadow-md">
                        الاشتراك وإصدار سند القبض 💳
                    </a>
                </div>
            @endforeach
        </div>
    </section>

</div>
@endsection
