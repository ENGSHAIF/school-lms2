@extends('layouts.app', ['title' => 'ميزان المراجعة بالمجاميع والأرصدة'])

@section('content')
<div class="space-y-6">

    <!-- شريط التنقل المحاسبي الموحد -->
    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>⚖️</span>
                <span>ميزان المراجعة بالمجاميع والأرصدة (Trial Balance)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                التقرير الختامي لمطابقة توازن دفتر الأستاذ العام لكافة حسابات الدليل المحاسبي
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('accounting.reports.trial_balance', ['from_date' => $fromDate, 'to_date' => $toDate, 'print' => 1]) }}" target="_blank" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5">
                <span>🖨️</span>
                <span>طباعة ميزان المراجعة (A4)</span>
            </a>
            <a href="{{ route('accounting.reports.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                ← العودة لمركز التقارير
            </a>
        </div>
    </div>

    <!-- شريط تصفية التواريخ وفحص التوازن -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <form action="{{ route('accounting.reports.trial_balance') }}" method="GET" class="flex items-center gap-3 flex-wrap w-full md:w-auto">
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-600">من تاريخ:</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="text-xs rounded-xl border-slate-300 p-2">
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-600">إلى تاريخ:</label>
                <input type="date" name="to_date" value="{{ $toDate }}" class="text-xs rounded-xl border-slate-300 p-2">
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition">
                تحديث التقرير
            </button>
        </form>

        <!-- حالة التوازن -->
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 rounded-2xl flex items-center gap-2 {{ $data['is_balanced'] ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                <span class="text-base">{{ $data['is_balanced'] ? '✓' : '✕' }}</span>
                <span class="text-xs font-black">
                    {{ $data['is_balanced'] ? 'ميزان المراجعة متزن ومطابق 100%' : 'تنبيه: ميزان المراجعة غير متزن' }}
                </span>
            </div>
        </div>
    </div>

    <!-- جدول ميزان المراجعة القياسي -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-900 text-white font-black text-center">
                    <tr>
                        <th rowspan="2" class="p-3 border-l border-slate-800 w-24">رقم الحساب</th>
                        <th rowspan="2" class="p-3 border-l border-slate-800 text-right">اسم الحساب المحاسبي</th>
                        <th rowspan="2" class="p-3 border-l border-slate-800 w-32">التصنيف</th>
                        <th colspan="2" class="p-3 border-l border-slate-800 bg-slate-800">مجاميع الحركات (Movements)</th>
                        <th colspan="2" class="p-3 bg-slate-800">الأرصدة الختامية (Balances)</th>
                    </tr>
                    <tr class="bg-slate-800/80 border-t border-slate-700 text-[11px]">
                        <th class="p-2 border-l border-slate-700 text-blue-300 w-28 text-left">مدين</th>
                        <th class="p-2 border-l border-slate-700 text-emerald-300 w-28 text-left">دائن</th>
                        <th class="p-2 border-l border-slate-700 text-blue-300 w-28 text-left">رصيد مدين</th>
                        <th class="p-2 text-emerald-300 w-28 text-left">رصيد دائن</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($data['rows'] as $r)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 font-mono font-bold text-indigo-700 text-center border-l border-slate-100">{{ $r['account_code'] }}</td>
                            <td class="p-3 font-bold text-slate-900 border-l border-slate-100">{{ $r['account_name'] }}</td>
                            <td class="p-3 text-slate-500 border-l border-slate-100 text-[11px]">{{ $r['account_category'] }}</td>
                            <td class="p-3 font-mono text-left text-blue-700 border-l border-slate-100">
                                {{ $r['total_debit'] > 0 ? number_format($r['total_debit'], 2) : '—' }}
                            </td>
                            <td class="p-3 font-mono text-left text-emerald-700 border-l border-slate-100">
                                {{ $r['total_credit'] > 0 ? number_format($r['total_credit'], 2) : '—' }}
                            </td>
                            <td class="p-3 font-mono font-bold text-left text-blue-700 border-l border-slate-100">
                                {{ $r['balance_debit'] > 0 ? number_format($r['balance_debit'], 2) : '—' }}
                            </td>
                            <td class="p-3 font-mono font-bold text-left text-emerald-700">
                                {{ $r['balance_credit'] > 0 ? number_format($r['balance_credit'], 2) : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 font-bold">لا توجد حركات مالية خلال هذه الفترة.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-900 text-white font-black text-xs">
                    <tr>
                        <td colspan="3" class="p-4 text-right">الإجمالي العام لميزان المراجعة:</td>
                        <td class="p-4 text-left font-mono text-blue-300 border-l border-slate-800">{{ number_format($data['total_debit_sum'], 2) }}</td>
                        <td class="p-4 text-left font-mono text-emerald-300 border-l border-slate-800">{{ number_format($data['total_credit_sum'], 2) }}</td>
                        <td class="p-4 text-left font-mono text-blue-400 border-l border-slate-800">{{ number_format($data['total_debit_balance'], 2) }}</td>
                        <td class="p-4 text-left font-mono text-emerald-400">{{ number_format($data['total_credit_balance'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
