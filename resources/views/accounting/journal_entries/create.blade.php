@extends('layouts.app', ['title' => 'تسجيل قيد يومية يدوي'])

@section('content')
<div class="space-y-6">

    <!-- شريط التنقل المحاسبي الموحد -->
    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>➕</span>
                <span>تسجيل قيد يومية عام يدوي (Manual Journal Entry)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                إدخال قيود التسويات، الإهلاك، إقفال الفترات، والقيود الافتتاحية مع فحص التوازن اللحظي التلقائي
            </p>
        </div>
        <a href="{{ route('accounting.journal_entries.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
            ← العودة لدفتر اليومية
        </a>
    </div>

    <!-- نموذج القيد -->
    <form action="{{ route('accounting.journal_entries.store') }}" method="POST" id="journalEntryForm" class="space-y-6">
        @csrf

        <!-- بيانات رأس القيد -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <h2 class="text-sm font-black text-slate-800 border-b border-slate-100 pb-3">1. البيانات العامة للقيد</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">رقم القيد المتسلسل الآلي</label>
                    <input type="text" value="{{ $nextEntryNo }}" readonly class="w-full text-xs font-mono font-black bg-slate-50 text-indigo-700 rounded-xl border-slate-200 p-2.5">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">تاريخ القيد *</label>
                    <input type="date" name="entry_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
                </div>
                <div class="flex items-center gap-2 pt-6">
                    <input type="checkbox" name="is_posted" value="1" id="isPostedCheck" checked class="rounded border-slate-300 text-indigo-600">
                    <label for="isPostedCheck" class="text-xs font-bold text-slate-700 cursor-pointer">
                        ترحيل القيد فوراً إلى الأستاذ العام وتحديث الأرصدة
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">البيان والشرح العام للقيد *</label>
                <input type="text" name="description" required placeholder="مثال: قيد تسوية إيجار المقر لشهر أكتوبر 2026..." class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
            </div>
        </div>

        <!-- جدول أسطر القيد المزدوج -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 flex-wrap gap-2">
                <div>
                    <h2 class="text-sm font-black text-slate-800">2. أسطر القيد المحاسبي المزدوج</h2>
                    <p class="text-[11px] text-slate-400">يشترط تساوي إجمالي الطرف المدين مع إجمالي الطرف الدائن لترحيل القيد</p>
                </div>
                <button type="button" onclick="addNewLine()" class="px-3.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs transition flex items-center gap-1.5">
                    <span>➕</span>
                    <span>إضافة سطر جديد</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs" id="linesTable">
                    <thead class="bg-slate-50 text-slate-700 font-black border-b border-slate-200">
                        <tr>
                            <th class="p-3 w-10 text-center">#</th>
                            <th class="p-3 w-1/3">الحساب المحاسبي الفرعي *</th>
                            <th class="p-3 w-1/4">مركز التكلفة</th>
                            <th class="p-3 w-28 text-left">مدين (Debit)</th>
                            <th class="p-3 w-28 text-left">دائن (Credit)</th>
                            <th class="p-3">شرح السطر (البيان)</th>
                            <th class="p-3 w-12 text-center">حذف</th>
                        </tr>
                    </thead>
                    <tbody id="linesTbody" class="divide-y divide-slate-100 font-medium">
                        <!-- الأسطر المبدئية للقيد -->
                        @for($i = 0; $i < 4; $i++)
                            <tr class="line-row">
                                <td class="p-3 text-center text-slate-400 row-number">{{ $i + 1 }}</td>
                                <td class="p-3">
                                    <select name="lines[{{ $i }}][account_id]" class="w-full text-xs font-bold rounded-xl border-slate-300 p-2 focus:border-indigo-500 account-select" {{ $i < 2 ? 'required' : '' }}>
                                        <option value="">— اختر الحساب الفرعي —</option>
                                        @foreach($subAccounts as $acc)
                                            <option value="{{ $acc->id }}">[{{ $acc->account_code }}] {{ $acc->account_name }} ({{ $acc->nature_label }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-3">
                                    <select name="lines[{{ $i }}][cost_center_id]" class="w-full text-xs rounded-xl border-slate-300 p-2 focus:border-indigo-500">
                                        <option value="">— اختياري —</option>
                                        @foreach($costCenters as $cc)
                                            <option value="{{ $cc->id }}">[{{ $cc->code }}] {{ $cc->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-3">
                                    <input type="number" step="0.01" min="0" name="lines[{{ $i }}][debit]" oninput="recalcTotals()" placeholder="0.00" class="w-full text-xs font-mono font-bold rounded-xl border-slate-300 p-2 text-left text-blue-700 debit-input">
                                </td>
                                <td class="p-3">
                                    <input type="number" step="0.01" min="0" name="lines[{{ $i }}][credit]" oninput="recalcTotals()" placeholder="0.00" class="w-full text-xs font-mono font-bold rounded-xl border-slate-300 p-2 text-left text-emerald-700 credit-input">
                                </td>
                                <td class="p-3">
                                    <input type="text" name="lines[{{ $i }}][line_description]" placeholder="شرح خاص بالسطر (اختياري)..." class="w-full text-xs rounded-xl border-slate-300 p-2">
                                </td>
                                <td class="p-3 text-center">
                                    <button type="button" onclick="removeLine(this)" class="text-slate-400 hover:text-rose-600 font-bold text-sm" title="حذف السطر">✕</button>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <!-- شريط فحص وتطابق التوازن المباشر -->
            <div class="bg-slate-900 text-white p-5 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-6">
                    <div>
                        <span class="text-[10px] text-slate-400 block">إجمالي الطرف المدين (Debit):</span>
                        <span id="totalDebitDisplay" class="font-mono text-lg font-black text-blue-400">0.00 ر.ي</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block">إجمالي الطرف الدائن (Credit):</span>
                        <span id="totalCreditDisplay" class="font-mono text-lg font-black text-emerald-400">0.00 ر.ي</span>
                    </div>
                    <div class="border-r border-slate-700 pr-4">
                        <span class="text-[10px] text-slate-400 block">حالة التوازن:</span>
                        <div id="balanceBadge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-rose-500/20 text-rose-400 border border-rose-500/30">
                            <span>✕</span>
                            <span id="balanceText">غير متزن (الفارق: 0.00)</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" id="saveEntryBtn" disabled class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-700 disabled:opacity-50 text-white font-black text-xs shadow-lg transition flex items-center gap-2">
                        <span>💾</span>
                        <span>حفظ وترحيل القيد</span>
                    </button>
                </div>
            </div>

        </div>

    </form>

</div>

<script>
    let lineIndex = 4;

    function addNewLine() {
        const tbody = document.getElementById('linesTbody');
        const row = document.createElement('tr');
        row.className = 'line-row';
        row.innerHTML = `
            <td class="p-3 text-center text-slate-400 row-number">${tbody.children.length + 1}</td>
            <td class="p-3">
                <select name="lines[${lineIndex}][account_id]" class="w-full text-xs font-bold rounded-xl border-slate-300 p-2 focus:border-indigo-500 account-select">
                    <option value="">— اختر الحساب الفرعي —</option>
                    @foreach($subAccounts as $acc)
                        <option value="{{ $acc->id }}">[{{ $acc->account_code }}] {{ $acc->account_name }} ({{ $acc->nature_label }})</option>
                    @endforeach
                </select>
            </td>
            <td class="p-3">
                <select name="lines[${lineIndex}][cost_center_id]" class="w-full text-xs rounded-xl border-slate-300 p-2 focus:border-indigo-500">
                    <option value="">— اختياري —</option>
                    @foreach($costCenters as $cc)
                        <option value="{{ $cc->id }}">[{{ $cc->code }}] {{ $cc->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="p-3">
                <input type="number" step="0.01" min="0" name="lines[${lineIndex}][debit]" oninput="recalcTotals()" placeholder="0.00" class="w-full text-xs font-mono font-bold rounded-xl border-slate-300 p-2 text-left text-blue-700 debit-input">
            </td>
            <td class="p-3">
                <input type="number" step="0.01" min="0" name="lines[${lineIndex}][credit]" oninput="recalcTotals()" placeholder="0.00" class="w-full text-xs font-mono font-bold rounded-xl border-slate-300 p-2 text-left text-emerald-700 credit-input">
            </td>
            <td class="p-3">
                <input type="text" name="lines[${lineIndex}][line_description]" placeholder="شرح خاص بالسطر (اختياري)..." class="w-full text-xs rounded-xl border-slate-300 p-2">
            </td>
            <td class="p-3 text-center">
                <button type="button" onclick="removeLine(this)" class="text-slate-400 hover:text-rose-600 font-bold text-sm" title="حذف السطر">✕</button>
            </td>
        `;
        tbody.appendChild(row);
        lineIndex++;
        recalcTotals();
    }

    function removeLine(btn) {
        const tbody = document.getElementById('linesTbody');
        if (tbody.children.length <= 2) {
            alert('يجب أن يحتوي القيد المحاسبي على سطرين على الأقل (طرف مدين وطرف دائن).');
            return;
        }
        btn.closest('tr').remove();
        // إعادة ترقيم الصفوف
        Array.from(tbody.children).forEach((tr, idx) => {
            tr.querySelector('.row-number').textContent = idx + 1;
        });
        recalcTotals();
    }

    function recalcTotals() {
        let totalDebit = 0;
        let totalCredit = 0;

        document.querySelectorAll('.debit-input').forEach(inp => {
            const val = parseFloat(inp.value) || 0;
            totalDebit += val;
        });

        document.querySelectorAll('.credit-input').forEach(inp => {
            const val = parseFloat(inp.value) || 0;
            totalCredit += val;
        });

        document.getElementById('totalDebitDisplay').textContent = totalDebit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ر.ي';
        document.getElementById('totalCreditDisplay').textContent = totalCredit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ر.ي';

        const diff = Math.abs(totalDebit - totalCredit);
        const badge = document.getElementById('balanceBadge');
        const text = document.getElementById('balanceText');
        const saveBtn = document.getElementById('saveEntryBtn');

        if (totalDebit > 0 && diff < 0.001) {
            badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
            badge.querySelector('span').textContent = '✓';
            text.textContent = 'القيد متزن محاسبياً 100%';
            saveBtn.disabled = false;
        } else {
            badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-rose-500/20 text-rose-400 border border-rose-500/30';
            badge.querySelector('span').textContent = '✕';
            text.textContent = totalDebit === 0 ? 'أدخل مبالغ القيد' : 'غير متزن (الفارق: ' + diff.toFixed(2) + ' ر.ي)';
            saveBtn.disabled = true;
        }
    }

    // تشغيل الحساب الأولي
    recalcTotals();
</script>
@endsection
