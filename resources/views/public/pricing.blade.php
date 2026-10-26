@extends('layouts.app', ['title' => 'الأسعار وباقات الحصص والنظام المالي - ' . config('asots.name_ar')])

@section('content')
<div class="space-y-16 py-4">

    <!-- بطل الصفحة (Pricing Hero) -->
    <section class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-8 sm:p-14 shadow-2xl relative overflow-hidden border border-slate-800">
        <div class="max-w-3xl space-y-4 relative z-10">
            <span class="inline-block bg-emerald-500/20 text-emerald-300 text-xs px-3.5 py-1 rounded-full font-bold border border-emerald-400/30 font-mono">
                المعايير المحاسبية المالية المعتمدة
            </span>
            <h1 class="text-3xl sm:text-4xl font-black">باقات الحصص الفردية والأسعار الرسمية</h1>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-medium">
                نقدم باقات مرنة لحصص الـ 25 دقيقة الفردية (1-on-1) بأسعار عادلة وشفافة. كل عملية دفع يصدر بها سند قبض رسمي مفقط باللغة العربية وبرقم تسلسلي معتمد يضاف فوراً إلى كشف حسابك المالي.
            </p>
            <div class="pt-2 flex flex-wrap gap-3">
                <a href="{{ route('onboarding') }}" class="bg-amber-400 hover:bg-amber-500 text-slate-950 font-black text-xs px-6 py-3 rounded-xl shadow-md transition">
                    ابدأ بحصة تجريبية مجانية 1-on-1 🎁
                </a>
                <a href="{{ route('student.booking') }}" class="bg-white/10 hover:bg-white/20 text-white font-bold text-xs px-6 py-3 rounded-xl border border-white/20 transition">
                    جدول الحصص المتاح 📅
                </a>
            </div>
        </div>
        <div class="absolute -bottom-10 -left-10 w-80 h-80 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>
    </section>

    <!-- بطاقات الباقات المتاحة (Packages Grid) -->
    <section class="space-y-6">
        <div class="text-center max-w-xl mx-auto">
            <h2 class="text-2xl font-black text-slate-900">باقات الحصص الفردية 25 دقيقة (1-on-1)</h2>
            <p class="text-xs text-slate-500 mt-1">اختر الباقة المناسبة لمستواك وأهدافك الأكاديمية</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            @foreach($packages as $pkg)
                <div class="bg-white rounded-3xl border-2 border-slate-200 hover:border-indigo-500 p-8 shadow-sm hover:shadow-xl transition flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10px] font-black bg-amber-50 text-amber-700 px-3 py-1 rounded-full border border-amber-200">
                                    {{ $pkg->lessons_count }} حصة تدريبية
                                </span>
                                <h3 class="text-xl font-black text-slate-900 mt-2">{{ $pkg->title }}</h3>
                            </div>
                            <div class="text-left font-mono">
                                <span class="text-2xl sm:text-3xl font-black text-slate-900 block">{{ $pkg->formatted_price }}</span>
                                <span class="text-[11px] text-slate-500 font-sans block">{{ \App\Helpers\ArabicCurrencyHelper::format(round($pkg->price / $pkg->lessons_count, 1), $pkg->currency, 1) }} / للحصة</span>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">{{ $pkg->description }}</p>

                        <div class="pt-4 border-t border-slate-100 text-xs space-y-2.5">
                            <div class="flex items-center gap-2 text-slate-700">
                                <span class="text-emerald-500">✔</span>
                                <span>جلسات تفاعلية حية فردية 1-on-1 مدة كل جلسة 25 دقيقة</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-700">
                                <span class="text-emerald-500">✔</span>
                                <span>فحص مخارج الحروف الصوتي بالذكاء الاصطناعي</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-700">
                                <span class="text-emerald-500">✔</span>
                                <span>تقارير تقدم فورية ومكافآت النجوم عبر الواتساب</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-700">
                                <span class="text-emerald-500">✔</span>
                                <span>سند قبض مالي رسمي فوري وسجل محاسبي معتمد</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('packages.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                        <button type="submit" class="w-full bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs py-3.5 rounded-xl transition shadow-md flex items-center justify-center gap-2">
                            <span>شحن الباقة وإصدار السند فوراً</span>
                            <span>💳</span>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </section>

    <!-- شريط المعايير المالية لالمعايير المحاسبية (YemenSoft Standards) -->
    <section class="bg-slate-50 border border-slate-200 rounded-3xl p-8 sm:p-10 space-y-6">
        <div class="text-center max-w-xl mx-auto">
            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full uppercase tracking-wider border border-emerald-200">
                الشفافية والتوثيق المحاسبي
            </span>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-2">معايير النظام المالي لمكتب النجم الآسيوي (ASOTS)</h2>
            <p class="text-xs text-slate-500 mt-1">مطابق للأنظمة والمعايير المحاسبية المعتمدة لسندات القبض والصرف</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-2">
                <div class="text-2xl">🧾</div>
                <h4 class="font-black text-sm text-slate-900">سندات قبض برقم تسلسلي آلي</h4>
                <p class="text-xs text-slate-600 leading-relaxed">توليد سند قبض غير قابل للتكرار لكل دفعة، مع التفقيط الكامل للمبلغ باللغة العربية (مثال: مائة وعشرون دولار أمريكي فقط لا غير).</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-2">
                <div class="text-2xl">📊</div>
                <h4 class="font-black text-sm text-slate-900">كشف حساب تفصيلي للطالب</h4>
                <p class="text-xs text-slate-600 leading-relaxed">سجل مالي يوضح الحركات الدائنة والمدينة والرصيد التراكمي وتواريخ الحصص، متاح للتحميل والطباعة كـ PDF في أي لحظة.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-2">
                <div class="text-2xl">💱</div>
                <h4 class="font-black text-sm text-slate-900">تعدد العملات وطرق السداد</h4>
                <p class="text-xs text-slate-600 leading-relaxed">دعم السداد بالدولار (USD)، والريال السعودي (SAR)، والريال اليمني (YER) عبر الكريمي، النجم، والتحويلات المصرفية.</p>
            </div>
        </div>
    </section>

</div>
@endsection
