@extends('layouts.app', ['title' => 'تفاصيل قيد اليومية ' . $entry->entry_no])

@section('content')
<div class="space-y-6">

    <!-- شريط التنقل المحاسبي الموحد -->
    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="font-mono text-xl font-black text-indigo-700 bg-indigo-50 px-3 py-1 rounded-xl border border-indigo-200">
                    {{ $entry->entry_no }}
                </span>
                <span class="px-2.5 py-1 rounded-full text-xs font-black {{ $entry->is_posted ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $entry->is_posted ? 'قيد مرحل نهائياً' : 'قيد مسودة' }}
                </span>
                @if($entry->reversed_by_entry_id)
                    <span class="px-2.5 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-800">
                        قيد ملغي بعكس
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1.5">{{ $entry->description }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('accounting.journal_entries.print', $entry->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5">
                <span>🖨️</span>
                <span>طباعة القيد (A4)</span>
            </a>
            @if($entry->is_posted && !$entry->reversed_by_entry_id)
                <button type="button" onclick="openReverseModal()" class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition flex items-center gap-1.5">
                    <span>🔄</span>
                    <span>إلغاء بقيد عكسي (Reversal)</span>
                </button>
            @endif
            <a href="{{ route('accounting.journal_entries.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                ← العودة للدفتر
            </a>
        </div>
    </div>

    <!-- بطاقة بيانات القيد العامة -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
        <div>
            <span class="text-slate-400 font-bold block">تاريخ القيد:</span>
            <span class="font-mono font-black text-slate-900 mt-1 block">{{ $entry->entry_date->format('Y/m/d') }}</span>
        </div>
        <div>
            <span class="text-slate-400 font-bold block">نوع القيد / المصدر:</span>
            <span class="font-bold text-slate-900 mt-1 block">{{ $entry->reference_type_label }}</span>
        </div>
        <div>
            <span class="text-slate-400 font-bold block">منشئ القيد:</span>
            <span class="font-bold text-slate-900 mt-1 block">{{ $entry->creator?->name ?? 'النظام الآلي' }}</span>
        </div>
        <div>
            <span class="text-slate-400 font-bold block">تاريخ ووقت الترحيل:</span>
            <span class="font-mono text-slate-900 mt-1 block">{{ $entry->posted_at?->format('Y/m/d H:i') ?? '—' }}</span>
        </div>
    </div>

    <!-- جدول أسطر القيد المزدوج -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-sm font-black text-slate-800">أسطر وتفاصيل القيد اليومي المزدوج</h2>
            <span class="text-xs font-bold text-slate-500">عدد الأسطر: ({{ $entry->lines->count() }})</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-700 font-black border-b border-slate-200">
                    <tr>
                        <th class="p-4 w-12 text-center">#</th>
                        <th class="p-4">كود الحساب</th>
                        <th class="p-4">اسم الحساب المحاسبي</th>
                        <th class="p-4">مركز التكلفة</th>
                        <th class="p-4 text-left">مدين (Debit)</th>
                        <th class="p-4 text-left">دائن (Credit)</th>
                        <th class="p-4">البيان والشرح</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($entry->lines as $idx => $line)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 text-center text-slate-400">{{ $idx + 1 }}</td>
                            <td class="p-4 font-mono font-black text-indigo-700">{{ $line->account?->account_code }}</td>
                            <td class="p-4 font-bold text-slate-900">{{ $line->account?->account_name }}</td>
                            <td class="p-4 text-slate-600">
                                @if($line->costCenter)
                                    <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-bold text-[11px]">
                                        [{{ $line->costCenter->code }}] {{ $line->costCenter->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="p-4 font-mono font-bold text-left text-blue-700">
                                {{ $line->debit > 0 ? number_format($line->debit, 2) : '—' }}
                            </td>
                            <td class="p-4 font-mono font-bold text-left text-emerald-700">
                                {{ $line->credit > 0 ? number_format($line->credit, 2) : '—' }}
                            </td>
                            <td class="p-4 text-slate-600">{{ $line->line_description ?: $entry->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-900 text-white font-black text-xs">
                    <tr>
                        <td colspan="4" class="p-4 text-right">الإجمالي العام للقيد المتزن:</td>
                        <td class="p-4 text-left font-mono text-blue-400">{{ number_format($entry->total_debit, 2) }} ر.ي</td>
                        <td class="p-4 text-left font-mono text-emerald-400">{{ number_format($entry->total_credit, 2) }} ر.ي</td>
                        <td class="p-4 text-center">
                            <span class="text-[11px] text-emerald-400">✓ متزن ومطابق</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<!-- Modal: إنشاء قيد عكسي -->
<div id="reverseModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
        <div class="p-6 bg-rose-900 text-white flex justify-between items-center">
            <h3 class="font-black text-base">إلغاء القيد بقيد عكسي (Reversal Entry)</h3>
            <button type="button" onclick="closeReverseModal()" class="text-rose-200 hover:text-white text-lg">✕</button>
        </div>
        <form action="{{ route('accounting.journal_entries.reverse', $entry->id) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <p class="text-xs text-slate-600 leading-relaxed">
                وفقاً للأعراف المحاسبية الصارمة، لا يجوز حذف أو تعديل القيد المرحل. سيقوم النظام بتوليد قيد عكسي آلي يعكس المدين دائناً والدائن مديناً ويلغي أثر القيد رقم <span class="font-mono font-bold text-slate-900">{{ $entry->entry_no }}</span> بالكامل.
            </p>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">سبب الإلغاء والتسوية العكسية *</label>
                <textarea name="reason" rows="3" required placeholder="مثال: تسجيل القيد بالخطأ / تعديل الحسابات..." class="w-full text-xs rounded-xl border-slate-300 p-2.5 focus:border-rose-500"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeReverseModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600">إلغاء</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-sm">تأكيد توليد القيد العكسي</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReverseModal() {
        document.getElementById('reverseModal').classList.remove('hidden');
    }
    function closeReverseModal() {
        document.getElementById('reverseModal').classList.add('hidden');
    }
</script>
@endsection
