@extends('layouts.app', ['title' => 'الموازنة التقديرية للحسابات - يمن سوفت ERP'])

@section('content')
<div class="space-y-6">

    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 mb-1 flex items-center gap-2">
                <span>📈</span>
                <span>الموازنة التقديرية وتحليل الانحرافات (Account Budgets)</span>
            </h1>
            <p class="text-xs text-slate-500">وفق دليل المتكامل بلاس ERP: تخطيط المصروفات والإيرادات الشهرية، المقارنة الآلية مع الفعلي المنفذ، وحساب نسب التجاوز أو الوفر المالي.</p>
        </div>
    </div>

    <!-- فلتر اختيار الحساب والسنة -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('accounting.budgets.index') }}" method="GET" class="flex flex-wrap items-end gap-3 text-xs">
            <div>
                <label class="block font-black text-slate-700 mb-1">السنة المالية:</label>
                <select name="year" class="rounded-xl border-slate-300 py-1.5 px-3 text-xs font-bold">
                    <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026</option>
                    <option value="2027" {{ $year == 2027 ? 'selected' : '' }}>2027</option>
                </select>
            </div>

            <div class="flex-1 min-w-[260px]">
                <label class="block font-black text-slate-700 mb-1">اختر الحساب المستهدف:</label>
                <select name="account_id" class="w-full rounded-xl border-slate-300 py-1.5 px-3 text-xs font-bold" onchange="this.form.submit()">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ $selectedAccount?->id == $acc->id ? 'selected' : '' }}>
                            {{ $acc->account_code }} - {{ $acc->account_name }} ({{ $acc->nature === 'debit' ? 'مصروف/أصل' : 'إيراد/التزام' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white font-black rounded-xl hover:bg-slate-900 transition">
                عرض الموازنة
            </button>
        </form>
    </div>

    @if($selectedAccount)
    <!-- نموذج إدخال الموازنة وتوزيعها -->
    <form action="{{ route('accounting.budgets.save') }}" method="POST">
        @csrf
        <input type="hidden" name="fiscal_year" value="{{ $year }}">
        <input type="hidden" name="account_id" value="{{ $selectedAccount->id }}">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-wrap justify-between items-center gap-2">
                <div>
                    <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                        <span>📊</span>
                        <span>موازنة: {{ $selectedAccount->account_code }} - {{ $selectedAccount->account_name }}</span>
                    </h2>
                    <span class="text-[11px] text-slate-500">العام المالي {{ $year }} - موزع على 12 شهراً</span>
                </div>

                <!-- زر يمن سوفت الشهير: التوزيع السريع للموازنة السنوية -->
                <div class="flex items-center gap-2">
                    <input type="number" id="annual_total_input" placeholder="إجمالي الموازنة السنوية" class="w-36 text-xs rounded-lg border-slate-300 py-1 px-2 font-mono text-left">
                    <button type="button" onclick="distributeEqually()" class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white font-black text-xs rounded-lg transition shadow-2xs flex items-center gap-1">
                        <span>⚡</span>
                        <span>توزيع سنوي بالتساوي (F8)</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="bg-slate-100/80 text-slate-700 font-black border-b border-slate-200">
                            <th class="p-3">الشهر #</th>
                            <th class="p-3">اسم الشهر</th>
                            <th class="p-3">الموازنة التقديرية (المخطط)</th>
                            <th class="p-3">الفعلي المنفذ (الدفاتري)</th>
                            <th class="p-3">الانحراف (الفارق)</th>
                            <th class="p-3">حالة التنفيذ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @php
                            $monthsNames = [
                                1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                                5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                                9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                            ];
                            $sumBudget = 0;
                            $sumActual = 0;
                        @endphp

                        @for($m = 1; $m <= 12; $m++)
                        @php
                            $bRow = $monthlyBudgets[$m] ?? null;
                            $bAmount = (float)($bRow?->budget_amount ?? 0);
                            $aAmount = (float)($bRow?->actual_amount ?? 0);
                            $vAmount = $bAmount - $aAmount;
                            $sumBudget += $bAmount;
                            $sumActual += $aAmount;
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 font-bold text-slate-500">{{ $m }}</td>
                            <td class="p-3 font-bold text-indigo-900">{{ $monthsNames[$m] }} {{ $year }}</td>
                            <td class="p-2">
                                <input type="number" step="0.01" name="months[{{ $m }}]" id="month_input_{{ $m }}" value="{{ $bAmount }}" class="month-budget-field w-36 rounded-lg border-slate-300 py-1 text-xs font-mono font-bold focus:ring-amber-500 focus:border-amber-500 text-left">
                            </td>
                            <td class="p-3 font-mono text-slate-700 font-bold">
                                {{ number_format($aAmount, 2) }}
                            </td>
                            <td class="p-3 font-mono font-black {{ $vAmount >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ number_format($vAmount, 2) }}
                            </td>
                            <td class="p-3">
                                @if($bAmount > 0)
                                    @if($vAmount >= 0)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800">ضمن الموازنة (وفر)</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800">تجاوز الموازنة</span>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-[10px]">غير محدد</span>
                                @endif
                            </td>
                        </tr>
                        @endfor

                        <!-- سطر الإجمالي -->
                        <tr class="bg-amber-50/70 font-black text-slate-900 border-t-2 border-amber-300">
                            <td colspan="2" class="p-3">الإجمالي السنوي للموازنة:</td>
                            <td class="p-3 font-mono text-amber-900 text-sm" id="annual_sum_display">{{ number_format($sumBudget, 2) }}</td>
                            <td class="p-3 font-mono text-slate-900 text-sm">{{ number_format($sumActual, 2) }}</td>
                            <td class="p-3 font-mono text-sm {{ ($sumBudget - $sumActual) >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ number_format($sumBudget - $sumActual, 2) }}
                            </td>
                            <td class="p-3"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-black text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                    <span>💾</span>
                    <span>حفظ واعتماد الموازنة السنوية</span>
                </button>
            </div>
        </div>
    </form>
    @endif

</div>

<script>
function distributeEqually() {
    const total = parseFloat(document.getElementById('annual_total_input').value);
    if (!total || total <= 0) {
        alert('يرجى كتابة مبلغ سنوي صحيح في خانة التوزيع أولاً');
        return;
    }
    const perMonth = (total / 12).toFixed(2);
    for (let i = 1; i <= 12; i++) {
        const input = document.getElementById('month_input_' + i);
        if (input) input.value = perMonth;
    }
}
</script>
@endsection
