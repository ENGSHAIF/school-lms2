@extends('layouts.app', ['title' => 'النظام المحاسبي والمالي'])

@section('content')
<div class="space-y-6">

    <!-- شريط التنقل المحاسبي الموحد -->
    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 mb-1 flex items-center gap-2">
                <span>💼</span>
                <span>النظام المحاسبي والمالي وإدارة السندات</span>
            </h1>
            <p class="text-xs text-slate-500">إصدار سندات القبض والصرف، متابعة حركة الصندوق والبنوك، كشوفات الطلاب والمعلمين، وتوليد القيود الآلية</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('accounting.chart_of_accounts.index') }}" class="text-xs font-black text-indigo-700 bg-indigo-50 border border-indigo-200 px-3.5 py-2 rounded-xl hover:bg-indigo-100 transition flex items-center gap-1.5 shadow-2xs">
                <span>🌳</span>
                <span>الدليل المحاسبي (COA)</span>
            </a>
            <a href="{{ route('accounting.payment_requests.index') }}" class="text-xs font-black text-amber-800 bg-amber-50 border border-amber-200 px-3.5 py-2 rounded-xl hover:bg-amber-100 transition flex items-center gap-1.5 shadow-2xs">
                <span>📝</span>
                <span>طلبات الصرف والاعتماد</span>
            </a>
            <a href="{{ route('accounting.journal_entries.index') }}" class="text-xs font-black text-blue-700 bg-blue-50 border border-blue-200 px-3.5 py-2 rounded-xl hover:bg-blue-100 transition flex items-center gap-1.5 shadow-2xs">
                <span>⚖️</span>
                <span>قيود اليومية</span>
            </a>
            <a href="{{ route('accounting.reports.index') }}" class="text-xs font-black text-emerald-800 bg-emerald-50 border border-emerald-300 px-3.5 py-2 rounded-xl hover:bg-emerald-100 transition flex items-center gap-1.5 shadow-2xs">
                <span>📊</span>
                <span>التقارير والقوائم الختامية</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-white bg-slate-900 px-4 py-2 rounded-xl hover:bg-slate-800 transition">
                ← لوحة الإدارة
            </a>
        </div>
    </div>

    <!-- البطاقات المالية -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start">
                    <span class="text-xs font-bold text-slate-400 block">رصيد الصندوق الرئيسي (النقدية)</span>
                    <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700">1101</span>
                </div>
                <span class="text-2xl font-black text-emerald-600 font-mono mt-1 block">
                    {{ \App\Helpers\ArabicCurrencyHelper::format($cashBox?->current_balance ?? 0, $cashBox?->currency ?? 'YER') }}
                </span>
                <span class="text-[11px] text-slate-400 block mt-1">حساب: {{ $cashBox?->account_code ?? '1101' }} ({{ $cashBox?->currency_name ?? 'ريال يمني' }})</span>
            </div>
            @if($cashBox)
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between gap-1 text-[11px] font-bold">
                    <a href="{{ route('accounting.cash_boxes.statement', $cashBox->id) }}" class="text-emerald-700 hover:text-emerald-800 hover:underline flex items-center gap-1" title="كشف حركة الصندوق">
                        <span>📊</span>
                        <span>كشف الحركة</span>
                    </a>
                    <a href="{{ route('accounting.cash_boxes.statement.print', $cashBox->id) }}" target="_blank" class="text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded-lg transition flex items-center gap-1" title="طباعة تقرير مفصل">
                        <span>🖨️</span>
                        <span>طباعة</span>
                    </a>
                </div>
            @endif
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start">
                    <span class="text-xs font-bold text-slate-400 block">رصيد الحساب البنكي</span>
                    <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-blue-50 text-blue-700">1102</span>
                </div>
                <span class="text-2xl font-black text-blue-600 font-mono mt-1 block">
                    {{ \App\Helpers\ArabicCurrencyHelper::format($bankAccount?->current_balance ?? 0, $bankAccount?->currency ?? 'YER') }}
                </span>
                <span class="text-[11px] text-slate-400 block mt-1">حساب: {{ $bankAccount?->account_code ?? '1102' }} ({{ $bankAccount?->currency_name ?? 'ريال يمني' }})</span>
            </div>
            @if($bankAccount)
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between gap-1 text-[11px] font-bold">
                    <a href="{{ route('accounting.cash_boxes.statement', $bankAccount->id) }}" class="text-blue-700 hover:text-blue-800 hover:underline flex items-center gap-1" title="كشف حركة الحساب البنكي">
                        <span>📊</span>
                        <span>كشف الحركة</span>
                    </a>
                    <a href="{{ route('accounting.cash_boxes.statement.print', $bankAccount->id) }}" target="_blank" class="text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded-lg transition flex items-center gap-1" title="طباعة تقرير مفصل">
                        <span>🖨️</span>
                        <span>طباعة</span>
                    </a>
                </div>
            @endif
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 block">إجمالي المقبوضات (سندات القبض)</span>
            <span class="text-2xl font-black text-slate-800 font-mono mt-1 block">
                {{ \App\Helpers\ArabicCurrencyHelper::format($totalReceipts, 'YER') }}
            </span>
            <span class="text-[11px] text-slate-400 block mt-1">تحصيلات الطلاب المسجلة</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 block">إجمالي المصروفات (سندات الصرف)</span>
            <span class="text-2xl font-black text-rose-600 font-mono mt-1 block">
                {{ \App\Helpers\ArabicCurrencyHelper::format($totalPayments, 'YER') }}
            </span>
            <span class="text-[11px] text-slate-400 block mt-1">المدفوعات والمصروفات المسددة</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-indigo-200 shadow-sm bg-indigo-50/20">
            <span class="text-xs font-bold text-indigo-900 block">إجمالي مستحقات المعلمين المثبتة</span>
            <span class="text-2xl font-black text-indigo-700 font-mono mt-1 block">
                {{ \App\Helpers\ArabicCurrencyHelper::format($totalEntitlements ?? 0, 'YER') }}
            </span>
            <span class="text-[11px] text-indigo-600 block mt-1">قيود استحقاق تدريس وخدمات</span>
        </div>
    </div>

    <!-- تبويبات إصدار السندات (سند قبض / سند صرف) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        
        <div class="flex items-center justify-between border-b border-slate-200 pb-4 flex-wrap gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" onclick="switchVoucherTab('receipt')" id="tabBtnReceipt" class="px-5 py-2.5 rounded-xl font-black text-xs transition flex items-center gap-2 bg-emerald-600 text-white shadow-sm">
                    <span>🧾</span>
                    <span>إصدار سند قبض (طالب)</span>
                </button>
                <button type="button" onclick="switchVoucherTab('payment')" id="tabBtnPayment" class="px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200">
                    <span>💸</span>
                    <span>إصدار سند صرف (معلم / مصروفات)</span>
                </button>
                <button type="button" onclick="switchVoucherTab('entitlement')" id="tabBtnEntitlement" class="px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200">
                    <span>💼</span>
                    <span>إثبات مستحق للمعلم (قيد دائن)</span>
                </button>
            </div>
            <span class="text-xs text-slate-400 hidden sm:inline">قيود وسندات مرقمة آلياً ومفقطة باللغة العربية</span>
        </div>

        <!-- 1. نموذج إصدار سند قبض -->
        <div id="receiptFormContainer">
            <!-- مفتاح التبديل بين السند الفردي والسند المركب الجماعي -->
            <div class="flex items-center gap-2 mb-4 p-1 bg-slate-100 rounded-xl max-w-fit">
                <button type="button" id="receiptModeSingleBtn" onclick="setReceiptMode('single')" class="px-3.5 py-1.5 rounded-lg font-black text-xs transition bg-white text-emerald-800 shadow-xs flex items-center gap-1.5">
                    <span>👤</span>
                    <span>سند قبض فردي (طالب واحد)</span>
                </button>
                <button type="button" id="receiptModeBatchBtn" onclick="setReceiptMode('batch')" class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
                    <span>👥</span>
                    <span>سند قبض جماعي مركب (ماستر وديتيل - عدة طلاب)</span>
                </button>
            </div>

            <form action="{{ route('accounting.receipt.store') }}" method="POST" id="receiptForm" onsubmit="return validateReceiptSubmit(event)" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="is_compound" id="receiptIsCompoundInput" value="0">

                <!-- الحقول الأساسية المشتركة / الماستر -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- حقل اختيار الطالب للسند الفردي -->
                    <div id="receiptSingleStudentWrapper">
                        <label class="block font-bold text-slate-700 mb-1">اسم الطالب المسدد <span class="text-red-500">*</span></label>
                        <select name="student_id" id="receiptSingleStudentSelect" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-emerald-500 outline-none bg-white font-bold">
                            <option value="">-- اختر الطالب المسدد --</option>
                            @foreach($studentsAccounts as $acc)
                                <option value="{{ $acc->user?->id }}">{{ $acc->user?->name }} (المتبقي: {{ $acc->formatted_balance }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- الصندوق / البنك المستلم -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الصندوق / البنك المستلم <span class="text-emerald-600">*</span></label>
                        <select name="cash_or_bank_account_id" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-emerald-500 outline-none bg-white font-bold text-slate-800">
                            @foreach($receiptBoxes as $box)
                                <option value="{{ $box->id }}">
                                    [{{ $box->account_code }}] {{ $box->account_name }} ({{ $box->box_category_label }} - {{ $box->box_nature_label }}) - رصيد: {{ number_format($box->current_balance, 2) }} {{ $box->currency }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- إجمالي مبلغ السند (الماستر) -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="font-bold text-slate-700" id="receiptAmountLabel">المبلغ المقبوض <span class="text-red-500">*</span></label>
                            <span id="receiptBatchTotalBadge" class="hidden text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">إجمالي الماستر الرئيسي</span>
                        </div>
                        <input type="number" step="0.01" name="amount" id="receiptMasterAmount" required placeholder="مثال: 20000" oninput="recalcReceiptBatch()"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-emerald-500 outline-none font-mono font-bold text-slate-900 text-sm">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">العملة الافتراضية</label>
                        <select name="currency" id="receiptCurrency" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-emerald-500 outline-none font-bold bg-white">
                            <option value="YER" selected>🇾🇪 ريال يمني (YER)</option>
                            <option value="SAR">🇸🇦 ريال سعودي (SAR)</option>
                            <option value="USD">💵 دولار أمريكي (USD)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الرقم الدفتري اليدوي (اختياري)</label>
                        <input type="text" name="manual_no" placeholder="مطابق لدفتر السندات الورقي" 
                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-emerald-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">طريقة السداد <span class="text-red-500">*</span></label>
                        <select name="payment_method" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-emerald-500 outline-none bg-white">
                            <option value="cash">نقداً عبر الصندوق</option>
                            <option value="bank_transfer">إيداع أو حوالة بنكية</option>
                            <option value="electronic_wallet">محفظة إلكترونية (جوالي/ون كاش/كريمي)</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="block font-bold text-slate-700 mb-1">البيان العام للسند <span class="text-red-500">*</span></label>
                        <input type="text" name="statement" id="receiptStatement" required placeholder="مثال: تحصيل أقساط دورة اللغة الإنجليزية" 
                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-emerald-500 outline-none">
                    </div>
                </div>

                <!-- قسم الماستر والديتيل (يظهر فقط في الوضع المركب / الجماعي) -->
                <div id="receiptBatchContainer" class="hidden space-y-3 pt-4 border-t border-slate-200">
                    <!-- شريط المطابقة اللحظي الذكي بين إجمالي الماستر والديتيل -->
                    <div id="receiptReconcileBar" class="p-4 rounded-2xl border transition bg-amber-50/60 border-amber-300">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div>
                                <span class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                                    <span>⚖️</span>
                                    <span>مطابقة الماستر والديتيل (Master vs Details Verification):</span>
                                </span>
                                <div class="flex items-center gap-4 mt-1 font-mono text-xs">
                                    <span>إجمالي الماستر: <strong id="dispReceiptMaster">0.00</strong></span>
                                    <span>مجموع الديتيل: <strong id="dispReceiptDetail">0.00</strong></span>
                                    <span>الفارق: <strong id="dispReceiptDiff" class="text-amber-700">0.00</strong></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span id="receiptMatchStatusBadge" class="px-3 py-1 rounded-full text-xs font-black bg-amber-200 text-amber-900 border border-amber-300">
                                    ⚠️ غير متطابق
                                </span>
                                <button type="button" onclick="autoMatchReceiptMaster()" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition text-xs shadow-xs" title="تحديث إجمالي الماستر ليكون مطابقاً تماماً لمجموع بنود الطلاب">
                                    ⚡ مطابقة الماستر مع البنود
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- جدول أسطر الديتيل للطلاب -->
                    <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                        <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-200 flex justify-between items-center">
                            <span class="font-black text-slate-800 text-xs">بنود الطلاب التفصيلية (Detail Rows)</span>
                            <button type="button" onclick="addReceiptRow()" class="px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-bold rounded-lg transition text-xs flex items-center gap-1 shadow-2xs">
                                <span>➕ إضافة طالب آخر للسند</span>
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-right text-xs">
                                <thead class="bg-slate-100 text-slate-700 font-bold">
                                    <tr>
                                        <th class="p-2.5 w-12 text-center">م</th>
                                        <th class="p-2.5 min-w-[220px]">اسم الطالب المسدد</th>
                                        <th class="p-2.5 w-36">المبلغ</th>
                                        <th class="p-2.5 min-w-[200px]">البيان التفصيلي الخاص بالطالب (اختياري)</th>
                                        <th class="p-2.5 w-14 text-center">إجراء</th>
                                    </tr>
                                </thead>
                                <tbody id="receiptRowsTbody" class="divide-y divide-slate-100">
                                    <!-- سيتم إدراج الأسطر هنا بالـ JS أو الافتراضي -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pt-3 border-t border-slate-100">
                    <span class="text-xs text-emerald-700 font-bold flex items-center gap-1">
                        <span>⚡</span>
                        <span id="receiptFooterNote">سيتم إرسال إشعار فوري مع سند القبض إلى واتساب الطالب وترحيل الحساب آلياً.</span>
                    </span>
                    <button type="submit" id="receiptSubmitBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-md transition flex items-center gap-2">
                        <span>🚀</span>
                        <span id="receiptSubmitText">حفظ سند القبض وترحيل الحساب</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. نموذج إصدار سند صرف (للمعلم أو المصروفات) -->
        <div id="paymentFormContainer" class="hidden">
            <!-- مفتاح التبديل بين السند الفردي والسند المركب الجماعي -->
            <div class="flex items-center gap-2 mb-4 p-1 bg-slate-100 rounded-xl max-w-fit">
                <button type="button" id="paymentModeSingleBtn" onclick="setPaymentMode('single')" class="px-3.5 py-1.5 rounded-lg font-black text-xs transition bg-white text-rose-800 shadow-xs flex items-center gap-1.5">
                    <span>👤</span>
                    <span>سند صرف فردي (مستفيد واحد)</span>
                </button>
                <button type="button" id="paymentModeBatchBtn" onclick="setPaymentMode('batch')" class="px-3.5 py-1.5 rounded-lg font-bold text-xs transition text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
                    <span>👥</span>
                    <span>سند صرف جماعي مركب (ماستر وديتيل - عدة مستفيدين)</span>
                </button>
            </div>

            <form action="{{ route('accounting.payment.store') }}" method="POST" id="paymentForm" onsubmit="return validatePaymentSubmit(event)" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="is_compound" id="paymentIsCompoundInput" value="0">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- حقل اختيار المعلم للسند الفردي -->
                    <div id="paymentSingleTeacherWrapper">
                        <label class="block font-bold text-slate-700 mb-1">المستفيد من الصرف (معلم / كادر) <span class="text-red-500">*</span></label>
                        <select name="teacher_id" id="paymentSingleTeacherSelect" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-red-500 outline-none bg-white font-bold">
                            <option value="">-- اختر المستفيد (معلم أو مشرف أو كادر) --</option>
                            @foreach($teachersAccounts as $acc)
                                @php
                                    $roleIcon = match($acc->user?->role) {
                                        'supervisor' => '🛡️ مشرف أكاديمي',
                                        'teacher' => '👨‍🏫 معلم',
                                        'admin' => '👑 إدارة',
                                        'accountant' => '💼 محاسب',
                                        default => 'كادر'
                                    };
                                @endphp
                                <option value="{{ $acc->user?->id }}">{{ $acc->user?->name }} [{{ $roleIcon }}] (المستحق له: {{ $acc->formatted_balance }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- الصندوق / البنك الصارف -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الصندوق / البنك الصارف <span class="text-rose-600">*</span></label>
                        <select name="cash_or_bank_account_id" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-red-500 outline-none bg-white font-bold text-slate-800">
                            @foreach($paymentBoxes as $box)
                                <option value="{{ $box->id }}">
                                    [{{ $box->account_code }}] {{ $box->account_name }} ({{ $box->box_category_label }} - {{ $box->box_nature_label }}) - رصيد: {{ number_format($box->current_balance, 2) }} {{ $box->currency }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- إجمالي مبلغ الصرف الماستر -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="font-bold text-slate-700" id="paymentAmountLabel">المبلغ المصروف <span class="text-red-500">*</span></label>
                            <span id="paymentBatchTotalBadge" class="hidden text-[10px] font-mono font-bold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded">إجمالي الماستر الرئيسي</span>
                        </div>
                        <input type="number" step="0.01" name="amount" id="paymentMasterAmount" required placeholder="مثال: 50000" oninput="recalcPaymentBatch()"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-red-500 outline-none font-mono font-bold text-slate-900 text-sm">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">العملة الافتراضية</label>
                        <select name="currency" id="paymentCurrency" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-red-500 outline-none font-bold bg-white">
                            <option value="YER" selected>🇾🇪 ريال يمني (YER)</option>
                            <option value="SAR">🇸🇦 ريال سعودي (SAR)</option>
                            <option value="USD">💵 دولار أمريكي (USD)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الرقم الدفتري اليدوي (اختياري)</label>
                        <input type="text" name="manual_no" placeholder="مطابق لدفتر سندات الصرف الورقي" 
                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-red-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">طريقة الصرف <span class="text-red-500">*</span></label>
                        <select name="payment_method" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-red-500 outline-none bg-white">
                            <option value="cash">نقداً من الصندوق</option>
                            <option value="bank_transfer">حوالة مصرفية / إيداع بنكي</option>
                            <option value="electronic_wallet">محفظة إلكترونية (جوالي/ون كاش/كريمي)</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="block font-bold text-slate-700 mb-1">البيان العام لسند الصرف <span class="text-red-500">*</span></label>
                        <input type="text" name="statement" id="paymentStatement" required placeholder="مثال: صرف مستحقات الحصص المنفذة لشهر أكتوبر" 
                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-red-500 outline-none">
                    </div>
                </div>

                <!-- قسم الماستر والديتيل للصرف -->
                <div id="paymentBatchContainer" class="hidden space-y-3 pt-4 border-t border-slate-200">
                    <div id="paymentReconcileBar" class="p-4 rounded-2xl border transition bg-amber-50/60 border-amber-300">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div>
                                <span class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                                    <span>⚖️</span>
                                    <span>مطابقة الماستر والديتيل للصرف (Payment Verification):</span>
                                </span>
                                <div class="flex items-center gap-4 mt-1 font-mono text-xs">
                                    <span>إجمالي الماستر الصارف: <strong id="dispPaymentMaster">0.00</strong></span>
                                    <span>مجموع المستفيدين: <strong id="dispPaymentDetail">0.00</strong></span>
                                    <span>الفارق: <strong id="dispPaymentDiff" class="text-amber-700">0.00</strong></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span id="paymentMatchStatusBadge" class="px-3 py-1 rounded-full text-xs font-black bg-amber-200 text-amber-900 border border-amber-300">
                                    ⚠️ غير متطابق
                                </span>
                                <button type="button" onclick="autoMatchPaymentMaster()" class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg transition text-xs shadow-xs" title="تحديث إجمالي الماستر ليكون مطابقاً تماماً لمجموع بنود الصرف">
                                    ⚡ مطابقة الماستر مع البنود
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                        <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-200 flex justify-between items-center">
                            <span class="font-black text-slate-800 text-xs">بنود المستفيدين التفصيلية (Detail Rows)</span>
                            <button type="button" onclick="addPaymentRow()" class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-300 font-bold rounded-lg transition text-xs flex items-center gap-1 shadow-2xs">
                                <span>➕ إضافة مستفيد آخر لسند الصرف</span>
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-right text-xs">
                                <thead class="bg-slate-100 text-slate-700 font-bold">
                                    <tr>
                                        <th class="p-2.5 w-12 text-center">م</th>
                                        <th class="p-2.5 min-w-[220px]">المستفيد من الصرف</th>
                                        <th class="p-2.5 w-36">المبلغ</th>
                                        <th class="p-2.5 min-w-[200px]">البيان التفصيلي الخاص بالمستفيد (اختياري)</th>
                                        <th class="p-2.5 w-14 text-center">إجراء</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentRowsTbody" class="divide-y divide-slate-100">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pt-3 border-t border-slate-100">
                    <span class="text-xs text-red-700 font-bold flex items-center gap-1">
                        <span>⚡</span>
                        <span id="paymentFooterNote">سيتم إنقاص رصيد الصندوق وقيد المبلغ في حساب المعلم وإرسال إشعار واتساب له.</span>
                    </span>
                    <button type="submit" id="paymentSubmitBtn" class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-md transition flex items-center gap-2">
                        <span>💸</span>
                        <span id="paymentSubmitText">حفظ سند الصرف وخصم المبلغ من الصندوق</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. نموذج إثبات مستحق للمعلم (قيد استحقاق مالي دائن) -->
        <div id="entitlementFormContainer" class="hidden">
            <form action="{{ route('accounting.teacher_entitlement.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 mb-1">المعلم / المدرب / المشرف المستحق <span class="text-rose-500">*</span></label>
                    <select name="teacher_id" id="entTeacherSelect" onchange="onEntTeacherChange()" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="">-- اختر المستحق (معلم / مشرف / موظف) --</option>
                        @foreach($teachersWithContracts as $t)
                            @php
                                $contract = $t->teacherContract;
                                $acc = $t->financialAccount;
                                $roleIcon = match($t->role) {
                                    'supervisor' => '🛡️ مشرف أكاديمي',
                                    'admin' => '👑 إدارة',
                                    'accountant' => '💼 محاسب',
                                    'staff' => '👔 موظف',
                                    default => '👨‍🏫 معلم'
                                };
                            @endphp
                            <option value="{{ $t->id }}" 
                                data-contract-type="{{ $contract?->contract_type ?? 'per_course' }}"
                                data-salary="{{ $contract?->monthly_salary ?? 0 }}"
                                data-hourly="{{ $contract?->default_hourly_rate ?? 0 }}"
                                data-currency="{{ $contract?->currency ?? 'YER' }}">
                                [{{ $roleIcon }}] {{ $t->name }} 
                                ({{ $contract?->contract_type === 'fixed_salary' ? 'راتب ثابت: ' . number_format($contract->monthly_salary) . ' ' . ($contract->currency ?? 'YER') : 'متعاقد بالدورات والخدمات' }})
                                - الرصيد: {{ number_format($acc?->current_balance ?? 0) }} {{ $contract?->currency ?? 'YER' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">نوع المستحق المالي <span class="text-rose-500">*</span></label>
                    <select name="entitlement_type" id="entTypeSelect" onchange="onEntTypeChange()" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-indigo-500 outline-none font-bold bg-white">
                        <option value="course_teaching">📚 أجر تدريس مجموعة / دورة</option>
                        <option value="fixed_salary">💼 راتب شهري ثابت للموظف</option>
                        <option value="exam_prep">📝 إعداد اختبار وبنك أسئلة</option>
                        <option value="grading">📊 تصحيح ومراجعة كشوفات درجات</option>
                        <option value="supervision">👥 إشراف أكاديمي وتنسيق مجموعات</option>
                        <option value="activity">🎯 ورشة عمل تدريبية / نشاط</option>
                        <option value="custom_service">⚙️ خدمة تعليمية من الدليل</option>
                        <option value="bonus">⭐ مكافأة تميز وبدل إنجاز</option>
                    </select>
                </div>

                <!-- اختيار المجموعة الدراسية -->
                <div id="entGroupWrapper">
                    <label class="block font-bold text-slate-700 mb-1">المجموعة الدراسية (اختياري)</label>
                    <select name="study_group_id" id="entGroupSelect" onchange="onEntGroupChange()" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-indigo-500 outline-none bg-white">
                        <option value="">-- غير مرتبطة بمجموعة محددة --</option>
                        @foreach($allStudyGroups as $grp)
                            @php
                                $primaryTeacher = $grp->teacher ?: ($grp->teachers->where('pivot.is_primary', true)->first() ?: $grp->teachers->first());
                                $course = $grp->course;
                            @endphp
                            <option value="{{ $grp->id }}" 
                                data-teacher-id="{{ $primaryTeacher?->id }}"
                                data-teacher-name="{{ $primaryTeacher?->name }}"
                                data-course-id="{{ $grp->course_id }}"
                                data-course-title="{{ $course?->title }}"
                                data-teacher-fee="{{ $course?->teacher_fee ?? 0 }}"
                                data-hourly-rate="{{ $course?->teacher_hourly_rate ?? 0 }}"
                                data-currency="{{ $course?->currency ?? 'YER' }}">
                                {{ $grp->name }} - {{ $course?->title }} 
                                @if($primaryTeacher)
                                    (المعلم الرئيسي: ⭐ {{ $primaryTeacher->name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- اختيار الدورة التدريبية -->
                <div id="entCourseWrapper">
                    <label class="block font-bold text-slate-700 mb-1">الدورة التدريبية (اختياري)</label>
                    <select name="course_id" id="entCourseSelect" onchange="onEntCourseChange()" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-indigo-500 outline-none bg-white">
                        <option value="">-- غير مرتبطة بدورة محددة --</option>
                        @foreach($allCourses as $c)
                            <option value="{{ $c->id }}" 
                                data-teacher-fee="{{ $c->teacher_fee ?? 0 }}"
                                data-hourly-rate="{{ $c->teacher_hourly_rate ?? 0 }}"
                                data-currency="{{ $c->currency ?? 'YER' }}">
                                {{ $c->title }} (أجر المدرس: {{ number_format($c->teacher_fee ?? 0) }} {{ $c->currency }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- اختيار خدمة تعليمية إضافية -->
                <div id="entServiceWrapper" class="hidden">
                    <label class="block font-bold text-slate-700 mb-1">الخدمة التعليمية المعتمدة</label>
                    <select name="educational_service_id" id="entServiceSelect" onchange="onEntServiceChange()" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-indigo-500 outline-none bg-white">
                        <option value="">-- اختر من دليل الخدمات التعليمية --</option>
                        @foreach($educationalServices as $srv)
                            <option value="{{ $srv->id }}" 
                                data-fee="{{ $srv->default_fee }}"
                                data-currency="{{ $srv->currency }}"
                                data-name="{{ $srv->name }}">
                                [{{ $srv->service_code }}] {{ $srv->name }} ({{ number_format($srv->default_fee) }} {{ $srv->currency }} - {{ $srv->unit_type_label }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-indigo-900 mb-1">المبلغ المستحق <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="amount" id="entAmount" required placeholder="مثال: 35000" 
                        class="w-full border border-indigo-300 bg-indigo-50/20 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-indigo-500 outline-none font-mono font-bold text-indigo-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">العملة الافتراضية <span class="text-rose-500">*</span></label>
                    <select name="currency" id="entCurrency" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-indigo-500 outline-none font-bold bg-white">
                        <option value="YER" selected>🇾🇪 ريال يمني (YER)</option>
                        <option value="SAR">🇸🇦 ريال سعودي (SAR)</option>
                        <option value="USD">💵 دولار أمريكي (USD)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ الاستحقاق <span class="text-rose-500">*</span></label>
                    <input type="date" name="due_date" value="{{ date('Y-m-d') }}" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-indigo-500 outline-none font-mono bg-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الشهر / الفترة المحاسبية</label>
                    <input type="text" name="period" id="entPeriod" value="{{ date('Y-m') }}" placeholder="مثال: 2026-10" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-indigo-500 outline-none font-mono bg-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الرقم الدفتري اليدوي (اختياري)</label>
                    <input type="text" name="manual_no" placeholder="مطابق لدفتر قيود الاستحقاق" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-1 focus:ring-indigo-500 outline-none font-mono">
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block font-bold text-slate-700 mb-1">البيان والشرح التفصيلي للاستحقاق <span class="text-rose-500">*</span></label>
                    <input type="text" name="statement" id="entStatement" required placeholder="مثال: إثبات استحقاق تدريس دورة لغة إنجليزية مستوى مبتدئ للمجموعة A1 لشهر أكتوبر" 
                        class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 focus:ring-1 focus:ring-indigo-500 outline-none">
                </div>

                <div class="sm:col-span-2 lg:col-span-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pt-3 border-t border-slate-100">
                    <span class="text-xs text-indigo-700 font-bold flex items-center gap-1.5">
                        <span>⚡</span>
                        <span>سيتم قيد المبلغ تلقائياً كاستحقاق دائن للمعلم (له)، وتحديث رصيد المطالبة، وإشعار المعلم عبر الواتساب.</span>
                    </span>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-md transition flex items-center gap-2">
                        <span>💼</span>
                        <span>إثبات المستحق المالي وترحيل الحساب</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- قوالب إضافة الأسطر ديناميكياً (HTML Templates) -->
    <template id="receiptRowTemplate">
        <tr class="receipt-item-row hover:bg-slate-50 transition border-b border-slate-100">
            <td class="p-2.5 text-center font-mono font-bold text-slate-500 row-number"></td>
            <td class="p-2.5">
                <select name="items[INDEX][student_id]" required onchange="recalcReceiptBatch()" class="w-full border border-slate-300 rounded-xl px-2.5 py-1.5 focus:ring-1 focus:ring-emerald-500 outline-none bg-white text-xs font-bold">
                    <option value="">-- اختر الطالب المسدد --</option>
                    @foreach($studentsAccounts as $acc)
                        <option value="{{ $acc->user?->id }}">{{ $acc->user?->name }} (المتبقي: {{ $acc->formatted_balance }})</option>
                    @endforeach
                </select>
            </td>
            <td class="p-2.5">
                <input type="number" step="0.01" min="0.01" name="items[INDEX][amount]" required placeholder="مثال: 5000" oninput="recalcReceiptBatch()" class="w-full border border-slate-300 rounded-xl px-2.5 py-1.5 focus:ring-1 focus:ring-emerald-500 outline-none font-mono text-xs font-bold text-emerald-700 row-amount">
            </td>
            <td class="p-2.5">
                <input type="text" name="items[INDEX][statement]" placeholder="بيان تفصيلي خاص بالطالب (اختياري)" class="w-full border border-slate-300 rounded-xl px-2.5 py-1.5 focus:ring-1 focus:ring-emerald-500 outline-none text-xs">
            </td>
            <td class="p-2.5 text-center">
                <button type="button" onclick="removeReceiptRow(this)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="حذف السطر">
                    ✕
                </button>
            </td>
        </tr>
    </template>

    <template id="paymentRowTemplate">
        <tr class="payment-item-row hover:bg-slate-50 transition border-b border-slate-100">
            <td class="p-2.5 text-center font-mono font-bold text-slate-500 row-number"></td>
            <td class="p-2.5">
                <select name="items[INDEX][teacher_id]" required onchange="recalcPaymentBatch()" class="w-full border border-slate-300 rounded-xl px-2.5 py-1.5 focus:ring-1 focus:ring-rose-500 outline-none bg-white text-xs font-bold">
                    <option value="">-- اختر المستفيد (معلم أو كادر) --</option>
                    @foreach($teachersAccounts as $acc)
                        @php
                            $roleIcon = match($acc->user?->role) {
                                'supervisor' => '🛡️ مشرف',
                                'teacher' => '👨‍🏫 معلم',
                                'admin' => '👑 إدارة',
                                'accountant' => '💼 محاسب',
                                default => 'كادر'
                            };
                        @endphp
                        <option value="{{ $acc->user?->id }}">{{ $acc->user?->name }} [{{ $roleIcon }}] (المستحق له: {{ $acc->formatted_balance }})</option>
                    @endforeach
                </select>
            </td>
            <td class="p-2.5">
                <input type="number" step="0.01" min="0.01" name="items[INDEX][amount]" required placeholder="مثال: 5000" oninput="recalcPaymentBatch()" class="w-full border border-slate-300 rounded-xl px-2.5 py-1.5 focus:ring-1 focus:ring-rose-500 outline-none font-mono text-xs font-bold text-rose-700 row-amount">
            </td>
            <td class="p-2.5">
                <input type="text" name="items[INDEX][statement]" placeholder="بيان تفصيلي خاص بالمستفيد (اختياري)" class="w-full border border-slate-300 rounded-xl px-2.5 py-1.5 focus:ring-1 focus:ring-rose-500 outline-none text-xs">
            </td>
            <td class="p-2.5 text-center">
                <button type="button" onclick="removePaymentRow(this)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="حذف السطر">
                    ✕
                </button>
            </td>
        </tr>
    </template>

    <!-- جدول السندات الأخيرة مع التعديل والحذف والطباعة -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="text-base font-black text-slate-900">سجل سندات القبض والصرف الحديثة</h3>
                <p class="text-xs text-slate-500">إمكانية طباعة السند، تعديل بياناته، أو حذفه وإلغاء أثره المالي بنقرة واحدة.</p>
            </div>
            <span class="text-xs font-mono font-bold text-slate-400">آخر 15 سند مسجل</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-3.5">رقم السند الآلي</th>
                        <th class="p-3.5">الرقم الدفتري</th>
                        <th class="p-3.5">النوع</th>
                        <th class="p-3.5">الصندوق / البنك</th>
                        <th class="p-3.5">الطرف المستلم/المسدد</th>
                        <th class="p-3.5">المبلغ والعملة</th>
                        <th class="p-3.5">البيان</th>
                        <th class="p-3.5">التاريخ</th>
                        <th class="p-3.5 text-center">التحكم والإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($vouchers as $voucher)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3.5 font-mono font-bold text-indigo-700">
                                {{ $voucher->voucher_no }}
                                @if($voucher->is_compound)
                                    <span class="inline-block mr-1 text-[9px] bg-purple-100 text-purple-800 px-1.5 py-0.5 rounded font-black">ماستر/ديتيل ({{ $voucher->items->count() }})</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono text-slate-500">{{ $voucher->manual_no ?? '-' }}</td>
                            <td class="p-3.5">
                                @if($voucher->voucher_type === 'receipt')
                                    <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded font-bold">سند قبض</span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded font-bold">سند صرف</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($voucher->cashOrBankAccount)
                                    <a href="{{ route('accounting.cash_boxes.statement', $voucher->cashOrBankAccount->id) }}" class="font-bold text-slate-800 hover:text-emerald-700 hover:underline flex items-center gap-1" title="كشف حركة الصندوق / الحساب">
                                        <span>{{ $voucher->cashOrBankAccount->account_type === 'bank' ? '🏦' : '💼' }}</span>
                                        <span class="truncate max-w-[120px]">{{ $voucher->cashOrBankAccount->account_name }}</span>
                                    </a>
                                    <span class="block text-[10px] text-slate-400 font-mono">{{ $voucher->cashOrBankAccount->account_code }}</span>
                                @else
                                    <span class="text-slate-400 font-mono">-</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-bold text-slate-900">
                                @if($voucher->is_compound && $voucher->items->count() > 1)
                                    <span>{{ $voucher->account?->user?->name ?? $voucher->account?->account_name }}</span>
                                    <span class="text-[10px] text-purple-700 block font-normal">وآخرون ({{ $voucher->items->count() }} أطراف)</span>
                                @else
                                    {{ $voucher->account?->user?->name ?? $voucher->account?->account_name }}
                                @endif
                            </td>
                            <td class="p-3.5 font-mono font-bold text-slate-900">{{ $voucher->formatted_amount }}</td>
                            <td class="p-3.5 text-slate-600 max-w-xs truncate">{{ $voucher->statement }}</td>
                            <td class="p-3.5 text-slate-400 font-mono">{{ $voucher->created_at->format('Y-m-d') }}</td>
                            <td class="p-3.5">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    @php
                                        $vUser = $voucher->account?->user;
                                        $vRawPh = $vUser?->phone_whatsapp ?: $vUser?->phone;
                                        $vCleanPh = preg_replace('/[^0-9]/', '', $vRawPh ?? '');
                                        $vCur = $voucher->currency_symbol ?? 'ر.ي';
                                        $vType = $voucher->voucher_type === 'receipt' ? 'قبض' : 'صرف';
                                        $vMsg = "مرحباً " . ($vUser?->name ?? $voucher->account?->account_name) . "،\nتم إصدار سند " . $vType . " رسمي برقم (" . $voucher->voucher_no . ") بمبلغ " . number_format($voucher->amount, 2) . " " . $vCur . ".\nرابط السند المعتمد بصيغة PDF:\n" . route('accounting.voucher.print', $voucher->id);
                                        $vWaLink = $vCleanPh ? "https://wa.me/{$vCleanPh}?text=" . urlencode($vMsg) : "https://wa.me/?text=" . urlencode($vMsg);
                                    @endphp

                                    <!-- إرسال واتساب -->
                                    <a href="{{ $vWaLink }}" target="_blank"
                                        class="px-2 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-lg transition text-[11px] flex items-center gap-1" title="إرسال إشعار السند ورابط PDF عبر الواتساب">
                                        <span>💬</span>
                                        <span>واتساب</span>
                                    </a>

                                    <!-- طباعة -->
                                    <a href="{{ route('accounting.voucher.print', $voucher->id) }}" target="_blank"
                                        class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition" title="طباعة">
                                        🖨️ طباعة
                                    </a>

                                    <!-- استعراض القيد المحاسبي والأثر المالي -->
                                    <button type="button" onclick="openJournalEntryModal({{ $voucher->id }})"
                                        class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 font-bold rounded-lg transition text-[11px] flex items-center gap-1 shadow-2xs" title="استعراض القيد المحاسبي والأثر المالي وفحص الاتزان">
                                        <span>⚖️</span>
                                        <span>القيد المحاسبي</span>
                                    </button>

                                    <!-- تعديل -->
                                    <button type="button" onclick='openEditVoucherModal(@json($voucher))' 
                                        class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition" title="تعديل السند">
                                        ✏️ تعديل
                                    </button>

                                    <!-- حذف / إلغاء -->
                                    <form action="{{ route('accounting.voucher.destroy', $voucher->id) }}" method="POST" class="inline" onsubmit="return confirm('تحذير: هل أنت متأكد من إلغاء وحذف هذا السند؟ سيتم عكس الأثر المالي تلقائياً وضبط أرصدة الصندوق والحساب!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition" title="حذف السند">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-6 text-center text-slate-400">لا توجد سندات مسجلة حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- جدول قيود إثبات مستحقات المعلمين مع الطباعة والإلغاء وكشف الحساب -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden" id="entitlementsSection">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[11px] font-black rounded border border-indigo-200">
                        قيود محاسبية دائنة للمعلمين
                    </span>
                    <h3 class="text-base font-black text-slate-900">سجل قيود إثبات مستحقات المعلمين والمدربين</h3>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    توثيق كافة أجور تدريس الدورات، الرواتب الشهرية، وبنوك الأسئلة والتصحيح، مع إمكانية طباعة إشعار رسمي أو صرف سند نقدي فوراً.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('accounting.pricing.index') }}" class="text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition">
                    🏷️ تسعيرة الدورات والخدمات
                </a>
                <span class="text-xs font-mono font-bold text-slate-400">آخر 20 قيد معتمد</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="p-3.5">رقم القيد الآلي</th>
                        <th class="p-3.5">المعلم المستحق</th>
                        <th class="p-3.5">نوع الاستحقاق</th>
                        <th class="p-3.5">المجموعة / الدورة / الخدمة</th>
                        <th class="p-3.5 text-indigo-700 font-black">المبلغ والعملة</th>
                        <th class="p-3.5">البيان والشرح</th>
                        <th class="p-3.5">التاريخ / الفترة</th>
                        <th class="p-3.5 text-center">الإجراءات والتحكم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($entitlements as $ent)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3.5 font-mono font-bold text-indigo-700 whitespace-nowrap">
                                {{ $ent->entitlement_no }}
                                @if($ent->manual_no)
                                    <span class="block text-[10px] text-slate-400 font-normal">دفتري: {{ $ent->manual_no }}</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <span class="font-black text-slate-900 block">{{ $ent->teacher?->name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $ent->teacher?->email }}</span>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-800 font-bold rounded border border-indigo-200 text-[10px]">
                                    {{ $ent->type_label }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-700">
                                @if($ent->studyGroup)
                                    <span class="font-bold block text-slate-900">{{ $ent->studyGroup->name }}</span>
                                    @if($ent->studyGroup->course)
                                        <span class="text-[10px] text-slate-500 block">{{ $ent->studyGroup->course->title }}</span>
                                    @endif
                                @elseif($ent->course)
                                    <span class="font-bold text-slate-900">{{ $ent->course->title }}</span>
                                @elseif($ent->service)
                                    <span class="font-bold text-indigo-700">{{ $ent->service->name }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono font-black text-indigo-700 text-sm whitespace-nowrap">
                                {{ number_format($ent->amount, 2) }}
                                <span class="text-[11px] font-sans font-normal text-slate-600">{{ $ent->currency }}</span>
                            </td>
                            <td class="p-3.5 text-slate-600 max-w-xs truncate" title="{{ $ent->statement }}">
                                {{ $ent->statement }}
                            </td>
                            <td class="p-3.5 text-slate-500 font-mono whitespace-nowrap text-[11px]">
                                <div>{{ $ent->due_date ? \Carbon\Carbon::parse($ent->due_date)->format('Y-m-d') : $ent->created_at->format('Y-m-d') }}</div>
                                @if($ent->period)
                                    <span class="text-[10px] text-slate-400">فترة: {{ $ent->period }}</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    @php
                                        $eTeacher = $ent->teacher;
                                        $eRawPh = $eTeacher?->phone_whatsapp ?: $eTeacher?->phone;
                                        $eCleanPh = preg_replace('/[^0-9]/', '', $eRawPh ?? '');
                                        $eCur = $ent->currency ?? 'YER';
                                        $eMsg = "أستاذنا الفاضل " . $eTeacher?->name . "،\nتم إثبات وقيد مستحقات مالية بحسابكم برقم (" . $ent->entitlement_no . ") بمبلغ " . number_format($ent->amount, 2) . " " . $eCur . " عن: " . $ent->title . ".\nرابط إشعار القيد المعتمد بصيغة PDF:\n" . route('accounting.teacher_entitlement.print', $ent->id);
                                        $eWaLink = $eCleanPh ? "https://wa.me/{$eCleanPh}?text=" . urlencode($eMsg) : "https://wa.me/?text=" . urlencode($eMsg);
                                    @endphp

                                    <!-- إرسال واتساب -->
                                    <a href="{{ $eWaLink }}" target="_blank"
                                        class="px-2 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-lg transition text-[11px] flex items-center gap-1" title="إرسال إشعار المستحق ورابط PDF عبر الواتساب">
                                        <span>💬</span>
                                        <span>واتساب</span>
                                    </a>

                                    <!-- طباعة الإشعار -->
                                    <a href="{{ route('accounting.teacher_entitlement.print', $ent->id) }}" target="_blank"
                                        class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition text-[11px] flex items-center gap-1" title="طباعة إشعار رسمي">
                                        <span>🖨️</span>
                                        <span>طباعة</span>
                                    </a>

                                    <!-- كشف الحساب -->
                                    <a href="{{ route('accounting.teacher.statement', $ent->teacher_id) }}" target="_blank"
                                        class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition text-[11px] flex items-center gap-1" title="كشف حساب المعلم">
                                        <span>📊</span>
                                        <span>كشف الحساب</span>
                                    </a>

                                    <!-- تعديل القيد والمبلغ -->
                                    <button type="button" 
                                        onclick='openEditEntitlementModal(@json($ent))'
                                        class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold rounded-lg transition text-[11px] flex items-center gap-1" title="تعديل بيانات ومبلغ قيد الاستحقاق">
                                        <span>✏️</span>
                                        <span>تعديل</span>
                                    </button>

                                    <!-- صرف فوري (سند صرف) -->
                                    <button type="button" 
                                        onclick="quickPayTeacher('{{ $ent->teacher_id }}', '{{ $ent->amount }}', 'صرف مستحق رقم {{ $ent->entitlement_no }} - {{ addslashes($ent->title) }}')"
                                        class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-lg transition text-[11px] flex items-center gap-1" title="إصدار سند صرف لهذا المستحق">
                                        <span>💸</span>
                                        <span>صرف</span>
                                    </button>

                                    <!-- إلغاء وحذف القيد -->
                                    <form action="{{ route('accounting.teacher_entitlement.destroy', $ent->id) }}" method="POST" class="inline" onsubmit="return confirm('تحذير: هل أنت متأكد من إلغاء قيد المستحق رقم ({{ $ent->entitlement_no }})؟ سيتم عكس الأثر المالي وإنقاص رصيد مطالبة المعلم تلقائياً!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition" title="إلغاء وحذف القيد">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                لا توجد قيود إثبات مستحقات مسجلة للمعلمين حتى الآن. يمكنك إثبات أول مستحق عبر النموذج أعلاه.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- مودال تعديل بيانات ومبلغ السند المالي -->
<div id="editVoucherModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200 p-6 space-y-5">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h3 class="font-black text-slate-900 text-sm">تعديل بيانات ومبلغ السند المالي</h3>
                <span class="text-[11px] font-mono text-indigo-600 font-bold" id="editVoucherNoTitle"></span>
            </div>
            <button type="button" onclick="closeEditVoucherModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
        </div>

        <form id="editVoucherForm" method="POST" action="" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 mb-1">المبلغ المالي للسند <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <input type="number" step="0.01" min="0.01" name="amount" id="editVoucherAmount" required class="w-full px-3.5 py-2.5 rounded-xl border border-indigo-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono font-bold text-indigo-900 text-sm">
                    <span class="absolute left-3 top-2.5 font-bold text-slate-400 font-mono text-xs" id="editVoucherCurrencyBadge">YER</span>
                </div>
                <span class="text-[10px] text-slate-500 block mt-1">سيتم تلقائياً عكس وضبط الأثر المالي على رصيد الصندوق وحساب الطرف الثاني.</span>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">الصندوق أو الحساب البنكي <span class="text-rose-500">*</span></label>
                <select name="cash_or_bank_account_id" id="editVoucherCashBox" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold text-slate-800">
                    @foreach($allCashBoxes as $box)
                        <option value="{{ $box->id }}">[{{ $box->account_code }}] {{ $box->account_name }} ({{ number_format($box->current_balance, 2) }} {{ $box->currency }})</option>
                    @endforeach
                </select>
                <span class="text-[10px] text-slate-500 block mt-1">في حال تغيير الصندوق سيتم نقل المبلغ بين الصندوقين وتحديث الرصيدين تلقائياً.</span>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">الرقم الدفتري اليدوي (اختياري)</label>
                <input type="text" name="manual_no" id="editVoucherManualNo" placeholder="رقم الدفتر الورقي" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">طريقة الدفع / الصرف</label>
                <select name="payment_method" id="editVoucherMethod" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                    <option value="cash">نقداً عبر الصندوق</option>
                    <option value="bank_transfer">إيداع / حوالة بنكية</option>
                    <option value="electronic_wallet">محفظة إلكترونية (جوالي/ون كاش/كريمي)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">البيان / سبب السند</label>
                <textarea name="statement" id="editVoucherStatement" rows="3" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeEditVoucherModal()" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm">حفظ التعديل 💾</button>
            </div>
        </form>
    </div>
</div>

<!-- مودال تعديل بيانات ومبلغ قيد استحقاق المعلم -->
<div id="editEntitlementModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200 p-6 space-y-5">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h3 class="font-black text-slate-900 text-sm">تعديل قيد استحقاق المعلم</h3>
                <span class="text-[11px] font-mono text-indigo-600 font-bold" id="editEntNoTitle"></span>
            </div>
            <button type="button" onclick="closeEditEntitlementModal()" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
        </div>

        <form id="editEntitlementForm" method="POST" action="" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">المبلغ المالي المستحق <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <input type="number" step="0.01" min="0.01" name="amount" id="editEntAmount" required class="w-full px-3.5 py-2.5 rounded-xl border border-indigo-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono font-bold text-indigo-900 text-sm">
                    <span class="absolute left-3 top-2.5 font-bold text-slate-400 font-mono text-xs" id="editEntCurrencyBadge">YER</span>
                </div>
                <span class="text-[10px] text-slate-500 block mt-1">سيتم تلقائياً تحديث رصيد مطالبة المعلم بفارق المبلغ الجديد.</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ الاستحقاق <span class="text-rose-500">*</span></label>
                    <input type="date" name="due_date" id="editEntDueDate" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الفترة / الشهر</label>
                    <input type="text" name="period" id="editEntPeriod" placeholder="2026-10" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">الرقم الدفتري اليدوي (اختياري)</label>
                <input type="text" name="manual_no" id="editEntManualNo" placeholder="رقم الدفتر الورقي" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">البيان والشرح التفصيلي <span class="text-rose-500">*</span></label>
                <textarea name="statement" id="editEntStatement" rows="3" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeEditEntitlementModal()" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-xl">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm">حفظ التعديل 💾</button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchVoucherTab(tab) {
        const rc = document.getElementById('receiptFormContainer');
        const pc = document.getElementById('paymentFormContainer');
        const ec = document.getElementById('entitlementFormContainer');

        const btnR = document.getElementById('tabBtnReceipt');
        const btnP = document.getElementById('tabBtnPayment');
        const btnE = document.getElementById('tabBtnEntitlement');

        rc.classList.add('hidden');
        pc.classList.add('hidden');
        ec.classList.add('hidden');

        btnR.className = "px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200";
        btnP.className = "px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200";
        btnE.className = "px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center gap-2 bg-slate-100 text-slate-600 hover:bg-slate-200";

        if (tab === 'receipt') {
            rc.classList.remove('hidden');
            btnR.className = "px-5 py-2.5 rounded-xl font-black text-xs transition flex items-center gap-2 bg-emerald-600 text-white shadow-sm";
        } else if (tab === 'payment') {
            pc.classList.remove('hidden');
            btnP.className = "px-5 py-2.5 rounded-xl font-black text-xs transition flex items-center gap-2 bg-red-600 text-white shadow-sm";
        } else if (tab === 'entitlement') {
            ec.classList.remove('hidden');
            btnE.className = "px-5 py-2.5 rounded-xl font-black text-xs transition flex items-center gap-2 bg-indigo-600 text-white shadow-sm";
        }
    }

    function quickPayTeacher(teacherId, amount, statement) {
        switchVoucherTab('payment');
        const teacherSelect = document.querySelector('select[name="teacher_id"]');
        if (teacherSelect) {
            teacherSelect.value = teacherId;
        }
        const amountInput = document.querySelector('#paymentFormContainer input[name="amount"]');
        if (amountInput) {
            amountInput.value = amount;
        }
        const stmtInput = document.querySelector('#paymentFormContainer input[name="statement"]');
        if (stmtInput) {
            stmtInput.value = statement;
        }
        window.scrollTo({ top: document.getElementById('paymentFormContainer').offsetTop - 100, behavior: 'smooth' });
    }

    // Dynamic Entitlement Form Handlers
    function onEntTeacherChange() {
        const select = document.getElementById('entTeacherSelect');
        const selected = select.options[select.selectedIndex];
        if (!selected || !selected.value) return;

        const contractType = selected.getAttribute('data-contract-type');
        const salary = parseFloat(selected.getAttribute('data-salary') || 0);

        if (contractType === 'fixed_salary') {
            document.getElementById('entTypeSelect').value = 'fixed_salary';
            onEntTypeChange();
        }
    }

    function onEntTypeChange() {
        const type = document.getElementById('entTypeSelect').value;
        const gWrapper = document.getElementById('entGroupWrapper');
        const cWrapper = document.getElementById('entCourseWrapper');
        const sWrapper = document.getElementById('entServiceWrapper');
        const teacherSelect = document.getElementById('entTeacherSelect');
        const selectedTeacher = teacherSelect.options[teacherSelect.selectedIndex];

        if (type === 'fixed_salary') {
            gWrapper.classList.add('hidden');
            cWrapper.classList.add('hidden');
            sWrapper.classList.add('hidden');
            if (selectedTeacher && selectedTeacher.value) {
                const salary = selectedTeacher.getAttribute('data-salary');
                if (salary && parseFloat(salary) > 0) {
                    document.getElementById('entAmount').value = salary;
                }
                const cur = selectedTeacher.getAttribute('data-currency');
                if (cur) document.getElementById('entCurrency').value = cur;
                const period = document.getElementById('entPeriod').value || new Date().toISOString().slice(0, 7);
                document.getElementById('entStatement').value = 'راتب شهري ثابت عن شهر (' + period + ') للأستاذ: ' + selectedTeacher.text.split('(')[0].trim();
            }
        } else if (type === 'custom_service' || type === 'exam_prep' || type === 'grading' || type === 'supervision' || type === 'activity') {
            sWrapper.classList.remove('hidden');
            gWrapper.classList.remove('hidden');
            cWrapper.classList.remove('hidden');
        } else {
            gWrapper.classList.remove('hidden');
            cWrapper.classList.remove('hidden');
            sWrapper.classList.add('hidden');
        }
    }

    function onEntGroupChange() {
        const select = document.getElementById('entGroupSelect');
        const selected = select.options[select.selectedIndex];
        if (!selected || !selected.value) return;

        const teacherId = selected.getAttribute('data-teacher-id');
        const teacherSelect = document.getElementById('entTeacherSelect');
        if (teacherId && (!teacherSelect.value || teacherSelect.value === '')) {
            teacherSelect.value = teacherId;
        }

        const fee = selected.getAttribute('data-teacher-fee');
        if (fee && parseFloat(fee) > 0) {
            document.getElementById('entAmount').value = fee;
        }

        const cur = selected.getAttribute('data-currency');
        if (cur) {
            document.getElementById('entCurrency').value = cur;
        }

        const courseTitle = selected.getAttribute('data-course-title') || '';
        const groupTitle = selected.text.split('-')[0].trim();
        document.getElementById('entStatement').value = 'أجر تدريس مجموعة [' + groupTitle + '] لدورة [' + courseTitle + ']';
    }

    function onEntCourseChange() {
        const select = document.getElementById('entCourseSelect');
        const selected = select.options[select.selectedIndex];
        if (!selected || !selected.value) return;

        const fee = selected.getAttribute('data-teacher-fee');
        if (fee && parseFloat(fee) > 0) {
            document.getElementById('entAmount').value = fee;
        }

        const cur = selected.getAttribute('data-currency');
        if (cur) {
            document.getElementById('entCurrency').value = cur;
        }

        const courseTitle = selected.text.split('(')[0].trim();
        document.getElementById('entStatement').value = 'أجر تدريس دورة [' + courseTitle + ']';
    }

    function onEntServiceChange() {
        const select = document.getElementById('entServiceSelect');
        const selected = select.options[select.selectedIndex];
        if (!selected || !selected.value) return;

        const fee = selected.getAttribute('data-fee');
        if (fee && parseFloat(fee) > 0) {
            document.getElementById('entAmount').value = fee;
        }

        const cur = selected.getAttribute('data-currency');
        if (cur) {
            document.getElementById('entCurrency').value = cur;
        }

        const name = selected.getAttribute('data-name');
        document.getElementById('entStatement').value = 'أتعاب خدمة تعليمية: [' + name + ']';
    }

    // ==========================================
    // إدارة وضع السند الفردي والمركب (ماستر / ديتيل)
    // ==========================================
    let receiptRowCounter = 0;
    let paymentRowCounter = 0;

    function setReceiptMode(mode) {
        const singleBtn = document.getElementById('receiptModeSingleBtn');
        const batchBtn = document.getElementById('receiptModeBatchBtn');
        const singleWrapper = document.getElementById('receiptSingleStudentWrapper');
        const singleSelect = document.getElementById('receiptSingleStudentSelect');
        const batchContainer = document.getElementById('receiptBatchContainer');
        const batchBadge = document.getElementById('receiptBatchTotalBadge');
        const amountLabel = document.getElementById('receiptAmountLabel');
        const isCompoundInput = document.getElementById('receiptIsCompoundInput');
        const footerNote = document.getElementById('receiptFooterNote');
        const submitText = document.getElementById('receiptSubmitText');

        if (mode === 'single') {
            singleBtn.className = "px-3.5 py-1.5 rounded-lg font-black text-xs transition bg-white text-emerald-800 shadow-xs flex items-center gap-1.5";
            batchBtn.className = "px-3.5 py-1.5 rounded-lg font-bold text-xs transition text-slate-600 hover:text-slate-900 flex items-center gap-1.5";
            singleWrapper.classList.remove('hidden');
            singleSelect.required = true;
            singleSelect.disabled = false;
            batchContainer.classList.add('hidden');
            batchBadge.classList.add('hidden');
            amountLabel.innerHTML = 'المبلغ المقبوض <span class="text-red-500">*</span>';
            isCompoundInput.value = "0";
            footerNote.innerText = "سيتم إرسال إشعار فوري مع سند القبض إلى واتساب الطالب وترحيل الحساب آلياً.";
            submitText.innerText = "حفظ سند القبض وترحيل الحساب 🚀";
        } else {
            batchBtn.className = "px-3.5 py-1.5 rounded-lg font-black text-xs transition bg-white text-emerald-800 shadow-xs flex items-center gap-1.5";
            singleBtn.className = "px-3.5 py-1.5 rounded-lg font-bold text-xs transition text-slate-600 hover:text-slate-900 flex items-center gap-1.5";
            singleWrapper.classList.add('hidden');
            singleSelect.required = false;
            singleSelect.disabled = true;
            batchContainer.classList.remove('hidden');
            batchBadge.classList.remove('hidden');
            amountLabel.innerHTML = 'إجمالي السند الماستر <span class="text-red-500">*</span>';
            isCompoundInput.value = "1";
            footerNote.innerText = "سيتم قيد المبالغ في حساب كل طالب على حدة وترحيل إجمالي الماستر إلى الصندوق المالي المختار.";
            submitText.innerText = "حفظ سند القبض الجماعي المركب (ماستر وديتيل) 👥";

            const tbody = document.getElementById('receiptRowsTbody');
            if (tbody.children.length === 0) {
                addReceiptRow();
                addReceiptRow();
            }
            recalcReceiptBatch();
        }
    }

    function addReceiptRow() {
        const tbody = document.getElementById('receiptRowsTbody');
        const template = document.getElementById('receiptRowTemplate').innerHTML;
        const html = template.replace(/INDEX/g, receiptRowCounter++);
        tbody.insertAdjacentHTML('beforeend', html);
        renumberReceiptRows();
        recalcReceiptBatch();
    }

    function removeReceiptRow(btn) {
        const tbody = document.getElementById('receiptRowsTbody');
        if (tbody.children.length <= 1) {
            alert('يجب أن يحتوي السند المركب على بند ديتيل واحد على الأقل.');
            return;
        }
        btn.closest('tr').remove();
        renumberReceiptRows();
        recalcReceiptBatch();
    }

    function renumberReceiptRows() {
        const rows = document.querySelectorAll('#receiptRowsTbody tr');
        rows.forEach((row, i) => {
            const numCell = row.querySelector('.row-number');
            if (numCell) numCell.innerText = i + 1;
        });
    }

    function recalcReceiptBatch() {
        const isCompound = document.getElementById('receiptIsCompoundInput').value === '1';
        if (!isCompound) return;

        const masterInput = document.getElementById('receiptMasterAmount');
        const masterVal = parseFloat(masterInput.value) || 0;

        let detailSum = 0;
        document.querySelectorAll('#receiptRowsTbody .row-amount').forEach(inp => {
            detailSum += parseFloat(inp.value) || 0;
        });
        detailSum = Math.round(detailSum * 100) / 100;

        const diff = Math.round((masterVal - detailSum) * 100) / 100;

        document.getElementById('dispReceiptMaster').innerText = masterVal.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('dispReceiptDetail').innerText = detailSum.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('dispReceiptDiff').innerText = Math.abs(diff).toLocaleString('en-US', {minimumFractionDigits: 2});

        const bar = document.getElementById('receiptReconcileBar');
        const badge = document.getElementById('receiptMatchStatusBadge');
        const diffElem = document.getElementById('dispReceiptDiff');

        if (Math.abs(diff) < 0.01 && masterVal > 0) {
            bar.className = "p-4 rounded-2xl border transition bg-emerald-50/70 border-emerald-300";
            badge.className = "px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300";
            badge.innerText = "✅ متطابق تماماً وموزون (0.00)";
            diffElem.className = "text-emerald-700";
        } else {
            bar.className = "p-4 rounded-2xl border transition bg-amber-50/70 border-amber-300";
            badge.className = "px-3 py-1 rounded-full text-xs font-black bg-amber-200 text-amber-900 border border-amber-300";
            badge.innerText = "⚠️ غير متطابق: فارق (" + Math.abs(diff).toFixed(2) + ")";
            diffElem.className = "text-amber-700";
        }
    }

    function autoMatchReceiptMaster() {
        let detailSum = 0;
        document.querySelectorAll('#receiptRowsTbody .row-amount').forEach(inp => {
            detailSum += parseFloat(inp.value) || 0;
        });
        detailSum = Math.round(detailSum * 100) / 100;
        document.getElementById('receiptMasterAmount').value = detailSum.toFixed(2);
        recalcReceiptBatch();
    }

    function validateReceiptSubmit(e) {
        const isCompound = document.getElementById('receiptIsCompoundInput').value === '1';
        if (isCompound) {
            const masterVal = parseFloat(document.getElementById('receiptMasterAmount').value) || 0;
            if (masterVal <= 0) {
                alert('يرجى إدخال مبلغ إجمالي صحيح لسند القبض الماستر.');
                e.preventDefault();
                return false;
            }

            let detailSum = 0;
            let validCount = 0;
            document.querySelectorAll('#receiptRowsTbody tr').forEach(row => {
                const studentSelect = row.querySelector('select');
                const amtInput = row.querySelector('.row-amount');
                if (studentSelect && studentSelect.value && amtInput && parseFloat(amtInput.value) > 0) {
                    detailSum += parseFloat(amtInput.value);
                    validCount++;
                }
            });

            if (validCount === 0) {
                alert('يرجى تحديد طالب واحد على الأقل مع المبلغ في بنود السند الديتيل.');
                e.preventDefault();
                return false;
            }

            detailSum = Math.round(detailSum * 100) / 100;
            if (Math.abs(masterVal - detailSum) > 0.01) {
                alert('خطأ في المطابقة:\nإجمالي السند الماستر (' + masterVal.toFixed(2) + ') لا يتطابق مع مجموع بنود الطلاب التفصيلية (' + detailSum.toFixed(2) + ')!\nالفارق: ' + Math.abs(masterVal - detailSum).toFixed(2) + '\nيرجى مطابقة المبالغ باستخدام زر "مطابقة الماستر مع البنود".');
                e.preventDefault();
                return false;
            }
        }
        return true;
    }

    // سند الصرف المركب
    function setPaymentMode(mode) {
        const singleBtn = document.getElementById('paymentModeSingleBtn');
        const batchBtn = document.getElementById('paymentModeBatchBtn');
        const singleWrapper = document.getElementById('paymentSingleTeacherWrapper');
        const singleSelect = document.getElementById('paymentSingleTeacherSelect');
        const batchContainer = document.getElementById('paymentBatchContainer');
        const batchBadge = document.getElementById('paymentBatchTotalBadge');
        const amountLabel = document.getElementById('paymentAmountLabel');
        const isCompoundInput = document.getElementById('paymentIsCompoundInput');
        const footerNote = document.getElementById('paymentFooterNote');
        const submitText = document.getElementById('paymentSubmitText');

        if (mode === 'single') {
            singleBtn.className = "px-3.5 py-1.5 rounded-lg font-black text-xs transition bg-white text-rose-800 shadow-xs flex items-center gap-1.5";
            batchBtn.className = "px-3.5 py-1.5 rounded-lg font-bold text-xs transition text-slate-600 hover:text-slate-900 flex items-center gap-1.5";
            singleWrapper.classList.remove('hidden');
            singleSelect.required = true;
            singleSelect.disabled = false;
            batchContainer.classList.add('hidden');
            batchBadge.classList.add('hidden');
            amountLabel.innerHTML = 'المبلغ المصروف <span class="text-red-500">*</span>';
            isCompoundInput.value = "0";
            footerNote.innerText = "سيتم إنقاص رصيد الصندوق وقيد المبلغ في حساب المعلم وإرسال إشعار واتساب له.";
            submitText.innerText = "حفظ سند الصرف وخصم المبلغ من الصندوق 💸";
        } else {
            batchBtn.className = "px-3.5 py-1.5 rounded-lg font-black text-xs transition bg-white text-rose-800 shadow-xs flex items-center gap-1.5";
            singleBtn.className = "px-3.5 py-1.5 rounded-lg font-bold text-xs transition text-slate-600 hover:text-slate-900 flex items-center gap-1.5";
            singleWrapper.classList.add('hidden');
            singleSelect.required = false;
            singleSelect.disabled = true;
            batchContainer.classList.remove('hidden');
            batchBadge.classList.remove('hidden');
            amountLabel.innerHTML = 'إجمالي السند الماستر الصارف <span class="text-red-500">*</span>';
            isCompoundInput.value = "1";
            footerNote.innerText = "سيتم قيد المبالغ في حساب كل معلم/مستفيد على حدة وخصم إجمالي الماستر من الصندوق.";
            submitText.innerText = "حفظ سند الصرف الجماعي المركب (ماستر وديتيل) 👥";

            const tbody = document.getElementById('paymentRowsTbody');
            if (tbody.children.length === 0) {
                addPaymentRow();
                addPaymentRow();
            }
            recalcPaymentBatch();
        }
    }

    function addPaymentRow() {
        const tbody = document.getElementById('paymentRowsTbody');
        const template = document.getElementById('paymentRowTemplate').innerHTML;
        const html = template.replace(/INDEX/g, paymentRowCounter++);
        tbody.insertAdjacentHTML('beforeend', html);
        renumberPaymentRows();
        recalcPaymentBatch();
    }

    function removePaymentRow(btn) {
        const tbody = document.getElementById('paymentRowsTbody');
        if (tbody.children.length <= 1) {
            alert('يجب أن يحتوي السند المركب على بند ديتيل واحد على الأقل.');
            return;
        }
        btn.closest('tr').remove();
        renumberPaymentRows();
        recalcPaymentBatch();
    }

    function renumberPaymentRows() {
        const rows = document.querySelectorAll('#paymentRowsTbody tr');
        rows.forEach((row, i) => {
            const numCell = row.querySelector('.row-number');
            if (numCell) numCell.innerText = i + 1;
        });
    }

    function recalcPaymentBatch() {
        const isCompound = document.getElementById('paymentIsCompoundInput').value === '1';
        if (!isCompound) return;

        const masterInput = document.getElementById('paymentMasterAmount');
        const masterVal = parseFloat(masterInput.value) || 0;

        let detailSum = 0;
        document.querySelectorAll('#paymentRowsTbody .row-amount').forEach(inp => {
            detailSum += parseFloat(inp.value) || 0;
        });
        detailSum = Math.round(detailSum * 100) / 100;

        const diff = Math.round((masterVal - detailSum) * 100) / 100;

        document.getElementById('dispPaymentMaster').innerText = masterVal.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('dispPaymentDetail').innerText = detailSum.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('dispPaymentDiff').innerText = Math.abs(diff).toLocaleString('en-US', {minimumFractionDigits: 2});

        const bar = document.getElementById('paymentReconcileBar');
        const badge = document.getElementById('paymentMatchStatusBadge');
        const diffElem = document.getElementById('dispPaymentDiff');

        if (Math.abs(diff) < 0.01 && masterVal > 0) {
            bar.className = "p-4 rounded-2xl border transition bg-emerald-50/70 border-emerald-300";
            badge.className = "px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300";
            badge.innerText = "✅ متطابق تماماً وموزون (0.00)";
            diffElem.className = "text-emerald-700";
        } else {
            bar.className = "p-4 rounded-2xl border transition bg-amber-50/70 border-amber-300";
            badge.className = "px-3 py-1 rounded-full text-xs font-black bg-amber-200 text-amber-900 border border-amber-300";
            badge.innerText = "⚠️ غير متطابق: فارق (" + Math.abs(diff).toFixed(2) + ")";
            diffElem.className = "text-amber-700";
        }
    }

    function autoMatchPaymentMaster() {
        let detailSum = 0;
        document.querySelectorAll('#paymentRowsTbody .row-amount').forEach(inp => {
            detailSum += parseFloat(inp.value) || 0;
        });
        detailSum = Math.round(detailSum * 100) / 100;
        document.getElementById('paymentMasterAmount').value = detailSum.toFixed(2);
        recalcPaymentBatch();
    }

    function validatePaymentSubmit(e) {
        const isCompound = document.getElementById('paymentIsCompoundInput').value === '1';
        if (isCompound) {
            const masterVal = parseFloat(document.getElementById('paymentMasterAmount').value) || 0;
            if (masterVal <= 0) {
                alert('يرجى إدخال مبلغ إجمالي صحيح لسند الصرف الماستر.');
                e.preventDefault();
                return false;
            }

            let detailSum = 0;
            let validCount = 0;
            document.querySelectorAll('#paymentRowsTbody tr').forEach(row => {
                const teacherSelect = row.querySelector('select');
                const amtInput = row.querySelector('.row-amount');
                if (teacherSelect && teacherSelect.value && amtInput && parseFloat(amtInput.value) > 0) {
                    detailSum += parseFloat(amtInput.value);
                    validCount++;
                }
            });

            if (validCount === 0) {
                alert('يرجى تحديد مستفيد واحد على الأقل مع المبلغ في بنود الصرف الديتيل.');
                e.preventDefault();
                return false;
            }

            detailSum = Math.round(detailSum * 100) / 100;
            if (Math.abs(masterVal - detailSum) > 0.01) {
                alert('خطأ في المطابقة:\nإجمالي السند الماستر الصارف (' + masterVal.toFixed(2) + ') لا يتطابق مع مجموع بنود المستفيدين التفصيلية (' + detailSum.toFixed(2) + ')!\nالفارق: ' + Math.abs(masterVal - detailSum).toFixed(2) + '\nيرجى مطابقة المبالغ باستخدام زر "مطابقة الماستر مع البنود".');
                e.preventDefault();
                return false;
            }
        }
        return true;
    }

    // ==========================================
    // مودالات التعديل المالي للسندات والقيود
    // ==========================================
    function openEditVoucherModal(v) {
        document.getElementById('editVoucherForm').action = '/accounting/voucher/' + v.id + '/update';
        document.getElementById('editVoucherNoTitle').innerText = v.voucher_no + (v.is_compound ? ' (سند مركب ماستر/ديتيل)' : '');
        document.getElementById('editVoucherAmount').value = v.amount;
        document.getElementById('editVoucherCurrencyBadge').innerText = v.currency || 'YER';
        if (document.getElementById('editVoucherCashBox')) {
            document.getElementById('editVoucherCashBox').value = v.cash_or_bank_account_id;
        }
        document.getElementById('editVoucherManualNo').value = v.manual_no || '';
        document.getElementById('editVoucherMethod').value = v.payment_method || 'cash';
        document.getElementById('editVoucherStatement').value = v.statement || '';
        document.getElementById('editVoucherModal').classList.remove('hidden');
    }

    function closeEditVoucherModal() {
        document.getElementById('editVoucherModal').classList.add('hidden');
    }

    function openEditEntitlementModal(ent) {
        document.getElementById('editEntitlementForm').action = '/accounting/teacher-entitlement/' + ent.id + '/update';
        document.getElementById('editEntNoTitle').innerText = ent.entitlement_no;
        document.getElementById('editEntAmount').value = ent.amount;
        document.getElementById('editEntCurrencyBadge').innerText = ent.currency || 'YER';
        document.getElementById('editEntDueDate').value = ent.due_date ? ent.due_date.substring(0, 10) : '';
        document.getElementById('editEntPeriod').value = ent.period || '';
        document.getElementById('editEntManualNo').value = ent.manual_no || '';
        document.getElementById('editEntStatement').value = ent.statement || '';
        document.getElementById('editEntitlementModal').classList.remove('hidden');
    }

    function closeEditEntitlementModal() {
        document.getElementById('editEntitlementModal').classList.add('hidden');
    }

    // فحص الرابط عند الفتح
    if (window.location.hash === '#paymentTab') {
        switchVoucherTab('payment');
    } else if (window.location.hash === '#entitlementsTab' || window.location.hash === '#entitlementTab') {
        switchVoucherTab('entitlement');
    }
</script>

@include('accounting.partials.journal_entry_modal')

@endsection
