<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف حضور وتقييم الحصة اليومية - {{ $session->group?->name }} - حصة #{{ $session->session_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-size: 11pt; }
            @page { margin: 12mm; size: A4 portrait; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-3xl shadow-xl space-y-6 print:shadow-none print:p-0">
        
        <!-- أزرار التحكم بالطباعة -->
        <div class="no-print flex justify-between items-center bg-slate-900 text-white p-4 rounded-2xl">
            <span class="text-xs font-bold">معاينة التقرير الرسمي لطباعة الحصة اليومية 🖨️</span>
            <div class="flex gap-2">
                <button onclick="window.print()" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs px-5 py-2 rounded-xl transition">
                    طباعة المستند الآن 🖨️
                </button>
                <button onclick="window.close()" class="bg-slate-800 text-slate-300 text-xs px-3 py-2 rounded-xl">
                    إغلاق ✕
                </button>
            </div>
        </div>

        <!-- ترويسة المدرسة الرسمية -->
        <div class="border-b-2 border-slate-900 pb-4 flex justify-between items-center text-right">
            <div>
                <h1 class="text-xl font-black text-slate-950">مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات (ASOTS)</h1>
                <span class="text-xs font-bold text-indigo-700 block mt-0.5">The Asian Star Office for Translation, Training, & Consultancy Services</span>
                <span class="text-[11px] text-slate-500 block font-mono">نظام إدارة التعلم الذكي • كشف الحضور والتقييم اليومي للمجموعات</span>
            </div>
            <div class="text-left font-mono text-xs text-slate-600">
                <span class="font-bold text-slate-950 block">رقم الحصة: #{{ $session->session_number }}</span>
                <span>التاريخ: {{ $session->session_date->format('Y-m-d') }}</span>
            </div>
        </div>

        <!-- بطاقة بيانات الحصة والمجموعة -->
        <div class="bg-slate-50 border border-slate-200 p-4 rounded-2xl grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <span class="text-slate-400 block font-bold">المجموعة الدراسية:</span>
                <strong class="text-slate-900 text-sm">{{ $session->group?->name }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block font-bold">المسار والمستوى:</span>
                <strong class="text-slate-900">{{ $session->group?->course?->name }} - {{ $session->group?->level?->name }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block font-bold">المعلم المحاضر:</span>
                <strong class="text-slate-900">{{ $session->teacher?->name }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block font-bold">نسبة الحضور:</span>
                <strong class="text-emerald-700 text-sm font-mono">{{ $session->attendance_rate }}% (حضر {{ $session->present_count }} من {{ $session->total_students }})</strong>
            </div>
        </div>

        <!-- موضوع الدرس والشرح والواجب -->
        <div class="space-y-2 text-xs">
            <div class="p-3 bg-indigo-50/50 rounded-xl border border-indigo-100">
                <strong class="text-indigo-950 block font-bold">عنوان وموضوع درس اليوم:</strong>
                <p class="text-slate-800 text-sm font-black mt-0.5">{{ $session->lesson_topic }}</p>
            </div>

            @if($session->lesson_summary)
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <strong class="text-slate-700 block font-bold">ملخص محتوى الدرس:</strong>
                    <p class="text-slate-600 leading-relaxed mt-0.5">{{ $session->lesson_summary }}</p>
                </div>
            @endif

            @if($session->homework_assigned)
                <div class="p-3 bg-amber-50/50 rounded-xl border border-amber-200">
                    <strong class="text-amber-950 block font-bold">الواجب والتكليف المنزلي:</strong>
                    <p class="text-amber-900 leading-relaxed mt-0.5">{{ $session->homework_assigned }}</p>
                </div>
            @endif
        </div>

        <!-- جدول الحضور والتقييم الفردي للطلاب -->
        <div class="space-y-2">
            <h3 class="text-xs font-black text-slate-900">سجل حضور وتفاعل الطلاب ومكافآت النجوم:</h3>
            <table class="w-full text-right text-xs border border-slate-300">
                <thead>
                    <tr class="bg-slate-100 text-slate-800 border-b border-slate-300 font-bold">
                        <th class="p-2 border-l border-slate-300">#</th>
                        <th class="p-2 border-l border-slate-300">اسم الطالب</th>
                        <th class="p-2 border-l border-slate-300 text-center">كود الطالب</th>
                        <th class="p-2 border-l border-slate-300 text-center">حالة الحضور</th>
                        <th class="p-2 border-l border-slate-300 text-center">المشاركة (5)</th>
                        <th class="p-2 border-l border-slate-300 text-center">الاستيعاب (5)</th>
                        <th class="p-2 border-l border-slate-300 text-center">الواجب (5)</th>
                        <th class="p-2 border-l border-slate-300 text-center">النجوم ⭐</th>
                        <th class="p-2 border-l border-slate-300 text-center">الدرجة</th>
                        <th class="p-2">ملاحظات المعلم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($session->evaluations as $idx => $ev)
                        <tr class="{{ $ev->attendance_status === 'absent' ? 'bg-rose-50/40' : '' }}">
                            <td class="p-2 border-l border-slate-200 font-mono text-center">{{ $idx + 1 }}</td>
                            <td class="p-2 border-l border-slate-200 font-bold text-slate-900">{{ $ev->student?->name }}</td>
                            <td class="p-2 border-l border-slate-200 text-center font-mono">{{ $ev->student?->student_code ?? $ev->student?->user_code }}</td>
                            <td class="p-2 border-l border-slate-200 text-center font-bold">
                                {{ $ev->attendance_label }}
                            </td>
                            <td class="p-2 border-l border-slate-200 text-center font-mono font-bold">{{ $ev->participation_score }}/5</td>
                            <td class="p-2 border-l border-slate-200 text-center font-mono">{{ $ev->understanding_score }}/5</td>
                            <td class="p-2 border-l border-slate-200 text-center font-mono">{{ $ev->homework_score }}/5</td>
                            <td class="p-2 border-l border-slate-200 text-center font-mono font-bold text-amber-700">⭐ {{ $ev->stars_awarded }}</td>
                            <td class="p-2 border-l border-slate-200 text-center font-mono font-bold">{{ $ev->daily_total_score }}%</td>
                            <td class="p-2 text-slate-600 text-[11px]">{{ $ev->teacher_notes ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- التوقيعات والاعتماد الرسمي -->
        <div class="pt-8 border-t border-slate-300 grid grid-cols-3 gap-6 text-center text-xs">
            <div class="space-y-8">
                <span class="font-bold text-slate-700 block">توقيع المعلم المسؤول:</span>
                <span class="font-bold text-slate-900 border-b border-dashed border-slate-400 pb-1 inline-block min-w-[120px]">
                    {{ $session->teacher?->name }}
                </span>
            </div>
            <div class="space-y-8">
                <span class="font-bold text-slate-700 block">المشرف الأكاديمي:</span>
                <span class="border-b border-dashed border-slate-400 pb-1 inline-block min-w-[120px]">....................</span>
            </div>
            <div class="space-y-8">
                <span class="font-bold text-slate-700 block">ختم إدارة الأكاديمية:</span>
                <span class="border-b border-dashed border-slate-400 pb-1 inline-block min-w-[120px]">....................</span>
            </div>
        </div>

    </div>

</body>
</html>
