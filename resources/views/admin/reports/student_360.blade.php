<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>السجل الأكاديمي والمالي الشامل: {{ $student->name }} - ASOTS</title>
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
                <a href="{{ url()->previous() ?: route('admin.reports.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                    <span>←</span>
                    <span>الرجوع</span>
                </a>
                <span class="text-xs text-slate-400 font-bold">|</span>
                <span class="text-xs font-black text-slate-900">السجل الأكاديمي وكشف الحساب الموحد (Student 360 Movement)</span>
            </div>

            <div class="flex items-center gap-2">
                <!-- زر الطباعة -->
                <button onclick="window.print()" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-2">
                    <span>🖨️</span>
                    <span>طباعة التقرير الشامل / PDF</span>
                </button>
            </div>
        </div>

        <!-- الورقة الرسمية المعتمدة للتقرير (Printable Document) -->
        <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-lg border border-slate-200 print:shadow-none print:border-none print:p-0 space-y-8">

            <!-- 1. ترويسة المركز الرسمية -->
            <div class="border-b-2 border-slate-900 pb-6 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-right space-y-1">
                    <h2 class="text-lg font-black text-slate-900">مكتب النجم الآسيوي للترجمة والتدريب والاستشارات</h2>
                    <h3 class="text-xs font-mono font-bold text-slate-500 uppercase tracking-wider">The Asian Star Office for Translation, Training, & Consultancy Services (ASOTS)</h3>
                    <p class="text-[11px] text-slate-500 font-bold">منصة التدريب الإلكتروني وإدارة المناهج واللغات • تقرير المسار الأكاديمي وكشف الحساب المالي</p>
                </div>

                <div class="text-center md:text-left bg-slate-50 p-4 rounded-2xl border border-slate-200 shrink-0">
                    <span class="text-[10px] text-slate-400 block font-bold">تاريخ إصدار التقرير</span>
                    <span class="text-sm font-black font-mono text-slate-900">{{ date('Y/m/d - H:i') }}</span>
                    <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 block mt-1">
                        معتمد رسمي من إدارة النظام
                    </span>
                </div>
            </div>

            <!-- 2. بطاقة بيانات الطالب الشخصية والأكاديمية والمالية -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-white/10 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-amber-400 text-slate-950 font-black text-2xl flex items-center justify-center shadow-md">
                            🎓
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-amber-400 uppercase tracking-wider">سجل الطالب الأكاديمي</span>
                            <h1 class="text-xl sm:text-2xl font-black text-white">{{ $student->name }}</h1>
                            <span class="text-xs text-slate-300 font-mono">{{ $student->email }} • {{ $student->phone_whatsapp ?? 'لا يوجد هاتف مسجل' }}</span>
                        </div>
                    </div>

                    <div class="text-right sm:text-left bg-white/10 px-4 py-2.5 rounded-xl border border-white/15">
                        <span class="text-[10px] text-slate-300 block font-bold">الرقم الأكاديمي الجامعي</span>
                        <span class="text-base font-black font-mono text-amber-400">{{ $student->student_code ?? 'STU-' . $student->id }}</span>
                        <span class="text-[10px] text-slate-400 block mt-0.5">تاريخ الانضمام: {{ $student->created_at->format('Y/m/d') }}</span>
                    </div>
                </div>

                <!-- مؤشرات مالية سريعة للطالب -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="bg-white/5 p-3 rounded-xl border border-white/10">
                        <span class="text-slate-400 block font-bold text-[10px]">إجمالي الرسوم المقررة:</span>
                        <span class="text-base font-black font-mono text-white">{{ number_format($totalTuition, 2) }}</span>
                        <span class="text-[10px] text-slate-400 block">{{ $account?->currency_name ?? 'ريال يمني' }}</span>
                    </div>

                    <div class="bg-white/5 p-3 rounded-xl border border-white/10">
                        <span class="text-amber-300 block font-bold text-[10px]">إجمالي الخصومات والمنح:</span>
                        <span class="text-base font-black font-mono text-amber-400">{{ number_format($totalDiscounts, 2) }}</span>
                        <span class="text-[10px] text-amber-300/80 block">{{ $account?->currency_name ?? 'ريال يمني' }}</span>
                    </div>

                    <div class="bg-white/5 p-3 rounded-xl border border-white/10">
                        <span class="text-emerald-300 block font-bold text-[10px]">إجمالي المسدد (سندات القبض):</span>
                        <span class="text-base font-black font-mono text-emerald-400">{{ number_format($totalPaid, 2) }}</span>
                        <span class="text-[10px] text-emerald-300/80 block">{{ $vouchers->where('voucher_type', 'receipt')->count() }} سند قبض</span>
                    </div>

                    <div class="bg-white/5 p-3 rounded-xl border border-white/10">
                        <span class="text-slate-400 block font-bold text-[10px]">الرصيد المالي المتبقي بذمته:</span>
                        <span class="text-base font-black font-mono {{ $currentBalance > 0 ? 'text-red-400' : 'text-emerald-400' }}">
                            {{ number_format($currentBalance, 2) }}
                        </span>
                        <span class="text-[10px] text-slate-400 block">
                            {{ $currentBalance > 0 ? 'متبقي مستحق الدفع ⏳' : 'خالص الذمة المالية ✅' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3. حركة الطالب الأكاديمية (جميع الدورات والمستويات والأترام والمجموعات) -->
            <div class="space-y-4 print-break-inside-avoid">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <h3 class="font-black text-sm text-slate-900 flex items-center gap-2">
                        <span>📚</span>
                        <span>أولاً: سجل الحركات والدورات الأكاديمية (Academic Journey)</span>
                    </h3>
                    <span class="text-xs text-slate-400 font-bold">عدد المجموعات والدورات: ({{ $student->studyGroups->count() }})</span>
                </div>

                @if($student->studyGroups->isEmpty())
                    <p class="text-xs text-slate-400 p-4 bg-slate-50 rounded-xl text-center">لم يتم تسكين الطالب في أي مجموعة دراسية بعد.</p>
                @else
                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full text-right text-xs">
                            <thead class="bg-slate-900 text-white font-bold">
                                <tr>
                                    <th class="p-3">#</th>
                                    <th class="p-3">الدورة والبرنامج</th>
                                    <th class="p-3">المستوى الأكاديمي</th>
                                    <th class="p-3">اسم المجموعة / الدفعة</th>
                                    <th class="p-3">الترم الدراسي</th>
                                    <th class="p-3">المعلم الرئيسي والمساعد</th>
                                    <th class="p-3">المشرف</th>
                                    <th class="p-3">تاريخ الالتحاق</th>
                                    <th class="p-3">حالة الدراسة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($student->studyGroups as $index => $grp)
                                    <tr class="hover:bg-slate-50 {{ $index % 2 == 0 ? 'bg-white' : 'bg-slate-50/40' }}">
                                        <td class="p-3 font-mono font-bold text-slate-400">{{ $index + 1 }}</td>
                                        <td class="p-3 font-black text-slate-900">
                                            {{ $grp->course?->name ?? 'دورات اللغات' }}
                                            @if($grp->program_type)
                                                <span class="text-[10px] text-indigo-700 block font-normal">{{ $grp->program_type }} ({{ $grp->duration_months ?? 3 }} شهور)</span>
                                            @endif
                                        </td>
                                        <td class="p-3">
                                            <span class="px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-800 font-bold font-mono">
                                                {{ $grp->level?->name ?? 'المستوى العام' }}
                                            </span>
                                        </td>
                                        <td class="p-3 font-bold text-slate-800">
                                            {{ $grp->name }}
                                            @if($grp->batch)
                                                <span class="text-[10px] text-amber-700 block font-mono">[{{ $grp->batch }}]</span>
                                            @endif
                                        </td>
                                        <td class="p-3">
                                            <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-bold text-[10px]">
                                                📅 {{ $grp->term ?: 'غير محدد' }}
                                            </span>
                                        </td>
                                        <td class="p-3">
                                            <span class="font-bold text-slate-900 block">👨‍🏫 {{ $grp->primaryTeacher()?->name ?? 'لم يحدد' }}</span>
                                            @if($grp->assistantTeachers()->isNotEmpty())
                                                <span class="text-[10px] text-slate-500 block">
                                                    مساعد: {{ $grp->assistantTeachers()->pluck('name')->join('، ') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-slate-600">
                                            🛡️ {{ $grp->primarySupervisor()?->name ?? 'إدارة المنصة' }}
                                        </td>
                                        <td class="p-3 font-mono text-slate-500">
                                            {{ $grp->pivot->enrolled_at ? date('Y/m/d', strtotime($grp->pivot->enrolled_at)) : '-' }}
                                        </td>
                                        <td class="p-3">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $grp->pivot->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                                {{ $grp->pivot->status === 'active' ? 'مستمر ونشط' : 'مكتمل / منقول' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- 4. نتائج الاختبارات والكويزات والتقييمات -->
            @if($examAttempts->isNotEmpty())
                <div class="space-y-4 print-break-inside-avoid">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <h3 class="font-black text-sm text-slate-900 flex items-center gap-2">
                            <span>🎯</span>
                            <span>ثانياً: نتائج الاختبارات والكويزات الإلكترونية</span>
                        </h3>
                        <span class="text-xs text-slate-400 font-bold">عدد المحاولات: ({{ $examAttempts->count() }})</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($examAttempts as $att)
                            <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-2">
                                <div class="flex justify-between items-start text-xs">
                                    <span class="font-black text-slate-900">{{ $att->exam?->title ?? 'اختبار إلكتروني' }}</span>
                                    <span class="text-[10px] font-mono text-slate-400">{{ $att->created_at->format('Y/m/d') }}</span>
                                </div>
                                <div class="flex items-baseline justify-between text-xs pt-1">
                                    <span class="text-slate-500">الدرجة المحققة:</span>
                                    <span class="font-mono font-black text-sm {{ ($att->score ?? 0) >= 60 ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $att->score ?? 0 }} / {{ $att->exam?->total_points ?? 100 }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 5. كشف الحساب المالي التحليلي الكامل (جميع المستندات بتواريخها) -->
            <div class="space-y-4 print-break-inside-avoid">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <h3 class="font-black text-sm text-slate-900 flex items-center gap-2">
                        <span>💳</span>
                        <span>ثالثاً: كشف الحساب المالي التحليلي التفصيلي (Financial Ledger)</span>
                    </h3>
                    <span class="text-xs text-slate-400 font-bold">العملة: {{ $account?->currency_name ?? 'ريال يمني' }}</span>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-900 text-white font-bold">
                            <tr>
                                <th class="p-3">التاريخ</th>
                                <th class="p-3">رقم المستند / السند</th>
                                <th class="p-3">البيان والشرح التفصيلي</th>
                                <th class="p-3 font-mono text-left">مدين (رسوم)</th>
                                <th class="p-3 font-mono text-left">خصم ممنوح</th>
                                <th class="p-3 font-mono text-left">دائن (مسدد)</th>
                                <th class="p-3 font-mono text-left">الرصيد التراكمي</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($movements as $m)
                                <tr class="hover:bg-slate-50 {{ $m['type'] === 'credit' ? 'bg-emerald-50/20' : '' }}">
                                    <td class="p-3 font-mono text-slate-500 whitespace-nowrap">{{ $m['date']->format('Y/m/d') }}</td>
                                    <td class="p-3 font-mono font-bold {{ $m['type'] === 'credit' ? 'text-emerald-700' : 'text-indigo-700' }} whitespace-nowrap">
                                        {{ $m['doc_no'] }}
                                    </td>
                                    <td class="p-3">
                                        <span class="font-bold text-slate-800 block">{{ $m['title'] }}</span>
                                        @if($m['discount_reason'])
                                            <span class="text-[10px] text-amber-700 block font-bold">سبب الخصم: {{ $m['discount_reason'] }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3 font-mono text-left {{ $m['debit'] > 0 ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                                        {{ number_format($m['debit'], 2) }}
                                    </td>
                                    <td class="p-3 font-mono text-left {{ $m['discount'] > 0 ? 'font-bold text-amber-600' : 'text-slate-400' }}">
                                        {{ number_format($m['discount'], 2) }}
                                    </td>
                                    <td class="p-3 font-mono text-left {{ $m['credit'] > 0 ? 'font-bold text-emerald-600' : 'text-slate-400' }}">
                                        {{ number_format($m['credit'], 2) }}
                                    </td>
                                    <td class="p-3 font-mono font-black text-left {{ $m['running_balance'] > 0 ? 'text-red-600' : 'text-slate-900' }}">
                                        {{ number_format($m['running_balance'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-6 text-center text-slate-400">لا توجد حركات مالية مسجلة على حساب الطالب.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-slate-100 font-bold border-t-2 border-slate-300">
                            <tr>
                                <td colspan="3" class="p-3 text-slate-900">الإجماليات الكلية لكشف الحساب:</td>
                                <td class="p-3 font-mono text-left text-slate-900 font-black">{{ number_format($totalTuition, 2) }}</td>
                                <td class="p-3 font-mono text-left text-amber-700 font-black">{{ number_format($totalDiscounts, 2) }}</td>
                                <td class="p-3 font-mono text-left text-emerald-700 font-black">{{ number_format($totalPaid, 2) }}</td>
                                <td class="p-3 font-mono text-left font-black text-sm {{ $currentBalance > 0 ? 'text-red-700' : 'text-emerald-700' }}">
                                    {{ number_format($currentBalance, 2) }} {{ $account?->currency ?? 'YER' }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- 6. التوقيعات والاعتماد الرسمي -->
            <div class="pt-8 border-t border-slate-200 grid grid-cols-3 gap-6 text-center text-xs print-break-inside-avoid">
                <div class="space-y-8">
                    <span class="font-bold text-slate-600 block">إعداد وتدقيق الحسابات</span>
                    <span class="text-slate-400 block font-mono">.......................................</span>
                </div>

                <div class="space-y-8">
                    <span class="font-bold text-slate-600 block">المشرف الأكاديمي للبرنامج</span>
                    <span class="text-slate-400 block font-mono">.......................................</span>
                </div>

                <div class="space-y-8">
                    <span class="font-bold text-slate-600 block">إدارة المنصة والختم الرسمي</span>
                    <span class="text-slate-400 block font-mono">.......................................</span>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
