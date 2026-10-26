@extends('layouts.app', ['title' => 'السياسات والشروط والنزاهة المؤسسية - The Asian Star Office (ASOTS)'])

@section('content')
<div class="space-y-12 max-w-5xl mx-auto my-6">

    <!-- رأس الصفحة -->
    <div class="text-center space-y-4">
        <span class="inline-flex items-center gap-2 text-xs font-black text-slate-700 bg-slate-100 px-4 py-1.5 rounded-full border border-slate-300 shadow-sm">
            <span>⚖️</span>
            <span>السياسات المؤسسية والحوكمة الرقمية • Policies, Terms & Governance (Section 14)</span>
        </span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
            السياسات والشروط والنزاهة المؤسسية
        </h1>
        <p class="text-xs sm:text-sm font-semibold text-slate-600 font-sans tracking-wide" dir="ltr">
            Institutional Policies, Terms of Service & Accreditation Guidelines • ASOTS
        </p>
        <p class="text-xs sm:text-sm text-slate-500 max-w-2xl mx-auto leading-relaxed">
            تلتزم منصة ASOTS بالشفافية المطلقة وتطبيق أعلى معايير الحوكمة والنزاهة المؤسسية والأخلاقيات المهنية لحماية حقوق المتعلمين والعملاء والكوادر الأكاديمية.
        </p>
    </div>

    <!-- البطاقة الرئيسية للسياسات -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-sm space-y-10">

        <!-- 1. سياسة التسجيل والقبول -->
        <div class="space-y-3 pb-8 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-sm">1</span>
                <h2 class="text-lg font-black text-slate-900">سياسة التسجيل والقبول واختبار تحديد المستوى</h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pr-11">
                يُعد اجتياز أو طلب اختبار تحديد المستوى المجاني الخطوة الأولى الإلزامية لضمان تسكين المتعلم في المستوى الذي يناسب قدراته الفعلية بدقة. لا يتم قبول أي طالب في مستويات متقدمة دون التحقق من اجتيازه المتطلبات السابقة للمستوى، وتخضع جميع طلبات التسجيل للمراجعة الإدارية والأكاديمية قبل تأكيد الجدول الدراسي.
            </p>
        </div>

        <!-- 2. سياسة الحضور والالتزام بالمواعيد -->
        <div class="space-y-3 pb-8 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-sm">2</span>
                <h2 class="text-lg font-black text-slate-900">سياسة الحضور والالتزام بالمواعيد وجلسات الـ 25 دقيقة</h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pr-11">
                نظراً لخصوصية نظام الجلسات الفردية 1-on-1، يُلزم المتعلم بالدخول إلى الفصل الافتراضي في الموعد المحدد تماماً. يُسمح بإلغاء أو إعادة جدولة الحصة قبل موعدها بساعتين على الأقل دون خصم من رصيد الحصص. وفي حال تأخر المتعلم لأكثر من 10 دقائق دون عذر مسبق، تُحتسب الحصة تلقائياً لضمان تعويض المعلم عن وقته المحجوز.
            </p>
        </div>

        <!-- 3. سياسة الرسوم وسندات القبض والاسترداد -->
        <div class="space-y-3 pb-8 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-sm">3</span>
                <h2 class="text-lg font-black text-slate-900">السياسة المالية، سندات القبض، واسترداد الرسوم</h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pr-11">
                وفقاً للمعايير المحاسبية المعتمدة في ASOTS (المعايير المحاسبية المعتمدة)، يصدر لكل معاملة مالية سند قبض رسمي فوري برقم تسلسلي غير مكرر مع التفقيط الكامل. يحق للمتعلم طلب استرداد رصيد الحصص المتبقية غير المستهلكة خلال 14 يوماً من تاريخ الشراء، مع خصم رسوم التحويل البنكي ورسوم الحصص التي تم حضورها بالفعل.
            </p>
        </div>

        <!-- 4. سياسة الخصوصية وحماية البيانات -->
        <div class="space-y-3 pb-8 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-sm">4</span>
                <h2 class="text-lg font-black text-slate-900">الخصوصية وحماية البيانات والسرية الأكاديمية والمهنية</h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pr-11">
                تلتزم ASOTS بحماية تامة لبيانات المستخدمين ووثائق الترجمة والاستشارات. لا يتم مشاركة أو بيع أي معلومات شخصية أو درجات أو وثائق لأي طرف ثالث خارجي. وتخضع وثائق الترجمة الرسمية لاتفاقيات سرية صارمة، كما تُحفظ تسجيلات الدروس والتقارير الأكاديمية في بيئة سحابية مشفرة ومحمية بضوابط وصول محددة.
            </p>
        </div>

        <!-- 5. سياسة إصدار الشهادات والتحقق الرقمي -->
        <div class="space-y-3 pb-8 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-sm">5</span>
                <h2 class="text-lg font-black text-slate-900">سياسة إصدار الشهادات والتحقق الرقمي المستقل</h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pr-11">
                تصدر شهادات ASOTS الرسمية للطلاب الذين استوفوا متطلبات إتمام المقرر بنسبة حضور لا تقل عن 80% ومعدل تقييم إجمالي لا يقل عن 70% وفق نموذج الجودة AQED. تحمل كل شهادة رقماً تسلسلياً فريداً يتيح لأصحاب العمل والجامعات التحقق الفوري من صحتها عبر بوابة التحقق الإلكتروني على المنصة.
            </p>
            <div class="pr-11 pt-2">
                <a href="{{ route('verify_certificate') }}" class="text-xs font-black text-indigo-600 hover:text-indigo-800 underline">
                    الدخول إلى نظام التحقق من صحة الشهادات الرقمية ←
                </a>
            </div>
        </div>

        <!-- 6. سياسة الشكاوى والدعم وتجربة المتعلم -->
        <div class="space-y-3 pb-8 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-sm">6</span>
                <h2 class="text-lg font-black text-slate-900">إدارة الشكاوى، المقترحات، وضمان جودة الخدمة (AQED)</h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pr-11">
                يتابع قسم الجودة والتميز (AQED) جميع شكاوى واستفسارات المتعلمين وأولياء الأمور بجدية تامة. وتضمن المنصة الرد على الشكاوى ودراستها خلال 24 ساعة كحد أقصى، مع اتخاذ الإجراءات التصحيحية الموثقة لضمان أفضل تجربة تعليمية ومهنية.
            </p>
        </div>

        <!-- 7. تنويهات الاعتماد والاختبارات الدولية الصارمة (Section 8.5 & 14) -->
        <div class="bg-rose-50/70 border border-rose-200 rounded-3xl p-6 sm:p-8 space-y-3 text-xs text-rose-950">
            <div class="flex items-center gap-2 font-black text-sm text-rose-900">
                <span>🛡️</span>
                <span>تنويهات النزاهة والالتزام بعدم الادعاء بدون برهان (Accreditation Integrity)</span>
            </div>
            <p class="leading-relaxed">
                <strong>• اختبارات TOEFL و IELTS:</strong> تقدم ASOTS برامج تأهيل وتدريب مكثف وتطوير للكفاية اللغوية فقط، ولا تدعي المنصة إصدار نتائج أو شهادات رسمية لاختبارات IELTS أو TOEFL، إذ تصدر تلك الشهادات حصراً عبر المنظمات والهيئات الدولية المالكة لها.
            </p>
            <p class="leading-relaxed">
                <strong>• الاعتماد والاعتراف الخارجي:</strong> التزاماً بالقاعدة الحاكمة لمنصة ASOTS «لا ادعاء بالجودة دون دليل»، لا تنشر المنصة أي مزاعم أو ادعاءات غير موثقة بشأن اعتمادات دولية أو شراكات لم تُوثّق رسمياً بمستندات قانونية معتمدة.
            </p>
        </div>

    </div>

</div>
@endsection
