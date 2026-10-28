@extends('layouts.app', ['title' => 'الإشعارات المدينة والدائنة - يمن سوفت ERP'])

@section('content')
<div class="space-y-6">

    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 mb-1 flex items-center gap-2">
                <span>📑</span>
                <span>الإشعارات المدينة والدائنة (Debit & Credit Notes)</span>
            </h1>
            <p class="text-xs text-slate-500">وفق دليل نظام المتكامل بلاس ERP: تسوية حسابات العملاء والموردين عبر إصدار إشعارات الخصم والإضافة دون الحاجة لسند قبض أو صرف نقدي مباشر، مع توليد القيود المزدوجة.</p>
        </div>
        <button onclick="document.getElementById('newNoteModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-xs transition flex items-center gap-2">
            <span>➕</span>
            <span>إصدار إشعار مدين / دائن جديد</span>
        </button>
    </div>

    <!-- جدول الإشعارات -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200">
            <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <span>📑</span>
                <span>سجل الإشعارات المحاسبية المسجلة</span>
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-100/80 text-slate-700 font-black border-b border-slate-200">
                        <th class="p-3">رقم الإشعار</th>
                        <th class="p-3">نوع الإشعار</th>
                        <th class="p-3">التاريخ</th>
                        <th class="p-3">الطرف المتأثر</th>
                        <th class="p-3">الحساب المقابل</th>
                        <th class="p-3">المبلغ</th>
                        <th class="p-3">البيان</th>
                        <th class="p-3">القيد المحاسبي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($notes as $note)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-3 font-mono font-black text-indigo-950">#{{ $note->note_no }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $note->note_type === 'debit' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $note->note_type_label }}
                            </span>
                        </td>
                        <td class="p-3 font-mono text-slate-600">{{ $note->note_date->format('Y-m-d') }}</td>
                        <td class="p-3 font-bold text-slate-900">{{ $note->account?->account_name }}</td>
                        <td class="p-3 text-slate-600">{{ $note->secondaryAccount?->account_name }}</td>
                        <td class="p-3 font-mono font-black text-slate-900">{{ number_format($note->amount, 2) }} {{ $note->currency }}</td>
                        <td class="p-3 text-slate-600 max-w-xs truncate">{{ $note->statement }}</td>
                        <td class="p-3">
                            @if($note->journalEntry)
                                <a href="{{ route('accounting.journal_entries.show', $note->journalEntry->id) }}" class="text-indigo-600 hover:underline font-mono font-bold">
                                    #{{ $note->journalEntry->entry_no }}
                                </a>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-400">لا توجد إشعارات مدينة أو دائنة مسجلة.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notes->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $notes->links() }}
        </div>
        @endif
    </div>

</div>

<!-- مودال إصدار إشعار جديد -->
<div id="newNoteModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <span>📑</span>
                <span>إصدار إشعار مدين / دائن وتسوية حساب</span>
            </h3>
            <button onclick="document.getElementById('newNoteModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form action="{{ route('accounting.notes.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">نوع الإشعار:</label>
                    <select name="note_type" class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-bold">
                        <option value="debit">إشعار مدين (قيد على الطرف / زيادة مديونية)</option>
                        <option value="credit">إشعار دائن (إضافة للطرف / تسوية وتخفيض)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ الإشعار:</label>
                    <input type="date" name="note_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-mono font-bold">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">الطرف المتأثر (طالب / معلم / مورد):</label>
                <select name="account_id" required class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-bold">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">الحساب المقابل (إيرادات / مصروفات / خصم مسموح به):</label>
                <select name="secondary_account_id" required class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-bold">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">المبلغ:</label>
                    <input type="number" step="0.01" name="amount" required class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-mono font-bold" placeholder="0.00">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">مركز التكلفة (اختياري):</label>
                    <select name="cost_center_id" class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-bold">
                        <option value="">-- بدون مركز تكلفة --</option>
                        @foreach($costCenters as $cc)
                            <option value="{{ $cc->id }}">{{ $cc->code }} - {{ $cc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">البيان وسبب إصدار الإشعار:</label>
                <textarea name="statement" required rows="2" class="w-full rounded-xl border-slate-300 py-1.5 text-xs" placeholder="توضيح سبب القيد أو التسوية المالية..."></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('newNoteModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl font-black">إصدار وترحيل القيد</button>
            </div>
        </form>
    </div>
</div>
@endsection
