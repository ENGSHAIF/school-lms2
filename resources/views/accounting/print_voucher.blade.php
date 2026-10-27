<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند قبض رسمي - {{ $voucher->voucher_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f8fafc; }
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            .voucher-box { border: 2px solid #000 !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="p-6">

    <div class="max-w-2xl mx-auto">
        <!-- شريط الطباعة والواتساب العلوي -->
        @php
            $recipientUser = $voucher->account?->user;
            $rawPhone = $recipientUser?->phone_whatsapp ?: $recipientUser?->phone;
            $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone ?? '');
            $curName = $voucher->currency_name ?? $voucher->currency;
            $vType = $voucher->voucher_type === 'receipt' ? 'القبض' : 'الصرف';
            $waText = "مرحباً " . ($recipientUser?->name ?? $voucher->account?->account_name) . "،\n";
            $waText .= "تم إصدار سند " . $vType . " رسمي برقم (" . $voucher->voucher_no . ") بمبلغ: " . number_format($voucher->amount, 2) . " " . $curName . ".\n";
            if ($voucher->statement) {
                $waText .= "البيان: " . $voucher->statement . "\n";
            }
            $waText .= "يمكنكم معاينة وطباعة السند رسمياً بصيغة PDF عبر الرابط المعتمد:\n" . route('accounting.voucher.print', $voucher->id);
            $waUrl = $cleanPhone ? "https://wa.me/{$cleanPhone}?text=" . urlencode($waText) : "https://wa.me/?text=" . urlencode($waText);
        @endphp
        <div class="no-print mb-4 flex flex-col sm:flex-row justify-between items-center gap-3 bg-slate-900 text-white p-4 rounded-xl shadow-md">
            <div>
                <span class="font-bold text-sm block">سند {{ $vType }} مالي رسمي معتمد</span>
                <span class="text-xs text-slate-400">يمكنك حفظه بصيغة PDF أو طباعته أو إرساله مباشرة لواتساب المستفيد</span>
            </div>
            <div class="flex items-center gap-2">
                @if($voucher->journalEntry)
                    <a href="{{ route('accounting.journal_entries.show', $voucher->journalEntry->id) }}" target="_blank" class="bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs px-3.5 py-2.5 rounded-lg transition flex items-center gap-1.5 shadow-sm" title="معاينة القيد المحاسبي المزدوج في دفتر اليومية">
                        <span>⚖️</span>
                        <span>القيد المحاسبي ({{ $voucher->journalEntry->entry_no }})</span>
                    </a>
                @endif
                <a href="{{ $waUrl }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-lg transition flex items-center gap-1.5 shadow-sm">
                    <span>💬</span>
                    <span>إرسال لواتساب {{ $recipientUser?->name ? ('(' . $recipientUser->name . ')') : '' }}</span>
                </a>
                <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-2.5 rounded-lg transition flex items-center gap-2 shadow-sm">
                    <span>🖨️</span>
                    <span>طباعة / Save PDF</span>
                </button>
            </div>
        </div>

        <!-- ورقة السند الرسمية -->
        <div class="voucher-box bg-white border-2 border-slate-800 rounded-2xl p-8 shadow-xl text-slate-900">
            
            <!-- رأس السند -->
            <div class="flex justify-between items-start border-b-2 border-slate-800 pb-5 mb-5">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="ASOTS Logo" class="w-14 h-14 object-contain rounded-xl border border-slate-200 p-0.5 shrink-0">
                    <div>
                        <h1 class="text-base font-black tracking-wide text-slate-900">The Asian Star Office for Translation, Training, & Consultancy Services (ASOTS)</h1>
                        <span class="text-xs text-slate-600 block font-bold">مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات • الإدارة المالية والحسابات</span>
                    </div>
                </div>
                <div class="text-left font-mono">
                    <div class="bg-slate-100 border border-slate-300 px-3 py-1 rounded text-xs mb-1">
                        <span class="font-bold">رقم السند:</span>
                        <strong class="text-indigo-700">{{ $voucher->voucher_no }}</strong>
                    </div>
                    @if($voucher->manual_no)
                        <div class="text-[11px] text-slate-500">
                            <span>الرقم الدفتري:</span>
                            <strong>{{ $voucher->manual_no }}</strong>
                        </div>
                    @endif
                    <div class="text-[11px] text-slate-500 mt-1">
                        <span>التاريخ:</span>
                        <strong>{{ $voucher->created_at->format('Y/m/d') }}</strong>
                    </div>
                </div>
            </div>

            <!-- عنوان السند والمبلغ -->
            <div class="flex justify-between items-center my-6">
                @if($voucher->voucher_type === 'receipt')
                    <div class="bg-slate-900 text-white text-base font-black px-6 py-2 rounded-xl">
                        ســـند قـــبـض نـقـدي (Receipt Voucher)
                    </div>
                    <div class="border-2 border-emerald-600 bg-emerald-50 px-5 py-2 rounded-xl text-left font-mono">
                        <span class="text-xs font-bold text-emerald-800 block">المبلغ المقبوض:</span>
                        <span class="text-xl font-black text-emerald-700">
                            @if($voucher->currency === 'USD')
                                ${{ number_format($voucher->amount, 2) }}
                            @else
                                {{ number_format($voucher->amount, 2) }} {{ $voucher->currency_symbol }}
                            @endif
                        </span>
                        <span class="text-[10px] text-emerald-800 font-sans block text-right font-bold">{{ $voucher->currency_name }}</span>
                    </div>
                @else
                    <div class="bg-rose-900 text-white text-base font-black px-6 py-2 rounded-xl">
                        ســـند صـــرف مــالـي (Payment Voucher)
                    </div>
                    <div class="border-2 border-rose-600 bg-rose-50 px-5 py-2 rounded-xl text-left font-mono">
                        <span class="text-xs font-bold text-rose-800 block">المبلغ المصروف:</span>
                        <span class="text-xl font-black text-rose-700">
                            @if($voucher->currency === 'USD')
                                ${{ number_format($voucher->amount, 2) }}
                            @else
                                {{ number_format($voucher->amount, 2) }} {{ $voucher->currency_symbol }}
                            @endif
                        </span>
                        <span class="text-[10px] text-rose-800 font-sans block text-right font-bold">{{ $voucher->currency_name }}</span>
                    </div>
                @endif
            </div>

            <!-- تفاصيل السند -->
            <div class="space-y-4 text-xs border border-slate-200 rounded-xl p-5 bg-slate-50/50">
                <div class="flex items-center">
                    <span class="font-bold text-slate-600 w-36">
                        {{ $voucher->voucher_type === 'receipt' ? 'استلمنا من الأخ/الجهة:' : 'صرفنا للأخ/المستفيد:' }}
                    </span>
                    <span class="font-black text-slate-900 text-sm border-b border-dotted border-slate-400 flex-1 pb-1">
                        @if($voucher->is_compound && $voucher->items->count() > 1)
                            {{ $voucher->account?->user?->name ?? $voucher->account?->account_name }} وآخرون ({{ $voucher->items->count() }} أطراف مفصلة بالجدول أدناه)
                        @else
                            {{ $voucher->account?->user?->name ?? $voucher->account?->account_name }}
                        @endif
                    </span>
                </div>

                <div class="flex items-center">
                    <span class="font-bold text-slate-600 w-36">مبلغ وقدره (كتابة):</span>
                    <span class="font-bold text-slate-900 border-b border-dotted border-slate-400 flex-1 pb-1">
                        {{ $voucher->amount_in_words ?? \App\Helpers\ArabicCurrencyHelper::tafqeet((float)$voucher->amount, $voucher->currency) }}
                    </span>
                </div>

                <div class="flex items-center">
                    <span class="font-bold text-slate-600 w-36">
                        {{ $voucher->voucher_type === 'receipt' ? 'أودع في حساب/صندوق:' : 'صُرف من حساب/صندوق:' }}
                    </span>
                    <span class="font-bold text-slate-900 border-b border-dotted border-slate-400 flex-1 pb-1">
                        @if($voucher->cashOrBankAccount)
                            {{ $voucher->cashOrBankAccount->account_name }} (كود: {{ $voucher->cashOrBankAccount->account_code }})
                        @else
                            الصندوق الرئيسي
                        @endif
                    </span>
                </div>

                <div class="flex items-center">
                    <span class="font-bold text-slate-600 w-36">طريقة الدفع / الصرف:</span>
                    <span class="font-bold text-slate-900 border-b border-dotted border-slate-400 flex-1 pb-1">
                        {{ $voucher->payment_method === 'cash' ? 'نقداً عبر الصندوق' : ($voucher->payment_method === 'bank_transfer' ? 'حوالة / إيداع بنكي' : 'محفظة إلكترونية') }}
                    </span>
                </div>

                <div class="flex items-center">
                    <span class="font-bold text-slate-600 w-36">وذلك مقابل / البيان:</span>
                    <span class="font-bold text-slate-900 border-b border-dotted border-slate-400 flex-1 pb-1">
                        {{ $voucher->statement }}
                    </span>
                </div>
            </div>

            <!-- جدول تفاصيل البنود عند وجود سند مركب (ماستر / ديتيل) -->
            @if($voucher->is_compound && $voucher->items->count() > 1)
                <div class="mt-5 border border-slate-300 rounded-xl overflow-hidden">
                    <div class="bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700 border-b border-slate-300 flex justify-between items-center">
                        <span class="flex items-center gap-1.5">
                            <span>📋</span>
                            <span>كشف تفاصيل البنود والجهات المقيدة بالسند (Detail Items)</span>
                        </span>
                        <span class="font-mono text-[11px] bg-white px-2 py-0.5 rounded border border-slate-200">عدد الأطراف: {{ $voucher->items->count() }}</span>
                    </div>
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                            <tr>
                                <th class="p-2.5 w-10 text-center">#</th>
                                <th class="p-2.5">{{ $voucher->voucher_type === 'receipt' ? 'اسم الطالب المسدد / الحساب' : 'المستفيد / الحساب' }}</th>
                                <th class="p-2.5">البيان والشرح الخاص بالبند</th>
                                <th class="p-2.5 text-left font-mono">المبلغ ({{ $voucher->currency_symbol }})</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($voucher->items as $idx => $item)
                                <tr>
                                    <td class="p-2.5 text-center font-mono text-slate-500">{{ $idx + 1 }}</td>
                                    <td class="p-2.5 font-bold text-slate-900">{{ $item->account?->user?->name ?? $item->account?->account_name }}</td>
                                    <td class="p-2.5 text-slate-700">{{ $item->statement ?: $voucher->statement }}</td>
                                    <td class="p-2.5 text-left font-mono font-bold text-slate-900">{{ number_format($item->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-100 font-black border-t-2 border-slate-400">
                            <tr>
                                <td colspan="3" class="p-2.5 text-right font-bold text-slate-800">الإجمالي العام المطابق للسند:</td>
                                <td class="p-2.5 text-left font-mono text-sm text-indigo-900">{{ number_format($voucher->amount, 2) }} {{ $voucher->currency_symbol }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

            <!-- التوقيعات والختم -->
            <div class="grid grid-cols-3 gap-4 mt-10 pt-6 border-t-2 border-slate-800 text-center text-xs font-bold">
                <div>
                    <span class="block mb-8 text-slate-500">
                        {{ $voucher->voucher_type === 'receipt' ? 'توقيع المستلم (المحاسب)' : 'توقيع الصارف (المحاسب/أمين الصندوق)' }}
                    </span>
                    <span class="block text-slate-900 font-bold">{{ $voucher->creator?->name ?? 'المحاسب المالي' }}</span>
                </div>
                <div>
                    <span class="block mb-8 text-slate-500">ختم الأكاديمية الرسمي</span>
                    <div class="w-16 h-16 border-2 border-dashed border-slate-300 rounded-full mx-auto flex items-center justify-center text-[10px] text-slate-400">
                        الختم
                    </div>
                </div>
                <div>
                    <span class="block mb-8 text-slate-500">
                        {{ $voucher->voucher_type === 'receipt' ? 'توقيع الطالب / المودع' : 'توقيع المستلم / المستفيد' }}
                    </span>
                    <span class="block text-slate-900 font-bold">
                        @if($voucher->is_compound && $voucher->items->count() > 1)
                            المسددون / المستفيدون
                        @else
                            {{ $voucher->account?->user?->name ?? $voucher->account?->account_name }}
                        @endif
                    </span>
                </div>
            </div>

            <!-- إشعار في الأسفل -->
            <div class="mt-8 text-center text-[10px] text-slate-400">
                تم إصدار هذا السند آلياً من النظام المحاسبي الموحد • يعتبر هذا السند إيصالاً رسمياً بالسداد
            </div>

        </div>
    </div>

</body>
</html>
