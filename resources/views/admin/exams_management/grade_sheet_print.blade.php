<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف درجات طلاب المجموعة: {{ $group->displayName }} - ASOTS</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f8fafc; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; color: black !important; }
            .print-border { border: 2px solid #0f172a !important; }
            .print-border-b { border-bottom: 2px solid #0f172a !important; }
            .print-break-inside-avoid { break-inside: avoid; }
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-6 text-slate-800">

    <div class="max-w-[1550px] mx-auto space-y-4">

        <!-- شريط الإجراءات العلوي (إخفاء عند الطباعة) -->
        <div class="no-print bg-slate-900 text-white p-4 rounded-2xl shadow-md flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.exams_management.index', ['tab' => 'grading', 'group_id' => $group->id]) }}"
                   class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                    <span>←</span>
                    <span>الرجوع للمركز</span>
                </a>
                <span class="text-xs text-slate-400 font-bold">|</span>
                <span class="text-xs font-black text-amber-400">كشف درجات معتمد رسمي للطباعة والحفظ PDF</span>
            </div>

            <!-- التبديل بين الكشف التفصيلي والإجمالي -->
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-300">نوع الكشف:</span>
                <a href="{{ route('admin.exams_management.grade_sheet.print', ['groupId' => $group->id, 'type' => 'detailed']) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $type === 'detailed' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    كشف تفصيلي (6 مكونات)
                </a>
                <a href="{{ route('admin.exams_management.grade_sheet.print', ['groupId' => $group->id, 'type' => 'summary']) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $type === 'summary' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    كشف إجمالي
                </a>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-2">
                    <span>🖨️</span>
                    <span>طباعة الكشف / Save PDF</span>
                </button>
            </div>
        </div>

        <!-- المستند الرسمي للطباعة -->
        <div class="bg-white border-2 border-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl text-slate-900 space-y-6">

            <!-- الرأس والترويسة الرسمية للأكاديمية -->
            <div class="flex flex-col sm:flex-row justify-between items-start border-b-2 border-slate-900 pb-5 gap-4">
                <div class="space-y-1">
                    <h1 class="text-base sm:text-lg font-black tracking-wide text-slate-950">
                        The Asian Star Office for Translation, Training, & Consultancy Services (ASOTS)
                    </h1>
                    <span class="text-xs text-slate-700 block font-bold">
                        مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات • إدارة الشؤون الأكاديمية والامتحانات
                    </span>
                    <span class="text-[11px] text-slate-500 block font-medium">
                        قسم شؤون الطلاب والكنترول والشهائد المعتمدة
                    </span>
                </div>

                <div class="text-left font-mono shrink-0">
                    <div class="bg-slate-100 border border-slate-300 px-3 py-1 rounded-xl text-xs mb-1">
                        <span class="font-bold">كود المجموعة:</span>
                        <strong class="text-indigo-700 font-black">GRP-{{ str_pad($group->id, 4, '0', STR_PAD_LEFT) }}</strong>
                    </div>
                    <div class="text-[11px] text-slate-500">
                        <span>تاريخ الطباعة:</span>
                        <strong>{{ now()->format('Y/m/d H:i') }}</strong>
                    </div>
                    <div class="text-[11px] text-slate-500">
                        <span>الترم / الدفعة:</span>
                        <strong>{{ $group->term ?: 'الترم المعتمد' }}</strong>
                    </div>
                </div>
            </div>

            <!-- عنوان الكشف وبيانات المجموعة -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div>
                    <h2 class="text-base font-black text-slate-900">
                        @if($type === 'detailed')
                            كشف درجات طلاب المجموعة الأكاديمي التفصيلي (Detailed Grade Sheet)
                        @else
                            كشف النتائج النهائية لطلاب المجموعة المعتمد (Final Result Summary)
                        @endif
                    </h2>
                    <div class="text-xs text-slate-600 font-bold flex flex-wrap items-center gap-3 mt-1">
                        <span>المجموعة: <strong class="text-purple-800">{{ $group->displayName }}</strong></span>
                        <span>• الدورة: <strong class="text-indigo-800">{{ $group->course?->displayName ?: 'عام' }}</strong></span>
                        @if($group->level)
                            <span>• المستوى: <strong>{{ $group->level->name }}</strong></span>
                        @endif
                        <span>• المعلم: <strong>{{ $group->teacher?->name ?: '—' }}</strong></span>
                    </div>
                </div>

                <!-- إحصائيات سريعة للرأس -->
                <div class="flex items-center gap-3 text-xs font-bold font-mono">
                    <div class="px-3 py-1.5 bg-white border border-slate-300 rounded-xl">
                        <span class="text-slate-500 text-[10px] block">إجمالي الطلاب</span>
                        <strong class="text-slate-900 text-sm font-black">{{ $stats['total'] }}</strong>
                    </div>
                    <div class="px-3 py-1.5 bg-emerald-50 border border-emerald-300 rounded-xl text-emerald-800">
                        <span class="text-emerald-600 text-[10px] block">الناجحين 🎓</span>
                        <strong class="text-sm font-black">{{ $stats['passed'] }}</strong>
                    </div>
                    <div class="px-3 py-1.5 bg-rose-50 border border-rose-300 rounded-xl text-rose-800">
                        <span class="text-rose-600 text-[10px] block">الراسبين</span>
                        <strong class="text-sm font-black">{{ $stats['failed'] }}</strong>
                    </div>
                    <div class="px-3 py-1.5 bg-indigo-50 border border-indigo-300 rounded-xl text-indigo-800">
                        <span class="text-indigo-600 text-[10px] block">المتوسط العام</span>
                        <strong class="text-sm font-black">{{ $stats['avg_pct'] }}%</strong>
                    </div>
                </div>
            </div>

            <!-- جدول رصد الدرجات الرسمي -->
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs border border-slate-900">
                    <thead class="bg-slate-900 text-white font-black">
                        <tr>
                            <th class="p-3 text-center border border-slate-700 w-10">#</th>
                            <th class="p-3 border border-slate-700">اسم الطالب الثلاثي / الرباعي</th>
                            <th class="p-3 border border-slate-700 font-mono text-center">كود الطالب</th>

                            @if($type === 'detailed')
                                <th class="p-3 text-center border border-slate-700 bg-slate-800">
                                    حضور ({{ $scale->attendance_max }})
                                </th>
                                <th class="p-3 text-center border border-slate-700 bg-slate-800">
                                    مشاركة ({{ $scale->participation_max }})
                                </th>
                                <th class="p-3 text-center border border-slate-700 bg-slate-800">
                                    أنشطة ({{ $scale->activities_max }})
                                </th>
                                <th class="p-3 text-center border border-slate-700 bg-slate-800">
                                    نصفي ({{ $scale->midterm_max }})
                                </th>
                                <th class="p-3 text-center border border-slate-700 bg-slate-800">
                                    عملي ({{ $scale->practical_max }})
                                </th>
                                <th class="p-3 text-center border border-slate-700 bg-slate-800">
                                    نهائي ({{ $scale->final_exam_max }})
                                </th>
                            @endif

                            <th class="p-3 text-center border border-slate-700 bg-amber-500 text-slate-950">
                                المجموع ({{ $scale->total_max }})
                            </th>
                            <th class="p-3 text-center border border-slate-700">النسبة %</th>
                            <th class="p-3 text-center border border-slate-700">التقدير العام</th>
                            <th class="p-3 text-center border border-slate-700">النتيجة الرسمية</th>
                            <th class="p-3 border border-slate-700">ملاحظات الكنترول</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-300 font-medium text-slate-900">
                        @forelse($studentsWithGrades as $idx => $st)
                            @php
                                $cg = $st->course_grade;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="p-2.5 text-center border border-slate-300 font-mono text-slate-500">{{ $idx + 1 }}</td>
                                <td class="p-2.5 border border-slate-300 font-black text-slate-950 text-sm">
                                    {{ $st->name }}
                                </td>
                                <td class="p-2.5 border border-slate-300 font-mono text-center text-slate-700">
                                    {{ $st->user_code ?: ('STU-' . $st->id) }}
                                </td>

                                @if($type === 'detailed')
                                    <td class="p-2 text-center border border-slate-300 font-mono font-bold">{{ $cg->attendance_score }}</td>
                                    <td class="p-2 text-center border border-slate-300 font-mono font-bold">{{ $cg->participation_score }}</td>
                                    <td class="p-2 text-center border border-slate-300 font-mono font-bold">{{ $cg->activities_score }}</td>
                                    <td class="p-2 text-center border border-slate-300 font-mono font-bold text-indigo-800">{{ $cg->midterm_score }}</td>
                                    <td class="p-2 text-center border border-slate-300 font-mono font-bold text-indigo-800">{{ $cg->practical_score }}</td>
                                    <td class="p-2 text-center border border-slate-300 font-mono font-bold text-rose-800">{{ $cg->final_exam_score }}</td>
                                @endif

                                <td class="p-2 text-center border border-slate-300 font-mono font-black text-sm bg-amber-50/70">
                                    {{ $cg->total_score }}
                                </td>
                                <td class="p-2 text-center border border-slate-300 font-mono font-black text-indigo-900">
                                    {{ $cg->percentage }}%
                                </td>
                                <td class="p-2 text-center border border-slate-300 font-bold">
                                    {{ $cg->grade_letter }}
                                </td>
                                <td class="p-2 text-center border border-slate-300 font-black">
                                    @if($cg->status === 'passed')
                                        <span class="text-emerald-700">ناجح ومؤهل للشهادة 🎓</span>
                                    @elseif($cg->status === 'failed')
                                        <span class="text-rose-700">راسب ❌</span>
                                    @else
                                        <span class="text-amber-700">قيد التقييم ⏳</span>
                                    @endif
                                </td>
                                <td class="p-2 border border-slate-300 text-[11px] text-slate-500">
                                    {{ $cg->teacher_notes ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="p-8 text-center text-slate-400">
                                    لا يوجد طلاب مسجلين في هذه المجموعة.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- قسم التواقيع الرسمية وختم الكنترول والامتحانات -->
            <div class="pt-6 border-t-2 border-slate-900 print-break-inside-avoid">
                <div class="grid grid-cols-3 gap-6 text-center text-xs font-bold text-slate-800">
                    <div class="space-y-8">
                        <span class="block text-slate-900 font-black">معلم / محاضر المجموعة</span>
                        <div class="border-b border-dashed border-slate-400 pb-1 max-w-[200px] mx-auto text-slate-600 font-medium">
                            {{ $group->teacher?->name ?: 'التوقيع: .....................' }}
                        </div>
                    </div>

                    <div class="space-y-8">
                        <span class="block text-slate-900 font-black">المشرف الأكاديمي والكنترول</span>
                        <div class="border-b border-dashed border-slate-400 pb-1 max-w-[200px] mx-auto text-slate-600 font-medium">
                            {{ $group->supervisor?->name ?: 'التوقيع: .....................' }}
                        </div>
                    </div>

                    <div class="space-y-8">
                        <span class="block text-slate-900 font-black">ختم واعتماد إدارة المعهد والأكاديمية</span>
                        <div class="border border-slate-300 rounded-xl p-3 max-w-[180px] mx-auto text-[10px] text-slate-400 font-medium">
                            الختم الرسمي للمعهد
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-center text-[11px] text-slate-400 font-medium">
                    تم إصدار هذا الكشف آلياً عبر نظام إدارة التعلم والامتحانات الموحد ASOTS • جميع الحقوق محفوظة © {{ date('Y') }}
                </div>
            </div>

        </div>

    </div>

</body>
</html>
