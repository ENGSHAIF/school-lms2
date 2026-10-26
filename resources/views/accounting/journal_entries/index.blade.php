@extends('layouts.app', ['title' => 'دفتر قيود اليومية العامة (GL)'])

@section('content')
<div class="space-y-6">

    <!-- شريط التنقل المحاسبي الموحد -->
    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>⚖️</span>
                <span>دفتر قيود اليومية العامة (General Journal)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                سجل كافة القيود المحاسبية المزدوجة المتزنة الناتجة آلياً عن السندات أو القيود اليدوية مع تتبع الترحيل والقيود العكسية
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('accounting.journal_entries.create') }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                <span>➕</span>
                <span>تسجيل قيد يومية يدوي متزن</span>
            </a>
            <a href="{{ route('accounting.reports.trial_balance') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                <span>⚖️</span>
                <span>ميزان المراجعة</span>
            </a>
        </div>
    </div>

    <!-- بطاقات المؤشرات السريعة -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-400 block">إجمالي القيود بالدفتر</span>
            <span class="text-2xl font-black text-slate-800 font-mono mt-1 block">{{ $totalEntries }}</span>
            <span class="text-[11px] text-slate-500 mt-1 block">منها ({{ $manualEntriesCount }}) قيد تسوية يدوي</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-blue-200 bg-blue-50/20 shadow-xs">
            <span class="text-xs font-bold text-blue-800 block">إجمالي حركات المدين (Debit)</span>
            <span class="text-2xl font-black text-blue-600 font-mono mt-1 block">
                {{ \App\Helpers\ArabicCurrencyHelper::format($totalDebits, 'YER') }}
            </span>
            <span class="text-[11px] text-blue-600 mt-1 block">مرحلة ومثبتة في الأستاذ</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-emerald-200 bg-emerald-50/20 shadow-xs">
            <span class="text-xs font-bold text-emerald-800 block">إجمالي حركات الدائن (Credit)</span>
            <span class="text-2xl font-black text-emerald-600 font-mono mt-1 block">
                {{ \App\Helpers\ArabicCurrencyHelper::format($totalCredits, 'YER') }}
            </span>
            <span class="text-[11px] text-emerald-600 mt-1 block">مرحلة ومثبتة في الأستاذ</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-400 block">حالة توازن الدفتر العام</span>
            @php $isBalanced = abs($totalDebits - $totalCredits) < 0.1; @endphp
            <div class="mt-2 flex items-center gap-2">
                <span class="w-3 h-3 rounded-full {{ $isBalanced ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                <span class="text-sm font-black {{ $isBalanced ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $isBalanced ? 'متزن ومطابق 100%' : 'فارق غير متزن' }}
                </span>
            </div>
            <span class="text-[11px] text-slate-400 mt-1 block">المدين = الدائن</span>
        </div>
    </div>

    <!-- جدول القيود اليومية -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center flex-wrap gap-4">
            <form action="{{ route('accounting.journal_entries.index') }}" method="GET" class="flex items-center gap-2 flex-wrap">
                <input type="text" name="search" value="{{ $search }}" placeholder="رقم القيد أو البيان..." class="text-xs rounded-xl border-slate-300 p-2 focus:border-indigo-500">
                <input type="date" name="from_date" value="{{ $fromDate }}" class="text-xs rounded-xl border-slate-300 p-2">
                <input type="date" name="to_date" value="{{ $toDate }}" class="text-xs rounded-xl border-slate-300 p-2">
                <select name="type" class="text-xs rounded-xl border-slate-300 p-2">
                    <option value="">— كافة أنواع القيود —</option>
                    <option value="voucher" {{ $type === 'voucher' ? 'selected' : '' }}>سندات صرف وقبض</option>
                    <option value="manual" {{ $type === 'manual' ? 'selected' : '' }}>قيود تسوية يدوية</option>
                    <option value="reversal" {{ $type === 'reversal' ? 'selected' : '' }}>قيود عكسية</option>
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800">تصفية</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-700 font-black border-b border-slate-200">
                    <tr>
                        <th class="p-4">رقم القيد</th>
                        <th class="p-4">التاريخ</th>
                        <th class="p-4">المصدر / النوع</th>
                        <th class="p-4">البيان والشرح العام</th>
                        <th class="p-4">إجمالي المدين</th>
                        <th class="p-4">إجمالي الدائن</th>
                        <th class="p-4">الحالة</th>
                        <th class="p-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($entries as $entry)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-black text-indigo-700">
                                <a href="{{ route('accounting.journal_entries.show', $entry->id) }}" class="hover:underline">
                                    {{ $entry->entry_no }}
                                </a>
                            </td>
                            <td class="p-4 font-mono text-slate-700">{{ $entry->entry_date->format('Y/m/d') }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800">
                                    {{ $entry->reference_type_label }}
                                </span>
                            </td>
                            <td class="p-4 max-w-sm text-slate-700 font-medium leading-relaxed">
                                {{ $entry->description }}
                                @if($entry->reversed_by_entry_id)
                                    <span class="block text-rose-600 font-bold text-[10px] mt-0.5">⚠️ ملغي بقيد عكسي رقم: {{ $entry->reversedBy?->entry_no }}</span>
                                @endif
                            </td>
                            <td class="p-4 font-mono font-black text-blue-700">
                                {{ number_format($entry->total_debit, 2) }}
                            </td>
                            <td class="p-4 font-mono font-black text-emerald-700">
                                {{ number_format($entry->total_credit, 2) }}
                            </td>
                            <td class="p-4">
                                @if($entry->is_posted)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">مرحل</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800">مسودة</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('accounting.journal_entries.show', $entry->id) }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] transition" title="استعراض أسطر القيد">
                                        👁️ عرض
                                    </a>
                                    <a href="{{ route('accounting.journal_entries.print', $entry->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition" title="طباعة سند القيد">
                                        🖨️ طباعة
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 font-bold">لا توجد قيود يومية مطابقة للبحث.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($entries->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $entries->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
