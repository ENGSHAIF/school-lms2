@extends('layouts.app', ['title' => 'دليل الصناديق والخزائن والحسابات البنكية'])

@section('content')
<div class="space-y-6">

    <!-- الترويسة والأزرار العلوية -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 mb-1 flex items-center gap-2">
                <span>🏦</span>
                <span>دليل وهيكل الصناديق والخزائن والحسابات البنكية</span>
            </h1>
            <p class="text-xs text-slate-500">
                إدارة الصناديق النقدية، تفريع الصناديق الفرعية تحت الصناديق الرئيسية، وتحديد طبيعة الحركة (قبض، صرف، نقاط بيع، إيراد عام).
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button onclick="openCreateModal()" class="text-xs font-black text-white bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 rounded-xl transition shadow-md flex items-center gap-1.5">
                <span>➕</span>
                <span>إضافة صندوق أو بنك جديد</span>
            </button>
            <a href="{{ route('accounting.dashboard') }}" class="text-xs font-bold text-slate-700 bg-white border border-slate-300 px-4 py-2.5 rounded-xl hover:bg-slate-50 transition shadow-xs flex items-center gap-1">
                <span>←</span>
                <span>لوحة المحاسبة والسندات</span>
            </a>
        </div>
    </div>

    <!-- بطاقات المؤشرات المالية للصناديق والبنوك -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- إجمالي رصيد الصناديق النقدية (YER) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-400 block">إجمالي السيولة النقدية (ريال يمني)</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 text-sm">💵</span>
            </div>
            <span class="text-2xl font-black text-emerald-600 font-mono mt-2 block">
                {{ \App\Helpers\ArabicCurrencyHelper::format($stats['balance_yer'], 'YER') }}
            </span>
            <div class="mt-2 text-[11px] text-slate-400 flex items-center gap-2">
                <span>{{ $stats['total_boxes'] }} صناديق وخزائن نقدية</span>
                <span>•</span>
                <span>{{ $stats['sub_boxes_count'] }} فرعية</span>
            </div>
        </div>

        <!-- إجمالي العملات الأجنبية (سعودي ودولار) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-400 block">أرصدة العملات الأجنبية</span>
                <span class="p-2 rounded-xl bg-blue-50 text-blue-600 text-sm">🌐</span>
            </div>
            <div class="mt-2 space-y-1">
                <div class="text-sm font-black text-blue-700 font-mono flex justify-between">
                    <span>ر.س:</span>
                    <span>{{ \App\Helpers\ArabicCurrencyHelper::format($stats['balance_sar'], 'SAR') }}</span>
                </div>
                <div class="text-sm font-black text-slate-800 font-mono flex justify-between">
                    <span>دولار $:</span>
                    <span>{{ \App\Helpers\ArabicCurrencyHelper::format($stats['balance_usd'], 'USD') }}</span>
                </div>
            </div>
        </div>

        <!-- توزيع الصناديق حسب الحركة -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-400 block">تصنيف وطبيعة حركة الصناديق</span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600 text-sm">⚡</span>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-3 text-[11px]">
                <div class="bg-slate-50 p-2 rounded-lg">
                    <span class="text-slate-500 block">مصرح للقبض:</span>
                    <strong class="text-emerald-700 font-bold">{{ $stats['receipt_boxes_count'] }} صندوق</strong>
                </div>
                <div class="bg-slate-50 p-2 rounded-lg">
                    <span class="text-slate-500 block">مصرح للصرف:</span>
                    <strong class="text-rose-700 font-bold">{{ $stats['payment_boxes_count'] }} صندوق</strong>
                </div>
            </div>
        </div>

        <!-- نقاط البيع والبنوك -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-400 block">نقاط البيع والحسابات البنكية</span>
                <span class="p-2 rounded-xl bg-purple-50 text-purple-600 text-sm">🛒</span>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-3 text-[11px]">
                <div class="bg-purple-50/60 p-2 rounded-lg">
                    <span class="text-purple-900 block">كاشير ونقاط بيع:</span>
                    <strong class="text-purple-700 font-bold">{{ $stats['pos_boxes_count'] }} نقاط</strong>
                </div>
                <div class="bg-blue-50/60 p-2 rounded-lg">
                    <span class="text-blue-900 block">بنوك ومحافظ:</span>
                    <strong class="text-blue-700 font-bold">{{ $stats['banks_count'] }} حسابات</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- شريط الفلاتر والبحث -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('accounting.cash_boxes.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 text-xs">
            <!-- البحث -->
            <div>
                <label class="block font-bold text-slate-600 mb-1">بحث بالاسم أو الكود:</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث برقم أو اسم الصندوق..." class="w-full border border-slate-300 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- نوع الحساب -->
            <div>
                <label class="block font-bold text-slate-600 mb-1">نوع الحساب:</label>
                <select name="account_type" class="w-full border border-slate-300 rounded-xl px-3 py-2 bg-white outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- جميع الأنواع --</option>
                    <option value="cash_box" {{ request('account_type') === 'cash_box' ? 'selected' : '' }}>صناديق وخزائن نقدية</option>
                    <option value="bank" {{ request('account_type') === 'bank' ? 'selected' : '' }}>حسابات بنكية ومحافظ</option>
                </select>
            </div>

            <!-- طبيعة الحركة -->
            <div>
                <label class="block font-bold text-slate-600 mb-1">طبيعة الحركة المسموحة:</label>
                <select name="box_nature" class="w-full border border-slate-300 rounded-xl px-3 py-2 bg-white outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- جميع الحركات --</option>
                    <option value="both" {{ request('box_nature') === 'both' ? 'selected' : '' }}>قبض وصرف (عام)</option>
                    <option value="receipt" {{ request('box_nature') === 'receipt' ? 'selected' : '' }}>قبض وتحصيل إيراد فقط</option>
                    <option value="payment" {{ request('box_nature') === 'payment' ? 'selected' : '' }}>صرف ومصروفات فقط</option>
                    <option value="pos" {{ request('box_nature') === 'pos' ? 'selected' : '' }}>نقطة بيع / كاشير استقبال</option>
                </select>
            </div>

            <!-- العملة -->
            <div>
                <label class="block font-bold text-slate-600 mb-1">العملة:</label>
                <select name="currency" class="w-full border border-slate-300 rounded-xl px-3 py-2 bg-white outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- جميع العملات --</option>
                    <option value="YER" {{ request('currency') === 'YER' ? 'selected' : '' }}>ريال يمني (YER)</option>
                    <option value="SAR" {{ request('currency') === 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                    <option value="USD" {{ request('currency') === 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                </select>
            </div>

            <!-- أزرار الفلترة -->
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition text-center shadow-xs">
                    تطبيق التصفية
                </button>
                @if(request()->hasAny(['search', 'account_type', 'box_nature', 'currency']))
                    <a href="{{ route('accounting.cash_boxes.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition" title="إعادة تعيين">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- جدول شجرة وهيكل الصناديق النقدية -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <span>📁</span>
                <span>شجرة الصناديق النقدية (الرئيسية والفرعية المتفرعة منها)</span>
            </h2>
            <span class="text-xs text-slate-500 font-bold">{{ $mainBoxes->count() }} صناديق رئيسية</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-100/70 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">رقم الحساب (الكود)</th>
                        <th class="py-3 px-4">اسم الصندوق / الخزينة</th>
                        <th class="py-3 px-4">التصنيف</th>
                        <th class="py-3 px-4">طبيعة الحركة المعتمدة</th>
                        <th class="py-3 px-4">أمين الصندوق / المسؤول</th>
                        <th class="py-3 px-4">العملة</th>
                        <th class="py-3 px-4 text-left">الرصيد الفعلي</th>
                        <th class="py-3 px-4 text-center">الحالة</th>
                        <th class="py-3 px-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mainBoxes as $main)
                        <!-- الصندوق الرئيسي الأب -->
                        <tr class="bg-slate-50/60 font-bold hover:bg-slate-100/60 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 flex items-center gap-2">
                                <span class="text-base text-amber-600">📂</span>
                                <span>{{ $main->account_code }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-900 font-black">
                                <span>{{ $main->account_name }}</span>
                                @if($main->description)
                                    <span class="block text-[10px] text-slate-400 font-normal">{{ $main->description }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $main->box_category_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($main->box_nature === 'receipt')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">📥 قبض فقط</span>
                                @elseif($main->box_nature === 'payment')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">📤 صرف فقط</span>
                                @elseif($main->box_nature === 'pos')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">🛒 نقطة بيع</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-800">🔄 قبض وصرف</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $main->custodian?->name ?? 'غير محدد (إدارة عامة)' }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-700">
                                {{ $main->currency_name }} ({{ $main->currency }})
                            </td>
                            <td class="py-3 px-4 text-left font-mono font-black text-slate-900 text-sm">
                                {{ $main->formatted_balance }}
                                @if($main->children->count() > 0)
                                    <span class="block text-[10px] text-slate-400 font-normal">
                                        تراكمي مع الفروع: {{ $main->formatted_total_balance }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($main->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">نشط</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-200 text-slate-600">موقف</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('accounting.cash_boxes.statement', $main->id) }}" class="p-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition" title="كشف حركة الصندوق">
                                        📜
                                    </a>
                                    <a href="{{ route('accounting.cash_boxes.statement.print', $main->id) }}" target="_blank" class="p-1.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition" title="طباعة تقرير رسمي A4 / PDF">
                                        🖨️
                                    </a>
                                    <button onclick="openSubBoxModal({{ $main->id }}, '{{ addslashes($main->account_name) }}', '{{ $main->currency }}')" class="p-1.5 bg-emerald-50 text-emerald-700 rounded-lg hover:bg-emerald-100 transition text-[11px] font-bold flex items-center gap-1" title="تفريغ صندوق فرعي تحت هذا الحساب">
                                        <span>➕ فرعي</span>
                                    </button>
                                    <button onclick="openEditModal({{ json_encode($main) }})" class="p-1.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition" title="تعديل">
                                        ✏️
                                    </button>
                                    @if($main->box_vouchers_count == 0 && $main->children->count() == 0 && abs((float)$main->current_balance) < 0.001)
                                        <form action="{{ route('accounting.cash_boxes.destroy', $main->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الصندوق؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-rose-50 text-rose-700 rounded-lg hover:bg-rose-100 transition" title="حذف">
                                                🗑️
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- الصناديق الفرعية المتفرعة من هذا الصندوق الرئيسي -->
                        @foreach($main->children as $sub)
                            <tr class="hover:bg-slate-50 transition border-r-4 border-emerald-400 bg-white">
                                <td class="py-2.5 px-4 font-mono text-slate-600 pr-8">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-slate-400 font-sans">↳</span>
                                        <span class="font-bold text-slate-800">{{ $sub->account_code }}</span>
                                    </div>
                                </td>
                                <td class="py-2.5 px-4 text-slate-800 font-bold pr-8">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-sm">🪙</span>
                                        <span>{{ $sub->account_name }}</span>
                                    </div>
                                    @if($sub->description)
                                        <span class="block text-[10px] text-slate-400 font-normal pr-5">{{ $sub->description }}</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        {{ $sub->box_category_label }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-4">
                                    @if($sub->box_nature === 'receipt')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">📥 قبض فقط</span>
                                    @elseif($sub->box_nature === 'payment')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">📤 صرف فقط</span>
                                    @elseif($sub->box_nature === 'pos')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">🛒 نقطة بيع</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-800">🔄 قبض وصرف</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-slate-600">
                                    {{ $sub->custodian?->name ?? 'غير محدد' }}
                                </td>
                                <td class="py-2.5 px-4 text-slate-600">
                                    {{ $sub->currency_name }}
                                </td>
                                <td class="py-2.5 px-4 text-left font-mono font-bold text-emerald-700">
                                    {{ $sub->formatted_balance }}
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    @if($sub->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">نشط</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">موقف</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('accounting.cash_boxes.statement', $sub->id) }}" class="p-1 bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition text-[11px]" title="كشف الحساب">
                                            📜
                                        </a>
                                        <a href="{{ route('accounting.cash_boxes.statement.print', $sub->id) }}" target="_blank" class="p-1 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition text-[11px]" title="طباعة تقرير رسمي A4 / PDF">
                                            🖨️
                                        </a>
                                        <button onclick="openEditModal({{ json_encode($sub) }})" class="p-1 bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition text-[11px]" title="تعديل">
                                            ✏️
                                        </button>
                                        @if($sub->box_vouchers_count == 0 && abs((float)$sub->current_balance) < 0.001)
                                            <form action="{{ route('accounting.cash_boxes.destroy', $sub->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الصندوق الفرعي؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 bg-rose-50 text-rose-700 rounded hover:bg-rose-100 transition text-[11px]" title="حذف">
                                                    🗑️
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">
                                لا توجد صناديق نقدية مضافة بعد. اضغط على زر "إضافة صندوق جديد" للبدء.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- جدول الحسابات البنكية والمحافظ الإلكترونية -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-sm font-black text-slate-800 flex items-center gap-2">
                <span>🏦</span>
                <span>الحسابات البنكية والمحافظ الإلكترونية المعتمدة</span>
            </h2>
            <span class="text-xs text-slate-500 font-bold">{{ $banks->count() }} حسابات</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-100/70 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">رقم الحساب (الكود)</th>
                        <th class="py-3 px-4">اسم الحساب / البنك</th>
                        <th class="py-3 px-4">جهة الحساب / البنك</th>
                        <th class="py-3 px-4">رقم الآيبان / الحساب المصرفي</th>
                        <th class="py-3 px-4">طبيعة الحركة</th>
                        <th class="py-3 px-4">العملة</th>
                        <th class="py-3 px-4 text-left">الرصيد الفعلي</th>
                        <th class="py-3 px-4 text-center">الحالة</th>
                        <th class="py-3 px-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($banks as $b)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $b->account_code }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $b->account_name }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $b->bank_name ?? '—' }}</td>
                            <td class="py-3 px-4 font-mono text-slate-600 dir-ltr text-right">{{ $b->account_number ?? '—' }}</td>
                            <td class="py-3 px-4">
                                @if($b->box_nature === 'receipt')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">📥 إيداع وقبض فقط</span>
                                @elseif($b->box_nature === 'payment')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">📤 سحب وصرف فقط</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">🔄 تحويل وقبض وصرف</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-700">{{ $b->currency_name }}</td>
                            <td class="py-3 px-4 text-left font-mono font-black text-blue-700">{{ $b->formatted_balance }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($b->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">نشط</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">موقف</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('accounting.cash_boxes.statement', $b->id) }}" class="p-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition" title="كشف الحساب">
                                        📜
                                    </a>
                                    <a href="{{ route('accounting.cash_boxes.statement.print', $b->id) }}" target="_blank" class="p-1.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition" title="طباعة تقرير رسمي A4 / PDF">
                                        🖨️
                                    </a>
                                    <button onclick="openEditModal({{ json_encode($b) }})" class="p-1.5 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 transition" title="تعديل">
                                        ✏️
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-6 text-center text-slate-400">
                                لا توجد حسابات بنكية مضافة بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- مودال إنشاء / تعديل صندوق أو بنك -->
<div id="boxModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center hidden p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 relative my-8">
        <div class="flex justify-between items-center pb-4 border-b border-slate-100">
            <div>
                <h3 id="modalTitle" class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span>🏦</span>
                    <span>إضافة صندوق أو بنك جديد</span>
                </h3>
                <p class="text-xs text-slate-400">تحديد نوع الحساب المالي، الشجرة التابعة، وطبيعة حركة القبض والصرف</p>
            </div>
            <button onclick="closeBoxModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">✕</button>
        </div>

        <form id="boxForm" action="{{ route('accounting.cash_boxes.store') }}" method="POST" class="space-y-4 pt-4 text-xs">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <!-- نوع الحساب العام -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">نوع الحساب المالي <span class="text-rose-500">*</span></label>
                    <select name="account_type" id="modalAccountType" onchange="toggleAccountTypeFields()" required class="w-full border border-slate-300 rounded-xl px-3 py-2 bg-white font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="cash_box">صندوق نقدي / خزينة</option>
                        <option value="bank">حساب بنكي / محفظة مصرفية</option>
                    </select>
                </div>

                <!-- تصنيف الصندوق -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">تصنيف وطبيعة الحساب <span class="text-rose-500">*</span></label>
                    <select name="box_category" id="modalBoxCategory" onchange="toggleParentBoxField()" required class="w-full border border-slate-300 rounded-xl px-3 py-2 bg-white font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="main_box">صندوق نقدي رئيسي (Main Box)</option>
                        <option value="sub_box">صندوق فرعي تابع لحساب رئيسي</option>
                        <option value="general_revenue">صندوق إيراد عام (General Revenue)</option>
                        <option value="pos">صندوق نقطة بيع / كاشير استقبال (POS)</option>
                        <option value="bank">حساب بنكي جاري / توفير</option>
                        <option value="electronic_wallet">محفظة إلكترونية (جوالي، كاش، إلخ)</option>
                    </select>
                </div>
            </div>

            <!-- الصندوق الأب (في حالة الصندوق الفرعي) -->
            <div id="parentBoxWrapper" class="hidden p-3 bg-amber-50 rounded-xl border border-amber-200">
                <label class="block font-black text-amber-950 mb-1">اختر الصندوق الرئيسي الأب <span class="text-rose-500">*</span></label>
                <select name="parent_id" id="modalParentId" class="w-full border border-amber-300 rounded-xl px-3 py-2 bg-white font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- اختر الصندوق الرئيسي التابع له --</option>
                    @foreach($parentBoxes as $p)
                        <option value="{{ $p->id }}">{{ $p->account_name }} (كود: {{ $p->account_code }}) - {{ $p->currency }}</option>
                    @endforeach
                </select>
                <span class="text-[10px] text-amber-700 block mt-1">سيتم تفريع الحساب تسلسلياً تحت كود هذا الصندوق الرئيسي تلقائياً.</span>
            </div>

            <!-- اسم الصندوق والكود -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">اسم الصندوق / الحساب <span class="text-rose-500">*</span></label>
                    <input type="text" name="account_name" id="modalAccountName" required placeholder="مثال: صندوق استقبال فرع 1، كاشير التسجيل، بنك الكريمي..." class="w-full border border-slate-300 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">رقم الحساب (الكود)</label>
                    <input type="text" name="account_code" id="modalAccountCode" placeholder="يُولّد تلقائياً إن تُرك فارغاً" class="w-full border border-slate-300 rounded-xl px-3 py-2 font-mono outline-none focus:ring-2 focus:ring-emerald-500" dir="ltr">
                </div>
            </div>

            <!-- طبيعة الحركة والوظيفة التشغيلية للصندوق -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                <label class="block font-black text-slate-800">طبيعة الحركة والوظيفة المالية المسموحة للصندوق <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-slate-200 cursor-pointer hover:border-emerald-300">
                        <input type="radio" name="box_nature" value="both" checked class="text-emerald-600">
                        <span class="text-[11px] font-bold">قبض وصرف (عام)</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-slate-200 cursor-pointer hover:border-emerald-300">
                        <input type="radio" name="box_nature" value="receipt" class="text-emerald-600">
                        <span class="text-[11px] font-bold">قبض وتحصيل فقط</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-slate-200 cursor-pointer hover:border-emerald-300">
                        <input type="radio" name="box_nature" value="payment" class="text-emerald-600">
                        <span class="text-[11px] font-bold">صرف ومصروفات فقط</span>
                    </label>
                    <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-slate-200 cursor-pointer hover:border-emerald-300">
                        <input type="radio" name="box_nature" value="pos" class="text-emerald-600">
                        <span class="text-[11px] font-bold">نقطة بيع / كاشير</span>
                    </label>
                </div>
                <span class="text-[10px] text-slate-400 block">
                    يحدد هذا الخيار في أي شاشات سندات يظهر الصندوق (في شاشة سند القبض، أم سند الصرف، أم في كليهما).
                </span>
            </div>

            <!-- العملة والرصيد الافتتاحي وأمين الصندوق -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">العملة <span class="text-rose-500">*</span></label>
                    <select name="currency" id="modalCurrency" required class="w-full border border-slate-300 rounded-xl px-3 py-2 bg-white font-bold outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="YER">ريال يمني (YER)</option>
                        <option value="SAR">ريال سعودي (SAR)</option>
                        <option value="USD">دولار أمريكي (USD)</option>
                    </select>
                </div>

                <div id="openingBalanceWrapper">
                    <label class="block font-bold text-slate-700 mb-1">الرصيد الافتتاحي</label>
                    <input type="number" step="0.01" min="0" name="opening_balance" id="modalOpeningBalance" value="0.00" class="w-full border border-slate-300 rounded-xl px-3 py-2 font-mono outline-none focus:ring-2 focus:ring-emerald-500" dir="ltr">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">أمين / مسؤول الصندوق</label>
                    <select name="custodian_id" id="modalCustodianId" class="w-full border border-slate-300 rounded-xl px-3 py-2 bg-white outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- بدون تعيين أمين محدد --</option>
                        @foreach($custodians as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->role }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- حقول الحساب البنكي (تظهر إذا تم اختيار بنك) -->
            <div id="bankDetailsWrapper" class="hidden grid grid-cols-2 gap-3 p-3 bg-blue-50/70 rounded-xl border border-blue-200">
                <div>
                    <label class="block font-bold text-blue-900 mb-1">اسم البنك / الجهة المصرفية</label>
                    <input type="text" name="bank_name" id="modalBankName" placeholder="مثال: بنك الكريمي للتمويل الأصغر" class="w-full border border-blue-300 rounded-xl px-3 py-2 bg-white outline-none">
                </div>
                <div>
                    <label class="block font-bold text-blue-900 mb-1">رقم الحساب المصرفي / الآيبان</label>
                    <input type="text" name="account_number" id="modalAccountNumber" placeholder="رقم الحساب أو المحفظة" class="w-full border border-blue-300 rounded-xl px-3 py-2 bg-white font-mono outline-none" dir="ltr">
                </div>
            </div>

            <!-- الوصف والملاحظات -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">ملاحظات وبيان الحساب</label>
                <input type="text" name="description" id="modalDescription" placeholder="ملاحظات حول صلاحيات الصندوق أو موقعه..." class="w-full border border-slate-300 rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- حالة التفعيل (تظهر فقط عند التعديل) -->
            <div id="activeStatusWrapper" class="hidden flex items-center gap-2 pt-1">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="modalIsActive" value="1" checked class="w-4 h-4 text-emerald-600 rounded">
                <label for="modalIsActive" class="font-bold text-slate-700 cursor-pointer">الحساب نشط ويستقبل العمليات المالية</label>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeBoxModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    إلغاء
                </button>
                <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>💾</span>
                    <span id="submitBtnText">حفظ الصندوق المالي</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'إضافة صندوق أو بنك جديد';
        document.getElementById('submitBtnText').innerText = 'حفظ الصندوق المالي';
        document.getElementById('boxForm').action = "{{ route('accounting.cash_boxes.store') }}";
        document.getElementById('formMethod').value = 'POST';

        document.getElementById('modalAccountType').value = 'cash_box';
        document.getElementById('modalBoxCategory').value = 'main_box';
        document.getElementById('modalAccountName').value = '';
        document.getElementById('modalAccountCode').value = '';
        document.getElementById('modalParentId').value = '';
        document.getElementById('modalCurrency').value = 'YER';
        document.getElementById('modalOpeningBalance').value = '0.00';
        document.getElementById('modalCustodianId').value = '';
        document.getElementById('modalBankName').value = '';
        document.getElementById('modalAccountNumber').value = '';
        document.getElementById('modalDescription').value = '';
        document.getElementById('modalIsActive').checked = true;

        document.querySelector('input[name="box_nature"][value="both"]').checked = true;

        document.getElementById('openingBalanceWrapper').classList.remove('hidden');
        document.getElementById('activeStatusWrapper').classList.add('hidden');

        toggleAccountTypeFields();
        toggleParentBoxField();

        document.getElementById('boxModal').classList.remove('hidden');
    }

    function openSubBoxModal(parentId, parentName, currency) {
        openCreateModal();
        document.getElementById('modalTitle').innerText = 'إضافة صندوق فرعي تابع لـ: ' + parentName;
        document.getElementById('modalBoxCategory').value = 'sub_box';
        document.getElementById('modalParentId').value = parentId;
        document.getElementById('modalCurrency').value = currency || 'YER';
        document.querySelector('input[name="box_nature"][value="receipt"]').checked = true;
        toggleParentBoxField();
    }

    function openEditModal(box) {
        document.getElementById('modalTitle').innerText = 'تعديل بيانات: ' + box.account_name;
        document.getElementById('submitBtnText').innerText = 'حفظ التعديلات';
        document.getElementById('boxForm').action = '/accounting/cash-boxes/' + box.id + '/update';
        document.getElementById('formMethod').value = 'POST';

        document.getElementById('modalAccountType').value = box.account_type || 'cash_box';
        document.getElementById('modalBoxCategory').value = box.box_category || 'main_box';
        document.getElementById('modalAccountName').value = box.account_name || '';
        document.getElementById('modalAccountCode').value = box.account_code || '';
        document.getElementById('modalParentId').value = box.parent_id || '';
        document.getElementById('modalCurrency').value = box.currency || 'YER';
        document.getElementById('modalCustodianId').value = box.custodian_id || '';
        document.getElementById('modalBankName').value = box.bank_name || '';
        document.getElementById('modalAccountNumber').value = box.account_number || '';
        document.getElementById('modalDescription').value = box.description || '';
        document.getElementById('modalIsActive').checked = !!box.is_active;

        const natureRadio = document.querySelector('input[name="box_nature"][value="' + (box.box_nature || 'both') + '"]');
        if (natureRadio) natureRadio.checked = true;

        // إخفاء حقل الرصيد الافتتاحي في التعديل لمنع التلاعب بالقيد
        document.getElementById('openingBalanceWrapper').classList.add('hidden');
        document.getElementById('activeStatusWrapper').classList.remove('hidden');

        toggleAccountTypeFields();
        toggleParentBoxField();

        document.getElementById('boxModal').classList.remove('hidden');
    }

    function closeBoxModal() {
        document.getElementById('boxModal').classList.add('hidden');
    }

    function toggleAccountTypeFields() {
        const type = document.getElementById('modalAccountType').value;
        const bankWrap = document.getElementById('bankDetailsWrapper');
        const catSelect = document.getElementById('modalBoxCategory');

        if (type === 'bank') {
            bankWrap.classList.remove('hidden');
            if (catSelect.value !== 'bank' && catSelect.value !== 'electronic_wallet') {
                catSelect.value = 'bank';
            }
        } else {
            bankWrap.classList.add('hidden');
            if (catSelect.value === 'bank' || catSelect.value === 'electronic_wallet') {
                catSelect.value = 'main_box';
            }
        }
        toggleParentBoxField();
    }

    function toggleParentBoxField() {
        const cat = document.getElementById('modalBoxCategory').value;
        const pWrap = document.getElementById('parentBoxWrapper');
        if (cat === 'sub_box') {
            pWrap.classList.remove('hidden');
        } else {
            pWrap.classList.add('hidden');
        }
    }
</script>
@endsection
