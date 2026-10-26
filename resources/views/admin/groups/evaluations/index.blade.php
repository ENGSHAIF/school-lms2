@extends('layouts.app', ['title' => 'سجل تقييمات وحضور ومشاركات المجموعات الدراسية'])

@section('content')
<div class="space-y-6">

    <!-- ترويسة الصفحة والتحكم العام -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border border-slate-800">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-bold border border-indigo-400/30">
                    نظام المتابعة الأكاديمية والتقييم الدوري الشامل 📋
                </span>
                <span class="text-xs text-amber-400 font-bold">درجات الدبلوم والشهادة</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">تقييمات وحضور وتفاعل طلاب المجموعات</h1>
            <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                متابعة يومية، أسبوعية، شهرية، وتقييم نهائي للدورة: تتبع نسب الحضور والغياب، التفاعل الصوتي والكتابي، حل الواجبات، ومكافآت النجوم التراكمية المعتمدة لدرجات الدبلوم.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
            @if($selectedGroupId)
                <a href="{{ route('admin.groups.evaluations.create', $selectedGroupId) }}" class="flex-1 md:flex-none px-5 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 transition flex items-center justify-center gap-2">
                    <span>➕</span>
                    <span>رصد تقييم وحضور اليوم 📋</span>
                </a>
            @endif

            <a href="{{ route('admin.groups.evaluations.print', request()->all()) }}" target="_blank" class="flex-1 md:flex-none px-4 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition flex items-center justify-center gap-2">
                <span>🖨️</span>
                <span>طباعة التقرير الرسمي</span>
            </a>
        </div>
    </div>

    <!-- شريط الإحصائيات العامة للمجموعات -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block">إجمالي الحصص المرصودة</span>
            <div class="text-2xl font-black text-slate-900 font-mono flex items-center gap-1.5">
                <span>📚</span>
                <span>{{ $totalSessionsCount }}</span>
                <span class="text-xs font-normal text-slate-400">حصة</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block">متوسط نسبة الحضور العام</span>
            <div class="text-2xl font-black text-emerald-600 font-mono flex items-center gap-1.5">
                <span>🟢</span>
                <span>{{ round($avgAttendanceRate, 1) }}%</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block">مجموع النجوم والمكافآت الممنوحة</span>
            <div class="text-2xl font-black text-amber-500 font-mono flex items-center gap-1.5">
                <span>⭐</span>
                <span>{{ number_format($totalStarsAwarded) }}</span>
                <span class="text-xs font-normal text-slate-400">نجمة</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block">المجموعات المتاحة للمتابعة</span>
            <div class="text-2xl font-black text-indigo-600 font-mono flex items-center gap-1.5">
                <span>👥</span>
                <span>{{ $availableGroups->count() }}</span>
                <span class="text-xs font-normal text-slate-400">مجموعة</span>
            </div>
        </div>
    </div>

    <!-- شريط الفلترة واختيار الفترة والمجموعة -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-2xs space-y-4">
        
        <!-- أزرار اختيار نوع الفترة الزمنية -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500">نوع التقرير الدوري:</span>
                <div class="flex items-center bg-slate-100 p-1 rounded-2xl text-xs font-bold">
                    <a href="{{ route('admin.groups.evaluations.index', array_merge(request()->all(), ['period' => 'daily'])) }}" 
                       class="px-3.5 py-1.5 rounded-xl transition {{ $period === 'daily' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        📅 التقييم اليومي
                    </a>
                    <a href="{{ route('admin.groups.evaluations.index', array_merge(request()->all(), ['period' => 'weekly'])) }}" 
                       class="px-3.5 py-1.5 rounded-xl transition {{ $period === 'weekly' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        📆 التقييم الأسبوعي
                    </a>
                    <a href="{{ route('admin.groups.evaluations.index', array_merge(request()->all(), ['period' => 'monthly'])) }}" 
                       class="px-3.5 py-1.5 rounded-xl transition {{ $period === 'monthly' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        🗓️ التقييم الشهري
                    </a>
                    <a href="{{ route('admin.groups.evaluations.index', array_merge(request()->all(), ['period' => 'final'])) }}" 
                       class="px-3.5 py-1.5 rounded-xl transition {{ $period === 'final' ? 'bg-amber-500 text-slate-950 font-black shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        🎓 تقييم نهاية الدورة والدبلوم
                    </a>
                </div>
            </div>

            <span class="text-[11px] text-slate-400">
                الفترة النشطة: <strong>{{ match($period) { 'weekly' => 'ملخص أسبوعي', 'monthly' => 'ملخص شهري', 'final' => 'الكشف النهائي التراكمي لدرجات الدبلوم', default => 'السجل اليومي المباشر' } }}</strong>
            </span>
        </div>

        <!-- فلاتر المجموعة والتواريخ -->
        <form method="GET" action="{{ route('admin.groups.evaluations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <input type="hidden" name="period" value="{{ $period }}">

            <div>
                <label class="block font-bold text-slate-600 mb-1">المجموعة الدراسية:</label>
                <select name="group_id" onchange="this.form.submit()" class="w-full border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">-- كل المجموعات الدراسية --</option>
                    @foreach($availableGroups as $grp)
                        <option value="{{ $grp->id }}" {{ $selectedGroupId == $grp->id ? 'selected' : '' }}>
                            {{ $grp->name }} ({{ $grp->course?->name }} - {{ $grp->level?->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">من تاريخ:</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full border border-slate-300 rounded-xl p-2 text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">إلى تاريخ:</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full border border-slate-300 rounded-xl p-2 text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition shadow-sm">
                    تطبيق الفلترة 🔍
                </button>
                <a href="{{ route('admin.groups.evaluations.index', ['period' => $period]) }}" class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    <!-- جدول ملخص المجموعات السريع (متابعة مباشرة لنشاط كل مجموعة) -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-2xs space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span>👥</span>
                    <span>ملخص أداء ونشاط المجموعات الدراسية المباشر</span>
                </h3>
                <span class="text-xs text-slate-400">معرفة نسبة الحضور، عدد الجلسات المنعقدة، ورصد تقييم اليوم لكل مجموعة بنقرة واحدة</span>
            </div>
            <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full font-mono">
                {{ count($groupSummaries) }} مجموعات
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 font-bold">
                        <th class="py-3 px-3">المجموعة الدراسية</th>
                        <th class="py-3 px-3">الدورة والمستوى</th>
                        <th class="py-3 px-3">المعلم المسؤول</th>
                        <th class="py-3 px-3 text-center">الطلاب المسكنين</th>
                        <th class="py-3 px-3 text-center">الحصص المرصودة</th>
                        <th class="py-3 px-3 text-center">متوسط الحضور</th>
                        <th class="py-3 px-3 text-center">مجموع النجوم</th>
                        <th class="py-3 px-3 text-center">آخر جلسة مرصودة</th>
                        <th class="py-3 px-3 text-center">الإجراء السريع</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($groupSummaries as $sum)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-3 font-bold text-slate-900">
                                <a href="{{ route('admin.groups.evaluations.index', ['group_id' => $sum->group->id, 'period' => $period]) }}" class="hover:text-indigo-600 hover:underline">
                                    {{ $sum->group->name }}
                                </a>
                                @if($sum->group->term || $sum->group->batch)
                                    <span class="text-[10px] text-slate-400 block font-normal font-mono">
                                        {{ $sum->group->term }} {{ $sum->group->batch ? '• ' . $sum->group->batch : '' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-600">
                                <span class="font-bold text-indigo-700">{{ $sum->group->course?->name }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $sum->group->level?->name }}</span>
                            </td>
                            <td class="py-3 px-3 text-slate-700 font-bold">
                                {{ $sum->group->teacher?->name ?? 'إدارة الأكاديمية' }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-bold text-slate-800">
                                {{ $sum->active_students_count }} طالب
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-bold text-slate-800">
                                {{ $sum->sessions_count }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-mono font-bold {{ $sum->avg_attendance >= 85 ? 'bg-emerald-100 text-emerald-800' : ($sum->avg_attendance >= 70 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $sum->avg_attendance }}%
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-black text-amber-500">
                                ⭐ {{ $sum->total_stars }}
                            </td>
                            <td class="py-3 px-3 text-center text-[11px] text-slate-500 font-mono">
                                {{ $sum->last_session ? $sum->last_session->session_date->format('Y-m-d') : 'لا يوجد' }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.groups.evaluations.create', $sum->group->id) }}" title="رصد تقييم اليوم" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg font-bold text-[11px] transition shadow-2xs flex items-center gap-1">
                                        <span>➕</span>
                                        <span>رصد تقييم</span>
                                    </a>
                                    <a href="{{ route('admin.groups.evaluations.index', ['group_id' => $sum->group->id, 'period' => 'final']) }}" title="الكشف النهائي للدبلوم" class="px-2 py-1.5 bg-slate-800 hover:bg-slate-700 text-amber-400 rounded-lg font-bold text-[11px] transition">
                                        🎓 الدبلوم
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-6 text-center text-slate-400">لا توجد مجموعات متاحة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- الطلاب الأكثر تفاعلاً ومشاركة (Top Active Students Leaderboard) -->
    @if($topActiveStudents->isNotEmpty())
        <div class="bg-gradient-to-r from-amber-500/10 via-purple-500/10 to-indigo-500/10 rounded-3xl border border-amber-500/20 p-6 space-y-4 shadow-sm">
            <div class="flex justify-between items-center border-b border-amber-200/50 pb-3">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span>🏆</span>
                    <span>لوحة شرف الطلاب الأكثر نشاطاً وتفاعلاً ومكافآت (Top Active Students)</span>
                </h3>
                <span class="text-xs font-bold text-amber-800 bg-amber-100 px-3 py-1 rounded-full font-mono">
                    أعلى 10 طلاب مشاركة
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                @foreach($topActiveStudents as $idx => $top)
                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs space-y-2 relative overflow-hidden">
                        <div class="flex justify-between items-start">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black {{ $idx === 0 ? 'bg-amber-400 text-slate-950 font-mono shadow-sm' : ($idx === 1 ? 'bg-slate-300 text-slate-950 font-mono' : ($idx === 2 ? 'bg-amber-600 text-white font-mono' : 'bg-slate-100 text-slate-600 font-mono')) }}">
                                {{ $idx + 1 }}
                            </span>
                            <span class="text-xs font-black text-amber-500 font-mono">
                                ⭐ {{ $top->total_stars }}
                            </span>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs text-slate-900 truncate">{{ $top->student?->name }}</h4>
                            <span class="text-[10px] text-slate-400 block truncate">{{ $top->group?->name }}</span>
                        </div>
                        <div class="pt-2 border-t border-slate-100 flex justify-between text-[10px] text-slate-500">
                            <span>🎙️ {{ $top->total_voice_participations }} صوتي</span>
                            <span>📝 {{ $top->total_homework_submissions }} واجبات</span>
                            <span>💬 {{ $top->total_chat_participations }} شات</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- القسم الديناميكي: إما جدول نهاية الدورة للدبلوم أو جدول الحصص اليومية -->
    @if($period === 'final')
        <!-- كشف التقييم النهائي للدبلوم ودرجات الاعتماد (Diploma Final Evaluation Roster) -->
        <div class="bg-white rounded-3xl border border-amber-300/80 p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span>🎓</span>
                        <span>كشف تقييم نهاية الدورة والترم النهائي لاعتماد درجات الدبلوم</span>
                    </h3>
                    <span class="text-xs text-slate-500">
                        حساب تلقائي شامل: الحضور (20) + المشاركة والتفاعل الصوتي (20) + الواجبات (20) + الكويزات (20) + تقييم الأستاذ والنجوم (20) = المجموع النهائي من 100%
                    </span>
                </div>

                @if($selectedGroupId)
                    <a href="{{ route('admin.groups.evaluations.print', ['group_id' => $selectedGroupId, 'period' => 'final']) }}" target="_blank" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-2">
                        <span>🖨️</span>
                        <span>طباعة كشف الدبلوم الرسمي</span>
                    </a>
                @endif
            </div>

            @if(!$selectedGroupId)
                <div class="p-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 space-y-2">
                    <span class="text-3xl block">👥</span>
                    <p class="font-bold text-slate-700">يرجى اختيار مجموعة محددة من القائمة أعلاه لعرض كشف درجات الدبلوم النهائي لها.</p>
                </div>
            @elseif($finalDiplomaEvaluations->isEmpty())
                <div class="p-8 text-center text-slate-400 bg-slate-50 rounded-2xl">
                    لا يوجد طلاب مسكنين في هذه المجموعة حتى الآن.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead>
                            <tr class="bg-amber-500/10 text-amber-950 border-b border-amber-200 font-black">
                                <th class="py-3 px-3">#</th>
                                <th class="py-3 px-3">اسم الطالب</th>
                                <th class="py-3 px-3 text-center">كود الطالب</th>
                                <th class="py-3 px-3 text-center">نسبة الحضور</th>
                                <th class="py-3 px-3 text-center">المواظبة (20)</th>
                                <th class="py-3 px-3 text-center">المشاركة الصوتية (20)</th>
                                <th class="py-3 px-3 text-center">الواجبات (20)</th>
                                <th class="py-3 px-3 text-center">الكويزات (20)</th>
                                <th class="py-3 px-3 text-center">النجوم والسلوك (20)</th>
                                <th class="py-3 px-3 text-center bg-amber-100">المجموع النهائي (100)</th>
                                <th class="py-3 px-3 text-center">التقدير الأكاديمي</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($finalDiplomaEvaluations as $idx => $res)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3 px-3 font-mono font-bold text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="py-3 px-3 font-bold text-slate-900">
                                        {{ $res->student->name }}
                                        <span class="text-[10px] text-slate-400 block font-normal">{{ $res->student->email }}</span>
                                    </td>
                                    <td class="py-3 px-3 text-center font-mono text-slate-600">{{ $res->student->student_code ?? $res->student->user_code }}</td>
                                    <td class="py-3 px-3 text-center font-mono font-bold text-emerald-700">{{ $res->attendance_percentage }}%</td>
                                    <td class="py-3 px-3 text-center font-mono font-bold">{{ $res->attendance_score }}</td>
                                    <td class="py-3 px-3 text-center font-mono font-bold text-indigo-700">{{ $res->participation_score }}</td>
                                    <td class="py-3 px-3 text-center font-mono font-bold text-purple-700">{{ $res->homework_score }}</td>
                                    <td class="py-3 px-3 text-center font-mono font-bold text-rose-700">{{ $res->quiz_score }}</td>
                                    <td class="py-3 px-3 text-center font-mono font-bold text-amber-600">⭐ {{ $res->stars_bonus }}</td>
                                    <td class="py-3 px-3 text-center font-mono font-black text-sm bg-amber-50 text-amber-950">
                                        {{ $res->final_grade }}%
                                    </td>
                                    <td class="py-3 px-3 text-center font-bold">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] {{ $res->final_grade >= 85 ? 'bg-emerald-100 text-emerald-800' : ($res->final_grade >= 70 ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                            {{ $res->grade_letter }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @else
        <!-- سجل الحصص والجلسات اليومية والدورية (Sessions Log) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-2xs space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span>📋</span>
                        <span>سجل الحصص اليومية وتقييمات الطلاب التفصيلية</span>
                    </h3>
                    <span class="text-xs text-slate-400">تتبع ما تم تدريسه، ونسب حضور الطلاب، وتفاصيل المشاركات لكل جلسة</span>
                </div>
                <span class="text-xs font-bold text-slate-600 font-mono">{{ $sessions->total() }} سجلات</span>
            </div>

            @if($sessions->isEmpty())
                <div class="text-center py-12 text-slate-400 space-y-2 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <span class="text-4xl block">📝</span>
                    <p class="font-bold text-slate-700">لم يتم رصد أي حصص في هذه الفترة أو للمجموعة المحددة.</p>
                    <p class="text-xs text-slate-400">يمكنك الضغط على زر "رصد تقييم وحضور اليوم" في الأعلى لإضافة تقرير جلسة جديدة.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($sessions as $session)
                        <div class="border border-slate-200 rounded-2xl p-5 hover:border-indigo-300 transition space-y-4 bg-slate-50/40">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 border-b border-slate-200 pb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex flex-col items-center justify-center font-bold font-mono">
                                        <span class="text-[10px] uppercase opacity-75">حصة</span>
                                        <span class="text-base font-black">#{{ $session->session_number }}</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-black text-sm text-slate-900">{{ $session->lesson_topic }}</h4>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                {{ $session->group?->name }}
                                            </span>
                                        </div>
                                        <span class="text-[11px] text-slate-500 block mt-0.5">
                                            المعلم: <strong class="text-slate-700">{{ $session->teacher?->name }}</strong> • 
                                            التاريخ: <strong class="text-slate-700 font-mono">{{ $session->session_date->format('Y-m-d') }}</strong>
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 w-full md:w-auto justify-between md:justify-end">
                                    <!-- إحصائيات الحضور والغياب -->
                                    <div class="flex items-center gap-1.5 text-xs font-mono">
                                        <span class="px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 font-bold">حاضر: {{ $session->present_count }}</span>
                                        <span class="px-2 py-0.5 rounded-lg bg-rose-100 text-rose-800 font-bold">غائب: {{ $session->absent_count }}</span>
                                        @if($session->late_count > 0)
                                            <span class="px-2 py-0.5 rounded-lg bg-amber-100 text-amber-800 font-bold">متأخر: {{ $session->late_count }}</span>
                                        @endif
                                        <span class="px-2.5 py-0.5 rounded-xl bg-slate-900 text-emerald-400 font-black">
                                            {{ $session->attendance_rate }}%
                                        </span>
                                    </div>

                                    <!-- أزرار الإجراءات -->
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.groups.evaluations.show', $session->id) }}" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-xl text-xs font-bold transition">
                                            استعراض 👁️
                                        </a>
                                        <a href="{{ route('admin.groups.evaluations.print', ['session_id' => $session->id]) }}" target="_blank" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition">
                                            طباعة 🖨️
                                        </a>
                                        <form method="POST" action="{{ route('admin.groups.evaluations.destroy', $session->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف تقرير هذه الحصة؟ سيتم إعادة خصم النجوم التي تم منحها.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="حذف السجل">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- ملخص الدرس والواجب المنزلي -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                @if($session->lesson_summary)
                                    <div class="bg-white p-3 rounded-xl border border-slate-200 space-y-1">
                                        <strong class="text-indigo-950 font-bold block">📖 ملخص محتوى الدرس:</strong>
                                        <p class="text-slate-600 leading-relaxed">{{ $session->lesson_summary }}</p>
                                    </div>
                                @endif

                                @if($session->homework_assigned)
                                    <div class="bg-amber-50/50 p-3 rounded-xl border border-amber-200 space-y-1">
                                        <strong class="text-amber-950 font-bold block">📝 الواجب المطلوب:</strong>
                                        <p class="text-amber-900 leading-relaxed">{{ $session->homework_assigned }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- تفاصيل تفاعل ونجوم طلاب الحصة -->
                            <div class="bg-white rounded-xl p-3 border border-slate-200 space-y-2">
                                <span class="text-[11px] font-bold text-slate-500 block">تفاصيل تقييم ومشاركات الطلاب في هذه الحصة:</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach($session->evaluations as $ev)
                                        <div class="p-2.5 rounded-xl border border-slate-100 flex items-center justify-between text-xs {{ $ev->attendance_status === 'absent' ? 'bg-rose-50/30' : 'bg-slate-50/50' }}">
                                            <div class="flex items-center gap-2 truncate">
                                                <span class="text-xs">{{ $ev->attendance_status === 'present' ? '🟢' : ($ev->attendance_status === 'absent' ? '🔴' : '🟡') }}</span>
                                                <div class="truncate">
                                                    <span class="font-bold text-slate-900 block truncate">{{ $ev->student?->name }}</span>
                                                    <span class="text-[10px] text-slate-400">
                                                        {{ $ev->spoke_voice ? '🎙️ صوت' : '' }}
                                                        {{ $ev->wrote_chat ? '💬 شات' : '' }}
                                                        {{ $ev->submitted_task ? '📝 واجب' : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-left shrink-0">
                                                @if($ev->stars_awarded > 0)
                                                    <span class="font-mono font-black text-amber-500 text-xs block">⭐ +{{ $ev->stars_awarded }}</span>
                                                @endif
                                                <span class="text-[10px] font-bold px-1.5 py-0.2 rounded {{ $ev->attendance_badge_class }}">
                                                    {{ $ev->attendance_label }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    @endforeach

                    <div class="pt-2">
                        {{ $sessions->links() }}
                    </div>
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
