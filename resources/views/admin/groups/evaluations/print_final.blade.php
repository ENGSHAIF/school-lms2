<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف درجات الدبلوم والتقييم النهائي للدورة - {{ $group->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; font-size: 10.5pt; }
            @page { margin: 10mm; size: A4 landscape; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8">

    <div class="max-w-6xl mx-auto bg-white p-8 rounded-3xl shadow-xl space-y-6 print:shadow-none print:p-0">
        
        <!-- أزرار التحكم بالطباعة -->
        <div class="no-print flex justify-between items-center bg-slate-900 text-white p-4 rounded-2xl">
            <span class="text-xs font-bold">معاينة كشف درجات الدبلوم النهائي الرسمي لطلاب المجموعة 🎓</span>
            <div class="flex gap-2">
                <button onclick="window.print()" class="bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs px-5 py-2 rounded-xl transition">
                    طباعة كشف الدبلوم الآن 🖨️
                </button>
                <button onclick="window.close()" class="bg-slate-800 text-slate-300 text-xs px-3 py-2 rounded-xl">
                    إغلاق ✕
                </button>
            </div>
        </div>

        <!-- ترويسة المدرسة الرسمية -->
        <div class="border-b-2 border-slate-900 pb-4 flex justify-between items-center text-right">
            <div>
                <h1 class="text-2xl font-black text-slate-950">مكتب النجم الآسيوي للخدمات التعليمية والترجمة (ASOTS)</h1>
                <span class="text-xs font-bold text-indigo-700 block mt-0.5">The Asian Star Office for Educational Services & Translation</span>
                <span class="text-xs text-slate-600 block font-bold mt-1">كشف درجات التقييم التراكمي النهائي لاعتماد شهادات ودبلومات اللغة</span>
            </div>
            <div class="text-left font-mono text-xs text-slate-600 space-y-1">
                <span class="px-3 py-1 bg-amber-100 text-amber-900 font-bold rounded-lg border border-amber-300 block text-center">
                    كشف رسمي معتمد
                </span>
                <span>تاريخ التصدير: {{ now()->format('Y-m-d') }}</span>
            </div>
        </div>

        <!-- بطاقة بيانات المجموعة والترم والدبلوم -->
        <div class="bg-slate-50 border border-slate-200 p-4 rounded-2xl grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block font-bold">اسم المجموعة:</span>
                <strong class="text-slate-900 text-sm font-black">{{ $group->name }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block font-bold">المسار والمستوى:</span>
                <strong class="text-indigo-900">{{ $group->course?->name }} - {{ $group->level?->name }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block font-bold">الترم / الدفعة:</span>
                <strong class="text-slate-900 font-mono">{{ $group->term ?: 'الترم الحالي' }} ({{ $group->batch ?: 'دفعة عامة' }})</strong>
            </div>
            <div>
                <span class="text-slate-400 block font-bold">المعلم المسؤول:</span>
                <strong class="text-slate-900">{{ $group->teacher?->name ?? 'إدارة الأكاديمية' }}</strong>
            </div>
        </div>

        <!-- معايير احتساب الدرجات الأكاديمية -->
        <div class="bg-amber-50/60 border border-amber-200/80 p-3 rounded-xl text-xs text-amber-950 flex flex-wrap justify-between items-center gap-2">
            <span class="font-bold">معايير احتساب درجات الدبلوم (المجموع الكلي من 100):</span>
            <div class="flex flex-wrap gap-2 text-[11px] font-mono">
                <span class="bg-white px-2 py-0.5 rounded border border-amber-200">المواظبة والحضور: 20 درجة</span>
                <span class="bg-white px-2 py-0.5 rounded border border-amber-200">التفاعل الصوتي والمحادثة: 20 درجة</span>
                <span class="bg-white px-2 py-0.5 rounded border border-amber-200">الواجبات وأوراق العمل: 20 درجة</span>
                <span class="bg-white px-2 py-0.5 rounded border border-amber-200">الكويزات والاختبارات: 20 درجة</span>
                <span class="bg-white px-2 py-0.5 rounded border border-amber-200">تقييم الأستاذ ومكافآت النجوم: 20 درجة</span>
            </div>
        </div>

        <!-- جدول درجات الدبلوم والتقييم النهائي -->
        <table class="w-full text-right text-xs border border-slate-300">
            <thead>
                <tr class="bg-slate-900 text-white border-b border-slate-900 font-bold">
                    <th class="p-2.5 border-l border-slate-800 text-center">#</th>
                    <th class="p-2.5 border-l border-slate-800">اسم الطالب الرباعي</th>
                    <th class="p-2.5 border-l border-slate-800 text-center">رقم الطالب</th>
                    <th class="p-2.5 border-l border-slate-800 text-center">نسبة الحضور</th>
                    <th class="p-2.5 border-l border-slate-800 text-center">المواظبة (20)</th>
                    <th class="p-2.5 border-l border-slate-800 text-center">المشاركة الصوتية (20)</th>
                    <th class="p-2.5 border-l border-slate-800 text-center">الواجبات (20)</th>
                    <th class="p-2.5 border-l border-slate-800 text-center">الكويزات (20)</th>
                    <th class="p-2.5 border-l border-slate-800 text-center">النجوم والسلوك (20)</th>
                    <th class="p-2.5 border-l border-slate-800 text-center bg-amber-400 text-slate-950 font-black">المجموع النهائي (100)</th>
                    <th class="p-2.5 text-center">التقدير العام</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($evaluations as $idx => $ev)
                    <tr class="hover:bg-slate-50">
                        <td class="p-2.5 border-l border-slate-200 text-center font-mono font-bold">{{ $idx + 1 }}</td>
                        <td class="p-2.5 border-l border-slate-200 font-bold text-slate-900">{{ $ev->student->name }}</td>
                        <td class="p-2.5 border-l border-slate-200 text-center font-mono text-slate-600">{{ $ev->student->student_code ?? $ev->student->user_code }}</td>
                        <td class="p-2.5 border-l border-slate-200 text-center font-mono font-bold text-emerald-700">{{ $ev->attendance_percentage }}%</td>
                        <td class="p-2.5 border-l border-slate-200 text-center font-mono font-bold">{{ $ev->attendance_score }}</td>
                        <td class="p-2.5 border-l border-slate-200 text-center font-mono font-bold">{{ $ev->participation_score }}</td>
                        <td class="p-2.5 border-l border-slate-200 text-center font-mono font-bold">{{ $ev->homework_score }}</td>
                        <td class="p-2.5 border-l border-slate-200 text-center font-mono font-bold">{{ $ev->quiz_score }}</td>
                        <td class="p-2.5 border-l border-slate-200 text-center font-mono font-bold">⭐ {{ $ev->stars_bonus }}</td>
                        <td class="p-2.5 border-l border-slate-200 text-center font-mono font-black text-sm bg-amber-50 text-amber-950">
                            {{ $ev->final_grade }}%
                        </td>
                        <td class="p-2.5 text-center font-bold text-[11px]">
                            {{ $ev->grade_letter }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- التوقيعات والاعتماد الرسمي لشهادة الدبلوم -->
        <div class="pt-10 border-t-2 border-slate-300 grid grid-cols-4 gap-4 text-center text-xs">
            <div class="space-y-8">
                <span class="font-bold text-slate-700 block">معلم المجموعة:</span>
                <span class="font-bold text-slate-900 border-b border-dashed border-slate-400 pb-1 inline-block min-w-[130px]">
                    {{ $group->teacher?->name }}
                </span>
            </div>
            <div class="space-y-8">
                <span class="font-bold text-slate-700 block">المشرف الأكاديمي:</span>
                <span class="border-b border-dashed border-slate-400 pb-1 inline-block min-w-[130px]">....................</span>
            </div>
            <div class="space-y-8">
                <span class="font-bold text-slate-700 block">مسؤول شؤون الطلاب:</span>
                <span class="border-b border-dashed border-slate-400 pb-1 inline-block min-w-[130px]">....................</span>
            </div>
            <div class="space-y-8">
                <span class="font-bold text-slate-700 block">عميد / مدير الأكاديمية (الختم):</span>
                <span class="border-b border-dashed border-slate-400 pb-1 inline-block min-w-[130px]">....................</span>
            </div>
        </div>

    </div>

</body>
</html>
