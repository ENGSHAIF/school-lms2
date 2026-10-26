@extends('layouts.app', ['title' => 'لوحة المعلم - إدارة الحصص والفصول الحية'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto my-4">

    <!-- شريط الترحيب والمعلم -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border border-slate-800">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 bg-indigo-500/20 text-indigo-300 text-xs px-3.5 py-1 rounded-full font-bold border border-indigo-400/30">
                <span>👨‍🏫</span>
                <span>بوابة التدريس الحي والفصول التفاعلية (1-on-1 Air Class)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">أهلاً بك، {{ $teacher->name }}</h1>
            <p class="text-xs text-slate-300">متابعة مواعيد حصصك المجدولة، إطلاق الفصول الحية 25 دقيقة، رصد النجوم وإرسال تقارير الواتساب الفورية للطلاب.</p>
        </div>

        <a href="{{ route('student.booking') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs px-6 py-3.5 rounded-2xl shadow-lg transition whitespace-nowrap">
            جدول الحجوزات والمواعيد 📅
        </a>
    </div>

    <!-- البطاقات الإحصائية والمالية للمعلم -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                🎙️
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">الحصص القادمة</span>
                <strong class="text-2xl font-black text-slate-900 font-mono">{{ $upcomingBookings->count() }}</strong>
                <span class="text-[10px] text-slate-500 block">حصص 25 دقيقة بانتظار البدء</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                ✅
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">الحصص المنفذة</span>
                <strong class="text-2xl font-black text-emerald-600 font-mono">{{ $completedBookings->count() }}</strong>
                <span class="text-[10px] text-slate-500 block">تم رفع تقاريرها</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                💵
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">أجر الحصة المعتمد</span>
                <strong class="text-2xl font-black text-indigo-600 font-mono">$7.00</strong>
                <span class="text-[10px] text-slate-500 block">لكل جلسة 25 دقيقة</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 bg-purple-50 border border-purple-200 rounded-2xl flex items-center justify-center text-3xl shadow-inner">
                🧾
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 block">الرصيد المالي المستحق</span>
                <strong class="text-2xl font-black text-purple-600 font-mono">{{ $account?->formatted_balance ?? '0.00 ر.ي' }}</strong>
                <span class="text-[10px] text-slate-500 block">حساب المعلم الدائن</span>
            </div>
        </div>
    </div>

    <!-- ================= المجموعات والدفعات الدراسية المسندة للمعلم ================= -->
    <div class="bg-gradient-to-r from-indigo-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl space-y-4 border border-indigo-800">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-indigo-800/80 pb-3">
            <div>
                <h2 class="text-lg font-black flex items-center gap-2">
                    <span>👥</span>
                    <span>المجموعات والدفعات الدراسية المسندة إليك (Study Groups)</span>
                </h2>
                <p class="text-xs text-indigo-200 mt-0.5">الفصول الجماعية التي تقوم بتدريسها مع رابط الغرفة التفاعلية المباشرة</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-amber-400 text-slate-950 font-black text-xs">
                {{ $myGroups->count() }} مجموعات دراسية
            </span>
        </div>

        @if($myGroups->isEmpty())
            <div class="text-center py-6 text-indigo-300 text-xs">
                <p>لم تُسند إليك أي مجموعة دراسية جماعية حالياً. جدولك متاح للحصص الفردية 1-on-1 بالأسفل.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                @foreach($myGroups as $grp)
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/15 space-y-3 flex flex-col justify-between">
                        <div class="space-y-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                            {{ $grp->course?->name }} • {{ $grp->level?->name ?? 'المستوى المعتمد' }}
                                        </span>
                                        @if($grp->is_live)
                                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-500 text-slate-950 animate-pulse font-bold">
                                                ● البث مفتوح للطلاب
                                            </span>
                                        @elseif($grp->isPendingApproval())
                                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-400 text-slate-950 animate-bounce font-bold">
                                                ⏳ طلب فتح بث معلق
                                            </span>
                                        @else
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-400/30">
                                                🔴 البث مغلق للطلاب
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-base font-black text-white mt-1.5">{{ $grp->name }}</h3>
                                </div>
                                <span class="text-xs font-mono font-bold text-amber-400 bg-amber-400/10 px-2 py-1 rounded-lg">
                                    {{ $grp->students->count() }} طلاب
                                </span>
                            </div>

                            <div class="text-xs text-indigo-200 space-y-1">
                                <div>⏰ المواعيد: <strong>{{ $grp->schedule_days ?: 'لم يحدد' }}</strong> ({{ $grp->schedule_time ?: 'توقيت مرن' }})</div>
                                <div class="text-[11px] text-slate-300">الطلاب: {{ $grp->students->pluck('name')->implode('، ') }}</div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-white/10 flex flex-col gap-2">
                            <!-- أزرار التحكم بالبث الحي للمعلم -->
                            @if($grp->isPendingApproval())
                                <div class="bg-amber-500/20 border border-amber-400/40 p-2.5 rounded-xl space-y-1.5 text-center">
                                    <span class="text-[11px] text-amber-300 font-bold block">
                                        ⏳ الطالب ({{ $grp->requester?->name ?? 'طالب' }}) ينتظر فتح البث!
                                    </span>
                                    <div class="grid grid-cols-2 gap-2">
                                        <form method="POST" action="{{ route('groups.live.start', $grp->id) }}">
                                            @csrf
                                            <button type="submit" class="w-full py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs transition">
                                                اعتماد وفتح ✅
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('groups.live.reject', $grp->id) }}">
                                            @csrf
                                            <button type="submit" class="w-full py-1.5 rounded-lg bg-white/20 hover:bg-white/30 text-white font-bold text-xs transition">
                                                رفض ✕
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @elseif($grp->is_live)
                                <form method="POST" action="{{ route('groups.live.stop', $grp->id) }}">
                                    @csrf
                                    <button type="submit" class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs text-center shadow-lg transition flex items-center justify-center gap-2">
                                        <span>⏹️</span>
                                        <span>إغلاق وإنهاء البث الحي للطلاب</span>
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('groups.live.start', $grp->id) }}">
                                    @csrf
                                    <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs text-center shadow-lg transition flex items-center justify-center gap-2">
                                        <span>📡</span>
                                        <span>فتح وبدء البث الحي للطلاب الآن</span>
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('classroom.show', $grp->room_id) }}" target="_blank" class="w-full py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs text-center shadow-sm transition flex items-center justify-center gap-2">
                                <span>🎥</span>
                                <span>دخول الفصل التفاعلي المباشر (Air Class)</span>
                            </a>

                            @if($grp->level)
                                @php $levelResCount = $grp->level->resources ? $grp->level->resources->count() : 0; @endphp
                                <button onclick='openGroupLevelResourcesModal(@json($grp), @json($grp->level), @json($grp->level->resources), @json($grp->activities))' class="w-full py-2 rounded-xl bg-gradient-to-r from-amber-500/20 to-purple-500/20 hover:from-amber-500/30 hover:to-purple-500/30 text-amber-200 border border-amber-400/40 font-bold text-xs text-center transition flex items-center justify-center gap-2">
                                    <span>📦</span>
                                    <span>استدعاء ومشاركة من بنك موارد المستوى ({{ $levelResCount }})</span>
                                </button>
                            @endif

                            <button onclick='openActivitiesModal(@json($grp), @json($grp->activities))' class="w-full py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white font-bold text-xs text-center transition flex items-center justify-center gap-2 border border-white/20">
                                <span>📎</span>
                                <span>أنشطة وصوتيات المجموعة ({{ $grp->activities->count() }})</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ================= الحصص القادمة المحجوزة وغرفة البث المباشر ================= -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <span>📡</span>
                    <span>الحصص التفاعلية الحية القادمة (Air Class 25 Min)</span>
                </h2>
                <p class="text-xs text-slate-500">ادخل الغرفة قبل الموعد بـ 5 دقائق لبدء التفاعل مع الطالب</p>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($upcomingBookings as $b)
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 hover:border-indigo-400 transition">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">🎓</span>
                            <strong class="text-sm font-black text-slate-900">{{ $b->student?->name }}</strong>
                            <span class="text-[10px] bg-indigo-100 text-indigo-800 font-bold px-2.5 py-0.5 rounded-full font-mono">{{ $b->student?->student_code }}</span>
                        </div>
                        <div class="text-xs text-slate-600 flex items-center gap-2">
                            <span>الموضوع: <strong>{{ $b->topic }}</strong></span>
                            <span>•</span>
                            <span>التاريخ: <strong class="font-mono">{{ $b->schedule?->session_date?->format('Y-m-d') }}</strong></span>
                            <span>•</span>
                            <span>الوقت: <strong class="font-mono">{{ $b->schedule?->start_time }}</strong></span>
                        </div>
                    </div>

                    <a href="{{ route('classroom.show', $b->room_id) }}" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs px-6 py-3 rounded-xl shadow-lg transition flex items-center gap-2 whitespace-nowrap">
                        <span>إطلاق ودخول الفصل الحي 🎙️</span>
                    </a>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs">
                    لا توجد حصص محجوزة قادمة حالياً. ستظهر الحصص هنا بمجرد حجز الطلاب أو أولياء أمورهم لمواعيدك.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ================= سجل الحصص المنفذة والتقارير المكتملة ================= -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
        <h2 class="text-base font-black text-slate-900 flex items-center gap-2 border-b pb-3">
            <span>⭐</span>
            <span>سجل الحصص المنفذة مؤخراً</span>
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="text-slate-400 border-b pb-2">
                        <th class="py-2.5 px-3">الطالب</th>
                        <th class="py-2.5 px-3">الموضوع</th>
                        <th class="py-2.5 px-3">النجوم</th>
                        <th class="py-2.5 px-3">نطق / طلاقة / قواعد</th>
                        <th class="py-2.5 px-3">الأجر المستحق</th>
                        <th class="py-2.5 px-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($completedBookings as $cb)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-3 font-bold text-slate-900">{{ $cb->student?->name }}</td>
                            <td class="py-3 px-3 text-slate-600">{{ $cb->topic }}</td>
                            <td class="py-3 px-3 text-amber-500 font-bold font-mono">{{ str_repeat('⭐', $cb->report?->stars_awarded ?? 5) }}</td>
                            <td class="py-3 px-3 font-mono text-[11px] text-indigo-700">
                                {{ $cb->report?->pronunciation_score ?? 5 }} / {{ $cb->report?->fluency_score ?? 5 }} / {{ $cb->report?->grammar_score ?? 5 }}
                            </td>
                            <td class="py-3 px-3 font-mono font-bold text-emerald-600">+$7.00</td>
                            <td class="py-3 px-3 text-slate-400 font-mono text-[11px]">{{ $cb->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-slate-400">لا توجد حصص مكتملة بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- نافذة منبثقة: أنشطة وصوتيات المجموعة للمعلم -->
    <!-- ======================================================= -->
    <div id="activitiesModal" class="fixed inset-0 z-50 hidden bg-slate-950/75 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh]">
            <div class="px-6 py-4 bg-purple-950 text-white flex justify-between items-center shrink-0">
                <div>
                    <h3 class="font-black text-sm flex items-center gap-2">
                        <span>📎</span>
                        <span>أنشطة وتسجيلات صوتية لمجموعة: <span id="activitiesGroupName" class="text-amber-400"></span></span>
                    </h3>
                    <span class="text-[11px] text-purple-200">انشر أوراق العمل أو التسجيلات الصوتية ليستمع إليها جميع طلاب المجموعة</span>
                </div>
                <button onclick="closeActivitiesModal()" class="text-white/70 hover:text-white text-lg">✕</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6 flex-1 text-xs">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-3">
                        <h4 class="font-black text-slate-900 flex items-center gap-1.5">
                            <span>📢</span>
                            <span>الأنشطة والصوتيات الحالية بالمجموعة</span>
                        </h4>
                        <span id="activitiesCountBadge" class="text-[11px] font-bold text-purple-700 bg-purple-50 px-2.5 py-0.5 rounded-full">0 منشور</span>
                    </div>
                    <div id="activitiesListContainer" class="space-y-3"></div>
                </div>

                <!-- نشر نشاط أو مقطع صوتي -->
                <div class="bg-purple-50/70 border border-purple-200 rounded-2xl p-5 space-y-4">
                    <div class="flex items-center gap-2 text-purple-950">
                        <span class="text-base">➕</span>
                        <h4 class="font-black text-sm">نشر نشاط أو تسجيل صوتي جديد لطلابك</h4>
                    </div>

                    <form id="newActivityForm" method="POST" action="" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">نوع المنشور <span class="text-red-500">*</span></label>
                                <select name="type" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 outline-none bg-white font-bold">
                                    <option value="worksheet">📄 ورقة نشاط / واجب عمل (PDF, Word)</option>
                                    <option value="audio">🎙️ تسجيل صوتي (MP3, Audio Note)</option>
                                    <option value="announcement">📢 تنبيه وتوجيه كتابي</option>
                                    <option value="video">🎥 مقطع فيديو توضيحي</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">عنوان النشاط / المقطع <span class="text-red-500">*</span></label>
                                <input type="text" name="title" required placeholder="مثال: استمع للنطق السليم وردد" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">التعليمات للطلاب</label>
                            <textarea name="instructions" rows="2" placeholder="اكتب لطلابك ما المطلوب منهم..." class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 outline-none"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">رفع الملف أو التسجيل الصوتي</label>
                                <input type="file" name="attachment" accept=".pdf,.doc,.docx,.mp3,.wav,.ogg,.m4a,.mp4,.png,.jpg,.jpeg" class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">موعد التسليم (اختياري)</label>
                                <input type="date" name="due_date" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-purple-500 outline-none">
                            </div>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                                <span>🚀</span>
                                <span>إرسال للطلاب فوراً</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end shrink-0">
                <button type="button" onclick="closeActivitiesModal()" class="px-5 py-2.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-900 transition">
                    إغلاق
                </button>
            </div>
        </div>
    </div>

    <!-- نافذة استدعاء ومشاركة موارد المستوى للمجموعة (Teacher Level Resource Bank Modal) -->
    <div id="groupLevelResourcesModal" class="fixed inset-0 z-50 hidden bg-slate-950/75 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-3xl w-full max-w-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh]">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-900 to-purple-950 text-white flex justify-between items-center shrink-0">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl">📦</span>
                    <div>
                        <h3 class="font-black text-sm flex items-center gap-2">
                            <span>بنك موارد المستوى: <span id="glrLevelName" class="text-amber-400"></span></span>
                        </h3>
                        <span class="text-[11px] text-indigo-200">مشاركة الكتب والتسجيلات والفيديوهات والأنشطة لمجموعة: <strong id="glrGroupName" class="text-white"></strong></span>
                    </div>
                </div>
                <button onclick="closeGroupLevelResourcesModal()" class="text-white/70 hover:text-white text-lg">✕</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-4 flex-1 text-xs">
                <div class="p-3 bg-amber-50 rounded-2xl border border-amber-200 text-amber-950 text-xs">
                    💡 <strong>توجيه للمعلم:</strong> هذه الموارد محفوظة دائماً للمستوى، ويمكنك استدعاء ونشر المورد الذي يناسب درس اليوم مع كتابة تعليماتك للطلاب.
                </div>

                <div id="glrResourcesListContainer" class="space-y-3"></div>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end shrink-0">
                <button type="button" onclick="closeGroupLevelResourcesModal()" class="px-5 py-2.5 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-900 transition">
                    إغلاق النافذة
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    function openGroupLevelResourcesModal(grp, level, resources, groupActivities) {
        document.getElementById('glrLevelName').innerText = level ? level.name : '';
        document.getElementById('glrGroupName').innerText = grp.name;

        const container = document.getElementById('glrResourcesListContainer');
        container.innerHTML = '';

        if (!resources || resources.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-slate-400 space-y-2 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <span class="text-3xl block">📚</span>
                    <p class="text-xs font-bold text-slate-600">لا توجد موارد مضافة بعد في بنك هذا المستوى.</p>
                </div>
            `;
        } else {
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
                                <span>تمت مشاركته لطلاب مجموعتك</span>
                            </span>
                            <form method="POST" action="/groups/${grp.id}/resources/${res.id}/unshare" onsubmit="return confirm('هل تريد إزالة هذا المورد من المجموعة؟');">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="px-2.5 py-1 text-[11px] text-rose-600 hover:bg-rose-50 rounded-lg border border-rose-200 font-bold transition">
                                    إلغاء المشاركة ✕
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
                                    <input type="text" name="custom_instructions" placeholder="ملاحظة أو توجيه للطلاب لدرس اليوم (اختياري)..." class="flex-1 px-3 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
                                    <button type="submit" class="px-4 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-black text-xs rounded-xl shadow-xs transition shrink-0 flex items-center gap-1.5">
                                        <span>🚀</span>
                                        <span>مشاركة للطلاب اليوم</span>
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

    function openActivitiesModal(grp, activities) {
        document.getElementById('activitiesGroupName').innerText = grp.name;
        document.getElementById('newActivityForm').action = "/groups/" + grp.id + "/activities";

        const container = document.getElementById('activitiesListContainer');
        const badge = document.getElementById('activitiesCountBadge');
        container.innerHTML = '';

        if (!activities || activities.length === 0) {
            badge.innerText = "0 منشور";
            container.innerHTML = `
                <div class="text-center py-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-slate-400 space-y-1">
                    <span class="text-3xl block">🎙️</span>
                    <p class="text-xs font-bold text-slate-600">لا توجد أنشطة أو تسجيلات صوتية منشورة بعد</p>
                    <p class="text-[11px]">يمكنك رفع ورقة عمل أو تسجيل صوتك وإرساله للطلاب من النموذج بالأسفل.</p>
                </div>
            `;
        } else {
            badge.innerText = activities.length + " أنشطة وصوتيات";
            activities.forEach((act) => {
                const item = document.createElement('div');
                item.className = "p-4 rounded-2xl border border-slate-200 bg-white shadow-sm space-y-2 hover:border-purple-300 transition";
                
                let icon = act.type === 'audio' ? '🎙️' : (act.type === 'announcement' ? '📢' : '📄');
                let typeBadge = act.type === 'audio' 
                    ? '<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-800">تسجيل صوتي 🎵</span>'
                    : '<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">ورقة نشاط</span>';

                let audioHtml = '';
                if (act.type === 'audio' && act.file_path) {
                    audioHtml = `
                        <div class="bg-purple-50 p-2.5 rounded-xl border border-purple-200 mt-2">
                            <audio controls class="w-full h-8"><source src="/${act.file_path}" type="audio/mpeg"></audio>
                        </div>
                    `;
                }

                let downloadHtml = '';
                if (act.file_path && act.type !== 'audio') {
                    downloadHtml = `
                        <div class="mt-2">
                            <a href="/${act.file_path}" download target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 font-bold text-xs border border-indigo-200">
                                <span>📥</span><span>تحميل الملف (${act.file_name || 'ملف'})</span>
                            </a>
                        </div>
                    `;
                }

                item.innerHTML = `
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">${icon}</span>
                            <div>
                                <h5 class="font-black text-sm text-slate-900 leading-snug">${act.title}</h5>
                                <span class="text-[10px] text-slate-400">${new Date(act.created_at).toLocaleDateString('ar-EG')}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            ${typeBadge}
                            <form method="POST" action="/groups/activities/${act.id}" onsubmit="return confirm('هل تريد حذف هذا النشاط؟');">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="w-6 h-6 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center text-xs">✕</button>
                            </form>
                        </div>
                    </div>
                    ${act.instructions ? `<p class="text-slate-600 bg-slate-50 p-2 rounded-xl text-xs">${act.instructions}</p>` : ''}
                    ${audioHtml}
                    ${downloadHtml}
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

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeActivitiesModal();
    });
</script>
@endsection
