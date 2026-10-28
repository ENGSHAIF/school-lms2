@extends('layouts.app', ['title' => 'الترحيل النهائي وتجميد القيود - يمن سوفت ERP'])

@section('content')
<div class="space-y-6">

    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 mb-1 flex items-center gap-2">
                <span>🔒</span>
                <span>الترحيل النهائي وتجميد القيود (Batch Posting Engine)</span>
            </h1>
            <p class="text-xs text-slate-500">وفق قواعد نظام المتكامل بلاس ERP: ترحيل السندات والقيود غير المرحلة دفعة واحدة إلى الأستاذ العام وتجميدها نهائياً لمنع أي تعديل أو حذف بعد التدقيق.</p>
        </div>
    </div>

    <!-- نموذج الترحيل النهائي -->
    <form action="{{ route('accounting.posting.batch') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- 1. السندات غير المرحلة -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                        <span>🧾</span>
                        <span>السندات غير المرحلة ({{ $unpostedVouchers->count() }})</span>
                    </h2>
                    <label class="text-[11px] font-bold text-indigo-600 flex items-center gap-1 cursor-pointer">
                        <input type="checkbox" onchange="toggleSelectAll('voucher_cb', this.checked)" class="rounded text-indigo-600">
                        <span>تحديد الكل</span>
                    </label>
                </div>

                <div class="max-h-96 overflow-y-auto divide-y divide-slate-100 text-xs">
                    @forelse($unpostedVouchers as $v)
                    <div class="p-3 flex items-center justify-between hover:bg-slate-50">
                        <div class="flex items-center gap-2.5">
                            <input type="checkbox" name="voucher_ids[]" value="{{ $v->id }}" class="voucher_cb rounded text-indigo-600">
                            <div>
                                <div class="font-mono font-bold text-slate-900">#{{ $v->voucher_no }}</div>
                                <div class="text-[11px] text-slate-500">{{ $v->account?->account_name }}</div>
                            </div>
                        </div>
                        <div class="text-left font-mono font-black text-slate-800">
                            {{ number_format($v->amount, 2) }} {{ $v->currency }}
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-400">كافة السندات مرحلة نهائياً ومجمدة.</div>
                    @endforelse
                </div>
            </div>

            <!-- 2. قيود اليومية غير المرحلة -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                        <span>⚖️</span>
                        <span>قيود اليومية غير المرحلة ({{ $unpostedEntries->count() }})</span>
                    </h2>
                    <label class="text-[11px] font-bold text-indigo-600 flex items-center gap-1 cursor-pointer">
                        <input type="checkbox" onchange="toggleSelectAll('entry_cb', this.checked)" class="rounded text-indigo-600">
                        <span>تحديد الكل</span>
                    </label>
                </div>

                <div class="max-h-96 overflow-y-auto divide-y divide-slate-100 text-xs">
                    @forelse($unpostedEntries as $e)
                    <div class="p-3 flex items-center justify-between hover:bg-slate-50">
                        <div class="flex items-center gap-2.5">
                            <input type="checkbox" name="entry_ids[]" value="{{ $e->id }}" class="entry_cb rounded text-indigo-600">
                            <div>
                                <div class="font-mono font-bold text-slate-900">#{{ $e->entry_no }}</div>
                                <div class="text-[11px] text-slate-500">{{ $e->description }}</div>
                            </div>
                        </div>
                        <div class="text-left font-mono font-black text-slate-800">
                            {{ number_format($e->total_debit, 2) }} YER
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-400">كافة قيود اليومية مرحلة في الأستاذ العام.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- شريط تأكيد الترحيل -->
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mt-6 flex flex-col sm:flex-row justify-between items-center gap-3">
            <div class="flex items-center gap-2 text-xs text-amber-900">
                <span class="text-lg">⚠️</span>
                <span>تنبيه يمن سوفت: الترحيل النهائي ينقل الحركات إلى الدفاتر المحاسبية الرسمية ويمنع تعديل المبالغ أو أطراف القيد.</span>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                <span>🔒</span>
                <span>تنفيذ الترحيل النهائي وتجميد السجلات</span>
            </button>
        </div>
    </form>

</div>

<script>
function toggleSelectAll(className, checked) {
    document.querySelectorAll('.' + className).forEach(cb => cb.checked = checked);
}
</script>
@endsection
