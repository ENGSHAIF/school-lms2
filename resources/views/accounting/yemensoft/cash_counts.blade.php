@extends('layouts.app', ['title' => 'جرد الخزينة بفئات العملة - يمن سوفت ERP'])

@section('content')
<div class="space-y-6">

    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 mb-1 flex items-center gap-2">
                <span>💵</span>
                <span>جرد النقدية ومطابقة فئات العملة (Cash Inventory Count)</span>
            </h1>
            <p class="text-xs text-slate-500">وفق إجراءات الرقابة على الصناديق في نظام المتكامل بلاس ERP: العد الفعلي للنقود بالورقة والعملة، مقارنة الفعلي بالرصيد الدفتري، وإثبات محاضر العجز والفائض.</p>
        </div>
        <button onclick="document.getElementById('newCountModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-xs transition flex items-center gap-2">
            <span>➕</span>
            <span>إجراء محضر جرد صندوق جديد</span>
        </button>
    </div>

    <!-- جدول محاضر الجرد السابقة -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200">
            <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <span>📑</span>
                <span>سجل محاضر جرد الصناديق والخزائن</span>
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-100/80 text-slate-700 font-black border-b border-slate-200">
                        <th class="p-3">رقم المحضر</th>
                        <th class="p-3">تاريخ الجرد</th>
                        <th class="p-3">الصندوق المفحوص</th>
                        <th class="p-3">الرصيد الدفتري</th>
                        <th class="p-3">الرصيد الفعلي (المعدود)</th>
                        <th class="p-3">الفارق (عجز / فائض)</th>
                        <th class="p-3">النتيجة الرقابية</th>
                        <th class="p-3">أمين الصندوق / الجارد</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($counts as $cnt)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-3 font-mono font-black text-indigo-950">#{{ $cnt->count_no }}</td>
                        <td class="p-3 font-mono text-slate-600">{{ $cnt->count_date->format('Y-m-d') }}</td>
                        <td class="p-3 font-bold text-slate-800">{{ $cnt->cashBox?->account_name }}</td>
                        <td class="p-3 font-mono text-slate-700 font-bold">{{ number_format($cnt->system_balance, 2) }} {{ $cnt->currency }}</td>
                        <td class="p-3 font-mono text-slate-900 font-black">{{ number_format($cnt->counted_balance, 2) }} {{ $cnt->currency }}</td>
                        <td class="p-3 font-mono font-black {{ $cnt->variance == 0 ? 'text-slate-700' : ($cnt->variance > 0 ? 'text-blue-600' : 'text-rose-600') }}">
                            {{ $cnt->variance > 0 ? '+' : '' }}{{ number_format($cnt->variance, 2) }} {{ $cnt->currency }}
                        </td>
                        <td class="p-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black border {{ $cnt->status_badge_class }}">
                                {{ $cnt->status_label }}
                            </span>
                        </td>
                        <td class="p-3 text-slate-600">{{ $cnt->creator?->name ?? 'المحاسب' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-400">لا توجد محاضر جرد نقدية مسجلة.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($counts->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $counts->links() }}
        </div>
        @endif
    </div>

</div>

<!-- مودال إجراء محضر جرد بفئات العملة -->
<div id="newCountModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <span>💵</span>
                <span>محضر جرد الصندوق وتفقيط فئات العملة الورقية</span>
            </h3>
            <button onclick="document.getElementById('newCountModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form action="{{ route('accounting.cash_counts.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الصندوق المراد جرده:</label>
                    <select name="cash_box_id" id="box_selector" required class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-bold" onchange="updateBoxBalance(this)">
                        <option value="">-- اختر الصندوق --</option>
                        @foreach($cashBoxes as $cb)
                            <option value="{{ $cb->id }}" data-balance="{{ $cb->current_balance }}">
                                {{ $cb->account_name }} (الرصيد الدفتري: {{ number_format($cb->current_balance, 2) }} {{ $cb->currency }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ الجرد:</label>
                    <input type="date" name="count_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-mono font-bold">
                </div>
            </div>

            <!-- جدول عد فئات النقود -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="p-2.5 bg-slate-50 font-black text-slate-700 text-xs border-b border-slate-200">
                    جدول عد الأوراق النقدية بالفئات (Banknotes Counting):
                </div>
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-100 text-slate-600 font-bold">
                        <tr>
                            <th class="p-2">فئة العملة</th>
                            <th class="p-2">العدد (الكمية)</th>
                            <th class="p-2 text-left">المبلغ الإجمالي للفئة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $denoms = [1000, 500, 250, 200, 100, 50];
                        @endphp
                        @foreach($denoms as $d)
                        <tr>
                            <td class="p-2 font-black text-slate-900 font-mono">فئة {{ $d }} ريال</td>
                            <td class="p-2">
                                <input type="number" min="0" name="denominations[{{ $d }}]" data-denom="{{ $d }}" value="0" class="denom-qty w-24 rounded border-slate-300 py-1 text-xs font-mono text-left" oninput="calculateCountTotal()">
                            </td>
                            <td class="p-2 font-mono font-bold text-slate-800 text-left" id="subtotal_{{ $d }}">0.00</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td class="p-2 font-bold text-slate-700">فئات وكسور أخرى / فكة:</td>
                            <td class="p-2">
                                <input type="number" min="0" name="denominations[1]" data-denom="1" value="0" class="denom-qty w-24 rounded border-slate-300 py-1 text-xs font-mono text-left" oninput="calculateCountTotal()">
                            </td>
                            <td class="p-2 font-mono font-bold text-slate-800 text-left" id="subtotal_1">0.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- بطاقة نتيجة المطابقة -->
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">إجمالي النقدية الفعلية (بعد العد):</span>
                    <span class="font-mono font-black text-indigo-900 text-sm" id="counted_display">0.00 YER</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">الرصيد الدفتري المسجل في النظام:</span>
                    <span class="font-mono font-black text-slate-800 text-sm" id="book_display">0.00 YER</span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-1.5">
                    <span class="font-black text-slate-800">الفارق (عجز أو فائض):</span>
                    <span class="font-mono font-black text-sm" id="diff_display">0.00 YER</span>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">ملاحظات لجنة الجرد:</label>
                <textarea name="notes" rows="2" class="w-full rounded-xl border-slate-300 py-1.5 text-xs" placeholder="أسباب الفارق إن وجد أو أية ملاحظات رقابية..."></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('newCountModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl font-black">حفظ واعتماد محضر الجرد</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentBookBalance = 0;

function updateBoxBalance(select) {
    const opt = select.options[select.selectedIndex];
    currentBookBalance = parseFloat(opt.getAttribute('data-balance') || 0);
    document.getElementById('book_display').innerText = currentBookBalance.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' YER';
    calculateCountTotal();
}

function calculateCountTotal() {
    let total = 0;
    document.querySelectorAll('.denom-qty').forEach(input => {
        const d = parseFloat(input.getAttribute('data-denom'));
        const q = parseInt(input.value) || 0;
        const sub = d * q;
        total += sub;
        const subEl = document.getElementById('subtotal_' + d);
        if (subEl) subEl.innerText = sub.toLocaleString('en-US', {minimumFractionDigits: 2});
    });

    document.getElementById('counted_display').innerText = total.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' YER';
    const diff = total - currentBookBalance;
    const diffEl = document.getElementById('diff_display');
    diffEl.innerText = (diff > 0 ? '+' : '') + diff.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' YER';
    if (Math.abs(diff) < 0.01) {
        diffEl.className = 'font-mono font-black text-sm text-emerald-600';
    } else if (diff > 0) {
        diffEl.className = 'font-mono font-black text-sm text-blue-600';
    } else {
        diffEl.className = 'font-mono font-black text-sm text-rose-600';
    }
}
</script>
@endsection
