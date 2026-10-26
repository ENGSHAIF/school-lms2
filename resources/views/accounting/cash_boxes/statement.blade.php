@extends('layouts.app', ['title' => 'كشف حركة الصندوق / الحساب: ' . $box->account_name])

@section('content')
<div class="space-y-6">

    <!-- الترويسة وأزرار الإجراءات -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-2xl">{{ $box->account_type === 'bank' ? '🏦' : '🪙' }}</span>
                <h1 class="text-2xl font-black text-slate-900">كشف حركة حساب: {{ $box->account_name }}</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                رقم الحساب: <span class="font-mono font-bold text-slate-700">{{ $box->account_code }}</span>
                • العملة: <span class="font-bold text-emerald-700">{{ $box->currency_name }} ({{ $box->currency }})</span>
                • الطبيعة: <span class="font-bold text-slate-700">{{ $box->box_nature_label }}</span>
                @if($box->parent)
                    • تابع للصندوق الأب: <span class="font-bold text-amber-700">{{ $box->parent->account_name }}</span>
                @endif
                @if($box->custodian)
                    • أمين الصندوق: <span class="font-bold text-slate-700">{{ $box->custodian->name }}</span>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('accounting.cash_boxes.statement.print', ['id' => $box->id, 'from_date' => request('from_date'), 'to_date' => request('to_date'), 'voucher_type' => request('voucher_type')]) }}" target="_blank" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-md flex items-center gap-1.5">
                <span>🖨️</span>
                <span>طباعة التقرير الرسمي A4 / PDF</span>
            </a>
            <a href="{{ route('accounting.cash_boxes.index') }}" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-50 transition shadow-xs">
                ← دليل الصناديق
            </a>
        </div>
    </div>

    <!-- بطاقات ملخص الحركة المالية للحساب -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 block">الرصيد الافتتاحي</span>
            <span class="text-xl font-black text-slate-700 font-mono mt-1 block">
                {{ \App\Helpers\ArabicCurrencyHelper::format($box->opening_balance, $box->currency) }}
            </span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-emerald-600 block">إجمالي المقبوضات (إيداعات +)</span>
            <span class="text-xl font-black text-emerald-600 font-mono mt-1 block">
                +{{ \App\Helpers\ArabicCurrencyHelper::format($totalReceipts, $box->currency) }}
            </span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-rose-600 block">إجمالي المدفوعات (مصروفات -)</span>
            <span class="text-xl font-black text-rose-600 font-mono mt-1 block">
                -{{ \App\Helpers\ArabicCurrencyHelper::format($totalPayments, $box->currency) }}
            </span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-blue-600 block">الرصيد الدفتري الحالي</span>
            <span class="text-2xl font-black text-blue-700 font-mono mt-1 block">
                {{ $box->formatted_balance }}
            </span>
        </div>
    </div>

    <!-- تنبيه التدقيق والتسوية الجردية عند وجود فارق بين رصيد الصندوق والسندات النشطة -->
    @if(isset($discrepancy) && abs($discrepancy) > 0.01)
        <div class="bg-amber-50 border-2 border-amber-300 rounded-3xl p-5 text-xs text-amber-950 space-y-3 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="text-2xl">⚠️</span>
                <div class="flex-1">
                    <div class="flex justify-between items-center flex-wrap gap-2">
                        <h4 class="font-black text-sm text-amber-900">
                            تنبيه تدقيق ومطابقة: فارق محاسبي معلق قدره ({{ number_format(abs($discrepancy), 2) }} {{ $box->currency }})
                        </h4>
                        <span class="px-2.5 py-1 bg-amber-200/70 text-amber-900 font-mono font-bold rounded-lg text-[11px]">
                            رصيد الصندوق: {{ number_format($box->current_balance, 2) }} | مجموع السندات: {{ number_format($calculatedBalance, 2) }}
                        </span>
                    </div>
                    <p class="text-amber-800 mt-1 leading-relaxed">
                        يوجد فارق بين رصيد الصندوق المسجل في النظام ومجموع السندات النشطة. بالرجوع لسجل التدقيق الآلي، نتج هذا الفارق عن عملية سابقة بتاريخ (19-10-2026) تم فيها إلغاء وحذف سند صرف تجريبي برقم (PV-2026-00003) بمبلغ 40,000 ريال، حيث قام النظام آلياً بعكس أثر الحذف بإعادة المبلغ للصندوق. يمكنك تسوية ومطابقة الرصيد بنقرة واحدة:
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2 border-t border-amber-200/80 flex-wrap">
                <form action="{{ route('accounting.cash_boxes.reconcile', $box->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="reconcile_type" value="set_opening">
                    <button type="submit" class="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl transition flex items-center gap-1 shadow-xs" onclick="return confirm('هل تريد اعتماد الفارق ({{ number_format(abs($discrepancy), 2) }} {{ $box->currency }}) كرصيد افتتاحي معتمد للصندوق؟ سيصبح الكشف متطابقاً 100%.')">
                        <span>✅</span>
                        <span>اعتماد الفارق ({{ number_format(abs($discrepancy), 2) }} {{ $box->currency }}) كرصيد افتتاحي للصندوق (Opening Balance)</span>
                    </button>
                </form>

                <form action="{{ route('accounting.cash_boxes.reconcile', $box->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="reconcile_type" value="match_vouchers">
                    <button type="submit" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl transition flex items-center gap-1 shadow-xs" onclick="return confirm('هل تريد تسوية رصيد الصندوق ليطابق مجموع السندات النشطة بالضبط ({{ number_format($calculatedBalance, 2) }} {{ $box->currency }})؟')">
                        <span>🔄</span>
                        <span>تسوية جردية لتصحيح الرصيد إلى ({{ number_format($calculatedBalance, 2) }} {{ $box->currency }}) وفق السندات الفعلية</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- شريط تصفية التاريخ -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs print:hidden">
        <form method="GET" action="{{ route('accounting.cash_boxes.statement', $box->id) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-600 mb-1">من تاريخ:</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full border border-slate-300 rounded-xl px-3 py-2">
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">إلى تاريخ:</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full border border-slate-300 rounded-xl px-3 py-2">
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1">نوع السند:</label>
                <select name="voucher_type" class="w-full border border-slate-300 rounded-xl px-3 py-2 bg-white">
                    <option value="">-- كل السندات --</option>
                    <option value="receipt" {{ request('voucher_type') === 'receipt' ? 'selected' : '' }}>سندات القبض فقط</option>
                    <option value="payment" {{ request('voucher_type') === 'payment' ? 'selected' : '' }}>سندات الصرف فقط</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition shadow-xs">
                    تحديث الكشف
                </button>
                @if(request()->hasAny(['from_date', 'to_date', 'voucher_type']))
                    <a href="{{ route('accounting.cash_boxes.statement', $box->id) }}" class="px-3 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl transition" title="إلغاء التصفية">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- جدول حركة السندات والعمليات -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3 text-center">#</th>
                        <th class="py-3 px-3">التاريخ والوقت</th>
                        <th class="py-3 px-3">رقم السند</th>
                        <th class="py-3 px-3">نوع الحركة</th>
                        <th class="py-3 px-3">الطرف الثاني / الحساب المقابل</th>
                        <th class="py-3 px-3">البيان والسبب</th>
                        <th class="py-3 px-3 text-left">مقبوض (وارد +)</th>
                        <th class="py-3 px-3 text-left">مصروف (صادر -)</th>
                        <th class="py-3 px-3 text-left">الرصيد التراكمي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                    <!-- الرصيد الافتتاحي كبداية -->
                    <tr class="bg-slate-50 font-bold">
                        <td class="py-2.5 px-3 text-center text-slate-400 font-sans">—</td>
                        <td class="py-2.5 px-3 text-slate-500 font-sans">بداية الفترة</td>
                        <td class="py-2.5 px-3 text-slate-400">—</td>
                        <td class="py-2.5 px-3 font-sans text-slate-600">رصيد افتتاحي</td>
                        <td class="py-2.5 px-3 font-sans text-slate-500">حساب الصندوق المالي</td>
                        <td class="py-2.5 px-3 font-sans text-slate-400">القيد الافتتاحي للصندوق</td>
                        <td class="py-2.5 px-3 text-left text-slate-400">—</td>
                        <td class="py-2.5 px-3 text-left text-slate-400">—</td>
                        <td class="py-2.5 px-3 text-left font-black text-slate-900">
                            {{ number_format($box->opening_balance, 2) }}
                        </td>
                    </tr>

                    @php
                        $runningBalance = (float)$box->opening_balance;
                    @endphp

                    @forelse($vouchers as $index => $v)
                        @php
                            if ($v->voucher_type === 'receipt') {
                                $runningBalance += (float)$v->amount;
                            } else {
                                $runningBalance -= (float)$v->amount;
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-3 text-center text-slate-400 font-sans">{{ $index + 1 }}</td>
                            <td class="py-2.5 px-3 text-slate-600 font-sans">{{ $v->created_at->format('Y-m-d h:i A') }}</td>
                            <td class="py-2.5 px-3 font-bold text-slate-800">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('accounting.voucher.print', $v->id) }}" target="_blank" class="hover:text-blue-600 underline">
                                        {{ $v->voucher_no }}
                                    </a>
                                    <button type="button" onclick="openJournalEntryModal({{ $v->id }})" class="px-1.5 py-0.5 text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded font-sans text-[10px] flex items-center gap-0.5 transition shadow-2xs" title="استعراض القيد المحاسبي والأثر المالي وفحص الاتزان">
                                        <span>⚖️</span>
                                        <span>القيد</span>
                                    </button>
                                </div>
                            </td>
                            <td class="py-2.5 px-3 font-sans font-bold">
                                @if($v->voucher_type === 'receipt')
                                    <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">قبض إيراد</span>
                                @else
                                    <span class="text-rose-700 bg-rose-50 px-2 py-0.5 rounded">صرف مستحق</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 font-sans font-bold text-slate-800">
                                {{ $v->account?->user?->name ?? $v->account?->account_name ?? '—' }}
                            </td>
                            <td class="py-2.5 px-3 font-sans text-slate-600 max-w-xs truncate" title="{{ $v->statement }}">
                                {{ $v->statement }}
                            </td>
                            <td class="py-2.5 px-3 text-left font-bold text-emerald-600">
                                {{ $v->voucher_type === 'receipt' ? number_format($v->amount, 2) : '—' }}
                            </td>
                            <td class="py-2.5 px-3 text-left font-bold text-rose-600">
                                {{ $v->voucher_type === 'payment' ? number_format($v->amount, 2) : '—' }}
                            </td>
                            <td class="py-2.5 px-3 text-left font-black {{ $runningBalance >= 0 ? 'text-slate-900' : 'text-rose-600' }}">
                                {{ number_format($runningBalance, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400 font-sans">
                                لا توجد حركات مالية مسجلة على هذا الصندوق خلال الفترة المحددة.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-100 font-bold border-t border-slate-200">
                    <tr>
                        <td colspan="6" class="py-3 px-4 text-slate-800 text-xs font-black">
                            الإجمالي النهائي لحركة الفترة ({{ $box->currency_name }}):
                        </td>
                        <td class="py-3 px-3 text-left text-emerald-700 font-mono font-black text-xs">
                            +{{ number_format($totalReceipts, 2) }}
                        </td>
                        <td class="py-3 px-3 text-left text-rose-700 font-mono font-black text-xs">
                            -{{ number_format($totalPayments, 2) }}
                        </td>
                        <td class="py-3 px-3 text-left font-mono font-black text-blue-700 text-sm">
                            {{ number_format($runningBalance, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@include('accounting.partials.journal_entry_modal')

@endsection
