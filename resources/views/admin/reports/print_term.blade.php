<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير وإحصائيات الفصل الدراسي: {{ $term }} - ASOTS</title>
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

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- شريط الإجراءات العلوي (إخفاء عند الطباعة) -->
        <div class="no-print bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                    <span>←</span>
                    <span>الرجوع للتقارير</span>
                </a>
                <span class="text-xs text-slate-400 font-bold">|</span>
                <span class="text-xs font-black text-slate-900">تقرير الطلاب والمجموعات بحسب الترم (Term Roster & Analytics)</span>
            </div>

            <!-- محدد الأترام السريع للتبديل بين الترم الأول والترم الثاني -->
            <form action="{{ route('admin.reports.term.print') }}" method="GET" class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-600">اختر الترم:</label>
                <select name="term" onchange="this.form.submit()" class="text-xs font-bold px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    @foreach($allTerms as $t)
                        <option value="{{ $t }}" {{ $term === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                    @if(empty($allTerms))
                        <option value="{{ $term }}">{{ $term }}</option>
                    @endif
                </select>
                <button type="submit" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition">
                    عرض
                </button>
            </form>

            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-2">
                    <span>🖨️</span>
                    <span>طباعة كشف الترم / PDF</span>
                </button>
            </div>
        </div>

        <!-- المستند الرسمي للطباعة -->
        <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-lg border border-slate-200 print:shadow-none print:border-none print:p-0 space-y-8">

            <!-- 1. الترويسة الرسمية للمركز -->
            <div class="border-b-2 border-slate-900 pb-6 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-right space-y-1">
                    <h2 class="text-lg font-black text-slate-900">مكتب النجم الآسيوي لخدمات الترجمة والتدريب والاستشارات</h2>
                    <h3 class="text-xs font-mono font-bold text-slate-500 uppercase tracking-wider">The Asian Star Office for Translation, Training, & Consultancy Services (ASOTS)</h3>
                    <p class="text-[11px] text-slate-500 font-bold">الإدارة الأكاديمية وشؤون الطلاب • التقرير الإحصائي والكشف العام للترم</p>
                </div>

                <div class="text-center md:text-left bg-slate-50 p-4 rounded-2xl border border-slate-200 shrink-0">
                    <span class="text-[10px] text-slate-400 block font-bold">الفصل الدراسي / الدفعة</span>
                    <span class="text-base font-black text-indigo-700">{{ $term }}</span>
                    <span class="text-[10px] text-slate-500 font-mono block mt-0.5">تاريخ الطباعة: {{ date('Y/m/d - H:i') }}</span>
                </div>
            </div>

            <!-- 2. بطاقات الإحصائيات العامة للترم -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-indigo-50 border border-indigo-200 p-4 rounded-2xl text-center">
                    <span class="text-[11px] font-bold text-indigo-800 block">إجمالي طلاب الترم</span>
                    <span class="text-2xl font-black text-indigo-950 font-mono mt-1 block">{{ $totalEnrolled }}</span>
                    <span class="text-[10px] text-indigo-600 font-bold block mt-0.5">طالب مسجل</span>
                </div>

                <div class="bg-blue-50 border border-blue-200 p-4 rounded-2xl text-center">
                    <span class="text-[11px] font-bold text-blue-800 block">المجموعات المفتوحة بالترم</span>
                    <span class="text-2xl font-black text-blue-950 font-mono mt-1 block">{{ $groups->count() }}</span>
                    <span class="text-[10px] text-blue-600 font-bold block mt-0.5">شعبة ومجموعة</span>
                </div>

                <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl text-center">
                    <span class="text-[11px] font-bold text-emerald-800 block">الطاقة الاستيعابية</span>
                    <span class="text-2xl font-black text-emerald-950 font-mono mt-1 block">{{ $totalCapacity }}</span>
                    <span class="text-[10px] text-emerald-600 font-bold block mt-0.5">مقعد متاح بالمجموعات</span>
                </div>

                <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl text-center">
                    <span class="text-[11px] font-bold text-amber-800 block">نسبة الإشغال العام</span>
                    <span class="text-2xl font-black text-amber-950 font-mono mt-1 block">{{ $occupancyRate }}%</span>
                    <span class="text-[10px] text-amber-700 font-bold block mt-0.5">معدل امتلاء المجموعات</span>
                </div>
            </div>

            <!-- 3. المجموعات والشعب المقيدة في هذا الترم -->
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <span>📚</span>
                        <span>المجموعات الدراسية للترم ({{ $term }})</span>
                    </h3>
                    <span class="text-xs text-slate-500 font-bold">{{ $groups->count() }} مجموعة</span>
                </div>

                @if($groups->isEmpty())
                    <div class="text-center py-6 bg-slate-50 rounded-2xl text-slate-400 text-xs">
                        لا توجد مجموعات مسجلة في هذا الترم حتى الآن.
                    </div>
                @else
                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full text-right text-xs">
                            <thead class="bg-slate-100 text-slate-600 font-bold border-b border-slate-200">
                                <tr>
                                    <th class="p-3">#</th>
                                    <th class="p-3">المجموعة</th>
                                    <th class="p-3">الدورة والمستوى</th>
                                    <th class="p-3">الدفعة والبرنامج</th>
                                    <th class="p-3">المعلم الرئيسي والمساعدين</th>
                                    <th class="p-3">المشرف الأكاديمي</th>
                                    <th class="p-3 text-center">الإشغال</th>
                                    <th class="p-3 text-center">المدة والتواريخ</th>
                                    <th class="p-3 text-center">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($groups as $idx => $g)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-3 font-mono text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="p-3 font-bold text-slate-900">
                                        {{ $g->name }}
                                        <span class="block text-[10px] text-slate-400 font-mono">ID: #{{ $g->id }}</span>
                                    </td>
                                    <td class="p-3">
                                        <span class="font-bold text-slate-800">{{ $g->course->title ?? '-' }}</span>
                                        <span class="block text-[10px] text-slate-500">{{ $g->level->title ?? '-' }}</span>
                                    </td>
                                    <td class="p-3">
                                        @if($g->batch)
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded font-bold text-[10px]">{{ $g->batch }}</span>
                                        @endif
                                        @if($g->program_type)
                                            <span class="block text-[10px] text-indigo-600 font-bold mt-0.5">{{ $g->program_type }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        @php
                                            $primaryT = $g->primaryTeacher() ?: $g->teacher;
                                            $asstT = $g->assistantTeachers();
                                        @endphp
                                        <div class="font-bold text-slate-900">
                                            ⭐ {{ $primaryT ? $primaryT->name : 'غير محدد' }}
                                        </div>
                                        @if($asstT && $asstT->isNotEmpty())
                                            <div class="text-[10px] text-slate-500 mt-0.5 flex flex-wrap gap-1">
                                                @foreach($asstT as $at)
                                                    <span class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded border border-indigo-100">
                                                        {{ $at->name }} ({{ $at->pivot->role_title ?: 'مساعد' }})
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-3 text-slate-700 font-medium">
                                        {{ $g->supervisor ? $g->supervisor->name : ($g->primarySupervisor() ? $g->primarySupervisor()->name : 'إشراف عام') }}
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="font-mono font-bold text-slate-900">{{ $g->students->count() }}</span>
                                        <span class="text-slate-400 font-mono">/ {{ $g->max_students }}</span>
                                    </td>
                                    <td class="p-3 text-center text-[10px] font-mono text-slate-500">
                                        {{ $g->duration_months ? $g->duration_months . ' شهر' : '-' }}
                                        @if($g->start_date)
                                            <span class="block text-[9px] text-slate-400">{{ $g->start_date->format('Y/m/d') }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $g->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $g->status === 'active' ? 'نشطة' : 'مكتملة' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- 4. كشف الطلاب المقيدين في الترم كاملاً -->
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <span>👨‍🎓</span>
                        <span>كشف الطلاب المقيدين في ({{ $term }})</span>
                    </h3>
                    <span class="text-xs text-slate-500 font-bold">إجمالي المقيدين: {{ $students->count() }} طالب</span>
                </div>

                @if($students->isEmpty())
                    <div class="text-center py-6 bg-slate-50 rounded-2xl text-slate-400 text-xs">
                        لا يوجد طلاب مقيدين في هذا الترم بعد.
                    </div>
                @else
                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full text-right text-xs">
                            <thead class="bg-slate-100 text-slate-600 font-bold border-b border-slate-200">
                                <tr>
                                    <th class="p-3">#</th>
                                    <th class="p-3">اسم الطالب</th>
                                    <th class="p-3">رقم الهاتف / البريد</th>
                                    <th class="p-3">المجموعة المسجل بها بالترم</th>
                                    <th class="p-3">الدورة والمستوى</th>
                                    <th class="p-3">المعلم الرئيسي</th>
                                    <th class="p-3 text-center">الرصيد المالي</th>
                                    <th class="p-3 text-center no-print">إجراءات الطالب</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($students as $idx => $st)
                                @php
                                    $studentGroup = $st->studyGroups->first();
                                    $bal = $st->financialAccount?->current_balance ?? 0;
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-3 font-mono text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="p-3">
                                        <div class="font-bold text-slate-900">{{ $st->name }}</div>
                                        <span class="text-[10px] text-slate-400 font-mono">كود: #{{ $st->id }}</span>
                                    </td>
                                    <td class="p-3 text-slate-600 font-mono text-[11px]">
                                        {{ $st->phone ?: 'لا يوجد' }}
                                        <span class="block text-[10px] text-slate-400">{{ $st->email }}</span>
                                    </td>
                                    <td class="p-3">
                                        @if($studentGroup)
                                            <span class="font-bold text-indigo-700">{{ $studentGroup->name }}</span>
                                            @if($studentGroup->batch)
                                                <span class="block text-[10px] text-slate-500 font-mono">{{ $studentGroup->batch }}</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-slate-700">
                                        <span class="font-bold">{{ $studentGroup?->course?->title ?? '-' }}</span>
                                        <span class="block text-[10px] text-slate-500">{{ $studentGroup?->level?->title ?? '-' }}</span>
                                    </td>
                                    <td class="p-3 text-slate-700">
                                        {{ $studentGroup?->primaryTeacher()?->name ?: ($studentGroup?->teacher?->name ?: '-') }}
                                    </td>
                                    <td class="p-3 text-center font-mono font-bold">
                                        @if($bal > 0)
                                            <span class="text-rose-600">متبقي: {{ number_format($bal, 2) }}</span>
                                        @else
                                            <span class="text-emerald-600">مسدد بالكامل</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center no-print">
                                        <a href="{{ route('admin.reports.student.transcript', $st->id) }}" target="_blank" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-[10px] font-bold transition inline-flex items-center gap-1 border border-indigo-200">
                                            <span>📜</span>
                                            <span>السجل الشامل 360</span>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- 5. التوقيعات الرسمية -->
            <div class="print-break-inside-avoid pt-6 border-t-2 border-slate-900 space-y-6">
                <div class="grid grid-cols-3 gap-6 text-center pt-4">
                    <div>
                        <span class="text-xs text-slate-500 font-bold block mb-8">شؤون الطلاب والتسجيل</span>
                        <span class="border-b-2 border-dashed border-slate-400 block w-32 mx-auto"></span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-bold block mb-8">المشرف الأكاديمي للترم</span>
                        <span class="border-b-2 border-dashed border-slate-400 block w-32 mx-auto"></span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-bold block mb-8">اعتماد الإدارة العامة</span>
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
