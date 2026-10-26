<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير رقابي رسمي لتدقيق العمليات وسجلات النظام</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f8fafc; color: #0f172a; }
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            .print-border { border: 1px solid #334155 !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- شريط التحكم والإجراءات للطباعة -->
        <div class="no-print bg-slate-900 text-white p-4 rounded-2xl shadow-md flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🛡️</span>
                <div>
                    <h2 class="text-sm font-black">معاينة التقرير الرقابي المعتمد للطباعة</h2>
                    <p class="text-xs text-slate-400">يمكنك طباعة التقرير أو حفظه كملف PDF معتمد مع كافة تفاصيل الحركات وأجهزة المستخدمين.</p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                    <span>🖨️</span>
                    <span>طباعة التقرير / Save as PDF</span>
                </button>
                <button onclick="window.close()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition">
                    إغلاق
                </button>
            </div>
        </div>

        <!-- المستند الرسمي القابل للطباعة -->
        <div class="bg-white border-2 border-slate-800 rounded-3xl p-8 sm:p-10 shadow-lg space-y-6">

            <!-- ترويسة التقرير الرسمية -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 border-b-2 border-slate-800 gap-4">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/logo.png') }}" alt="ASOTS Logo" class="w-16 h-16 object-contain rounded-2xl border border-slate-300 p-1 bg-white shrink-0">
                    <div>
                        <h1 class="text-lg font-black text-slate-900 tracking-tight">
                            {{ \App\Models\SiteSetting::get('school_name', 'مكتب النجم الآسيوي للترجمة والتدريب وخدمات الاستشارات') }}
                        </h1>
                        <span class="text-xs text-slate-600 font-bold block mt-0.5">
                            إدارة الرقابة الداخلية وتدقيق العمليات • وحدة الأمان المالي والإداري
                        </span>
                        <span class="text-[11px] text-slate-400 font-mono">SYSTEM AUDIT TRAIL & SURVEILLANCE REPORT</span>
                    </div>
                </div>
                <div class="text-left sm:text-left text-xs space-y-1">
                    <div><span class="font-bold text-slate-600">تاريخ إصدار التقرير:</span> <span class="font-mono font-bold">{{ date('Y/m/d - H:i') }}</span></div>
                    <div><span class="font-bold text-slate-600">المشرف المنفذ للطباعة:</span> <span class="font-bold text-slate-900">{{ auth()->user()->name }}</span></div>
                    <div><span class="font-bold text-slate-600">عدد العمليات المضمنة:</span> <span class="font-bold text-indigo-700 font-mono">{{ $logs->count() }} حركة</span></div>
                </div>
            </div>

            <!-- عنوان التقرير وفترة التصفية -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                <div>
                    <span class="font-black text-slate-900 text-sm">تقرير تدقيق العمليات والأنشطة الإدارية والمالية</span>
                    <p class="text-slate-500 mt-0.5">
                        يتضمن أسماء المستخدمين، الحركات المنفذة، اسم الجهاز والمتصفح، عنوان الـ IP، والبيانات قبل وبعد التعديل.
                    </p>
                </div>
                <div class="flex items-center gap-3 font-mono text-[11px] text-slate-600">
                    <span>من: {{ request('date_from', 'بداية السجلات') }}</span>
                    <span>|</span>
                    <span>إلى: {{ request('date_to', date('Y-m-d')) }}</span>
                </div>
            </div>

            <!-- جدول العمليات -->
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-800 text-white border border-slate-800 text-[11px] font-black">
                            <th class="py-2.5 px-3 border border-slate-700 text-center w-12">#</th>
                            <th class="py-2.5 px-3 border border-slate-700 whitespace-nowrap">التاريخ والوقت</th>
                            <th class="py-2.5 px-3 border border-slate-700 whitespace-nowrap">المستخدم المنفذ</th>
                            <th class="py-2.5 px-3 border border-slate-700 whitespace-nowrap">الجهاز والـ IP</th>
                            <th class="py-2.5 px-3 border border-slate-700 whitespace-nowrap">نوع الإجراء</th>
                            <th class="py-2.5 px-3 border border-slate-700">بيان وملخص العملية</th>
                            <th class="py-2.5 px-3 border border-slate-700">القيم السابقة / الجديدة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($logs as $index => $log)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50/70' }}">
                                <td class="py-2.5 px-3 border border-slate-200 text-center font-mono font-bold text-slate-500">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-2.5 px-3 border border-slate-200 whitespace-nowrap">
                                    <div class="font-bold text-slate-900">{{ $log->formatted_date }}</div>
                                    <div class="text-[10px] font-mono text-slate-500">{{ $log->formatted_time }}</div>
                                </td>
                                <td class="py-2.5 px-3 border border-slate-200 whitespace-nowrap">
                                    <div class="font-bold text-slate-900">{{ $log->user?->name ?? 'النظام تلقائي' }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">[{{ $log->user?->user_code ?? '-' }}] - {{ $log->user?->role }}</div>
                                </td>
                                <td class="py-2.5 px-3 border border-slate-200 whitespace-nowrap">
                                    <div class="font-bold text-slate-800 text-[11px]">{{ $log->device_display }}</div>
                                    <div class="text-[10px] font-mono text-slate-500">IP: {{ $log->ip_address }}</div>
                                </td>
                                <td class="py-2.5 px-3 border border-slate-200 whitespace-nowrap">
                                    <span class="font-bold text-slate-800 block text-[11px]">{{ $log->module_info['name'] }}</span>
                                    <span class="text-[10px] text-slate-500 font-mono">{{ $log->action }}</span>
                                </td>
                                <td class="py-2.5 px-3 border border-slate-200 font-medium text-slate-900 leading-relaxed">
                                    {{ $log->description }}
                                </td>
                                <td class="py-2.5 px-3 border border-slate-200 text-[11px]">
                                    @if($log->old_values || $log->new_values)
                                        <div class="space-y-1">
                                            @if($log->old_values)
                                                <div class="text-rose-800 font-bold">
                                                    <span>🔴 سابقاً: </span>
                                                    @foreach($log->old_values as $k => $v)
                                                        <span class="font-normal">{{ $k }}: <strong class="font-bold">{{ is_array($v) ? json_encode($v) : $v }}</strong></span>@if(!$loop->last)، @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if($log->new_values)
                                                <div class="text-emerald-800 font-bold">
                                                    <span>🟢 جديداً: </span>
                                                    @foreach($log->new_values as $k => $v)
                                                        <span class="font-normal">{{ $k }}: <strong class="font-bold">{{ is_array($v) ? json_encode($v) : $v }}</strong></span>@if(!$loop->last)، @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-center block">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-500 font-bold">
                                    لا توجد حركات مسجلة ضمن هذه الفترة
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- كتلة التوقيعات والاعتماد الرسمي -->
            <div class="pt-8 border-t-2 border-slate-800 mt-8 grid grid-cols-3 gap-6 text-center text-xs">
                <div class="space-y-8">
                    <span class="font-black text-slate-800 block">المراجع الداخلي للأنظمة</span>
                    <span class="block text-slate-400 font-mono">........................................</span>
                </div>
                <div class="space-y-8">
                    <span class="font-black text-slate-800 block">مسؤول الرقابة المالية والإدارية</span>
                    <span class="block text-slate-400 font-mono">........................................</span>
                </div>
                <div class="space-y-8">
                    <span class="font-black text-slate-800 block">مدير عام المنصة / الختم الرسمي</span>
                    <span class="block text-slate-400 font-mono">........................................</span>
                </div>
            </div>

            <!-- حاشية سرية التقرير -->
            <div class="pt-4 border-t border-slate-200 text-center text-[10px] text-slate-400">
                هذا التقرير وثيقة رقابية سرية داخلية تصدر آلياً من سجل التدقيق المشفر للنظام. يمنع تداوله لغير المخولين.
            </div>

        </div>

    </div>

</body>
</html>
