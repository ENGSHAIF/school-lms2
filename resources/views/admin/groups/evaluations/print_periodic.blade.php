<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير متابعة الحضور والتقييمات الدورية - {{ $group ? $group->name : 'كافة المجموعات' }}</title>
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
            <span class="text-xs font-bold">معاينة التقرير الدوري للحضور والتقييمات 🖨️</span>
            <div class="flex gap-2">
                <button onclick="window.print()" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs px-5 py-2 rounded-xl transition">
                    طباعة التقرير الآن 🖨️
                </button>
                <button onclick="window.close()" class="bg-slate-800 text-slate-300 text-xs px-3 py-2 rounded-xl">
                    إغلاق ✕
                </button>
            </div>
        </div>

        <!-- ترويسة المدرسة الرسمية -->
        <div class="border-b-2 border-slate-900 pb-4 flex justify-between items-center text-right">
            <div>
                <h1 class="text-xl font-black text-slate-950">مكتب النجم الآسيوي للخدمات التعليمية والترجمة (ASOTS)</h1>
                <span class="text-xs font-bold text-indigo-700 block mt-0.5">The Asian Star Office for Educational Services & Translation</span>
                <span class="text-[11px] text-slate-500 block font-mono">
                    تقرير المتابعة الدورية للحصص والتقييمات • {{ match($period) { 'weekly' => 'التقرير الأسبوعي', 'monthly' => 'التقرير الشهري', default => 'التقرير الدوري' } }}
                </span>
            </div>
            <div class="text-left font-mono text-xs text-slate-600">
                <span>تاريخ التقرير: {{ now()->format('Y-m-d') }}</span>
                @if($dateFrom)
                    <span class="block">من: {{ $dateFrom }}</span>
                @endif
                @if($dateTo)
                    <span class="block">إلى: {{ $dateTo }}</span>
                @endif
            </div>
        </div>

        @if($group)
            <div class="bg-slate-50 border border-slate-200 p-4 rounded-2xl grid grid-cols-3 gap-3 text-xs">
                <div>
                    <span class="text-slate-400 block font-bold">المجموعة:</span>
                    <strong class="text-slate-900 text-sm">{{ $group->name }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block font-bold">المسار والمستوى:</span>
                    <strong class="text-slate-900">{{ $group->course?->name }} - {{ $group->level?->name }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block font-bold">المعلم:</span>
                    <strong class="text-slate-900">{{ $group->teacher?->name ?? 'إدارة الأكاديمية' }}</strong>
                </div>
            </div>
        @endif

        <!-- جدول الحصص المنعقدة -->
        <table class="w-full text-right text-xs border border-slate-300">
            <thead>
                <tr class="bg-slate-100 text-slate-800 border-b border-slate-300 font-bold">
                    <th class="p-2 border-l border-slate-300">#</th>
                    <th class="p-2 border-l border-slate-300">التاريخ</th>
                    <th class="p-2 border-l border-slate-300">المجموعة</th>
                    <th class="p-2 border-l border-slate-300">عنوان الدرس ومحتواه</th>
                    <th class="p-2 border-l border-slate-300 text-center">الحضور</th>
                    <th class="p-2 border-l border-slate-300 text-center">الغياب</th>
                    <th class="p-2 border-l border-slate-300 text-center">النسبة</th>
                    <th class="p-2">المعلم</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($sessions as $idx => $s)
                    <tr>
                        <td class="p-2 border-l border-slate-200 font-mono text-center">{{ $s->session_number }}</td>
                        <td class="p-2 border-l border-slate-200 font-mono">{{ $s->session_date->format('Y-m-d') }}</td>
                        <td class="p-2 border-l border-slate-200 font-bold text-slate-900">{{ $s->group?->name }}</td>
                        <td class="p-2 border-l border-slate-200">
                            <strong class="text-slate-900 block">{{ $s->lesson_topic }}</strong>
                            @if($s->lesson_summary)
                                <span class="text-[10px] text-slate-500 block truncate max-w-xs">{{ $s->lesson_summary }}</span>
                            @endif
                        </td>
                        <td class="p-2 border-l border-slate-200 text-center font-mono font-bold text-emerald-700">{{ $s->present_count }}</td>
                        <td class="p-2 border-l border-slate-200 text-center font-mono font-bold text-rose-700">{{ $s->absent_count }}</td>
                        <td class="p-2 border-l border-slate-200 text-center font-mono font-bold">{{ $s->attendance_rate }}%</td>
                        <td class="p-2 text-slate-700">{{ $s->teacher?->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-4 text-center text-slate-400">لا توجد حصص مرصودة في هذه الفترة.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- التوقيعات -->
        <div class="pt-8 border-t border-slate-300 grid grid-cols-2 gap-6 text-center text-xs">
            <div class="space-y-6">
                <span class="font-bold text-slate-700 block">المشرف الأكاديمي:</span>
                <span class="border-b border-dashed border-slate-400 pb-1 inline-block min-w-[150px]">....................</span>
            </div>
            <div class="space-y-6">
                <span class="font-bold text-slate-700 block">اعتماد إدارة الأكاديمية:</span>
                <span class="border-b border-dashed border-slate-400 pb-1 inline-block min-w-[150px]">....................</span>
            </div>
        </div>

    </div>

</body>
</html>
