@extends('layouts.app', ['title' => 'دورة طلبات القبض والصرف والاعتمادات'])

@section('content')
<div class="space-y-6">

    <!-- شريط التنقل المحاسبي الموحد -->
    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <span>📝</span>
                <span>دورة طلبات القبض والصرف ومنظومة الاعتمادات (Workflow)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                الفصل بين طلب الصرف، المراجعة والاعتماد الإداري، والتنفيذ الفعلي بالصندوق لضمان الرقابة والامتثال المالي
            </p>
        </div>
        <button type="button" onclick="openNewRequestModal()" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
            <span>➕</span>
            <span>إنشاء طلب مالي جديد (صرف / قبض)</span>
        </button>
    </div>

    <!-- بطاقات المؤشرات وحالات الطلبات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('accounting.payment_requests.index', ['status' => 'pending_manager']) }}" class="bg-white p-5 rounded-2xl border {{ $status === 'pending_manager' ? 'border-indigo-600 ring-2 ring-indigo-500/20' : 'border-slate-200' }} shadow-xs hover:border-indigo-300 transition block">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-indigo-700">بانتظار الاعتماد الإداري</span>
                <span class="text-xl">⏳</span>
            </div>
            <span class="text-2xl font-black text-indigo-600 font-mono mt-2 block">{{ $pendingManagerCount }}</span>
            <span class="text-[11px] text-slate-400 mt-1 block">تتطلب موافقة المشرف العام / المدير</span>
        </a>

        <a href="{{ route('accounting.payment_requests.index', ['status' => 'approved']) }}" class="bg-white p-5 rounded-2xl border {{ $status === 'approved' ? 'border-emerald-600 ring-2 ring-emerald-500/20' : 'border-slate-200' }} shadow-xs hover:border-emerald-300 transition block">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-700">معتمدة وجاهزة للصرف</span>
                <span class="text-xl">✅</span>
            </div>
            <span class="text-2xl font-black text-emerald-600 font-mono mt-2 block">{{ $approvedCount }}</span>
            <span class="text-[11px] text-slate-400 mt-1 block">جاهزة لدى أمين الصندوق للصرف</span>
        </a>

        <a href="{{ route('accounting.payment_requests.index', ['status' => 'executed']) }}" class="bg-white p-5 rounded-2xl border {{ $status === 'executed' ? 'border-teal-600 ring-2 ring-teal-500/20' : 'border-slate-200' }} shadow-xs hover:border-teal-300 transition block">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-teal-700">تم التنفيذ والصرف</span>
                <span class="text-xl">💸</span>
            </div>
            <span class="text-2xl font-black text-teal-600 font-mono mt-2 block">{{ $executedCount }}</span>
            <span class="text-[11px] text-slate-400 mt-1 block">صدرت بها سندات رسمية وقيدت</span>
        </a>

        <a href="{{ route('accounting.payment_requests.index') }}" class="bg-white p-5 rounded-2xl border {{ empty($status) ? 'border-slate-800 ring-2 ring-slate-800/10' : 'border-slate-200' }} shadow-xs hover:border-slate-400 transition block">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700">إجمالي الطلبات</span>
                <span class="text-xl">📁</span>
            </div>
            <span class="text-2xl font-black text-slate-800 font-mono mt-2 block">{{ $requests->total() }}</span>
            <span class="text-[11px] text-slate-400 mt-1 block">كافة سجلات دورة الصرف والقبض</span>
        </a>
    </div>

    <!-- جدول الطلبات -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <a href="{{ route('accounting.payment_requests.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ empty($status) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">الكل</a>
                <a href="{{ route('accounting.payment_requests.index', ['status' => 'pending_manager']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $status === 'pending_manager' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">قيد الاعتماد</a>
                <a href="{{ route('accounting.payment_requests.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $status === 'approved' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">معتمدة للصرف</a>
                <a href="{{ route('accounting.payment_requests.index', ['status' => 'executed']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $status === 'executed' ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">منفذة</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-700 font-black border-b border-slate-200">
                    <tr>
                        <th class="p-4">رقم الطلب</th>
                        <th class="p-4">النوع والمستفيد</th>
                        <th class="p-4">المبلغ والعملة</th>
                        <th class="p-4">الغرض والمبررات</th>
                        <th class="p-4">مقدم الطلب</th>
                        <th class="p-4">مركز التكلفة</th>
                        <th class="p-4">الحالة</th>
                        <th class="p-4 text-center">الإجراءات والاعتماد</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($requests as $r)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-mono font-black text-indigo-700">
                                {{ $r->request_no }}
                                <span class="block text-[10px] text-slate-400 font-sans mt-0.5">{{ $r->created_at->format('Y/m/d H:i') }}</span>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $r->request_type === 'payment' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $r->request_type === 'payment' ? 'طلب صرف' : 'طلب توريد/قبض' }}
                                </span>
                                <span class="font-bold text-slate-900 block mt-1">{{ $r->beneficiary_name }}</span>
                            </td>
                            <td class="p-4 font-mono font-black text-sm text-slate-900">
                                {{ number_format($r->amount, 2) }} {{ $r->currency }}
                            </td>
                            <td class="p-4 max-w-xs text-slate-600 leading-relaxed">
                                {{ $r->purpose }}
                                @if($r->attachment_path)
                                    <a href="{{ asset('storage/' . $r->attachment_path) }}" target="_blank" class="block text-indigo-600 hover:underline text-[11px] font-bold mt-1">
                                        📎 استعراض المرفق والفاتورة
                                    </a>
                                @endif
                                @if($r->rejection_reason)
                                    <span class="block text-rose-600 text-[10px] font-bold mt-1">سبب الرفض: {{ $r->rejection_reason }}</span>
                                @endif
                            </td>
                            <td class="p-4 text-slate-700">
                                {{ $r->requester?->name ?? 'مستخدم' }}
                            </td>
                            <td class="p-4 text-slate-600">
                                @if($r->costCenter)
                                    <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-bold text-[11px]">{{ $r->costCenter->name }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-black border {{ $r->status_badge_class }}">
                                    {{ $r->status_label }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    @if(in_array($r->status, ['draft', 'pending_financial', 'pending_manager']))
                                        <!-- زر الاعتماد الإداري -->
                                        <form action="{{ route('accounting.payment_requests.approve', $r->id) }}" method="POST" onsubmit="return confirm('تأكيد اعتماد وموافقة الطلب المالي؟');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1">
                                                <span>✓</span>
                                                <span>اعتماد</span>
                                            </button>
                                        </form>

                                        <!-- زر الرفض -->
                                        <button type="button" onclick="openRejectModal({{ $r->id }}, '{{ $r->request_no }}')" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] border border-rose-200 transition">
                                            <span>✕</span>
                                            <span>رفض</span>
                                        </button>
                                    @elseif($r->status === 'approved')
                                        <!-- زر التنفيذ المالي لدى الصندوق -->
                                        <button type="button" onclick="openExecuteModal({{ $r->id }}, '{{ $r->request_no }}', {{ $r->amount }}, '{{ $r->currency }}', '{{ $r->beneficiary_name }}', '{{ $r->request_type }}')" class="px-3.5 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-black text-[11px] shadow-sm transition flex items-center gap-1">
                                            <span>💸</span>
                                            <span>صرف / تنفيذ</span>
                                        </button>
                                    @elseif($r->status === 'executed' && $r->voucher)
                                        <a href="{{ route('accounting.voucher.print', $r->voucher->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px] transition flex items-center gap-1">
                                            <span>📄</span>
                                            <span>السند: {{ $r->voucher->voucher_no }}</span>
                                        </a>
                                        <button type="button" onclick="openJournalEntryModal({{ $r->voucher->id }})" class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 font-bold text-[11px] transition flex items-center gap-1 shadow-2xs" title="استعراض القيد المحاسبي والأثر المالي">
                                            <span>⚖️</span>
                                            <span>القيد</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 font-bold">لا توجد طلبات صرف أو قبض مسجلة في هذا القسم.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: إنشاء طلب مالي جديد -->
<div id="newRequestModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
        <div class="p-6 bg-slate-900 text-white flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-xl">📝</span>
                <h3 class="font-black text-base">إنشاء طلب صرف أو توريد مالي جديد</h3>
            </div>
            <button type="button" onclick="closeNewRequestModal()" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>
        <form action="{{ route('accounting.payment_requests.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">نوع الطلب *</label>
                    <select name="request_type" required class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
                        <option value="payment">طلب صرف مالي (مصروفات / عهد / أجور)</option>
                        <option value="receipt">طلب توريد / قبض مالي</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">العملة *</label>
                    <select name="currency" required class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
                        <option value="YER">ريال يمني (YER)</option>
                        <option value="SAR">ريال سعودي (SAR)</option>
                        <option value="USD">دولار أمريكي (USD)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">اسم المستفيد / الجهه *</label>
                    <input type="text" name="beneficiary_name" required placeholder="مثال: شركة الكهرباء / الأستاذ أحمد" class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">المبلغ المطلوب *</label>
                    <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full text-xs font-mono font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">الحساب المالي المرتبط (اختياري)</label>
                    <select name="account_id" class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
                        <option value="">— اختر الحساب —</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">[{{ $acc->account_code }}] {{ $acc->account_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">مركز التكلفة (اختياري)</label>
                    <select name="cost_center_id" class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-indigo-500">
                        <option value="">— اختر مركز التكلفة —</option>
                        @foreach($costCenters as $cc)
                            <option value="{{ $cc->id }}">[{{ $cc->code }}] {{ $cc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">بيان ومبررات الصرف بالتفصيل *</label>
                <textarea name="purpose" rows="3" required placeholder="اكتب مبررات الصرف وأسبابها بالتفصيل..." class="w-full text-xs rounded-xl border-slate-300 p-2.5 focus:border-indigo-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">إرفاق فاتورة أو سند مؤيد (PDF / صورة)</label>
                <input type="file" name="attachment" accept=".pdf,.png,.jpg,.jpeg" class="w-full text-xs rounded-xl border border-slate-300 p-2">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                <button type="button" onclick="closeNewRequestModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">إلغاء</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm">إرسال الطلب للاعتماد</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: رفض الطلب المالي -->
<div id="rejectModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
        <div class="p-6 bg-rose-900 text-white flex justify-between items-center">
            <h3 class="font-black text-base">رفض الطلب المالي</h3>
            <button type="button" onclick="closeRejectModal()" class="text-rose-200 hover:text-white text-lg">✕</button>
        </div>
        <form id="rejectForm" method="POST" class="p-6 space-y-4">
            @csrf
            <p class="text-xs text-slate-600">أدخل سبب رفض الطلب رقم <span id="rejectReqNo" class="font-bold text-slate-900"></span> لإخطار مقدم الطلب:</p>
            <div>
                <textarea name="rejection_reason" rows="3" required placeholder="سبب الرفض..." class="w-full text-xs rounded-xl border-slate-300 p-2.5 focus:border-rose-500"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600">إلغاء</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-sm">تأكيد الرفض</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: تنفيذ وصرف الطلب المعتمد -->
<div id="executeModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
        <div class="p-6 bg-teal-900 text-white flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-xl">💸</span>
                <h3 class="font-black text-base">تنفيذ وصرف الطلب المالي</h3>
            </div>
            <button type="button" onclick="closeExecuteModal()" class="text-teal-200 hover:text-white text-lg">✕</button>
        </div>
        <form id="executeForm" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div class="p-3 rounded-2xl bg-teal-50 border border-teal-200 text-teal-950 space-y-1">
                <div class="flex justify-between items-center text-xs">
                    <span>رقم الطلب:</span>
                    <span id="execReqNo" class="font-mono font-bold"></span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span>المستفيد:</span>
                    <span id="execBeneficiary" class="font-bold"></span>
                </div>
                <div class="flex justify-between items-center text-sm font-black text-teal-800 pt-1 border-t border-teal-200">
                    <span>المبلغ المعتمد:</span>
                    <span id="execAmount" class="font-mono"></span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">الصندوق أو الحساب البنكي المنفذ منه *</label>
                <select name="cash_or_bank_account_id" required class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-teal-500">
                    @foreach($cashBoxes as $box)
                        <option value="{{ $box->id }}">[{{ $box->account_code }}] {{ $box->account_name }} (رصيد: {{ number_format($box->current_balance, 2) }} {{ $box->currency }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">طريقة الدفع *</label>
                    <select name="payment_method" required class="w-full text-xs font-bold rounded-xl border-slate-300 p-2.5 focus:border-teal-500">
                        <option value="cash">نقداً (كاش)</option>
                        <option value="bank_transfer">تحويل بنكي</option>
                        <option value="electronic_wallet">محفظة إلكترونية</option>
                        <option value="cheque">شيك مصرفي</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">الرقم الدفتري (اختياري)</label>
                    <input type="text" name="manual_no" placeholder="سند يدوي" class="w-full text-xs font-mono rounded-xl border-slate-300 p-2.5">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200">
                <button type="button" onclick="closeExecuteModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">إلغاء</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-sm">تأكيد الصرف وتوليد السند</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openNewRequestModal() {
        document.getElementById('newRequestModal').classList.remove('hidden');
    }
    function closeNewRequestModal() {
        document.getElementById('newRequestModal').classList.add('hidden');
    }

    function openRejectModal(id, reqNo) {
        document.getElementById('rejectForm').action = "/accounting/payment-requests/" + id + "/reject";
        document.getElementById('rejectReqNo').textContent = reqNo;
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    function openExecuteModal(id, reqNo, amount, currency, beneficiary, type) {
        document.getElementById('executeForm').action = "/accounting/payment-requests/" + id + "/execute";
        document.getElementById('execReqNo').textContent = reqNo;
        document.getElementById('execBeneficiary').textContent = beneficiary;
        document.getElementById('execAmount').textContent = amount.toLocaleString() + ' ' + currency;
        document.getElementById('executeModal').classList.remove('hidden');
    }
    function closeExecuteModal() {
        document.getElementById('executeModal').classList.add('hidden');
    }
</script>

@include('accounting.partials.journal_entry_modal')

@endsection
