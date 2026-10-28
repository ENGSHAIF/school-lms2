@extends('layouts.app', ['title' => 'الأرصدة الافتتاحية وميزان البداية - يمن سوفت ERP'])

@section('content')
<div class="space-y-6">

    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 mb-1 flex items-center gap-2">
                <span>⚖️</span>
                <span>مدخلات الأرصدة الافتتاحية والميزان الافتتاحي (Opening Balances)</span>
            </h1>
            <p class="text-xs text-slate-500">وفق الفصل الثاني من دليل المتكامل بلاس ERP: إدخال أرصدة بداية المدة للحسابات الفرعية وضبط توازن القيد الافتتاحي (المدين = الدائن) مع تحديد سقوف الائتمان والسيولة.</p>
        </div>
    </div>

    <!-- شريط فحص التوازن المالي -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-500">إجمالي الأرصدة المدينة الافتتاحية</span>
            <div class="text-xl font-black text-emerald-600 mt-1">{{ number_format($totalDebit, 2) }} <span class="text-xs font-normal">YER</span></div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[11px] font-bold text-slate-500">إجمالي الأرصدة الدائنة الافتتاحية</span>
            <div class="text-xl font-black text-blue-600 mt-1">{{ number_format($totalCredit, 2) }} <span class="text-xs font-normal">YER</span></div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs {{ $isBalanced ? 'bg-emerald-50/50 border-emerald-200' : 'bg-rose-50/50 border-rose-200' }}">
            <span class="text-[11px] font-bold {{ $isBalanced ? 'text-emerald-700' : 'text-rose-700' }}">حالة اتزان الميزان الافتتاحي</span>
            <div class="text-xl font-black mt-1 {{ $isBalanced ? 'text-emerald-700' : 'text-rose-700' }}">
                @if($isBalanced)
                    <span>✅ متزن تماماً (الفارق: 0.00)</span>
                @else
                    <span>⚠️ غير متزن (الفارق: {{ number_format($difference, 2) }})</span>
                @endif
            </div>
        </div>
    </div>

    <!-- جدول إدخال الأرصدة -->
    <form action="{{ route('accounting.opening_balances.save') }}" method="POST">
        @csrf
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                    <span>📑</span>
                    <span>كافة الحسابات الفرعية (الرتبة الخامسة - تقبل القيود)</span>
                </h2>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-black text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                    <span>💾</span>
                    <span>حفظ كافة الأرصدة الافتتاحية</span>
                </button>
            </div>

            <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                <table class="w-full text-right text-xs">
                    <thead class="sticky top-0 bg-slate-100 text-slate-700 font-black z-10 border-b border-slate-200">
                        <tr>
                            <th class="p-3">رقم الحساب</th>
                            <th class="p-3">اسم الحساب في الدليل</th>
                            <th class="p-3">النوع والطبيعة</th>
                            <th class="p-3">الرصيد الافتتاحي</th>
                            <th class="p-3">الحد الأدنى للسيولة (Min)</th>
                            <th class="p-3">سقف الائتمان الأقصى (Max)</th>
                            <th class="p-3">الرصيد الحالي الفعلي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($accounts as $acc)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 font-mono font-bold text-slate-800">{{ $acc->account_code }}</td>
                            <td class="p-3 font-bold text-indigo-950">{{ $acc->account_name }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $acc->nature === 'debit' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $acc->nature === 'debit' ? 'طبيعة مدينة' : 'طبيعة دائنة' }}
                                </span>
                            </td>
                            <td class="p-2">
                                <input type="number" step="0.01" name="balances[{{ $acc->id }}][amount]" value="{{ $acc->opening_balance }}" class="w-32 rounded-lg border-slate-300 py-1 text-xs font-mono font-bold focus:ring-amber-500 focus:border-amber-500 text-left">
                            </td>
                            <td class="p-2">
                                <input type="number" step="0.01" name="balances[{{ $acc->id }}][min_balance]" value="{{ $acc->min_balance }}" class="w-28 rounded-lg border-slate-300 py-1 text-xs font-mono focus:ring-amber-500 focus:border-amber-500 text-left" placeholder="0.00">
                            </td>
                            <td class="p-2">
                                <input type="number" step="0.01" name="balances[{{ $acc->id }}][max_balance]" value="{{ $acc->max_balance }}" class="w-28 rounded-lg border-slate-300 py-1 text-xs font-mono focus:ring-amber-500 focus:border-amber-500 text-left" placeholder="اختياري">
                            </td>
                            <td class="p-3 font-mono font-black {{ $acc->current_balance >= 0 ? 'text-slate-900' : 'text-rose-600' }}">
                                {{ number_format($acc->current_balance, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-black text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                    <span>💾</span>
                    <span>حفظ الأرصدة الافتتاحية والحدود الرقابية</span>
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
