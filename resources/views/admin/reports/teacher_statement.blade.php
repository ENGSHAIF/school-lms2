<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف حساب وتحركات المعلم: {{ $teacher->name }} - ASOTS</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-break-inside-avoid { break-inside: avoid; }
            .print-border-b { border-bottom: 2px solid #0f172a !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-3 sm:p-8">

    <div class="max-w-5xl mx-auto space-y-6">

        <!-- شريط الإجراءات العلوي (إخفاء عند الطباعة) -->
        <div class="no-print bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('accounting.dashboard') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                    <span>←</span>
                    <span>لوحة المحاسبة</span>
                </a>
                <span class="text-xs text-slate-400 font-bold">|</span>
                <span class="text-xs font-black text-slate-900">كشف حساب المعلم والحركة المالية والأكاديمية (Teacher Statement)</span>
            </div>

            @php
                $rawTeacherPhone = $teacher->phone_whatsapp ?: $teacher->phone;
                $cleanTeacherPhone = preg_replace('/[^0-9]/', '', $rawTeacherPhone ?? '');
                $waStmtText = "أستاذنا القدير " . $teacher->name . "،\n";
                $waStmtText .= "مرفق لكم كشف الحساب المالي والأكاديمي المعتمد من منصة ASOTS.\n";
                $waStmtText .= "• إجمالي المستحقات المعتمدة (له): " . number_format($totalEntitlements, 2) . " " . $currency . "\n";
                $waStmtText .= "• إجمالي السندات المسلمة (عليه): " . number_format($totalPaidVouchers, 2) . " " . $currency . "\n";
                $waStmtText .= "• صافي الرصيد الحالي: " . number_format(abs($netBalance), 2) . " " . $currency . " (" . $netBalanceWords . ")\n";
                $waStmtText .= "يمكنكم استعراض وطباعة الكشف الرسمي بصيغة PDF عبر الرابط المعتمد:\n" . route('accounting.teacher.statement', $teacher->id);
                $waStmtUrl = $cleanTeacherPhone ? "https://wa.me/{$cleanTeacherPhone}?text=" . urlencode($waStmtText) : "https://wa.me/?text=" . urlencode($waStmtText);
            @endphp
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ $waStmtUrl }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                    <span>💬</span>
                    <span>إرسال الكشف لواتساب المعلم</span>
                </a>
                <a href="{{ route('accounting.dashboard') }}#entitlementsTab" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl border border-indigo-200 transition flex items-center gap-1.5">
                    <span>💼</span>
                    <span>إثبات مستحق مالي</span>
                </a>
                <a href="{{ route('accounting.pricing.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                    <span>🏷️</span>
                    <span>تسعيرة الدورات والعقود</span>
                </a>
                <button onclick="window.print()" class="px-5 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-2">
                    <span>🖨️</span>
                    <span>طباعة كشف الحساب / PDF</span>
                </button>
            </div>
        </div>

        <!-- الورقة الرسمية المعتمدة لكشف الحساب -->
        <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-lg border border-slate-200 print:shadow-none print:border-none print:p-0 space-y-8">

            <!-- 1. ترويسة المركز الرسمية -->
            <div class="border-b-2 border-slate-900 pb-6 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-right space-y-1">
                    <h2 class="text-lg font-black text-slate-900">مكتب النجم الآسيوي للترجمة والتدريب والاستشارات</h2>
                    <h3 class="text-xs font-mono font-bold text-slate-500 uppercase tracking-wider">The Asian Star Office for Translation, Training, & Consultancy Services (ASOTS)</h3>
                    <p class="text-[11px] text-slate-500 font-bold">منصة التدريب وإدارة المعلمين • كشف حساب مالي وأكاديمي شامل ومفصل</p>
                </div>

                <div class="text-center md:text-left bg-slate-50 p-4 rounded-2xl border border-slate-200 shrink-0">
                    <span class="text-[10px] text-slate-400 block font-bold">تاريخ إصدار الكشف</span>
                    <span class="text-sm font-black font-mono text-slate-900">{{ date('Y/m/d - H:i') }}</span>
                    <span class="text-[10px] text-indigo-700 font-bold bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200 block mt-1">
                        كشف حساب رسمي معتمد
                    </span>
                </div>
            </div>

            <!-- 2. بيانات المعلم الإدارية والتعاقدية -->
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white font-black text-2xl flex items-center justify-center shadow-md shrink-0">
                            {{ mb_substr($teacher->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[10px] bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded-full font-bold">كادر التدريس والمدربين</span>
                                @if($contract?->contract_type === 'fixed_salary')
                                    <span class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-black border border-emerald-300">
                                        💼 موظف رسمي براتب ثابت: {{ number_format($contract->monthly_salary) }} {{ $contract->currency }}
                                    </span>
                                @elseif($contract?->contract_type === 'hourly')
                                    <span class="text-[10px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full font-bold border border-amber-300">
                                        ⏱️ متعاقد بالساعة: {{ number_format($contract->default_hourly_rate) }} {{ $contract->currency }}
                                    </span>
                                @else
                                    <span class="text-[10px] bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full font-bold border border-blue-300">
                                        📚 متعاقد بحسب الدورات المنجزة
                                    </span>
                                @endif
                            </div>
                            <h1 class="text-xl font-black text-slate-900 mt-1">{{ $teacher->name }}</h1>
                            <p class="text-xs text-slate-500 font-mono mt-0.5">
                                {{ $teacher->email }} • {{ $teacher->phone ?: 'لا يوجد هاتف مسجل' }} • رقم الحساب: {{ $account?->account_code ?: ('22' . str_pad($teacher->id, 4, '0', STR_PAD_LEFT)) }}
                            </p>
                        </div>
                    </div>

                    <!-- إحصائيات تدريس المعلم -->
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm">
                            <span class="text-[10px] text-slate-400 block font-bold">مجموعات رئيسية</span>
                            <span class="text-sm font-black text-indigo-700 font-mono">{{ $primaryGroupsCount }}</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm">
                            <span class="text-[10px] text-slate-400 block font-bold">مجموعات مساعدة</span>
                            <span class="text-sm font-black text-amber-600 font-mono">{{ $asstGroupsCount }}</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm">
                            <span class="text-[10px] text-slate-400 block font-bold">الطلاب المدرسين</span>
                            <span class="text-sm font-black text-emerald-600 font-mono">{{ $totalStudentsTaught }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. البطاقات المالية الإجمالية (Financial KPI Summary) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100/50 p-4 rounded-2xl border border-indigo-200">
                    <span class="text-[11px] font-bold text-indigo-900 block">إجمالي المستحقات المعتمدة (له / دائن)</span>
                    <div class="text-xl font-black text-indigo-700 font-mono mt-1">
                        {{ number_format($totalEntitlements, 2) }}
                        <span class="text-xs font-sans text-indigo-600">{{ $currency }}</span>
                    </div>
                    <span class="text-[10px] text-indigo-500 block mt-1">مستحقات تدريس الدورات والخدمات والرواتب</span>
                </div>

                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 p-4 rounded-2xl border border-emerald-200">
                    <span class="text-[11px] font-bold text-emerald-900 block">إجمالي السندات المصروفة (عليه / مدين)</span>
                    <div class="text-xl font-black text-emerald-700 font-mono mt-1">
                        {{ number_format($totalPaidVouchers, 2) }}
                        <span class="text-xs font-sans text-emerald-600">{{ $currency }}</span>
                    </div>
                    <span class="text-[10px] text-emerald-600 block mt-1">سندات الصرف والتحويلات المسددة للمعلم</span>
                </div>

                <div class="bg-gradient-to-br {{ $netBalance > 0 ? 'from-amber-50 to-amber-100/50 border-amber-200' : ($netBalance < 0 ? 'from-rose-50 to-rose-100 border-rose-200' : 'from-slate-50 to-slate-100 border-slate-200') }} p-4 rounded-2xl border">
                    <span class="text-[11px] font-bold {{ $netBalance > 0 ? 'text-amber-900' : ($netBalance < 0 ? 'text-rose-900' : 'text-slate-800') }} block">
                        صافي الرصيد المستحق للمعلم
                    </span>
                    <div class="text-xl font-black {{ $netBalance > 0 ? 'text-amber-700' : ($netBalance < 0 ? 'text-rose-700' : 'text-slate-900') }} font-mono mt-1">
                        {{ number_format(abs($netBalance), 2) }}
                        <span class="text-xs font-sans">{{ $currency }}</span>
                        @if($netBalance < 0)
                            <span class="text-xs font-bold text-rose-600">(مدين للمعهد)</span>
                        @endif
                    </div>
                    <span class="text-[10px] {{ $netBalance > 0 ? 'text-amber-700 font-bold' : ($netBalance < 0 ? 'text-rose-700 font-bold' : 'text-slate-500') }} block mt-1">
                        {{ $netBalance > 0 ? 'متبقي في ذمة المعهد للمعلم' : ($netBalance < 0 ? 'منصرف زيادة في ذمة المعلم' : 'الحساب خالص ومسدد بالكامل') }}
                    </span>
                    <div class="mt-2 text-[10px] font-bold text-slate-700 bg-white/80 p-1.5 rounded-lg border border-slate-200">
                        {{ $netBalanceWords }}
                    </div>
                </div>
            </div>

            <!-- 4. المجموعات والدورات المسندة للمعلم -->
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <span>👥</span>
                        <span>المجموعات الدراسية والدفعات المسندة للمعلم</span>
                    </h3>
                    <span class="text-xs text-slate-500 font-bold">عدد المجموعات: {{ $teachingGroups->count() }}</span>
                </div>

                @if($teachingGroups->isEmpty())
                    <div class="text-center py-6 bg-slate-50 rounded-2xl text-slate-400 text-xs">
                        لا توجد مجموعات مسندة لهذا المعلم حتى الآن.
                    </div>
                @else
                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full text-right text-xs">
                            <thead class="bg-slate-100 text-slate-600 font-bold border-b border-slate-200">
                                <tr>
                                    <th class="p-3">#</th>
                                    <th class="p-3">اسم المجموعة</th>
                                    <th class="p-3">الدورة والمستوى</th>
                                    <th class="p-3">الترم / الدفعة</th>
                                    <th class="p-3">دور المعلم</th>
                                    <th class="p-3">المدة والتواريخ</th>
                                    <th class="p-3 text-center">عدد الطلاب</th>
                                    <th class="p-3 text-center">حالة المجموعة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($teachingGroups as $idx => $grp)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-3 font-mono text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="p-3 font-bold text-slate-900">
                                        {{ $grp->name }}
                                        @if($grp->program_type)
                                            <span class="block text-[10px] text-indigo-600 font-normal">{{ $grp->program_type }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-slate-700">
                                        <span class="font-bold">{{ $grp->level->course->title ?? '-' }}</span>
                                        <span class="block text-[10px] text-slate-500">{{ $grp->level->title ?? '-' }}</span>
                                    </td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-1 flex-wrap">
                                            @if($grp->term)
                                                <span class="px-2 py-0.5 bg-blue-50 text-blue-800 rounded font-bold text-[10px] border border-blue-200">
                                                    {{ $grp->term }}
                                                </span>
                                            @endif
                                            @if($grp->batch)
                                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded font-bold text-[10px]">
                                                    {{ $grp->batch }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        @if(($grp->pivot && $grp->pivot->is_primary) || ($grp->teacher_id == $teacher->id))
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-black text-[10px]">
                                                ⭐ المعلم الرئيسي
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-full font-bold text-[10px]">
                                                مساعد: {{ ($grp->pivot ? $grp->pivot->role_title : null) ?: 'معلم مشارك' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-slate-500 text-[11px] font-mono">
                                        {{ $grp->duration_months ? $grp->duration_months . ' أشهر' : 'غير محدد' }}
                                        @if($grp->start_date)
                                            <span class="block text-[10px] text-slate-400">من {{ \Carbon\Carbon::parse($grp->start_date)->format('Y/m/d') }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center font-bold text-slate-800 font-mono">
                                        {{ $grp->students ? $grp->students->count() : ($grp->students_count ?? 0) }} طالب
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $grp->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $grp->status === 'active' ? 'نشطة حالياً' : 'مكتملة/مغلقة' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- 5. كشف الحساب التحليلي وحركة العمليات (Chronological Financial Movement) -->
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <span>📊</span>
                        <span>كشف الحساب التحليلي للحركة المالية (المستحقات والسندات بتواريخها)</span>
                    </h3>
                    <span class="text-xs text-slate-500 font-bold">عدد العمليات: {{ count($ledger) }}</span>
                </div>

                @if(empty($ledger))
                    <div class="text-center py-6 bg-slate-50 rounded-2xl text-slate-400 text-xs">
                        لا توجد حركات مالية مسجلة للمعلم في النظام حتى تاريخه.
                    </div>
                @else
                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full text-right text-xs">
                            <thead class="bg-slate-100 text-slate-600 font-bold border-b border-slate-200">
                                <tr>
                                    <th class="p-3">#</th>
                                    <th class="p-3">التاريخ</th>
                                    <th class="p-3">نوع العملية</th>
                                    <th class="p-3">البيان والتفاصيل</th>
                                    <th class="p-3">رقم المرجع / المجموعة</th>
                                    <th class="p-3 text-emerald-700 font-black">له (مستحقات)</th>
                                    <th class="p-3 text-rose-700 font-black">عليه (سندات صرف)</th>
                                    <th class="p-3 text-slate-900 font-black">الرصيد التراكمي</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono">
                                @php $currentBalance = 0; @endphp
                                @foreach($ledger as $idx => $item)
                                @php
                                    $currentBalance += ($item['credit'] - $item['debit']);
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-3 text-slate-400 text-[11px]">{{ $idx + 1 }}</td>
                                    <td class="p-3 text-slate-600 text-[11px] whitespace-nowrap">{{ $item['date'] }}</td>
                                    <td class="p-3 font-sans">
                                        @if($item['type'] === 'entitlement')
                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded font-bold text-[10px]">
                                                💼 استحقاق أتعاب
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded font-bold text-[10px]">
                                                🧾 سند صرف نقد/شيك
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 font-sans font-bold text-slate-900 text-[11px]">
                                        {{ $item['description'] }}
                                    </td>
                                    <td class="p-3 text-slate-500 font-sans text-[11px]">
                                        {{ $item['reference'] }}
                                    </td>
                                    <td class="p-3 text-emerald-700 font-black font-mono">
                                        {{ $item['credit'] > 0 ? number_format($item['credit'], 2) : '-' }}
                                    </td>
                                    <td class="p-3 text-rose-700 font-black font-mono">
                                        {{ $item['debit'] > 0 ? number_format($item['debit'], 2) : '-' }}
                                    </td>
                                    <td class="p-3 font-black text-slate-900 font-mono">
                                        {{ number_format($currentBalance, 2) }} {{ $currency }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-300">
                                <tr>
                                    <td colspan="5" class="p-3 text-right font-black font-sans text-slate-900">
                                        الإجماليات وصافي رصيد الحساب حتى تاريخه:
                                    </td>
                                    <td class="p-3 text-emerald-700 font-black font-mono">
                                        {{ number_format($totalEntitlements, 2) }}
                                    </td>
                                    <td class="p-3 text-rose-700 font-black font-mono">
                                        {{ number_format($totalPaidVouchers, 2) }}
                                    </td>
                                    <td class="p-3 text-amber-700 font-black font-mono">
                                        {{ number_format($netBalance, 2) }} {{ $currency }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>

            <!-- 6. الملاحظات والتوقيعات الرسمية المعتمدة -->
            <div class="print-break-inside-avoid pt-6 border-t-2 border-slate-900 space-y-6">
                <div class="text-xs text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <span class="font-bold text-slate-900 block mb-1">إقرار وتنويه مالي:</span>
                    يُعتبر هذا الكشف مستنداً رسمياً يعبر عن كامل حركة المستحقات والمدفوعات الخاصة بالمعلم في النظام. لأي استفسار أو مراجعة يرجى مراجعة الإدارة المالية خلال أسبوع من تاريخ إصدار الكشف.
                </div>

                <div class="grid grid-cols-3 gap-6 text-center pt-4">
                    <div>
                        <span class="text-xs text-slate-500 font-bold block mb-8">توقيع المعلم / المدرب</span>
                        <span class="border-b-2 border-dashed border-slate-400 block w-32 mx-auto"></span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-bold block mb-8">المدير المالي والحسابات</span>
                        <span class="border-b-2 border-dashed border-slate-400 block w-32 mx-auto"></span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-bold block mb-8">اعتماد إدارة المركز والختم</span>
                        <div class="w-20 h-20 rounded-full border-2 border-dashed border-indigo-400 mx-auto flex items-center justify-center text-[10px] text-indigo-400 font-bold">
                            الختم الرسمي
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
