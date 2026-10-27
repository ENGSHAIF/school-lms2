@extends('layouts.app', ['title' => 'مركز استبيانات وتقييمات الطلاب الشامل'])

@section('content')
<div class="space-y-6">

    <!-- ترويسة الصفحة والتحكم العام -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border border-slate-800">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-bold border border-indigo-400/30">
                    منظومة قياس رضا الطلاب وجودة التدريس 📊
                </span>
                <span class="text-xs text-amber-400 font-bold">بإشراف د. فخرالدين الصوفي</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">مركز استبيانات وتقييمات الطلاب والأداء الأكاديمي</h1>
            <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                متابعة استبيانات الطلاب اليومية، الأسبوعية، وتقييمات نهاية الدبلوم، مع تقارير تفصيلية حسب الأستاذ، المجموعة، والفترة الزمنية.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
            <!-- زر فتح قوالب الواتساب وروابط الاستبيان -->
            <button type="button" onclick="openWhatsAppModal()"
                    class="flex-1 md:flex-none px-4 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                <span>📱</span>
                <span>قوالب وروابط الواتساب</span>
            </button>

            <!-- زر طباعة التقرير التحليلي المفلتر -->
            <a href="{{ route('admin.surveys.print', request()->all()) }}" target="_blank"
               class="flex-1 md:flex-none px-4 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/20 transition flex items-center justify-center gap-2">
                <span>🖨️</span>
                <span>طباعة تقرير التقييم</span>
            </a>
        </div>
    </div>

    <!-- شريط الإحصائيات العامة ومؤشرات الأداء (KPIs) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- إجمالي التقييمات -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block">إجمالي التقييمات المستلمة</span>
            <div class="text-2xl font-black text-slate-900 font-mono flex items-center gap-2">
                <span>📋</span>
                <span>{{ number_format($totalSurveys) }}</span>
                <span class="text-xs font-normal text-slate-400">استبيان</span>
            </div>
        </div>

        <!-- متوسط تقييم الأساتذة -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block">متوسط تقييم الأساتذة</span>
            <div class="text-2xl font-black text-amber-500 font-mono flex items-center gap-2">
                <span>⭐</span>
                <span>{{ $avgTeacher > 0 ? number_format($avgTeacher, 2) : '-' }}</span>
                <span class="text-xs font-bold text-slate-400">/ 5</span>
                @if($avgTeacher > 0)
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-bold">
                        {{ round(($avgTeacher / 5) * 100) }}%
                    </span>
                @endif
            </div>
        </div>

        <!-- متوسط تقييم المنهج -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block">ملاءمة وجودة المناهج</span>
            <div class="text-2xl font-black text-indigo-600 font-mono flex items-center gap-2">
                <span>📚</span>
                <span>{{ $avgCurriculum > 0 ? number_format($avgCurriculum, 2) : '-' }}</span>
                <span class="text-xs font-bold text-slate-400">/ 5</span>
                @if($avgCurriculum > 0)
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-bold">
                        {{ round(($avgCurriculum / 5) * 100) }}%
                    </span>
                @endif
            </div>
        </div>

        <!-- متوسط الرضا الإجمالي -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block">معدل الرضا العام للمنصة</span>
            <div class="text-2xl font-black text-emerald-600 font-mono flex items-center gap-2">
                <span>🌟</span>
                <span>{{ $avgOverall > 0 ? number_format($avgOverall, 2) : '-' }}</span>
                <span class="text-xs font-bold text-slate-400">/ 5</span>
                @if($avgOverall > 0)
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold">
                        {{ round(($avgOverall / 5) * 100) }}%
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- كارت الفلاتر المتقدمة والفرز -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-2xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h2 class="text-xs sm:text-sm font-black text-slate-800 flex items-center gap-2">
                <span>🔍</span>
                <span>تصفية وفلترة التقييمات (يومي / أسبوعي / شهري / حسب الأستاذ)</span>
            </h2>
            @if(request()->anyFilled(['survey_type', 'teacher_id', 'study_group_id', 'period', 'date_from', 'date_to', 'search']))
                <a href="{{ route('admin.surveys.index') }}" class="text-[11px] font-bold text-rose-600 hover:text-rose-700">
                    ✕ إلغاء الفلاتر
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.surveys.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            <!-- 1. نوع الاستبيان -->
            <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-500">نوع الاستبيان</label>
                <select name="survey_type" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-slate-50">
                    <option value="all" {{ $surveyType === 'all' ? 'selected' : '' }}>جميع الأنواع</option>
                    <option value="daily" {{ $surveyType === 'daily' ? 'selected' : '' }}>⚡ التقييم اليومي السريع</option>
                    <option value="weekly" {{ $surveyType === 'weekly' ? 'selected' : '' }}>📊 التقييم الأسبوعي الشامل</option>
                    <option value="diploma_final" {{ $surveyType === 'diploma_final' ? 'selected' : '' }}>🎓 التقييم النهائي للدبلوم</option>
                </select>
            </div>

            <!-- 2. الأستاذ -->
            <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-500">الأستاذ / المدرّس</label>
                <select name="teacher_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-slate-50">
                    <option value="">جميع الأساتذة</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ (string)$teacherId === (string)$t->id ? 'selected' : '' }}>
                            👨‍🏫 {{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. المجموعة الدراسية -->
            <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-500">المجموعة الدراسية</label>
                <select name="study_group_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-slate-50">
                    <option value="">جميع المجموعات</option>
                    @foreach($studyGroups as $g)
                        <option value="{{ $g->id }}" {{ (string)$studyGroupId === (string)$g->id ? 'selected' : '' }}>
                            👥 {{ $g->name_ar ?: $g->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 4. الفترة الزمنية -->
            <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-500">الفترة الزمنية</label>
                <select name="period" id="period-select" onchange="toggleCustomDates(this.value)" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-slate-50">
                    <option value="all" {{ $period === 'all' ? 'selected' : '' }}>كامل السجل</option>
                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>📅 اليوم فقط (Daily)</option>
                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>📈 هذا الأسبوع (Weekly)</option>
                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>🗓️ هذا الشهر (Monthly)</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>⏱️ نطاق مخصص (من - إلى)</option>
                </select>
            </div>

            <!-- 5. زر البحث والتطبيق -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                    <span>تطبيق الفلتر</span>
                    <span>✓</span>
                </button>
            </div>

            <!-- حقول التواريخ المخصصة تظهر عند اختيار مخصص -->
            <div id="custom-dates-row" class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 {{ $period === 'custom' || (!empty($dateFrom) && !empty($dateTo)) ? '' : 'hidden' }}">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">من تاريخ:</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">إلى تاريخ:</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">بحث بالطالب / الهاتف:</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم الطالب أو رقمه..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-slate-50">
                </div>
            </div>
        </form>
    </div>

    <!-- لوحة التحليلات المصغرة: تقييمات الأساتذة + التحديات -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- لوحة أداء الأساتذة (Teacher Leaderboard) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-2xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                    <span>👨‍🏫</span>
                    <span>مؤشر تقييم أداء الأساتذة (حسب التقييمات المفلترة)</span>
                </h3>
                <span class="text-xs text-slate-400 font-bold">{{ count($teacherStats) }} مدرّس تم تقييمهم</span>
            </div>

            @if(empty($teacherStats))
                <div class="text-center py-8 text-slate-400 text-xs font-bold">
                    لا توجد تقييمات مسجلة للأساتذة في هذه الفترة المحددة.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($teacherStats as $stat)
                        <div class="p-3.5 rounded-2xl bg-slate-50 hover:bg-slate-100/80 transition flex items-center justify-between gap-4 border border-slate-100">
                            <div class="flex items-center gap-3">
                                <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-black text-sm">
                                    ★
                                </span>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-black text-slate-900">{{ $stat['teacher']->name }}</h4>
                                    <span class="text-[11px] text-slate-400">{{ $stat['count'] }} تقييم مستلم</span>
                                </div>
                            </div>

                            <div class="text-left flex items-center gap-3">
                                <div class="hidden sm:block w-32 bg-slate-200 h-2 rounded-full overflow-hidden">
                                    <div class="bg-amber-500 h-2 rounded-full" style="width: {{ ($stat['avg'] / 5) * 100 }}%"></div>
                                </div>
                                <span class="text-sm font-black text-slate-900 font-mono">{{ number_format($stat['avg'], 2) }}</span>
                                <span class="text-xs text-amber-500">/ 5</span>
                                <a href="{{ route('admin.surveys.index', ['teacher_id' => $stat['teacher']->id]) }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-[10px] font-bold hover:bg-indigo-100 transition">
                                    تصفية 🔍
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- بطاقة التحديات الأكثر شيوعاً والتوصية -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-2xs space-y-5 flex flex-col justify-between">
            <div class="space-y-4">
                <h3 class="text-sm font-black text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span>⚠️</span>
                    <span>أبرز التحديات التي واجهت الطلاب</span>
                </h3>

                <div class="space-y-2.5 text-xs">
                    @php
                        $chLabels = [
                            'speaking' => ['🗣️', 'التحدث'],
                            'listening' => ['👂', 'الاستماع'],
                            'reading' => ['📖', 'القراءة'],
                            'writing' => ['✍️', 'الكتابة'],
                            'grammar' => ['📐', 'القواعد'],
                        ];
                        $maxCh = max(array_values($challengesCount) ?: [1]);
                        if ($maxCh == 0) $maxCh = 1;
                    @endphp
                    @foreach($chLabels as $k => $info)
                        <div class="space-y-1">
                            <div class="flex justify-between text-slate-700 font-bold text-[11px]">
                                <span>{{ $info[0] }} {{ $info[1] }}</span>
                                <span class="font-mono text-slate-500">{{ $challengesCount[$k] }} إشارة</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ ($challengesCount[$k] / $maxCh) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- بطاقة التوصية بـ ASOTS -->
            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100 space-y-2">
                <span class="text-[11px] font-black text-emerald-900 block">هل يوصي الطلاب بـ ASOTS للآخرين؟</span>
                <div class="flex items-center justify-between text-xs font-bold text-emerald-800">
                    <span>نعم بكل تأكيد: {{ $recommendationCount['yes'] }}</span>
                    <span>ربما: {{ $recommendationCount['maybe'] }}</span>
                    <span>لا: {{ $recommendationCount['no'] }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- جدول استعراض كافة الاستبيانات -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden space-y-4">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span>📑</span>
                    <span>سجل الاستبيانات والتقييمات التفصيلي</span>
                </h3>
                <p class="text-xs text-slate-500">يعرض أحدث التقييمات مع إمكانية استعراض ردود كل طالب وملاحظاته</p>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1 rounded-full">
                إجمالي المعروض: {{ $surveys->total() }} استبيان
            </span>
        </div>

        @if($surveys->isEmpty())
            <div class="text-center py-12 text-slate-400 space-y-2">
                <span class="text-4xl block">📭</span>
                <p class="text-xs font-bold">لا توجد استبيانات مطابقة للخيارات المحددة.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200/80">
                            <th class="p-3.5">النوع والتاريخ</th>
                            <th class="p-3.5">الأستاذ</th>
                            <th class="p-3.5">الطالب</th>
                            <th class="p-3.5">المجموعة</th>
                            <th class="p-3.5 text-center">تقييم المدرس</th>
                            <th class="p-3.5 text-center">التقييم الإجمالي</th>
                            <th class="p-3.5">الملاحظات والمقترح</th>
                            <th class="p-3.5 text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($surveys as $survey)
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- النوع والتاريخ -->
                                <td class="p-3.5 font-bold text-slate-900 space-y-1">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black border {{ $survey->type_badge_class }}">
                                        {{ $survey->type_icon }} {{ $survey->type_label }}
                                    </span>
                                    <div class="text-[11px] text-slate-400 font-mono">
                                        {{ $survey->survey_date->format('Y-m-d') }}
                                    </div>
                                </td>

                                <!-- الأستاذ -->
                                <td class="p-3.5 font-bold text-slate-900">
                                    {{ $survey->teacher ? $survey->teacher->name : 'غير محدد' }}
                                </td>

                                <!-- الطالب -->
                                <td class="p-3.5 text-slate-700">
                                    <div class="font-bold">{{ $survey->display_name }}</div>
                                    @if(!$survey->is_anonymous && $survey->student_phone)
                                        <div class="text-[10px] text-slate-400 font-mono" dir="ltr">{{ $survey->student_phone }}</div>
                                    @endif
                                </td>

                                <!-- المجموعة -->
                                <td class="p-3.5 text-slate-500">
                                    {{ $survey->studyGroup ? ($survey->studyGroup->name_ar ?: $survey->studyGroup->name) : 'عام' }}
                                </td>

                                <!-- تقييم المدرس -->
                                <td class="p-3.5 text-center">
                                    @if($survey->teacher_rating)
                                        <span class="font-bold text-amber-500 text-sm">★ {{ number_format($survey->teacher_rating, 1) }}</span>
                                    @elseif($survey->teacher_clarity)
                                        <span class="font-bold text-amber-500 text-sm">★ {{ $survey->teacher_clarity }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>

                                <!-- التقييم الإجمالي -->
                                <td class="p-3.5 text-center">
                                    @if($survey->overall_score)
                                        <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-700 font-mono font-black text-xs border border-emerald-100">
                                            {{ number_format($survey->overall_score, 1) }} / 5
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>

                                <!-- الملاحظات -->
                                <td class="p-3.5 text-slate-600 max-w-xs truncate">
                                    {{ $survey->notes_suggestions ?: ($survey->best_feature ?: '-') }}
                                </td>

                                <!-- إجراءات -->
                                <td class="p-3.5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- زر عرض التفاصيل الكاملة في نافذة منبثقة -->
                                        <button type="button" onclick="showSurveyDetail({{ $survey->id }})"
                                                class="p-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold transition" title="عرض تفاصيل الإجابات">
                                            👁️
                                        </button>

                                        <!-- زر الحذف -->
                                        <form action="{{ route('admin.surveys.destroy', $survey->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا التقييم؟');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold transition" title="حذف">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- ترقيم الصفحات -->
            <div class="p-5 border-t border-slate-100">
                {{ $surveys->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ================== نافذة منبثقة: قوالب الواتساب وروابط المشاركة المباشرة ================== -->
<div id="whatsapp-modal" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2">
                <span>📱</span>
                <span>قوالب رسائل الواتساب الجاهزة مع الروابط</span>
            </h3>
            <button type="button" onclick="closeWhatsAppModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold">✕</button>
        </div>

        <p class="text-xs text-slate-500 leading-relaxed">
            يمكنك نسخ أي من الصيغ الثلاث مباشرة وإرسالها للطلاب في مجموعات الواتساب بنقرة واحدة:
        </p>

        <div class="space-y-4">
            <!-- قالب 1: اليومي السريع -->
            <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="font-black text-xs text-amber-900">⚡ الصيغة الأولى: التقييم اليومي السريع (نهاية الحصة)</span>
                    <button type="button" onclick="copyText('tpl-daily')" class="px-3 py-1 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition">
                        نسخ الرسالة 📋
                    </button>
                </div>
                <textarea id="tpl-daily" rows="7" readonly class="w-full text-xs font-mono p-3 rounded-xl bg-white border border-amber-200 text-slate-800">📝 تقييم سريع لحصة اليوم — ASOTS

عزيزي الطالب، رأيك يصنع الفرق! خصّص دقيقة واحدة لتقييم حصة اليوم:

⭐ تقييم المدرّس اليوم:
1️⃣ هل كان شرح المدرّس واضحاً اليوم؟ (١–٥ نجوم)
2️⃣ هل شعرت أنك استفدت من الحصة؟ (نعم / جزئياً / لا)
3️⃣ ما مدى رضاك عن التفاعل والأسئلة داخل الصف؟ (١–٥)
4️⃣ هل كانت المادة المقدمة مناسبة لمستواك؟ (نعم / لا)
5️⃣ ملاحظة سريعة لتحسين الحصة القادمة: ______

💬 رأيك سرّي وسيُسهم مباشرة في تحسين حصتك القادمة.
🤍 شكراً لأنك جزء من عائلة ASOTS

رابط التقييم المباشر: {{ url('/survey?type=daily') }}</textarea>
            </div>

            <!-- قالب 2: الأسبوعي الشامل -->
            <div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-200 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="font-black text-xs text-indigo-900">📊 الصيغة الثانية: التقييم الأسبوعي الشامل (نهاية الأسبوع)</span>
                    <button type="button" onclick="copyText('tpl-weekly')" class="px-3 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition">
                        نسخ الرسالة 📋
                    </button>
                </div>
                <textarea id="tpl-weekly" rows="8" readonly class="w-full text-xs font-mono p-3 rounded-xl bg-white border border-indigo-200 text-slate-800">📈 استبانة التقييم الأسبوعي — ASOTS
بإشراف د. فخرالدين الصوفي

أهلاً بك، نريد أن نعرف كيف كانت تجربتك هذا الأسبوع:

🔹 محور المدرّس: الكفاءة، وضوح الشرح، أسلوب التدريس، الالتزام بالوقت (١–٥)
🔹 محور المنهج: مناسبة المحتوى لمستواك وتوافقه مع الأهداف (١–٥)
🔹 محور التحديات: (التحدث – الاستماع – القراءة – الكتابة – القواعد)
🔹 محور الدعم: تواصل الإدارة الأكاديمية وسهولة نظام التعلم عن بُعد (١–٥)

💡 اقتراحك لهذا الأسبوع وملاحظاتك القيمة.
🌟 شكراً لوقتك — فخورون بك 🤍

رابط التقييم المباشر: {{ url('/survey?type=weekly') }}</textarea>
            </div>

            <!-- قالب 3: النهائي للدبلوم -->
            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="font-black text-xs text-emerald-900">🎓 الصيغة الثالثة: التقييم النهائي للدبلوم والتخرج</span>
                    <button type="button" onclick="copyText('tpl-final')" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition">
                        نسخ الرسالة 📋
                    </button>
                </div>
                <textarea id="tpl-final" rows="8" readonly class="w-full text-xs font-mono p-3 rounded-xl bg-white border border-emerald-200 text-slate-800">🎓 استبانة تقييم تجربة التعلم النهائية — ASOTS
بإشراف مؤسس ومدير المكتب د. فخرالدين الصوفي

وصلت إلى محطة الإنجاز! 🎉 نرجو أن تمنحنا دقائق لتقييم تجربتك الكاملة بكل شفافية ومصداقية، فآراؤك تُعامل بسرية تامة وتُسهم مباشرة في تطوير برامجنا لصالحك أولاً.

📌 تقييم المدرّس | المنهج | تحديات التعلم | الإدارة والدعم | الانطباع العام
🌟 شكراً لوقتك — فخورون بكم 🤍

رابط التقييم النهائي: {{ url('/survey?type=diploma_final') }}</textarea>
            </div>

            <!-- فكرة إضافية: تقييم سريع بالنجوم عبر واتساب -->
            <div class="p-4 rounded-2xl bg-slate-100 border border-slate-200 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="font-black text-xs text-slate-800">💡 فكرة إضافية: استبانة "نجوم سريعة" بضغطة زر</span>
                    <button type="button" onclick="copyText('tpl-stars')" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                        نسخ 📋
                    </button>
                </div>
                <textarea id="tpl-stars" rows="5" readonly class="w-full text-xs font-mono p-3 rounded-xl bg-white border border-slate-200 text-slate-800">⭐⭐⭐⭐⭐
قيّم مدرّس اليوم بضغطة:
أرسل رقماً من ١ إلى ٥ فقط 👇
١ = ضعيف | ٥ = ممتاز
(وسنعرف أن تقييمك سري وكافٍ)</textarea>
            </div>
        </div>
    </div>
</div>

<!-- ================== نافذة منبثقة: تفاصيل الاستبيان المختار ================== -->
<div id="survey-detail-modal" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-xs">
    <div class="bg-white rounded-3xl max-w-xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 space-y-5 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-black text-slate-900" id="detail-modal-title">تفاصيل الاستبيان</h3>
            <button type="button" onclick="closeDetailModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold">✕</button>
        </div>
        <div id="detail-modal-content" class="text-xs text-slate-700 space-y-4">
            <!-- يملأ آلياً بواسطة JavaScript -->
        </div>
    </div>
</div>

<script>
function toggleCustomDates(val) {
    const row = document.getElementById('custom-dates-row');
    if (val === 'custom') {
        row.classList.remove('hidden');
    } else {
        row.classList.add('hidden');
    }
}

function openWhatsAppModal() {
    document.getElementById('whatsapp-modal').classList.remove('hidden');
}

function closeWhatsAppModal() {
    document.getElementById('whatsapp-modal').classList.add('hidden');
}

function copyText(elemId) {
    const textarea = document.getElementById(elemId);
    textarea.select();
    document.execCommand('copy');
    alert('تم نسخ النص بنجاح إلى الحافظة! يمكنك لصقه الآن في واتساب.');
}

// تخزين بيانات الاستبيانات لعرض التفاصيل الكاملة
const surveysData = @json($surveys->items());

function showSurveyDetail(id) {
    const s = surveysData.find(item => item.id === id);
    if (!s) return;

    const modal = document.getElementById('survey-detail-modal');
    const content = document.getElementById('detail-modal-content');
    const title = document.getElementById('detail-modal-title');

    title.textContent = 'تفاصيل ' + (s.type_label || 'الاستبيان') + ' #' + s.id;

    let html = `
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2">
            <div class="flex justify-between">
                <span class="text-slate-400 font-bold">الأستاذ:</span>
                <span class="font-bold text-slate-900">${s.teacher ? s.teacher.name : 'غير محدد'}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400 font-bold">الطالب:</span>
                <span class="font-bold text-slate-900">${s.is_anonymous ? 'هوية سرية 🕵️' : (s.student_name || 'غير محدد')}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400 font-bold">التاريخ:</span>
                <span class="font-mono text-slate-900">${s.survey_date ? s.survey_date.substring(0, 10) : ''}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400 font-bold">النتيجة الإجمالية:</span>
                <span class="font-bold text-amber-500">★ ${s.overall_score || '-'} / 5</span>
            </div>
        </div>

        <div class="space-y-3">
            <h4 class="font-black text-slate-900 border-b pb-1">إجابات المحاور:</h4>
            <div class="grid grid-cols-2 gap-2 text-[11px]">
                ${s.teacher_clarity ? `<div class="p-2 rounded-xl bg-slate-50">وضوح الشرح: <strong>★ ${s.teacher_clarity}</strong></div>` : ''}
                ${s.teacher_competence ? `<div class="p-2 rounded-xl bg-slate-50">الكفاءة العلمية: <strong>★ ${s.teacher_competence}</strong></div>` : ''}
                ${s.teacher_interaction ? `<div class="p-2 rounded-xl bg-slate-50">أسلوب التدريس: <strong>★ ${s.teacher_interaction}</strong></div>` : ''}
                ${s.teacher_punctuality ? `<div class="p-2 rounded-xl bg-slate-50">الالتزام/التحفيز: <strong>★ ${s.teacher_punctuality}</strong></div>` : ''}
                ${s.curriculum_suitability ? `<div class="p-2 rounded-xl bg-slate-50">ملاءمة المنهج: <strong>★ ${s.curriculum_suitability}</strong></div>` : ''}
                ${s.curriculum_alignment ? `<div class="p-2 rounded-xl bg-slate-50">توافق الأهداف: <strong>★ ${s.curriculum_alignment}</strong></div>` : ''}
                ${s.admin_communication ? `<div class="p-2 rounded-xl bg-slate-50">تواصل الإدارة: <strong>★ ${s.admin_communication}</strong></div>` : ''}
                ${s.remote_learning_ease ? `<div class="p-2 rounded-xl bg-slate-50">سهولة النظام: <strong>★ ${s.remote_learning_ease}</strong></div>` : ''}
            </div>
        </div>
    `;

    if (s.daily_benefited) {
        html += `<div class="p-2.5 rounded-xl bg-indigo-50 text-indigo-900 text-xs">هل استفدت من الحصة؟ <strong>${s.daily_benefited === 'yes' ? 'نعم استفدت 😍' : (s.daily_benefited === 'partial' ? 'جزئياً 🙂' : 'لا 😞')}</strong></div>`;
    }

    if (s.best_feature) {
        html += `<div class="p-3 rounded-2xl bg-amber-50 text-amber-900 text-xs font-bold">أبرز ميزة: ${s.best_feature}</div>`;
    }

    if (s.recommend_institute) {
        html += `<div class="p-3 rounded-2xl bg-emerald-50 text-emerald-900 text-xs font-bold">التوصية بالمنصة: ${s.recommend_institute === 'yes' ? 'نعم بكل تأكيد 😍' : (s.recommend_institute === 'maybe' ? 'ربما 🤔' : 'لا 😞')} ${s.recommend_reason ? ' - ' + s.recommend_reason : ''}</div>`;
    }

    if (s.notes_suggestions) {
        html += `
            <div class="space-y-1">
                <span class="font-bold text-slate-700 block">المقترحات والملاحظات:</span>
                <div class="p-3 rounded-2xl bg-slate-100 text-slate-800 text-xs leading-relaxed">${s.notes_suggestions}</div>
            </div>
        `;
    }

    content.innerHTML = html;
    modal.classList.remove('hidden');
}

function closeDetailModal() {
    document.getElementById('survey-detail-modal').classList.add('hidden');
}
</script>
@endsection
