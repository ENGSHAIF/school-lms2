@extends('layouts.app', ['title' => 'بوابة ولي الأمر - متابعة الأبناء الشاملة'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto my-4">

    <!-- شريط الترحيب والتعريف بحساب ولي الأمر ومجموعة الصلاحيات -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-7 sm:p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border border-slate-800">
        <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2">
                <div class="inline-flex items-center gap-2 bg-indigo-500/20 text-indigo-300 text-xs px-3.5 py-1 rounded-full font-bold border border-indigo-400/30">
                    <span>👨‍👧‍👦</span>
                    <span>بوابة ولي الأمر الأكاديمية والمالية الشاملة 360</span>
                </div>
                @if($parent->permissionGroup)
                    <span class="inline-flex items-center gap-1 bg-amber-500/20 text-amber-300 text-[11px] px-3 py-0.5 rounded-full font-bold border border-amber-400/30">
                        <span>🛡️</span>
                        <span>مجموعة الصلاحيات: {{ $parent->permissionGroup->name }}</span>
                    </span>
                @endif
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">أهلاً بك، {{ $parent->name }}</h1>
            <p class="text-xs text-slate-300 leading-relaxed max-w-2xl">
                منصة المتابعة الموحدة لأبنائك: تقارير الحضور والغياب، التقييمات اليومية ورصيد النجوم ⭐، الواجبات والأنشطة، نتائج الكويزات، وسندات القبض والسجل الشامل من أول يوم حتى التخرج.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" onclick="document.getElementById('modalAddChild').classList.remove('hidden')" 
                class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs px-5 py-3 rounded-2xl shadow-lg transition flex items-center gap-2 whitespace-nowrap">
                <span>➕</span>
                <span>إضافة طفل جديد للحساب</span>
            </button>
        </div>
    </div>

    <!-- شريط محول واختيار الأبناء (Child Switcher Bar) -->
    <div class="bg-white rounded-3xl border border-slate-200 p-4 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2 shrink-0">
            <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm">👨‍👧‍👦</span>
            <div>
                <span class="text-xs font-black text-slate-900 block">الأبناء المسجلون تحت حسابك:</span>
                <span class="text-[10px] text-slate-400">{{ $childrenProfiles->count() }} طالب / أبناء مرتبطون</span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            @forelse($childrenProfiles as $cp)
                @php $childUser = $cp->user; @endphp
                @if($childUser)
                    <a href="{{ route('parent.dashboard', ['child_id' => $childUser->id]) }}" 
                        class="px-4 py-2.5 rounded-2xl text-xs font-black transition flex items-center gap-2 border {{ ($activeChild && $activeChild->id === $childUser->id) ? 'bg-indigo-600 text-white border-indigo-600 shadow-md ring-2 ring-indigo-200' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                        <span class="text-base">{{ $childUser->gender === 'female' ? '👧' : '👦' }}</span>
                        <span>{{ $childUser->name }}</span>
                        <span class="text-[10px] opacity-80 font-mono">({{ $childUser->user_code ?? $childUser->student_code }})</span>
                    </a>
                @endif
            @empty
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs flex items-center gap-2">
                    <span>⚠️</span>
                    <span>لا يوجد أبناء مرتبطون بحسابك حالياً. يمكنك إضافة طفل جديد أو التواصل مع إدارة المدرسة لربط أبنائك.</span>
                </div>
            @endforelse
        </div>
    </div>

    @if($activeChild)
        <!-- ================= بطاقة تعريف الطالب النشط المحدد ================= -->
        <div class="bg-gradient-to-r from-indigo-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-7 shadow-lg border border-indigo-800 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-amber-400 to-amber-500 text-slate-950 font-black text-3xl flex items-center justify-center shadow-md shrink-0">
                    {{ $activeChild->gender === 'female' ? '👧' : '👦' }}
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-black text-white">{{ $activeChild->name }}</h2>
                        @if($activeChild->name_en)
                            <span class="text-xs text-indigo-300 font-mono" dir="ltr">({{ $activeChild->name_en }})</span>
                        @endif
                        <span class="font-mono text-xs px-2.5 py-0.5 rounded-full bg-indigo-500/30 text-indigo-200 border border-indigo-400/40">
                            {{ $activeChild->user_code ?? $activeChild->student_code }}
                        </span>
                        <span class="text-xs px-2.5 py-0.5 rounded-full {{ $activeChild->status === 'active' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-amber-500/20 text-amber-300 border border-amber-400/30' }} font-bold">
                            {{ $activeChild->status === 'active' ? 'مفعل دراسياً 🟢' : 'بانتظار الاعتماد 🟡' }}
                        </span>
                    </div>

                    @if(auth()->user()->hasPermission('parent.profile_view'))
                        <div class="text-xs text-indigo-200 flex flex-wrap items-center gap-3">
                            <span>المرحلة: <strong class="text-amber-300">{{ $activeChildProfile->grade_substage ?? 'مسار عام' }}</strong></span>
                            <span>•</span>
                            @if($activeChild->enrollments->isNotEmpty() && $activeChild->enrollments->first()->level)
                                <span>المستوى الحالي: <strong class="text-white">{{ $activeChild->enrollments->first()->level->name }}</strong></span>
                                <span>•</span>
                            @endif
                            @if($studyGroups->isNotEmpty())
                                <span>الشعبة / المجموعة: <strong class="text-emerald-300">{{ $studyGroups->first()->name }}</strong></span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- زر السجل الشامل 360 المعتمد -->
            @if(auth()->user()->hasPermission('parent.transcript_view'))
                <a href="{{ route('admin.reports.student.transcript', $activeChild->id) }}" target="_blank"
                    class="bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-slate-950 font-black text-xs px-6 py-3.5 rounded-2xl shadow-xl transition flex items-center gap-2 whitespace-nowrap self-stretch lg:self-auto justify-center">
                    <span>📜</span>
                    <span>السجل الأكاديمي والمالي الشامل 360 (من الدخول للتخرج)</span>
                </a>
            @endif
        </div>

        <!-- ================= مؤشرات الأداء السريعة (KPI Highlights) ================= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- 1. نسبة الحضور والغياب -->
            @if(auth()->user()->hasPermission('parent.attendance_view'))
                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4 hover:border-emerald-300 transition">
                    <div class="w-13 h-13 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-2xl shadow-2xs shrink-0">
                        📅
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 block">نسبة الحضور والالتزام</span>
                        <strong class="text-2xl font-black text-slate-900 font-mono">{{ $attendanceRate }}%</strong>
                        <span class="text-[10px] text-emerald-600 font-bold block">{{ $presentCount }} جلسة حضور من {{ $totalSessions }}</span>
                    </div>
                </div>
            @endif

            <!-- 2. رصيد النجوم والتقييمات -->
            @if(auth()->user()->hasPermission('parent.evaluations_view'))
                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4 hover:border-amber-300 transition">
                    <div class="w-13 h-13 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-2xl shadow-2xs shrink-0">
                        ⭐
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 block">رصيد النجوم والمكافآت</span>
                        <strong class="text-2xl font-black text-amber-600 font-mono">{{ $totalStars }} ⭐</strong>
                        <span class="text-[10px] text-slate-500 block">{{ $evaluations->count() }} تقييم مرصود للطالب</span>
                    </div>
                </div>
            @endif

            <!-- 3. الرسوم والوضع المالي -->
            @if(auth()->user()->hasPermission('parent.finance_view'))
                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4 hover:border-indigo-300 transition">
                    <div class="w-13 h-13 rounded-2xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-2xl shadow-2xs shrink-0">
                        🧾
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 block">الرصيد المالي الحالي</span>
                        <strong class="text-xl font-black text-slate-900 font-mono">{{ number_format($currentBalance, 2) }} {{ $account->currency ?? 'YER' }}</strong>
                        <span class="text-[10px] text-slate-500 block">المسدد: {{ number_format($totalPaid, 2) }} {{ $account->currency ?? 'YER' }}</span>
                    </div>
                </div>
            @endif

            <!-- 4. محفظة الحصص أو الكويزات -->
            @if(auth()->user()->hasPermission('parent.exams_view'))
                <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4 hover:border-purple-300 transition">
                    <div class="w-13 h-13 rounded-2xl bg-purple-50 border border-purple-200 flex items-center justify-center text-2xl shadow-2xs shrink-0">
                        🎯
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 block">الاختبارات والكويزات المنجزة</span>
                        <strong class="text-2xl font-black text-slate-900 font-mono">{{ $examAttempts->count() }}</strong>
                        <span class="text-[10px] text-purple-700 font-bold block">معدل التحصيل والدرجات</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- ================= أزرار تبويبات المتابعة التفاعلية ================= -->
        @php
            $defaultTab = 'evaluations';
            if (!auth()->user()->hasPermission('parent.evaluations_view')) {
                if (auth()->user()->hasPermission('parent.attendance_view')) $defaultTab = 'attendance';
                elseif (auth()->user()->hasPermission('parent.activities_view')) $defaultTab = 'activities';
                elseif (auth()->user()->hasPermission('parent.exams_view')) $defaultTab = 'exams';
                elseif (auth()->user()->hasPermission('parent.finance_view')) $defaultTab = 'finance';
                elseif (auth()->user()->hasPermission('parent.transcript_view')) $defaultTab = 'transcript';
                elseif (auth()->user()->hasPermission('parent.live_schedule')) $defaultTab = 'schedule';
            }
        @endphp

        <div class="bg-white rounded-3xl border border-slate-200 p-2 shadow-sm flex flex-wrap items-center gap-2">
            @if(auth()->user()->hasPermission('parent.evaluations_view'))
                <button type="button" id="btn-tab-evaluations" onclick="switchParentTab('evaluations')"
                    class="parent-tab-btn px-4 py-2.5 rounded-2xl text-xs font-black transition flex items-center gap-2 {{ $defaultTab === 'evaluations' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100' }}">
                    <span>⭐</span>
                    <span>التقييمات اليومية والنجوم</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-400 text-slate-950 font-bold">{{ $evaluations->count() }}</span>
                </button>
            @endif

            @if(auth()->user()->hasPermission('parent.attendance_view'))
                <button type="button" id="btn-tab-attendance" onclick="switchParentTab('attendance')"
                    class="parent-tab-btn px-4 py-2.5 rounded-2xl text-xs font-black transition flex items-center gap-2 {{ $defaultTab === 'attendance' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100' }}">
                    <span>📅</span>
                    <span>سجل الحضور والغياب</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-800 font-bold">{{ $attendanceRate }}%</span>
                </button>
            @endif

            @if(auth()->user()->hasPermission('parent.activities_view'))
                <button type="button" id="btn-tab-activities" onclick="switchParentTab('activities')"
                    class="parent-tab-btn px-4 py-2.5 rounded-2xl text-xs font-black transition flex items-center gap-2 {{ $defaultTab === 'activities' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100' }}">
                    <span>📝</span>
                    <span>الواجبات والأنشطة المدرسية</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-100 text-indigo-800 font-bold">{{ $groupActivities->count() }}</span>
                </button>
            @endif

            @if(auth()->user()->hasPermission('parent.exams_view'))
                <button type="button" id="btn-tab-exams" onclick="switchParentTab('exams')"
                    class="parent-tab-btn px-4 py-2.5 rounded-2xl text-xs font-black transition flex items-center gap-2 {{ $defaultTab === 'exams' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100' }}">
                    <span>🏆</span>
                    <span>نتائج الاختبارات والكويزات</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-purple-100 text-purple-800 font-bold">{{ $examAttempts->count() }}</span>
                </button>
            @endif

            @if(auth()->user()->hasPermission('parent.finance_view'))
                <button type="button" id="btn-tab-finance" onclick="switchParentTab('finance')"
                    class="parent-tab-btn px-4 py-2.5 rounded-2xl text-xs font-black transition flex items-center gap-2 {{ $defaultTab === 'finance' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100' }}">
                    <span>🧾</span>
                    <span>الرسوم وسندات القبض</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-bold">{{ $vouchers->count() }}</span>
                </button>
            @endif

            @if(auth()->user()->hasPermission('parent.transcript_view'))
                <button type="button" id="btn-tab-transcript" onclick="switchParentTab('transcript')"
                    class="parent-tab-btn px-4 py-2.5 rounded-2xl text-xs font-black transition flex items-center gap-2 {{ $defaultTab === 'transcript' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100' }}">
                    <span>📜</span>
                    <span>مسار الطالب والسجل الشامل 360</span>
                </button>
            @endif

            @if(auth()->user()->hasPermission('parent.live_schedule'))
                <button type="button" id="btn-tab-schedule" onclick="switchParentTab('schedule')"
                    class="parent-tab-btn px-4 py-2.5 rounded-2xl text-xs font-black transition flex items-center gap-2 {{ $defaultTab === 'schedule' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100' }}">
                    <span>⏰</span>
                    <span>جدول الحصص والبث المباشر</span>
                </button>
            @endif
        </div>

        <!-- ================= محتويات التبويبات ================= -->

        <!-- 1. تبويب التقييمات اليومية والنجوم -->
        @if(auth()->user()->hasPermission('parent.evaluations_view'))
            <div id="tab-evaluations" class="parent-tab-content space-y-6 {{ $defaultTab === 'evaluations' ? '' : 'hidden' }}">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-sm space-y-5">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                                <span>⭐</span>
                                <span>سجل التقييم اليومي وتفاعل الطالب في الحصص</span>
                            </h3>
                            <p class="text-xs text-slate-500">متابعة تفاعل الطفل في الحصة، مشاركته الصوتية والكتابية، والنجوم التشجيعية الممنوحة له من المعلم.</p>
                        </div>
                        <div class="bg-amber-50 border border-amber-200 px-4 py-2 rounded-2xl flex items-center gap-2">
                            <span class="text-xs font-bold text-amber-900">إجمالي النجوم المحصودة:</span>
                            <span class="text-lg font-black text-amber-600 font-mono">{{ $totalStars }} ⭐</span>
                        </div>
                    </div>

                    <div class="space-y-3.5">
                        @forelse($evaluations as $ev)
                            <div class="border border-slate-200 hover:border-amber-300 rounded-2xl p-4 sm:p-5 bg-white transition space-y-3">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-2.5">
                                    <div class="flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">⭐</span>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <strong class="text-xs text-slate-900">{{ $ev->group?->name ?? 'شعبة دراسية' }}</strong>
                                                <span class="text-[10px] text-slate-400 font-mono">{{ $ev->session?->session_date ? $ev->session->session_date->format('Y-m-d') : $ev->created_at->format('Y-m-d') }}</span>
                                            </div>
                                            <span class="text-[11px] text-slate-500 block">المعلم: <strong>{{ $ev->session?->teacher?->name ?? 'معلم المادة' }}</strong></span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $ev->attendance_badge_class }}">
                                            {{ $ev->attendance_label }}
                                        </span>
                                        <div class="bg-amber-50 text-amber-800 border border-amber-200 px-3 py-1 rounded-full text-xs font-black">
                                            النجوم: {{ str_repeat('⭐', max(1, $ev->stars_awarded ?? 1)) }}
                                        </div>
                                    </div>
                                </div>

                                <!-- شارات التفاعل والمشاركة -->
                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold border {{ $ev->spoke_voice ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-slate-50 text-slate-400 border-slate-200' }}">
                                        <span>🎙️</span>
                                        <span>مشاركة صوتية: {{ $ev->spoke_voice ? 'نعم ✅' : 'لم يشارك' }}</span>
                                    </span>

                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold border {{ $ev->wrote_chat ? 'bg-indigo-50 text-indigo-800 border-indigo-200' : 'bg-slate-50 text-slate-400 border-slate-200' }}">
                                        <span>💬</span>
                                        <span>محادثة كتابية: {{ $ev->wrote_chat ? 'نعم ✅' : 'لم يشارك' }}</span>
                                    </span>

                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold border {{ $ev->submitted_task ? 'bg-purple-50 text-purple-800 border-purple-200' : 'bg-slate-50 text-slate-400 border-slate-200' }}">
                                        <span>📋</span>
                                        <span>تسليم التكليف: {{ $ev->submitted_task ? 'مكتمل ✅' : 'قيد الانتظار' }}</span>
                                    </span>

                                    @if($ev->daily_total_score)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-amber-50 text-amber-900 border border-amber-200 mr-auto font-mono">
                                            <span>الدرجة اليومية:</span>
                                            <strong>{{ $ev->daily_total_score }}/100</strong>
                                        </span>
                                    @endif
                                </div>

                                @if($ev->teacher_notes)
                                    <div class="p-3 bg-amber-50/60 rounded-xl border border-amber-200/80 text-xs text-amber-950">
                                        <strong class="block text-amber-900 mb-0.5">ملاحظات وتشجيع المعلم:</strong>
                                        <span>{{ $ev->teacher_notes }}</span>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-10 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs space-y-2">
                                <span class="text-3xl block">📋</span>
                                <p class="font-bold text-slate-700">لا توجد تقييمات يومية مرصودة لهذا الطالب حتى الآن.</p>
                                <p class="text-[11px] text-slate-400">ستظهر التقييمات والنجوم والملاحظات اليومية هنا فور بدء المعلم برصد جلسات المجموعة.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        <!-- 2. تبويب سجل الحضور والغياب -->
        @if(auth()->user()->hasPermission('parent.attendance_view'))
            <div id="tab-attendance" class="parent-tab-content space-y-6 {{ $defaultTab === 'attendance' ? '' : 'hidden' }}">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                                <span>📅</span>
                                <span>تقرير الحضور والغياب والانضباط الأكاديمي</span>
                            </h3>
                            <p class="text-xs text-slate-500">إحصائية شاملة لنسب حضور وغياب وتأخير الطالب في الحصص المقررة.</p>
                        </div>
                        <span class="text-xs font-bold text-slate-400 font-mono">{{ $totalSessions }} حصة / جلسة مسجلة</span>
                    </div>

                    <!-- شريط التقدم لنسبة الحضور -->
                    <div class="bg-slate-50 border border-slate-200 p-5 rounded-2xl space-y-3">
                        <div class="flex justify-between items-center text-xs font-black">
                            <span class="text-slate-800">معدل الانضباط وحضور الحصص:</span>
                            <span class="text-emerald-700 font-mono text-base">{{ $attendanceRate }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-3.5 overflow-hidden shadow-inner">
                            <div class="bg-emerald-500 h-3.5 rounded-full transition-all duration-500" style="width: {{ $attendanceRate }}%"></div>
                        </div>
                    </div>

                    <!-- بطاقات إحصائية للأعذار والغياب -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
                            <span class="text-[11px] font-bold text-emerald-800 block mb-1">جلسات الحضور</span>
                            <strong class="text-2xl font-black text-emerald-700 font-mono">{{ $presentCount }}</strong>
                            <span class="text-[10px] text-emerald-600 block mt-0.5">حضور فعلي 🟢</span>
                        </div>

                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                            <span class="text-[11px] font-bold text-amber-800 block mb-1">مرات التأخير</span>
                            <strong class="text-2xl font-black text-amber-700 font-mono">{{ $lateCount }}</strong>
                            <span class="text-[10px] text-amber-600 block mt-0.5">تأخر عن البداية 🟡</span>
                        </div>

                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                            <span class="text-[11px] font-bold text-slate-800 block mb-1">غياب بعذر مقبول</span>
                            <strong class="text-2xl font-black text-slate-700 font-mono">{{ $excusedCount }}</strong>
                            <span class="text-[10px] text-slate-600 block mt-0.5">عذر مسبق ⚪</span>
                        </div>

                        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl">
                            <span class="text-[11px] font-bold text-rose-800 block mb-1">غياب بدون عذر</span>
                            <strong class="text-2xl font-black text-rose-700 font-mono">{{ $absentCount }}</strong>
                            <span class="text-[10px] text-rose-600 block mt-0.5">غياب غير مبرر 🔴</span>
                        </div>
                    </div>

                    <!-- كشف الجلسات الفردية -->
                    <div class="space-y-2">
                        <h4 class="font-black text-xs text-slate-900 mb-2">سجل الحصص المنعقدة:</h4>
                        @forelse($evaluations as $ev)
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-base">📅</span>
                                    <div>
                                        <strong class="text-slate-900 block">{{ $ev->group?->name ?? 'شعبة دراسية' }}</strong>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $ev->session?->session_date ? $ev->session->session_date->format('Y-m-d') : $ev->created_at->format('Y-m-d') }} | المعلم: {{ $ev->session?->teacher?->name }}</span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $ev->attendance_badge_class }}">
                                    {{ $ev->attendance_label }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-4">لا توجد حصص مسجلة حتى الآن.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        <!-- 3. تبويب الواجبات والأنشطة المدرسية -->
        @if(auth()->user()->hasPermission('parent.activities_view'))
            <div id="tab-activities" class="parent-tab-content space-y-6 {{ $defaultTab === 'activities' ? '' : 'hidden' }}">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-sm space-y-5">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                                <span>📝</span>
                                <span>التكليفات والواجبات المدرسية وأوراق العمل</span>
                            </h3>
                            <p class="text-xs text-slate-500">الاطلاع على الواجبات المنشورة للطالب، الملاحظات التوجيهية، وتحميل ملفات العمل.</p>
                        </div>
                        <span class="text-xs font-bold text-slate-400 font-mono">{{ $groupActivities->count() }} تكليف ونشاط</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($groupActivities as $act)
                            <div class="border border-slate-200 rounded-2xl p-5 bg-white hover:border-indigo-300 transition space-y-3">
                                <div class="flex justify-between items-start gap-2">
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 flex items-center justify-center text-xl shadow-2xs">
                                            {{ $act->type_icon }}
                                        </span>
                                        <div>
                                            <strong class="text-xs text-slate-900 block">{{ $act->title }}</strong>
                                            <span class="text-[10px] text-slate-400 block font-mono">{{ $act->created_at->format('Y-m-d') }} | بواسطة: {{ $act->publisher?->name }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $act->type_label }}
                                    </span>
                                </div>

                                @if($act->instructions)
                                    <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed">
                                        {{ $act->instructions }}
                                    </p>
                                @endif

                                <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                                    @if($act->due_date)
                                        <span class="text-[11px] text-rose-600 font-bold">
                                            ⏰ موعد التسليم: {{ $act->due_date->format('Y-m-d') }}
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400">بدون موعد تسليم محدد</span>
                                    @endif

                                    @if($act->file_path)
                                        <a href="{{ asset($act->file_path) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-xl border border-indigo-200 text-[11px] transition shadow-2xs">
                                            <span>📥</span>
                                            <span>تحميل المرفق</span>
                                            @if($act->file_size)
                                                <span class="font-mono text-[9px] text-indigo-500">({{ $act->file_size }})</span>
                                            @endif
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full p-10 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs space-y-2">
                                <span class="text-3xl block">📚</span>
                                <p class="font-bold text-slate-700">لا توجد واجبات أو تكليفات منشورة للشعبة حالياً.</p>
                                <p class="text-[11px] text-slate-400">ستظهر أوراق العمل والتسجيلات والواجبات هنا فور نشرها من قبل المعلم.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        <!-- 4. تبويب نتائج الاختبارات والكويزات -->
        @if(auth()->user()->hasPermission('parent.exams_view'))
            <div id="tab-exams" class="parent-tab-content space-y-6 {{ $defaultTab === 'exams' ? '' : 'hidden' }}">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-sm space-y-5">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                                <span>🏆</span>
                                <span>نتائج الاختبارات والكويزات الدورية وتفاصيل الدرجات</span>
                            </h3>
                            <p class="text-xs text-slate-500">استعراض كشوف الدرجات، الكويزات القصيرة، والامتحانات النصفية والنهائية للدبلوم.</p>
                        </div>
                        <span class="text-xs font-bold text-slate-400 font-mono">{{ $examAttempts->count() }} محاولة اختبار</span>
                    </div>

                    <div class="space-y-3">
                        @forelse($examAttempts as $att)
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 p-4 sm:p-5 bg-slate-50 border border-slate-200 rounded-2xl hover:border-purple-300 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center text-2xl shadow-2xs shrink-0">
                                        🎯
                                    </div>
                                    <div>
                                        <strong class="text-xs text-slate-900 block">{{ $att->exam?->title ?? 'كويز / اختبار أكاديمي' }}</strong>
                                        <span class="text-[11px] text-slate-500 block">
                                            {{ $att->exam?->course?->title ?? 'دبلوم اللغات' }} • {{ $att->exam?->level?->name ?? 'مستوى دراسي' }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-mono">تاريخ الأداء: {{ $att->created_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 self-end sm:self-center">
                                    <div class="text-left font-mono">
                                        <strong class="text-sm font-black text-slate-900 block">{{ $att->score }} / {{ $att->total_marks ?? ($att->exam?->total_marks ?? 100) }}</strong>
                                        <span class="text-[10px] text-slate-500 font-bold">النسبة: {{ $att->percentage ?? 0 }}%</span>
                                    </div>

                                    @if(($att->percentage ?? 0) >= 60)
                                        <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            ناجح 🟢
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-300">
                                            يحتاج مراجعة 🟡
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-10 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs space-y-2">
                                <span class="text-3xl block">🎯</span>
                                <p class="font-bold text-slate-700">لم يؤدِّ هذا الطالب أي اختبارات أو كويزات حتى الآن.</p>
                                <p class="text-[11px] text-slate-400">ستظهر نتائج وتفاصيل تصحيح الاختبارات هنا فور نشرها واعتمادها.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        <!-- 5. تبويب الرسوم المالية وسندات القبض -->
        @if(auth()->user()->hasPermission('parent.finance_view'))
            <div id="tab-finance" class="parent-tab-content space-y-6 {{ $defaultTab === 'finance' ? '' : 'hidden' }}">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                                <span>🧾</span>
                                <span>الموقف المالي، الأقساط المجدولة، وسندات القبض الرسمية</span>
                            </h3>
                            <p class="text-xs text-slate-500">متابعة رسوم المستويات، المبالغ المسددة، وتنزيل أو طباعة سندات القبض المعتمدة.</p>
                        </div>
                        <a href="{{ route('accounting.statement', $activeChild->id) }}" target="_blank"
                            class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5 whitespace-nowrap">
                            <span>📄</span>
                            <span>كشف الحساب المالي الكامل</span>
                        </a>
                    </div>

                    <!-- ملخص الحساب المالي -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                            <span class="text-[11px] font-bold text-slate-500 block mb-1">إجمالي الرسوم الدراسية</span>
                            <strong class="text-xl font-black text-slate-900 font-mono">{{ number_format($totalTuition, 2) }}</strong>
                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $account->currency ?? 'YER' }}</span>
                        </div>

                        <div class="p-4 bg-purple-50 border border-purple-200 rounded-2xl">
                            <span class="text-[11px] font-bold text-purple-800 block mb-1">الخصومات والمنح</span>
                            <strong class="text-xl font-black text-purple-700 font-mono">{{ number_format($totalDiscounts, 2) }}</strong>
                            <span class="text-[10px] text-purple-600 block mt-0.5">{{ $account->currency ?? 'YER' }}</span>
                        </div>

                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
                            <span class="text-[11px] font-bold text-emerald-800 block mb-1">المسدد بسندات قبض</span>
                            <strong class="text-xl font-black text-emerald-700 font-mono">{{ number_format($totalPaid, 2) }}</strong>
                            <span class="text-[10px] text-emerald-600 block mt-0.5">{{ $account->currency ?? 'YER' }}</span>
                        </div>

                        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl">
                            <span class="text-[11px] font-bold text-rose-800 block mb-1">الرصيد المالي المتبقي</span>
                            <strong class="text-xl font-black text-rose-700 font-mono">{{ number_format($currentBalance, 2) }}</strong>
                            <span class="text-[10px] text-rose-600 block mt-0.5">{{ $account->currency ?? 'YER' }}</span>
                        </div>
                    </div>

                    <!-- سندات القبض المعتمدة -->
                    <div class="space-y-3">
                        <h4 class="font-black text-xs text-slate-900 flex items-center gap-1.5">
                            <span>🧾</span>
                            <span>سندات القبض المالية الصادرة:</span>
                        </h4>

                        @forelse($vouchers as $v)
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs hover:border-emerald-300 transition">
                                <div class="flex items-center gap-3">
                                    <span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 font-black flex items-center justify-center shrink-0">
                                        {{ $v->voucher_type === 'receipt' ? '📥' : '📤' }}
                                    </span>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <strong class="font-mono text-indigo-700 font-bold">{{ $v->voucher_no ?? ('VOU-' . $v->id) }}</strong>
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $v->created_at->format('Y-m-d') }}</span>
                                        </div>
                                        <span class="text-[11px] text-slate-600 block">{{ $v->statement ?? 'سداد رسوم دراسية' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 self-end sm:self-center">
                                    <strong class="font-mono text-emerald-600 text-sm font-black">{{ number_format($v->amount, 2) }} {{ $v->currency }}</strong>
                                    <a href="{{ route('accounting.voucher.print', $v->id) }}" target="_blank"
                                        class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-xl border border-slate-300 text-[11px] transition shadow-2xs flex items-center gap-1">
                                        <span>🖨️</span>
                                        <span>طباعة السند</span>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs">
                                لا توجد سندات قبض مسجلة لهذا الحساب بعد.
                            </div>
                        @endforelse
                    </div>

                    <!-- جدول الأقساط المجدولة -->
                    @if($installments->isNotEmpty())
                        <div class="space-y-3 pt-3 border-t border-slate-100">
                            <h4 class="font-black text-xs text-slate-900 flex items-center gap-1.5">
                                <span>📅</span>
                                <span>الأقساط الدراسية المجدولة:</span>
                            </h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-right text-xs">
                                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                                        <tr>
                                            <th class="p-3"># القسط</th>
                                            <th class="p-3">المستوى / الدورة</th>
                                            <th class="p-3">المبلغ</th>
                                            <th class="p-3">الخصم</th>
                                            <th class="p-3">تاريخ الاستحقاق</th>
                                            <th class="p-3">الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($installments as $inst)
                                            <tr>
                                                <td class="p-3 font-mono font-bold">القسط {{ $inst->installment_number }}</td>
                                                <td class="p-3">{{ $inst->level?->name ?? 'مستوى دراسي' }}</td>
                                                <td class="p-3 font-mono font-bold">{{ number_format($inst->amount, 2) }}</td>
                                                <td class="p-3 font-mono text-purple-700">{{ number_format($inst->discount_amount, 2) }}</td>
                                                <td class="p-3 font-mono text-slate-500">{{ $inst->due_date ? $inst->due_date->format('Y-m-d') : 'مستحق' }}</td>
                                                <td class="p-3">
                                                    @if($inst->status === 'paid')
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">مسدد بالكامل ✅</span>
                                                    @else
                                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">قيد السداد ⏳</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- 6. تبويب مسار الطالب والسجل الشامل 360 -->
        @if(auth()->user()->hasPermission('parent.transcript_view'))
            <div id="tab-transcript" class="parent-tab-content space-y-6 {{ $defaultTab === 'transcript' ? '' : 'hidden' }}">
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gradient-to-r from-indigo-900 to-slate-900 text-white p-6 rounded-2xl border border-indigo-800 shadow-md">
                        <div class="space-y-1">
                            <span class="text-xs text-amber-400 font-bold block">🎓 تقرير المسار الأكاديمي الشامل 360</span>
                            <h3 class="text-lg font-black">السجل الأكاديمي والمالي الموحد للطالب من أول يوم حتى التخرج</h3>
                            <p class="text-xs text-slate-300">سجل رسمي معتمد ومطبوع يشمل كافة المستويات التي اجتازها الطالب، نسب النجاح، والحركة المالية.</p>
                        </div>
                        <a href="{{ route('admin.reports.student.transcript', $activeChild->id) }}" target="_blank"
                            class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs px-6 py-3 rounded-xl shadow-lg transition flex items-center gap-2 whitespace-nowrap self-stretch sm:self-auto justify-center">
                            <span>🖨️</span>
                            <span>استعراض وطباعة السجل الرسمي 360</span>
                        </a>
                    </div>

                    <!-- ملخص رحلة ومسار الطالب -->
                    <div class="space-y-4">
                        <h4 class="font-black text-xs text-slate-900 flex items-center gap-2">
                            <span>🚀</span>
                            <span>المستويات والدورات المسجلة عبر مسار الطالب:</span>
                        </h4>

                        <div class="space-y-3">
                            @forelse($activeChild->enrollments as $enr)
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs hover:border-indigo-300 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 font-black flex items-center justify-center text-sm shadow-2xs">
                                            📚
                                        </div>
                                        <div>
                                            <strong class="text-xs text-slate-900 block">{{ $enr->level?->course?->title ?? 'الدورة التعليمية' }}</strong>
                                            <span class="text-[11px] text-indigo-700 font-bold block">المستوى: {{ $enr->level?->name ?? 'مستوى دراسي' }}</span>
                                            <span class="text-[10px] text-slate-400 font-mono">تاريخ التسجيل: {{ $enr->created_at->format('Y-m-d') }}</span>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $enr->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-indigo-100 text-indigo-800' }}">
                                        {{ $enr->status === 'completed' ? 'تم اجتياز المستوى بنجاح 🎓' : 'قيد الدراسة والمتابعة ⏳' }}
                                    </span>
                                </div>
                            @empty
                                <div class="p-6 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs">
                                    لا توجد تسجيلات مستويات سابقة لهذا الطالب.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- 7. تبويب جدول الحصص والبث المباشر -->
        @if(auth()->user()->hasPermission('parent.live_schedule'))
            <div id="tab-schedule" class="parent-tab-content space-y-6 {{ $defaultTab === 'schedule' ? '' : 'hidden' }}">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- العمود الأيمن (2/3): جدول المجموعات وحصص Air Class -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4">
                            <h3 class="font-black text-sm text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                                <span>📡</span>
                                <span>مواعيد الحصص الأسبوعية وغرفة البث المباشر (Air Class)</span>
                            </h3>

                            @forelse($studyGroups as $grp)
                                <div class="border border-slate-200 p-4 sm:p-5 rounded-2xl space-y-3 bg-slate-50/50">
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                        <div>
                                            <strong class="text-xs text-indigo-900 block font-black">{{ $grp->name }}</strong>
                                            <span class="text-[11px] text-slate-500 block">
                                                المعلم الرئيسي: <strong>{{ $grp->teacher?->name ?? 'غير محدد' }}</strong>
                                            </span>
                                            <span class="text-[10px] text-slate-400 block font-mono">
                                                المواعيد: {{ $grp->schedule_days ?? 'الأيام المعتمدة' }} | {{ $grp->schedule_time ?? 'التوقيت اليومي' }}
                                            </span>
                                        </div>

                                        @if($grp->is_live)
                                            <a href="{{ route('classroom.show', $grp->live_room_id ?? 'group-' . $grp->id) }}" target="_blank"
                                                class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs px-5 py-2.5 rounded-xl shadow-md transition flex items-center gap-1.5 whitespace-nowrap animate-pulse">
                                                <span>🔴</span>
                                                <span>دخول البث المباشر مع الطالب 🎙️</span>
                                            </a>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700">
                                                البث المباشر متوقف حالياً ⏸️
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs">
                                    هذا الطالب غير مسكن في أي مجموعة دراسية حالياً.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- العمود الأيسر (1/3): الحجز السريع 1-on-1 -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4">
                            <h3 class="font-black text-sm text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                                <span>🎙️</span>
                                <span>حجز حصة فردية 1-on-1 إضافية</span>
                            </h3>

                            <div class="space-y-3">
                                @forelse($availableSchedules->take(4) as $sch)
                                    <div class="p-3 rounded-xl border border-slate-200 hover:border-indigo-400 transition bg-slate-50/50 space-y-2">
                                        <div class="flex justify-between items-center text-xs">
                                            <strong class="text-slate-900">{{ $sch->teacher?->name }}</strong>
                                            <span class="text-amber-600 font-bold text-[11px]">25 دقيقة</span>
                                        </div>
                                        <div class="text-[11px] text-slate-500 flex justify-between">
                                            <span>📅 {{ $sch->session_date->format('Y-m-d') }}</span>
                                            <span>⏰ {{ $sch->start_time }}</span>
                                        </div>

                                        <form action="{{ route('parent.child.book', $activeChild->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="schedule_id" value="{{ $sch->id }}">
                                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2 rounded-xl transition shadow-sm">
                                                تأكيد الحجز لهذا الطفل 🎟️
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 text-center py-4">لا توجد خانات متاحة حالياً.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @else
        <!-- في حال لم يكن هناك أي طفل مسجل أو محدد -->
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-xl mx-auto space-y-4 shadow-sm">
            <span class="text-5xl block">👶</span>
            <h3 class="font-black text-lg text-slate-900">لا يوجد أطفال أو طلاب مرتبطون بحسابك حالياً</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                يرجى الضغط على زر "إضافة طفل جديد" لإضافة بيانات طفلك والاستفادة من الحصة التجريبية المجانية، أو طلب ربط حساب أبنائك المسجلين بالمدرسة من إدارة القبول والتسجيل.
            </p>
            <button type="button" onclick="document.getElementById('modalAddChild').classList.remove('hidden')" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-6 py-3 rounded-2xl shadow-md transition inline-flex items-center gap-2">
                <span>➕</span>
                <span>إضافة طفل جديد الآن</span>
            </button>
        </div>
    @endif

</div>

<!-- مودال تفاعلي لإضافة طفل جديد تحت حساب ولي الأمر -->
<div id="modalAddChild" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 p-8 max-w-md w-full shadow-2xl space-y-6">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-black text-base text-slate-900">إضافة طفل جديد للحساب</h3>
            <button type="button" onclick="document.getElementById('modalAddChild').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>

        <form action="{{ route('parent.child.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">اسم الطفل *</label>
                <input type="text" name="name" required placeholder="محمد شائف عبدالواحد علي" 
                    class="w-full border border-slate-300 rounded-xl p-3 outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">المرحلة الدراسية *</label>
                <select name="stage" class="w-full border border-slate-300 rounded-xl p-3 outline-none font-bold">
                    <option value="kindergarten">طفل روضة (3 - 5 سنوات)</option>
                    <option value="primary" selected>طالب ابتدائي (6 - 11 سنة)</option>
                    <option value="middle_school">طالب أساسي / متوسط (12 - 14 سنة)</option>
                    <option value="high_school">طالب ثانوي (15 - 17 سنة)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">الصف الدراسي أو المرحلة الفرعية</label>
                <input type="text" name="grade_substage" placeholder="مثال: الصف الرابع الابتدائي" 
                    class="w-full border border-slate-300 rounded-xl p-3 outline-none">
            </div>

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900">
                🎁 سيتم منح الطفل فوراً <strong>حصة تجريبية مجانية 1-on-1</strong> وفتح حسابه المالي تلقائياً.
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="document.getElementById('modalAddChild').classList.add('hidden')" class="px-4 py-2 text-slate-500 font-bold">إلغاء</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition">حفظ وإضافة الطفل</button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchParentTab(tabName) {
        document.querySelectorAll('.parent-tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.parent-tab-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-md');
            btn.classList.add('bg-white', 'text-slate-700', 'hover:bg-slate-100');
        });
        
        const target = document.getElementById('tab-' + tabName);
        const targetBtn = document.getElementById('btn-tab-' + tabName);
        if (target) target.classList.remove('hidden');
        if (targetBtn) {
            targetBtn.classList.remove('bg-white', 'text-slate-700', 'hover:bg-slate-100');
            targetBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-md');
        }
    }
</script>
@endsection
