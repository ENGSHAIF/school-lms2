@extends('layouts.app', ['title' => 'إدارة المجموعات والدفعات الدراسية (Study Groups)'])

@section('content')
<div class="space-y-6">

    <!-- شريط الترويسة الرئيسي والأزرار السريعة -->
    <div class="bg-gradient-to-r from-indigo-950 via-slate-900 to-indigo-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border border-indigo-800/40">
        <div>
            <div class="flex items-center gap-2 text-xs text-amber-400 font-bold mb-2">
                <span>📚</span>
                <span>نظام إدارة المجموعات والفصول الجماعية (Cohorts & Batches)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">المجموعات الدراسية وتسكين الطلاب</h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                أنشئ مجموعات دراسية موحدة، عيّن لكل مجموعة الأستاذ والمشرف المناسب، وسكّن الطلاب الجدد بضغطة زر واحدة مع ربطهم بالفصول الافتراضية المباشرة.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.groups.evaluations.index') }}" class="px-4 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs sm:text-sm shadow-lg shadow-indigo-500/20 transition flex items-center gap-2">
                <span>📋</span>
                <span>سجل التقييمات والحضور</span>
            </a>
            <button onclick="openCreateGroupModal()" class="px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs sm:text-sm shadow-lg shadow-amber-500/20 transition flex items-center gap-2">
                <span class="text-base">➕</span>
                <span>إنشاء مجموعة جديدة</span>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/10 flex items-center gap-2">
                <span>← لوحة الإدارة</span>
            </a>
        </div>
    </div>

    <!-- رسائل النجاح والأخطاء -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-lg">✅</span>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-lg">⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
    @endif

    <!-- تنبيه إداري عاجل بطلبات فتح البث الحي المعلقة بانتظار الاعتماد -->
    @if(isset($pendingLiveRequests) && $pendingLiveRequests->isNotEmpty())
        <div class="bg-gradient-to-r from-amber-500 via-amber-600 to-amber-700 rounded-3xl p-6 text-slate-950 shadow-xl border border-amber-300 space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-slate-950 text-amber-400 flex items-center justify-center text-2xl font-black shrink-0 animate-bounce">
                        📡
                    </span>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-950">تنبيه عاجل: يوجد ({{ $pendingLiveRequests->count() }}) طلبات فتح بث حي بانتظار الاعتماد</h3>
                        <p class="text-xs text-slate-900 font-bold">هناك طلاب بانتظار فتح البث التفاعلي المباشر لدخول الفصل الآن. يرجى الاعتماد أو الرفض.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($pendingLiveRequests as $req)
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 border border-amber-200 flex flex-col justify-between gap-3 shadow-md">
                        <div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-black text-indigo-950 text-sm">{{ $req->name }}</span>
                                <span class="text-[10px] bg-amber-100 text-amber-900 px-2 py-0.5 rounded font-mono font-bold">{{ $req->live_requested_at?->diffForHumans() ?? 'الآن' }}</span>
                            </div>
                            <div class="text-[11px] text-slate-600 mt-1">
                                🎒 الطالب مقدم الطلب: <strong>{{ $req->requester?->name ?? 'طالب بالمجموعة' }}</strong>
                                @if($req->teacher)
                                    • 👨‍🏫 الأستاذ: {{ $req->teacher->name }}
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                            <form method="POST" action="{{ route('groups.live.start', $req->id) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5">
                                    <span>✅</span>
                                    <span>اعتماد وفتح البث للطلاب الآن</span>
                                </button>
                            </form>

                            <form method="POST" action="{{ route('groups.live.reject', $req->id) }}">
                                @csrf
                                <button type="submit" class="py-2 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition">
                                    ✕ رفض
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- بطاقات الإحصائيات الأربعة السريعة -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-black">
                👥
            </div>
            <div>
                <span class="text-xs text-slate-400 block font-bold">المجموعات النشطة</span>
                <span class="text-2xl font-black text-slate-900 font-mono">{{ $activeGroups }}</span>
                <span class="text-[10px] text-slate-400 block">من إجمالي {{ $totalGroups }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-black">
                🎓
            </div>
            <div>
                <span class="text-xs text-slate-400 block font-bold">الطلاب المسكنين</span>
                <span class="text-2xl font-black text-emerald-600 font-mono">{{ $totalEnrolledStudents }}</span>
                <span class="text-[10px] text-slate-400 block">طالب في مجموعات دراسية</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-black">
                👨‍🏫
            </div>
            <div>
                <span class="text-xs text-slate-400 block font-bold">الأساتذة المكلفون</span>
                <span class="text-2xl font-black text-amber-600 font-mono">{{ $totalTeachersAssigned }}</span>
                <span class="text-[10px] text-slate-400 block">معلماً يديرون مجموعات</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-black">
                🚪
            </div>
            <div>
                <span class="text-xs text-slate-400 block font-bold">الفصول الحية</span>
                <span class="text-2xl font-black text-purple-600 font-mono">{{ $groups->count() }}</span>
                <span class="text-[10px] text-slate-400 block">غرف تفاعلية جاهزة</span>
            </div>
        </div>
    </div>

    <!-- شريط البحث والفلترة -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
        <form method="GET" action="{{ route('admin.groups.index') }}" class="w-full flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المجموعة، الأستاذ، أو اليوم..."
                    class="w-full pr-10 pl-4 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <span class="absolute right-3.5 top-3 text-slate-400 text-sm">🔍</span>
            </div>

            <select name="term" onchange="this.form.submit()" class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-white focus:outline-none">
                <option value="">جميع الأترام</option>
                @foreach($allTerms as $t)
                    <option value="{{ $t }}" {{ request('term') == $t ? 'selected' : '' }}>🗓️ {{ $t }}</option>
                @endforeach
            </select>

            <select name="teacher_id" onchange="this.form.submit()" class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-white focus:outline-none">
                <option value="">جميع المعلمين</option>
                @foreach($teachers as $t)
                    <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>👨‍🏫 {{ $t->name }}</option>
                @endforeach
            </select>

            <select name="course_id" onchange="this.form.submit()" class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-white focus:outline-none">
                <option value="">جميع الدورات</option>
                @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-700 bg-white focus:outline-none">
                <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>المجموعات النشطة فقط</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>المكتملة</option>
                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>المؤرشفة</option>
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>عرض الكل</option>
            </select>

            <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                تطبيق
            </button>
            <a href="{{ route('admin.reports.term.print', ['term' => request('term')]) }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs transition flex items-center justify-center gap-1.5 shadow-sm whitespace-nowrap">
                <span>🖨️</span>
                <span>طباعة طلاب وكشف الترم</span>
            </a>
            @if(request()->hasAny(['search', 'term', 'teacher_id', 'course_id', 'status']))
                <a href="{{ route('admin.groups.index') }}" class="px-3 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs hover:bg-slate-200 transition text-center whitespace-nowrap">
                    إلغاء الفلتر
                </a>
            @endif
        </form>
    </div>

    <!-- شبكة بطاقات المجموعات الدراسية (Group Cards Grid) -->
    @if($groups->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center shadow-sm space-y-4">
            <span class="text-5xl block">📚</span>
            <h3 class="text-lg font-black text-slate-800">لا توجد مجموعات دراسية حتى الآن</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                يمكنك إنشاء أول مجموعة دراسية للطلاب الـ 5 واختيار الأستاذ والدورة ومواعيد الحصص بضغطة زر واحدة.
            </p>
            <button onclick="openCreateGroupModal()" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                ➕ إنشاء أول مجموعة دراسية
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($groups as $grp)
                @php
                    $activeCount = $grp->active_count;
                    $percent = $grp->max_students > 0 ? round(($activeCount / $grp->max_students) * 100) : 0;
                    $isFull = $grp->isFull();
                @endphp
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden">
                    
                    <!-- ترويسة بطاقة المجموعة -->
                    <div class="p-5 border-b border-slate-100 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $grp->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($grp->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $grp->status === 'active' ? '● نشطة' : ($grp->status === 'completed' ? 'مكتملة' : 'مؤرشفة') }}
                                    </span>
                                    @if($grp->is_live)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white animate-pulse">
                                            <span>● البث مفتوح للطلاب</span>
                                        </span>
                                    @elseif($grp->isPendingApproval())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-slate-950 animate-bounce">
                                            <span>⏳ بانتظار الاعتماد</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span>🔴 البث مغلق</span>
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-base font-black text-slate-900 mt-1.5 leading-snug">
                                    {{ $grp->name_ar ?: $grp->name }}
                                    @if($grp->name_en)
                                        <span class="block text-xs font-mono font-bold text-indigo-600 mt-0.5" dir="ltr">{{ $grp->name_en }}</span>
                                    @endif
                                </h3>
                                
                                <!-- بادجات الترم والدفعة والبرنامج والمدة -->
                                <div class="flex items-center gap-1.5 flex-wrap mt-2">
                                    @if($grp->term)
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-black bg-indigo-50 text-indigo-800 border border-indigo-200">
                                            🗓️ {{ $grp->term }}
                                        </span>
                                    @endif
                                    @if($grp->batch)
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            🏷️ {{ $grp->batch }}
                                        </span>
                                    @endif
                                    @if($grp->program_type)
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                            📜 {{ $grp->program_type }}
                                        </span>
                                    @endif
                                    @if($grp->duration_months)
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 font-mono">
                                            ⏳ {{ $grp->duration_months }} أشهر
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- قائمة خيارات إدارية -->
                            <div class="flex items-center gap-1">
                                <button onclick='openEditGroupModal(@json($grp))' title="تعديل بيانات المجموعة وتغيير الأستاذ" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition">
                                    ✏️
                                </button>
                                <form method="POST" action="{{ route('admin.groups.destroy', $grp->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه المجموعة؟ لن يتم حذف الطلاب، بل فك قيدهم فقط.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="حذف المجموعة" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center text-xs transition">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- الدورة والمستوى -->
                        <div class="flex items-center gap-2 text-xs text-indigo-700 font-bold bg-indigo-50/70 p-2.5 rounded-xl border border-indigo-100">
                            <span>📖</span>
                            <span>{{ $grp->course?->name ?? 'دورات اللغة' }}</span>
                            @if($grp->level)
                                <span>•</span>
                                <span class="text-indigo-900 font-mono">[{{ $grp->level->name }}]</span>
                            @endif
                        </div>
                    </div>

                    <!-- جسم البطاقة: الأستاذ، المشرف، المواعيد، ونسبة الاستيعاب -->
                    <div class="p-5 space-y-4 text-xs">
                        <!-- الأستاذ المسؤول (الرئيسي والمساعدين) -->
                        <div class="flex flex-col py-1 border-b border-slate-50 gap-1">
                            <div class="flex items-center justify-between flex-wrap gap-1">
                                <span class="text-slate-400 font-bold flex items-center gap-1.5">
                                    <span>⭐</span>
                                    <span>المعلم الرئيسي:</span>
                                </span>
                                <div class="flex items-center gap-1.5">
                                    @php $pTeacher = $grp->primaryTeacher() ?: $grp->teacher; @endphp
                                    <span class="font-bold text-slate-900">
                                        {{ $pTeacher?->name ?? 'لم يُحدد بعد' }}
                                    </span>
                                    @if($pTeacher)
                                        <a href="{{ route('accounting.dashboard') }}#entitlementsTab" 
                                           class="text-[10px] text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-1.5 py-0.5 rounded border border-indigo-200 transition font-bold" 
                                           title="إثبات مستحقات مالية للمعلم عن هذه المجموعة">
                                            💼 مستحقات
                                        </a>
                                        <a href="{{ route('accounting.teacher.statement', $pTeacher->id) }}" target="_blank"
                                           class="text-[10px] text-slate-600 bg-slate-100 hover:bg-slate-200 px-1.5 py-0.5 rounded transition font-bold" 
                                           title="كشف حساب المعلم">
                                            📊 كشف
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @if($grp->assistantTeachers()->isNotEmpty())
                                <div class="flex flex-wrap items-center gap-1 mt-0.5">
                                    <span class="text-[10px] text-slate-400 font-bold">المساعدين:</span>
                                    @foreach($grp->assistantTeachers() as $asst)
                                        <span class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded text-[10px] font-bold border border-indigo-100">
                                            {{ $asst->name }} ({{ $asst->pivot->role_title ?: 'مساعد' }})
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- المشرف الأكاديمي -->
                        <div class="flex items-center justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-400 font-bold flex items-center gap-1.5">
                                <span>🛡️</span>
                                <span>المشرف الأكاديمي:</span>
                            </span>
                            <span class="font-bold text-slate-700">
                                {{ $grp->primarySupervisor()?->name ?: ($grp->supervisor?->name ?? 'إدارة المنصة') }}
                                @if($grp->assistantSupervisors()->isNotEmpty())
                                    <span class="text-[10px] text-slate-400 font-normal">({{ $grp->assistantSupervisors()->count() }} إضافي)</span>
                                @endif
                            </span>
                        </div>

                        <!-- المواعيد -->
                        <div class="flex items-center justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-400 font-bold flex items-center gap-1.5">
                                <span>⏰</span>
                                <span>المواعيد:</span>
                            </span>
                            <span class="font-bold text-slate-800 text-right">
                                {{ $grp->schedule_days ?: 'غير محدد' }}
                                @if($grp->schedule_time)
                                    <span class="text-[11px] text-amber-600 block font-mono" dir="ltr">{{ $grp->schedule_time }}</span>
                                @endif
                            </span>
                        </div>

                        <!-- شريط سعة الطلاب المسكنين -->
                        <div class="space-y-1.5 pt-1">
                            <div class="flex justify-between items-center text-[11px]">
                                <span class="font-bold text-slate-700">الطلاب المسكنين:</span>
                                <span class="font-mono font-bold text-slate-900">
                                    <strong class="text-indigo-600 text-sm">{{ $activeCount }}</strong> / {{ $grp->max_students }} طالب
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300 {{ $isFull ? 'bg-red-500' : ($percent > 70 ? 'bg-amber-500' : 'bg-indigo-600') }}" style="width: {{ min(100, $percent) }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-400">
                                <span>{{ $percent }}% مكتمل</span>
                                <span>المقاعد الشاغرة: {{ $grp->available_seats }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الإجراءات السريعة في الأسفل -->
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex flex-col gap-2">
                        <div class="grid grid-cols-2 gap-2">
                            <!-- زر تسكين الطلاب -->
                            <button onclick='openEnrollModal(@json($grp))' class="px-3 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5">
                                <span>👥</span>
                                <span>تسكين طلاب</span>
                            </button>

                            <!-- زر استعراض طلاب المجموعة -->
                            <button onclick='openViewStudentsModal(@json($grp), @json($grp->students))' class="px-3 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-800 border border-slate-200 font-bold text-xs transition flex items-center justify-center gap-1.5">
                                <span>📋</span>
                                <span>الطلاب ({{ $activeCount }})</span>
                            </button>
                        </div>

                        <!-- زر استدعاء ومشاركة من بنك موارد المستوى المركزي -->
                        @if($grp->level)
                            @php $levelResCount = $grp->level->resources ? $grp->level->resources->count() : 0; @endphp
                            <button onclick='openGroupLevelResourcesModal(@json($grp), @json($grp->level), @json($grp->level->resources), @json($grp->activities))' class="w-full py-2.5 rounded-xl bg-gradient-to-r from-indigo-50 to-purple-50 hover:from-indigo-100 hover:to-purple-100 text-indigo-950 border border-indigo-200/80 font-black text-xs shadow-2xs transition flex items-center justify-center gap-2">
                                <span>📦</span>
                                <span>استدعاء ومشاركة من بنك موارد المستوى</span>
                                <span class="bg-indigo-600 text-white text-[10px] px-2 py-0.5 rounded-full font-mono">{{ $levelResCount }} موارد</span>
                            </button>
                        @endif

                        <!-- زر أنشطة وصوتيات المجموعة (مثل الواتساب) -->
                        <button onclick='openActivitiesModal(@json($grp), @json($grp->activities))' class="w-full py-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-900 border border-purple-200 font-bold text-xs shadow-sm transition flex items-center justify-center gap-2">
                            <span>📎</span>
                            <span>أنشطة وصوتيات المجموعة ({{ $grp->activities->count() }})</span>
                        </button>

                        <!-- أزرار التقييم اليومي وسجل الحضور والدبلوم -->
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('admin.groups.evaluations.create', $grp->id) }}" class="py-2.5 px-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 font-bold text-xs transition flex items-center justify-center gap-1 shadow-2xs">
                                <span>➕</span>
                                <span>رصد تقييم اليوم</span>
                            </a>
                            <a href="{{ route('admin.groups.evaluations.index', ['group_id' => $grp->id]) }}" class="py-2.5 px-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 font-bold text-xs transition flex items-center justify-center gap-1 shadow-2xs">
                                <span>📊</span>
                                <span>سجل التقييم</span>
                            </a>
                        </div>

                        <!-- تحكم الإدارة في فتح وإغلاق واعتماد البث الحي -->
                        @if($grp->isPendingApproval())
                            <div class="bg-amber-50 border border-amber-200 p-2.5 rounded-2xl space-y-2">
                                <div class="text-[11px] text-amber-900 font-bold flex items-center justify-between">
                                    <span>⏳ طلب فتح بث من: {{ $grp->requester?->name ?? 'طالب بالمجموعة' }}</span>
                                    <span class="text-[10px] opacity-75 font-mono">{{ $grp->live_requested_at?->diffForHumans() }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <form method="POST" action="{{ route('groups.live.start', $grp->id) }}">
                                        @csrf
                                        <button type="submit" class="w-full py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1">
                                            <span>✅</span>
                                            <span>اعتماد وفتح</span>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('groups.live.reject', $grp->id) }}">
                                        @csrf
                                        <button type="submit" class="w-full py-2 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-800 font-bold text-xs transition flex items-center justify-center gap-1">
                                            <span>✕</span>
                                            <span>رفض الطلب</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @elseif($grp->is_live)
                            <form method="POST" action="{{ route('groups.live.stop', $grp->id) }}">
                                @csrf
                                <button type="submit" class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-2">
                                    <span>⏹️</span>
                                    <span>إغلاق وإنهاء البث الحي للطلاب</span>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('groups.live.start', $grp->id) }}">
                                @csrf
                                <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-2">
                                    <span>📡</span>
                                    <span>بدء وفتح البث الحي للطلاب الآن</span>
                                </button>
                            </form>
                        @endif

                        <!-- زر إطلاق ودخول الفصل الجماعي المباشر -->
                        <a href="{{ route('classroom.show', $grp->room_id) }}" target="_blank" class="w-full py-2.5 rounded-xl {{ $grp->is_live ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-slate-800 hover:bg-slate-700' }} text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-2">
                            <span>🎥</span>
                            <span>{{ $grp->is_live ? 'دخول الفصل الحي المباشر (Air Class)' : 'معاينة ودخول الفصل كمسؤول' }}</span>
                            <span class="text-[10px] {{ $grp->is_live ? 'bg-emerald-400 text-slate-950 font-black' : 'bg-slate-700 text-slate-300' }} px-1.5 py-0.5 rounded font-mono" dir="ltr">
                                {{ $grp->is_live ? 'Live' : 'Admin' }}
                            </span>
                        </a>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

</div>

<!-- ======================================================= -->
<!-- نافذة منبثقة: 1. إنشاء مجموعة جديدة (Create Group Modal) -->
<!-- ======================================================= -->
<div id="createGroupModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 overflow-y-auto">
    <div class="fixed inset-0 cursor-pointer" onclick="closeCreateGroupModal()"></div>
    <div class="relative bg-white rounded-2xl sm:rounded-3xl w-full max-w-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh] min-w-0 my-auto z-10">
        <!-- ترويسة -->
        <div class="px-4 py-3 sm:px-6 sm:py-4 bg-indigo-900 text-white flex justify-between items-center shrink-0 gap-2 min-w-0">
            <h3 class="font-black text-xs sm:text-sm flex items-center gap-2 truncate">
                <span class="shrink-0">➕</span>
                <span class="truncate">إنشاء مجموعة دراسية جديدة (Study Group & Cohort)</span>
            </h3>
            <button onclick="closeCreateGroupModal()" class="text-white/70 hover:text-white text-lg shrink-0 p-1">✕</button>
        </div>

        <!-- النموذج -->
        <form action="{{ route('admin.groups.store') }}" method="POST" class="p-4 sm:p-6 space-y-4 overflow-y-auto text-xs min-w-0">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">اسم المجموعة بالعربي <span class="text-rose-500">*</span></label>
                    <input type="text" name="name_ar" required placeholder="مثال: دورة اللغة الإنجليزية العامة - المستوى الأول A" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">اسم المجموعة بالإنجليزي (English)</label>
                    <input type="text" name="name_en" placeholder="مثال: General English - Level 1 Cohort A" dir="ltr" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-xs">
                </div>
            </div>

            <!-- تصنيف الدبلوم والترم والدفعة والمدة -->
            <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100 space-y-3">
                <span class="font-black text-indigo-950 block text-xs">🗓️ بيانات الدبلوم، الترم، والفترة الزمنية (للمقارنة والتقارير الإحصائية):</span>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الترم / الفصل الدراسي</label>
                        <input type="text" name="term" list="createTermsList" placeholder="مثال: الترم الأول 2026" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <datalist id="createTermsList">
                            <option value="الترم الأول 2026">
                            <option value="الترم الثاني 2026">
                            <option value="الترم الصيفي 2026">
                            @foreach($allTerms as $t)
                                <option value="{{ $t }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الدفعة / الـ Batch</label>
                        <input type="text" name="batch" placeholder="مثال: الدفعة الأولى، دفعة يناير" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">نوع البرنامج / الدبلوم</label>
                        <input type="text" name="program_type" placeholder="مثال: دبلوم لغة إنجليزية شامل، دورة محادثة" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">مدة البرنامج / الدبلوم بالأشهر</label>
                        <input type="number" name="duration_months" value="3" min="1" max="24" placeholder="3 أو 4 أو 6 أشهر" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">تاريخ بداية الدبلوم / الترم</label>
                        <input type="date" name="start_date" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">تاريخ نهاية الدبلوم المتوقعة</label>
                        <input type="date" name="end_date" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    </div>
                </div>
            </div>

            <!-- المستوى والدورة التعليمية -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">المستوى الأكاديمي الحاضن</label>
                    <select name="level_id" id="createLevelSelect" onchange="filterCoursesByLevel(this.value, 'createCourseSelect')" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="">-- كل المستويات الأكاديمية --</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl->id }}">{{ $lvl->name_en }} ({{ $lvl->name_ar }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الدورة التعليمية <span class="text-red-500">*</span></label>
                    <select name="course_id" id="createCourseSelect" required onchange="onCourseSelected(event, 'createLevelSelect')" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="">-- اختر الدورة التعليمية --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" data-level="{{ $c->level_id }}">{{ $c->name_en }} ({{ $c->hours }} ساعة) - [{{ $c->level?->name_en }}]</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- الكادر التعليمي: المعلم الرئيسي + معلمين مساعدين -->
            <div class="p-4 bg-amber-50/50 rounded-2xl border border-amber-200/80 space-y-3">
                <span class="font-black text-amber-950 block text-xs">👨‍🏫 الكادر التعليمي للمجموعة:</span>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">⭐ المعلم الرئيسي للمجموعة (Primary Teacher)</label>
                    <select name="teacher_id" class="w-full px-3.5 py-2.5 rounded-xl border border-amber-300 focus:ring-2 focus:ring-amber-500 outline-none bg-white font-bold">
                        <option value="">-- اختر المعلم الرئيسي --</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">👨‍🏫 {{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">إسناد معلمين مساعدين للمجموعة مع تحديد الدور (اختياري):</label>
                    <div class="max-h-36 overflow-y-auto space-y-1.5 p-1 bg-white rounded-xl border border-slate-200">
                        @foreach($teachers as $t)
                            <div class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded-lg">
                                <input type="checkbox" name="assistant_teachers[]" value="{{ $t->id }}" id="asst_create_{{ $t->id }}" class="rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <label for="asst_create_{{ $t->id }}" class="font-bold text-slate-800 flex-1 cursor-pointer">{{ $t->name }}</label>
                                <input type="text" name="assistant_roles[{{ $t->id }}]" placeholder="الدور: محادثة / استماع" class="w-36 px-2 py-1 text-[11px] rounded-lg border border-slate-200 focus:ring-1 focus:ring-indigo-500 outline-none">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- هيئة الإشراف: المشرف الرئيسي + مساعدين -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                <span class="font-black text-slate-900 block text-xs">🛡️ هيئة الإشراف والمتابعة الأكاديمية:</span>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">المشرف الأكاديمي الرئيسي (Supervisor)</label>
                    <select name="supervisor_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="">-- إدارة المنصة العامة --</option>
                        @foreach($supervisors as $s)
                            <option value="{{ $s->id }}">🛡️ {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">مشرفين مساعدين إضافيين للمجموعة (اختياري):</label>
                    <div class="max-h-28 overflow-y-auto space-y-1.5 p-1 bg-white rounded-xl border border-slate-200">
                        @foreach($supervisors as $s)
                            <label class="flex items-center gap-2 p-1.5 hover:bg-slate-50 rounded-lg cursor-pointer">
                                <input type="checkbox" name="assistant_supervisors[]" value="{{ $s->id }}" class="rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <span class="font-bold text-slate-700">🛡️ {{ $s->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- المواعيد والسعة -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">أيام الحصص</label>
                    <input type="text" name="schedule_days" placeholder="مثال: السبت، الإثنين، الأربعاء" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الحد الأقصى للطلاب</label>
                    <input type="number" name="max_students" value="15" min="1" max="100" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">توقيت الحصة</label>
                <input type="text" name="schedule_time" placeholder="مثال: 05:00 م - 06:00 م" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono" dir="ltr">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">ملاحظات أو وصف إضافي</label>
                <textarea name="description" rows="2" placeholder="أي تعليمات أو ملاحظات خاصة بهذه الدفعة..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="closeCreateGroupModal()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition text-center">
                    إلغاء
                </button>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition shadow-md text-center">
                    حفظ وإنشاء المجموعة ✅
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- نافذة منبثقة: 2. تسكين وقيد الطلاب دفعة واحدة (Enroll Students) -->
<!-- ======================================================= -->
<div id="enrollModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 overflow-y-auto">
    <div class="fixed inset-0 cursor-pointer" onclick="closeEnrollModal()"></div>
    <div class="relative bg-white rounded-2xl sm:rounded-3xl w-full max-w-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh] min-w-0 my-auto z-10">
        <!-- ترويسة -->
        <div class="px-4 py-3 sm:px-6 sm:py-4 bg-indigo-950 text-white flex justify-between items-center shrink-0 gap-2 min-w-0">
            <div class="min-w-0">
                <h3 class="font-black text-xs sm:text-sm flex items-center gap-2 truncate">
                    <span class="shrink-0">👥</span>
                    <span class="truncate">تسكين الطلاب في المجموعة: <span id="enrollGroupName" class="text-amber-400"></span></span>
                </h3>
                <span class="text-[10px] sm:text-[11px] text-slate-300 block truncate">حدد الطلاب المراد ضمهم واضغط تسكين فوراً</span>
            </div>
            <button onclick="closeEnrollModal()" class="text-white/70 hover:text-white text-lg shrink-0 p-1">✕</button>
        </div>

        <form id="enrollForm" method="POST" action="" class="flex flex-col flex-1 overflow-hidden min-w-0">
            @csrf
            
            <!-- شريط بحث سريع وتحديد الكل -->
            <div class="p-3 sm:p-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-2.5 sm:gap-3 shrink-0 min-w-0">
                <div class="relative w-full sm:w-72 min-w-0">
                    <input type="text" id="studentFilterInput" onkeyup="filterStudentList()" placeholder="ابحث باسم الطالب أو هاتفه..." class="w-full pr-8 pl-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="absolute right-2.5 top-2.5 text-slate-400 text-xs">🔍</span>
                </div>
                
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end shrink-0">
                    <button type="button" onclick="selectAllStudents(true)" class="flex-1 sm:flex-initial px-3 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition text-center">
                        ✓ تحديد الكل
                    </button>
                    <button type="button" onclick="selectAllStudents(false)" class="flex-1 sm:flex-initial px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition text-center">
                        إلغاء التحديد
                    </button>
                </div>
            </div>

            <!-- قائمة الطلاب القابلة للاختيار -->
            <div class="p-3 sm:p-4 overflow-y-auto space-y-2 flex-1 min-w-0" id="studentsCheckboxContainer">
                @if($students->isEmpty())
                    <p class="text-center text-slate-400 py-8 text-xs">لا يوجد طلاب مسجلين في النظام حالياً.</p>
                @else
                    @foreach($students as $st)
                        <label class="student-item flex items-center justify-between p-2.5 sm:p-3 rounded-2xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/20 cursor-pointer transition gap-2" data-name="{{ strtolower($st->name) }}" data-phone="{{ $st->phone_whatsapp }}">
                            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                <input type="checkbox" name="student_ids[]" value="{{ $st->id }}" class="student-checkbox w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 border-slate-300 shrink-0">
                                <div class="min-w-0">
                                    <span class="font-bold text-xs text-slate-900 block truncate">{{ $st->name }}</span>
                                    <span class="text-[10px] sm:text-[11px] text-slate-400 block font-mono truncate" dir="ltr">{{ $st->phone_whatsapp ?: $st->email }}</span>
                                </div>
                            </div>

                            <div class="text-left shrink-0">
                                @if($st->studyGroups->count() > 0)
                                    <span class="text-[9px] sm:text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold block max-w-[120px] sm:max-w-none truncate">
                                        {{ $st->studyGroups->first()->name }}
                                    </span>
                                @else
                                    <span class="text-[9px] sm:text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold block">
                                        جديد 🟢
                                    </span>
                                @endif
                                <span class="text-[9px] sm:text-[10px] text-slate-400 block mt-0.5 max-w-[120px] sm:max-w-none truncate">
                                    {{ $st->enrollments->first()?->level?->name ?? 'بانتظار التسكين' }}
                                </span>
                            </div>
                        </label>
                    @endforeach
                @endif
            </div>

            <!-- أزرار الإجراءات في أسفل النافذة -->
            <div class="p-3 sm:p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-2.5 sm:gap-3 shrink-0">
                <span id="selectedCountBadge" class="text-xs font-bold text-indigo-700 text-center sm:text-right">0 طلاب محددين</span>
                <div class="flex flex-col-reverse sm:flex-row gap-2">
                    <button type="button" onclick="closeEnrollModal()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition text-center">
                        إلغاء
                    </button>
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition text-center">
                        تسكين الطلاب المحددين في المجموعة 👥
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- نافذة منبثقة: 3. استعراض طلاب المجموعة (View Students Modal) -->
<!-- ======================================================= -->
<div id="viewStudentsModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 overflow-y-auto">
    <div class="fixed inset-0 cursor-pointer" onclick="closeViewStudentsModal()"></div>
    <div class="relative bg-white rounded-2xl sm:rounded-3xl w-full max-w-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh] min-w-0 my-auto z-10">
        <!-- ترويسة -->
        <div class="px-4 py-3 sm:px-6 sm:py-4 bg-slate-900 text-white flex justify-between items-center shrink-0 gap-2 min-w-0">
            <div class="min-w-0">
                <h3 class="font-black text-xs sm:text-sm flex items-center gap-2 truncate">
                    <span class="shrink-0">📋</span>
                    <span class="truncate">طلاب المجموعة: <span id="viewGroupName" class="text-amber-400"></span></span>
                </h3>
                <span class="text-[10px] sm:text-[11px] text-slate-300 block truncate" id="viewGroupCapacity"></span>
            </div>
            <button onclick="closeViewStudentsModal()" class="text-white/70 hover:text-white text-lg shrink-0 p-1">✕</button>
        </div>

        <div class="p-4 sm:p-6 overflow-y-auto flex-1 min-w-0">
            <div id="studentsListTableContainer" class="space-y-3 min-w-0">
                <!-- سيتم ملؤها بالجافاسكريبت -->
            </div>
        </div>

        <div class="p-3 sm:p-4 bg-slate-50 border-t border-slate-100 flex justify-end shrink-0">
            <button type="button" onclick="closeViewStudentsModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-900 transition text-center">
                إغلاق
            </button>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- نافذة منبثقة: 4. تعديل المجموعة وتغيير الأستاذ (Edit Modal) -->
<!-- ======================================================= -->
<div id="editGroupModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 overflow-y-auto">
    <div class="fixed inset-0 cursor-pointer" onclick="closeEditGroupModal()"></div>
    <div class="relative bg-white rounded-2xl sm:rounded-3xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh] min-w-0 my-auto z-10">
        <div class="px-4 py-3 sm:px-6 sm:py-4 bg-slate-900 text-white flex justify-between items-center shrink-0 gap-2 min-w-0">
            <h3 class="font-black text-xs sm:text-sm flex items-center gap-2 truncate">
                <span class="shrink-0">✏️</span>
                <span class="truncate">تعديل المجموعة وتغيير الأستاذ والمشرف</span>
            </h3>
            <button onclick="closeEditGroupModal()" class="text-white/70 hover:text-white text-lg shrink-0 p-1">✕</button>
        </div>

        <form id="editGroupForm" method="POST" action="" class="p-4 sm:p-6 space-y-4 overflow-y-auto text-xs min-w-0">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">اسم المجموعة بالعربي <span class="text-rose-500">*</span></label>
                    <input type="text" name="name_ar" id="editNameAr" required placeholder="مثال: دورة اللغة الإنجليزية العامة" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">اسم المجموعة بالإنجليزي (English)</label>
                    <input type="text" name="name_en" id="editNameEn" placeholder="مثال: General English Cohort A" dir="ltr" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-xs">
                </div>
            </div>

            <!-- تصنيف الدبلوم والترم والدفعة والمدة والتواريخ للتعديل -->
            <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100 space-y-3">
                <span class="font-black text-indigo-950 block text-xs">🗓️ بيانات الدبلوم، الترم، والفترة الزمنية:</span>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الترم / الفصل الدراسي</label>
                        <input type="text" name="term" id="editTerm" list="createTermsList" placeholder="مثال: الترم الأول 2026" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الدفعة / الـ Batch</label>
                        <input type="text" name="batch" id="editBatch" placeholder="مثال: الدفعة الأولى" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">نوع البرنامج / الدبلوم</label>
                        <input type="text" name="program_type" id="editProgramType" placeholder="مثال: دبلوم لغة إنجليزية شامل" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">مدة الدبلوم بالأشهر</label>
                        <input type="number" name="duration_months" id="editDurationMonths" min="1" max="24" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">تاريخ بداية الدبلوم / الترم</label>
                        <input type="date" name="start_date" id="editStartDate" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">تاريخ نهاية الدبلوم المتوقعة</label>
                        <input type="date" name="end_date" id="editEndDate" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    </div>
                </div>
            </div>

            <!-- المستوى والدورة التعليمية في التعديل -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">المستوى الأكاديمي</label>
                    <select name="level_id" id="editLevelId" onchange="filterCoursesByLevel(this.value, 'editCourseId')" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="">-- اختياري --</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl->id }}">{{ $lvl->name_en }} ({{ $lvl->name_ar }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الدورة التعليمية <span class="text-red-500">*</span></label>
                    <select name="course_id" id="editCourseId" required onchange="onCourseSelected(event, 'editLevelId')" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" data-level="{{ $c->level_id }}">{{ $c->name_en }} ({{ $c->hours }} ساعة) - [{{ $c->level?->name_en }}]</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- الكادر التعليمي في التعديل: المعلم الرئيسي + المساعدين -->
            <div class="p-4 bg-amber-50/50 rounded-2xl border border-amber-200/80 space-y-3">
                <span class="font-black text-amber-950 block text-xs">👨‍🏫 الكادر التعليمي للمجموعة:</span>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">⭐ المعلم الرئيسي (Primary Teacher)</label>
                    <select name="teacher_id" id="editTeacherId" class="w-full px-3.5 py-2.5 rounded-xl border border-amber-300 focus:ring-2 focus:ring-amber-500 outline-none bg-white font-bold">
                        <option value="">-- لم يحدد بعد --</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">👨‍🏫 {{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">المعلمين المساعدين المسندين للمجموعة:</label>
                    <div class="max-h-36 overflow-y-auto space-y-1.5 p-1 bg-white rounded-xl border border-slate-200">
                        @foreach($teachers as $t)
                            <div class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded-lg">
                                <input type="checkbox" name="assistant_teachers[]" value="{{ $t->id }}" id="asst_edit_{{ $t->id }}" class="edit-asst-teacher rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <label for="asst_edit_{{ $t->id }}" class="font-bold text-slate-800 flex-1 cursor-pointer">{{ $t->name }}</label>
                                <input type="text" name="assistant_roles[{{ $t->id }}]" id="asst_role_edit_{{ $t->id }}" placeholder="الدور: محادثة / استماع" class="edit-asst-role w-36 px-2 py-1 text-[11px] rounded-lg border border-slate-200 focus:ring-1 focus:ring-indigo-500 outline-none">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- هيئة الإشراف في التعديل -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                <span class="font-black text-slate-900 block text-xs">🛡️ هيئة الإشراف الأكاديمي:</span>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">المشرف الرئيسي للمجموعة</label>
                    <select name="supervisor_id" id="editSupervisorId" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="">-- إدارة المنصة --</option>
                        @foreach($supervisors as $s)
                            <option value="{{ $s->id }}">🛡️ {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">المشرفين المساعدين:</label>
                    <div class="max-h-28 overflow-y-auto space-y-1.5 p-1 bg-white rounded-xl border border-slate-200">
                        @foreach($supervisors as $s)
                            <label class="flex items-center gap-2 p-1.5 hover:bg-slate-50 rounded-lg cursor-pointer">
                                <input type="checkbox" name="assistant_supervisors[]" value="{{ $s->id }}" id="sup_edit_{{ $s->id }}" class="edit-asst-sup rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <span class="font-bold text-slate-700">🛡️ {{ $s->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">أيام الحصص</label>
                    <input type="text" name="schedule_days" id="editScheduleDays" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الحد الأقصى للطلاب</label>
                    <input type="number" name="max_students" id="editMaxStudents" min="1" max="100" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">توقيت الحصة</label>
                    <input type="text" name="schedule_time" id="editScheduleTime" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono" dir="ltr">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">حالة المجموعة</label>
                    <select name="status" id="editStatus" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="active">● نشطة وتدرس (Active)</option>
                        <option value="completed">✓ مكتملة (Completed)</option>
                        <option value="archived">📦 مؤرشفة (Archived)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">وصف أو ملاحظات</label>
                <textarea name="description" id="editDescription" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="closeEditGroupModal()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition text-center">
                    إلغاء
                </button>
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold transition shadow-md text-center">
                    حفظ التعديلات 💾
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- نافذة منبثقة: 5. أنشطة وصوتيات المجموعة (Activities & Audio Feed) -->
<!-- ======================================================= -->
<div id="activitiesModal" class="fixed inset-0 z-50 hidden bg-slate-950/75 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 overflow-y-auto">
    <div class="fixed inset-0 cursor-pointer" onclick="closeActivitiesModal()"></div>
    <div class="relative bg-white rounded-2xl sm:rounded-3xl w-full max-w-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh] min-w-0 my-auto z-10">
        <!-- ترويسة النافذة -->
        <div class="px-4 py-3 sm:px-6 sm:py-4 bg-purple-950 text-white flex justify-between items-center shrink-0 gap-2 min-w-0">
            <div class="min-w-0">
                <h3 class="font-black text-xs sm:text-sm flex items-center gap-2 truncate">
                    <span class="shrink-0">📎</span>
                    <span class="truncate">حائط أنشطة وصوتيات المجموعة: <span id="activitiesGroupName" class="text-amber-400"></span></span>
                </h3>
                <span class="text-[10px] sm:text-[11px] text-purple-200 block truncate">أوراق النشاط والتسجيلات الصوتية والتنبيهات الموجهة لجميع طلاب المجموعة</span>
            </div>
            <button onclick="closeActivitiesModal()" class="text-white/70 hover:text-white text-lg shrink-0 p-1">✕</button>
        </div>

        <div class="p-4 sm:p-6 overflow-y-auto space-y-4 sm:space-y-6 flex-1 text-xs min-w-0">
            
            <!-- القسم الأول: سجل الأنشطة والصوتيات المنشورة -->
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-3">
                    <h4 class="font-black text-slate-900 flex items-center gap-1.5">
                        <span>📢</span>
                        <span>الأنشطة والصوتيات الحالية</span>
                    </h4>
                    <span id="activitiesCountBadge" class="text-[11px] font-bold text-purple-700 bg-purple-50 px-2.5 py-0.5 rounded-full">0 أنشطة</span>
                </div>

                <div id="activitiesListContainer" class="space-y-3">
                    <!-- سيتم ملؤه ديناميكياً بالجافاسكريبت -->
                </div>
            </div>

            <!-- القسم الثاني: نشر نشاط أو تسجيل صوتي جديد -->
            <div class="bg-purple-50/70 border border-purple-200 rounded-2xl p-5 space-y-4">
                <div class="flex items-center gap-2 text-purple-950">
                    <span class="text-base">➕</span>
                    <h4 class="font-black text-sm">نشر نشاط أو تسجيل صوتي أو ورقة عمل جديدة</h4>
                </div>

                <form id="newActivityForm" method="POST" action="" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">نوع المنشور <span class="text-red-500">*</span></label>
                            <select name="type" id="activityTypeSelect" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 outline-none bg-white font-bold">
                                <option value="worksheet">📄 ورقة نشاط / واجب (PDF, Word, صورة)</option>
                                <option value="audio">🎙️ تسجيل صوتي (MP3, Audio Note)</option>
                                <option value="announcement">📢 تنبيه وملاحظة كتابية</option>
                                <option value="video">🎥 مقطع فيديو توضيحي</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">عنوان النشاط / التسجيل <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required placeholder="مثال: تدريب الاستماع للدرس الثاني" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">تعليمات وتوجيهات النشاط للطلاب</label>
                        <textarea name="instructions" rows="2" placeholder="اكتب للطلاب ما المطلوب منهم فعله (مثلاً: استمعوا للتسجيل وحلوا الأسئلة بالصفحة 15)..." class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 outline-none"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">إرفاق الملف أو التسجيل الصوتي</label>
                            <input type="file" name="attachment" accept=".pdf,.doc,.docx,.mp3,.wav,.ogg,.m4a,.mp4,.png,.jpg,.jpeg" class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer">
                            <span class="text-[10px] text-slate-400 block mt-1">يدعم الصوتيات MP3/WAV، المستندات PDF/Word، والصور (حتى 50MB).</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">موعد التسليم الأخير (اختياري)</label>
                            <input type="date" name="due_date" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 outline-none">
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                            <span>🚀</span>
                            <span>نشر في حائط المجموعة الآن</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <div class="p-3 sm:p-4 bg-slate-50 border-t border-slate-100 flex justify-end shrink-0">
            <button type="button" onclick="closeActivitiesModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-900 transition text-center">
                إغلاق النافذة
            </button>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- نافذة منبثقة: 6. استدعاء ومشاركة من بنك موارد المستوى المركزي للمجموعة -->
<!-- ======================================================= -->
<div id="groupLevelResourcesModal" class="fixed inset-0 z-50 hidden bg-slate-950/75 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 overflow-y-auto">
    <div class="fixed inset-0 cursor-pointer" onclick="closeGroupLevelResourcesModal()"></div>
    <div class="relative bg-white rounded-2xl sm:rounded-3xl w-full max-w-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh] min-w-0 my-auto z-10">
        <!-- ترويسة النافذة -->
        <div class="px-4 py-3 sm:px-6 sm:py-4 bg-gradient-to-r from-indigo-900 to-purple-950 text-white flex justify-between items-center shrink-0 gap-2 min-w-0">
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-white/10 flex items-center justify-center text-lg sm:text-xl shrink-0">📦</span>
                <div class="min-w-0">
                    <h3 class="font-black text-xs sm:text-sm flex items-center gap-2 truncate">
                        <span class="truncate">بنك موارد المستوى: <span id="glrLevelName" class="text-amber-400"></span></span>
                    </h3>
                    <span class="text-[10px] sm:text-[11px] text-indigo-200 block truncate">مشاركة الكتب، التسجيلات الصوتية، ومحاضرات الفيديو</span>
                </div>
            </div>
            <button onclick="closeGroupLevelResourcesModal()" class="text-white/70 hover:text-white text-lg shrink-0 p-1">✕</button>
        </div>

        <div class="p-4 sm:p-6 overflow-y-auto space-y-4 flex-1 text-xs min-w-0">
            <div class="p-3 bg-amber-50 rounded-2xl border border-amber-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-amber-950 min-w-0">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="text-lg shrink-0">💡</span>
                    <span class="text-[11px] sm:text-xs">الموارد المرفوعة في هذا المستوى تظل محفوظة دوماً في بنك المناهج، ويمكنك مشاركتها للطلاب.</span>
                </div>
                <a id="glrOpenBankLink" href="#" target="_blank" class="w-full sm:w-auto px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-[11px] whitespace-nowrap shadow-xs text-center shrink-0">
                    فتح بنك المستوى ↗️
                </a>
            </div>

            <!-- قائمة الموارد المتاحة للاستدعاء والنشر -->
            <div id="glrResourcesListContainer" class="space-y-3 min-w-0">
                <!-- سيتم ملؤه بالجافاسكريبت -->
            </div>
        </div>

        <div class="p-3 sm:p-4 bg-slate-50 border-t border-slate-100 flex justify-end shrink-0">
            <button type="button" onclick="closeGroupLevelResourcesModal()" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-900 transition text-center">
                إغلاق النافذة
            </button>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- سكريبتات التحكم والنوافذ المنبثقة -->
<!-- ======================================================= -->
<script>
    // 0. فتح وإغلاق نافذة استدعاء ومشاركة موارد المستوى للمجموعة
    function openGroupLevelResourcesModal(grp, level, resources, groupActivities) {
        document.getElementById('glrLevelName').innerText = level ? level.name : '';
        document.getElementById('glrGroupName').innerText = grp.name;
        document.getElementById('glrOpenBankLink').href = '/admin/levels/' + (level ? level.id : '') + '/resources';

        const container = document.getElementById('glrResourcesListContainer');
        container.innerHTML = '';

        if (!resources || resources.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-slate-400 space-y-2 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <span class="text-3xl block">📚</span>
                    <p class="text-xs font-bold text-slate-600">لا توجد موارد مضافة بعد في بنك هذا المستوى.</p>
                    <p class="text-[11px]">يمكن للإدارة أو المعلم ذو الصلاحية رفع الكتب والمقاطع الصوتية والفيديوهات من بنك المناهج والأسعار.</p>
                </div>
            `;
        } else {
            // تجميع المعرفات للأنشطة المنشورة في هذه المجموعة
            const sharedResourceMap = {};
            if (groupActivities && groupActivities.length > 0) {
                groupActivities.forEach(act => {
                    if (act.level_resource_id) {
                        sharedResourceMap[act.level_resource_id] = act;
                    }
                });
            }

            resources.forEach(res => {
                const isShared = !!sharedResourceMap[res.id];

                let icon = '📄';
                let typeLabel = 'مورد تعليمي';
                let badgeClass = 'bg-slate-100 text-slate-800';

                if (res.type === 'book') { icon = '📚'; typeLabel = 'كتاب / مقرر دراسي'; badgeClass = 'bg-emerald-50 text-emerald-800 border border-emerald-200'; }
                else if (res.type === 'audio') { icon = '🎧'; typeLabel = 'تسجيل صوتي'; badgeClass = 'bg-purple-50 text-purple-800 border border-purple-200'; }
                else if (res.type === 'video') { icon = '🎥'; typeLabel = 'محاضرة فيديو'; badgeClass = 'bg-blue-50 text-blue-800 border border-blue-200'; }
                else if (res.type === 'worksheet') { icon = '📝'; typeLabel = 'ورقة نشاط وتطبيق'; badgeClass = 'bg-amber-50 text-amber-800 border border-amber-200'; }
                else if (res.type === 'quiz') { icon = '🎯'; typeLabel = 'كويز واختبار قصير'; badgeClass = 'bg-rose-50 text-rose-800 border border-rose-200'; }

                const card = document.createElement('div');
                card.className = `p-4 rounded-2xl border ${isShared ? 'border-emerald-300 bg-emerald-50/20' : 'border-slate-200 bg-white'} space-y-3 shadow-2xs hover:border-indigo-300 transition`;

                let shareActionHtml = '';
                if (isShared) {
                    shareActionHtml = `
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <span class="text-[11px] font-bold text-emerald-700 flex items-center gap-1.5">
                                <span>✅</span>
                                <span>تمت مشاركته لطلاب هذه المجموعة</span>
                            </span>
                            <form method="POST" action="/groups/${grp.id}/resources/${res.id}/unshare" onsubmit="return confirm('هل تريد إزالة هذا المورد من المجموعة؟ (سيبقى محفوظاً في بنك المستوى)');">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="px-2.5 py-1 text-[11px] text-rose-600 hover:bg-rose-50 rounded-lg border border-rose-200 font-bold transition">
                                    إلغاء المشاركة من المجموعة ✕
                                </button>
                            </form>
                        </div>
                    `;
                } else {
                    shareActionHtml = `
                        <div class="pt-2 border-t border-slate-100">
                            <form method="POST" action="/groups/${grp.id}/share-resource" class="space-y-2">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="resource_id" value="${res.id}">
                                <div class="flex items-center gap-2">
                                    <input type="text" name="custom_instructions" placeholder="ملاحظة أو توجيه إضافي للطلاب لهذا اليوم (اختياري)..." class="flex-1 px-3 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                                    <button type="submit" class="px-4 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-black text-xs rounded-xl shadow-xs transition shrink-0 flex items-center gap-1.5">
                                        <span>🚀</span>
                                        <span>مشاركة للمجموعة اليوم</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    `;
                }

                card.innerHTML = `
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-start gap-2.5">
                            <span class="text-2xl shrink-0">${icon}</span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold ${badgeClass}">${typeLabel}</span>
                                    ${res.file_size ? `<span class="text-[10px] text-slate-400 font-mono">(${res.file_size})</span>` : ''}
                                    ${res.duration_minutes ? `<span class="text-[10px] text-blue-600 font-mono">⏱️ ${res.duration_minutes} د</span>` : ''}
                                </div>
                                <h4 class="font-black text-xs sm:text-sm text-slate-900 mt-1 leading-tight">${res.title}</h4>
                                ${res.description ? `<p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">${res.description}</p>` : ''}
                            </div>
                        </div>
                    </div>
                    ${shareActionHtml}
                `;
                container.appendChild(card);
            });
        }

        document.getElementById('groupLevelResourcesModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeGroupLevelResourcesModal() {
        document.getElementById('groupLevelResourcesModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    // 1. فتح وإغلاق نافذة الإنشاء
    function openCreateGroupModal() {
        document.getElementById('createGroupModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeCreateGroupModal() {
        document.getElementById('createGroupModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // 2. فتح وإغلاق نافذة التعديل
    function openEditGroupModal(grp) {
        document.getElementById('editGroupForm').action = "/admin/groups/" + grp.id + "/update";
        if (document.getElementById('editNameAr')) {
            document.getElementById('editNameAr').value = grp.name_ar || grp.name || '';
        }
        if (document.getElementById('editNameEn')) {
            document.getElementById('editNameEn').value = grp.name_en || '';
        }
        document.getElementById('editTerm').value = grp.term || '';
        document.getElementById('editBatch').value = grp.batch || '';
        document.getElementById('editProgramType').value = grp.program_type || '';
        document.getElementById('editDurationMonths').value = grp.duration_months || '';
        document.getElementById('editStartDate').value = grp.start_date ? grp.start_date.substring(0, 10) : '';
        document.getElementById('editEndDate').value = grp.end_date ? grp.end_date.substring(0, 10) : '';
        document.getElementById('editCourseId').value = grp.course_id || '';
        document.getElementById('editTeacherId').value = grp.teacher_id || '';
        document.getElementById('editSupervisorId').value = grp.supervisor_id || '';
        document.getElementById('editScheduleDays').value = grp.schedule_days || '';
        document.getElementById('editScheduleTime').value = grp.schedule_time || '';
        document.getElementById('editMaxStudents').value = grp.max_students || 15;
        document.getElementById('editStatus').value = grp.status || 'active';
        document.getElementById('editDescription').value = grp.description || '';

        // تفريغ وتحديد المعلمين المساعدين
        document.querySelectorAll('.edit-asst-teacher').forEach(cb => { cb.checked = false; });
        document.querySelectorAll('.edit-asst-role').forEach(inp => { inp.value = ''; });
        if (grp.teachers && grp.teachers.length > 0) {
            grp.teachers.forEach(t => {
                if (!t.pivot || !t.pivot.is_primary) {
                    const cb = document.getElementById('asst_edit_' + t.id);
                    if (cb) cb.checked = true;
                    const roleInp = document.getElementById('asst_role_edit_' + t.id);
                    if (roleInp && t.pivot) roleInp.value = t.pivot.role_title || '';
                }
            });
        }

        // تفريغ وتحديد المشرفين المساعدين
        document.querySelectorAll('.edit-asst-sup').forEach(cb => { cb.checked = false; });
        if (grp.supervisors && grp.supervisors.length > 0) {
            grp.supervisors.forEach(s => {
                if (!s.pivot || !s.pivot.is_primary) {
                    const cb = document.getElementById('sup_edit_' + s.id);
                    if (cb) cb.checked = true;
                }
            });
        }

        if (grp.level_id) {
            document.getElementById('editLevelId').value = grp.level_id;
            filterCoursesByLevel(grp.level_id, 'editCourseId');
        } else {
            document.getElementById('editLevelId').value = '';
            filterCoursesByLevel('', 'editCourseId');
        }
        document.getElementById('editCourseId').value = grp.course_id || '';

        document.getElementById('editGroupModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeEditGroupModal() {
        document.getElementById('editGroupModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // 3. فلترة الدورات حسب المستوى الأكاديمي المختار
    function filterCoursesByLevel(levelId, selectId) {
        const select = document.getElementById(selectId);
        if (!select) return;
        const options = select.querySelectorAll('option');
        options.forEach(opt => {
            if (!opt.value) return;
            if (!levelId || opt.getAttribute('data-level') == levelId) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
    }

    function onCourseSelected(event, levelSelectId) {
        const select = event.target;
        const opt = select.selectedOptions[0];
        if (opt) {
            const lvlId = opt.getAttribute('data-level');
            if (lvlId) {
                const lvlSelect = document.getElementById(levelSelectId);
                if (lvlSelect) lvlSelect.value = lvlId;
            }
        }
    }

    // 4. فتح وإغلاق نافذة تسكين الطلاب
    let currentEnrollingGroupId = null;
    function openEnrollModal(grp) {
        currentEnrollingGroupId = grp.id;
        document.getElementById('enrollGroupName').innerText = grp.name;
        document.getElementById('enrollForm').action = "/admin/groups/" + grp.id + "/enroll";

        // إعادة ضبط التحديد
        selectAllStudents(false);
        updateSelectedCount();

        document.getElementById('enrollModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeEnrollModal() {
        document.getElementById('enrollModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function selectAllStudents(select) {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => {
            // فقط الظاهرة بعد الفلترة
            if (cb.closest('.student-item').style.display !== 'none') {
                cb.checked = select;
            }
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = document.querySelectorAll('.student-checkbox:checked').length;
        document.getElementById('selectedCountBadge').innerText = count + " طلاب محددين للتسكين";
    }

    document.querySelectorAll('.student-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });

    function filterStudentList() {
        const q = (document.getElementById('studentFilterInput').value || '').toLowerCase();
        const items = document.querySelectorAll('.student-item');
        items.forEach(it => {
            const name = it.getAttribute('data-name') || '';
            const phone = it.getAttribute('data-phone') || '';
            if (name.includes(q) || phone.includes(q)) {
                it.style.display = '';
            } else {
                it.style.display = 'none';
            }
        });
    }

    // 5. استعراض طلاب المجموعة وحذفهم أو نقلهم
    function openViewStudentsModal(grp, students) {
        document.getElementById('viewGroupName').innerText = grp.name;
        document.getElementById('viewGroupCapacity').innerText = "السعة: " + (students ? students.length : 0) + " من " + grp.max_students + " طلاب";

        const container = document.getElementById('studentsListTableContainer');
        container.innerHTML = '';

        if (!students || students.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-slate-400 space-y-2">
                    <span class="text-3xl block">👥</span>
                    <p class="text-xs">لم يتم تسكين أي طلاب في هذه المجموعة بعد.</p>
                </div>
            `;
        } else {
            students.forEach((st, idx) => {
                const row = document.createElement('div');
                row.className = "flex flex-col sm:flex-row sm:items-center justify-between p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 gap-3";
                row.innerHTML = `
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 font-mono font-bold text-xs flex items-center justify-center shrink-0">
                            ${idx + 1}
                        </span>
                        <div>
                            <span class="font-bold text-xs text-slate-900 block">${st.name}</span>
                            <span class="text-[11px] text-slate-400 font-mono block" dir="ltr">${st.phone_whatsapp || st.email}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="/admin/reports/student/${st.id}/transcript" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] border border-indigo-200 transition flex items-center gap-1 shadow-2xs">
                            <span>📜</span>
                            <span>السجل الشامل 360</span>
                        </a>
                        <form method="POST" action="/admin/groups/${grp.id}/students/${st.id}" onsubmit="return confirm('هل تريد إلغاء تسكين الطالب [${st.name}] من هذه المجموعة؟');">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-bold text-[11px] transition">
                                إلغاء التسكين
                            </button>
                        </form>
                    </div>
                `;
                container.appendChild(row);
            });
        }

        document.getElementById('viewStudentsModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeViewStudentsModal() {
        document.getElementById('viewStudentsModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // 6. فتح وإغلاق نافذة أنشطة وصوتيات المجموعة
    function openActivitiesModal(grp, activities) {
        document.getElementById('activitiesGroupName').innerText = grp.name;
        document.getElementById('newActivityForm').action = "/groups/" + grp.id + "/activities";

        const container = document.getElementById('activitiesListContainer');
        const badge = document.getElementById('activitiesCountBadge');
        container.innerHTML = '';

        if (!activities || activities.length === 0) {
            badge.innerText = "0 أنشطة";
            container.innerHTML = `
                <div class="text-center py-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-slate-400 space-y-1">
                    <span class="text-3xl block">🎙️</span>
                    <p class="text-xs font-bold text-slate-600">لا توجد أنشطة أو تسجيلات صوتية منشورة بعد</p>
                    <p class="text-[11px]">استخدم النموذج بالأسفل لرفع ورقة نشاط (PDF) أو تسجيل صوتي (MP3) للطلاب الآن.</p>
                </div>
            `;
        } else {
            badge.innerText = activities.length + " أنشطة وصوتيات";
            activities.forEach((act) => {
                const item = document.createElement('div');
                item.className = "p-4 rounded-2xl border border-slate-200 bg-white shadow-sm space-y-2 hover:border-purple-300 transition";
                
                const isStudent = act.publisher && act.publisher.role === 'student';
                let icon = isStudent ? '🎒' : '📄';
                let typeBadge = `<span class="text-[10px] font-bold px-2 py-0.5 rounded-full ${isStudent ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800'}">${isStudent ? 'تسليم طالب' : 'ورقة نشاط / واجب'}</span>`;
                
                const ext = act.file_path ? act.file_path.split('.').pop().toLowerCase() : '';
                const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);

                if (act.type === 'audio' || ['mp3', 'wav', 'ogg', 'webm', 'm4a'].includes(ext)) {
                    icon = '🎙️';
                    typeBadge = `<span class="text-[10px] font-bold px-2 py-0.5 rounded-full ${isStudent ? 'bg-emerald-100 text-emerald-800' : 'bg-purple-100 text-purple-800'}">${isStudent ? 'تسجيل صوتي من طالب' : 'تسجيل صوتي 🎵'}</span>`;
                } else if (isImage) {
                    icon = '📷';
                    typeBadge = `<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">صورة ورقة إجابة 🖼️</span>`;
                } else if (act.type === 'announcement') {
                    icon = '📢';
                    typeBadge = '<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">تنبيه وملاحظة</span>';
                } else if (act.type === 'video') {
                    icon = '🎥';
                    typeBadge = '<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-800">مقطع فيديو</span>';
                }

                let imagePreviewHtml = '';
                if (isImage && act.file_path) {
                    imagePreviewHtml = `
                        <div class="mt-2 space-y-1">
                            <a href="/${act.file_path}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-200 aspect-video max-h-44 bg-slate-900 group">
                                <img src="/${act.file_path}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                            </a>
                            <a href="/${act.file_path}" target="_blank" class="text-[11px] font-bold text-emerald-700 hover:underline inline-block">🔍 فتح الصورة بالحجم الكامل</a>
                        </div>
                    `;
                }

                let audioPlayerHtml = '';
                if ((act.type === 'audio' || ['mp3', 'wav', 'ogg', 'webm', 'm4a'].includes(ext)) && act.file_path) {
                    audioPlayerHtml = `
                        <div class="bg-purple-50 p-2.5 rounded-xl border border-purple-200 mt-2">
                            <span class="text-[10px] font-bold text-purple-900 block mb-1">▶️ استمع للتسجيل الصوتي مباشرة:</span>
                            <audio controls class="w-full h-8">
                                <source src="/${act.file_path}">
                                المتصفح لا يدعم مشغل الصوت.
                            </audio>
                        </div>
                    `;
                }

                let downloadFileHtml = '';
                if (act.file_path && !isImage && act.type !== 'audio' && !['mp3', 'wav', 'ogg', 'webm', 'm4a'].includes(ext)) {
                    downloadFileHtml = `
                        <div class="mt-2">
                            <a href="/${act.file_path}" download target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs border border-indigo-200 transition">
                                <span>📥</span>
                                <span>تحميل ورقة النشاط (${act.file_name || 'ملف مرفق'})</span>
                                ${act.file_size ? `<span class="text-[10px] text-indigo-400 font-mono">(${act.file_size})</span>` : ''}
                            </a>
                        </div>
                    `;
                }

                const publisherName = act.publisher ? act.publisher.name : 'إدارة المنصة';
                const createdDate = new Date(act.created_at).toLocaleDateString('ar-EG', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });

                item.innerHTML = `
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">${icon}</span>
                            <div>
                                <h5 class="font-black text-sm text-slate-900 leading-snug">${act.title}</h5>
                                <span class="text-[10px] text-slate-400">بواسطة: ${publisherName} • ${createdDate}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            ${typeBadge}
                            <form method="POST" action="/groups/activities/${act.id}" onsubmit="return confirm('هل تريد حذف هذا النشاط؟');">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" title="حذف النشاط" class="w-6 h-6 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center text-xs transition">
                                    ✕
                                </button>
                            </form>
                        </div>
                    </div>

                    ${act.instructions ? `<p class="text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 leading-relaxed">${act.instructions}</p>` : ''}
                    ${imagePreviewHtml}
                    ${audioPlayerHtml}
                    ${downloadFileHtml}
                `;
                container.appendChild(item);
            });
        }

        document.getElementById('activitiesModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeActivitiesModal() {
        document.getElementById('activitiesModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // إغلاق بمفتاح Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeCreateGroupModal();
            closeEditGroupModal();
            closeEnrollModal();
            closeViewStudentsModal();
            closeActivitiesModal();
        }
    });
</script>
@endsection
