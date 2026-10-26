<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إشعار إثبات مستحق مالي - {{ $entitlement->entitlement_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f8fafc; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            .voucher-box { border: 2px solid #000 !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="p-4 sm:p-6 text-slate-800">

    <div class="max-w-3xl mx-auto space-y-4">
        <!-- شريط الإجراءات العلوي (إخفاء عند الطباعة) -->
        @php
            $teacherUser = $entitlement->teacher;
            $rawPhone = $teacherUser?->phone_whatsapp ?: $teacherUser?->phone;
            $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone ?? '');
            $curName = \App\Helpers\ArabicCurrencyHelper::currencyName($entitlement->currency);
            $waText = "أستاذنا الفاضل " . $teacherUser?->name . "،\n";
            $waText .= "تم قيد وإثبات مستحق مالي بحسابكم برقم (" . $entitlement->entitlement_no . ") بمبلغ: " . number_format($entitlement->amount, 2) . " " . $curName . ".\n";
            $waText .= "الموضوع: " . $entitlement->title . "\n";
            if ($entitlement->statement) {
                $waText .= "البيان: " . $entitlement->statement . "\n";
            }
            $waText .= "يمكنكم استعراض وطباعة إشعار القيد رسمياً بصيغة PDF عبر الرابط:\n" . route('accounting.teacher_entitlement.print', $entitlement->id);
            $waUrl = $cleanPhone ? "https://wa.me/{$cleanPhone}?text=" . urlencode($waText) : "https://wa.me/?text=" . urlencode($waText);
        @endphp
        <div class="no-print flex flex-col sm:flex-row justify-between items-center gap-3 bg-slate-900 text-white p-4 rounded-2xl shadow-md">
            <div>
                <span class="font-bold text-sm block">إشعار إثبات قيد مستحقات مالية للمعلم</span>
                <span class="text-xs text-slate-400">مستند رسمي معتمد لإثبات استحقاق تدريس أو خدمة تعليمية</span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('accounting.dashboard') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition">
                    ← لوحة المحاسبة
                </a>
                <a href="{{ $waUrl }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition flex items-center gap-1.5 shadow-sm">
                    <span>💬</span>
                    <span>إرسال لواتساب المعلم</span>
                </a>
                <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                    <span>🖨️</span>
                    <span>طباعة الإشعار / PDF</span>
                </button>
            </div>
        </div>

        <!-- ورقة الإشعار الرسمية -->
        <div class="voucher-box bg-white border-2 border-slate-900 rounded-3xl p-6 sm:p-10 shadow-xl space-y-6">
            
            <!-- رأس الإشعار -->
            <div class="flex flex-col sm:flex-row justify-between items-start border-b-2 border-slate-900 pb-5 gap-4">
                <div>
                    <h1 class="text-base sm:text-lg font-black text-slate-900">مكتب النجم الآسيوي للترجمة والتدريب والاستشارات (ASOTS)</h1>
                    <span class="text-xs font-bold text-slate-500 block">The Asian Star Office for Translation, Training, & Consultancy Services</span>
                    <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-md inline-block mt-1 border border-indigo-200">
                        الإدارة المالية والحسابات • قسم مستحقات وأجور الكادر التدريسي
                    </span>
                </div>
                <div class="text-left font-mono shrink-0">
                    <div class="bg-slate-100 border border-slate-300 px-3 py-1.5 rounded-xl text-xs mb-1">
                        <span class="font-sans font-bold text-slate-600">رقم القيد:</span>
                        <strong class="text-indigo-800 text-sm mr-1">{{ $entitlement->entitlement_no }}</strong>
                    </div>
                    @if($entitlement->manual_no)
                        <div class="text-[11px] text-slate-500">
                            <span class="font-sans">الرقم المرجعي / الدفتري:</span>
                            <strong class="font-mono text-slate-700">{{ $entitlement->manual_no }}</strong>
                        </div>
                    @endif
                    <div class="text-[11px] text-slate-500 mt-0.5">
                        <span class="font-sans">تاريخ القيد:</span>
                        <strong class="font-mono text-slate-800">{{ $entitlement->due_date ? \Carbon\Carbon::parse($entitlement->due_date)->format('Y/m/d') : $entitlement->created_at->format('Y/m/d') }}</strong>
                    </div>
                </div>
            </div>

            <!-- العنوان والبطاقة المالية -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gradient-to-r from-slate-900 to-indigo-950 text-white p-5 rounded-2xl shadow-sm">
                <div>
                    <span class="text-[11px] text-indigo-200 block font-bold">مستند قيد محاسبي دائن</span>
                    <h2 class="text-lg font-black mt-0.5">إشعار إثبات مستحق مالي (Accrual Voucher)</h2>
                    <span class="text-xs text-indigo-100 block mt-1">
                        {{ $entitlement->title }}
                    </span>
                </div>

                <div class="bg-white/10 backdrop-blur-sm border border-white/20 px-5 py-3 rounded-xl text-left shrink-0">
                    <span class="text-[10px] text-indigo-200 block font-bold">المبلغ المستحق المعتمد:</span>
                    <span class="text-2xl font-black font-mono text-emerald-400">
                        {{ number_format($entitlement->amount, 2) }}
                        <span class="text-xs font-sans text-white">{{ $entitlement->currency }}</span>
                    </span>
                    <span class="text-[10px] text-indigo-200 block text-right font-bold">
                        {{ \App\Helpers\ArabicCurrencyHelper::currencyName($entitlement->currency) }}
                    </span>
                </div>
            </div>

            <!-- تفاصيل القيد وبيانات المعلم -->
            <div class="space-y-4 text-xs border border-slate-200 rounded-2xl p-5 bg-slate-50/50">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-3 border-b border-slate-200">
                    <div>
                        <span class="font-bold text-slate-500 block mb-0.5">المستفيد (المعلم / المدرب):</span>
                        <span class="font-black text-slate-900 text-sm block">{{ $entitlement->teacher?->name }}</span>
                        <span class="text-[11px] text-slate-500 font-mono">{{ $entitlement->teacher?->email }} • {{ $entitlement->teacher?->phone ?: 'لا يوجد هاتف' }}</span>
                    </div>

                    <div>
                        <span class="font-bold text-slate-500 block mb-0.5">رقم الحساب المالي للمعلم:</span>
                        <span class="font-mono font-black text-indigo-700 text-sm block">
                            {{ $entitlement->teacher?->financialAccount?->account_code ?: ('22' . str_pad($entitlement->teacher_id, 4, '0', STR_PAD_LEFT)) }}
                        </span>
                        <span class="text-[11px] text-slate-500">حساب الأستاذ المساعد للكادر التدريسي</span>
                    </div>
                </div>

                <!-- تفاصيل المهمة أو الدورة -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pb-3 border-b border-slate-200">
                    <div>
                        <span class="font-bold text-slate-500 block mb-0.5">نوع الاستحقاق:</span>
                        <span class="px-2.5 py-1 bg-indigo-100 text-indigo-900 font-black rounded-lg text-[11px] inline-block">
                            {{ $entitlement->type_label }}
                        </span>
                    </div>

                    <div>
                        <span class="font-bold text-slate-500 block mb-0.5">المجموعة الدراسية / الدورة:</span>
                        <span class="font-bold text-slate-800 text-[11px] block">
                            @if($entitlement->studyGroup)
                                {{ $entitlement->studyGroup->name }}
                                @if($entitlement->studyGroup->course)
                                    <span class="text-slate-500 block text-[10px]">({{ $entitlement->studyGroup->course->title }})</span>
                                @endif
                            @elseif($entitlement->course)
                                {{ $entitlement->course->title }}
                            @else
                                <span class="text-slate-400">غير مرتبطة بمجموعة محددة</span>
                            @endif
                        </span>
                    </div>

                    <div>
                        <span class="font-bold text-slate-500 block mb-0.5">الفترة المحاسبية / الشهر:</span>
                        <span class="font-mono font-bold text-slate-800 text-[11px] block">
                            {{ $entitlement->period ?: date('Y-m') }}
                        </span>
                    </div>
                </div>

                <!-- المبلغ كتابة وتفقيط -->
                <div class="flex items-start gap-2 pt-1">
                    <span class="font-bold text-slate-600 shrink-0">المبلغ كتابة:</span>
                    <span class="font-black text-indigo-950 bg-indigo-50/70 border border-indigo-200 px-3 py-1 rounded-lg flex-1">
                        {{ \App\Helpers\ArabicCurrencyHelper::tafqeet((float)$entitlement->amount, $entitlement->currency) }}
                    </span>
                </div>

                <!-- البيان التفصيلي -->
                <div class="pt-2">
                    <span class="font-bold text-slate-600 block mb-1">البيان والشرح التفصيلي للعملية:</span>
                    <div class="bg-white border border-slate-200 rounded-xl p-3 text-slate-800 font-sans leading-relaxed">
                        {{ $entitlement->statement }}
                    </div>
                </div>

            </div>

            <!-- بطاقة الأثر المالي على رصيد المعلم -->
            <div class="bg-slate-100 p-4 rounded-2xl border border-slate-200 flex flex-col sm:flex-row items-center justify-between text-xs gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-700">تم قيد هذا المبلغ تلقائياً كاستحقاق مالي دائن (له) في حساب المعلم بالنظام.</span>
                </div>
                <div class="font-mono text-slate-900 font-bold shrink-0">
                    الرصيد المحدث الحالي: 
                    <span class="text-indigo-700 font-black text-sm">
                        {{ number_format($entitlement->teacher?->financialAccount?->current_balance ?? 0, 2) }}
                    </span> 
                    {{ $entitlement->currency }}
                </div>
            </div>

            <!-- التوقيعات والاعتمادات الرسمية -->
            <div class="pt-6 border-t-2 border-slate-900 grid grid-cols-3 gap-6 text-center text-xs">
                <div>
                    <span class="text-slate-500 font-bold block mb-10">الموظف المختص (معد القيد)</span>
                    <span class="font-bold text-slate-800 block">{{ $entitlement->creator?->name ?? 'الإدارة المالية' }}</span>
                    <span class="border-b-2 border-dashed border-slate-400 block w-28 mx-auto mt-2"></span>
                </div>

                <div>
                    <span class="text-slate-500 font-bold block mb-10">المدير المالي والتدقيق</span>
                    <span class="font-bold text-slate-800 block">الإدارة المالية</span>
                    <span class="border-b-2 border-dashed border-slate-400 block w-28 mx-auto mt-2"></span>
                </div>

                <div>
                    <span class="text-slate-500 font-bold block mb-10">اعتماد إدارة المركز والختم</span>
                    <div class="w-16 h-16 rounded-full border-2 border-dashed border-indigo-400 mx-auto flex items-center justify-center text-[9px] text-indigo-400 font-bold">
                        الختم الرسمي
                    </div>
                </div>
            </div>

        </div>

        <div class="no-print text-center text-slate-400 text-xs">
            منظومة ASOTS الذكية • نظام الإدارة المالية واستحقاقات الكادر التعليمي
        </div>
    </div>

</body>
</html>
