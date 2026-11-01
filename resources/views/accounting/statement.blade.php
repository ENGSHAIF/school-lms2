@extends('layouts.app', ['title' => 'كشف حساب الطالب والمصارفة الآلية - ASOTS'])

@section('content')
<div class="max-w-5xl mx-auto my-6 space-y-4">

    <!-- أزرار الإجراءات السريعة في الأعلى (تختفي عند الطباعة) -->
    <div class="print:hidden flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-slate-900 text-white p-4 rounded-2xl shadow-md">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-amber-400 bg-amber-400/10 px-3 py-1 rounded-full border border-amber-400/20">
                كشف حساب أكاديمي ومصارفة آلية للعملات 📋
            </span>
            <span class="text-xs text-slate-300">مطابق للمعايير المحاسبية المعتمدة (نظام المتكامل بلاس ERP)</span>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('accounting.dashboard') }}?action=receipt&student_id={{ $student->id }}" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                <span>💵</span>
                <span>إصدار سند قبض لهذا الطالب</span>
            </a>

            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                <span>🖨️</span>
                <span>طباعة الكشف الرسمي / PDF</span>
            </button>

            <a href="{{ url()->previous() }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                رجوع ✕
            </a>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-6 sm:p-8 print:p-0 print:border-none print:shadow-none space-y-6">

        <!-- ترويسة كشف الحساب الرسمية -->
        <div class="border-b-2 border-slate-900 pb-5">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-right">
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-950">مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS)</h2>
                    <span class="text-xs font-bold text-indigo-700 block mt-0.5">The Asian Star Office for Translation, Training, & Consultancy Services</span>
                    <span class="text-[11px] text-slate-500 block font-mono">نظام الإدارة المالية والأكاديمية • كشف حساب الطالب التفصيلي المزدوج العملات</span>
                </div>
                <div class="text-left font-mono text-xs text-slate-600">
                    <span class="font-bold text-slate-900 block">رقم الحساب: {{ $account?->account_code ?? 'STU-' . $student->id }}</span>
                    <span>التاريخ: {{ date('Y/m/d') }}</span>
                    <span class="text-[10px] text-emerald-700 block font-bold">حالة الحساب: {{ $student->status === 'active' ? 'مقبول ومفعل 🟢' : 'بانتظار الاعتماد ⏳' }}</span>
                </div>
            </div>

            <!-- بطاقة بيانات الطالب الأكاديمية والمالية -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs">
                <div>
                    <span class="text-slate-400 block font-bold">اسم الطالب:</span>
                    <strong class="text-slate-900 text-sm">{{ $student->name }}</strong>
                    @if($student->name_en)
                        <span class="text-[10px] text-slate-400 font-mono block">{{ $student->name_en }}</span>
                    @endif
                </div>
                <div>
                    <span class="text-slate-400 block font-bold">الرقم الأكاديمي:</span>
                    <span class="text-indigo-700 font-mono font-black text-sm">{{ $student->student_code ?? $student->user_code ?? ('STU-' . $student->id) }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-bold">رقم الهاتف / الواتساب:</span>
                    <span class="text-slate-800 font-mono font-bold">{{ $student->phone_whatsapp ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-bold">ولي أمر الطالب:</span>
                    <strong class="text-slate-800">{{ $student->studentProfile?->parent?->name ?? 'غير مسجل' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block font-bold">أسعار الصرف المعتمدة:</span>
                    <span class="text-indigo-900 font-mono font-bold block text-[10px]">1 ر.س = {{ $rateSAR }} ر.ي</span>
                    <span class="text-blue-900 font-mono font-bold block text-[10px]">1 $ = {{ $rateUSD }} ر.ي</span>
                </div>
            </div>
        </div>

        <!-- شريط تبديل العملة المعروضة (في الشاشة والطباعة) -->
        <div class="print:hidden flex items-center justify-between gap-3 bg-indigo-50/70 p-3 rounded-2xl border border-indigo-100 text-xs">
            <span class="font-bold text-indigo-950 flex items-center gap-1.5">
                <span>💱</span>
                <span>المصارفة اللحظية للتقرير (Auto-Currency Switcher):</span>
            </span>
            <div class="flex items-center gap-1.5 bg-white p-1 rounded-xl border border-indigo-200 text-xs font-bold">
                <button type="button" onclick="setStatementCurrencyView('all')" id="btn-view-all" class="curr-view-btn px-3 py-1 rounded-lg bg-indigo-600 text-white transition">
                    عرض شامل بالعملات المقابلة (يمني + سعودي)
                </button>
                <button type="button" onclick="setStatementCurrencyView('yer')" id="btn-view-yer" class="curr-view-btn px-3 py-1 rounded-lg text-slate-600 hover:text-indigo-700 transition">
                    🇾🇪 مصارفة كاملة بالريال اليمني
                </button>
                <button type="button" onclick="setStatementCurrencyView('sar')" id="btn-view-sar" class="curr-view-btn px-3 py-1 rounded-lg text-slate-600 hover:text-indigo-700 transition">
                    🇸🇦 مصارفة كاملة بالريال السعودي
                </button>
            </div>
        </div>

        <!-- جدول الحركات المالية المحاسبي (مدين - دائن - رصيد) مع المصارفة -->
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs border border-slate-300 border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold text-center">
                        <th class="p-2.5 border border-slate-700 w-24">التاريخ</th>
                        <th class="p-2.5 border border-slate-700 w-28">رقم المستند</th>
                        <th class="p-2.5 border border-slate-700 text-right">البيان والتفاصيل الأكاديمية</th>
                        <th class="p-2.5 border border-slate-700 w-28">المبلغ الأصلي</th>
                        <th class="p-2.5 border border-slate-700 w-28 bg-indigo-950 text-indigo-200 col-yer">
                            مدين (ر.ي)
                        </th>
                        <th class="p-2.5 border border-slate-700 w-28 bg-emerald-950 text-emerald-200 col-yer">
                            دائن (ر.ي)
                        </th>
                        <th class="p-2.5 border border-slate-700 w-28 bg-amber-950 text-amber-200 col-sar">
                            مدين (ر.س)
                        </th>
                        <th class="p-2.5 border border-slate-700 w-28 bg-teal-950 text-teal-200 col-sar">
                            دائن (ر.س)
                        </th>
                        <th class="p-2.5 border border-slate-700 w-32 bg-slate-800 text-amber-400">
                            الرصيد التراكمي
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-mono">
                    @php
                        // ندمج الأقساط (مدين) وسندات القبض (دائن) في خط زمني واحد
                        $entries = collect();

                        foreach ($installments as $inst) {
                            $amt = (float) $inst->amount;
                            $cur = strtoupper($inst->currency ?: 'SAR');
                            $amtYER = \App\Models\Currency::convert($amt, $cur, 'YER');
                            $amtSAR = \App\Models\Currency::convert($amt, $cur, 'SAR');

                            $courseName = $inst->course ? ($inst->course->name_ar ?: $inst->course->name_en) : null;
                            $desc = $courseName 
                                ? "قيمة دورة [{$courseName}] - مستوى: {$inst->level?->name} (القسط {$inst->installment_number})"
                                : "رسوم دراسية: {$inst->level?->name} (القسط {$inst->installment_number})";

                            $entries->push((object)[
                                'date' => $inst->created_at,
                                'doc_no' => 'INV-' . $inst->id,
                                'type' => 'debit',
                                'description' => $desc,
                                'orig_amount' => $amt,
                                'orig_currency' => $cur,
                                'debit_yer' => $amtYER,
                                'credit_yer' => 0.00,
                                'debit_sar' => $amtSAR,
                                'credit_sar' => 0.00,
                            ]);
                        }

                        foreach ($receiptList as $rec) {
                            $amt = (float) $rec->amount;
                            $cur = strtoupper($rec->currency ?: 'YER');
                            $amtYER = \App\Models\Currency::convert($amt, $cur, 'YER');
                            $amtSAR = \App\Models\Currency::convert($amt, $cur, 'SAR');

                            $entries->push((object)[
                                'date' => $rec->created_at,
                                'doc_no' => $rec->voucher_no,
                                'type' => 'credit',
                                'description' => "سند قبض مالي: {$rec->statement}" . ($rec->manual_no ? " (دفتري: {$rec->manual_no})" : ''),
                                'orig_amount' => $amt,
                                'orig_currency' => $cur,
                                'debit_yer' => 0.00,
                                'credit_yer' => $amtYER,
                                'debit_sar' => 0.00,
                                'credit_sar' => $amtSAR,
                            ]);
                        }

                        // فرز الحركات تصاعدياً حسب التاريخ
                        $entries = $entries->sortBy('date')->values();

                        $runningYER = 0;
                        $runningSAR = 0;
                        $totDebitYER = 0;
                        $totCreditYER = 0;
                        $totDebitSAR = 0;
                        $totCreditSAR = 0;
                    @endphp

                    @forelse($entries as $idx => $entry)
                        @php
                            $runningYER += ($entry->debit_yer - $entry->credit_yer);
                            $runningSAR += ($entry->debit_sar - $entry->credit_sar);

                            $totDebitYER += $entry->debit_yer;
                            $totCreditYER += $entry->credit_yer;
                            $totDebitSAR += $entry->debit_sar;
                            $totCreditSAR += $entry->credit_sar;
                        @endphp
                        <tr class="hover:bg-slate-50 transition {{ $entry->type === 'credit' ? 'bg-emerald-50/30' : '' }}">
                            <td class="p-2 border border-slate-200 text-center">{{ $entry->date->format('Y-m-d') }}</td>
                            <td class="p-2 border border-slate-200 text-center font-bold {{ $entry->type === 'credit' ? 'text-emerald-700' : 'text-indigo-700' }}">
                                {{ $entry->doc_no }}
                            </td>
                            <td class="p-2 border border-slate-200 font-sans text-slate-800 text-right">
                                {{ $entry->description }}
                            </td>
                            <td class="p-2 border border-slate-200 text-center font-bold text-slate-700">
                                {{ number_format($entry->orig_amount, 2) }} {{ $entry->orig_currency }}
                            </td>

                            <!-- مبالغ الريال اليمني -->
                            <td class="p-2 border border-slate-200 text-center font-bold text-rose-600 col-yer">
                                {{ $entry->debit_yer > 0 ? number_format($entry->debit_yer, 2) : '-' }}
                            </td>
                            <td class="p-2 border border-slate-200 text-center font-bold text-emerald-600 col-yer">
                                {{ $entry->credit_yer > 0 ? number_format($entry->credit_yer, 2) : '-' }}
                            </td>

                            <!-- مبالغ الريال السعودي -->
                            <td class="p-2 border border-slate-200 text-center font-bold text-rose-700 col-sar">
                                {{ $entry->debit_sar > 0 ? number_format($entry->debit_sar, 2) : '-' }}
                            </td>
                            <td class="p-2 border border-slate-200 text-center font-bold text-emerald-700 col-sar">
                                {{ $entry->credit_sar > 0 ? number_format($entry->credit_sar, 2) : '-' }}
                            </td>

                            <!-- الرصيد التراكمي -->
                            <td class="p-2 border border-slate-200 text-center font-bold bg-slate-50">
                                <span class="block text-slate-900">{{ number_format($runningYER, 2) }} ر.ي</span>
                                <span class="block text-[10px] text-amber-700">({{ number_format($runningSAR, 2) }} ر.س)</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400 font-sans">
                                لا توجد حركات مالية مسجلة باسم هذا الطالب حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 font-bold border-t-2 border-slate-900 text-xs text-center font-mono">
                        <td colspan="4" class="p-3 text-right font-sans font-black">إجمالي الحركات المحاسبية:</td>
                        <td class="p-3 border border-slate-300 text-rose-600 col-yer">{{ number_format($totDebitYER, 2) }} ر.ي</td>
                        <td class="p-3 border border-slate-300 text-emerald-600 col-yer">{{ number_format($totCreditYER, 2) }} ر.ي</td>
                        <td class="p-3 border border-slate-300 text-rose-700 col-sar">{{ number_format($totDebitSAR, 2) }} ر.س</td>
                        <td class="p-3 border border-slate-300 text-emerald-700 col-sar">{{ number_format($totCreditSAR, 2) }} ر.س</td>
                        <td class="p-3 border border-slate-300 bg-slate-900 text-amber-400 font-black">
                            {{ number_format($runningYER, 2) }} ر.ي
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @php
            $netBalanceYER = $runningYER;
            $netBalanceSAR = $runningSAR;
            $netBalanceUSD = \App\Models\Currency::convert($runningYER, 'YER', 'USD');
        @endphp

        <!-- بطاقة خلاصة الرصيد النهائي والمصارفة الآلية بين العملات (المتكامل بلاس ERP) -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 shadow-md space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-white/10 pb-3">
                <div>
                    <h3 class="text-sm font-black text-amber-400 flex items-center gap-2">
                        <span>⚖️</span>
                        <span>خلاصة الرصيد الصافي المتبقي ذمة على الطالب (المصارفة التلقائية):</span>
                    </h3>
                    <span class="text-xs text-slate-300">يتم خصم كل سند قبض مسدد آلياً، ويظهر الرصيد مصارفاً بالعملة المحلية والسعودية والدولار</span>
                </div>
                <span class="text-xs font-mono font-bold text-emerald-400 bg-emerald-400/20 px-3 py-1 rounded-full border border-emerald-400/30">
                    {{ $netBalanceYER <= 0 ? 'خالص ومسدد بالكامل ✅' : 'متبقي مديونية مستحقة ⏳' }}
                </span>
            </div>

            <!-- بطاقات الرصيد بالعملات المختلفة -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- 1. بالريال اليمني -->
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 border border-white/10 space-y-1">
                    <span class="text-[10px] text-slate-300 font-bold block">🇾🇪 الرصيد بالعملة المحلية (الريال اليمني):</span>
                    <div class="text-2xl font-black text-emerald-400 font-mono">
                        {{ number_format($netBalanceYER, 2) }} <span class="text-sm font-sans font-bold">ريال يمني</span>
                    </div>
                    <p class="text-[10px] text-slate-300 leading-relaxed font-sans">
                        {{ \App\Helpers\ArabicCurrencyHelper::tafqeet($netBalanceYER, 'YER') }}
                    </p>
                </div>

                <!-- 2. بالريال السعودي -->
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 border border-amber-400/20 space-y-1">
                    <span class="text-[10px] text-amber-300 font-bold block">🇸🇦 الرصيد المعادل بالريال السعودي:</span>
                    <div class="text-2xl font-black text-amber-300 font-mono">
                        {{ number_format($netBalanceSAR, 2) }} <span class="text-sm font-sans font-bold">ريال سعودي</span>
                    </div>
                    <p class="text-[10px] text-slate-300 leading-relaxed font-sans">
                        {{ \App\Helpers\ArabicCurrencyHelper::tafqeet($netBalanceSAR, 'SAR') }}
                    </p>
                </div>

                <!-- 3. بالدولار الأمريكي -->
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 border border-blue-400/20 space-y-1">
                    <span class="text-[10px] text-blue-300 font-bold block">🇺🇸 الرصيد المعادل بالدولار الأمريكي:</span>
                    <div class="text-2xl font-black text-blue-300 font-mono">
                        ${{ number_format($netBalanceUSD, 2) }} <span class="text-sm font-sans font-bold">دولار</span>
                    </div>
                    <p class="text-[10px] text-slate-300 leading-relaxed font-sans">
                        {{ \App\Helpers\ArabicCurrencyHelper::tafqeet($netBalanceUSD, 'USD') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- التوقيعات والاعتماد الرسمي (تظهر في الطباعة) -->
        <div class="pt-8 border-t border-slate-300 grid grid-cols-3 gap-6 text-center text-xs text-slate-700 font-bold">
            <div class="space-y-8">
                <span class="block">محاسب الطلاب:</span>
                <span class="border-b border-dashed border-slate-400 pb-1 inline-block min-w-[120px]">....................</span>
            </div>
            <div class="space-y-8">
                <span class="block">المراجع المالي / المدير المالي:</span>
                <span class="border-b border-dashed border-slate-400 pb-1 inline-block min-w-[120px]">....................</span>
            </div>
            <div class="space-y-8">
                <span class="block">ختم الإدارة المالية والاعتماد:</span>
                <span class="border-b border-dashed border-slate-400 pb-1 inline-block min-w-[120px]">....................</span>
            </div>
        </div>

    </div>

</div>

<script>
function setStatementCurrencyView(view) {
    document.querySelectorAll('.curr-view-btn').forEach(b => {
        b.classList.remove('bg-indigo-600', 'text-white');
        b.classList.add('text-slate-600');
    });

    const activeBtn = document.getElementById('btn-view-' + view);
    if (activeBtn) {
        activeBtn.classList.add('bg-indigo-600', 'text-white');
        activeBtn.classList.remove('text-slate-600');
    }

    const yerCols = document.querySelectorAll('.col-yer');
    const sarCols = document.querySelectorAll('.col-sar');

    if (view === 'all') {
        yerCols.forEach(c => c.style.display = '');
        sarCols.forEach(c => c.style.display = '');
    } else if (view === 'yer') {
        yerCols.forEach(c => c.style.display = '');
        sarCols.forEach(c => c.style.display = 'none');
    } else if (view === 'sar') {
        yerCols.forEach(c => c.style.display = 'none');
        sarCols.forEach(c => c.style.display = '');
    }
}
</script>
@endsection
