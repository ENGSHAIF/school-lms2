@extends('layouts.app')

@section('title', 'إدارة الاختبارات والشهائد الأكاديمية - ASOTS')

@section('content')
<div class="py-6 px-3 sm:px-6 lg:px-8 max-w-[1700px] mx-auto space-y-6">

    <!-- شريط التنبيهات والرسائل -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-2xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <span class="text-2xl">✅</span>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-black">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-300 text-rose-900 rounded-2xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <span class="text-2xl">⚠️</span>
                <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-black">✕</button>
        </div>
    @endif

    <!-- الهيدر الأكاديمي الرئيسي -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 relative overflow-hidden">
        <div class="absolute -top-12 -left-12 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -right-12 w-64 h-64 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-black border border-amber-400/30">
                    <span>🎓</span>
                    <span>النظام الشامل لإدارة الاختبارات وسلالم الدرجات وإصدار الشهائد الملكية</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-wide">
                    إدارة الاختبارات والشهائد الأكاديمية
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-medium max-w-2xl">
                    إسناد الاختبارات والكويزات للدورات والمجموعات، ضبط أوزان الدرجات 100%، رصد ومراجعة تقييمات الطلاب، طباعة الكشوفات التفصيلية، وإصدار شهائد التخرج المعتمدة دفعة واحدة.
                </p>
            </div>

            <!-- إحصائيات سريعة للرأس -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-3 text-center backdrop-blur">
                    <span class="text-xs text-slate-400 block font-bold">الاختبارات المسندة</span>
                    <strong class="text-xl font-black text-amber-400 font-mono">{{ $exams->total() }}</strong>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-3 text-center backdrop-blur">
                    <span class="text-xs text-slate-400 block font-bold">المجموعات الدراسية</span>
                    <strong class="text-xl font-black text-indigo-400 font-mono">{{ $groups->count() }}</strong>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-3 text-center backdrop-blur">
                    <span class="text-xs text-slate-400 block font-bold">سلالم الدرجات</span>
                    <strong class="text-xl font-black text-emerald-400 font-mono">{{ $gradeScales->count() }}</strong>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-3 text-center backdrop-blur">
                    <span class="text-xs text-slate-400 block font-bold">الشهائد الصادرة</span>
                    <strong class="text-xl font-black text-rose-400 font-mono">{{ $certificates->total() }}</strong>
                </div>
            </div>
        </div>

        <!-- شريط التصفية السريع للمجموعات والدورات -->
        <div class="mt-6 pt-6 border-t border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
            <form action="{{ route('admin.exams_management.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-slate-300">المجموعة:</label>
                    <select name="group_id" onchange="this.form.submit()" class="text-xs font-bold px-3 py-2 bg-slate-800 text-white border border-slate-700 rounded-xl focus:ring-2 focus:ring-amber-400">
                        <option value="">-- اختر المجموعة الدراسية --</option>
                        @foreach($groups as $grp)
                            <option value="{{ $grp->id }}" {{ (string)$groupId === (string)$grp->id ? 'selected' : '' }}>
                                {{ $grp->displayName }} ({{ $grp->course?->displayName ?: 'عام' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-slate-300">الدورة:</label>
                    <select name="course_id" onchange="this.form.submit()" class="text-xs font-bold px-3 py-2 bg-slate-800 text-white border border-slate-700 rounded-xl focus:ring-2 focus:ring-amber-400">
                        <option value="">-- كافة الدورات --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ (string)$courseId === (string)$c->id ? 'selected' : '' }}>
                                {{ $c->displayName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($groupId || $courseId || $levelId)
                    <a href="{{ route('admin.exams_management.index', ['tab' => $tab]) }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold rounded-xl transition">
                        إلغاء التصفية ✕
                    </a>
                @endif
            </form>

            <!-- روابط سريعة -->
            <div class="flex items-center gap-2">
                <a href="{{ route('help.index') }}" title="دليل وشرح إدارة الاختبارات وسلالم الدرجات والشهائد" class="px-3 py-2 bg-amber-400 hover:bg-amber-500 text-slate-950 font-black text-xs rounded-xl transition flex items-center gap-1.5 shadow-sm">
                    <span>📖</span>
                    <span>دليل الشاشة</span>
                </a>
                <a href="{{ route('admin.exams.index') }}" class="px-3 py-2 bg-rose-600/30 hover:bg-rose-600/50 text-rose-200 border border-rose-500/30 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                    <span>🎯</span>
                    <span>بنك الأسئلة والكويزات</span>
                </a>
                <a href="{{ route('admin.groups.evaluations.index') }}" class="px-3 py-2 bg-amber-500/20 hover:bg-amber-500/40 text-amber-300 border border-amber-400/30 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                    <span>📋</span>
                    <span>التقييم اليومي والحضور</span>
                </a>
            </div>
        </div>
    </div>

    <!-- شريط التبويبات الرئيسي (4 تبويبات شاملة) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-2 flex items-center gap-2 overflow-x-auto">
        <a href="{{ route('admin.exams_management.index', ['tab' => 'exams', 'group_id' => $groupId, 'course_id' => $courseId]) }}"
           class="px-5 py-3 rounded-xl text-xs sm:text-sm font-black transition flex items-center gap-2 shrink-0 {{ $tab === 'exams' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>🎯</span>
            <span>1. إدارة وإسناد الاختبارات والكويزات</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-mono {{ $tab === 'exams' ? 'bg-slate-950 text-amber-400' : 'bg-slate-100 text-slate-600' }}">{{ $exams->total() }}</span>
        </a>

        <a href="{{ route('admin.exams_management.index', ['tab' => 'scales', 'group_id' => $groupId, 'course_id' => $courseId]) }}"
           class="px-5 py-3 rounded-xl text-xs sm:text-sm font-black transition flex items-center gap-2 shrink-0 {{ $tab === 'scales' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>⚖️</span>
            <span>2. أوزان وسلالم درجات الدورة (100%)</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-mono {{ $tab === 'scales' ? 'bg-indigo-800 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $gradeScales->count() }}</span>
        </a>

        <a href="{{ route('admin.exams_management.index', ['tab' => 'grading', 'group_id' => $groupId, 'course_id' => $courseId]) }}"
           class="px-5 py-3 rounded-xl text-xs sm:text-sm font-black transition flex items-center gap-2 shrink-0 {{ $tab === 'grading' ? 'bg-purple-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>📝</span>
            <span>3. رصد ومراجعة درجات الطلاب والكشوفات</span>
            @if($selectedGroup)
                <span class="px-2 py-0.5 rounded-full text-[11px] font-mono {{ $tab === 'grading' ? 'bg-purple-800 text-white' : 'bg-purple-100 text-purple-700' }}">{{ $selectedGroup->students->count() }} طالب</span>
            @endif
        </a>

        <a href="{{ route('admin.exams_management.index', ['tab' => 'certificates', 'group_id' => $groupId, 'course_id' => $courseId]) }}"
           class="px-5 py-3 rounded-xl text-xs sm:text-sm font-black transition flex items-center gap-2 shrink-0 {{ $tab === 'certificates' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
            <span>📜</span>
            <span>4. إصدار وطباعة الشهائد الأكاديمية</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-mono {{ $tab === 'certificates' ? 'bg-emerald-800 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $certificates->total() }}</span>
        </a>
    </div>

    <!-- ========================================== -->
    <!-- التبويب 1: إدارة وإسناد الاختبارات والكويزات -->
    <!-- ========================================== -->
    @if($tab === 'exams')
        <div class="space-y-6">
            <!-- شريط العمليات العلوي للتبويب -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span>🎯</span>
                        <span>قائمة الاختبارات والكويزات المخصصة والمسندة</span>
                    </h2>
                    <p class="text-xs text-slate-500 font-medium">
                        تخصيص الاختبارات لمستوى معين، دورة معينة، مجموعة دراسية، أو طلاب محددين مع تحديد درجة الكويز ووزنه.
                    </p>
                </div>

                <button onclick="document.getElementById('createExamModal').classList.remove('hidden')"
                        class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs sm:text-sm rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                    <span>➕</span>
                    <span>إنشاء وتخصيص اختبار / كويز جديد</span>
                </button>
            </div>

            <!-- جدول الاختبارات -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-black">
                            <tr>
                                <th class="p-4">#</th>
                                <th class="p-4">عنوان الاختبار / الكويز</th>
                                <th class="p-4">فترة الاختبار</th>
                                <th class="p-4">نطاق الإسناد والتخصيص</th>
                                <th class="p-4 text-center">الدرجة الكلية</th>
                                <th class="p-4 text-center">درجة النجاح</th>
                                <th class="p-4 text-center">المدة (دقيقة)</th>
                                <th class="p-4 text-center">الأسئلة والمحاولات</th>
                                <th class="p-4 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                            @forelse($exams as $exam)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 text-slate-400 font-mono">{{ $exam->id }}</td>
                                    <td class="p-4">
                                        <div class="font-black text-slate-900 text-sm">{{ $exam->title }}</div>
                                        <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                                            @if($exam->course)
                                                <span class="text-indigo-600 font-bold">دورة: {{ $exam->course->displayName }}</span>
                                            @endif
                                            @if($exam->level)
                                                <span>• مستوى: {{ $exam->level->name }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $exam->quiz_period_badge }}">
                                            {{ $exam->quiz_period_label }}
                                        </span>
                                        @if($exam->weight_category)
                                            <div class="text-[11px] text-slate-500 mt-1 font-bold">
                                                الوزن: {{ match($exam->weight_category) {
                                                    'midterm' => 'اختبار نصفي',
                                                    'final' => 'اختبار نهائي',
                                                    'activities' => 'أنشطة وتكاليف',
                                                    'participation' => 'مشاركة وتفاعل',
                                                    default => 'كويز تدريبي',
                                                } }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        @if($exam->target_type === 'group' && $exam->studyGroup)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 border border-purple-200 text-xs font-bold">
                                                <span>👥</span>
                                                <span>مجموعة: {{ $exam->studyGroup->displayName }}</span>
                                            </span>
                                        @elseif($exam->target_type === 'students' && !empty($exam->assigned_student_ids))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold">
                                                <span>👤</span>
                                                <span>طلاب مخصصون ({{ count($exam->assigned_student_ids) }})</span>
                                            </span>
                                        @elseif($exam->target_type === 'course' && $exam->course)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                                                <span>📚</span>
                                                <span>دورة: {{ $exam->course->displayName }}</span>
                                            </span>
                                        @elseif($exam->target_type === 'level' && $exam->level)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold">
                                                <span>🏷️</span>
                                                <span>مستوى: {{ $exam->level->name }}</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold">
                                                <span>🌐</span>
                                                <span>عام لكافة الطلاب</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="font-mono font-black text-sm text-indigo-700 bg-indigo-50 px-2 py-1 rounded-lg border border-indigo-200">
                                            {{ $exam->total_points }} د
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="font-mono font-bold text-xs text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-200">
                                            {{ $exam->passing_score }} د
                                        </span>
                                    </td>
                                    <td class="p-4 text-center font-mono font-bold text-slate-600">
                                        {{ $exam->duration_minutes }} دقيقة
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('admin.exams.edit', $exam->id) }}" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition" title="إدارة الأسئلة">
                                                ❓ {{ $exam->questions()->count() }} سؤال
                                            </a>
                                            <a href="{{ route('admin.exams.attempts', $exam->id) }}" class="px-2 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-[11px] transition" title="عرض المحاولات">
                                                📝 {{ $exam->attempts()->count() }} محاولة
                                            </a>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <!-- زر تعديل الإسناد والدرجة -->
                                            <button onclick="openAssignModal({{ $exam->id }}, '{{ addslashes($exam->title) }}', '{{ $exam->target_type }}', '{{ $exam->course_id }}', '{{ $exam->study_group_id }}', '{{ $exam->level_id }}', '{{ $exam->quiz_period }}', '{{ $exam->weight_category }}', {{ $exam->total_points }}, {{ $exam->passing_score }})"
                                                    class="p-2 rounded-lg bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-800 transition"
                                                    title="تعديل الإسناد والدرجات">
                                                ✏️
                                            </button>
                                            <!-- زر إدارة بنك الأسئلة -->
                                            <a href="{{ route('admin.exams.edit', $exam->id) }}" class="p-2 rounded-lg bg-slate-100 hover:bg-indigo-100 text-slate-700 hover:text-indigo-800 transition" title="محرر بنك الأسئلة">
                                                ⚙️
                                            </a>
                                            <!-- رصد مباشر لطلاب هذا الاختبار -->
                                            @if($exam->study_group_id)
                                                <a href="{{ route('admin.exams_management.index', ['tab' => 'grading', 'group_id' => $exam->study_group_id]) }}" class="p-2 rounded-lg bg-slate-100 hover:bg-purple-100 text-slate-700 hover:text-purple-800 transition" title="رصد درجات طلاب المجموعة">
                                                    📝
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-12 text-center text-slate-400">
                                        <div class="text-4xl mb-2">🎯</div>
                                        <div class="text-sm font-bold text-slate-600">لا يوجد اختبارات مخصصة مسجلة حالياً</div>
                                        <p class="text-xs text-slate-400 mt-1">اضغط على زر «إنشاء وتخصيص اختبار / كويز جديد» بالأعلى لبدء الإسناد.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($exams->hasPages())
                    <div class="p-4 border-t border-slate-200">
                        {{ $exams->links() }}
                    </div>
                @endif
            </div>
        </div>

    <!-- ========================================== -->
    <!-- التبويب 2: أوزان وسلالم درجات الدورة (100%) -->
    <!-- ========================================== -->
    @elseif($tab === 'scales')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- بطاقة إدخال / تعديل سلم الدرجات -->
            <div class="lg:col-span-1 bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-5">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span>⚖️</span>
                        <span>ضبط وتوزيع أوزان درجات الدورة / الدبلوم</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        تحديد الحد الأقصى لكل مكون ليكون إجمالي الدورة أو الدبلوم 100 درجة مع نسبة النجاح المعتمدة.
                    </p>
                </div>

                <form action="{{ route('admin.exams_management.scales.save') }}" method="POST" id="scaleForm" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">مسمى السلم الأكاديمي *</label>
                        <input type="text" name="name" value="{{ $selectedScale ? $selectedScale->name : 'سلم تقييم الدورة القياسي (100 درجة)' }}" required
                               class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">تخصيص لدورة محددة</label>
                            <select name="course_id" class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- عام لكل الدورات --</option>
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}" {{ $selectedScale && $selectedScale->course_id == $c->id ? 'selected' : '' }}>
                                        {{ $c->displayName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">أو تخصيص لمجموعة</label>
                            <select name="study_group_id" class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- بدون تخصيص مجموعة --</option>
                                @foreach($groups as $grp)
                                    <option value="{{ $grp->id }}" {{ $selectedScale && $selectedScale->study_group_id == $grp->id ? 'selected' : '' }}>
                                        {{ $grp->displayName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- تفصيل الدرجات الستة المكونة للمجموع -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3">
                        <span class="text-xs font-black text-slate-800 block">تفصيل مكونات التقييم الستة:</span>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">1. الحضور والانضباط (نظري)</label>
                                <input type="number" step="0.5" min="0" max="100" name="attendance_max" id="sc_att"
                                       value="{{ $selectedScale ? $selectedScale->attendance_max : 10 }}" oninput="calcScaleTotal()"
                                       class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">2. المشاركة والتفاعل (شفهي)</label>
                                <input type="number" step="0.5" min="0" max="100" name="participation_max" id="sc_par"
                                       value="{{ $selectedScale ? $selectedScale->participation_max : 10 }}" oninput="calcScaleTotal()"
                                       class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">3. الأنشطة والواجبات</label>
                                <input type="number" step="0.5" min="0" max="100" name="activities_max" id="sc_act"
                                       value="{{ $selectedScale ? $selectedScale->activities_max : 15 }}" oninput="calcScaleTotal()"
                                       class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">4. اختبار نصفي (Midterm)</label>
                                <input type="number" step="0.5" min="0" max="100" name="midterm_max" id="sc_mid"
                                       value="{{ $selectedScale ? $selectedScale->midterm_max : 20 }}" oninput="calcScaleTotal()"
                                       class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">5. الشفهي / العملي (Speaking)</label>
                                <input type="number" step="0.5" min="0" max="100" name="practical_max" id="sc_pra"
                                       value="{{ $selectedScale ? $selectedScale->practical_max : 15 }}" oninput="calcScaleTotal()"
                                       class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">6. الاختبار النهائي (Final)</label>
                                <input type="number" step="0.5" min="0" max="100" name="final_exam_max" id="sc_fin"
                                       value="{{ $selectedScale ? $selectedScale->final_exam_max : 30 }}" oninput="calcScaleTotal()"
                                       class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-300 rounded-lg">
                            </div>
                        </div>

                        <!-- المجموع والنجاح -->
                        <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-slate-500 font-bold block">المجموع الكلي:</span>
                                <span id="scaleTotalDisplay" class="text-lg font-black text-indigo-700 font-mono">100</span>
                                <span class="text-xs text-slate-400">درجة</span>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">نسبة النجاح %</label>
                                <input type="number" step="1" min="40" max="90" name="passing_percentage"
                                       value="{{ $selectedScale ? $selectedScale->passing_percentage : 60 }}"
                                       class="w-20 px-2 py-1 text-xs font-mono font-bold border border-slate-300 rounded-lg text-center">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs sm:text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
                        <span>💾</span>
                        <span>حفظ واعتماد سلم الدرجات</span>
                    </button>
                </form>
            </div>

            <!-- قائمة السلالم المعتمدة في النظام -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <span>📜</span>
                        <span>السلالم المعتمدة لتوزيع الدرجات عبر المنصة</span>
                    </h3>
                    <p class="text-xs text-slate-500">
                        السلالم التي يتم احتساب نسبة النجاح وكشوفات درجات الطلاب والشهائد بناءً عليها.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($gradeScales as $scale)
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 space-y-3 relative overflow-hidden">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-black text-slate-900 text-sm">{{ $scale->name }}</h4>
                                    <span class="text-xs text-indigo-600 font-bold">
                                        @if($scale->studyGroup)
                                            مجموعة: {{ $scale->studyGroup->displayName }}
                                        @elseif($scale->course)
                                            دورة: {{ $scale->course->displayName }}
                                        @else
                                            السلم الافتراضي العام لكافة الدورات
                                        @endif
                                    </span>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-black font-mono bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    {{ $scale->total_max }} درجة
                                </span>
                            </div>

                            <!-- تفاصيل الدرجات كأشرطة ملونة -->
                            <div class="grid grid-cols-3 gap-2 text-[11px] pt-2 border-t border-slate-100 font-bold text-slate-700">
                                <div class="bg-slate-50 p-2 rounded-lg border border-slate-200 text-center">
                                    <span class="text-slate-400 block text-[10px]">نظري / حضور</span>
                                    <span class="font-mono text-indigo-700 font-black">{{ $scale->attendance_max }}</span>
                                </div>
                                <div class="bg-slate-50 p-2 rounded-lg border border-slate-200 text-center">
                                    <span class="text-slate-400 block text-[10px]">مشاركة / تفاعل</span>
                                    <span class="font-mono text-indigo-700 font-black">{{ $scale->participation_max }}</span>
                                </div>
                                <div class="bg-slate-50 p-2 rounded-lg border border-slate-200 text-center">
                                    <span class="text-slate-400 block text-[10px]">أنشطة / تكاليف</span>
                                    <span class="font-mono text-indigo-700 font-black">{{ $scale->activities_max }}</span>
                                </div>
                                <div class="bg-slate-50 p-2 rounded-lg border border-slate-200 text-center">
                                    <span class="text-slate-400 block text-[10px]">اختبار نصفي</span>
                                    <span class="font-mono text-purple-700 font-black">{{ $scale->midterm_max }}</span>
                                </div>
                                <div class="bg-slate-50 p-2 rounded-lg border border-slate-200 text-center">
                                    <span class="text-slate-400 block text-[10px]">شفهي / عملي</span>
                                    <span class="font-mono text-purple-700 font-black">{{ $scale->practical_max }}</span>
                                </div>
                                <div class="bg-slate-50 p-2 rounded-lg border border-slate-200 text-center">
                                    <span class="text-slate-400 block text-[10px]">اختبار نهائي</span>
                                    <span class="font-mono text-rose-700 font-black">{{ $scale->final_exam_max }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                                <span class="text-slate-500 font-bold">نسبة النجاح: <strong class="text-emerald-700 font-mono">{{ $scale->passing_percentage }}%</strong></span>
                                <a href="{{ route('admin.exams_management.index', ['tab' => 'scales', 'course_id' => $scale->course_id, 'group_id' => $scale->study_group_id]) }}"
                                   class="text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1">
                                    <span>تعديل السلم</span>
                                    <span>←</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 bg-white p-8 rounded-2xl border border-slate-200 text-center text-slate-400">
                            لا يوجد سلالم مخصصة حالياً، يمكنك إنشاء السلم القياسي من النموذج باليمين.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    <!-- ========================================== -->
    <!-- التبويب 3: رصد ومراجعة درجات الطلاب والكشوفات -->
    <!-- ========================================== -->
    @elseif($tab === 'grading')
        <div class="space-y-6">

            @if(!$selectedGroup)
                <!-- رسالة إرشادية لاختيار المجموعة -->
                <div class="bg-white p-12 rounded-3xl shadow-sm border border-slate-200 text-center max-w-2xl mx-auto space-y-4">
                    <div class="w-16 h-16 bg-purple-100 text-purple-700 rounded-2xl flex items-center justify-center text-3xl mx-auto shadow-sm">
                        👥
                    </div>
                    <h3 class="text-lg font-black text-slate-900">يرجى تحديد المجموعة الدراسية أولاً</h3>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">
                        اختر الشعبة أو المجموعة الدراسية لاستعراض كشف درجات طلابها، رصد التقييمات، تعديل النتائج، وطباعة الكشوفات الرسمية أو إصدار الشهائد.
                    </p>
                    <form action="{{ route('admin.exams_management.index') }}" method="GET" class="flex items-center justify-center gap-2 max-w-md mx-auto pt-2">
                        <input type="hidden" name="tab" value="grading">
                        <select name="group_id" onchange="this.form.submit()" class="px-4 py-2.5 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 bg-slate-50">
                            <option value="">-- اضغط هنا لاختيار المجموعة --</option>
                            @foreach($groups as $grp)
                                <option value="{{ $grp->id }}">{{ $grp->displayName }} ({{ $grp->course?->displayName ?: 'عام' }})</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @else
                <!-- لوحة رصد درجات المجموعة المحددة -->
                <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-200 space-y-4">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg bg-purple-100 text-purple-800 font-black text-xs">
                                    مجموعة: {{ $selectedGroup->displayName }}
                                </span>
                                @if($selectedGroup->course)
                                    <span class="text-xs text-slate-500 font-bold">• {{ $selectedGroup->course->displayName }}</span>
                                @endif
                                @if($selectedGroup->teacher)
                                    <span class="text-xs text-slate-500 font-bold">• المعلم: {{ $selectedGroup->teacher->name }}</span>
                                @endif
                            </div>
                            <h2 class="text-lg font-black text-slate-900 mt-1">
                                كشف رصد وتعديل درجات الطلاب الأكاديمية (100 درجة)
                            </h2>
                            <p class="text-xs text-slate-500">
                                السلم المطبق: <strong>{{ $groupScale->name }}</strong> (نسبة النجاح: {{ $groupScale->passing_percentage }}%)
                            </p>
                        </div>

                        <!-- أزرار الإجراءات السريعة (طباعة وإصدار شهائد) -->
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('admin.exams_management.grade_sheet.print', ['groupId' => $selectedGroup->id, 'type' => 'detailed']) }}" target="_blank"
                               class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5 shadow-sm">
                                <span>🖨️</span>
                                <span>طباعة كشف تفصيلي</span>
                            </a>
                            <a href="{{ route('admin.exams_management.grade_sheet.print', ['groupId' => $selectedGroup->id, 'type' => 'summary']) }}" target="_blank"
                               class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                                <span>📄</span>
                                <span>طباعة كشف إجمالي</span>
                            </a>

                            <!-- زر إصدار الشهائد لكافة الناجحين -->
                            <form action="{{ route('admin.exams_management.certificates.bulk_generate', $selectedGroup->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إصدار الشهائد الأكاديمية لكافة الطلاب الناجحين في هذه المجموعة؟')">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                                    <span>🎓</span>
                                    <span>إصدار الشهائد للناجحين دفعة واحدة ({{ $readyForCertCount }} جاهز)</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- إحصائيات رصد الدرجات للمجموعة -->
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-center">
                            <span class="text-[11px] text-slate-500 block font-bold">إجمالي طلاب المجموعة</span>
                            <strong class="text-base font-black text-slate-900 font-mono">{{ $gradingStats['total_students'] }}</strong>
                        </div>
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-center">
                            <span class="text-[11px] text-emerald-700 block font-bold">ناجح ومؤهل للشهادة 🎓</span>
                            <strong class="text-base font-black text-emerald-800 font-mono">{{ $gradingStats['passed'] }}</strong>
                        </div>
                        <div class="bg-rose-50 border border-rose-200 rounded-xl p-3 text-center">
                            <span class="text-[11px] text-rose-700 block font-bold">راسب ❌</span>
                            <strong class="text-base font-black text-rose-800 font-mono">{{ $gradingStats['failed'] }}</strong>
                        </div>
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-center">
                            <span class="text-[11px] text-amber-700 block font-bold">قيد التقييم / ناقص</span>
                            <strong class="text-base font-black text-amber-800 font-mono">{{ $gradingStats['incomplete'] }}</strong>
                        </div>
                        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-3 text-center">
                            <span class="text-[11px] text-indigo-700 block font-bold">متوسط الدرجات</span>
                            <strong class="text-base font-black text-indigo-800 font-mono">{{ $gradingStats['average_score'] }}%</strong>
                        </div>
                    </div>
                </div>

                <!-- نموذج ورقة رصد الدرجات التفاعلية للطلاب -->
                <form action="{{ route('admin.exams_management.grades.save', $selectedGroup->id) }}" method="POST" id="gradesForm">
                    @csrf

                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-right text-xs">
                                <thead class="bg-slate-900 text-white font-black">
                                    <tr>
                                        <th class="p-3 text-center">#</th>
                                        <th class="p-3">اسم الطالب / الرمز</th>
                                        <th class="p-3 text-center bg-slate-800/80">
                                            <span>الحضور ({{ $groupScale->attendance_max }})</span>
                                        </th>
                                        <th class="p-3 text-center bg-slate-800/80">
                                            <span>المشاركة ({{ $groupScale->participation_max }})</span>
                                        </th>
                                        <th class="p-3 text-center bg-slate-800/80">
                                            <span>الأنشطة ({{ $groupScale->activities_max }})</span>
                                        </th>
                                        <th class="p-3 text-center bg-indigo-950">
                                            <span>نصفي ({{ $groupScale->midterm_max }})</span>
                                        </th>
                                        <th class="p-3 text-center bg-indigo-950">
                                            <span>عملي/شفهي ({{ $groupScale->practical_max }})</span>
                                        </th>
                                        <th class="p-3 text-center bg-rose-950">
                                            <span>نهائي ({{ $groupScale->final_exam_max }})</span>
                                        </th>
                                        <th class="p-3 text-center bg-amber-500 text-slate-950">
                                            <span>المجموع (100)</span>
                                        </th>
                                        <th class="p-3 text-center">النسبة والتقدير</th>
                                        <th class="p-3 text-center">الحالة</th>
                                        <th class="p-3">ملاحظات المعلم</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                                    @forelse($groupStudents as $idx => $st)
                                        @php
                                            $cg = $st->course_grade;
                                        @endphp
                                        <tr class="hover:bg-slate-50 transition student-row" id="row-{{ $st->id }}">
                                            <td class="p-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                            <td class="p-3">
                                                <div class="font-black text-slate-900 text-sm">{{ $st->name }}</div>
                                                <div class="text-[10px] text-slate-500 font-mono">{{ $st->user_code ?: $st->email }}</div>
                                            </td>

                                            <!-- 1. حضور -->
                                            <td class="p-2 text-center bg-slate-50/50">
                                                <input type="number" step="0.5" min="0" max="{{ $groupScale->attendance_max }}"
                                                       name="grades[{{ $st->id }}][attendance]"
                                                       value="{{ $cg->attendance_score }}"
                                                       oninput="calcStudentRow({{ $st->id }})"
                                                       class="w-16 px-2 py-1.5 text-center font-mono font-bold text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 inp-att">
                                            </td>

                                            <!-- 2. مشاركة -->
                                            <td class="p-2 text-center bg-slate-50/50">
                                                <input type="number" step="0.5" min="0" max="{{ $groupScale->participation_max }}"
                                                       name="grades[{{ $st->id }}][participation]"
                                                       value="{{ $cg->participation_score }}"
                                                       oninput="calcStudentRow({{ $st->id }})"
                                                       class="w-16 px-2 py-1.5 text-center font-mono font-bold text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 inp-par">
                                            </td>

                                            <!-- 3. أنشطة -->
                                            <td class="p-2 text-center bg-slate-50/50">
                                                <input type="number" step="0.5" min="0" max="{{ $groupScale->activities_max }}"
                                                       name="grades[{{ $st->id }}][activities]"
                                                       value="{{ $cg->activities_score }}"
                                                       oninput="calcStudentRow({{ $st->id }})"
                                                       class="w-16 px-2 py-1.5 text-center font-mono font-bold text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 inp-act">
                                            </td>

                                            <!-- 4. نصفي -->
                                            <td class="p-2 text-center bg-indigo-50/30">
                                                <input type="number" step="0.5" min="0" max="{{ $groupScale->midterm_max }}"
                                                       name="grades[{{ $st->id }}][midterm]"
                                                       value="{{ $cg->midterm_score }}"
                                                       oninput="calcStudentRow({{ $st->id }})"
                                                       class="w-16 px-2 py-1.5 text-center font-mono font-bold text-xs border border-indigo-300 rounded-lg focus:ring-2 focus:ring-indigo-500 inp-mid">
                                            </td>

                                            <!-- 5. شفهي / عملي -->
                                            <td class="p-2 text-center bg-indigo-50/30">
                                                <input type="number" step="0.5" min="0" max="{{ $groupScale->practical_max }}"
                                                       name="grades[{{ $st->id }}][practical]"
                                                       value="{{ $cg->practical_score }}"
                                                       oninput="calcStudentRow({{ $st->id }})"
                                                       class="w-16 px-2 py-1.5 text-center font-mono font-bold text-xs border border-indigo-300 rounded-lg focus:ring-2 focus:ring-indigo-500 inp-pra">
                                            </td>

                                            <!-- 6. نهائي -->
                                            <td class="p-2 text-center bg-rose-50/30">
                                                <input type="number" step="0.5" min="0" max="{{ $groupScale->final_exam_max }}"
                                                       name="grades[{{ $st->id }}][final_exam]"
                                                       value="{{ $cg->final_exam_score }}"
                                                       oninput="calcStudentRow({{ $st->id }})"
                                                       class="w-16 px-2 py-1.5 text-center font-mono font-bold text-xs border border-rose-300 rounded-lg focus:ring-2 focus:ring-rose-500 inp-fin">
                                            </td>

                                            <!-- المجموع المحسوب -->
                                            <td class="p-2 text-center bg-amber-50">
                                                <span class="font-mono font-black text-sm text-slate-900 cell-total">{{ $cg->total_score }}</span>
                                            </td>

                                            <!-- التقدير والنسبة -->
                                            <td class="p-2 text-center">
                                                <div class="font-mono font-bold text-xs text-indigo-700 cell-pct">{{ $cg->percentage }}%</div>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border cell-letter {{ $cg->grade_badge_class }}">
                                                    {{ $cg->grade_letter }}
                                                </span>
                                            </td>

                                            <!-- الحالة -->
                                            <td class="p-2 text-center">
                                                <span class="px-2 py-1 rounded-full text-[11px] font-bold border cell-status {{ $cg->status_badge_class }}">
                                                    {{ $cg->status_label }}
                                                </span>
                                            </td>

                                            <!-- ملاحظات -->
                                            <td class="p-2">
                                                <input type="text" name="grades[{{ $st->id }}][notes]"
                                                       value="{{ $cg->teacher_notes }}" placeholder="ملاحظات المعلم..."
                                                       class="w-36 px-2 py-1.5 text-xs border border-slate-300 rounded-lg">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="p-8 text-center text-slate-400">
                                                لا يوجد طلاب مسكنين في هذه المجموعة حالياً.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- شريط التثبيت والحفظ السفلي -->
                        <div class="p-5 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="finalize" value="1" class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                                    <span class="text-xs font-black text-slate-900">تثبيت واعتماد النتائج النهائية للمجموعة</span>
                                </label>
                                <span class="text-xs text-slate-400">• يتم تأكيد جاهزية الطلاب لإصدار الشهائد الرسمية.</span>
                            </div>

                            <button type="submit" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 text-white font-black text-sm rounded-xl shadow-lg transition flex items-center gap-2">
                                <span>💾</span>
                                <span>حفظ وتحديث درجات المجموعة</span>
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>

    <!-- ========================================== -->
    <!-- التبويب 4: إصدار وطباعة الشهائد الأكاديمية -->
    <!-- ========================================== -->
    @elseif($tab === 'certificates')
        <div class="space-y-6">

            <!-- شريط رأس التبويب والعمليات -->
            <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span>📜</span>
                        <span>سجل الشهائد الأكاديمية الصادرة والتحقق الرقمي</span>
                    </h2>
                    <p class="text-xs text-slate-500 font-medium">
                        كافة شهائد إتمام الدورات والدبلومات المصدرة مع أرقام التسلسل المعتمدة ورموز التحقق QR.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if($selectedGroup)
                        <!-- طباعة شهائد المجموعة كاملة دفعة واحدة -->
                        <a href="{{ route('admin.exams_management.certificates.print_batch', $selectedGroup->id) }}" target="_blank"
                           class="px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                            <span>🖨️</span>
                            <span>طباعة شهائد المجموعة دفعة واحدة (Batch Print)</span>
                        </a>

                        <!-- إصدار شهائد للناجحين -->
                        <form action="{{ route('admin.exams_management.certificates.bulk_generate', $selectedGroup->id) }}" method="POST" onsubmit="return confirm('إصدار شهائد لكافة الناجحين في المجموعة؟')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                                <span>➕</span>
                                <span>إصدار شهائد المجموعة ({{ $readyForCertCount }} مؤهل)</span>
                            </button>
                        </form>
                    @endif

                    <button onclick="document.getElementById('singleCertModal').classList.remove('hidden')"
                            class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                        <span>🎓</span>
                        <span>إصدار شهادة فردية</span>
                    </button>
                </div>
            </div>

            <!-- جدول الشهائد الصادرة -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-black">
                            <tr>
                                <th class="p-4">رقم الشهادة المعتمد</th>
                                <th class="p-4">اسم الطالب (عربي / English)</th>
                                <th class="p-4">الدورة / الدبلوم</th>
                                <th class="p-4">المجموعة الدراسية</th>
                                <th class="p-4 text-center">الساعات</th>
                                <th class="p-4 text-center">المجموع والنسبة</th>
                                <th class="p-4 text-center">التقدير</th>
                                <th class="p-4">تاريخ الإصدار</th>
                                <th class="p-4 text-center">التحقق والطباعة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                            @forelse($certificates as $cert)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4">
                                        <div class="font-mono font-black text-sm text-indigo-700">{{ $cert->certificate_number }}</div>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 mt-1">
                                            <span>✓</span>
                                            <span>معتمدة رسمياً</span>
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-black text-slate-900 text-sm">{{ $cert->student_name_ar }}</div>
                                        <div class="text-[11px] text-slate-500 font-sans font-bold">{{ $cert->student_name_en }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-slate-900">{{ $cert->course_name_ar }}</div>
                                        <div class="text-[11px] text-slate-500 font-sans">{{ $cert->course_name_en }}</div>
                                    </td>
                                    <td class="p-4">
                                        @if($cert->studyGroup)
                                            <span class="font-bold text-purple-700">{{ $cert->studyGroup->displayName }}</span>
                                        @else
                                            <span class="text-slate-400">عام</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center font-mono font-bold text-slate-700">
                                        {{ $cert->hours }} ساعة
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="font-mono font-black text-slate-900 text-sm">{{ $cert->total_score }} د</span>
                                        <div class="text-[11px] font-mono font-bold text-emerald-700">({{ $cert->percentage }}%)</div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold border {{ $cert->percentage >= 90 ? 'bg-purple-100 text-purple-800 border-purple-200 font-black' : 'bg-blue-100 text-blue-800 border-blue-200' }}">
                                            {{ $cert->grade_letter }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-slate-600 font-mono text-[11px]">
                                        {{ $cert->issue_date ? $cert->issue_date->format('Y-m-d') : '—' }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- طباعة الشهادة الملكية -->
                                            <a href="{{ route('admin.exams_management.certificates.print', $cert->id) }}" target="_blank"
                                               class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow-sm transition flex items-center gap-1">
                                                <span>🖨️</span>
                                                <span>طباعة</span>
                                            </a>
                                            <!-- معاينة التحقق العامة -->
                                            <a href="{{ route('verify.certificate', $cert->certificate_number) }}" target="_blank"
                                               class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="رابط التحقق العام">
                                                🔍
                                            </a>
                                            <!-- حذف الشهادة -->
                                            <form action="{{ route('admin.exams_management.certificates.destroy', $cert->id) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذه الشهادة نهائياً؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 transition" title="حذف الشهادة">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-12 text-center text-slate-400">
                                        <div class="text-4xl mb-2">📜</div>
                                        <div class="text-sm font-bold text-slate-600">لا يوجد شهائد مصدرة حالياً</div>
                                        <p class="text-xs text-slate-400 mt-1">يمكنك رصد درجات الطلاب والضغط على «إصدار الشهائد للناجحين دفعة واحدة» لتوليدها فوراً.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($certificates->hasPages())
                    <div class="p-4 border-t border-slate-200">
                        {{ $certificates->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>

<!-- ============================================================== -->
<!-- مودال 1: إنشاء وتخصيص اختبار / كويز جديد مع التوجيه والدرجة -->
<!-- ============================================================== -->
<div id="createExamModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span>➕</span>
                    <span>إنشاء وتخصيص اختبار أو كويز جديد</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    حدد النطاق والوزن والدرجة المخصصة للاختبار (عام، دورة، مستوى، مجموعة، أو طلاب).
                </p>
            </div>
            <button onclick="document.getElementById('createExamModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-black text-lg">✕</button>
        </div>

        <form action="{{ route('admin.exams_management.exams.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">عنوان الاختبار / الكويز *</label>
                <input type="text" name="title" required placeholder="مثال: كويز القواعد الأسبوعي / الاختبار النهائي للدورة..."
                       class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">فترة ونوع الاختبار *</label>
                    <select name="quiz_period" required class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        <option value="weekly">كويز أسبوعي (Weekly Quiz)</option>
                        <option value="midterm">اختبار منتصف الدورة (نصفي)</option>
                        <option value="final">اختبار نهاية الدورة (نهائي)</option>
                        <option value="pop_quiz">كويز قصير / مفاجئ</option>
                        <option value="placement">اختبار تحديد مستوى</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">تصنيف الوزن في سلم الدورة</label>
                    <select name="weight_category" class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        <option value="midterm">درجات الاختبار النصفي (Midterm)</option>
                        <option value="final">درجات الاختبار النهائي (Final Exam)</option>
                        <option value="activities">أنشطة وتكاليف (Activities)</option>
                        <option value="participation">مشاركة وتفاعل (Participation)</option>
                        <option value="quiz">كويز تدريبي عام</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">نطاق التخصيص والإسناد *</label>
                    <select name="target_type" id="createTargetType" onchange="toggleTargetFields(this.value)" required
                            class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        <option value="group">مجموعة دراسية محددة (Study Group)</option>
                        <option value="course">دورة تدريبية محددة (Course)</option>
                        <option value="level">مستوى أكاديمي (Level)</option>
                        <option value="all">عام لكافة الطلاب في المنصة</option>
                    </select>
                </div>

                <div id="fieldGroupSelect">
                    <label class="block text-xs font-bold text-slate-700 mb-1">المجموعة الدراسية</label>
                    <select name="study_group_id" class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        <option value="">-- اختر المجموعة --</option>
                        @foreach($groups as $grp)
                            <option value="{{ $grp->id }}" {{ (string)$groupId === (string)$grp->id ? 'selected' : '' }}>
                                {{ $grp->displayName }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">الدورة التدريبية</label>
                    <select name="course_id" class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        <option value="">-- اختياري أو عام --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ (string)$courseId === (string)$c->id ? 'selected' : '' }}>
                                {{ $c->displayName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">المستوى الأكاديمي</label>
                    <select name="level_id" class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        <option value="">-- اختياري --</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl->id }}">{{ $lvl->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">الدرجة الكلية *</label>
                    <input type="number" step="0.5" min="1" max="100" name="total_points" value="10" required
                           class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">درجة النجاح *</label>
                    <input type="number" step="0.5" min="1" max="100" name="passing_score" value="6" required
                           class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">المدة (دقيقة) *</label>
                    <input type="number" min="5" max="240" name="duration_minutes" value="20" required
                           class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-300 rounded-lg">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">وصف أو تعليمات الاختبار للطلاب</label>
                <textarea name="description" rows="2" placeholder="أدخل تعليمات أداء الاختبار إن وجدت..."
                          class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('createExamModal').classList.add('hidden')"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    إلغاء
                </button>
                <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs sm:text-sm rounded-xl shadow-md transition">
                    حفظ وإسناد الاختبار
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- مودال 2: تعديل إسناد ودرجة اختبار قائم -->
<!-- ============================================================== -->
<div id="assignExamModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-5 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-base font-black text-slate-900" id="assignModalTitle">تعديل إسناد ودرجة الاختبار</h3>
                <span class="text-xs text-slate-400">تحديث النطاق وتوزيع الدرجات</span>
            </div>
            <button onclick="document.getElementById('assignExamModal').classList.add('hidden')" class="text-slate-400 font-black">✕</button>
        </div>

        <form id="assignExamForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">نطاق التخصيص</label>
                <select name="target_type" id="edit_target_type" required class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl">
                    <option value="group">مجموعة دراسية محددة</option>
                    <option value="course">دورة تدريبية محددة</option>
                    <option value="level">مستوى أكاديمي</option>
                    <option value="all">عام لكافة الطلاب</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">المجموعة الدراسية</label>
                <select name="study_group_id" id="edit_study_group_id" class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl">
                    <option value="">-- بدون مجموعة --</option>
                    @foreach($groups as $grp)
                        <option value="{{ $grp->id }}">{{ $grp->displayName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">الدورة التدريبية</label>
                    <select name="course_id" id="edit_course_id" class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl">
                        <option value="">-- عام --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->displayName }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">الوزن النسبي</label>
                    <select name="weight_category" id="edit_weight_category" class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl">
                        <option value="midterm">نصفي</option>
                        <option value="final">نهائي</option>
                        <option value="activities">أنشطة</option>
                        <option value="participation">مشاركة</option>
                        <option value="quiz">كويز</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">الدرجة الكلية *</label>
                    <input type="number" step="0.5" min="1" max="100" name="total_points" id="edit_total_points" required
                           class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">درجة النجاح *</label>
                    <input type="number" step="0.5" min="1" max="100" name="passing_score" id="edit_passing_score" required
                           class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-300 rounded-lg">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('assignExamModal').classList.add('hidden')"
                        class="px-4 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- مودال 3: إصدار شهادة فردية لطالب محدد -->
<!-- ============================================================== -->
<div id="singleCertModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <span>🎓</span>
                <span>إصدار شهادة أكاديمية فردية</span>
            </h3>
            <button onclick="document.getElementById('singleCertModal').classList.add('hidden')" class="text-slate-400 font-black">✕</button>
        </div>

        <form action="{{ route('admin.exams_management.certificates.generate_single') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">المجموعة الدراسية *</label>
                <select name="study_group_id" id="singleCertGroup" onchange="loadGroupStudentsForCert(this.value)" required class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl">
                    <option value="">-- اختر المجموعة --</option>
                    @foreach($groups as $grp)
                        <option value="{{ $grp->id }}" {{ (string)$groupId === (string)$grp->id ? 'selected' : '' }}>
                            {{ $grp->displayName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">الطالب *</label>
                <select name="student_id" id="singleCertStudent" required class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl">
                    <option value="">-- اختر الطالب --</option>
                    @if($selectedGroup)
                        @foreach($selectedGroup->students as $st)
                            <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->user_code ?: $st->email }})</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">نوع الشهادة *</label>
                    <select name="type" required class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl">
                        <option value="course">شهادة إتمام دورة تدريبية</option>
                        <option value="diploma">شهادة دبلوم معتمد</option>
                        <option value="level">شهادة إتمام مستوى</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">عدد الساعات التدريبية</label>
                    <input type="number" name="hours" value="40" min="1" class="w-full px-3 py-2 text-xs font-mono font-bold border border-slate-300 rounded-xl">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">تاريخ الإصدار</label>
                <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-xs font-bold border border-slate-300 rounded-xl">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('singleCertModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl">إصدار الشهادة فوراً</button>
            </div>
        </form>
    </div>
</div>

<script>
    // حساب المجموع الكلي لسلم الدرجات تفاعلياً
    function calcScaleTotal() {
        const att = parseFloat(document.getElementById('sc_att')?.value) || 0;
        const par = parseFloat(document.getElementById('sc_par')?.value) || 0;
        const act = parseFloat(document.getElementById('sc_act')?.value) || 0;
        const mid = parseFloat(document.getElementById('sc_mid')?.value) || 0;
        const pra = parseFloat(document.getElementById('sc_pra')?.value) || 0;
        const fin = parseFloat(document.getElementById('sc_fin')?.value) || 0;
        const sum = att + par + act + mid + pra + fin;

        const disp = document.getElementById('scaleTotalDisplay');
        if (disp) {
            disp.innerText = sum;
            disp.className = sum === 100 ? 'text-lg font-black text-emerald-600 font-mono' : 'text-lg font-black text-rose-600 font-mono';
        }
    }

    // حساب درجات سطر الطالب تفاعلياً
    function calcStudentRow(studentId) {
        const row = document.getElementById('row-' + studentId);
        if (!row) return;

        const att = parseFloat(row.querySelector('.inp-att')?.value) || 0;
        const par = parseFloat(row.querySelector('.inp-par')?.value) || 0;
        const act = parseFloat(row.querySelector('.inp-act')?.value) || 0;
        const mid = parseFloat(row.querySelector('.inp-mid')?.value) || 0;
        const pra = parseFloat(row.querySelector('.inp-pra')?.value) || 0;
        const fin = parseFloat(row.querySelector('.inp-fin')?.value) || 0;

        const total = att + par + act + mid + pra + fin;
        const passingPct = {{ $groupScale ? $groupScale->passing_percentage : 60 }};
        const totalMax = {{ $groupScale ? $groupScale->total_max : 100 }};
        const pct = totalMax > 0 ? ((total / totalMax) * 100).toFixed(1) : 0;

        const totalCell = row.querySelector('.cell-total');
        const pctCell = row.querySelector('.cell-pct');
        const letterCell = row.querySelector('.cell-letter');
        const statusCell = row.querySelector('.cell-status');

        if (totalCell) totalCell.innerText = total;
        if (pctCell) pctCell.innerText = pct + '%';

        // حساب التقدير
        let letter = 'راسب (Fail)';
        let letterClass = 'bg-rose-100 text-rose-900 border-rose-300 font-bold';
        if (pct >= 90) {
            letter = 'ممتاز (Excellent)';
            letterClass = 'bg-purple-100 text-purple-900 border-purple-300 font-black';
        } else if (pct >= 80) {
            letter = 'جيد جداً (Very Good)';
            letterClass = 'bg-blue-100 text-blue-900 border-blue-300 font-bold';
        } else if (pct >= 70) {
            letter = 'جيد (Good)';
            letterClass = 'bg-emerald-100 text-emerald-900 border-emerald-300 font-bold';
        } else if (pct >= 60) {
            letter = 'مقبول (Pass)';
            letterClass = 'bg-amber-100 text-amber-900 border-amber-300 font-bold';
        }

        if (letterCell) {
            letterCell.innerText = letter;
            letterCell.className = 'px-2 py-0.5 rounded text-[10px] font-bold border cell-letter ' + letterClass;
        }

        if (statusCell) {
            if (pct >= passingPct) {
                statusCell.innerText = 'ناجح ومؤهل للشهادة 🎓';
                statusCell.className = 'px-2 py-1 rounded-full text-[11px] font-bold border cell-status bg-emerald-100 text-emerald-800 border-emerald-300';
            } else {
                statusCell.innerText = 'راسب ❌';
                statusCell.className = 'px-2 py-1 rounded-full text-[11px] font-bold border cell-status bg-rose-100 text-rose-800 border-rose-300';
            }
        }
    }

    // فتح مودال تعديل إسناد الاختبار
    function openAssignModal(id, title, targetType, courseId, groupId, levelId, quizPeriod, weightCategory, totalPoints, passingScore) {
        const form = document.getElementById('assignExamForm');
        form.action = '/admin/exams-management/exams/' + id + '/assign';
        document.getElementById('assignModalTitle').innerText = 'تعديل إسناد: ' + title;
        document.getElementById('edit_target_type').value = targetType || 'group';
        document.getElementById('edit_study_group_id').value = groupId || '';
        document.getElementById('edit_course_id').value = courseId || '';
        document.getElementById('edit_weight_category').value = weightCategory || 'midterm';
        document.getElementById('edit_total_points').value = totalPoints || 10;
        document.getElementById('edit_passing_score').value = passingScore || 6;

        document.getElementById('assignExamModal').classList.remove('hidden');
    }
</script>
@endsection
