<!-- مودال استعراض القيد المحاسبي والأثر المالي للعملية -->
<div id="voucherJournalEntryModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- خلفية معتمة -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" onclick="closeJournalEntryModal()"></div>

    <div class="flex min-h-screen items-center justify-center p-3 text-center sm:p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-right shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-slate-200">
            
            <!-- رأس المودال -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white px-6 py-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-2xl border border-white/20 shadow-inner">
                        ⚖️
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black text-white tracking-wide" id="modalVoucherTitle">
                                القيد المحاسبي والأثر المالي للعملية
                            </h3>
                            <span id="modalVoucherTypeBadge" class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-white/20 text-white border border-white/30">
                                سند قبض
                            </span>
                        </div>
                        <p class="text-xs text-slate-300 mt-1 flex items-center gap-3 flex-wrap">
                            <span>رقم السند: <strong id="modalVoucherNo" class="font-mono text-amber-300">—</strong></span>
                            <span>•</span>
                            <span>رقم القيد الآلي: <strong id="modalEntryNo" class="font-mono text-emerald-300">—</strong></span>
                            <span>•</span>
                            <span>التاريخ: <span id="modalVoucherDate" class="font-mono">—</span></span>
                        </p>
                    </div>
                </div>

                <button type="button" onclick="closeJournalEntryModal()" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-lg transition">
                    ✕
                </button>
            </div>

            <!-- محتوى المودال -->
            <div class="p-6 space-y-6">

                <!-- حالة التحميل Spinner -->
                <div id="modalLoadingState" class="py-16 text-center">
                    <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-indigo-600 border-t-transparent"></div>
                    <p class="mt-3 text-sm font-bold text-slate-600">جارٍ قراءة القيد المحاسبي وفحص توازن الطرفين (المدين والدائن)...</p>
                </div>

                <!-- رسالة الخطأ إن وجدت -->
                <div id="modalErrorState" class="hidden p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-bold text-center">
                    تعذر جلب تفاصيل القيد المحاسبي لهذه العملية.
                </div>

                <!-- تفاصيل القيد بعد التحميل -->
                <div id="modalEntryContent" class="hidden space-y-5">
                    
                    <!-- شريط حالة التوازن المحاسبي المعياري -->
                    <div id="balanceAlertBox" class="p-4 rounded-2xl border flex items-center justify-between gap-3 flex-wrap transition shadow-xs">
                        <div class="flex items-center gap-3">
                            <span id="balanceIcon" class="text-2xl">✓</span>
                            <div>
                                <h4 id="balanceStatusTitle" class="font-black text-sm">القيد متزن ومطابق 100%</h4>
                                <p id="balanceStatusDesc" class="text-xs text-slate-600 mt-0.5">
                                    يطابق المعايير المحاسبية المعتمدة (المدين = الدائن، الفارق 0.00 ر.ي)
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-mono font-bold">
                            <div class="text-left">
                                <span class="text-slate-500 block text-[10px] font-sans">إجمالي المدين (Debit)</span>
                                <span id="badgeTotalDebit" class="text-blue-700 text-sm font-black">—</span>
                            </div>
                            <div class="text-slate-300 text-lg">|</div>
                            <div class="text-left">
                                <span class="text-slate-500 block text-[10px] font-sans">إجمالي الدائن (Credit)</span>
                                <span id="badgeTotalCredit" class="text-purple-700 text-sm font-black">—</span>
                            </div>
                        </div>
                    </div>

                    <!-- البيان العام للعملية -->
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 text-xs text-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div>
                            <span class="font-bold text-slate-500 block mb-0.5 text-[11px]">بيان وسند العملية:</span>
                            <span id="modalVoucherStatement" class="font-bold text-slate-900 text-sm">—</span>
                        </div>
                        <div class="flex items-center gap-3 font-mono text-[11px] text-slate-500 shrink-0">
                            <span>الخزينة/الصندوق: <strong id="modalBoxName" class="text-slate-800 font-sans">—</strong></span>
                            <span>•</span>
                            <span>المستخدم: <strong id="modalCreatorName" class="text-slate-800 font-sans">—</strong></span>
                        </div>
                    </div>

                    <!-- جدول أطراف القيد المزدوج (مدين / دائن) -->
                    <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                        <div class="bg-slate-100/90 px-4 py-2.5 border-b border-slate-200 flex items-center justify-between">
                            <span class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                                <span>📋</span>
                                <span>بنود وأطراف القيد اليومي (Double-Entry Lines)</span>
                            </span>
                            <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-md">
                                قيد آلي مرحّل إلى دفتر الأستاذ العام
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-right text-xs">
                                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                                    <tr>
                                        <th class="py-2.5 px-3">الطرف</th>
                                        <th class="py-2.5 px-3 font-mono">رقم الحساب</th>
                                        <th class="py-2.5 px-3">اسم الحساب في الدليل</th>
                                        <th class="py-2.5 px-3">مركز التكلفة</th>
                                        <th class="py-2.5 px-3">البيان / الشرح التفصيلي</th>
                                        <th class="py-2.5 px-3 text-left font-mono">مدين (+)</th>
                                        <th class="py-2.5 px-3 text-left font-mono">دائن (-)</th>
                                    </tr>
                                </thead>
                                <tbody id="modalJournalLinesTable" class="divide-y divide-slate-100 font-mono text-[11px]">
                                    <!-- يتم ملء الأسطر عبر JavaScript -->
                                </tbody>
                                <tfoot class="bg-slate-100 font-bold border-t-2 border-slate-300 text-xs">
                                    <tr>
                                        <td colspan="5" class="py-3 px-3 text-slate-800 font-black">
                                            الإجمالي العام للقيد المحاسبي
                                        </td>
                                        <td id="tableTotalDebit" class="py-3 px-3 text-left font-mono font-black text-blue-700">0.00</td>
                                        <td id="tableTotalCredit" class="py-3 px-3 text-left font-mono font-black text-purple-700">0.00</td>
                                    </tr>
                                    <tr class="bg-slate-200/60 text-[11px]">
                                        <td colspan="5" class="py-2 px-3 text-slate-600">
                                            الفارق بين المدين والدائن (حالة الاتزان):
                                        </td>
                                        <td colspan="2" id="tableDifference" class="py-2 px-3 text-left font-mono font-black text-emerald-700">
                                            0.00 ر.ي (متزن تماماً ✓)
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- بطاقات الأثر المالي والتحليل المحاسبي -->
                    <div>
                        <h5 class="text-xs font-black text-slate-800 mb-2 flex items-center gap-1.5">
                            <span>💡</span>
                            <span>التحليل والأثر المالي للعملية (Financial Impact Breakdown)</span>
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <!-- أثر السيولة -->
                            <div class="bg-blue-50/70 border border-blue-200 rounded-2xl p-3.5 text-right">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[11px] font-black text-blue-900">أثر السيولة النقدية</span>
                                    <span class="text-sm">🏦</span>
                                </div>
                                <p id="impactBox" class="text-xs font-bold text-blue-800 mt-1 leading-relaxed">—</p>
                                <span class="text-[10px] text-blue-600 block mt-1">حساب الأصول المتداولة (الصندوق/البنك)</span>
                            </div>

                            <!-- أثر الطرف المقابل -->
                            <div class="bg-purple-50/70 border border-purple-200 rounded-2xl p-3.5 text-right">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[11px] font-black text-purple-900">أثر الحساب المقابل / الذمم</span>
                                    <span class="text-sm">👥</span>
                                </div>
                                <p id="impactParty" class="text-xs font-bold text-purple-800 mt-1 leading-relaxed">—</p>
                                <span class="text-[10px] text-purple-600 block mt-1">ذمم الطلاب / المعلمين / المصروفات</span>
                            </div>

                            <!-- القاعدة المحاسبية المطبقة -->
                            <div class="bg-emerald-50/70 border border-emerald-200 rounded-2xl p-3.5 text-right">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[11px] font-black text-emerald-900">معيار القيد المزدوج</span>
                                    <span class="text-sm">⚖️</span>
                                </div>
                                <p id="impactRule" class="text-xs font-bold text-emerald-800 mt-1 leading-relaxed">—</p>
                                <span class="text-[10px] text-emerald-600 block mt-1">توازن الأصول والخصوم والمصروفات</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ذيل المودال وأزرار الإجراءات -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a id="btnViewJournalEntry" href="#" target="_blank" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5 shadow-xs">
                        <span>🔍</span>
                        <span>عرض القيد في دفتر اليومية العام</span>
                    </a>
                    <a id="btnPrintJournalEntry" href="#" target="_blank" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                        <span>🖨️</span>
                        <span>طباعة إشعار القيد</span>
                    </a>
                </div>

                <button type="button" onclick="closeJournalEntryModal()" class="w-full sm:w-auto px-5 py-2 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl transition">
                    إغلاق النافذة
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    function openJournalEntryModal(voucherId) {
        const modal = document.getElementById('voucherJournalEntryModal');
        const loading = document.getElementById('modalLoadingState');
        const errorState = document.getElementById('modalErrorState');
        const content = document.getElementById('modalEntryContent');

        modal.classList.remove('hidden');
        loading.classList.remove('hidden');
        errorState.classList.add('hidden');
        content.classList.add('hidden');

        fetch(`/accounting/voucher/${voucherId}/journal-entry`)
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(data => {
                if (!data.success) throw new Error(data.message || 'Error');

                const v = data.voucher;
                const e = data.journal_entry;
                const impact = data.impact_summary;

                // تحديث الرأس
                document.getElementById('modalVoucherTitle').innerText = `القيد المحاسبي والأثر المالي: ${v.voucher_type_label}`;
                document.getElementById('modalVoucherNo').innerText = v.voucher_no;
                document.getElementById('modalEntryNo').innerText = e.entry_no;
                document.getElementById('modalVoucherDate').innerText = v.created_at;
                
                const typeBadge = document.getElementById('modalVoucherTypeBadge');
                typeBadge.innerText = v.voucher_type_label;
                if (v.voucher_type === 'receipt') {
                    typeBadge.className = "text-xs px-2.5 py-0.5 rounded-full font-bold bg-emerald-500/30 text-emerald-200 border border-emerald-400/40";
                } else {
                    typeBadge.className = "text-xs px-2.5 py-0.5 rounded-full font-bold bg-rose-500/30 text-rose-200 border border-rose-400/40";
                }

                // البيان والخزينة
                document.getElementById('modalVoucherStatement').innerText = v.statement || '—';
                document.getElementById('modalBoxName').innerText = v.box_name;
                document.getElementById('modalCreatorName').innerText = v.creator_name;

                // التوازن المحاسبي
                const balanceBox = document.getElementById('balanceAlertBox');
                const balanceIcon = document.getElementById('balanceIcon');
                const balanceTitle = document.getElementById('balanceStatusTitle');
                const balanceDesc = document.getElementById('balanceStatusDesc');

                if (e.is_balanced) {
                    balanceBox.className = "p-4 rounded-2xl border border-emerald-200 bg-emerald-50/70 text-emerald-900 flex items-center justify-between gap-3 flex-wrap transition shadow-xs";
                    balanceIcon.innerText = "✓";
                    balanceIcon.className = "text-2xl text-emerald-600";
                    balanceTitle.innerText = "القيد المحاسبي متزن ومطابق 100% معيارياً";
                    balanceDesc.innerText = `إجمالي المدين يساوي إجمالي الدائن تماماً (${e.formatted_total_debit} ${v.currency_symbol}) بدون أي فارق.`;
                } else {
                    balanceBox.className = "p-4 rounded-2xl border border-rose-200 bg-rose-50/70 text-rose-900 flex items-center justify-between gap-3 flex-wrap transition shadow-xs";
                    balanceIcon.innerText = "⚠️";
                    balanceIcon.className = "text-2xl text-rose-600";
                    balanceTitle.innerText = "تنبيه: القيد المحاسبي غير متزن!";
                    balanceDesc.innerText = `يوجد فارق بمقدار ${e.formatted_difference} ${v.currency_symbol} بين المدين والدائن.`;
                }

                document.getElementById('badgeTotalDebit').innerText = `${e.formatted_total_debit} ${v.currency_symbol}`;
                document.getElementById('badgeTotalCredit').innerText = `${e.formatted_total_credit} ${v.currency_symbol}`;

                // جدول أسطر القيد
                const tbody = document.getElementById('modalJournalLinesTable');
                tbody.innerHTML = '';

                e.lines.forEach(line => {
                    const tr = document.createElement('tr');
                    tr.className = "hover:bg-slate-50/80 transition";
                    tr.innerHTML = `
                        <td class="py-2.5 px-3">
                            <span class="px-2 py-0.5 rounded-md font-sans font-bold text-[10px] border ${line.side_badge}">
                                ${line.side_label}
                            </span>
                        </td>
                        <td class="py-2.5 px-3 font-bold text-slate-800">${line.account_code}</td>
                        <td class="py-2.5 px-3 font-sans font-bold text-slate-900">
                            <div>${line.account_name}</div>
                            <div class="text-[10px] text-slate-400 font-normal">${line.category} • ${line.impact}</div>
                        </td>
                        <td class="py-2.5 px-3 font-sans text-slate-600">
                            ${line.cost_center_code ? `<span class="bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">${line.cost_center_code} - ${line.cost_center_name}</span>` : '<span class="text-slate-400">—</span>'}
                        </td>
                        <td class="py-2.5 px-3 font-sans text-slate-600 max-w-xs truncate" title="${line.line_description}">
                            ${line.line_description}
                        </td>
                        <td class="py-2.5 px-3 text-left font-bold ${line.debit > 0 ? 'text-blue-700' : 'text-slate-300'}">
                            ${line.formatted_debit}
                        </td>
                        <td class="py-2.5 px-3 text-left font-bold ${line.credit > 0 ? 'text-purple-700' : 'text-slate-300'}">
                            ${line.formatted_credit}
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                // تذييل الجدول
                document.getElementById('tableTotalDebit').innerText = `${e.formatted_total_debit} ${v.currency_symbol}`;
                document.getElementById('tableTotalCredit').innerText = `${e.formatted_total_credit} ${v.currency_symbol}`;
                
                const diffElem = document.getElementById('tableDifference');
                if (e.is_balanced) {
                    diffElem.className = "py-2 px-3 text-left font-mono font-black text-emerald-700";
                    diffElem.innerText = `0.00 ${v.currency_symbol} (متزن تماماً ✓)`;
                } else {
                    diffElem.className = "py-2 px-3 text-left font-mono font-black text-rose-600";
                    diffElem.innerText = `${e.formatted_difference} ${v.currency_symbol} (فارق غير متزن ⚠️)`;
                }

                // الأثر المالي
                document.getElementById('impactBox').innerText = impact.box_impact;
                document.getElementById('impactParty').innerText = impact.party_impact;
                document.getElementById('impactRule').innerText = impact.accounting_rule;

                // روابط الإجراءات
                document.getElementById('btnViewJournalEntry').href = e.view_url;
                document.getElementById('btnPrintJournalEntry').href = e.print_url;

                // إظهار المحتوى
                loading.classList.add('hidden');
                content.classList.remove('hidden');
            })
            .catch(err => {
                console.error(err);
                loading.classList.add('hidden');
                errorState.classList.remove('hidden');
            });
    }

    function closeJournalEntryModal() {
        document.getElementById('voucherJournalEntryModal').classList.add('hidden');
    }

    // إغلاق المودال بزر ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeJournalEntryModal();
        }
    });
</script>
