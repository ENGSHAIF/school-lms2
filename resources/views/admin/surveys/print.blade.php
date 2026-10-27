<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير استبيانات وتقييمات الطلاب - ASOTS</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        @media print {
            @page {
                size: A4;
                margin: 12mm;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <!-- شريط أدوات الطباعة (يختفي عند الطباعة) -->
    <div class="no-print max-w-5xl mx-auto mb-6 p-4 rounded-2xl bg-slate-900 text-white flex flex-wrap items-center justify-between gap-4 shadow-xl">
        <div class="flex items-center gap-3">
            <span class="text-xl">🖨️</span>
            <div>
                <h2 class="text-sm font-bold">جاهز للطباعة وتصدير PDF</h2>
                <p class="text-[11px] text-slate-300">تم تجهيز التقرير بملف ومقاسات الورق القياسي A4 للمكتب</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs transition flex items-center gap-2 shadow-md">
                <span>طباعة التقرير الآن</span>
                <span>🖨️</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                إغلاق النافذة
            </button>
        </div>
    </div>

    <!-- حاوية التقرير الرسمية للطباعة -->
    <div class="max-w-5xl mx-auto bg-white p-8 sm:p-12 rounded-3xl border border-slate-200 shadow-sm space-y-6">

        <!-- ترويسة التقرير الرسمية (Header) -->
        <div class="flex items-center justify-between pb-6 border-b-2 border-slate-900">
            <div class="space-y-1">
                <h2 class="text-lg font-black text-slate-900">مكتب النجم الآسيوي للخدمات التعليمية والاستشارية والترجمة</h2>
                <h3 class="text-xs font-bold text-slate-600 font-mono tracking-wider">The Asian Star Office for Educational & Consultancy Services (ASOTS)</h3>
                <p class="text-[11px] text-slate-500">
                    بإشراف مؤسس ومدير المكتب: <strong class="text-slate-800">د. فخرالدين الصوفي</strong>
                </p>
            </div>

            <div class="text-left space-y-1">
                @if(file_exists(public_path('uploads/branding/asots_logo.png')))
                    <img src="{{ asset('uploads/branding/asots_logo.png') }}" alt="Logo" class="h-16 w-auto inline-block">
                @elseif(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 w-auto inline-block">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-indigo-900 text-white font-black flex items-center justify-center text-xl">
                        ASOTS
                    </div>
                @endif
                <div class="text-[10px] text-slate-400 font-mono">ترخيص معتمد</div>
            </div>
        </div>

        <!-- عنوان التقرير وبيانات الفلترة -->
        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-[10px] font-black border border-indigo-200">
                        تقرير أكاديمي رسمي 📊
                    </span>
                    <h1 class="text-xl font-black text-slate-900 mt-1">
                        تقرير استبيانات وتقييمات الطلاب
                        @if($selectedTeacher)
                            - للأستاذ: {{ $selectedTeacher->name }}
                        @else
                            - الأداء العام لكافة الأساتذة
                        @endif
                    </h1>
                </div>

                <div class="text-left text-xs font-mono text-slate-500">
                    <div>تاريخ الإصدار: {{ date('Y-m-d H:i') }}</div>
                    <div>إجمالي التقييمات المشمولة: <strong class="text-slate-900">{{ $totalSurveys }}</strong></div>
                </div>
            </div>

            <!-- نطاق وفترة التقرير -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 border-t border-slate-200 text-xs">
                <div>
                    <span class="text-slate-400 block text-[10px]">نوع التقييم:</span>
                    <strong class="text-slate-800">
                        @if($surveyType === 'daily') ⚡ التقييم اليومي السريع
                        @elseif($surveyType === 'weekly') 📊 التقييم الأسبوعي الشامل
                        @elseif($surveyType === 'diploma_final') 🎓 التقييم النهائي للدبلوم
                        @else كامل أنواع التقييمات
                        @endif
                    </strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">الأستاذ المعني:</span>
                    <strong class="text-slate-800">{{ $selectedTeacher ? $selectedTeacher->name : 'كافة الأساتذة' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">المجموعة الدراسية:</span>
                    <strong class="text-slate-800">{{ $selectedGroup ? ($selectedGroup->name_ar ?: $selectedGroup->name) : 'كافة المجموعات' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">فترة التقرير:</span>
                    <strong class="text-slate-800">
                        @if($period === 'today') اليوم ({{ date('Y-m-d') }})
                        @elseif($period === 'week') هذا الأسبوع
                        @elseif($period === 'month') هذا الشهر ({{ date('Y-m') }})
                        @elseif(!empty($dateFrom) && !empty($dateTo)) من {{ $dateFrom }} إلى {{ $dateTo }}
                        @else السجل التراكمي الشامل
                        @endif
                    </strong>
                </div>
            </div>
        </div>

        <!-- بطاقات المؤشرات الرقمية (KPIs Summary) -->
        <div class="grid grid-cols-4 gap-3">
            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 text-center space-y-1">
                <span class="text-[10px] font-bold text-slate-500 block">متوسط تقييم المدرسين</span>
                <div class="text-xl font-black text-amber-500 font-mono">
                    ★ {{ $avgTeacher > 0 ? number_format($avgTeacher, 2) : '-' }}
                </div>
                <span class="text-[10px] text-slate-400">من 5</span>
            </div>

            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 text-center space-y-1">
                <span class="text-[10px] font-bold text-slate-500 block">ملاءمة وجودة المنهج</span>
                <div class="text-xl font-black text-indigo-600 font-mono">
                    ★ {{ $avgCurriculum > 0 ? number_format($avgCurriculum, 2) : '-' }}
                </div>
                <span class="text-[10px] text-slate-400">من 5</span>
            </div>

            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 text-center space-y-1">
                <span class="text-[10px] font-bold text-slate-500 block">الإدارة والدعم والتعلم</span>
                <div class="text-xl font-black text-slate-700 font-mono">
                    ★ {{ $avgSupport > 0 ? number_format($avgSupport, 2) : '-' }}
                </div>
                <span class="text-[10px] text-slate-400">من 5</span>
            </div>

            <div class="p-4 rounded-2xl border border-emerald-200 bg-emerald-50 text-center space-y-1">
                <span class="text-[10px] font-bold text-emerald-800 block">معدل الرضا العام</span>
                <div class="text-xl font-black text-emerald-700 font-mono">
                    {{ $avgOverall > 0 ? round(($avgOverall / 5) * 100) : 0 }}%
                </div>
                <span class="text-[10px] text-emerald-600">★ {{ number_format($avgOverall, 2) }} / 5</span>
            </div>
        </div>

        <!-- المعايير التفصيلية لكفاءة التدريس -->
        <div class="p-5 rounded-2xl border border-slate-200 space-y-3">
            <h3 class="text-xs font-black text-slate-900 border-b pb-2 flex items-center justify-between">
                <span>تفصيل محاور كفاءة التدريس والشرح (من 5)</span>
                <span class="text-[10px] text-slate-400">درجات الطلاب المباشرة</span>
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-slate-500 block text-[11px]">وضوح الشرح وتبسيطه:</span>
                    <strong class="text-slate-900 font-mono text-sm">★ {{ $avgClarity > 0 ? number_format($avgClarity, 2) : '-' }}</strong>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-slate-500 block text-[11px]">الكفاءة العلمية والتمكن:</span>
                    <strong class="text-slate-900 font-mono text-sm">★ {{ $avgCompetence > 0 ? number_format($avgCompetence, 2) : '-' }}</strong>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-slate-500 block text-[11px]">أسلوب التدريس والتفاعل:</span>
                    <strong class="text-slate-900 font-mono text-sm">★ {{ $avgInteraction > 0 ? number_format($avgInteraction, 2) : '-' }}</strong>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-slate-500 block text-[11px]">الالتزام بالوقت والتحفيز:</span>
                    <strong class="text-slate-900 font-mono text-sm">★ {{ $avgPunctuality > 0 ? number_format($avgPunctuality, 2) : '-' }}</strong>
                </div>
            </div>
        </div>

        <!-- جدول التقييمات الفردية للطلاب -->
        <div class="space-y-3">
            <h3 class="text-xs font-black text-slate-900 border-b pb-2">سجل إجابات الطلاب والتقييمات الفردية:</h3>

            @if($surveys->isEmpty())
                <div class="text-center py-6 text-slate-400 text-xs">
                    لا توجد سجلات مطابقة لهذه الفترة.
                </div>
            @else
                <table class="w-full text-right text-xs border border-slate-200">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                            <th class="p-2 border-l border-slate-200">#</th>
                            <th class="p-2 border-l border-slate-200">التاريخ</th>
                            <th class="p-2 border-l border-slate-200">النوع</th>
                            <th class="p-2 border-l border-slate-200">الأستاذ</th>
                            <th class="p-2 border-l border-slate-200">الطالب</th>
                            <th class="p-2 border-l border-slate-200 text-center">تقييم المدرس</th>
                            <th class="p-2 border-l border-slate-200 text-center">الإجمالي</th>
                            <th class="p-2">الملاحظات والمقترح</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($surveys as $idx => $s)
                            <tr>
                                <td class="p-2 border-l border-slate-200 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                <td class="p-2 border-l border-slate-200 font-mono">{{ $s->survey_date->format('Y-m-d') }}</td>
                                <td class="p-2 border-l border-slate-200 font-bold">
                                    {{ $s->type_label }}
                                </td>
                                <td class="p-2 border-l border-slate-200 font-bold">
                                    {{ $s->teacher ? $s->teacher->name : 'عام' }}
                                </td>
                                <td class="p-2 border-l border-slate-200">
                                    {{ $s->display_name }}
                                </td>
                                <td class="p-2 border-l border-slate-200 text-center font-bold text-amber-600">
                                    ★ {{ $s->teacher_rating ?: ($s->teacher_clarity ?: '-') }}
                                </td>
                                <td class="p-2 border-l border-slate-200 text-center font-bold font-mono">
                                    {{ $s->overall_score ? number_format($s->overall_score, 1) . ' / 5' : '-' }}
                                </td>
                                <td class="p-2 text-slate-600 text-[11px] max-w-xs">
                                    {{ $s->notes_suggestions ?: ($s->best_feature ?: '-') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- قسم التوصيات والملاحظات النوعية للطلاب -->
        @php
            $notedSurveys = $surveys->filter(fn ($s) => !empty($s->notes_suggestions) || !empty($s->best_feature));
        @endphp
        @if($notedSurveys->isNotEmpty())
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <h3 class="text-xs font-black text-slate-900 border-b pb-2">
                    أبرز مقترحات وملاحظات الطلاب النوعية:
                </h3>
                <div class="space-y-2 text-xs text-slate-700">
                    @foreach($notedSurveys->take(8) as $ns)
                        <div class="p-2 rounded-xl bg-white border border-slate-100 flex items-start gap-2">
                            <span class="text-indigo-600 font-bold">💬</span>
                            <div>
                                <span class="font-bold text-slate-900">{{ $ns->display_name }} ({{ $ns->survey_date->format('Y-m-d') }}):</span>
                                <span class="text-slate-600 mr-1">{{ $ns->notes_suggestions ?: $ns->best_feature }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- صندوق الاعتماد والتوقيع الرسمي -->
        <div class="pt-8 border-t-2 border-slate-200 grid grid-cols-2 gap-8 text-center text-xs">
            <div class="space-y-8">
                <p class="font-bold text-slate-700">إعداد / المشرف الأكاديمي</p>
                <div class="text-slate-400 font-mono text-[11px]">التوقيع: ............................</div>
            </div>
            <div class="space-y-8">
                <p class="font-bold text-slate-900">اعتماد مدير المنصة / د. فخرالدين الصوفي</p>
                <div class="text-slate-400 font-mono text-[11px]">الختم الرسمي والتوقيع: ............................</div>
            </div>
        </div>

        <!-- تذييل التقرير -->
        <div class="pt-4 text-center text-[10px] text-slate-400 border-t border-slate-100">
            ASOTS Education & Training Platform • نظام إدارة الاستبيانات والتقييم الأكاديمي • {{ config('asots.contact.phone_1', '+967 735 999 101') }}
        </div>

    </div>

</body>
</html>
