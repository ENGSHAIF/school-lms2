<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - ASOTS LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #ffffff;
            color: #0f172a;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                background: white;
            }
            @page {
                size: A4 portrait;
                margin: 12mm;
            }
        }
    </style>
</head>
<body class="p-6 sm:p-10 max-w-5xl mx-auto text-slate-900">

    <!-- شريط التحكم العلوي قبل الطباعة (يختفي أثناء الطباعة) -->
    <div class="no-print mb-8 p-4 bg-slate-900 text-white rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xl">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🖨️</span>
            <div>
                <h4 class="font-black text-sm">جاهز للطباعة والتصدير الورقي الرسمي</h4>
                <p class="text-xs text-slate-300">نسخة معتمدة ومطابقة للمعايير الإدارية والأكاديمية لمنصة ASOTS</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs transition shadow flex items-center gap-1.5">
                <span>🖨️</span>
                <span>طباعة المستند الآن</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition">
                إغلاق النافذة ✕
            </button>
        </div>
    </div>

    <!-- ترويسة الوثيقة الرسمية (Official Header) -->
    <header class="border-b-2 border-slate-900 pb-5 mb-6">
        <div class="flex items-center justify-between">
            <div class="text-right space-y-1">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight">
                    {{ site_setting('site_name_ar', 'أكاديمية جنوب الجزيرة للغات والتدريب') }}
                </h1>
                <p class="text-xs font-bold text-slate-600">
                    {{ site_setting('site_name_en', 'Arab South Online Training & Schooling (ASOTS)') }}
                </p>
                <div class="text-[11px] text-slate-500 font-semibold">
                    مركز الشؤون الأكاديمية والتقارير الإدارية الموحدة
                </div>
            </div>

            <!-- الشعار الرسمي -->
            <div class="flex flex-col items-center justify-center">
                @php
                    $logoPath = site_setting('site_logo');
                @endphp
                @if($logoPath)
                    <img src="{{ asset($logoPath) }}" alt="Logo" class="h-16 w-auto object-contain mb-1">
                @else
                    <div class="w-14 h-14 bg-slate-900 text-amber-400 font-black rounded-2xl flex items-center justify-center text-2xl shadow">
                        ⭐
                    </div>
                @endif
                <span class="text-[10px] font-mono font-bold text-slate-400">ASOTS LMS</span>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-dashed border-slate-300 flex flex-wrap justify-between items-center text-xs text-slate-600">
            <div>
                <span class="font-bold text-slate-900">تاريخ وساعة التقرير:</span>
                <span class="font-mono">{{ now()->format('Y-m-d H:i') }}</span>
            </div>
            <div>
                <span class="font-bold text-slate-900">المستخدم المستخرج:</span>
                <span>{{ auth()->user()->name }} ({{ auth()->user()->role === 'admin' ? 'مدير عام' : 'مشرف أكاديمي' }})</span>
            </div>
            <div>
                <span class="font-bold text-slate-900">الرقم المرجعي للتقرير:</span>
                <span class="font-mono">REP-{{ date('Ymd') }}-{{ rand(1000, 9999) }}</span>
            </div>
        </div>
    </header>

    <!-- عنوان التقرير وملخص المعايير -->
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="text-lg font-black text-slate-900">{{ $title }}</h2>
                @if($subtitle)
                    <p class="text-xs text-slate-600 font-medium">{{ $subtitle }}</p>
                @endif
            </div>

            <!-- ملخص رقمي سريع -->
            <div class="flex items-center gap-3 text-xs flex-wrap">
                @foreach($summary as $label => $val)
                    <div class="bg-white px-3 py-1.5 rounded-lg border border-slate-200 text-right">
                        <span class="text-slate-500 font-semibold block text-[10px]">{{ $label }}:</span>
                        <span class="font-black text-slate-900">{{ $val }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- جدول التقرير بحسب النوع المختار -->

    <!-- 1. تقرير المجموعات الدراسية -->
    @if($type === 'groups')
        <table class="w-full text-right text-xs border border-slate-300 mb-6">
            <thead class="bg-slate-100 text-slate-900 font-black border-b border-slate-300">
                <tr>
                    <th class="p-2.5 border-l border-slate-300">#ID</th>
                    <th class="p-2.5 border-l border-slate-300">اسم المجموعة</th>
                    <th class="p-2.5 border-l border-slate-300">الدورة والمستوى</th>
                    <th class="p-2.5 border-l border-slate-300">التسعيرة المعتمدة</th>
                    <th class="p-2.5 border-l border-slate-300">الأستاذ المسؤول</th>
                    <th class="p-2.5 border-l border-slate-300 text-center">المقاعد</th>
                    <th class="p-2.5 border-l border-slate-300 text-center">الطلاب</th>
                    <th class="p-2.5 border-l border-slate-300 text-center">الشاغر</th>
                    <th class="p-2.5 border-l border-slate-300">المواعيد</th>
                    <th class="p-2.5 text-center">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 font-medium">
                @foreach($data['groups'] as $grp)
                    <tr class="hover:bg-slate-50">
                        <td class="p-2.5 border-l border-slate-200 font-bold">#{{ $grp->id }}</td>
                        <td class="p-2.5 border-l border-slate-200 font-bold text-slate-900">{{ $grp->name }}</td>
                        <td class="p-2.5 border-l border-slate-200">
                            {{ $grp->course?->name }} - {{ $grp->level?->name }}
                        </td>
                        <td class="p-2.5 border-l border-slate-200 font-bold">
                            {{ $grp->level?->formatted_fee ?? '-' }}
                        </td>
                        <td class="p-2.5 border-l border-slate-200">{{ $grp->teacher?->name ?? 'غير محدد' }}</td>
                        <td class="p-2.5 border-l border-slate-200 text-center font-bold">{{ $grp->max_students }}</td>
                        <td class="p-2.5 border-l border-slate-200 text-center font-bold text-purple-700">{{ $grp->students->count() }}</td>
                        <td class="p-2.5 border-l border-slate-200 text-center font-bold text-emerald-700">{{ max(0, $grp->max_students - $grp->students->count()) }}</td>
                        <td class="p-2.5 border-l border-slate-200">{{ $grp->schedule_days }} {{ $grp->schedule_time }}</td>
                        <td class="p-2.5 text-center font-bold">
                            {{ $grp->status === 'active' ? 'نشطة' : 'مغلقة' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-100 font-black border-t-2 border-slate-400">
                <tr>
                    <td colspan="5" class="p-2.5 border-l border-slate-300 text-left pl-4">الإجماليات الكلية:</td>
                    <td class="p-2.5 border-l border-slate-300 text-center">{{ $data['total_capacity'] }}</td>
                    <td class="p-2.5 border-l border-slate-300 text-center">{{ $data['total_enrolled'] }}</td>
                    <td class="p-2.5 border-l border-slate-300 text-center">{{ $data['total_available_seats'] }}</td>
                    <td colspan="2" class="p-2.5 text-center">نسبة الإشغال: {{ $data['occupancy_rate'] }}%</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- 2. تقرير الدورات والتسعيرة -->
    @if($type === 'courses')
        <div class="space-y-6 mb-6">
            @foreach($data['courses'] as $course)
                <div class="border border-slate-300 rounded-xl overflow-hidden">
                    <div class="bg-slate-100 p-3 border-b border-slate-300 flex justify-between items-center">
                        <span class="font-black text-sm text-slate-900">{{ $course->name }}</span>
                        <span class="text-xs font-bold text-slate-600">{{ $course->levels->count() }} مستويات معتمدة</span>
                    </div>
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-700">
                            <tr>
                                <th class="p-2 border-l border-slate-200">المستوى</th>
                                <th class="p-2 border-l border-slate-200">الترتيب</th>
                                <th class="p-2 border-l border-slate-200">رسوم المستوى والتسعيرة الرسمية</th>
                                <th class="p-2 border-l border-slate-200 text-center">عدد الحصص</th>
                                <th class="p-2 border-l border-slate-200 text-center">الكتب</th>
                                <th class="p-2 text-center">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 font-medium">
                            @foreach($course->levels as $lvl)
                                <tr>
                                    <td class="p-2 border-l border-slate-200 font-bold">{{ $lvl->name }}</td>
                                    <td class="p-2 border-l border-slate-200">مستوى {{ $lvl->level_order }}</td>
                                    <td class="p-2 border-l border-slate-200 font-black text-sm">{{ $lvl->formatted_fee }}</td>
                                    <td class="p-2 border-l border-slate-200 text-center">{{ $lvl->lessons->count() }}</td>
                                    <td class="p-2 border-l border-slate-200 text-center">{{ $lvl->books->count() }}</td>
                                    <td class="p-2 text-center font-bold">{{ $lvl->is_active ? 'نشط' : 'معطل' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            <!-- باقات الحصص الإضافية -->
            <div class="border border-slate-300 rounded-xl overflow-hidden mt-6">
                <div class="bg-slate-100 p-3 border-b border-slate-300 font-black text-sm text-slate-900">
                    باقات الحصص الإضافية والتفاعلية (25 دقيقة)
                </div>
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-700">
                        <tr>
                            <th class="p-2 border-l border-slate-200">اسم الباقة</th>
                            <th class="p-2 border-l border-slate-200 text-center">عدد الحصص</th>
                            <th class="p-2 border-l border-slate-200">سعر الباقة</th>
                            <th class="p-2 border-l border-slate-200">متوسط سعر الحصة</th>
                            <th class="p-2 text-center">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-medium">
                        @foreach($data['packages'] as $pkg)
                            <tr>
                                <td class="p-2 border-l border-slate-200 font-bold">{{ $pkg->title }}</td>
                                <td class="p-2 border-l border-slate-200 text-center font-bold">{{ $pkg->lessons_count }}</td>
                                <td class="p-2 border-l border-slate-200 font-black">{{ $pkg->formatted_price }}</td>
                                <td class="p-2 border-l border-slate-200">
                                    {{ $pkg->lessons_count > 0 ? round($pkg->price / $pkg->lessons_count, 2) : 0 }} {{ $pkg->currency }}
                                </td>
                                <td class="p-2 text-center font-bold">{{ $pkg->is_active ? 'نشطة' : 'معطلة' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- 3. كشف السندات المالية -->
    @if($type === 'vouchers')
        <table class="w-full text-right text-xs border border-slate-300 mb-6">
            <thead class="bg-slate-100 text-slate-900 font-black border-b border-slate-300">
                <tr>
                    <th class="p-2.5 border-l border-slate-300">رقم السند</th>
                    <th class="p-2.5 border-l border-slate-300">دفتري</th>
                    <th class="p-2.5 border-l border-slate-300">التاريخ</th>
                    <th class="p-2.5 border-l border-slate-300">النوع</th>
                    <th class="p-2.5 border-l border-slate-300">الحساب / المستفيد</th>
                    <th class="p-2.5 border-l border-slate-300">البيان</th>
                    <th class="p-2.5 border-l border-slate-300">المبلغ</th>
                    <th class="p-2.5 border-l border-slate-300">الدفع</th>
                    <th class="p-2.5 text-center">المحرر</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 font-medium">
                @foreach($data['vouchers'] as $v)
                    <tr class="hover:bg-slate-50">
                        <td class="p-2.5 border-l border-slate-200 font-mono font-bold">{{ $v->voucher_no }}</td>
                        <td class="p-2.5 border-l border-slate-200 font-mono">{{ $v->manual_no ?? '-' }}</td>
                        <td class="p-2.5 border-l border-slate-200">{{ $v->created_at->format('Y-m-d') }}</td>
                        <td class="p-2.5 border-l border-slate-200 font-bold">
                            {{ $v->voucher_type === 'receipt' ? 'قبض 🟢' : 'صرف 🔴' }}
                        </td>
                        <td class="p-2.5 border-l border-slate-200 font-bold">
                            {{ $v->account?->user?->name ?? $v->account?->account_name }}
                        </td>
                        <td class="p-2.5 border-l border-slate-200 max-w-xs truncate">{{ $v->statement }}</td>
                        <td class="p-2.5 border-l border-slate-200 font-black text-sm">
                            {{ number_format($v->amount, 2) }} {{ $v->currency }}
                        </td>
                        <td class="p-2.5 border-l border-slate-200">
                            {{ $v->payment_method === 'cash' ? 'كاش' : ($v->payment_method === 'kuraimi' ? 'كريمي' : 'تحويل') }}
                        </td>
                        <td class="p-2.5 text-center">{{ $v->creator?->name ?? 'مدير' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-100 font-black border-t-2 border-slate-400">
                <tr>
                    <td colspan="6" class="p-2.5 border-l border-slate-300 text-left pl-4">صافي حركة السندات:</td>
                    <td class="p-2.5 border-l border-slate-300 font-black text-sm">{{ number_format($data['net_amount'], 2) }} YER</td>
                    <td colspan="2" class="p-2.5 text-center">
                        قبض: {{ number_format($data['total_receipts_amount'], 2) }} | صرف: {{ number_format($data['total_payments_amount'], 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- 4. كشف الطلاب -->
    @if($type === 'students')
        <table class="w-full text-right text-xs border border-slate-300 mb-6">
            <thead class="bg-slate-100 text-slate-900 font-black border-b border-slate-300">
                <tr>
                    <th class="p-2.5 border-l border-slate-300">#ID</th>
                    <th class="p-2.5 border-l border-slate-300">اسم الطالب</th>
                    <th class="p-2.5 border-l border-slate-300">الهاتف / الواتساب</th>
                    <th class="p-2.5 border-l border-slate-300">المجموعة المسكن بها</th>
                    <th class="p-2.5 border-l border-slate-300">الرصيد المالي</th>
                    <th class="p-2.5 text-center">تاريخ التسجيل</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 font-medium">
                @foreach($data['students'] as $stu)
                    @php
                        $grp = $stu->studyGroups->where('pivot.status', 'active')->first();
                        $bal = $stu->financialAccount?->current_balance ?? 0;
                    @endphp
                    <tr>
                        <td class="p-2.5 border-l border-slate-200 font-bold">#{{ $stu->id }}</td>
                        <td class="p-2.5 border-l border-slate-200 font-bold">{{ $stu->name }}</td>
                        <td class="p-2.5 border-l border-slate-200 font-mono">{{ $stu->phone_whatsapp ?? '-' }}</td>
                        <td class="p-2.5 border-l border-slate-200">
                            {{ $grp ? $grp->name : 'بانتظار التسكين' }}
                        </td>
                        <td class="p-2.5 border-l border-slate-200 font-bold">
                            {{ number_format($bal, 2) }} YER
                        </td>
                        <td class="p-2.5 text-center">{{ $stu->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- تذييل التقرير وتواقيع الاعتماد الرسمية (Signatures Block) -->
    <footer class="mt-12 pt-6 border-t-2 border-slate-300 text-xs text-slate-700">
        <div class="grid grid-cols-3 gap-6 text-center">
            <div class="space-y-8">
                <span class="font-bold block text-slate-900">المحاسب / مسؤول الإدخال</span>
                <span class="block border-b border-dashed border-slate-400 w-3/4 mx-auto"></span>
                <span class="text-[11px] text-slate-500">التوقيع والتاريخ</span>
            </div>

            <div class="space-y-8">
                <span class="font-bold block text-slate-900">مراجعة الشؤون الأكاديمية والإشراف</span>
                <span class="block border-b border-dashed border-slate-400 w-3/4 mx-auto"></span>
                <span class="text-[11px] text-slate-500">التوقيع والتاريخ</span>
            </div>

            <div class="space-y-8">
                <span class="font-bold block text-slate-900">اعتماد إدارة الأكاديمية والختم الرسمي</span>
                <span class="block border-b border-dashed border-slate-400 w-3/4 mx-auto"></span>
                <span class="text-[11px] text-slate-500">الختم والاعتماد المالي</span>
            </div>
        </div>

        <div class="mt-8 pt-4 border-t border-slate-200 text-center text-[10px] text-slate-400 font-semibold">
            هذا المستند صادر إلكترونياً من نظام إدارة التعلم ASOTS LMS - جميع البيانات الواردة معتمدة رسمياً ومحمية.
        </div>
    </footer>

</body>
</html>
