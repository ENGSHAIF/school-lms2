@extends('layouts.app', ['title' => 'محفظة الشيكات وأوراق القبض والدفع - يمن سوفت ERP'])

@section('content')
<div class="space-y-6">

    @include('accounting.partials.nav')

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 mb-1 flex items-center gap-2">
                <span>💳</span>
                <span>محفظة الشيكات وأوراق القبض والدفع (Cheques Portfolio)</span>
            </h1>
            <p class="text-xs text-slate-500">وفق الفصل الثالث من دليل المتكامل بلاس ERP: إدارة الشيكات الآجلة تحت التحصيل، إثبات تحصيلها في البنك، إرجاع الشيكات المرفوضة، وتمديد التواريخ.</p>
        </div>
        <button onclick="document.getElementById('newChequeModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-xs transition flex items-center gap-2">
            <span>➕</span>
            <span>إضافة شيك جديد إلى المحفظة</span>
        </button>
    </div>

    <!-- جدول الشيكات -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-wrap justify-between items-center gap-3">
            <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <span>📑</span>
                <span>سجل أوراق القبض والدفع</span>
            </h2>

            <!-- فلاتر -->
            <form action="{{ route('accounting.cheques.index') }}" method="GET" class="flex items-center gap-2 text-xs">
                <select name="type" class="rounded-lg border-slate-300 py-1 text-xs font-bold" onchange="this.form.submit()">
                    <option value="">-- كل الأنواع --</option>
                    <option value="receivable" {{ request('type') === 'receivable' ? 'selected' : '' }}>أوراق قبض (وارد)</option>
                    <option value="payable" {{ request('type') === 'payable' ? 'selected' : '' }}>أوراق دفع (صادر)</option>
                </select>
                <select name="status" class="rounded-lg border-slate-300 py-1 text-xs font-bold" onchange="this.form.submit()">
                    <option value="">-- كل الحالات --</option>
                    <option value="under_collection" {{ request('status') === 'under_collection' ? 'selected' : '' }}>تحت التحصيل</option>
                    <option value="collected" {{ request('status') === 'collected' ? 'selected' : '' }}>تم التحصيل</option>
                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>مرتجع</option>
                    <option value="extended" {{ request('status') === 'extended' ? 'selected' : '' }}>مؤجل/ممدد</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-100/80 text-slate-700 font-black border-b border-slate-200">
                        <th class="p-3">رقم الشيك</th>
                        <th class="p-3">النوع</th>
                        <th class="p-3">الحساب / الطرف</th>
                        <th class="p-3">البنك المسحوب عليه</th>
                        <th class="p-3">المبلغ</th>
                        <th class="p-3">تاريخ الاستحقاق</th>
                        <th class="p-3">الحالة الحالية</th>
                        <th class="p-3 text-center">إجراءات يمن سوفت</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($cheques as $ch)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-3 font-mono font-black text-indigo-950">#{{ $ch->cheque_no }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $ch->cheque_type === 'receivable' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $ch->cheque_type_label }}
                            </span>
                        </td>
                        <td class="p-3 font-bold text-slate-800">{{ $ch->account?->account_name ?? 'حساب مالي' }}</td>
                        <td class="p-3 text-slate-600">{{ $ch->bank_name ?? ($ch->bankAccount?->account_name ?? '-') }}</td>
                        <td class="p-3 font-mono font-black text-slate-900">{{ number_format($ch->amount, 2) }} {{ $ch->currency }}</td>
                        <td class="p-3 font-mono text-slate-600">
                            {{ $ch->due_date->format('Y-m-d') }}
                            @if($ch->extension_date)
                                <div class="text-[10px] text-purple-600 font-bold">ممدد إلى: {{ $ch->extension_date->format('Y-m-d') }}</div>
                            @endif
                        </td>
                        <td class="p-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black border {{ $ch->status_badge_class }}">
                                {{ $ch->status_label }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            @if($ch->status === 'under_collection' || $ch->status === 'extended')
                            <div class="inline-flex items-center gap-1">
                                <!-- تحصيل -->
                                <form action="{{ route('accounting.cheques.update_status', $ch->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="collect">
                                    <button type="submit" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-black transition" onclick="return confirm('تأكيد تحصيل الشيك وإيداعه في البنك؟')">
                                        تحصيل
                                    </button>
                                </form>
                                <!-- إرجاع -->
                                <form action="{{ route('accounting.cheques.update_status', $ch->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="return">
                                    <button type="submit" class="px-2 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded text-[11px] font-black transition" onclick="return confirm('تأكيد تسجيل الشيك كمرتجع؟')">
                                        إرجاع
                                    </button>
                                </form>
                                <!-- تمديد -->
                                <button type="button" onclick="openExtendModal({{ $ch->id }}, '{{ $ch->cheque_no }}')" class="px-2 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded text-[11px] font-black transition">
                                    تمديد
                                </button>
                            </div>
                            @else
                                <span class="text-slate-400 text-[11px]">مكتمل</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-400">لا توجد شيكات مسجلة في المحفظة حالياً.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cheques->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $cheques->links() }}
        </div>
        @endif
    </div>

</div>

<!-- مودال إضافة شيك جديد -->
<div id="newChequeModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <span>💳</span>
                <span>إضافة ورقة قبض / دفع جديدة إلى المحفظة</span>
            </h3>
            <button onclick="document.getElementById('newChequeModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form action="{{ route('accounting.cheques.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">نوع الشيك:</label>
                    <select name="cheque_type" class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-bold">
                        <option value="receivable">ورقة قبض (شيك وارد من طالب/عميل)</option>
                        <option value="payable">ورقة دفع (شيك صادر لمعلم/مورد)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">رقم الشيك:</label>
                    <input type="text" name="cheque_no" required class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-mono font-bold" placeholder="مثلاً: 984512">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">الحساب المالي المرتبط (العميل / المستفيد):</label>
                <select name="account_id" required class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-bold">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">البنك المسحوب عليه:</label>
                    <input type="text" name="bank_name" class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-bold" placeholder="مثلاً: بنك الكريمي / التضامن">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">المبلغ:</label>
                    <input type="number" step="0.01" name="amount" required class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-mono font-bold" placeholder="0.00">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">العملة:</label>
                    <select name="currency" class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-bold">
                        <option value="YER">ريال يمني (YER)</option>
                        <option value="SAR">ريال سعودي (SAR)</option>
                        <option value="USD">دولار أمريكي (USD)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ الاستحقاق:</label>
                    <input type="date" name="due_date" required class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-mono font-bold">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">ملاحظات وبيان الشيك:</label>
                <textarea name="notes" rows="2" class="w-full rounded-xl border-slate-300 py-1.5 text-xs" placeholder="أية تفاصيل إضافية أو اسم الساحب..."></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('newChequeModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-xl font-black">حفظ في المحفظة</button>
            </div>
        </form>
    </div>
</div>

<!-- مودال تمديد أجل الشيك -->
<div id="extendModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl space-y-4">
        <h3 class="text-sm font-black text-slate-800" id="extendTitle">تمديد أجل الشيك</h3>
        <form id="extendForm" method="POST" class="space-y-3 text-xs">
            @csrf
            <input type="hidden" name="action" value="extend">
            <div>
                <label class="block font-bold text-slate-700 mb-1">تاريخ الاستحقاق الجديد:</label>
                <input type="date" name="extension_date" required class="w-full rounded-xl border-slate-300 py-1.5 text-xs font-mono font-bold">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('extendModal').classList.add('hidden')" class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg font-bold">إلغاء</button>
                <button type="submit" class="px-4 py-1.5 bg-purple-600 text-white rounded-lg font-black">تأكيد التمديد</button>
            </div>
        </form>
    </div>
</div>

<script>
function openExtendModal(id, no) {
    document.getElementById('extendTitle').innerText = 'تمديد استحقاق الشيك #' + no;
    document.getElementById('extendForm').action = '/accounting/cheques/' + id + '/status';
    document.getElementById('extendModal').classList.remove('hidden');
}
</script>
@endsection
