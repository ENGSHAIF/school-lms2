@extends('layouts.app', ['title' => 'بوابة الطالب الأكاديمية والتعليم عن بعد'])

@section('content')
@php
    $vouchers = $vouchers ?? collect();
    $installments = $installments ?? collect();
@endphp
<div class="space-y-8">

    <!-- بطاقة الترحيب والمستوى الحالي للطالب -->
    <div class="bg-gradient-to-r from-indigo-900 via-slate-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-emerald-500/20 text-emerald-300 text-xs px-3 py-1 rounded-full font-bold border border-emerald-400/30">
                    حساب طالب مفعل ونشط ✅
                </span>
                <span class="text-xs text-indigo-300 font-mono">{{ $user->student_code }}</span>
            </div>
            <h1 class="text-2xl font-black">مرحباً بك، {{ $user->name }} 👋</h1>
            <p class="text-xs text-slate-300 mt-1">
                المسار الحالي: <strong>{{ $enrollment?->level?->course?->name ?? 'دورات اللغة الإنجليزية' }}</strong> - 
                المستوى المعتمد: <span class="text-amber-400 font-bold">[{{ $enrollment?->level?->name ?? 'المستوى المعتمد' }}]</span>
            </p>
        </div>

        <!-- إحصائيات الطالب: رصيد النجوم والحساب المالي -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- رصيد النجوم والمكافآت التراكمية -->
            <div class="bg-amber-400/20 backdrop-blur-md border border-amber-400/30 p-3.5 rounded-2xl text-center min-w-[130px]">
                <span class="text-[11px] text-amber-200 block font-bold">النجوم والمكافآت</span>
                <div class="text-xl font-black font-mono mt-0.5 text-amber-300 flex items-center justify-center gap-1">
                    <span>⭐</span>
                    <span>{{ $user->stars_balance ?? 0 }}</span>
                </div>
                <span class="text-[10px] text-amber-200/80 block mt-0.5">رصيد تفاعلي تراكمي</span>
            </div>

            <!-- ملخص الحساب المالي السريع للطالب -->
            <div class="bg-white/10 backdrop-blur-md border border-white/20 p-3.5 rounded-2xl text-right min-w-[170px]">
                <span class="text-[11px] text-indigo-200 block font-bold">رصيد الحساب المالي</span>
                <div class="text-xl font-black font-mono mt-0.5 {{ ($account?->current_balance ?? 0) > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                    {{ $account?->formatted_balance ?? '0.00 ر.ي' }}
                </div>
                <span class="text-[10px] text-slate-300 block mt-0.5">
                    {{ ($account?->current_balance ?? 0) > 0 ? 'متبقي عليك قسط دراسي' : 'مسدد بالكامل' }}
                </span>
            </div>
        </div>
    </div>

    <!-- بطاقة استبيان وتقييم الأستاذ والحصة (Student Survey Quick Access) -->
    <div class="bg-gradient-to-r from-amber-500/10 via-indigo-500/10 to-emerald-500/10 rounded-3xl p-5 sm:p-6 border border-amber-300/40 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-amber-500 text-slate-950 text-[10px] font-black">
                    رأيك يصنع الفرق 🌟
                </span>
                <span class="text-xs font-bold text-slate-700">تقييمات الحصص والمدرسين</span>
            </div>
            <h2 class="text-base sm:text-lg font-black text-slate-900">شاركنا رأيك في حصة اليوم أو تجربة التعلم الأسبوعية</h2>
            <p class="text-xs text-slate-500">
                تقييمك سري ويساعدنا مباشرة في تحسين مستوى الشرح وتطوير الفصول التفاعلية.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto shrink-0">
            <a href="{{ route('surveys.create', ['type' => 'daily', 'group_id' => $studyGroup?->id, 'teacher_id' => $studyGroup?->teacher_id]) }}"
               class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-sm transition flex items-center justify-center gap-1.5">
                <span>⚡</span>
                <span>تقييم حصة اليوم (دقيقة)</span>
            </a>
            <a href="{{ route('student.surveys.index') }}"
               class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-800 font-bold text-xs border border-slate-200 shadow-2xs transition flex items-center justify-center gap-1.5">
                <span>📋</span>
                <span>كافة الاستبيانات</span>
            </a>
        </div>
    </div>

    <!-- بطاقة المجموعة الدراسية المسكن فيها الطالب (إن وُجدت) -->
    @if(isset($studyGroup) && $studyGroup)
        @php
            $myGroupEvals = \App\Models\GroupStudentEvaluation::where('study_group_id', $studyGroup->id)->where('student_id', $user->id)->get();
            $myAttendedCount = $myGroupEvals->whereIn('attendance_status', ['present', 'late'])->count();
            $groupTotalSessions = $studyGroup->sessions()->count();
            $myAttRate = $groupTotalSessions > 0 ? round(($myAttendedCount / $groupTotalSessions) * 100, 1) : 100;
        @endphp
        <div class="bg-gradient-to-r from-purple-950 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white shadow-xl border border-purple-800/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 text-[10px] font-bold border border-purple-400/30">
                        مجموعتك الدراسية المعتمدة 👥
                    </span>
                    <span class="text-xs text-amber-400 font-bold">{{ $studyGroup->course?->name }} • {{ $studyGroup->level?->name }}</span>
                </div>
                <h2 class="text-lg font-black text-white">{{ $studyGroup->name }}</h2>
                <div class="text-xs text-indigo-200 flex flex-wrap items-center gap-x-4 gap-y-1 pt-1">
                    <span>👨‍🏫 الأستاذ: <strong class="text-white">{{ $studyGroup->teacher?->name ?? 'إدارة المنصة' }}</strong></span>
                    <span>⏰ المواعيد: <strong class="text-amber-300">{{ $studyGroup->schedule_days ?: 'أوقات متفق عليها' }}</strong> ({{ $studyGroup->schedule_time ?: 'مرن' }})</span>
                    <span>🟢 نسبة حضورك: <strong class="text-emerald-300 font-mono font-bold">{{ $myAttRate }}%</strong> (حضور {{ $myAttendedCount }} من {{ $groupTotalSessions }} حصص)</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto shrink-0">
                @if($studyGroup->is_live)
                    <a href="{{ route('classroom.show', $studyGroup->room_id) }}" target="_blank" class="w-full md:w-auto px-6 py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 transition flex items-center justify-center gap-2 animate-pulse">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-950"></span>
                        <span>البث مفتوح الآن - انضم للفصل التفاعلي 🎥</span>
                    </a>
                @elseif($studyGroup->isPendingApproval())
                    <a href="{{ route('classroom.show', $studyGroup->room_id) }}" target="_blank" class="w-full md:w-auto px-6 py-3.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-lg transition flex items-center justify-center gap-2">
                        <span>⏳</span>
                        <span>طلب فتح البث قيد المراجعة (غرفة الانتظار)</span>
                    </a>
                @else
                    <a href="{{ route('classroom.show', $studyGroup->room_id) }}" target="_blank" class="w-full md:w-auto px-6 py-3.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 shadow-sm transition flex items-center justify-center gap-2">
                        <span>📡</span>
                        <span>دخول غرفة الانتظار / طلب فتح البث</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- حائط التفاعل والمحادثة اليومية للمجموعة (Daily Feed & Submissions) -->
        <div class="bg-white rounded-3xl border border-purple-200/80 p-6 shadow-sm space-y-5">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span>💬</span>
                        <span>حائط المحادثة اليومية والأنشطة وتسليمات الواجبات</span>
                    </h2>
                    <span class="text-xs text-slate-500">
                        مساحة التفاعل اليومي بين الطلاب والمعلم: رفع أوراق الإجابات، التسجيلات الصوتية، وأوراق العمل (بينما كتب المنهج ثابتة في الأسفل).
                    </span>
                </div>

                <!-- أزرار تفاعل الطالب: رفع ورقة إجابة + تسجيل صوتي -->
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <button type="button" onclick="openVoiceRecordModal()" class="flex-1 md:flex-none px-4 py-2.5 rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-black text-xs shadow-md shadow-purple-500/20 transition flex items-center justify-center gap-2">
                        <span class="text-base">🎙️</span>
                        <span>تسجيل صوتي للمجموعة</span>
                    </button>

                    <button type="button" onclick="openUploadAnswerModal()" class="flex-1 md:flex-none px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-500/20 transition flex items-center justify-center gap-2">
                        <span class="text-base">📸</span>
                        <span>رفع ورقة إجابة / صورة</span>
                    </button>

                    <span class="text-xs font-bold text-purple-700 bg-purple-50 px-3 py-1.5 rounded-xl font-mono">
                        {{ $studyGroup->activities->count() }} منشورات
                    </span>
                </div>
            </div>

            @if($studyGroup->activities->isEmpty())
                <div class="text-center py-8 text-slate-400 text-xs space-y-2 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <span class="text-4xl block">🎙️</span>
                    <p class="font-bold text-slate-600">حائط المجموعة بانتظار مشاركتك الأولى!</p>
                    <p class="text-slate-400">يمكنك الضغط على الأزرار أعلاه لتسجيل مقطع صوتي بالمايكروفون أو رفع ورقة إجابة الواجب ليراجعها الأستاذ.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($studyGroup->activities as $act)
                        <div class="p-4 rounded-2xl border {{ $act->isStudentSubmission() ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200 bg-slate-50/50' }} hover:shadow-md transition flex flex-col justify-between space-y-3">
                            <div class="space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg {{ $act->isStudentSubmission() ? 'bg-emerald-100 text-emerald-800' : 'bg-indigo-100 text-indigo-800' }}">
                                            {{ $act->type_icon }}
                                        </div>
                                        <div>
                                            <h4 class="font-black text-xs sm:text-sm text-slate-900 leading-tight">{{ $act->title }}</h4>
                                            <span class="text-[10px] text-slate-500 block mt-0.5">
                                                @if($act->isStudentSubmission())
                                                    🎒 بواسطة الطالب: <strong class="text-emerald-700">{{ $act->publisher?->name ?? 'طالب' }}</strong>
                                                @else
                                                    👨‍🏫 بواسطة الأستاذ: <strong class="text-indigo-700">{{ $act->publisher?->name ?? 'المعلم المسؤول' }}</strong>
                                                @endif
                                                • {{ $act->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $act->isStudentSubmission() ? 'bg-emerald-100 text-emerald-800' : ($act->type === 'audio' ? 'bg-purple-100 text-purple-800' : ($act->type === 'announcement' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800')) }}">
                                        {{ $act->type_label }}
                                    </span>
                                </div>

                                @if($act->instructions)
                                    <p class="text-xs text-slate-700 bg-white p-3 rounded-xl border border-slate-100 leading-relaxed">
                                        {{ $act->instructions }}
                                    </p>
                                @endif
                            </div>

                            <!-- معاينة الصورة المصغرة إن كانت صورة ورقة إجابة -->
                            @if($act->isImage() && $act->file_path)
                                <div class="space-y-1.5 pt-1">
                                    <div class="relative group cursor-pointer overflow-hidden rounded-xl border border-slate-200 bg-slate-900 aspect-video max-h-48 flex items-center justify-center" onclick="showImagePreview('{{ asset($act->file_path) }}', '{{ addslashes($act->title) }}')">
                                        <img src="{{ asset($act->file_path) }}" alt="{{ $act->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold gap-1.5 backdrop-blur-[2px]">
                                            <span>🔍</span>
                                            <span>انقر لتكبير ورقة الإجابة</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] text-slate-400">
                                        <span>📷 صورة مرفقة</span>
                                        <a href="{{ asset($act->file_path) }}" download target="_blank" class="text-emerald-700 font-bold hover:underline">تحميل الصورة الأصلية 📥</a>
                                    </div>
                                </div>
                            @endif

                            <!-- مشغل الصوت إن كان تسجيلاً صوتياً -->
                            @if($act->isAudio() && $act->file_path)
                                <div class="bg-purple-100/60 p-3 rounded-xl border border-purple-200 space-y-1.5">
                                    <div class="flex justify-between items-center text-[10px] text-purple-900 font-bold">
                                        <span>▶️ استمع للتسجيل الصوتي:</span>
                                        @if($act->file_size)
                                            <span class="font-mono text-purple-700 font-normal">({{ $act->file_size }})</span>
                                        @endif
                                    </div>
                                    <audio controls class="w-full h-8">
                                        <source src="{{ asset($act->file_path) }}">
                                        متصفحك لا يدعم مشغل الصوت.
                                    </audio>
                                </div>
                            @endif

                            <!-- زر تحميل الملف إن وُجد (مستند أو PDF) -->
                            @if($act->file_path && !$act->isAudio() && !$act->isImage())
                                <div class="pt-2">
                                    <a href="{{ asset($act->file_path) }}" download target="_blank" class="w-full py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs flex items-center justify-center gap-2 transition shadow-sm">
                                        <span>📥</span>
                                        <span>تحميل الملف ({{ $act->file_name ?? 'ورقة النشاط' }})</span>
                                        @if($act->file_size)
                                            <span class="text-[10px] opacity-75 font-mono">({{ $act->file_size }})</span>
                                        @endif
                                    </a>
                                </div>
                            @endif

                            <!-- زر أداء الكويز الإلكتروني للمجموعة أو استعراض نتيجته -->
                            @if($act->type === 'quiz' && $act->levelResource?->exam_id)
                                @php
                                    $studentAttempt = \App\Models\ExamAttempt::where('user_id', auth()->id())
                                        ->where('exam_id', $act->levelResource->exam_id)
                                        ->latest()
                                        ->first();
                                @endphp
                                <div class="pt-2">
                                    @if($studentAttempt && in_array($studentAttempt->status, ['submitted', 'graded', 'reviewed']))
                                        <a href="{{ route('student.exam.result', $studentAttempt->id) }}" class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs flex items-center justify-center gap-2 transition shadow-sm">
                                            <span>📊</span>
                                            <span>عرض نتيجة الكويز (تم تسليم الإجابة)</span>
                                        </a>
                                    @else
                                        <a href="{{ route('student.exam.take', ['exam' => $act->levelResource->exam_id, 'groupId' => $studyGroup->id]) }}" class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white font-black text-xs flex items-center justify-center gap-2 transition shadow-md">
                                            <span>🎯</span>
                                            <span>بدء أداء الكويز الإلكتروني الآن</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- شبكة المحتوى الأكاديمي والتعليم عن بعد -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- القسم الأول: المحاضرات والدروس المسجلة (فيديو) والمكتبة الرقمية للمستوى -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- محاضرات وفيديوهات شرح المستوى الأكاديمي -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                            <span>🎥</span>
                            <span>محاضرات وفيديوهات شرح المستوى ({{ $activeLevel?->name ?? 'المستوى المعتمد' }})</span>
                        </h2>
                        <span class="text-xs text-slate-400">شاهد المحاضرات المرئية وتدرب على النطق ومحتوى الكتاب</span>
                    </div>
                    @php $videoItems = $activeLevel?->resources?->where('type', 'video') ?? collect(); @endphp
                    <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-full font-mono">
                        {{ $videoItems->count() }} محاضرات
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($videoItems as $idx => $vid)
                        <div class="p-4 rounded-xl border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/20 transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ str_pad($vid->order_index ?: ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-sm text-slate-900">{{ $vid->title }}</h3>
                                    <div class="flex items-center gap-2 text-xs text-slate-400 mt-0.5">
                                        @if($vid->duration_minutes)
                                            <span>⏱️ المدة: {{ $vid->duration_minutes }} دقيقة</span>
                                        @endif
                                        @if($vid->description)
                                            <span>• {{ Str::limit($vid->description, 60) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($vid->external_url)
                                <a href="{{ $vid->external_url }}" target="_blank" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-xl transition text-center shrink-0">
                                    مشاهدة الآن ▶️
                                </a>
                            @elseif($vid->file_path)
                                <a href="{{ asset($vid->file_path) }}" target="_blank" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2 rounded-xl transition text-center shrink-0">
                                    تشغيل الفيديو ▶️
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs bg-slate-50 rounded-xl">
                            <span class="text-2xl block mb-1">🎬</span>
                            <span>سيتم رفع محاضرات الفيديو والشرح التفاعلي لهذا المستوى قريباً من الإدارة.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- الكتب والمقررات والمذكرات الرقمية (PDF) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span>📖</span>
                        <span>مكتبة المناهج والكتب الرقمية (PDF)</span>
                    </h2>
                    @php $bookItems = $activeLevel?->resources?->where('type', 'book') ?? collect(); @endphp
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full font-mono">
                        {{ $bookItems->count() }} كتب معتمدة
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse($bookItems as $bk)
                        <div class="p-4 rounded-xl border border-slate-200 flex items-center justify-between bg-slate-50 hover:bg-white hover:border-emerald-300 transition">
                            <div class="truncate pl-2">
                                <span class="font-bold text-xs text-slate-900 block truncate">{{ $bk->title }}</span>
                                <span class="text-[11px] text-slate-400 block font-mono">
                                    صيغة PDF • {{ $bk->file_size ?? 'كتاب معتمد' }}
                                </span>
                            </div>
                            @if($bk->file_path)
                                <a href="{{ asset($bk->file_path) }}" download target="_blank" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold px-3 py-1.5 rounded-lg transition shrink-0">
                                    تحميل 📥
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full p-4 text-center text-slate-400 text-xs bg-slate-50 rounded-xl">
                            <span>لا توجد كتب مرفوعة حالياً لهذا المستوى.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- التسجيلات الصوتية للاستماع والمحادثة (Audios) -->
            @php $audioItems = $activeLevel?->resources?->where('type', 'audio') ?? collect(); @endphp
            @if($audioItems->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                        <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                            <span>🎧</span>
                            <span>التسجيلات الصوتية للاستماع واللفظ (Listening Practice)</span>
                        </h2>
                        <span class="text-xs font-bold text-purple-700 bg-purple-50 px-2.5 py-0.5 rounded-full font-mono">
                            {{ $audioItems->count() }} تسجيلات
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($audioItems as $aud)
                            <div class="p-3.5 rounded-xl border border-purple-100 bg-purple-50/50 space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-purple-950 truncate">{{ $aud->title }}</span>
                                    @if($aud->file_size)
                                        <span class="text-[10px] text-purple-600 font-mono">{{ $aud->file_size }}</span>
                                    @endif
                                </div>
                                @if($aud->file_path)
                                    <audio controls class="w-full h-7">
                                        <source src="{{ asset($aud->file_path) }}" type="audio/mpeg">
                                        متصفحك لا يدعم مشغل الصوت.
                                    </audio>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- القسم الثاني: كشف الحساب الشخصي وسندات القبض -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="border-b border-slate-100 pb-3 mb-4 flex justify-between items-center">
                    <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span>🧾</span>
                        <span>سندات القبض المسددة</span>
                    </h2>
                    <a href="{{ route('accounting.statement', $user->id) }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline">
                        كشف الحساب الكامل
                    </a>
                </div>

                @if($vouchers->count() > 0)
                    <div class="space-y-3">
                        @foreach($vouchers as $v)
                            <div class="p-3.5 rounded-xl border border-emerald-100 bg-emerald-50/30 flex justify-between items-center">
                                <div>
                                    <span class="font-mono font-bold text-xs text-emerald-800 block">{{ $v->voucher_no }}</span>
                                    <span class="text-[11px] text-slate-500">{{ $v->created_at->format('Y/m/d') }} • {{ $v->statement }}</span>
                                </div>
                                <div class="text-left">
                                    <span class="font-mono font-bold text-xs text-slate-900 block">{{ $v->formatted_amount }}</span>
                                    <a href="{{ route('accounting.voucher.print', $v->id) }}" target="_blank" class="text-[11px] font-bold text-indigo-600 hover:underline">
                                        طباعة السند 🖨️
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-slate-400 text-xs">
                        لم يتم تسجيل أي سندات سداد حتى الآن.
                    </div>
                @endif
            </div>

            <!-- معلومات الدعم الفني والتواصل -->
            <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-5 text-xs text-indigo-950 space-y-2">
                <span class="font-bold block text-sm flex items-center gap-1.5">
                    <span>💬</span>
                    <span>الدعم الفني وشؤون الطلاب</span>
                </span>
                <p>تصلك كافة إشعارات الأكاديمية والواجبات وسندات السداد عبر رقم الواتساب المسجل: <strong>{{ $user->phone_whatsapp }}</strong>.</p>
            </div>
        </div>

    </div>

</div>

@if(isset($studyGroup) && $studyGroup)
    <!-- نافذة منبثقة 1: رفع ورقة إجابة / صورة نشاط للطالب -->
    <div id="uploadAnswerModal" class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">📸</span>
                    <div>
                        <h3 class="text-base font-black text-slate-900">تسليم ورقة إجابة أو صورة نشاط</h3>
                        <span class="text-xs text-slate-500">مجموعتك: <strong class="text-indigo-600">{{ $studyGroup->name }}</strong></span>
                    </div>
                </div>
                <button type="button" onclick="closeUploadAnswerModal()" class="text-slate-400 hover:text-slate-700 text-lg">✕</button>
            </div>

            <form action="{{ route('groups.activities.store', $studyGroup->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="type" value="worksheet">

                <div>
                    <label class="block font-bold text-slate-700 mb-1">عنوان ورقة الإجابة / الواجب:</label>
                    <input type="text" name="title" required value="حل واجب - {{ $user->name }}" 
                        placeholder="مثال: ورقة إجابة تمارين الأزمنة (Unit 2)"
                        class="w-full border border-slate-300 rounded-xl p-3 font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">اختر صورة ورقة الإجابة (أو التقط بالكاميرا مباشرة):</label>
                    <div class="border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-4 text-center cursor-pointer bg-slate-50 relative">
                        <input type="file" name="attachment" required accept="image/*,application/pdf" capture="environment" 
                            class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                            onchange="updateFileNamePreview(this)">
                        <div id="fileUploadPrompt" class="space-y-1">
                            <span class="text-3xl block">📷 / 📄</span>
                            <span class="font-bold text-slate-700 block">انقر لاختيار ملف من جهازك أو التقاط صورة بالكاميرا</span>
                            <span class="text-[11px] text-slate-400 block">يقبل الصور (JPG, PNG) أو ملفات PDF حتى 50MB</span>
                        </div>
                        <div id="fileNameDisplay" class="hidden text-xs font-bold text-emerald-700 font-mono mt-1"></div>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">ملاحظاتك أو استفسارك للأستاذ (اختياري):</label>
                    <textarea name="instructions" rows="2" 
                        placeholder="مثال: أستاذ قمت بحل التمرين كاملاً ولدي استفسار بالسؤال الثالث..."
                        class="w-full border border-slate-300 rounded-xl p-3 text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" onclick="closeUploadAnswerModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                        إلغاء
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black shadow-md transition flex items-center gap-1.5">
                        <span>📤</span>
                        <span>إرسال ورقة الإجابة الآن</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- نافذة منبثقة 2: تسجيل صوتي مباشر للمجموعة بالمايكروفون (Web Audio / MediaRecorder) -->
    <div id="voiceRecordModal" class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-5 text-center">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3 text-right">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🎙️</span>
                    <div>
                        <h3 class="text-base font-black text-slate-900">مسجل الصوت المباشر</h3>
                        <span class="text-xs text-slate-500">سجل قراءتك أو إجابتك الشفوية للمجموعة</span>
                    </div>
                </div>
                <button type="button" onclick="closeVoiceRecordModal()" class="text-slate-400 hover:text-slate-700 text-lg">✕</button>
            </div>

            <div class="space-y-4">
                <div class="text-right">
                    <label class="block font-bold text-xs text-slate-700 mb-1">عنوان التسجيل الصوتي:</label>
                    <input type="text" id="voiceRecordingTitle" value="تسجيل صوتي - {{ $user->name }}" 
                        class="w-full border border-slate-300 rounded-xl p-2.5 font-bold text-xs text-slate-800 outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <!-- شاشة عداد التسجيل والموجة الصوتية -->
                <div class="bg-slate-900 rounded-2xl p-6 text-white space-y-3 relative overflow-hidden">
                    <div id="recordingWave" class="hidden flex items-center justify-center gap-1.5 h-8">
                        <span class="w-1.5 h-6 bg-purple-400 rounded-full animate-bounce"></span>
                        <span class="w-1.5 h-8 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                        <span class="w-1.5 h-4 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                        <span class="w-1.5 h-7 bg-pink-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                        <span class="w-1.5 h-5 bg-amber-400 rounded-full animate-bounce" style="animation-delay: 0.25s"></span>
                    </div>

                    <div id="recordTimer" class="font-mono text-2xl font-black text-amber-400">00:00</div>
                    <div id="recordStatusText" class="text-xs text-slate-400">انقر على زر المايكروفون أدناه لبدء التسجيل</div>
                </div>

                <!-- مشغل معاينة الصوت المسجل قبل الإرسال -->
                <div id="audioPreviewContainer" class="hidden text-right space-y-1 bg-purple-50 p-3 rounded-2xl border border-purple-200">
                    <span class="text-[11px] font-bold text-purple-900 block">🎧 استمع للمعاينة قبل الإرسال:</span>
                    <audio id="audioPreviewPlayer" controls class="w-full h-8"></audio>
                </div>

                <!-- أزرار التحكم بالتسجيل -->
                <div class="flex items-center justify-center gap-3 pt-2">
                    <button type="button" id="btnStartRecord" onclick="startVoiceRecording()" class="px-5 py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow-lg shadow-rose-600/30 flex items-center gap-2 transition">
                        <span class="w-3 h-3 bg-white rounded-full animate-ping"></span>
                        <span>بدء التسجيل الآن 🎙️</span>
                    </button>

                    <button type="button" id="btnStopRecord" onclick="stopVoiceRecording()" class="hidden px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-900 text-white font-black text-xs shadow-md flex items-center gap-2 transition">
                        <span>⏹️</span>
                        <span>إيقاف التسجيل</span>
                    </button>

                    <button type="button" id="btnSubmitVoice" onclick="submitVoiceRecording('{{ route('groups.activities.store', $studyGroup->id) }}')" class="hidden px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-lg shadow-emerald-600/30 flex items-center gap-2 transition">
                        <span>🚀</span>
                        <span>إرسال للمجموعة والأستاذ</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- نافذة تكبير ومعاينة الصورة المنبثقة (Lightbox Modal) -->
<div id="imagePreviewModal" class="hidden fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex items-center justify-center p-4" onclick="closeImagePreview()">
    <div class="max-w-4xl max-h-[90vh] w-full flex flex-col items-center relative" onclick="event.stopPropagation()">
        <div class="w-full flex justify-between items-center text-white mb-2 px-2">
            <span id="previewModalTitle" class="text-sm font-bold truncate"></span>
            <button onclick="closeImagePreview()" class="text-white hover:text-rose-400 text-2xl font-black">✕</button>
        </div>
        <img id="previewModalImage" src="" alt="ورقة الإجابة" class="max-w-full max-h-[80vh] rounded-2xl shadow-2xl object-contain border border-white/10 bg-slate-950">
    </div>
</div>

<script>
// 1. نوافذ رفع ورقة الإجابة
function openUploadAnswerModal() {
    document.getElementById('uploadAnswerModal').classList.remove('hidden');
}
function closeUploadAnswerModal() {
    document.getElementById('uploadAnswerModal').classList.add('hidden');
}
function updateFileNamePreview(input) {
    if (input.files && input.files[0]) {
        document.getElementById('fileUploadPrompt').classList.add('hidden');
        const display = document.getElementById('fileNameDisplay');
        display.classList.remove('hidden');
        display.innerHTML = '✅ تم اختيار: ' + input.files[0].name;
    }
}

// 2. مسجل الصوت المباشر من المتصفح (MediaRecorder API)
let mediaRecorder;
let audioChunks = [];
let recordedAudioBlob = null;
let timerInterval = null;
let recordSeconds = 0;

function openVoiceRecordModal() {
    document.getElementById('voiceRecordModal').classList.remove('hidden');
    resetVoiceRecorderState();
}
function closeVoiceRecordModal() {
    if (mediaRecorder && mediaRecorder.state === 'recording') {
        mediaRecorder.stop();
    }
    clearInterval(timerInterval);
    document.getElementById('voiceRecordModal').classList.add('hidden');
}
function resetVoiceRecorderState() {
    audioChunks = [];
    recordedAudioBlob = null;
    recordSeconds = 0;
    clearInterval(timerInterval);
    document.getElementById('recordTimer').innerText = '00:00';
    document.getElementById('recordStatusText').innerText = 'انقر على زر المايكروفون لبدء التسجيل';
    document.getElementById('recordingWave').classList.add('hidden');
    document.getElementById('audioPreviewContainer').classList.add('hidden');
    document.getElementById('btnStartRecord').classList.remove('hidden');
    document.getElementById('btnStopRecord').classList.add('hidden');
    document.getElementById('btnSubmitVoice').classList.add('hidden');
}

async function startVoiceRecording() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];

        mediaRecorder.ondataavailable = (e) => {
            if (e.data.size > 0) audioChunks.push(e.data);
        };

        mediaRecorder.onstop = () => {
            recordedAudioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            const audioUrl = URL.createObjectURL(recordedAudioBlob);
            const player = document.getElementById('audioPreviewPlayer');
            player.src = audioUrl;
            document.getElementById('audioPreviewContainer').classList.remove('hidden');
            document.getElementById('btnSubmitVoice').classList.remove('hidden');
            document.getElementById('recordStatusText').innerText = '✅ تم إنهاء التسجيل بنجاح! يمكنك الاستماع أو الإرسال.';
            // إيقاف مسارات المايكروفون لتحرير العتاد
            stream.getTracks().forEach(track => track.stop());
        };

        mediaRecorder.start();

        // واجهة التسجيل الجاري
        document.getElementById('btnStartRecord').classList.add('hidden');
        document.getElementById('btnStopRecord').classList.remove('hidden');
        document.getElementById('recordingWave').classList.remove('hidden');
        document.getElementById('recordStatusText').innerText = '🔴 جاري التسجيل الصوتي الآن... تحدث بوضوح.';

        recordSeconds = 0;
        timerInterval = setInterval(() => {
            recordSeconds++;
            const m = Math.floor(recordSeconds / 60);
            const s = recordSeconds % 60;
            document.getElementById('recordTimer').innerText = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        }, 1000);

    } catch (err) {
        alert('تعذر الوصول إلى المايكروفون. يرجى التأكد من منح الإذن للمتصفح باستخدام المايكروفون.');
        console.error('Microphone error:', err);
    }
}

function stopVoiceRecording() {
    if (mediaRecorder && mediaRecorder.state === 'recording') {
        mediaRecorder.stop();
        clearInterval(timerInterval);
        document.getElementById('recordingWave').classList.add('hidden');
        document.getElementById('btnStopRecord').classList.add('hidden');
    }
}

async function submitVoiceRecording(targetUrl) {
    if (!recordedAudioBlob) {
        alert('لا يوجد تسجيل صوتي للإرسال!');
        return;
    }

    const title = document.getElementById('voiceRecordingTitle').value || 'تسجيل صوتي';
    const submitBtn = document.getElementById('btnSubmitVoice');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ جاري الرفع...';

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('title', title);
    formData.append('type', 'audio');
    formData.append('attachment', recordedAudioBlob, 'voice_' + Date.now() + '.webm');

    try {
        const response = await fetch(targetUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        if (data.success) {
            alert(data.message || 'تم إرسال التسجيل الصوتي بنجاح!');
            window.location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء إرسال التسجيل.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '🚀 إعادة المحاولة';
        }
    } catch (e) {
        console.error(e);
        // في حال كان الرد إعادة توجيه عادية
        window.location.reload();
    }
}

// 3. معاينة وتكبير صور أوراق الإجابات (Lightbox)
function showImagePreview(src, title) {
    document.getElementById('previewModalImage').src = src;
    document.getElementById('previewModalTitle').innerText = title || 'ورقة إجابة الطالب';
    document.getElementById('imagePreviewModal').classList.remove('hidden');
}
function closeImagePreview() {
    document.getElementById('imagePreviewModal').classList.add('hidden');
}
</script>
@endsection
