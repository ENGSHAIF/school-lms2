@extends('layouts.app', ['title' => 'حوافظ الصرف والقبض اليومية - يمن سوفت ERP'])

@section('content')
<div class="space-y-6">

    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 mb-1 flex items-center gap-2">
                <span>📋</span>
                <span>حوافظ الصرف والقبض اليومية (Daily Portfolios)</span>
            </h1>
            <p class="text-xs text-slate-500">وفق أسلوب الصناديق في نظام المتكامل بلاس ERP: تسجيل الحركات النقدية اليومية المتفرقة لأمين الصندوق واعتمادها وترحيلها دفعة واحدة بسند رسمي مع قيد اليومية.</p>
        </div>
        <button onclick="document.getElementById('newPortfolioModal').classList.remove('hidden')" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-black text-xs rounded-xl shadow-xs transition flex items-center gap-2">
            <span>➕</span>
            <span>فتح حافظة يومية جديدة</span>
        </button>
    </div>

    <!-- جدول الحوافظ -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200">
            <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <span>📑</span>
                <span>سجل حوافظ الصناديق اليومية</span>
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-100/80 text-slate-700 font-black border-b border-slate-200">
                        <th class="p-3">رقم الحافظة</th>
                        <th class="p-3">النوع</th>
                        <th class="p-3">التاريخ</th>
                        <th class="p-3">الصندوق المرتبط</th>
                        <th class="p-3">عدد الحركات</th>
                        <th class="p-3">إجمالي المبلغ</th>
                        <th class="p-3">الحالة</th>
                        <th class="p-3 text-center">إجراءات يمن سوفت</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($portfolios as $p)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-3 font-mono font-black text-indigo-950">#{{ $p->portfolio_no }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $p->portfolio_type === 'receipt' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $p->portfolio_type_label }}
                            </span>
                        </td>
                        <td class="p-3 font-mono text-slate-600">{{ $p->portfolio_date->format('Y-m-d') }}</td>
                        <td class="p-3 font-bold text-slate-800">{{ $p->cashBox?->account_name ?? 'صندوق رئيسي' }}</td>
                        <td class="p-3 font-mono text-slate-700 font-bold">{{ $p->items_count }} حركات</td>
                        <td class="p-3 font-mono font-black text-slate-900">{{ number_format($p->total_amount, 2) }} {{ $p->currency }}</td>
                        <td class="p-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black border {{ $p->status_badge_class }}">
                                {{ $p->status_label }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <div class="inline-flex items-center gap-1">
                                @if($p->status === 'draft')
                                    <form action="{{ route('accounting.daily_portfolios.approve', $p->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-[11px] font-black transition">
                                            اعتماد
                                        </button>
                                    </form>
                                @elseif($p->status === 'approved')
                                    <form action="{{ route('accounting.daily_portfolios.post_voucher', $p->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-black transition" onclick="return confirm('تأكيد ترحيل الحافظة إلى سند رسمي مرقم وقيد يومية؟')">
                                            ترحيل لسند رسمي
                                        </button>
                                    </form>
                                @else
                                    <span class="text-emerald-700 font-bold text-[11px]">مرحل (#{{ $p->voucher?->voucher_no ?? 'سند' }})</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-400">لا توجد حوافظ يومية مسجلة حالياً.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($portfolios->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $portfolios->links() }}
        </div>
        @endif
    </div>

</div>

<!-- مودال فتح حافظة يومية جديدة -->
<div id="newPortfolioModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <span>📋</span>
                <span>فتح حافظة نقدية يومية جديدة (صرف / قبض)</span>
            </h3>
            <button onclick="document.getElementById('newPortfolioModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form action="{{ route('accounting.daily_portfolios.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">نوع الحافظة:</label>
                    <select name="portfolio_type" class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-bold">
                        <option value="payment">حافظة صرف يومية (مدفوعات)</option>
                        <option value="receipt">حافظة قبض يومية (مقبوضات)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ الحافظة:</label>
                    <input type="date" name="portfolio_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-mono font-bold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الصندوق المسؤول:</label>
                    <select name="cash_box_id" required class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-bold">
                        @foreach($cashBoxes as $cb)
                            <option value="{{ $cb->id }}">{{ $cb->account_name }} ({{ $cb->currency }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- أسطر الحافظة -->
            <div class="border border-slate-200 rounded-xl p-3 bg-slate-50/50 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="font-black text-slate-700 text-xs">حركات الحافظة التفصيلية:</span>
                    <button type="button" onclick="addPortfolioRow()" class="text-xs font-bold text-indigo-600 hover:underline">+ سطر إضافي</button>
                </div>

                <div id="portfolioRows" class="space-y-2">
                    <div class="grid grid-cols-12 gap-2 items-center bg-white p-2 rounded-lg border border-slate-200">
                        <div class="col-span-4">
                            <select name="items[0][account_id]" required class="w-full rounded border-slate-300 py-1 text-[11px]">
                                <option value="">-- اختر الحساب الفرعي --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <input type="number" step="0.01" name="items[0][amount]" required placeholder="المبلغ" class="w-full rounded border-slate-300 py-1 text-[11px] font-mono text-left">
                        </div>
                        <div class="col-span-4">
                            <input type="text" name="items[0][statement]" required placeholder="البيان" class="w-full rounded border-slate-300 py-1 text-[11px]">
                        </div>
                        <div class="col-span-2">
                            <input type="text" name="items[0][payee_or_payer]" placeholder="المستلم" class="w-full rounded border-slate-300 py-1 text-[11px]">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">ملاحظات عامة للحافظة:</label>
                <textarea name="notes" rows="2" class="w-full rounded-xl border-slate-300 py-1.5 text-xs" placeholder="أي ملاحظات تخص وردية أمين الصندوق..."></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('newPortfolioModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 text-white rounded-xl font-black">حفظ الحافظة كمسودة</button>
            </div>
        </form>
    </div>
</div>

<script>
let pRowIndex = 1;
function addPortfolioRow() {
    const container = document.getElementById('portfolioRows');
    const firstRow = container.firstElementChild;
    const newRow = firstRow.cloneNode(true);
    
    newRow.querySelectorAll('input, select').forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            input.setAttribute('name', name.replace(/\[\d+\]/, '[' + pRowIndex + ']'));
        }
        if (input.tagName === 'INPUT') input.value = '';
    });
    
    container.appendChild(newRow);
    pRowIndex++;
}
</script>
@endsection
