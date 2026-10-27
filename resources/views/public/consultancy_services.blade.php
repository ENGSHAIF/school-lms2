@extends('layouts.app', ['title' => 'الاستشارات الأكاديمية والتربوية والمهنية - The Asian Star Office (ASOTS)'])

@section('content')
<div class="space-y-12 max-w-5xl mx-auto my-6">

    <!-- رأس الصفحة -->
    <div class="text-center space-y-4">
        <span class="inline-flex items-center gap-2 text-xs font-black text-indigo-700 bg-indigo-50 px-4 py-1.5 rounded-full border border-indigo-200 shadow-sm">
            <span>💼</span>
            <span>استشارات أكاديمية وتطوير مؤسسي • Academic & Professional Consultancy</span>
        </span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
            الاستشارات الأكاديمية والتربوية والمهنية
        </h1>
        <p class="text-xs sm:text-sm font-semibold text-slate-600 font-sans tracking-wide" dir="ltr">
            The Asian Star Office for Translation, Training, & Consultancy Services (ASOTS)
        </p>
        <p class="text-xs sm:text-sm text-slate-600 max-w-2xl mx-auto leading-relaxed">
            خبرة أكاديمية وبحثية استثنائية يقدمها الدكتور/ فخر الدين ناجي ردمان عبده الصوفي ونخبة من الخبراء لدعم الأفراد والجامعات والمؤسسات التعليمية والشركات في بناء المناهج، وتطوير الكوادر، وتحقيق التميز.
        </p>
    </div>

    <!-- البطاقة الرئيسية -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-sm space-y-12">

        <!-- الإشراف الاستشاري -->
        <div class="p-8 rounded-2xl bg-gradient-to-r from-[#003859] via-[#004e7c] to-[#0066A1] text-white flex flex-col md:flex-row items-center justify-between gap-6 border border-white/20 shadow-md">
            <div class="space-y-2 text-center md:text-right">
                <span class="text-xs font-black text-[#F58220] block uppercase">استشارات مبنية على رصيد أكاديمي وبحثي</span>
                <h2 class="text-xl font-black text-white">إشراف وتوجيه مباشر من الدكتور/ فخر الدين ناجي ردمان عبده الصوفي</h2>
                <p class="text-xs text-sky-100">
                    أستاذ اللسانيات التطبيقية الحديثة، والتواصل بين الثقافات، وخبير التطوير المهني وتدريب المعلمين.
                </p>
            </div>
            <a href="{{ route('leadership') }}" class="px-5 py-2.5 rounded-xl bg-[#F58220] text-white hover:bg-[#d96e14] font-black text-xs whitespace-nowrap transition">
                السيرة الأكاديمية للقيادة
            </a>
        </div>

        <!-- حقول ومسارات الاستشارات -->
        <div class="space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <span>🎯</span>
                <span>المجالات الاستشارية الرئيسية لـ ASOTS</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 1. تصميم المناهج والمقررات -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-300 transition space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">📐</span>
                        <div>
                            <h4 class="text-base font-black text-slate-900">تصميم وتطوير المناهج ومخرجات التعلم</h4>
                            <span class="text-[11px] text-indigo-600 font-bold">Curriculum Design & Learning Outcomes</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        بناء وتطوير الخطط والمناهج اللغوية للمدارس والمعاهد والجامعات، ومواءمتها مع المعايير الدولية (CEFR) وصياغة مصفوفات مخرجات التعلم والتقويم.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-2">
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">مصفوفات المخرجات</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">أدلة المعلم</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">تقويم المناهج</span>
                    </div>
                </div>

                <!-- 2. القبول الجامعي والمنح الدولية -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-300 transition space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">🎓</span>
                        <div>
                            <h4 class="text-base font-black text-slate-900">القبول الجامعي والدراسات العليا والمنح الدولية</h4>
                            <span class="text-[11px] text-indigo-600 font-bold">University Admissions & Research Proposals</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        توجيه الطلاب والباحثين في إعداد مقترحات البحوث (Research Proposals)، وخطابات الدوافع (SOP)، ومراجعة السيرة الذاتية الأكاديمية واستراتيجيات اختبارات TOEFL و IELTS.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-2">
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">مقترحات الماجستير/الدكتوراه</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">خطابات التقديم</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">المنح التنافسية</span>
                    </div>
                </div>

                <!-- 3. تدريب وتأهيل المعلمين (TEFL) -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-300 transition space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">👨‍🏫</span>
                        <div>
                            <h4 class="text-base font-black text-slate-900">تأهيل المعلمين وبرامج TEFL / TESOL</h4>
                            <span class="text-[11px] text-indigo-600 font-bold">Teacher Training & Professional Development</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        استشارات متخصصة للمؤسسات التعليمية لتدريب وتأهيل المعلمين على أحدث طرائق التدريس التفاعلية، وإدارة الصف، واستراتيجيات التقييم البديل.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-2">
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">إعداد وتأهيل الكوادر</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">تقييم أداء المعلمين</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">ورش عمل تخصصية</span>
                    </div>
                </div>

                <!-- 4. الجودة والاعتماد المؤسسي اللغوي -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-300 transition space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">🛡️</span>
                        <div>
                            <h4 class="text-base font-black text-slate-900">الجودة والتميز المؤسسي (AQED)</h4>
                            <span class="text-[11px] text-indigo-600 font-bold">Quality Assurance & Institutional Excellence</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        مساعدة المراكز والمؤسسات في بناء أنظمة ضمان الجودة الداخلية، وتوثيق الأدلة التعليمية، وتطبيق سياسة التحسين المستمر.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-2">
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">معايير الجودة المبنية على الأدلة</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">مراجعة التقارير</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">التقييم المؤسسي</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- خطوات الحصول على استشارة أكاديمية -->
        <div class="pt-8 border-t border-slate-100 space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <span>🛤️</span>
                <span>مسار تقديم الخدمة الاستشارية</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5 text-center">
                    <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-black text-sm flex items-center justify-center mx-auto">1</span>
                    <strong class="text-slate-900 block font-bold">تقديم الطلب</strong>
                    <span class="text-slate-500 text-[11px]">تحديد موضوع الاستشارة وإرفاق الوثائق أو نبذة عن المشروع.</span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5 text-center">
                    <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-black text-sm flex items-center justify-center mx-auto">2</span>
                    <strong class="text-slate-900 block font-bold">الدراسة الأكاديمية</strong>
                    <span class="text-slate-500 text-[11px]">مراجعة الطلب بواسطة القيادة الأكاديمية وإعداد خطة العمل.</span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5 text-center">
                    <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-black text-sm flex items-center justify-center mx-auto">3</span>
                    <strong class="text-slate-900 block font-bold">الجلسة الاستشارية</strong>
                    <span class="text-slate-500 text-[11px]">لقاء افتراضي أو حضوري مكثف مع الخبير لمناقشة التوصيات.</span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5 text-center">
                    <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-black text-sm flex items-center justify-center mx-auto">4</span>
                    <strong class="text-slate-900 block font-bold">التقرير النهائي</strong>
                    <span class="text-slate-500 text-[11px]">تسليم تقرير استشاري مكتوب وموثق يحدد خارطة الطريق التنفيذية.</span>
                </div>
            </div>
        </div>

        <!-- حجز جلسة استشارية -->
        <div class="pt-8 border-t border-slate-100 bg-indigo-50/60 p-8 rounded-3xl border border-indigo-200 space-y-4">
            <div class="space-y-1">
                <span class="text-xs font-black text-indigo-800 uppercase block">حجز موعد استشارة أكاديمية أو مهنية</span>
                <h3 class="text-xl font-black text-slate-900">تحدث مباشرة مع مستشاري ASOTS</h3>
                <p class="text-xs text-slate-600">
                    نحن هنا لمساعدتك في اتخاذ القرارات الأكاديمية والمهنية الصائبة بناءً على أسس بحثية ومؤسسية رصينة.
                </p>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', site_setting('whatsapp_platform_number') ?: site_setting('whatsapp_number', '967735999101')) }}?text={{ urlencode('مرحباً، أود حجز استشارة أكاديمية وتربوية لدى مكتب ASOTS') }}" target="_blank" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                    <span>💬</span>
                    <span>تنسيق موعد الاستشارة عبر الواتساب</span>
                </a>
                <a href="{{ route('contact') }}" class="px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs transition flex items-center gap-2">
                    <span>📨</span>
                    <span>إرسال تفاصيل الطلب عبر نموذج التواصل</span>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
