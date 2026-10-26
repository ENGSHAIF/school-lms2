@extends('layouts.app', ['title' => 'قسم الجودة والتميز (AQED) - The Asian Star Office (ASOTS)'])

@section('content')
<div class="space-y-12 max-w-5xl mx-auto my-6">

    <!-- رأس الصفحة -->
    <div class="text-center space-y-4">
        <span class="inline-flex items-center gap-2 text-xs font-black text-emerald-700 bg-emerald-50 px-4 py-1.5 rounded-full border border-emerald-200 shadow-sm">
            <span>🛡️</span>
            <span>ضمان الجودة الأكاديمية والاعتماد المؤسسي</span>
        </span>
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
            {{ $info['aqed']['name_ar'] }}
        </h1>
        <p class="text-xs sm:text-sm font-semibold text-slate-600 font-sans tracking-wide" dir="ltr">
            {{ $info['aqed']['name_en'] }}
        </p>

        <!-- الشعار الحاكم لقسم الجودة -->
        <div class="inline-flex items-center gap-3 bg-gradient-to-r from-red-600 via-rose-700 to-red-600 text-white px-6 py-2.5 rounded-2xl shadow-md font-black text-sm sm:text-base border border-red-500">
            <span>⚖️</span>
            <span>المبدأ الحاكم: {{ $info['aqed']['motto_ar'] }}</span>
            <span class="opacity-70 font-light hidden sm:inline">|</span>
            <span class="font-sans text-xs hidden sm:inline" dir="ltr">{{ $info['aqed']['motto_en'] }}</span>
        </div>
    </div>

    <!-- البطاقة الرئيسية لنظام الجودة والتميز -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-sm space-y-10">

        <!-- الفلسفة المنهجية لـ AQED -->
        <div class="space-y-4">
            <h2 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>🔍</span>
                <span>فلسفة الجودة في منصة ASOTS الدولية</span>
            </h2>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                انطلاقاً من توجيهات القيادة الأكاديمية ممثلة بالأستاذ الدكتور/ فخر الدين الصوفي، ترفض منصة <strong>ASOTS</strong> الادعاءات التسويقية الفضفاضة وتلتزم بنموذج الجودة القائم على الأدلة الرقمية والتوثيق المباشر. كل حصة دراسية، وكل اختبار، وكل سند مالي هو جزء من منظومة موثقة بالكامل تخضع للرقابة والتدقيق الأكاديمي والتحسين المستمر.
            </p>
        </div>

        <!-- الركائز الخمس لضمان الجودة -->
        <div class="space-y-6">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <span>🌟</span>
                <span>الركائز الإجرائية الخمس لضمان الجودة</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($info['aqed']['principles'] as $index => $principle)
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 flex items-start gap-3 hover:border-emerald-300 hover:bg-emerald-50/20 transition">
                        <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xs flex-shrink-0">
                            {{ $index + 1 }}
                        </span>
                        <p class="text-xs sm:text-sm font-bold text-slate-800 leading-relaxed">
                            {{ $principle }}
                        </p>
                    </div>
                @endforeach
                <div class="p-5 rounded-2xl bg-gradient-to-br from-indigo-900 to-slate-900 text-white flex items-center gap-3">
                    <span class="text-2xl">📈</span>
                    <div>
                        <strong class="text-xs block text-amber-400 font-black">حلقة التحسين المغلقة</strong>
                        <span class="text-[11px] text-slate-300">ملاحظة الطالب ← تقرير الجودة ← تدريب وتطوير المعلم فوراً.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول توزيع الدرجات والتقييم المعتمد (100 درجة) -->
        <div class="pt-8 border-t border-slate-100 space-y-6">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                <div>
                    <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
                        <span>📊</span>
                        <span>نموذج التقييم وتوزيع الدرجات المعتمد (100 درجة)</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">
                        توزيع درجات عادل وعلمي يركز على الممارسة الفعلية والاستماع والتحدث والانضباط الصفي.
                    </p>
                </div>
                <span class="px-3 py-1 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-black border border-indigo-200 whitespace-nowrap self-start sm:self-auto">
                    معيار ASOTS الرسمي
                </span>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-100/80 text-slate-700 font-black border-b border-slate-200">
                        <tr>
                            <th class="p-4">#</th>
                            <th class="p-4">عنصر التقييم والقياس</th>
                            <th class="p-4">الوزن النسبي والدرجة</th>
                            <th class="p-4">آلية الرصد والتوثيق</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <tr class="hover:bg-slate-50">
                            <td class="p-4 font-black text-slate-400">1</td>
                            <td class="p-4 font-bold text-slate-900">الاختبار القصير الأول (Quiz 1)</td>
                            <td class="p-4 font-black text-indigo-600">5 درجات</td>
                            <td class="p-4 text-slate-500">اختبار إلكتروني قصير في نهاية الوحدة الأولى لقياس استيعاب القواعد والمفردات.</td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="p-4 font-black text-slate-400">2</td>
                            <td class="p-4 font-bold text-slate-900">الاختبار القصير الثاني (Quiz 2)</td>
                            <td class="p-4 font-black text-indigo-600">5 درجات</td>
                            <td class="p-4 text-slate-500">تقييم مرحلي في منتصف المستوى للتأكد من ثبات المهارات اللغوية المكتسبة.</td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="p-4 font-black text-slate-400">3</td>
                            <td class="p-4 font-bold text-slate-900">الاستماع والتحدث (Listening & Speaking)</td>
                            <td class="p-4 font-black text-amber-600">10 درجات</td>
                            <td class="p-4 text-slate-500">جلسة محادثة حية مع فحص مخارج الحروف والطلاقة الصوتية عبر الذكاء الاصطناعي.</td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="p-4 font-black text-slate-400">4</td>
                            <td class="p-4 font-bold text-slate-900">الاختبار النهائي الشامل (Final Exam)</td>
                            <td class="p-4 font-black text-purple-600">30 درجة</td>
                            <td class="p-4 text-slate-500">اختبار تحريري وشفهي شامل لكامل المستوى بنموذج يحاكي المعايير الدولية.</td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="p-4 font-black text-slate-400">5</td>
                            <td class="p-4 font-bold text-slate-900">الحضور والتفاعل والمشاركة المستمرة</td>
                            <td class="p-4 font-black text-emerald-600">50 درجة</td>
                            <td class="p-4 text-slate-500">رصد آلي بنظام الحضور في جلسات الـ 25 دقيقة الفردية وتقييم تفاعل الطالب في كل حصة.</td>
                        </tr>
                        <tr class="bg-indigo-50/70 font-black text-slate-900">
                            <td class="p-4">∑</td>
                            <td class="p-4 text-sm text-indigo-900">المجموع النهائي لاجتياز المستوى</td>
                            <td class="p-4 text-base text-indigo-700">100 درجة</td>
                            <td class="p-4 text-indigo-900">درجة النجاح المعتمدة: 70% للترقية إلى المستوى التالي في سلم CEFR.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- دورة التحسين المستمر بالوثائق والأدلة -->
        <div class="pt-8 border-t border-slate-100 space-y-6">
            <h3 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <span>🔄</span>
                <span>كيف نحقق معادلة «لا ادعاء بالجودة دون دليل»؟</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-xs">
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <span class="text-2xl">📝</span>
                    <strong class="text-slate-900 font-black block text-sm">تقرير المعلم بعد كل 25 دقيقة</strong>
                    <p class="text-slate-600 leading-relaxed">
                        يلزم النظام المعلم بتعبئة تقرير تقييم دقيق لكل طالب فور انتهاء الحصة يوثق نقاط القوة، الكلمات المتعلمة، والتوصيات.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <span class="text-2xl">💬</span>
                    <strong class="text-slate-900 font-black block text-sm">إشعار ولي الأمر الفوري</strong>
                    <p class="text-slate-600 leading-relaxed">
                        يتلقى ولي الأمر تقريراً فورياً عبر تطبيق الواتساب وبوابته الخاصة لمتابعة أداء طفله وعدد النجوم التي جمعها دون تأخير.
                    </p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <span class="text-2xl">🧾</span>
                    <strong class="text-slate-900 font-black block text-sm">التوثيق المالي والمحاسبي المعتمد</strong>
                    <p class="text-slate-600 leading-relaxed">
                        لا رسوم مخفية ولا معاملات عشوائية؛ كل اشتراك يصدر له سند قبض رسمي معتمد وفقاً للمعايير والأنظمة المحاسبية المعتمدة.
                    </p>
                </div>
            </div>
        </div>

        <!-- أسئلة واستفسارات الجودة ودعم الطلاب -->
        <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-emerald-50/60 p-6 rounded-2xl border border-emerald-200">
            <div class="space-y-1 text-center sm:text-right">
                <strong class="text-sm font-black text-emerald-950 block">لديك استفسار أو مقترح لشؤون الطلاب وضمان الجودة؟</strong>
                <p class="text-xs text-emerald-800">
                    قسم الجودة (AQED) يتابع جميع التقارير والملاحظات مع الإشراف الأكاديمي لضمان تجربة تعليمية فائقة.
                </p>
            </div>
            <a href="{{ route('contact') }}" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-black text-xs shadow-md transition whitespace-nowrap">
                تواصل مع قسم الجودة مباشرة
            </a>
        </div>

    </div>

</div>
@endsection
