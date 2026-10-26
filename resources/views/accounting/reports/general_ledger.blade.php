@extends('layouts.app', ['title' => 'دفتر الأستاذ العام'])

@section('content')
<div class="space-y-6">

    <!-- شريط التنقل المحاسبي الموحد -->
    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>📖</span>
                <span>دفتر الأستاذ العام (General Ledger Statement)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                كشف حركة تفصيلي لأي حساب مالي في الدليل المحاسبي يعرض أثر القيود اليومية والرصيد التراكمي
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('accounting.reports.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                ← العودة لمركز التقارير
            </a>
        </div>
    </div>

    <!-- شريط اختيار الحساب والتواريخ -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <form action="{{ route('accounting.reports.general_ledger') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-700 mb-1">اختر الحساب المحاسبي المراد كشف حركته *</label>
                <select name="account_id" required class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ $selectedAccount?->id == $acc->id ? 'selected' : '' }}>
                            [{{ $acc->account_code }}] {{ $acc->account_name }} ({{ $acc->account_category_label }} - {{ $acc->nature_label }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">من تاريخ:</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="w-full text-xs rounded-xl border-slate-300 p-2.5">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">إلى تاريخ:</label>
                <div class="flex items-center gap-2">
                    <input type="date" name="to_date" value="{{ $toDate }}" class="w-full text-xs rounded-xl border-slate-300 p-2.5">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition">
                        عرض
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($selectedAccount)
        <!-- بطاقة الحساب المختار والرصيد -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-base font-black px-2.5 py-1 rounded-xl bg-slate-900 text-white">
                        {{ $selectedAccount->account_code }}
                    </span>
                    <h2 class="text-lg font-black text-slate-900">{{ $selectedAccount->account_name }}</h2>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">
                        {{ $selectedAccount->account_category_label }}
                    </span>
                </div>
                <span class="text-xs text-slate-400 mt-1 block">
                    طبيعة الحساب: {{ $selectedAccount->nature_label }} | العملة: {{ $selectedAccount->currency_name }}
                </span>
            </div>

            <div class="flex items-center gap-6">
                <div class="text-left">
                    <span class="text-[11px] text-slate-400 block font-bold">إجمالي المدين:</span>
                    <span class="font-mono text-base font-black text-blue-600">{{ number_format($totalDebit, 2) }}</span>
                </div>
                <div class="text-left">
                    <span class="text-[11px] text-slate-400 block font-bold">إجمالي الدائن:</span>
                    <span class="font-mono text-base font-black text-emerald-600">{{ number_format($totalCredit, 2) }}</span>
                </div>
                <div class="text-left p-3 rounded-2xl bg-slate-50 border border-slate-200">
                    <span class="text-[11px] text-slate-400 block font-bold">الرصيد الختامي:</span>
                    <span class="font-mono text-lg font-black {{ $runningBalance < 0 ? 'text-rose-600' : 'text-slate-900' }}">
                        {{ number_format($runningBalance, 2) }} {{ $selectedAccount->currency }}
                    </span>
                </div>
            </div>
        </div>

        <!-- جدول كشف الحركات اليومية -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50 text-slate-700 font-black border-b border-slate-200">
                        <tr>
                            <th class="p-4 w-12 text-center">#</th>
                            <th class="p-4 w-28">التاريخ</th>
                            <th class="p-4 w-32">رقم القيد</th>
                            <th class="p-4">البيان والشرح</th>
                            <th class="p-4 w-36">مركز التكلفة</th>
                            <th class="p-4 w-32 text-left">مدين (Debit)</th>
                            <th class="p-4 w-32 text-left">دائن (Credit)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($lines as $idx => $line)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-4 text-center text-slate-400">{{ $idx + 1 }}</td>
                                <td class="p-4 font-mono text-slate-700">{{ $line->entry?->entry_date?->format('Y/m/d') }}</td>
                                <td class="p-4 font-mono font-black text-indigo-700">
                                    <a href="{{ route('accounting.journal_entries.show', $line->entry_id) }}" class="hover:underline">
                                        {{ $line->entry?->entry_no }}
                                    </a>
                                </td>
                                <td class="p-4 text-slate-800 font-medium">
                                    {{ $line->line_description ?: $line->entry?->description }}
                                </td>
                                <td class="p-4 text-slate-600">
                                    {{ $line->costCenter?->name ?? '—' }}
                                </td>
                                <td class="p-4 font-mono font-bold text-left text-blue-700">
                                    {{ $line->debit > 0 ? number_format($line->debit, 2) : '—' }}
                                </td>
                                <td class="p-4 font-mono font-bold text-left text-emerald-700">
                                    {{ $line->credit > 0 ? number_format($line->credit, 2) : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400 font-bold">لا توجد حركات أو قيود مسجلة على هذا الحساب في الفترة المحددة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-900 text-white font-black text-xs">
                        <tr>
                            <td colspan="5" class="p-4 text-right">إجمالي حركات الفترة:</td>
                            <td class="p-4 text-left font-mono text-blue-300">{{ number_format($totalDebit, 2) }}</td>
                            <td class="p-4 text-left font-mono text-emerald-300">{{ number_format($totalCredit, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
