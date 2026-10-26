@extends('layouts.app', ['title' => 'خدمات الترجمة والترجمة الشفوية - The Asian Star Office (ASOTS)'])

@section('content')
<div class="space-y-12 max-w-5xl mx-auto my-6">

    <!-- رأس الصفحة -->
    <div class="text-center space-y-4">
        <span class="inline-flex items-center gap-2 text-xs font-black text-amber-700 bg-amber-50 px-4 py-1.5 rounded-full border border-amber-200 shadow-sm">
            <span>📄</span>
            <span>ترجمة معتمدة وشفوية رفيعة المستوى • Certified Translation & Interpreting</span>
        </span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
            خدمات الترجمة والترجمة الشفوية المعتمدة
        </h1>
        <p class="text-xs sm:text-sm font-semibold text-slate-600 font-sans tracking-wide" dir="ltr">
            The Asian Star Office for Translation, Training, & Consultancy Services (ASOTS)
        </p>
        <p class="text-xs sm:text-sm text-slate-600 max-w-2xl mx-auto leading-relaxed">
            ترجمة أكاديمية وقانونية وتقنية متخصصة بين اللغتين العربية والإنجليزية، بإشراف لغوي دقيق وتدقيق أكاديمي يضمن أعلى درجات الدقة والسرية والمطابقة للمعايير الدولية.
        </p>
    </div>

    <!-- البطاقة الرئيسية -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-sm space-y-12">

        <!-- الإشراف الأكاديمي والضمانات -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-6 p-6 rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white">
            <div class="space-y-2 text-center sm:text-right">
                <span class="text-xs font-black text-amber-400 block uppercase">ضمان الدقة والنزاهة الأكاديمية</span>
                <h2 class="text-lg font-black text-white">إشراف مباشر من أستاذ اللسانيات التطبيقية والترجمة</h2>
                <p class="text-xs text-slate-300">
                    تخضع جميع الأعمال لتدقيق مزدوج من مترجمين محترفين وأكاديميين متخصصين في اللسانيات والتواصل بين الثقافات.
                </p>
            </div>
            <a href="{{ route('leadership') }}" class="px-5 py-2.5 rounded-xl bg-amber-400 text-slate-950 hover:bg-amber-300 font-black text-xs whitespace-nowrap transition">
                عن القيادة الأكاديمية
            </a>
        </div>

        <!-- مجالات وأقسام الترجمة المعتمدة -->
        <div class="space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <span>📚</span>
                <span>مجالات الترجمة المعتمدة (عربي ⟷ إنجليزي)</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 1. الترجمة الأكاديمية -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-300 transition space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">🎓</span>
                        <div>
                            <h4 class="text-base font-black text-slate-900">الترجمة الأكاديمية والبحثية</h4>
                            <span class="text-[11px] text-indigo-600 font-bold">Academic & Research Translation</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        ترجمة أطروحات الماجستير والدكتوراه، الأوراق العلمية المحكمة، ملخصات البحوث، الشهادات الجامعية، كشوف الدرجات، ومقترحات المنح الدراسية الدولية.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-2">
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">شهادات جامعية</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">أوراق محكمة</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">توصيات أكاديمية</span>
                    </div>
                </div>

                <!-- 2. الترجمة القانونية والرسمية -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-300 transition space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">⚖️</span>
                        <div>
                            <h4 class="text-base font-black text-slate-900">الترجمة القانونية والرسمية المعتمدة</h4>
                            <span class="text-[11px] text-indigo-600 font-bold">Certified Legal Translation</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        ترجمة العقود والاتفاقيات، السجلات التجارية، الوثائق الرسمية للجهات الحكومية والمنظمات والسفارات، واللوائح الداخلية للشركات بدقة تامة.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-2">
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">عقود واتفاقيات</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">وثائق سفارات</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">سجلات تجارية</span>
                    </div>
                </div>

                <!-- 3. الترجمة التقنية والطبية -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-300 transition space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">🔬</span>
                        <div>
                            <h4 class="text-base font-black text-slate-900">الترجمة الطبية والتقنية</h4>
                            <span class="text-[11px] text-indigo-600 font-bold">Medical & Technical Translation</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        ترجمة التقارير الطبية التخصصية، دراسات الجدوى، الكتيبات الإرشادية للأجهزة والمعدات، ومحتوى المواقع الإلكترونية والأنظمة التقنية.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-2">
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">تقارير طبية</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">أدلة فنية</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">برمجيات ومواقع</span>
                    </div>
                </div>

                <!-- 4. الترجمة الشفوية وتدريب المترجمين -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-indigo-300 transition space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">🎙️</span>
                        <div>
                            <h4 class="text-base font-black text-slate-900">الترجمة الشفوية وتأهيل المترجمين</h4>
                            <span class="text-[11px] text-indigo-600 font-bold">Interpreting & Translator Training</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        تغطية المؤتمرات والندوات والاجتماعات الافتراضية والحضورية بالترجمة الفورية والتعاقبية، مع برامج تأهيل وتدريب المترجمين الصاعدين.
                    </p>
                    <div class="flex flex-wrap gap-1.5 pt-2">
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">ترجمة فورية للمؤتمرات</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">ترجمة تعاقبية</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md font-bold">دورات تدريب المترجمين</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- معايير النزاهة والسرية والالتزام -->
        <div class="pt-8 border-t border-slate-100 space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <span>🛡️</span>
                <span>التزامات ASOTS تجاه وثائق العملاء</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 text-xs">
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <span class="text-2xl">🔒</span>
                    <strong class="text-slate-900 font-black block text-sm">السرية التامة للبيانات</strong>
                    <p class="text-slate-600 leading-relaxed">
                        نلتزم باتفاقيات سرية صارمة (NDA) لحماية وثائق العملاء الأكاديمية والمالية والشخصية من أي وصول غير مصرح به.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <span class="text-2xl">⏱️</span>
                    <strong class="text-slate-900 font-black block text-sm">الالتزام بالمواعيد المحددة</strong>
                    <p class="text-slate-600 leading-relaxed">
                        جدول زمني واضح ومحدد لكل مشروع ترجمة مع خيارات الترجمة العاجلة مع الحفاظ على أعلى معايير الجودة.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <span class="text-2xl">🧾</span>
                    <strong class="text-slate-900 font-black block text-sm">سندات قبض وفواتير رسمية</strong>
                    <p class="text-slate-600 leading-relaxed">
                        توثيق مالي متوافق مع المعايير المحاسبية المعتمدة لكل طلب لضمان الشفافية المؤسسية.
                    </p>
                </div>
            </div>
        </div>

        <!-- استمارة طلب تسعير ترجمة فورية -->
        <div class="pt-8 border-t border-slate-100 bg-amber-50/50 p-8 rounded-3xl border border-amber-200 space-y-4">
            <div class="space-y-1">
                <span class="text-xs font-black text-amber-800 uppercase block">طلب ترجمة وثيقة أو خدمة شفوية</span>
                <h3 class="text-xl font-black text-slate-900">احصل على تسعير رسمي وتحديد موعد التسليم</h3>
                <p class="text-xs text-slate-600">
                    يمكنك إرسال وثيقتك أو مواصفات مؤتمرك مباشرة إلى فريق الترجمة للحصول على مراجعة أكاديمية وعرض سعر رسمي خلال ساعات قليلة.
                </p>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', site_setting('whatsapp_platform_number') ?: site_setting('whatsapp_number', '967735999101')) }}?text={{ urlencode('مرحباً، أود الاستفسار عن خدمة ترجمة معتمدة لدى مكتب ASOTS') }}" target="_blank" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                    <span>💬</span>
                    <span>إرسال الملف عبر الواتساب للاعتماد السريع</span>
                </a>
                <a href="{{ route('contact') }}" class="px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs transition flex items-center gap-2">
                    <span>📨</span>
                    <span>تعبئة نموذج طلب ترجمة رسمي</span>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
