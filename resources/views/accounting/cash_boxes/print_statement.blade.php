<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف حركة حساب {{ $box->account_name }} ({{ $box->account_code }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f8fafc; color: #0f172a; }
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            .print-container { box-shadow: none !important; border: none !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <div class="max-w-5xl mx-auto">
        <!-- شريط الإجراءات العلوي (لا يظهر عند الطباعة) -->
        <div class="no-print mb-6 flex flex-col sm:flex-row justify-between items-center gap-3 bg-slate-900 text-white p-4 rounded-2xl shadow-lg">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🖨️</span>
                <div>
                    <span class="font-black text-sm block">تقرير كشف حركة الصندوق والحساب المالي (جاهز للطباعة والـ PDF)</span>
                    <span class="text-xs text-slate-400">يمكنك حفظه مباشرة بصيغة PDF بدقة عالية أو طباعته على ورق A4</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('accounting.cash_boxes.statement', $box->id) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition">
                    ← العودة لكشف الحساب
                </a>
                <button onclick="window.print()" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-xl transition flex items-center gap-1.5 shadow-md">
                    <span>🖨️</span>
                    <span>طباعة التقرير / Save as PDF</span>
                </button>
            </div>
        </div>

        <!-- ورقة التقرير الرسمية A4 -->
        <div class="print-container bg-white border border-slate-300 rounded-3xl p-8 sm:p-10 shadow-xl space-y-6">
            
            <!-- ترويسة التقرير الرسمية -->
            <div class="flex justify-between items-start border-b-2 border-slate-900 pb-5">
                <div>
                    <h1 class="text-base font-black tracking-wide text-slate-900">The Asian Star Office for Translation, Training, & Consultancy Services (ASOTS)</h1>
                    <span class="text-xs text-slate-700 block font-bold mt-0.5">مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات</span>
                    <span class="text-[11px] text-slate-500 block">الإدارة المالية العامة • دائرة الحسابات والخزينة • النظام المحاسبي الموحد</span>
                </div>
                <div class="text-left font-mono text-xs">
                    <div class="bg-slate-100 border border-slate-300 px-3 py-1.5 rounded-lg mb-1 font-bold text-slate-900">
                        كود الحساب: <span class="text-indigo-700">{{ $box->account_code }}</span>
                    </div>
                    <div class="text-[11px] text-slate-500">
                        <span>تاريخ الطباعة:</span>
                        <strong>{{ now()->format('Y/m/d h:i A') }}</strong>
                    </div>
                </div>
            </div>

            <!-- عنوان التقرير -->
            <div class="text-center my-4">
                <span class="inline-block bg-slate-900 text-white text-sm sm:text-base font-black px-6 py-2 rounded-xl shadow-xs">
                    كـشـف حـركـة الـصـنـدوق والـحـسـاب الـمـالـي (Cash Box & Bank Statement)
                </span>
            </div>

            <!-- بطاقة بيانات الصندوق والحساب -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs">
                <div>
                    <span class="text-slate-500 block">اسم الحساب / الصندوق:</span>
                    <strong class="text-slate-900 font-black text-sm block mt-0.5">{{ $box->account_name }}</strong>
                </div>
                <div>
                    <span class="text-slate-500 block">تصنيف وطبيعة الحركة:</span>
                    <strong class="text-indigo-700 font-bold block mt-0.5">{{ $box->box_category_label }} ({{ $box->box_nature_label }})</strong>
                </div>
                <div>
                    <span class="text-slate-500 block">العملة الأساسية:</span>
                    <strong class="text-emerald-700 font-bold block mt-0.5">{{ $box->currency_name }} ({{ $box->currency }})</strong>
                </div>
                <div>
                    <span class="text-slate-500 block">أمين الصندوق / المسؤول:</span>
                    <strong class="text-slate-900 font-bold block mt-0.5">{{ $box->custodian?->name ?? 'إدارة الحسابات العامة' }}</strong>
                </div>
                @if($box->parent)
                    <div class="sm:col-span-2">
                        <span class="text-slate-500 block">تابع للحساب الرئيسي (الأب):</span>
                        <strong class="text-amber-800 font-bold block mt-0.5">{{ $box->parent->account_name }} [{{ $box->parent->account_code }}]</strong>
                    </div>
                @endif
                @if($box->account_number)
                    <div class="sm:col-span-2">
                        <span class="text-slate-500 block">رقم الحساب البنكي / الآيبان:</span>
                        <strong class="font-mono text-slate-800 font-bold block mt-0.5">{{ $box->bank_name }} - {{ $box->account_number }}</strong>
                    </div>
                @endif
            </div>

            <!-- جدول حركات الصندوق -->
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs border border-slate-300">
                    <thead class="bg-slate-800 text-white font-bold">
                        <tr>
                            <th class="py-2.5 px-3 text-center w-10">#</th>
                            <th class="py-2.5 px-3 whitespace-nowrap">التاريخ والوقت</th>
                            <th class="py-2.5 px-3 whitespace-nowrap">رقم السند</th>
                            <th class="py-2.5 px-3 whitespace-nowrap">نوع الحركة</th>
                            <th class="py-2.5 px-3">الطرف المقابل (المسدد / المستلم)</th>
                            <th class="py-2.5 px-3">البيان والشرح التفصيلي</th>
                            <th class="py-2.5 px-3 text-left whitespace-nowrap">المقبوضات (مدين +)</th>
                            <th class="py-2.5 px-3 text-left whitespace-nowrap">المدفوعات (دائن -)</th>
                            <th class="py-2.5 px-3 text-left whitespace-nowrap">الرصيد التراكمي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-mono text-[11px]">
                        <!-- سطر الرصيد الافتتاحي -->
                        <tr class="bg-slate-100 font-bold text-slate-800">
                            <td class="py-2 px-3 text-center text-slate-400 font-sans">—</td>
                            <td class="py-2 px-3 text-slate-600 font-sans">بداية الفترة</td>
                            <td class="py-2 px-3 text-slate-400 font-sans">—</td>
                            <td class="py-2 px-3 font-sans text-indigo-800">رصيد افتتاحي</td>
                            <td class="py-2 px-3 font-sans text-slate-600">رصيد عهدة / رأس مال الصندوق</td>
                            <td class="py-2 px-3 font-sans text-slate-500">القيد والرصيد الافتتاحي المعتمد للصندوق</td>
                            <td class="py-2 px-3 text-left text-slate-400">—</td>
                            <td class="py-2 px-3 text-left text-slate-400">—</td>
                            <td class="py-2 px-3 text-left font-black text-slate-900 text-xs">
                                {{ number_format($box->opening_balance, 2) }}
                            </td>
                        </tr>

                        @php
                            $runningBalance = (float)$box->opening_balance;
                        @endphp

                        @forelse($vouchers as $index => $v)
                            @php
                                if ($v->voucher_type === 'receipt') {
                                    $runningBalance += (float)$v->amount;
                                } else {
                                    $runningBalance -= (float)$v->amount;
                                }
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="py-2 px-3 text-center text-slate-500 font-sans">{{ $index + 1 }}</td>
                                <td class="py-2 px-3 text-slate-600 font-sans whitespace-nowrap">{{ $v->created_at->format('Y-m-d h:i A') }}</td>
                                <td class="py-2 px-3 font-bold text-slate-900 whitespace-nowrap">{{ $v->voucher_no }}</td>
                                <td class="py-2 px-3 font-sans font-bold whitespace-nowrap">
                                    @if($v->voucher_type === 'receipt')
                                        <span class="text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">سند قبض (إيداع)</span>
                                    @else
                                        <span class="text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">سند صرف (منصرف)</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 font-sans font-bold text-slate-900">
                                    {{ $v->account?->user?->name ?? $v->account?->account_name ?? '—' }}
                                </td>
                                <td class="py-2 px-3 font-sans text-slate-700">
                                    {{ $v->statement }}
                                </td>
                                <td class="py-2 px-3 text-left font-bold text-emerald-700 whitespace-nowrap">
                                    {{ $v->voucher_type === 'receipt' ? number_format($v->amount, 2) : '—' }}
                                </td>
                                <td class="py-2 px-3 text-left font-bold text-rose-700 whitespace-nowrap">
                                    {{ $v->voucher_type === 'payment' ? number_format($v->amount, 2) : '—' }}
                                </td>
                                <td class="py-2 px-3 text-left font-black text-slate-900 whitespace-nowrap text-xs">
                                    {{ number_format($runningBalance, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-6 text-center text-slate-400 font-sans">
                                    لا توجد سندات أو حركات مسجلة على هذا الصندوق خلال الفترة المحددة.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-100 font-bold border-t-2 border-slate-800 text-xs">
                        <tr>
                            <td colspan="6" class="py-3 px-3 text-slate-900 font-black font-sans">
                                إجمالي حركة الفترة ومحصلة الصندوق ({{ $box->currency_name }}):
                            </td>
                            <td class="py-3 px-3 text-left text-emerald-800 font-mono font-black">
                                +{{ number_format($totalReceipts, 2) }}
                            </td>
                            <td class="py-3 px-3 text-left text-rose-800 font-mono font-black">
                                -{{ number_format($totalPayments, 2) }}
                            </td>
                            <td class="py-3 px-3 text-left font-mono font-black text-slate-900 text-sm">
                                {{ number_format($runningBalance, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- ملخص الحساب والتفقيط -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div class="border border-slate-300 rounded-2xl p-4 bg-slate-50 text-xs space-y-2">
                    <span class="font-black text-slate-900 block text-xs border-b border-slate-200 pb-1">
                        📊 خلاصة حركة الصندوق:
                    </span>
                    <div class="flex justify-between">
                        <span class="text-slate-600">الرصيد الافتتاحي:</span>
                        <strong class="font-mono text-slate-900">{{ number_format($box->opening_balance, 2) }} {{ $box->currency }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">إجمالي المقبوضات (+):</span>
                        <strong class="font-mono text-emerald-700">+{{ number_format($totalReceipts, 2) }} {{ $box->currency }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">إجمالي المدفوعات (-):</span>
                        <strong class="font-mono text-rose-700">-{{ number_format($totalPayments, 2) }} {{ $box->currency }}</strong>
                    </div>
                    <div class="flex justify-between border-t border-slate-200 pt-1.5 font-bold">
                        <span class="text-slate-800">صافي الحركة المحققة:</span>
                        <strong class="font-mono text-indigo-700">{{ ($netMovement >= 0 ? '+' : '') . number_format($netMovement, 2) }} {{ $box->currency }}</strong>
                    </div>
                </div>

                <div class="border-2 border-slate-800 rounded-2xl p-4 bg-white text-xs flex flex-col justify-between">
                    <div>
                        <span class="text-slate-500 block font-bold">الرصيد الدفتري النهائي للصندوق:</span>
                        <span class="text-2xl font-black font-mono text-slate-900 block mt-1">
                            {{ number_format($runningBalance, 2) }}
                            <span class="text-xs font-sans font-normal text-slate-600">{{ $box->currency_name }}</span>
                        </span>
                    </div>
                    <div class="mt-3 pt-2 border-t border-slate-200 text-slate-700 text-[11px] font-bold">
                        <span>المبلغ كتابةً: </span>
                        <span class="text-indigo-900 font-black">
                            {{ \App\Helpers\ArabicCurrencyHelper::tafqeet((float)$runningBalance, $box->currency) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- التوقيعات والاعتماد الرسمي -->
            <div class="grid grid-cols-3 gap-4 mt-8 pt-6 border-t-2 border-slate-900 text-center text-xs font-bold">
                <div>
                    <span class="block mb-10 text-slate-500">أمين الصندوق / العهدة</span>
                    <span class="block text-slate-900 font-bold border-t border-slate-400 pt-1 mx-4">
                        {{ $box->custodian?->name ?? 'أمين الخزينة' }}
                    </span>
                </div>
                <div>
                    <span class="block mb-10 text-slate-500">ختم الأكاديمية الرسمي</span>
                    <div class="w-16 h-16 border-2 border-dashed border-slate-300 rounded-full mx-auto flex items-center justify-center text-[10px] text-slate-400">
                        الختم الرسمي
                    </div>
                </div>
                <div>
                    <span class="block mb-10 text-slate-500">المحاسب المالي / الاعتماد الإداري</span>
                    <span class="block text-slate-900 font-bold border-t border-slate-400 pt-1 mx-4">
                        {{ auth()->user()->name ?? 'الإدارة المالية' }}
                    </span>
                </div>
            </div>

            <!-- التذييل الرسمي -->
            <div class="mt-6 text-center text-[10px] text-slate-400 border-t border-slate-100 pt-3">
                تم استخراج وطباعة هذا التقرير آلياً من النظام المحاسبي الموحد • يعتبر وثيقة رسمية معتمدة للتدقيق والتسويات الجردية
            </div>

        </div>
    </div>

</body>
</html>
