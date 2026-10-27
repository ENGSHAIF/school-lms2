@extends('layouts.app', ['title' => 'كشف حساب الطالب التفصيلي'])

@section('content')
<div class="max-w-4xl mx-auto my-6">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-lg p-8 print:p-0 print:border-none print:shadow-none">

        <!-- ترويسة كشف الحساب الرسمية (وفق الأنظمة المحاسبية المعتمدة) -->
        <div class="border-b-2 border-slate-900 pb-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-base font-black text-slate-900">The Asian Star Office for Translation, Training, & Consultancy Services (ASOTS)</h2>
                    <span class="text-xs text-slate-500 block font-bold">مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات • كشف حساب طالب (المعايير المحاسبية المعتمدة)</span>
                </div>
                <div class="text-left print:hidden">
                    <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-xl transition flex items-center gap-1.5 shadow-sm">
                        <span>🖨️</span>
                        <span>طباعة كشف الحساب / PDF</span>
                    </button>
                </div>
            </div>

            <!-- بطاقة بيانات الطالب الأكاديمية والمالية -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mt-6 bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs">
                <div>
                    <span class="text-slate-400 block font-bold">اسم الطالب:</span>
                    <span class="text-slate-900 font-black text-sm">{{ $student->name }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-bold">الرقم الأكاديمي:</span>
                    <span class="text-indigo-600 font-mono font-black text-sm">{{ $student->student_code ?? 'STU-' . $student->id }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-bold">رقم الحساب المالي:</span>
                    <span class="text-slate-900 font-mono font-bold">{{ $account?->account_code ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-bold">عملة الحساب:</span>
                    <span class="text-emerald-700 font-bold">{{ $account?->currency_name ?? 'ريال يمني' }} ({{ $account?->currency_symbol ?? 'ر.ي' }})</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-bold">تاريخ استخراج الكشف:</span>
                    <span class="text-slate-900 font-mono font-bold">{{ date('Y/m/d') }}</span>
                </div>
            </div>
        </div>

        @php
            $cur = $account?->currency ?? 'YER';
            $curSymbol = \App\Helpers\ArabicCurrencyHelper::currencySymbol($cur);
        @endphp

        <!-- جدول الحركات المالية (الحركات المحاسبية المعتمدة: مدين / دائن / رصيد) -->
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold">
                        <th class="p-3 border border-slate-700">التاريخ</th>
                        <th class="p-3 border border-slate-700">رقم السند/المستند</th>
                        <th class="p-3 border border-slate-700">البيان والتفاصيل</th>
                        <th class="p-3 border border-slate-700 text-left font-mono">مدين ({{ $curSymbol }})</th>
                        <th class="p-3 border border-slate-700 text-left font-mono">دائن ({{ $curSymbol }})</th>
                        <th class="p-3 border border-slate-700 text-left font-mono">الرصيد التراكمي ({{ $curSymbol }})</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @php
                        $runningBalance = 0;
                        $totalDebit = 0;
                        $totalCredit = 0;
                    @endphp

                    <!-- 1. الأقساط والرسوم المستحقة (مدين) -->
                    @foreach($installments as $inst)
                        @php
                            $runningBalance += $inst->amount;
                            $totalDebit += $inst->amount;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 border border-slate-200 font-mono">{{ $inst->created_at->format('Y/m/d') }}</td>
                            <td class="p-3 border border-slate-200 font-mono font-bold text-slate-700">INV-{{ $inst->id }}</td>
                            <td class="p-3 border border-slate-200">رسوم دراسية: {{ $inst->level?->name }} (القسط {{ $inst->installment_number }})</td>
                            <td class="p-3 border border-slate-200 text-left font-mono font-bold text-red-600">{{ number_format($inst->amount, 2) }} {{ $curSymbol }}</td>
                            <td class="p-3 border border-slate-200 text-left font-mono text-slate-400">0.00 {{ $curSymbol }}</td>
                            <td class="p-3 border border-slate-200 text-left font-mono font-bold text-slate-900">{{ number_format($runningBalance, 2) }} {{ $curSymbol }}</td>
                        </tr>
                    @endforeach

                    <!-- 2. السندات المسددة (دائن) -->
                    @foreach($vouchers as $v)
                        @php
                            $runningBalance -= $v->amount;
                            $totalCredit += $v->amount;
                        @endphp
                        <tr class="hover:bg-slate-50 bg-emerald-50/20">
                            <td class="p-3 border border-slate-200 font-mono">{{ $v->created_at->format('Y/m/d') }}</td>
                            <td class="p-3 border border-slate-200 font-mono font-bold text-emerald-700">{{ $v->voucher_no }}</td>
                            <td class="p-3 border border-slate-200">{{ $v->statement }} {{ $v->manual_no ? '(دفتري: ' . $v->manual_no . ')' : '' }}</td>
                            <td class="p-3 border border-slate-200 text-left font-mono text-slate-400">0.00 {{ $curSymbol }}</td>
                            <td class="p-3 border border-slate-200 text-left font-mono font-bold text-emerald-600">{{ number_format($v->amount, 2) }} {{ $curSymbol }}</td>
                            <td class="p-3 border border-slate-200 text-left font-mono font-bold text-slate-900">{{ number_format($runningBalance, 2) }} {{ $curSymbol }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 font-bold border-t-2 border-slate-900 text-xs">
                        <td colspan="3" class="p-3 text-right">الإجمالي الكلي للحركات:</td>
                        <td class="p-3 text-left font-mono text-red-600">{{ number_format($totalDebit, 2) }} {{ $curSymbol }}</td>
                        <td class="p-3 text-left font-mono text-emerald-600">{{ number_format($totalCredit, 2) }} {{ $curSymbol }}</td>
                        <td class="p-3 text-left font-mono font-black text-indigo-700 text-sm">{{ number_format($runningBalance, 2) }} {{ $curSymbol }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- خلاصة الرصيد النهائي -->
        <div class="mt-6 p-4 rounded-xl border border-slate-200 bg-slate-50 flex justify-between items-center text-xs">
            <div>
                <span class="text-slate-500 font-bold">الرصيد المتبقي ذمة على الطالب:</span>
                <span class="font-bold text-slate-900 mr-1">{{ \App\Helpers\ArabicCurrencyHelper::tafqeet($runningBalance, $cur) }}</span>
            </div>
            <div class="text-left font-mono font-black text-base {{ $runningBalance > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                صافي الحساب: {{ number_format($runningBalance, 2) }} {{ $curSymbol }}
            </div>
        </div>

        <!-- التوقيعات الرسمية -->
        <div class="grid grid-cols-3 gap-6 mt-12 pt-8 border-t border-slate-200 text-center text-xs text-slate-600 font-bold">
            <div>المحاسب المالي المختص</div>
            <div>مدير الشؤون المالية</div>
            <div>ختم الأكاديمية والاعتماد</div>
        </div>

    </div>

</div>
@endsection
