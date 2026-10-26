@extends('layouts.app', ['title' => 'مركز التقارير المالية والقوائم الختامية'])

@section('content')
<div class="space-y-6">

    <!-- شريط التنقل المحاسبي الموحد -->
    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>📊</span>
                <span>مركز التقارير والقوائم المالية الختامية (Financial Reports Hub)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                استخراج القوائم الختامية وموازين المراجعة ومؤشرات الربحية ومطابقة الحسابات العامة وفق المعايير المحاسبية المعتمدة
            </p>
        </div>
    </div>

    <!-- شبكة بطاقات التقارير المتخصصة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- 1. ميزان المراجعة -->
        <a href="{{ route('accounting.reports.trial_balance') }}" class="bg-white p-6 rounded-3xl border border-slate-200 hover:border-indigo-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-2xl group-hover:scale-110 transition shadow-inner">
                    ⚖️
                </div>
                <div>
                    <h3 class="font-black text-base text-slate-900 group-hover:text-indigo-700 transition">
                        ميزان المراجعة (Trial Balance)
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        استخراج ميزان المراجعة الشامل بالمجاميع والأرصدة لجميع حسابات الدليل المحاسبي والتأكد من مطابقة الأستاذ العام وتوازن المدين والدائن.
                    </p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-indigo-700 group-hover:translate-x-1 transition">
                <span>فتح تقرير ميزان المراجعة</span>
                <span>←</span>
            </div>
        </a>

        <!-- 2. قائمة الدخل والأرباح والخسائر -->
        <a href="{{ route('accounting.reports.income_statement') }}" class="bg-white p-6 rounded-3xl border border-slate-200 hover:border-emerald-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-2xl group-hover:scale-110 transition shadow-inner">
                    📈
                </div>
                <div>
                    <h3 class="font-black text-base text-slate-900 group-hover:text-emerald-700 transition">
                        قائمة الدخل والأرباح والخسائر (P&L)
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        تحليل صافي أرباح ونشاط المنصة عبر مقارنة إجمالي إيرادات الدورات والخدمات والترجمة مع التكاليف التدريبية والمصروفات التشغيلية.
                    </p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-emerald-700 group-hover:translate-x-1 transition">
                <span>فتح قائمة الدخل</span>
                <span>←</span>
            </div>
        </a>

        <!-- 3. دفتر الأستاذ العام -->
        <a href="{{ route('accounting.reports.general_ledger') }}" class="bg-white p-6 rounded-3xl border border-slate-200 hover:border-blue-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-2xl group-hover:scale-110 transition shadow-inner">
                    📖
                </div>
                <div>
                    <h3 class="font-black text-base text-slate-900 group-hover:text-blue-700 transition">
                        دفتر الأستاذ العام (General Ledger)
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        كشف حساب تفصيلي لأي حساب مالي في الدليل المحاسبي يعرض الرصيد الافتتاحي، الحركات اليومية المدينة والدائنة، والرصيد التراكمي.
                    </p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-700 group-hover:translate-x-1 transition">
                <span>فتح دفتر الأستاذ</span>
                <span>←</span>
            </div>
        </a>

        <!-- 4. تقرير مراكز التكلفة -->
        <a href="{{ route('accounting.reports.cost_centers') }}" class="bg-white p-6 rounded-3xl border border-slate-200 hover:border-amber-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-2xl group-hover:scale-110 transition shadow-inner">
                    🏢
                </div>
                <div>
                    <h3 class="font-black text-base text-slate-900 group-hover:text-amber-700 transition">
                        تقرير ربحية مراكز التكلفة (Cost Centers P&L)
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        استعراض تفصيلي لأرباح كل قسم ومشروع (قسم اللغات، دبلوم الإنجليزية، الترجمة الفورية، الاستشارات) ومصروفاتها المباشرة.
                    </p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-amber-700 group-hover:translate-x-1 transition">
                <span>فتح تقرير مراكز التكلفة</span>
                <span>←</span>
            </div>
        </a>

        <!-- 5. كشف حركة الصناديق والبنوك -->
        <a href="{{ route('accounting.cash_boxes.index') }}" class="bg-white p-6 rounded-3xl border border-slate-200 hover:border-teal-400 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-2xl group-hover:scale-110 transition shadow-inner">
                    🏦
                </div>
                <div>
                    <h3 class="font-black text-base text-slate-900 group-hover:text-teal-700 transition">
                        حركة الخزائن والصناديق والبنوك
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        متابعة حركة النقدية اليومية، أرصدة العُهد، الخزن الفرعية، الحسابات البنكية والمحافظ، وإجراءات الجرد والمطابقة.
                    </p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-teal-700 group-hover:translate-x-1 transition">
                <span>فتح دليل الصناديق</span>
                <span>←</span>
            </div>
        </a>

        <!-- 6. دفتر قيود اليومية العامة -->
        <a href="{{ route('accounting.journal_entries.index') }}" class="bg-white p-6 rounded-3xl border border-slate-200 hover:border-slate-800 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center text-2xl group-hover:scale-110 transition shadow-inner">
                    ⚖️
                </div>
                <div>
                    <h3 class="font-black text-base text-slate-900 group-hover:text-slate-900 transition">
                        سجل قيود اليومية العامة (GL)
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        استعراض كافة القيود المحاسبية المنفذة في النظام مع خيارات التصفية بالتواريخ، والمصادر، وحالات الترحيل والقيود العكسية.
                    </p>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-900 group-hover:translate-x-1 transition">
                <span>فتح دفتر اليومية</span>
                <span>←</span>
            </div>
        </a>

    </div>

</div>
@endsection
