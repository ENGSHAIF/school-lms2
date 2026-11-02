<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>الفصل التفاعلي المباشر (Air Class Live) - {{ $title ?? ($booking->topic ?? 'محادثة حية') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        if (window.pdfjsLib) {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }
    </script>
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #0b1120; color: #f8fafc; }
        .star-pop { animation: pop 0.4s ease-out; }
        @keyframes pop {
            0% { transform: scale(0.5); opacity: 0; }
            80% { transform: scale(1.3); }
            100% { transform: scale(1); opacity: 1; }
        }
        .speaking-glow { box-shadow: 0 0 15px rgba(16, 185, 129, 0.6); border-color: #10b981 !important; }
        .hand-raised-glow { animation: handPulse 1.2s infinite; }
        @keyframes handPulse {
            0%, 100% { border-color: #f59e0b; box-shadow: 0 0 8px rgba(245, 158, 11, 0.5); }
            50% { border-color: #fbbf24; box-shadow: 0 0 18px rgba(251, 191, 36, 0.9); }
        }
        .laser-glow {
            box-shadow: 0 0 15px 4px rgba(239, 68, 68, 0.9), 0 0 30px 8px rgba(239, 68, 68, 0.4);
        }
        .cursor-pen { cursor: crosshair; }
        .cursor-highlighter { cursor: crosshair; }
        .cursor-eraser { cursor: cell; }
        .cursor-laser { cursor: none !important; }
    </style>
</head>
<body class="min-h-screen lg:h-screen flex flex-col overflow-y-auto lg:overflow-hidden select-none">

    <!-- شريط تنبيه تشغيل الصوت إذا تم حظره تلقائياً من المتصفح -->
    <div id="audioUnblockBanner" class="hidden bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 px-4 py-2 text-xs font-bold flex justify-between items-center z-50 shadow-md">
        <span class="flex items-center gap-1.5">
            <span>🔊</span>
            <span>انقر هنا لتفعيل سماع أصوات ومشاركات الطلاب والمعلم في الفصل الحي تلقائياً.</span>
        </span>
        <button onclick="unblockAudio()" class="bg-slate-950 hover:bg-slate-900 text-white px-3.5 py-1 rounded-lg text-xs font-black shadow transition">
            تفعيل سماع الصوت 🔊
        </button>
    </div>

    <!-- نافذة إشعار المعلم اللحظية بطلب انضمام طالب للبث الحي (Toast + Chime 🔔) -->
    @if($isTeacher)
        <div id="admissionAlertToast" class="hidden fixed top-14 left-1/2 -translate-x-1/2 z-50 bg-slate-900/95 backdrop-blur-md border-2 border-amber-500 text-white rounded-2xl shadow-2xl p-3.5 max-w-md w-[92%] transition-all duration-300">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="text-2xl animate-bounce shrink-0">🔔</span>
                    <div class="min-w-0">
                        <h4 class="font-black text-xs text-amber-300 flex items-center gap-1">
                            <span>طلب انضمام جديد للبث</span>
                            <span class="text-[10px] text-amber-400/80 font-normal">(غرفة الانتظار)</span>
                        </h4>
                        <p id="toastStudentName" class="text-xs text-white font-bold truncate mt-0.5">الطالب: ...</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" id="btnToastAdmit" onclick="admitCurrentToastStudent()" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black px-3 py-1.5 rounded-xl shadow transition">
                        قبول ✓
                    </button>
                    <button type="button" id="btnToastReject" onclick="rejectCurrentToastStudent()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold px-2.5 py-1.5 rounded-xl border border-slate-700 transition">
                        رفض ✕
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- شاشة انتظار الطالب حتى يوافق المعلم على دخوله للبث الحي (Waiting Room Modal) -->
    @if(! $isTeacher)
        <div id="studentWaitingOverlay" class="hidden fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-md flex flex-col items-center justify-center p-4 text-center">
            <div class="max-w-md w-full space-y-6 bg-slate-900/90 border border-slate-800 p-6 sm:p-8 rounded-3xl shadow-2xl">
                <div class="relative w-24 h-24 mx-auto flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full bg-amber-500/20 radar-pulse"></div>
                    <div class="relative z-10 w-16 h-16 rounded-full bg-gradient-to-tr from-amber-600 to-amber-400 flex items-center justify-center text-3xl shadow-xl">
                        <span>🔔</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <h2 id="overlayWaitingTitle" class="text-lg sm:text-xl font-black text-white">بانتظار موافقة المعلم للدخول إلى البث... 🔔</h2>
                    <p id="overlayWaitingDesc" class="text-xs text-slate-300 leading-relaxed">
                        تم إرسال طلب انضمامك إلى المعلم. يرجى الانتظار، سيتم إدخالك إلى الفصل التفاعلي الحي تلقائياً بمجرد موافقة الأستاذ!
                    </p>
                </div>

                <div class="p-3.5 bg-slate-950 border border-slate-800 rounded-2xl flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-bold">حالة الانضمام:</span>
                    <span id="overlayWaitingBadge" class="bg-amber-500/20 text-amber-300 font-bold px-3 py-1 rounded-full border border-amber-500/30 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                        <span>قيد المراجعة لدى المعلم</span>
                    </span>
                </div>

                <div class="flex items-center justify-center gap-3 pt-2">
                    <button id="btnRetryAdmission" type="button" onclick="retryAdmissionRequest()" class="hidden bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow">
                        إعادة إرسال طلب الانضمام 🔄
                    </button>
                    <a href="{{ route('student.portal') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold px-4 py-2 rounded-xl border border-slate-700 transition">
                        العودة لبوابة الطالب ←
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- شريط الأدوات العلوي للفصل التفاعلي -->
    <header class="bg-slate-900/95 backdrop-blur border-b border-slate-800 px-3 sm:px-5 py-2.5 flex flex-wrap justify-between items-center gap-2 z-20 shrink-0">
        <div class="flex items-center gap-2 sm:gap-3">
            <span class="w-3 h-3 {{ (isset($group) && $group->is_live) || (isset($booking) && $booking->is_live) ? 'bg-red-500 animate-ping' : 'bg-slate-500' }} rounded-full"></span>
            <span class="bg-red-500/20 text-red-400 text-xs px-2.5 py-0.5 rounded-full font-bold border border-red-500/30">
                {{ isset($isGroup) && $isGroup ? 'فصل جماعي حي • Live Group' : 'مباشر حي • Live 1-on-1' }}
            </span>
            <h1 class="text-xs sm:text-sm font-black text-white truncate max-w-[200px] sm:max-w-xs md:max-w-md">
                {{ $title ?? ($booking->topic ?? 'الفصل التفاعلي المباشر') }}
            </h1>
        </div>

        <!-- أزرار التحكم بالبث الحي وإدارة الحضور والمشاركات -->
        <div class="flex items-center flex-wrap gap-2">
            
            <!-- زر طلبات الانضمام المعلقة بغرفة الانتظار (للمعلم والإدارة) -->
            @if($isTeacher)
                <button type="button" onclick="toggleSidePanel('participants')" id="btnHeaderAdmission" class="hidden bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-xs font-bold px-3 py-1.5 rounded-xl border border-amber-500/40 flex items-center gap-1.5 transition animate-pulse">
                    <span>🔔</span>
                    <span>طلبات الانضمام (<strong id="headerAdmissionCount" class="font-mono text-amber-200">0</strong>)</span>
                </button>
            @endif

            <!-- زر لوحة تحكم المجموعة وبطاقات الطلاب (للمعلم والإدارة) -->
            @if($isTeacher && isset($group) && $group)
                <a href="{{ route('admin.groups.dashboard', $group->id) }}" target="_blank" class="bg-slate-800 hover:bg-slate-700 text-amber-300 text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-700 flex items-center gap-1.5 transition" title="فتح لوحة تحكم المجموعة وبطاقات الطلاب">
                    <span>🎛️</span>
                    <span class="hidden md:inline">لوحة التحكم</span>
                </a>
            @endif

            <!-- زر قائمة الحاضرين في البث الحي مع تنبيه رفع اليد -->
            <button type="button" onclick="toggleSidePanel('participants')" id="btnHeaderParticipants" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-700 flex items-center gap-1.5 transition relative">
                <span>👥</span>
                <span>الحاضرون (<strong id="headerParticipantsCount" class="font-mono text-emerald-400">1</strong>)</span>
                <span id="headerHandBadge" class="hidden px-1.5 py-0.2 rounded-full bg-amber-400 text-slate-950 text-[10px] font-black animate-bounce">
                    ✋ طلب كلمة
                </span>
            </button>

            <!-- زر رفع اليد لطلب الكلمة (للطلاب) -->
            @if(auth()->user()->isStudent())
                <button type="button" onclick="toggleHandRaise()" id="btnRaiseHand" class="bg-slate-800 hover:bg-slate-700 text-amber-300 text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-700 flex items-center gap-1.5 transition">
                    <span id="iconRaiseHand">✋</span>
                    <span id="txtRaiseHand">طلب الكلمة</span>
                </button>
            @endif

            <!-- زر سياسة الغرفة: مسموح للكل / بإذن الأستاذ (للمعلم والإدارة) -->
            @if($isTeacher)
                <button type="button" onclick="toggleRoomPolicy()" id="btnPolicyToggle" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-700 flex items-center gap-1.5 transition" title="تبديل سياسة التحدث في الجلسة">
                    <span id="iconPolicy">📢</span>
                    <span id="txtPolicy">الكلام: مسموح</span>
                </button>
            @endif

            <!-- أزرار تشغيل/كتم المايكروفون والكاميرا الخاصة بي -->
            <div class="flex items-center gap-1 bg-slate-950 p-1 rounded-xl border border-slate-800">
                <button type="button" onclick="toggleLocalMic()" id="btnMyMic" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-2.5 py-1 rounded-lg flex items-center gap-1 transition shadow-sm" title="كتم/تشغيل المايك الخاص بي">
                    <span id="iconMyMic">🎙️</span>
                    <span id="txtMyMic" class="hidden md:inline">المايك</span>
                </button>
                <button type="button" onclick="toggleLocalCam()" id="btnMyCam" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold px-2.5 py-1 rounded-lg flex items-center gap-1 border border-slate-700 transition" title="إيقاف/تشغيل الكاميرا الخاصة بي">
                    <span id="iconMyCam">📷</span>
                    <span id="txtMyCam" class="hidden md:inline">الكاميرا</span>
                </button>
            </div>

            <!-- أزرار فتح وغلق البث الحي للمعلم -->
            @if($isTeacher)
                @if(isset($group) && $group)
                    @if($group->is_live)
                        <form method="POST" action="{{ route('groups.live.stop', $group->id) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-[11px] font-bold bg-rose-600 hover:bg-rose-500 text-white px-2.5 py-1.5 rounded-xl transition shadow-sm">
                                ⏹️ إغلاق البث
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('groups.live.start', $group->id) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-[11px] font-black bg-emerald-500 hover:bg-emerald-400 text-slate-950 px-3 py-1.5 rounded-xl transition shadow-md flex items-center gap-1">
                                <span>📡</span>
                                <span>فتح البث الآن</span>
                            </button>
                        </form>
                    @endif
                @elseif(isset($booking) && $booking)
                    <form method="POST" action="{{ route('classroom.booking.toggle_live', $booking->id) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-[11px] font-bold {{ $booking->is_live ? 'bg-rose-600 text-white' : 'bg-emerald-500 text-slate-950' }} px-3 py-1.5 rounded-xl transition">
                            {{ $booking->is_live ? '⏹️ إغلاق البث' : '📡 فتح البث' }}
                        </button>
                    </form>
                @endif
            @endif

            <!-- زر المحادثة وأوراق الإجابة -->
            <button type="button" onclick="toggleSidePanel('chat')" id="btnToggleChat" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-3 py-1.5 rounded-xl border border-indigo-400/30 flex items-center gap-1.5 transition shadow-sm relative">
                <span>💬</span>
                <span>المحادثة</span>
                <span id="unreadMessagesDot" class="hidden w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
            </button>

            <!-- زر بنك المناهج والكتب -->
            <button type="button" onclick="toggleSidePanel('resources')" id="btnToggleResources" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-700 flex items-center gap-1.5 transition">
                <span>📚</span>
                <span class="hidden sm:inline">المنهج</span>
            </button>

            <!-- عداد النجوم -->
            <div class="bg-slate-800 border border-amber-500/40 px-3 py-1.5 rounded-xl flex items-center gap-1.5">
                <span>⭐</span>
                <span id="starsCount" class="text-xs font-black text-amber-400 font-mono">0</span>
            </div>

            <!-- العداد التنازلي للحصة -->
            <div class="bg-slate-800 border border-slate-700 px-3 py-1.5 rounded-xl font-mono text-xs font-bold text-emerald-400 flex items-center gap-1">
                <span>⏱️</span>
                <span id="sessionTimer">25:00</span>
            </div>

            <!-- زر مغادرة الفصل -->
            <a href="{{ auth()->user()->isStudent() ? route('student.portal') : (auth()->user()->isTeacher() ? route('teacher.dashboard') : route('admin.groups.index')) }}" 
               onclick="return confirm('هل ترغب بمغادرة الفصل التفاعلي المباشر الآن؟');"
               class="text-xs text-rose-400 hover:text-white bg-slate-800 hover:bg-rose-700 px-3 py-1.5 rounded-xl transition font-bold">
                مغادرة ✕
            </a>
        </div>
    </header>

    @php
        $levelResources = collect();
        if (isset($group) && $group && $group->level) {
            $levelResources = $group->level->resources ?? collect();
            if ($group->course && $group->course->resources) {
                $levelResources = $levelResources->merge($group->course->resources);
            }
        } elseif (isset($booking) && $booking && $booking->student) {
            $enrollment = $booking->student->enrollments()->where('status', 'active')->first();
            $levelResources = $enrollment?->level?->resources ?? collect();
            if ($enrollment?->course && $enrollment->course->resources) {
                $levelResources = $levelResources->merge($enrollment->course->resources);
            }
        }
        $isTeacherOrStaff = auth()->user() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor() || auth()->user()->isTeacher());
        if (! $isTeacherOrStaff) {
            $levelResources = $levelResources->filter(fn($r) => $r->isForStudent());
        }
        $groupActivities = isset($group) && $group ? ($group->activities ?? collect()) : collect();
        $groupBooks = $levelResources->where('type', 'book');
        $groupVideos = $levelResources->where('type', 'video');
    @endphp

    <!-- مساحة العمل الكلية: الجانب الأيمن (محادثة وحضور ومنهج وتحكم) + الجزء الوسط الكبير (شاشة الشرح والسبورة نفس الزوم) -->
    <main class="flex-1 flex flex-col lg:flex-row overflow-hidden p-2 sm:p-3 gap-2 sm:gap-3">

        <!-- 1. العمود الأيمن المخصص: المحادثة + الحضور والكاميرات + المنهج + أدوات التحكم والمغادرة -->
        <aside id="liveInteractivePanel" class="w-full lg:w-[350px] xl:w-[390px] shrink-0 bg-slate-900 border border-slate-800 rounded-2xl flex flex-col overflow-hidden shadow-xl z-20">
            
            <!-- ترويسة التبويبات الثلاثة (المحادثة / الحضور / المنهج) -->
            <div class="p-2 bg-slate-950 border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-800 text-xs font-bold w-full">
                    <button type="button" onclick="switchLiveTab('chat')" id="tabBtnChat" class="flex-1 py-1.5 rounded-lg bg-indigo-600 text-white transition flex items-center justify-center gap-1 shadow-sm text-center">
                        <span>💬</span>
                        <span>المحادثة</span>
                        <span id="unreadMessagesDot" class="hidden w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    </button>
                    <button type="button" onclick="switchLiveTab('participants')" id="tabBtnParticipants" class="flex-1 py-1.5 rounded-lg text-slate-400 hover:text-white transition flex items-center justify-center gap-1 text-center relative">
                        <span>👥</span>
                        <span>الحضور (<span id="tabCountBadge">1</span>)</span>
                    </button>
                    <button type="button" onclick="switchLiveTab('resources')" id="tabBtnResources" class="flex-1 py-1.5 rounded-lg text-slate-400 hover:text-white transition flex items-center justify-center gap-1 text-center">
                        <span>📚</span>
                        <span>المنهج</span>
                    </button>
                </div>
            </div>

            <!-- المحتوى الداخلي المتغير للتبويب (قابل للتمرير السلس) -->
            <div class="flex-1 overflow-y-auto">
                
                <!-- 1. تبويب المحادثة المباشرة وتسليم أوراق الإجابات والتسجيلات -->
                <div id="liveTabChat" class="h-full flex flex-col justify-between">
                    <div id="liveChatMessagesContainer" class="flex-1 p-3 space-y-2.5 overflow-y-auto text-xs">
                        <div class="bg-indigo-950/60 border border-indigo-800/60 rounded-2xl p-3 text-indigo-200 space-y-1">
                            <div class="font-bold flex items-center gap-1.5 text-indigo-300 text-xs">
                                <span>👋</span>
                                <span>محادثة الفصل التفاعلي المباشر</span>
                            </div>
                            <p class="text-[11px] leading-relaxed text-indigo-200/80">
                                الرسائل النصية، والتسجيلات الصوتية 🎙️، وأوراق الإجابة 📸 تظهر لجميع أعضاء المجموعة والمعلم لحظياً.
                            </p>
                        </div>

                        @if(isset($chatMessages) && $chatMessages->isNotEmpty())
                            @foreach($chatMessages as $msg)
                                @php $isMine = ($msg->user_id === auth()->id()); @endphp
                                <div id="chatMsg_{{ $msg->id }}" class="p-2.5 rounded-xl border {{ $msg->user?->isStudent() ? 'border-emerald-800/40 bg-emerald-950/20' : 'border-indigo-800/40 bg-indigo-950/20' }} space-y-1">
                                    <div class="flex justify-between items-center text-[10px]">
                                        <span class="font-bold flex items-center gap-1 text-slate-300">
                                            <span>{{ $msg->user?->isStudent() ? '🎒' : '👨‍🏫' }}</span>
                                            <span>{{ $msg->user?->name ?? 'مشارك' }} {{ $isMine ? '(أنت)' : '' }}</span>
                                        </span>
                                        <span class="text-slate-500 font-mono">{{ $msg->created_at->format('H:i') }}</span>
                                    </div>
                                    @if($msg->content)
                                        <div class="text-xs text-slate-200 leading-relaxed">{{ $msg->content }}</div>
                                    @endif
                                    @if(($msg->type === 'image' || $msg->type === 'worksheet') && $msg->file_path)
                                        <div class="relative group mt-1">
                                            <div class="cursor-pointer overflow-hidden rounded-xl border border-slate-700 bg-black aspect-video max-h-32 flex items-center justify-center" onclick="openLiveImageModal('{{ asset($msg->file_path) }}', '{{ addslashes($msg->content ?? '') }}')">
                                                <img src="{{ asset($msg->file_path) }}" class="w-full h-full object-cover">
                                            </div>
                                            @if($isTeacher)
                                                <button type="button" onclick="openResourceInWorkspace('{{ asset($msg->file_path) }}', 'ورقة إجابة: {{ addslashes($msg->user?->name ?? '') }}', 'image')" class="mt-1 w-full bg-emerald-600/90 hover:bg-emerald-600 text-white text-[10px] font-bold py-1 px-2 rounded-lg transition flex items-center justify-center gap-1 shadow">
                                                    <span>🎨</span>
                                                    <span>فتح للشرح والتصحيح بالسبورة</span>
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                    @if($msg->type === 'audio' && $msg->file_path)
                                        <div class="bg-purple-950/40 p-1.5 rounded-xl border border-purple-800/40 mt-1">
                                            <audio controls src="{{ asset($msg->file_path) }}" class="w-full h-7"></audio>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- مسجل الصوت المصغر -->
                    <div id="liveVoiceBar" class="hidden p-2.5 bg-slate-950 border-t border-slate-800 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span id="liveVoiceStatus" class="text-xs text-rose-400 font-bold flex items-center gap-1.5 animate-pulse">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span>تسجيل صوتك الآن...</span>
                            </span>
                            <span id="liveVoiceTimer" class="font-mono text-xs font-black text-amber-400">00:00</span>
                        </div>
                        <div id="liveVoicePreviewContainer" class="hidden">
                            <audio id="liveVoicePreview" controls class="w-full h-7 mb-1.5"></audio>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" id="btnLiveVoiceStop" onclick="stopLiveVoiceRecording()" class="flex-1 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                                ⏹️ إيقاف
                            </button>
                            <button type="button" id="btnLiveVoiceSend" onclick="sendLiveVoiceRecording()" class="hidden flex-1 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs transition shadow">
                                🚀 إرسال
                            </button>
                            <button type="button" onclick="cancelLiveVoiceRecording()" class="px-2 py-1 text-slate-400 hover:text-white text-xs">إلغاء</button>
                        </div>
                    </div>

                    <!-- صندوق الإدخال والأزرار السريعة -->
                    <div class="p-2.5 bg-slate-950 border-t border-slate-800 space-y-1.5 shrink-0">
                        <div class="flex items-center gap-1.5">
                            <input type="file" id="liveAnswerFileInput" accept="image/*,application/pdf" capture="environment" class="hidden" onchange="handleLiveAnswerUpload(this)">
                            
                            <button type="button" onclick="triggerLiveAnswerUpload()" class="flex-1 py-1 px-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-emerald-400 border border-slate-700 font-bold text-[10px] flex items-center justify-center gap-1 transition" title="رفع صورة أو ورقة إجابة">
                                <span>📸</span>
                                <span>رفع ورقة إجابة</span>
                            </button>

                            <button type="button" onclick="startLiveVoiceRecording()" class="flex-1 py-1 px-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-purple-400 border border-slate-700 font-bold text-[10px] flex items-center justify-center gap-1 transition" title="تسجيل صوتي">
                                <span>🎙️</span>
                                <span>تسجيل صوتي</span>
                            </button>
                        </div>

                        <form id="liveChatForm" onsubmit="sendLiveChatMessage(event)" class="flex gap-1.5">
                            <input type="text" id="liveChatInput" placeholder="اكتب سؤالك أو مشاركتك..." 
                                class="flex-1 bg-slate-900 border border-slate-800 rounded-xl px-2.5 py-1.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1 shrink-0">
                                <span>✈️</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 2. تبويب الحضور والفيديو وإدارة الجلسة -->
                <div id="liveTabParticipants" class="hidden p-3 space-y-3 text-xs">
                    
                    <!-- أدوات المعلم والمشرف لإدارة الفصل -->
                    @if($isTeacher)
                        <div class="bg-indigo-950/60 border border-indigo-800/80 p-2.5 rounded-2xl space-y-2 shadow-inner">
                            <div class="flex justify-between items-center text-[11px] font-bold text-indigo-200">
                                <span class="flex items-center gap-1">
                                    <span>👑</span>
                                    <span>إدارة الفصل والمشاركين:</span>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-1.5 text-[10px]">
                                <button type="button" onclick="toggleRoomPolicy()" class="p-1.5 bg-slate-900 hover:bg-slate-800 rounded-xl border border-indigo-700/50 font-bold text-white flex items-center justify-center gap-1 transition">
                                    <span id="drawerPolicyIcon">📢</span>
                                    <span id="drawerPolicyText">الكلام مسموح</span>
                                </button>
                                <button type="button" onclick="muteAllParticipants()" class="p-1.5 bg-rose-950/60 hover:bg-rose-900/60 rounded-xl border border-rose-800 font-bold text-rose-300 flex items-center justify-center gap-1 transition">
                                    <span>🔇</span>
                                    <span>كتم الجميع</span>
                                </button>
                                <button type="button" onclick="toggleRequiresAdmission()" id="btnToggleAdmission" class="p-1.5 bg-slate-900 hover:bg-slate-800 rounded-xl border border-amber-500/50 font-bold text-amber-300 flex items-center justify-center gap-1 transition col-span-2">
                                    <span id="iconAdmissionToggle">🔔</span>
                                    <span id="txtAdmissionToggle">غرفة الانتظار: مفعلة 🔔</span>
                                </button>
                            </div>
                        </div>

                        <!-- بطاقة طلبات الانضمام المعلقة (غرفة الانتظار) -->
                        <div id="admissionPendingContainer" class="hidden bg-amber-950/50 border border-amber-500/80 p-2.5 rounded-2xl space-y-2 shadow">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-amber-300 text-xs flex items-center gap-1">
                                    <span>🔔</span>
                                    <span>طلبات بانتظار موافقتك:</span>
                                </span>
                                <button type="button" onclick="admitAllStudents()" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[10px] px-2 py-0.5 rounded-lg transition">
                                    قبول الكل ✓
                                </button>
                            </div>
                            <div id="admissionPendingList" class="space-y-1"></div>
                        </div>
                    @endif

                    <!-- طلبات رفع اليد -->
                    <div id="handRaisedContainer" class="hidden bg-amber-950/50 border border-amber-600/70 p-2.5 rounded-2xl space-y-1.5">
                        <span class="font-bold text-amber-300 text-xs flex items-center gap-1">
                            <span>✋</span>
                            <span>طلاب يطلبون الإذن بالحديث:</span>
                        </span>
                        <div id="handRaisedList" class="space-y-1"></div>
                    </div>

                    <!-- كاميرات الفيديو (المعلم + أنا + الطلاب المتصلون) -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-xs font-bold text-slate-300">
                            <span>📹 كاميرات الفيديو والمشاركين:</span>
                            <span class="text-[10px] text-emerald-400 font-mono">متصل (<span id="remoteCountBadge">0</span>)</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <!-- كاميرا المعلم -->
                            <div id="teacherCard" class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden relative shadow min-h-[110px] max-h-[140px] flex flex-col justify-between p-2">
                                <div class="flex justify-between items-center z-10 text-[10px]">
                                    <span class="font-bold bg-slate-950/80 px-1.5 py-0.5 rounded text-indigo-300 truncate max-w-[90px]">
                                        👨‍🏫 {{ $teacher?->name ?? ($booking->teacher?->name ?? 'المعلم') }}
                                    </span>
                                    <span id="teacherStatusBadge" class="text-emerald-400 font-bold">● حي</span>
                                </div>
                                <div class="absolute inset-0 flex items-center justify-center bg-slate-950">
                                    <video id="teacherVideo" autoplay playsinline class="w-full h-full object-cover hidden"></video>
                                    <div id="teacherAvatar" class="text-center">
                                        <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-lg mx-auto mb-0.5 shadow">👨‍🏫</div>
                                        <span class="text-[10px] text-slate-300 font-bold truncate max-w-[100px] block">{{ $teacher?->name ?? ($booking->teacher?->name ?? 'المعلم') }}</span>
                                    </div>
                                </div>
                                <div class="z-10 flex justify-between items-center text-[9px] text-slate-300">
                                    <span id="teacherMicBadge" class="bg-black/60 px-1 rounded">🎙️ المايك شغال</span>
                                </div>
                            </div>

                            <!-- كاميرا المستخدم الحالي (أنا) -->
                            <div id="mySelfCard" class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden relative shadow min-h-[110px] max-h-[140px] flex flex-col justify-between p-2">
                                <div class="flex justify-between items-center z-10 text-[10px]">
                                    <span class="font-bold bg-slate-950/80 px-1.5 py-0.5 rounded text-emerald-300">
                                        {{ auth()->user()->isStudent() ? '🎓 أنا (طالب)' : '👨‍🏫 أنا' }}
                                    </span>
                                    <span id="myLiveBadge" class="text-emerald-400 font-bold">● حي</span>
                                </div>
                                <div class="absolute inset-0 flex items-center justify-center bg-slate-950">
                                    <video id="mySelfVideo" autoplay playsinline muted class="w-full h-full object-cover hidden"></video>
                                    <div id="mySelfAvatar" class="text-center">
                                        <div class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center text-lg mx-auto mb-0.5 shadow">
                                            {{ auth()->user()->isStudent() ? '🎓' : '👨‍🏫' }}
                                        </div>
                                        <span class="text-[10px] text-slate-300 font-bold truncate max-w-[100px] block">{{ auth()->user()->name }}</span>
                                    </div>
                                </div>
                                <div class="z-10 flex justify-between items-center text-[9px]">
                                    <span id="mySelfMicStatus" class="bg-black/60 text-emerald-300 px-1 rounded">🎙️ صوتك نشط</span>
                                    <button type="button" onclick="toggleLocalCam()" class="bg-indigo-600 hover:bg-indigo-500 text-white px-1.5 py-0.5 rounded font-bold transition">
                                        📷
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- شبكة كاميرات بقية الطلاب المتصلين (Remote Peers) -->
                        <div id="remotePeersGrid" class="space-y-1.5">
                            <div id="noRemotePeersPlaceholder" class="p-2.5 bg-slate-950/60 border border-dashed border-slate-800 rounded-xl text-center text-slate-500 text-[11px]">
                                <span>📡 بانتظار انضمام بقية طلاب المجموعة...</span>
                            </div>
                        </div>
                    </div>

                    <!-- قائمة المتصلين نصياً مع أدوات التحكم الفردية -->
                    <div class="space-y-1.5 pt-2 border-t border-slate-800">
                        <span class="text-xs font-bold text-slate-300 block">قائمة الأعضاء المتصلين:</span>
                        <div id="participantsList" class="space-y-1.5">
                            <div class="p-2 bg-slate-950 rounded-xl text-center text-slate-500 text-[11px]">
                                جاري تحميل الحاضرين...
                            </div>
                        </div>
                    </div>

                </div>

                <!-- 3. تبويب بنك المناهج والموارد (مع زر الفتح بالشرح) -->
                <div id="liveTabResources" class="hidden p-3 space-y-3 text-xs">
                    <div class="bg-indigo-950/40 border border-indigo-900/50 p-2.5 rounded-xl text-[11px] text-indigo-300 space-y-1">
                        <span class="font-bold block text-white flex items-center gap-1">
                            <span>📌</span>
                            <span>بنك المناهج والكتب للمستوى</span>
                        </span>
                        <p>اضغط على زر <strong class="text-emerald-300">"عرض بالشرح 🚀"</strong> لفتح أي كتاب أو مورد فوراً في مساحة الشرح المركزية ليشاهده جميع طلاب المجموعة في نفس الوقت!</p>
                    </div>

                    <!-- قائمة كتب المنهج الرقمية (PDF) -->
                    <div class="space-y-2">
                        <span class="font-bold text-slate-300 text-xs block">📖 المقررات والكتب الرقمية:</span>
                        @forelse($groupBooks as $bk)
                            <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 flex justify-between items-center gap-2">
                                <div class="truncate min-w-0">
                                    <span class="font-bold text-slate-200 block truncate text-xs">{{ $bk->title }}</span>
                                    <span class="text-[10px] text-slate-500 font-mono">PDF • كتاب معتمد</span>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($isTeacher)
                                        <button type="button" onclick="openResourceInWorkspace('{{ asset($bk->file_path) }}', '{{ addslashes($bk->title) }}', 'pdf')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[10px] rounded-lg transition flex items-center gap-1 shadow">
                                            <span>🚀</span>
                                            <span>عرض بالشرح</span>
                                        </button>
                                    @endif
                                    <a href="{{ asset($bk->file_path) }}" target="_blank" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] rounded-lg transition" title="تحميل">
                                        ⬇️
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-2.5 text-center text-slate-500 text-[11px] bg-slate-950/40 rounded-xl">
                                لا توجد كتب مرفوعة لهذا المستوى حالياً.
                            </div>
                        @endforelse
                    </div>

                    <!-- تسجيلات الاستماع المنهجية (Listening Tracks) -->
                    <div class="space-y-2">
                        <span class="font-bold text-slate-300 text-xs block">🎧 تسجيلات الاستماع (Listening):</span>
                        @php $audios = $levelResources->where('type', 'audio'); @endphp
                        @forelse($audios as $aud)
                            <div class="p-2 rounded-xl bg-slate-950 border border-slate-800 space-y-1">
                                <div class="flex justify-between items-center text-[10px] text-purple-300 font-bold">
                                    <span class="truncate">{{ $aud->title }}</span>
                                </div>
                                @if($aud->file_path)
                                    <audio controls class="w-full h-6">
                                        <source src="{{ asset($aud->file_path) }}">
                                    </audio>
                                @endif
                            </div>
                        @empty
                            <div class="p-2.5 text-center text-slate-500 text-[11px] bg-slate-950/40 rounded-xl">
                                لا توجد تسجيلات صوتية لهذا المستوى حالياً.
                            </div>
                        @endforelse
                    </div>

                    <!-- مقاطع الفيديو التعليمية (Videos) -->
                    <div class="space-y-2">
                        <span class="font-bold text-slate-300 text-xs block">🎥 الفيديوهات التعليمية (Educational Videos):</span>
                        @forelse($groupVideos as $vid)
                            <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800 flex justify-between items-center gap-2">
                                <div class="truncate min-w-0">
                                    <span class="font-bold text-slate-200 block truncate text-xs">{{ $vid->title }}</span>
                                    <span class="text-[10px] text-slate-500 font-mono">فيديو تعليمي</span>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($isTeacher)
                                        <button type="button" onclick="openVideoInWorkspace('{{ asset($vid->file_path) }}', '{{ addslashes($vid->title) }}', false)" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-500 text-white font-bold text-[10px] rounded-lg transition flex items-center gap-1 shadow">
                                            <span>▶️</span>
                                            <span>عرض بالشرح</span>
                                        </button>
                                    @endif
                                    <a href="{{ asset($vid->file_path) }}" target="_blank" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] rounded-lg transition" title="فتح">
                                        ↗️
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-2.5 text-center text-slate-500 text-[11px] bg-slate-950/40 rounded-xl">
                                لا توجد فيديوهات مرفوعة لهذا المستوى حالياً.
                            </div>
                        @endforelse
                    </div>

                </div>

            </div>

            <!-- الشريط السفلي للتحكم السريع والمغادرة المنسقة بالجانب الأيمن -->
            <div class="p-2.5 bg-slate-950 border-t border-slate-800 space-y-2 shrink-0">
                <div class="flex items-center gap-1.5 justify-between">
                    <button type="button" onclick="toggleLocalMic()" id="btnMyMic" class="flex-1 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1 transition shadow-sm" title="كتم/تشغيل المايك">
                        <span id="iconMyMic">🎙️</span>
                        <span id="txtMyMic">المايك</span>
                    </button>
                    <button type="button" onclick="toggleLocalCam()" id="btnMyCam" class="flex-1 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl flex items-center justify-center gap-1 border border-slate-700 transition" title="تشغيل/إيقاف الكاميرا">
                        <span id="iconMyCam">📷</span>
                        <span id="txtMyCam">الكاميرا</span>
                    </button>
                    @if(auth()->user()->isStudent())
                        <button type="button" onclick="toggleHandRaise()" id="btnRaiseHand" class="flex-1 py-1.5 bg-slate-800 hover:bg-slate-700 text-amber-300 text-xs font-bold rounded-xl border border-slate-700 flex items-center justify-center gap-1 transition">
                            <span id="iconRaiseHand">✋</span>
                            <span id="txtRaiseHand">طلب كلمة</span>
                        </button>
                    @endif
                    @if($isTeacher)
                        <button type="button" onclick="rewardStar()" class="px-3 py-1.5 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-xs font-bold rounded-xl border border-amber-500/40 flex items-center justify-center gap-1 transition" title="مكافأة بنجمة">
                            <span>⭐</span>
                            <span id="starsCount">0</span>
                        </button>
                    @endif
                </div>

                <div class="flex items-center gap-1.5 justify-between">
                    @if($isTeacher)
                        @if(isset($group) && $group)
                            @if($group->is_live)
                                <form method="POST" action="{{ route('groups.live.stop', $group->id) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full text-xs font-bold bg-rose-600 hover:bg-rose-500 text-white py-1.5 rounded-xl transition shadow">
                                        ⏹️ إغلاق البث
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('groups.live.start', $group->id) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full text-xs font-black bg-emerald-500 hover:bg-emerald-400 text-slate-950 py-1.5 rounded-xl transition shadow">
                                        📡 فتح البث
                                    </button>
                                </form>
                            @endif
                        @elseif(isset($booking) && $booking)
                            <form method="POST" action="{{ route('classroom.booking.toggle_live', $booking->id) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full text-xs font-bold {{ $booking->is_live ? 'bg-rose-600 text-white' : 'bg-emerald-500 text-slate-950' }} py-1.5 rounded-xl transition">
                                    {{ $booking->is_live ? '⏹️ إغلاق البث' : '📡 فتح البث' }}
                                </button>
                            </form>
                        @endif

                        <button type="button" onclick="openReportModal()" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition shadow" title="إنهاء وإصدار تقرير الحصة">
                            📋 تقرير
                        </button>
                    @endif

                    <a href="{{ auth()->user()->isStudent() ? route('student.portal') : (auth()->user()->isTeacher() ? route('teacher.dashboard') : route('admin.groups.index')) }}" 
                       onclick="return confirm('هل ترغب بمغادرة الفصل التفاعلي المباشر الآن؟');"
                       class="px-3 py-1.5 bg-slate-800 hover:bg-rose-700 text-rose-300 hover:text-white rounded-xl text-xs font-bold border border-slate-700 transition">
                        مغادرة ✕
                    </a>
                </div>
            </div>
        </aside>

        <!-- 2. الجزء الوسط واليسار الكبير: مساحة الشرح والمشاركة المباشرة (Zoom Live Presentation & Whiteboard) -->
        <section class="flex-1 bg-slate-900 border border-slate-800 rounded-2xl flex flex-col overflow-hidden shadow-2xl relative min-h-[500px] lg:min-h-0">
            
            <!-- الشريط العلوي لمساحة الشرح والسبورة -->
            <div class="p-2 sm:p-2.5 bg-slate-950/95 border-b border-slate-800 flex flex-wrap items-center justify-between gap-2 shrink-0 z-10 text-xs">
                
                <!-- شارة المحتوى المفتوح حالياً -->
                <div class="flex items-center gap-2 min-w-0">
                    <span class="bg-indigo-600/30 text-indigo-300 font-bold px-2.5 py-1 rounded-lg border border-indigo-500/40 flex items-center gap-1.5 shrink-0">
                        <span id="wbModeIcon">📋</span>
                        <span id="wbModeText" class="truncate max-w-[180px] sm:max-w-[280px]">سبورة بيضاء تفاعلية</span>
                    </span>
                    <span id="wbSyncBadge" class="hidden sm:inline-flex items-center gap-1 text-[11px] text-emerald-400 font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>شرح مباشر متزامن</span>
                    </span>
                </div>

                <!-- أزرار تبديل الخلفيات وفتح الكتب والملفات -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    @if($isTeacher)
                        <!-- أزرار السبورة البيضاء والسوداء والمربعات -->
                        <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-800">
                            <button type="button" onclick="setWhiteboardBg('whiteboard')" class="px-2 py-1 rounded-lg hover:bg-slate-800 text-slate-200 text-[11px] font-bold transition flex items-center gap-1" title="سبورة بيضاء نظيفة">
                                <span>📋</span>
                                <span class="hidden md:inline">بيضاء</span>
                            </button>
                            <button type="button" onclick="setWhiteboardBg('blackboard')" class="px-2 py-1 rounded-lg hover:bg-slate-800 text-slate-200 text-[11px] font-bold transition flex items-center gap-1" title="سبورة سوداء">
                                <span>⬛</span>
                                <span class="hidden md:inline">سوداء</span>
                            </button>
                            <button type="button" onclick="setWhiteboardBg('grid')" class="px-2 py-1 rounded-lg hover:bg-slate-800 text-slate-200 text-[11px] font-bold transition flex items-center gap-1" title="شبكة مربعات هندسية/كتابة">
                                <span>📐</span>
                                <span class="hidden md:inline">مربعات</span>
                            </button>
                        </div>

                        <!-- قائمة منسدلة سريعة لكتب المنهج والمقررات -->
                        @if($groupBooks->isNotEmpty())
                            <div class="relative inline-block text-right">
                                <button type="button" onclick="toggleBooksDropdown()" id="btnBooksDropdown" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[11px] px-2.5 py-1.5 rounded-xl flex items-center gap-1 shadow transition">
                                    <span>📖</span>
                                    <span>كتب المنهج ▾</span>
                                </button>
                                <div id="booksDropdownMenu" class="hidden absolute left-0 sm:right-0 mt-1 w-64 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl z-50 p-1.5 space-y-1">
                                    <span class="text-[10px] text-slate-400 px-2 py-1 block font-bold border-b border-slate-800">اختر كتاباً لفتحه والشرح عليه للطلاب:</span>
                                    @foreach($groupBooks as $bk)
                                        <button type="button" onclick="openResourceInWorkspace('{{ asset($bk->file_path) }}', '{{ addslashes($bk->title) }}', 'pdf')" class="w-full text-right p-2 rounded-lg hover:bg-indigo-600/30 text-white text-xs font-bold transition flex items-center justify-between">
                                            <span class="truncate">{{ $bk->title }}</span>
                                            <span class="text-[10px] text-emerald-400">فتح 🚀</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- مستعرض وبحث جوجل والمسابقات -->
                        <button type="button" onclick="openBrowserModal()" class="bg-slate-800 hover:bg-slate-700 text-sky-400 border border-slate-700 font-bold text-[11px] px-2.5 py-1.5 rounded-xl flex items-center gap-1 transition shadow" title="فتح مستعرض جوجل والبحث عن معلومات أو مسابقات وألعاب للطلاب">
                            <span>🌐</span>
                            <span class="hidden sm:inline">جوجل ومسابقات</span>
                        </button>

                        <!-- فيديو تعليمي داخل السبورة -->
                        <button type="button" onclick="openVideoModal()" class="bg-slate-800 hover:bg-slate-700 text-rose-400 border border-slate-700 font-bold text-[11px] px-2.5 py-1.5 rounded-xl flex items-center gap-1 transition shadow" title="تشغيل فيديو تعليمي (يوتيوب أو ملف) داخل السبورة">
                            <span>🎬</span>
                            <span class="hidden sm:inline">فيديو تعليمي</span>
                        </button>

                        <!-- مشاركة أي ملف من جهاز المعلم (صورة، كتاب PDF، فيديو) -->
                        <input type="file" id="wbLocalFileInput" accept="image/*,application/pdf,video/*" class="hidden" onchange="handleWbLocalFileUpload(this)">
                        <button type="button" onclick="document.getElementById('wbLocalFileInput').click()" class="bg-slate-800 hover:bg-slate-700 text-emerald-400 border border-slate-700 font-bold text-[11px] px-2.5 py-1.5 rounded-xl flex items-center gap-1 transition shadow" title="مشاركة أي ملف من جهازي (صورة، كتاب PDF، أو فيديو)">
                            <span>📁</span>
                            <span class="hidden sm:inline">مشاركة ملف من جهازي</span>
                        </button>
                    @endif

                    <!-- التنقل بين صفحات الكتب والـ PDF -->
                    <div id="wbPageNavControls" class="hidden flex items-center gap-1 bg-slate-900 px-2 py-1 rounded-xl border border-slate-800 text-[11px] font-bold">
                        <button type="button" onclick="changeWbPage(-1)" class="px-1.5 py-0.5 hover:bg-slate-800 text-slate-300 rounded" title="الصفحة السابقة">◀</button>
                        <span class="text-slate-400">صفحة <strong id="wbCurrentPageText" class="text-white font-mono">1</strong> / <span id="wbTotalPagesText" class="font-mono">1</span></span>
                        <button type="button" onclick="changeWbPage(1)" class="px-1.5 py-0.5 hover:bg-slate-800 text-slate-300 rounded" title="الصفحة التالية">▶</button>
                    </div>

                    <!-- أزرار التكبير والتصغير -->
                    <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-800 text-[11px]">
                        <button type="button" onclick="zoomWb(-0.1)" class="px-1.5 py-0.5 hover:bg-slate-800 text-slate-300 rounded" title="تصغير">-</button>
                        <span id="wbZoomLabel" class="font-mono text-slate-300 px-1">100%</span>
                        <button type="button" onclick="zoomWb(0.1)" class="px-1.5 py-0.5 hover:bg-slate-800 text-slate-300 rounded" title="تكبير">+</button>
                        <button type="button" onclick="resetWbZoom()" class="px-1.5 py-0.5 hover:bg-slate-800 text-slate-400 rounded" title="إعادة ضبط الحجم">⟲</button>
                    </div>

                    <!-- حفظ لقطة من الشرح والسبورة -->
                    <button type="button" onclick="downloadWhiteboardSnapshot()" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-[11px] px-2.5 py-1.5 rounded-xl flex items-center gap-1 transition shadow" title="تصدير وحفظ صورة الشرح كاملة مع الملاحظات">
                        <span>💾</span>
                        <span class="hidden md:inline">حفظ الشرح</span>
                    </button>
                </div>
            </div>

            <!-- حاوية الرسم والعرض التفاعلية (Dual Canvas + Laser + Text Input + Browser & Video Layers) -->
            <div id="whiteboardContainer" class="flex-1 relative overflow-hidden bg-slate-950 flex items-center justify-center select-none">
                <!-- طبقة خلفية الكتاب أو الصورة أو السبورة النظيفة -->
                <canvas id="whiteboardBgCanvas" class="absolute inset-0 w-full h-full object-contain pointer-events-none z-[1]"></canvas>

                <!-- طبقة مستعرض جوجل ومسابقات الأطفال التفاعلية داخل السبورة -->
                <div id="whiteboardBrowserWrapper" class="hidden absolute inset-0 w-full h-full flex flex-col bg-slate-900 z-[5]">
                    <div class="p-1 sm:p-1.5 bg-slate-950/95 border-b border-slate-800 flex items-center justify-between gap-1.5 shrink-0 z-20 text-xs">
                        <div class="flex items-center gap-1.5 flex-1 min-w-0">
                            <button type="button" onclick="reloadBrowserIframe()" class="p-1 rounded hover:bg-slate-800 text-slate-300 text-xs" title="إعادة تحميل">🔄</button>
                            <span class="text-slate-400 font-mono text-[10px] hidden sm:inline">URL:</span>
                            <input type="text" id="wbBrowserCurrentUrl" class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-2 py-0.5 text-xs text-white truncate font-mono" readonly>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <span class="text-[10px] text-amber-300 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/30 hidden md:inline font-bold">
                                💡 اختر الماوس 🖱️ للتفاعل أو القلم ✏️ للشرح فوق الصفحة
                            </span>
                            @if($isTeacher)
                                <button type="button" onclick="openBrowserModal()" class="px-2 py-0.5 rounded bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[11px] shadow transition">
                                    🔍 بحث جديد
                                </button>
                                <button type="button" onclick="closeWbMediaLayer()" class="px-2 py-0.5 rounded bg-rose-600/30 hover:bg-rose-600 text-rose-200 hover:text-white font-bold text-[11px] border border-rose-500/40 transition" title="إنهاء العرض والرجوع للسبورة">
                                    ✕ إنهاء
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 relative w-full h-full bg-white overflow-hidden">
                        <iframe id="whiteboardBrowserIframe" src="about:blank" class="w-full h-full border-0" sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-modals" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div>
                </div>

                <!-- طبقة مشغل الفيديو التعليمي داخل السبورة -->
                <div id="whiteboardVideoWrapper" class="hidden absolute inset-0 w-full h-full flex flex-col items-center justify-center bg-black z-[5]">
                    <div class="absolute top-2 right-2 z-20 flex items-center gap-2">
                        <span id="wbVideoTitleBadge" class="bg-black/80 backdrop-blur text-white text-xs px-2.5 py-1 rounded-lg border border-white/20 truncate max-w-xs font-bold"></span>
                        @if($isTeacher)
                            <button type="button" onclick="closeWbMediaLayer()" class="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow transition" title="إغلاق الفيديو والرجوع للسبورة">
                                ✕ إنهاء الفيديو
                            </button>
                        @endif
                    </div>
                    <video id="whiteboardHtml5Video" controls playsinline class="hidden w-full h-full max-h-full object-contain"></video>
                    <iframe id="whiteboardYoutubeFrame" class="hidden w-full h-full max-w-full max-h-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>

                <!-- طبقة الرسم والكتابة التفاعلية بالقلم والأشكال -->
                <canvas id="whiteboardDrawCanvas" class="absolute inset-0 w-full h-full object-contain cursor-pen z-[10]"></canvas>

                <!-- مؤشر الليزر الحي (Red Laser Pointer) -->
                <div id="liveLaserPointer" class="hidden absolute pointer-events-none z-30 -translate-x-1/2 -translate-y-1/2 transition-all duration-75">
                    <div class="w-6 h-6 rounded-full bg-red-500 laser-glow animate-ping opacity-75 absolute -inset-0.5"></div>
                    <div class="w-4 h-4 rounded-full bg-red-600 border-2 border-white shadow-xl relative z-10"></div>
                    <span id="laserUserBadge" class="absolute top-5 right-1/2 translate-x-1/2 text-[9px] bg-red-950/90 text-red-200 border border-red-500/50 px-1.5 py-0.5 rounded font-bold whitespace-nowrap shadow-md">🔴 مؤشر المعلم</span>
                </div>

                <!-- مربع إدخال النص على السبورة -->
                <div id="wbTextInputWrapper" class="hidden absolute z-30">
                    <input type="text" id="wbTextInput" class="bg-slate-900/95 border-2 border-indigo-500 text-white px-2.5 py-1 rounded-xl text-sm outline-none shadow-2xl font-bold" placeholder="اكتب الملاحظة هنا واضغط Enter..." onkeydown="handleWbTextKey(event)">
                </div>

                <!-- شريط أدوات الشرح باليسار نفس الزوم (Left Floating Annotation Toolbar - Zoom Style) -->
                <div id="wbLeftToolbar" class="absolute left-3 top-3 bottom-3 z-30 flex flex-col items-center justify-between bg-slate-900/95 backdrop-blur-md border border-slate-700/80 rounded-2xl p-1.5 shadow-2xl space-y-1 text-xs overflow-y-auto max-h-[96%] scrollbar-none">
                    <div class="flex flex-col items-center gap-1">
                        <!-- مؤشر التحديد والماوس -->
                        <button type="button" onclick="selectWbTool('select')" id="toolBtn_select" class="wb-tool-btn p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition relative" title="مؤشر الماوس والتحديد">
                            <span class="text-base">🖱️</span>
                        </button>
                        <!-- القلم للكتابة الحرة -->
                        <button type="button" onclick="selectWbTool('pen')" id="toolBtn_pen" class="wb-tool-btn p-2 rounded-xl bg-indigo-600 text-white transition shadow-sm relative" title="قلم الكتابة والشرح العادي">
                            <span class="text-base">✏️</span>
                        </button>
                        <!-- فرشاة خط عربي وفني -->
                        <button type="button" onclick="selectWbTool('brush')" id="toolBtn_brush" class="wb-tool-btn p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition relative" title="فرشاة خط عربي وفنية ذات زاوية">
                            <span class="text-base">🖊️</span>
                        </button>
                        <!-- القلم الفسفوري الشفاف لتحديد النصوص والكلمات -->
                        <button type="button" onclick="selectWbTool('highlighter')" id="toolBtn_highlighter" class="wb-tool-btn p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition relative" title="قلم تظليل فسفوري شفاف لتحديد العبارات">
                            <span class="text-base">🖍️</span>
                        </button>
                        <!-- القلم السحري (يختفي بعد ثانيتين ونصف) -->
                        <button type="button" onclick="selectWbTool('magic')" id="toolBtn_magic" class="wb-tool-btn p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition relative" title="القلم السحري (يختفي تلقائياً للفت الانتباه)">
                            <span class="text-base">✨</span>
                        </button>
                        <!-- مؤشر الليزر التفاعلي الحي -->
                        <button type="button" onclick="selectWbTool('laser')" id="toolBtn_laser" class="wb-tool-btn p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition relative" title="مؤشر الليزر الحي الفوري">
                            <span class="text-base">🔴</span>
                        </button>

                        <!-- قائمة الأشكال الهندسية الموسعة -->
                        <div class="relative">
                            <button type="button" onclick="toggleShapesFlyout()" id="toolBtn_shapes" class="wb-tool-btn p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition relative" title="الأشكال الهندسية والرسومات">
                                <span id="activeShapeIcon" class="text-base">🔲</span>
                            </button>
                            <div id="shapesFlyoutMenu" class="hidden absolute left-12 top-0 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl p-2 flex flex-col gap-1 z-50 w-44">
                                <span class="text-[10px] text-slate-400 font-bold border-b border-slate-800 pb-1">اختر الشكل الهندسي:</span>
                                <div class="grid grid-cols-2 gap-1 text-xs font-bold">
                                    <button type="button" onclick="selectWbShape('rect')" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white flex items-center gap-1">
                                        <span>🔲</span> <span>مستطيل</span>
                                    </button>
                                    <button type="button" onclick="selectWbShape('rounded_rect')" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white flex items-center gap-1">
                                        <span>◽</span> <span>مستدير</span>
                                    </button>
                                    <button type="button" onclick="selectWbShape('circle')" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white flex items-center gap-1">
                                        <span>⭕</span> <span>دائرة</span>
                                    </button>
                                    <button type="button" onclick="selectWbShape('triangle')" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white flex items-center gap-1">
                                        <span>🔺</span> <span>مثلث</span>
                                    </button>
                                    <button type="button" onclick="selectWbShape('line')" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white flex items-center gap-1">
                                        <span>➖</span> <span>خط</span>
                                    </button>
                                    <button type="button" onclick="selectWbShape('arrow')" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white flex items-center gap-1">
                                        <span>➡️</span> <span>سهم</span>
                                    </button>
                                    <button type="button" onclick="selectWbShape('double_arrow')" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white flex items-center gap-1">
                                        <span>↔️</span> <span>سهمين</span>
                                    </button>
                                    <button type="button" onclick="selectWbShape('star')" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white flex items-center gap-1">
                                        <span>⭐</span> <span>نجمة</span>
                                    </button>
                                    <button type="button" onclick="selectWbShape('checkmark')" class="p-1.5 rounded-lg hover:bg-emerald-600 text-white flex items-center gap-1">
                                        <span>✔️</span> <span>صح</span>
                                    </button>
                                    <button type="button" onclick="selectWbShape('cross')" class="p-1.5 rounded-lg hover:bg-rose-600 text-white flex items-center gap-1">
                                        <span>❌</span> <span>خطأ</span>
                                    </button>
                                    <button type="button" onclick="selectWbShape('bubble')" class="col-span-2 p-1.5 rounded-lg hover:bg-indigo-600 text-white flex items-center justify-center gap-1">
                                        <span>💬</span> <span>فقاعة كلام</span>
                                    </button>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800">
                                    <button type="button" onclick="toggleShapeFill()" id="btnShapeFill" class="w-full py-1 px-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-[10px] font-bold flex items-center justify-between transition">
                                        <span>تعبئة اللون:</span>
                                        <span id="shapeFillState" class="text-indigo-400">مفرغ 🔲</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- قائمة أختام التشجيع والتقييم (Stamps) -->
                        <div class="relative">
                            <button type="button" onclick="toggleStampsFlyout()" id="toolBtn_stamp" class="wb-tool-btn p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition relative" title="أختام التشجيع والتقييم للطلاب">
                                <span id="activeStampIcon" class="text-base">⭐</span>
                            </button>
                            <div id="stampsFlyoutMenu" class="hidden absolute left-12 top-0 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl p-2 grid grid-cols-3 gap-1 z-50 w-36">
                                <button type="button" onclick="selectWbStamp('⭐')" class="p-2 rounded-xl hover:bg-indigo-600 text-center text-xl transition" title="نجمة إبداع">⭐</button>
                                <button type="button" onclick="selectWbStamp('💯')" class="p-2 rounded-xl hover:bg-indigo-600 text-center text-xl transition" title="ممتاز 100%">💯</button>
                                <button type="button" onclick="selectWbStamp('👍')" class="p-2 rounded-xl hover:bg-indigo-600 text-center text-xl transition" title="إعجاب ورائع">👍</button>
                                <button type="button" onclick="selectWbStamp('👏')" class="p-2 rounded-xl hover:bg-indigo-600 text-center text-xl transition" title="أحسنت وتصفيق">👏</button>
                                <button type="button" onclick="selectWbStamp('❤️')" class="p-2 rounded-xl hover:bg-indigo-600 text-center text-xl transition" title="مميز ومحبوب">❤️</button>
                                <button type="button" onclick="selectWbStamp('👑')" class="p-2 rounded-xl hover:bg-indigo-600 text-center text-xl transition" title="بطل الحصة">👑</button>
                            </div>
                        </div>

                        <!-- أداة الملاحظة اللاصقة (Sticky Note) -->
                        <button type="button" onclick="selectWbTool('note')" id="toolBtn_note" class="wb-tool-btn p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition relative" title="ملصق وملاحظة لاصقة ملونة">
                            <span class="text-base">📝</span>
                        </button>

                        <!-- أداة كتابة النص العادي -->
                        <button type="button" onclick="selectWbTool('text')" id="toolBtn_text" class="wb-tool-btn p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition relative" title="كتابة نص وملاحظات">
                            <span class="text-base">🔤</span>
                        </button>
                        <!-- الممحاة -->
                        <button type="button" onclick="selectWbTool('eraser')" id="toolBtn_eraser" class="wb-tool-btn p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition relative" title="الممحاة">
                            <span class="text-base">🧽</span>
                        </button>
                    </div>

                    <!-- فاصل بين الأدوات والألوان -->
                    <div class="w-6 h-px bg-slate-700/60 my-1"></div>

                    <div class="flex flex-col items-center gap-1.5">
                        <!-- لوحة الألوان الموسعة (20 لوناً + منتقي مخصص) -->
                        <div class="relative">
                            <button type="button" onclick="toggleColorFlyout()" class="w-6 h-6 rounded-full border-2 border-white shadow transition transform hover:scale-110 flex items-center justify-center" id="activeColorPreview" style="background-color: #ef4444;" title="لوحة الألوان المتنوعة"></button>
                            <div id="colorFlyoutMenu" class="hidden absolute left-12 bottom-0 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl p-2.5 flex flex-col gap-2 z-50 w-52">
                                <span class="text-[10px] text-slate-400 font-bold">لوحة ألوان الشرح:</span>
                                <div class="grid grid-cols-5 gap-1.5">
                                    <button type="button" onclick="setWbColor('#ef4444')" class="w-6 h-6 rounded-full bg-red-500 border border-white/40 hover:scale-125 transition" title="أحمر فاتح"></button>
                                    <button type="button" onclick="setWbColor('#dc2626')" class="w-6 h-6 rounded-full bg-red-700 border border-white/40 hover:scale-125 transition" title="أحمر قاني"></button>
                                    <button type="button" onclick="setWbColor('#ec4899')" class="w-6 h-6 rounded-full bg-pink-500 border border-white/40 hover:scale-125 transition" title="وردي"></button>
                                    <button type="button" onclick="setWbColor('#a855f7')" class="w-6 h-6 rounded-full bg-purple-500 border border-white/40 hover:scale-125 transition" title="بنفسجي"></button>
                                    <button type="button" onclick="setWbColor('#6366f1')" class="w-6 h-6 rounded-full bg-indigo-500 border border-white/40 hover:scale-125 transition" title="نيلي"></button>
                                    <button type="button" onclick="setWbColor('#3b82f6')" class="w-6 h-6 rounded-full bg-blue-500 border border-white/40 hover:scale-125 transition" title="أزرق"></button>
                                    <button type="button" onclick="setWbColor('#1d4ed8')" class="w-6 h-6 rounded-full bg-blue-700 border border-white/40 hover:scale-125 transition" title="كحلي"></button>
                                    <button type="button" onclick="setWbColor('#06b6d4')" class="w-6 h-6 rounded-full bg-cyan-500 border border-white/40 hover:scale-125 transition" title="سماوي"></button>
                                    <button type="button" onclick="setWbColor('#14b8a6')" class="w-6 h-6 rounded-full bg-teal-500 border border-white/40 hover:scale-125 transition" title="تركواز"></button>
                                    <button type="button" onclick="setWbColor('#10b981')" class="w-6 h-6 rounded-full bg-emerald-500 border border-white/40 hover:scale-125 transition" title="زمردي"></button>
                                    <button type="button" onclick="setWbColor('#22c55e')" class="w-6 h-6 rounded-full bg-green-500 border border-white/40 hover:scale-125 transition" title="أخضر عشبي"></button>
                                    <button type="button" onclick="setWbColor('#84cc16')" class="w-6 h-6 rounded-full bg-lime-500 border border-white/40 hover:scale-125 transition" title="ليموني فسفوري"></button>
                                    <button type="button" onclick="setWbColor('#facc15')" class="w-6 h-6 rounded-full bg-yellow-400 border border-white/40 hover:scale-125 transition" title="أصفر فاقع"></button>
                                    <button type="button" onclick="setWbColor('#f59e0b')" class="w-6 h-6 rounded-full bg-amber-500 border border-white/40 hover:scale-125 transition" title="كهرماني"></button>
                                    <button type="button" onclick="setWbColor('#f97316')" class="w-6 h-6 rounded-full bg-orange-500 border border-white/40 hover:scale-125 transition" title="برتقالي"></button>
                                    <button type="button" onclick="setWbColor('#ea580c')" class="w-6 h-6 rounded-full bg-orange-700 border border-white/40 hover:scale-125 transition" title="برتقالي ناري"></button>
                                    <button type="button" onclick="setWbColor('#78350f')" class="w-6 h-6 rounded-full bg-amber-900 border border-white/40 hover:scale-125 transition" title="بني"></button>
                                    <button type="button" onclick="setWbColor('#64748b')" class="w-6 h-6 rounded-full bg-slate-500 border border-white/40 hover:scale-125 transition" title="رمادي أردوازي"></button>
                                    <button type="button" onclick="setWbColor('#0f172a')" class="w-6 h-6 rounded-full bg-slate-900 border border-white/40 hover:scale-125 transition" title="أسود فاحم"></button>
                                    <button type="button" onclick="setWbColor('#ffffff')" class="w-6 h-6 rounded-full bg-white border border-slate-400 hover:scale-125 transition" title="أبيض ناصع"></button>
                                </div>
                                <div class="pt-1.5 border-t border-slate-800 flex items-center justify-between text-[11px] text-slate-300">
                                    <span>لون مخصص:</span>
                                    <input type="color" id="wbCustomColorInput" value="#ef4444" onchange="setWbColor(this.value)" class="w-7 h-7 rounded-lg cursor-pointer border border-slate-600 bg-transparent p-0" title="اختر أي لون مخصص">
                                </div>
                            </div>
                        </div>

                        <!-- سماكة وحجم الخط والقلم (7 أحجام) -->
                        <div class="relative">
                            <button type="button" onclick="toggleSizeFlyout()" class="p-1 rounded-lg text-slate-300 hover:bg-slate-800 transition font-mono font-bold text-[10px]" title="سماكة الخط">
                                <span id="activeSizePreview">4px</span>
                            </button>
                            <div id="sizeFlyoutMenu" class="hidden absolute left-12 bottom-0 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl p-2 flex flex-col gap-1 z-50 w-32">
                                <span class="text-[10px] text-slate-400 font-bold border-b border-slate-800 pb-1">سماكة القلم:</span>
                                <button type="button" onclick="setWbSize(1)" class="p-1 rounded-lg hover:bg-indigo-600 text-white text-xs font-bold flex items-center justify-between">
                                    <span>دقيق جداً</span> <span class="font-mono text-[10px]">1px</span>
                                </button>
                                <button type="button" onclick="setWbSize(2)" class="p-1 rounded-lg hover:bg-indigo-600 text-white text-xs font-bold flex items-center justify-between">
                                    <span>رفيع</span> <span class="font-mono text-[10px]">2px</span>
                                </button>
                                <button type="button" onclick="setWbSize(4)" class="p-1 rounded-lg hover:bg-indigo-600 text-white text-xs font-bold flex items-center justify-between">
                                    <span>متوسط</span> <span class="font-mono text-[10px]">4px</span>
                                </button>
                                <button type="button" onclick="setWbSize(8)" class="p-1 rounded-lg hover:bg-indigo-600 text-white text-xs font-bold flex items-center justify-between">
                                    <span>عريض</span> <span class="font-mono text-[10px]">8px</span>
                                </button>
                                <button type="button" onclick="setWbSize(14)" class="p-1 rounded-lg hover:bg-indigo-600 text-white text-xs font-bold flex items-center justify-between">
                                    <span>ماركر</span> <span class="font-mono text-[10px]">14px</span>
                                </button>
                                <button type="button" onclick="setWbSize(24)" class="p-1 rounded-lg hover:bg-indigo-600 text-white text-xs font-bold flex items-center justify-between">
                                    <span>عريض جداً</span> <span class="font-mono text-[10px]">24px</span>
                                </button>
                                <button type="button" onclick="setWbSize(36)" class="p-1 rounded-lg hover:bg-indigo-600 text-white text-xs font-bold flex items-center justify-between">
                                    <span>جامبو</span> <span class="font-mono text-[10px]">36px</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- التراجع والإعادة والمسح -->
                    <div class="w-6 h-px bg-slate-700/60 my-1"></div>

                    <div class="flex flex-col items-center gap-1">
                        <button type="button" onclick="undoStroke()" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition" title="تراجع (Ctrl+Z)">
                            <span class="text-base">↩️</span>
                        </button>
                        <button type="button" onclick="redoStroke()" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition" title="إعادة">
                            <span class="text-base">↪️</span>
                        </button>
                        <button type="button" onclick="clearWhiteboard()" class="p-2 rounded-xl text-rose-400 hover:text-rose-300 hover:bg-rose-950/60 transition" title="مسح كل الشروحات">
                            <span class="text-base">🗑️</span>
                        </button>
                    </div>
                </div>

            </div>
        </section>

    </main>

    <!-- نافذة تكبير ومعاينة صور أوراق الإجابات (Live Lightbox) -->
    <div id="liveImageModal" class="hidden fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex items-center justify-center p-4" onclick="closeLiveImageModal()">
        <div class="max-w-4xl max-h-[90vh] w-full flex flex-col items-center relative" onclick="event.stopPropagation()">
            <div class="w-full flex justify-between items-center text-white mb-2 px-2">
                <span id="liveImageModalTitle" class="text-sm font-bold truncate"></span>
                <button onclick="closeLiveImageModal()" class="text-white hover:text-rose-400 text-2xl font-black">✕</button>
            </div>
            <img id="liveImageModalImg" src="" alt="ورقة الإجابة" class="max-w-full max-h-[80vh] rounded-2xl shadow-2xl object-contain border border-white/10 bg-slate-950">
        </div>
    </div>

    <!-- نافذة مستعرض وبحث جوجل والألعاب التفاعلية (Google Browser & Educational Games Modal) -->
    <div id="wbBrowserModal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4" onclick="closeWbMediaModals()">
        <div class="max-w-2xl w-full bg-slate-900 border border-slate-700 rounded-3xl shadow-2xl p-5 sm:p-6 space-y-4" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🌐</span>
                    <div>
                        <h3 class="text-base font-black text-white">مستعرض جوجل والمسابقات والألعاب داخل السبورة</h3>
                        <p class="text-[11px] text-slate-400">استعرض جوجل أو شغّل ألعاب ومسابقات تفاعلية يشاهدها الطلاب في البث مباشرة مع الشرح فوقها</p>
                    </div>
                </div>
                <button type="button" onclick="closeWbMediaModals()" class="text-slate-400 hover:text-white text-xl font-bold">✕</button>
            </div>

            <!-- نموذج البحث السريع في جوجل -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-300">🔍 بحث جوجل الفوري داخل السبورة (Google Search):</label>
                <div class="flex gap-2">
                    <input type="text" id="wbGoogleSearchInput" class="flex-1 bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none" placeholder="اكتب للبحث في جوجل، مثلاً: ألعاب مسابقات لغوية للأطفال، خريطة العالم..." onkeydown="if(event.key==='Enter') executeBrowserSearch()">
                    <button type="button" onclick="executeBrowserSearch()" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow flex items-center gap-1">
                        <span>بحث في جوجل</span>
                        <span>🔎</span>
                    </button>
                </div>
            </div>

            <!-- إدخال رابط مباشر -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-300">🔗 أو افتح موقعاً أو رابط لعبة مباشرة (Direct URL):</label>
                <div class="flex gap-2">
                    <input type="url" id="wbDirectUrlInput" class="flex-1 bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none font-mono" placeholder="https://wordwall.net/ar/resource/..." onkeydown="if(event.key==='Enter') executeDirectUrlOpen()">
                    <button type="button" onclick="executeDirectUrlOpen()" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow flex items-center gap-1">
                        <span>فتح الرابط</span>
                        <span>🚀</span>
                    </button>
                </div>
            </div>

            <!-- اختصارات سريعة لأشهر منصات مسابقات وألعاب الأطفال -->
            <div class="space-y-2 pt-2 border-t border-slate-800">
                <span class="block text-xs font-bold text-amber-400">⚡ منصات مسابقات وألعاب تعليمية سريعة:</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <button type="button" onclick="openPresetGame('https://wordwall.net/ar-sa/community', 'مسابقات Wordwall التفاعلية')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-indigo-950/60 border border-slate-800 hover:border-indigo-500/50 text-right transition group">
                        <span class="text-base block mb-1">🎮</span>
                        <strong class="text-xs text-white block">Wordwall</strong>
                        <span class="text-[10px] text-slate-400 block truncate">ألعاب ومسابقات مجتمعية</span>
                    </button>
                    <button type="button" onclick="openPresetGame('https://quizizz.com/join', 'منصة Quizizz التنافسية')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-indigo-950/60 border border-slate-800 hover:border-indigo-500/50 text-right transition group">
                        <span class="text-base block mb-1">⚡</span>
                        <strong class="text-xs text-white block">Quizizz</strong>
                        <span class="text-[10px] text-slate-400 block truncate">منافسات وأسئلة تفاعلية</span>
                    </button>
                    <button type="button" onclick="openPresetGame('https://kahoot.it/', 'لعبة كاهوت Kahoot')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-indigo-950/60 border border-slate-800 hover:border-indigo-500/50 text-right transition group">
                        <span class="text-base block mb-1">🏆</span>
                        <strong class="text-xs text-white block">Kahoot!</strong>
                        <span class="text-[10px] text-slate-400 block truncate">إدخال كود المنافسة للفرق</span>
                    </button>
                    <button type="button" onclick="openPresetGame('https://www.educaplay.com/', 'ألعاب Educaplay التعليمية')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-indigo-950/60 border border-slate-800 hover:border-indigo-500/50 text-right transition group">
                        <span class="text-base block mb-1">🧩</span>
                        <strong class="text-xs text-white block">Educaplay</strong>
                        <span class="text-[10px] text-slate-400 block truncate">كلمات متقاطعة وتوصيل</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- نافذة تشغيل الفيديو التعليمي داخل السبورة (Educational Video Modal) -->
    <div id="wbVideoModal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4" onclick="closeWbMediaModals()">
        <div class="max-w-xl w-full bg-slate-900 border border-slate-700 rounded-3xl shadow-2xl p-5 sm:p-6 space-y-4" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🎬</span>
                    <div>
                        <h3 class="text-base font-black text-white">تشغيل فيديو تعليمي داخل السبورة</h3>
                        <p class="text-[11px] text-slate-400">اعرض فيديو من اليوتيوب أو من مقررات المجموعة أو ارفع فيديو من جهازك للشرح عليه</p>
                    </div>
                </div>
                <button type="button" onclick="closeWbMediaModals()" class="text-slate-400 hover:text-white text-xl font-bold">✕</button>
            </div>

            <!-- إدخال رابط يوتيوب -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-300">▶️ رابط فيديو يوتيوب (YouTube URL):</label>
                <div class="flex gap-2">
                    <input type="url" id="wbYoutubeUrlInput" class="flex-1 bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none font-mono" placeholder="https://www.youtube.com/watch?v=... أو youtu.be/..." onkeydown="if(event.key==='Enter') executeYoutubeOpen()">
                    <button type="button" onclick="executeYoutubeOpen()" class="bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow flex items-center gap-1">
                        <span>تشغيل</span>
                        <span>🚀</span>
                    </button>
                </div>
            </div>

            <!-- فيديوهات المنهج المرفوعة مسبقاً -->
            @if($groupVideos->isNotEmpty())
                <div class="space-y-2 pt-2 border-t border-slate-800">
                    <label class="block text-xs font-bold text-slate-300">📂 اختر من فيديوهات المنهج والمقررات:</label>
                    <div class="max-h-40 overflow-y-auto space-y-1.5 p-1 bg-slate-950 rounded-xl border border-slate-800">
                        @foreach($groupVideos as $cv)
                            <div class="p-2 rounded-lg bg-slate-900 border border-slate-800 flex justify-between items-center gap-2">
                                <span class="text-xs font-bold text-white truncate">{{ $cv->title }}</span>
                                <button type="button" onclick="openVideoInWorkspace('{{ asset($cv->file_path) }}', '{{ addslashes($cv->title) }}', false)" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-bold rounded-lg transition shadow shrink-0">
                                    تشغيل بالفصل ▶️
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- رفع فيديو من الجهاز -->
            <div class="pt-2 border-t border-slate-800 flex items-center justify-between">
                <div>
                    <span class="block text-xs font-bold text-slate-300">📁 رفع فيديو محلي من جهازي (MP4, WebM):</span>
                    <span class="text-[10px] text-slate-500">سيتم حفظه في ملفات الشرح وعرضه تلقائياً لجميع الطلاب</span>
                </div>
                <button type="button" onclick="closeWbMediaModals(); document.getElementById('wbLocalFileInput').click();" class="bg-slate-800 hover:bg-slate-700 text-emerald-400 border border-slate-700 text-xs font-bold px-3 py-2 rounded-xl transition shadow">
                    اختر ملف فيديو 📤
                </button>
            </div>
        </div>
    </div>

    <!-- نافذة إدخال تقرير الحصة الفوري للمعلم (Modal) -->
    @if($isTeacher)
        <div id="reportModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white text-slate-900 rounded-3xl max-w-lg w-full p-8 shadow-2xl space-y-4">
                <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-black">إصدار تقرير الحصة الفوري (ASOTS Report)</h3>
                        <span class="text-xs text-slate-500">سيتم إرسال هذا التقييم فوراً إلى واتساب الطالب وترحيل أجرك المالي.</span>
                    </div>
                    <button onclick="closeReportModal()" class="text-slate-400 hover:text-slate-700 text-lg">✕</button>
                </div>

                <form action="{{ isset($booking) && $booking ? route('classroom.report.submit', $booking->id) : '#' }}" onsubmit="{{ !isset($booking) || !$booking ? "event.preventDefault(); alert('تم توثيق تقرير الحصة الجماعية بنجاح!'); window.location.href='/admin/groups';" : '' }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">تقييم النطق (1-5)</label>
                            <select name="pronunciation_score" class="w-full border border-slate-300 rounded-xl p-2 font-bold text-indigo-700 text-sm">
                                <option value="5">⭐⭐⭐⭐⭐ 5</option>
                                <option value="4" selected>⭐⭐⭐⭐ 4</option>
                                <option value="3">⭐⭐⭐ 3</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">الطلاقة (1-5)</label>
                            <select name="fluency_score" class="w-full border border-slate-300 rounded-xl p-2 font-bold text-indigo-700 text-sm">
                                <option value="5">⭐⭐⭐⭐⭐ 5</option>
                                <option value="4" selected>⭐⭐⭐⭐ 4</option>
                                <option value="3">⭐⭐⭐ 3</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">القواعد (1-5)</label>
                            <select name="grammar_score" class="w-full border border-slate-300 rounded-xl p-2 font-bold text-indigo-700 text-sm">
                                <option value="5">⭐⭐⭐⭐⭐ 5</option>
                                <option value="4" selected>⭐⭐⭐⭐ 4</option>
                                <option value="3">⭐⭐⭐ 3</option>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" id="modalStarsInput" name="stars_awarded" value="4">

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">توجيهات وملاحظات المعلم للطالب:</label>
                        <textarea name="teacher_comment" required rows="3" 
                            class="w-full border border-slate-300 rounded-xl p-3 focus:ring-1 focus:ring-indigo-500 outline-none text-xs"
                            placeholder="أداء ممتاز اليوم! ركز على نطق الأصوات الصامتة في نهاية الكلمات وربط الجمل."></textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">الواجب الصوتي المنزلي:</label>
                        <input type="text" name="homework" value="تسجيل قراءة النص السابق 3 مرات في قسم الواجبات"
                            class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-1 focus:ring-indigo-500 outline-none text-xs">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-[11px] text-emerald-700 font-bold">⚡ سيتم قيد $7.00 كاستحقاق مالي للمعلم.</span>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition">
                            اعتماد وإنهاء الحصة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- سكريبتات WebRTC الصوت والفيديو وشبكة الحضور والمحادثة المتزامنة -->
    <script>
    // دالة هروب آمنة لحماية النصوص في القوالب
    function addslashes(str) {
        if (!str) return '';
        return String(str).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    const ROOM_ID = "{{ $roomId }}";
    const CURRENT_USER_ID = {{ auth()->id() }};
    const CURRENT_USER_NAME = "{{ addslashes(auth()->user()->name) }}";
    const IS_TEACHER = {{ $isTeacher ? 'true' : 'false' }};
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // المعرف الفريد للنظير (WebRTC Peer ID)
    const MY_PEER_ID = 'peer_' + CURRENT_USER_ID + '_' + Math.random().toString(36).substring(2, 8);

    // حالة الأجهزة والمشاركة الحالية
    let localStream = null;
    let isMicEnabled = true;
    let isCamEnabled = false;
    let isHandRaised = false;
    let currentRoomPolicy = 'open'; // 'open' | 'permission'
    let lastMessageId = {{ isset($chatMessages) && $chatMessages->isNotEmpty() ? $chatMessages->last()->id : 0 }};

    // اتصالات WebRTC بالأقران (PeerConnections Map)
    const peerConnections = {}; // key: peerId => RTCPeerConnection
    const peerDataChannels = {}; // key: peerId => RTCDataChannel (P2P ultra-low latency sub-50ms)
    const peerStreams = {}; // key: peerId => MediaStream
    const remoteAudios = {}; // key: peerId => HTMLAudioElement

    const ICE_SERVERS = {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' },
            { urls: 'stun:stun2.l.google.com:19302' },
            { urls: 'stun:stun3.l.google.com:19302' }
        ]
    };

    // إدارة قنوات البيانات الفورية بين الأقران (WebRTC DataChannel P2P Sub-50ms)
    function setupPeerDataChannel(remotePeerId, dc) {
        if (!dc) return;
        peerDataChannels[remotePeerId] = dc;

        dc.onopen = () => {
            console.log('⚡ P2P Realtime DataChannel opened with peer:', remotePeerId);
        };
        dc.onclose = () => {
            delete peerDataChannels[remotePeerId];
        };
        dc.onerror = (err) => {
            console.warn('P2P DataChannel error:', err);
        };
        dc.onmessage = (event) => {
            handleP2PRealtimeMessage(event.data);
        };
    }

    function broadcastP2P(payload) {
        const str = typeof payload === 'string' ? payload : JSON.stringify(payload);
        Object.entries(peerDataChannels).forEach(([peerId, dc]) => {
            if (dc && dc.readyState === 'open') {
                try {
                    dc.send(str);
                } catch (err) {
                    console.warn('P2P send error to peer', peerId, err);
                }
            }
        });
    }

    function handleP2PRealtimeMessage(raw) {
        try {
            const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
            if (!data || !data.type) return;

            switch (data.type) {
                case 'chat_message':
                    if (data.message) {
                        if (data.message.id && data.message.id > lastMessageId) {
                            lastMessageId = data.message.id;
                        }
                        appendLiveChatMessage(data.message);
                    }
                    break;
                case 'wb_stroke':
                    if (data.stroke) {
                        wbStrokes.push(data.stroke);
                        redrawWhiteboard();
                    }
                    break;
                case 'wb_magic_stroke':
                    if (data.stroke) {
                        wbMagicStrokes.push({
                            ...data.stroke,
                            expiresAt: Date.now() + 2500
                        });
                        redrawWhiteboard();
                    }
                    break;
                case 'wb_undo':
                    if (wbStrokes.length > 0) {
                        wbStrokes.pop();
                        redrawWhiteboard();
                    }
                    break;
                case 'wb_clear':
                    wbStrokes = [];
                    redrawWhiteboard();
                    break;
                case 'wb_bg':
                    if (data.state) {
                        applyServerWhiteboardState(data.state);
                    }
                    break;
                case 'laser':
                    renderRemoteLaser({ active: true, x: data.x, y: data.y, user_name: data.name });
                    break;
                case 'laser_hide':
                    renderRemoteLaser({ active: false });
                    break;
            }
        } catch (e) {
            console.error('Error handling P2P realtime message:', e);
        }
    }

    // مزامنة كافة مسارات الصوت والصورة المحلية إلى جميع اتصالات الأقران
    function ensureTracksInPeerConnections() {
        if (!localStream) return;
        const tracks = localStream.getTracks();

        Object.entries(peerConnections).forEach(([peerId, pc]) => {
            try {
                const senders = pc.getSenders();
                tracks.forEach(track => {
                    const existingSender = senders.find(s => s.track && s.track.kind === track.kind);
                    if (existingSender) {
                        if (existingSender.track !== track) {
                            existingSender.replaceTrack(track).catch(e => console.log('replaceTrack err:', e));
                        }
                    } else {
                        pc.addTrack(track, localStream);
                        // إعادة التفاوض (Renegotiation) إذا كان بادئ الاتصال
                        if (MY_PEER_ID < peerId) {
                            pc.createOffer({ offerToReceiveAudio: true, offerToReceiveVideo: true })
                                .then(offer => pc.setLocalDescription(offer))
                                .then(() => postSignal(peerId, 'offer', pc.localDescription))
                                .catch(e => console.log('Renegotiate offer err:', e));
                        }
                    }
                });
            } catch (err) {
                console.log('ensureTracks error for peer', peerId, err);
            }
        });
    }

    // 1. تشغيل المايكروفون فقط افتراضياً (الكاميرا تبدأ مغلقة تماماً عند الدخول)
    async function initLocalMedia() {
        try {
            // الكاميرا تبدأ مغلقة افتراضياً عند الدخول، ويتم تشغيل المايكروفون فقط
            localStream = await navigator.mediaDevices.getUserMedia({
                audio: { echoCancellation: true, noiseSuppression: true, autoGainControl: true },
                video: false
            });

            isCamEnabled = false;
            // المعلم صوته مفتوح دائماً، أما الطالب فبحسب سياسة الغرفة
            isMicEnabled = IS_TEACHER ? true : (currentRoomPolicy !== 'permission');

            if (!isMicEnabled) {
                localStream.getAudioTracks().forEach(t => t.enabled = false);
            }

            ensureTracksInPeerConnections();
            updateMediaButtonsUI();

        } catch (err) {
            console.warn('Microphone access denied or error:', err);
            isMicEnabled = false;
            isCamEnabled = false;
            updateMediaButtonsUI();
        }
    }

    function toggleLocalMic() {
        if (!localStream) {
            initLocalMedia();
            return;
        }

        // إذا كانت السياسة "بإذن" والمستخدم طالب ولم يُمنح الإذن بعد
        if (!IS_TEACHER && currentRoomPolicy === 'permission' && !isHandRaised) {
            alert('⚠️ التحدث في هذا الفصل مقيد بإذن الأستاذ. يرجى الضغط على زر "طلب الكلمة ✋" أولاً.');
            return;
        }

        isMicEnabled = !isMicEnabled;
        localStream.getAudioTracks().forEach(track => track.enabled = isMicEnabled);
        ensureTracksInPeerConnections();
        updateMediaButtonsUI();
    }

    // تشغيل وإيقاف الكاميرا عند الطلب فقط (فيديو عند النقر)
    async function toggleLocalCam() {
        try {
            if (!isCamEnabled) {
                // المستخدم يريد فتح الكاميرا الآن
                let videoTrack = localStream ? localStream.getVideoTracks()[0] : null;
                if (!videoTrack) {
                    const videoStream = await navigator.mediaDevices.getUserMedia({
                        video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: "user" }
                    });
                    videoTrack = videoStream.getVideoTracks()[0];
                    if (!localStream) {
                        localStream = videoStream;
                    } else {
                        localStream.addTrack(videoTrack);
                    }
                } else {
                    videoTrack.enabled = true;
                }

                isCamEnabled = true;

                const videoEl = IS_TEACHER ? document.getElementById('teacherVideo') : document.getElementById('mySelfVideo');
                const avatarEl = IS_TEACHER ? document.getElementById('teacherAvatar') : document.getElementById('mySelfAvatar');

                if (videoEl) {
                    videoEl.srcObject = localStream;
                    videoEl.classList.remove('hidden');
                    avatarEl?.classList.add('hidden');
                }

                ensureTracksInPeerConnections();
                updateMediaButtonsUI();

            } else {
                // إيقاف الكاميرا
                isCamEnabled = false;
                if (localStream) {
                    localStream.getVideoTracks().forEach(track => {
                        track.enabled = false;
                        track.stop();
                        localStream.removeTrack(track);
                    });
                }

                const videoEl = IS_TEACHER ? document.getElementById('teacherVideo') : document.getElementById('mySelfVideo');
                const avatarEl = IS_TEACHER ? document.getElementById('teacherAvatar') : document.getElementById('mySelfAvatar');

                if (videoEl) {
                    videoEl.classList.add('hidden');
                    avatarEl?.classList.remove('hidden');
                }

                ensureTracksInPeerConnections();
                updateMediaButtonsUI();
            }
        } catch (camErr) {
            console.error('Camera toggle error:', camErr);
            alert('⚠️ تعذر تشغيل الكاميرا. يرجى التأكد من توصيل الكاميرا ومنح إذن المتصفح.');
            isCamEnabled = false;
            updateMediaButtonsUI();
        }
    }

    function updateMediaButtonsUI() {
        const btnMic = document.getElementById('btnMyMic');
        const iconMic = document.getElementById('iconMyMic');
        const txtMic = document.getElementById('txtMyMic');
        const mySelfMicStatus = document.getElementById('mySelfMicStatus');

        if (btnMic && iconMic && txtMic) {
            if (isMicEnabled) {
                btnMic.className = "bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-2.5 py-1 rounded-lg flex items-center gap-1 transition shadow-sm";
                iconMic.textContent = "🎙️";
                txtMic.textContent = "المايك شغال";
                if (mySelfMicStatus) {
                    mySelfMicStatus.textContent = "🎙️ صوتك نشط";
                    mySelfMicStatus.className = "bg-black/60 text-emerald-300 px-2 py-0.5 rounded font-bold";
                }
            } else {
                btnMic.className = "bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold px-2.5 py-1 rounded-lg flex items-center gap-1 transition shadow-sm";
                iconMic.textContent = "🔇";
                txtMic.textContent = "المايك مكتوم";
                if (mySelfMicStatus) {
                    mySelfMicStatus.textContent = "🔇 صوتك مكتوم";
                    mySelfMicStatus.className = "bg-black/60 text-rose-300 px-2 py-0.5 rounded font-bold";
                }
            }
        }

        const btnCam = document.getElementById('btnMyCam');
        const iconCam = document.getElementById('iconMyCam');
        const txtCam = document.getElementById('txtMyCam');

        if (btnCam && iconCam && txtCam) {
            if (isCamEnabled) {
                btnCam.className = "bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-2.5 py-1 rounded-lg flex items-center gap-1 transition shadow-sm";
                iconCam.textContent = "📷";
                txtCam.textContent = "الكاميرا نشطة";
            } else {
                btnCam.className = "bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold px-2.5 py-1 rounded-lg flex items-center gap-1 border border-slate-700 transition";
                iconCam.textContent = "📷";
                txtCam.textContent = "الكاميرا مغلقة";
            }
        }
    }

    // 2. محرك WebRTC الصوتي والمرئي (WebRTC Peer Connections Mesh)
    function getOrCreatePeerConnection(remotePeerId, remoteUserId, remoteName, isRemoteStaff) {
        if (peerConnections[remotePeerId]) {
            return peerConnections[remotePeerId];
        }

        const pc = new RTCPeerConnection(ICE_SERVERS);
        peerConnections[remotePeerId] = pc;

        // تهيئة قناة البيانات التفاعلية الفورية (P2P DataChannel) لنقل الدردشة والسبورة في أجزاء من الثانية
        try {
            const dc = pc.createDataChannel('live_classroom_sync', { negotiated: true, id: 0 });
            setupPeerDataChannel(remotePeerId, dc);
        } catch (e) {
            console.warn('DataChannel init fallback:', e);
        }
        pc.ondatachannel = (event) => {
            setupPeerDataChannel(remotePeerId, event.channel);
        };

        // إضافة مسارات الصوت والصورة المحلية إلى الاتصال
        if (localStream) {
            localStream.getTracks().forEach(track => pc.addTrack(track, localStream));
        }

        // عند وصول تيار الصوت أو الصورة من النظير البعيد
        pc.ontrack = (event) => {
            const stream = event.streams[0];
            peerStreams[remotePeerId] = stream;
            attachRemoteMedia(remotePeerId, remoteUserId, remoteName, isRemoteStaff, stream);
        };

        // تبادل مرشحي ICE
        pc.onicecandidate = (event) => {
            if (event.candidate) {
                postSignal(remotePeerId, 'candidate', event.candidate);
            }
        };

        pc.onconnectionstatechange = () => {
            if (pc.connectionState === 'disconnected' || pc.connectionState === 'failed' || pc.connectionState === 'closed') {
                removeRemotePeerUI(remotePeerId, remoteUserId);
            }
        };

        return pc;
    }

    function attachRemoteMedia(remotePeerId, remoteUserId, remoteName, isRemoteStaff, stream) {
        // تشغيل الصوت دائماً عبر عنصر Audio مخفي
        if (!remoteAudios[remotePeerId]) {
            const audioEl = document.createElement('audio');
            audioEl.id = 'audio_' + remotePeerId;
            audioEl.autoplay = true;
            audioEl.playsInline = true;
            audioEl.srcObject = stream;
            document.body.appendChild(audioEl);
            remoteAudios[remotePeerId] = audioEl;

            audioEl.play().catch(e => {
                document.getElementById('audioUnblockBanner')?.classList.remove('hidden');
            });
        } else {
            remoteAudios[remotePeerId].srcObject = stream;
        }

        // إذا كان النظير معلماً والمستخدم الحالي طالباً: نعرض فيديو المعلم في بطاقة المعلم
        if (isRemoteStaff && !IS_TEACHER) {
            const tVideo = document.getElementById('teacherVideo');
            const tAvatar = document.getElementById('teacherAvatar');
            if (tVideo && stream.getVideoTracks().length > 0) {
                tVideo.srcObject = stream;
                tVideo.classList.remove('hidden');
                tAvatar?.classList.add('hidden');
            }
            return;
        }

        // عرض بطاقة الطالب في شبكة الطلاب (Remote Peers Grid)
        renderRemotePeerCard(remotePeerId, remoteUserId, remoteName, isRemoteStaff, stream);
    }

    function renderRemotePeerCard(remotePeerId, remoteUserId, remoteName, isRemoteStaff, stream) {
        document.getElementById('noRemotePeersPlaceholder')?.classList.add('hidden');
        const grid = document.getElementById('remotePeersGrid');
        if (!grid) return;

        let card = document.getElementById('peerCard_' + remoteUserId);
        if (!card) {
            card = document.createElement('div');
            card.id = 'peerCard_' + remoteUserId;
            card.className = "bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden relative shadow-lg min-h-[140px] max-h-[180px] flex flex-col justify-between p-2.5 transition";
            card.innerHTML = `
                <div class="flex justify-between items-center z-10">
                    <span class="text-[11px] font-bold bg-slate-950/80 px-2 py-0.5 rounded-lg text-slate-200 border border-slate-700 flex items-center gap-1">
                        <span>🎒</span>
                        <span class="truncate max-w-[130px]">${remoteName}</span>
                    </span>
                    <span class="text-emerald-400 text-[10px] font-bold flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>صوت متصل</span>
                    </span>
                </div>
                <div class="absolute inset-0 flex items-center justify-center bg-slate-950">
                    <video id="peerVideo_${remoteUserId}" autoplay playsinline class="w-full h-full object-cover hidden"></video>
                    <div id="peerAvatar_${remoteUserId}" class="text-center">
                        <div class="w-12 h-12 bg-indigo-700 rounded-full flex items-center justify-center text-lg mx-auto mb-1 shadow-md">🎓</div>
                        <span class="text-xs text-slate-300 font-bold">${remoteName}</span>
                    </div>
                </div>
                <div class="z-10 flex justify-between items-center text-[10px]">
                    <span id="peerMicBadge_${remoteUserId}" class="bg-black/60 text-slate-300 px-2 py-0.5 rounded">🎙️ المايك شغال</span>
                    ${IS_TEACHER ? `
                        <button type="button" onclick="muteStudent(${remoteUserId})" class="bg-rose-950 hover:bg-rose-900 text-rose-300 border border-rose-800 px-2 py-0.5 rounded font-bold">
                            كتم 🔇
                        </button>
                    ` : ''}
                </div>
            `;
            grid.appendChild(card);
        }

        const vEl = document.getElementById('peerVideo_' + remoteUserId);
        const aEl = document.getElementById('peerAvatar_' + remoteUserId);
        if (vEl && stream && stream.getVideoTracks().length > 0 && stream.getVideoTracks()[0].enabled) {
            vEl.srcObject = stream;
            vEl.classList.remove('hidden');
            aEl?.classList.add('hidden');
        }
    }

    function removeRemotePeerUI(remotePeerId, remoteUserId) {
        if (remoteAudios[remotePeerId]) {
            remoteAudios[remotePeerId].pause();
            remoteAudios[remotePeerId].remove();
            delete remoteAudios[remotePeerId];
        }
        delete peerConnections[remotePeerId];
        delete peerStreams[remotePeerId];
        if (peerDataChannels[remotePeerId]) {
            try { peerDataChannels[remotePeerId].close(); } catch(e){}
            delete peerDataChannels[remotePeerId];
        }

        const card = document.getElementById('peerCard_' + remoteUserId);
        if (card) card.remove();

        const grid = document.getElementById('remotePeersGrid');
        if (grid && grid.children.length <= 1) {
            document.getElementById('noRemotePeersPlaceholder')?.classList.remove('hidden');
        }
    }

    function unblockAudio() {
        Object.values(remoteAudios).forEach(a => {
            try { a.play(); } catch(e){}
        });
        document.getElementById('audioUnblockBanner')?.classList.add('hidden');
    }

    async function postSignal(toPeerId, signalType, payload) {
        try {
            await fetch(`/classroom/${ROOM_ID}/signal`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    from_peer_id: MY_PEER_ID,
                    to_peer_id: toPeerId,
                    signal_type: signalType,
                    payload: payload
                })
            });
        } catch (e) {
            console.error('Signal send error:', e);
        }
    }

    // متغيرات متابعة غرفة الانتظار والقبول وحالة المايك
    let previousPendingIds = new Set();
    let currentToastStudentId = null;
    let requestAdmissionFlag = false;
    let wasMutedByHost = false;

    // تشغيل نغمة جرس ورنين عند وصول طلب انضمام طالب للبث الحي (Web Audio API synthetic chime 🔔)
    function playAdmissionChime() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            const now = ctx.currentTime;
            
            // نغمة التنبيه الأولى (High Tone 880Hz)
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(880, now);
            gain1.gain.setValueAtTime(0.35, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.55);

            // نغمة التنبيه الثانية الرنانة (1108Hz) بعد 120ms
            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(1108.73, now + 0.12);
            gain2.gain.setValueAtTime(0.4, now + 0.12);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.95);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.12);
            osc2.stop(now + 0.95);
        } catch (e) {
            console.log('Admission chime audio blocked or unsupported:', e);
        }
    }

    // 3. المزامنة اللحظية (Sync Loop كل ثانيتين)
    async function syncClassroom() {
        try {
            const params = new URLSearchParams({
                peer_id: MY_PEER_ID,
                has_camera: isCamEnabled ? '1' : '0',
                has_mic: isMicEnabled ? '1' : '0',
                is_hand_raised: isHandRaised ? '1' : '0',
                last_message_id: lastMessageId,
                whiteboard_version: currentWbVersion
            });

            if (requestAdmissionFlag) {
                params.append('request_admission', '1');
                requestAdmissionFlag = false;
            }

            const response = await fetch(`/classroom/${ROOM_ID}/sync?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) return;
            const data = await response.json();
            if (!data.success) return;

            // أ) التحقق من حالة دخول الطالب وغرفة الانتظار (Waiting Room Flow)
            if (!IS_TEACHER) {
                const overlay = document.getElementById('studentWaitingOverlay');
                if (data.admission_status === 'pending') {
                    if (overlay) {
                        overlay.classList.remove('hidden');
                        document.getElementById('overlayWaitingTitle').textContent = 'بانتظار موافقة المعلم للدخول إلى البث... 🔔';
                        document.getElementById('overlayWaitingDesc').textContent = 'تم إرسال إشعار انضمامك إلى المعلم. يرجى الانتظار، سيتم إدخالك إلى الفصل التفاعلي الحي تلقائياً بمجرد موافقة الأستاذ!';
                        document.getElementById('overlayWaitingBadge').innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span> <span>قيد المراجعة لدى المعلم</span>';
                        document.getElementById('btnRetryAdmission')?.classList.add('hidden');
                    }
                    return; // التوقف عن الربط الصوتي والفيديو حتى يُقبل الطالب
                } else if (data.admission_status === 'rejected') {
                    if (overlay) {
                        overlay.classList.remove('hidden');
                        document.getElementById('overlayWaitingTitle').textContent = 'عذراً، تعذر قبول طلب انضمامك حالياً ❌';
                        document.getElementById('overlayWaitingDesc').textContent = 'قام المعلم بتأجيل أو رفض دخولك إلى هذه الجلسة الحية في الوقت الراهن.';
                        document.getElementById('overlayWaitingBadge').innerHTML = '<span class="text-rose-400 font-bold">✕ تم رفض الطلب</span>';
                        document.getElementById('btnRetryAdmission')?.classList.remove('hidden');
                    }
                    return;
                } else {
                    // الطالب مقبول رسمياً (Admitted)
                    if (overlay && !overlay.classList.contains('hidden')) {
                        overlay.classList.add('hidden');
                    }
                }
            }

            // ب) معالجة قرارات الإدارة والمعلم (كتم، طرد، سياسة الفصل)
            if (data.room_control) {
                if (data.room_control.is_kicked) {
                    alert('🚫 تم إنهاء جلستك في هذا البث الحي من قِبل إدارة الفصل.');
                    window.location.href = "{{ auth()->user()->isStudent() ? route('student.portal') : route('home') }}";
                    return;
                }

                if (!IS_TEACHER) {
                    if (data.room_control.is_muted_by_host) {
                        if (isMicEnabled) {
                            isMicEnabled = false;
                            localStream?.getAudioTracks().forEach(t => t.enabled = false);
                            updateMediaButtonsUI();
                            alert('🔇 قام الأستاذ بكتم صوتك في هذه الجلسة.');
                        }
                        wasMutedByHost = true;
                    } else if (wasMutedByHost) {
                        // الأستاذ سمح للطالب بالتحدث (Unmuted by host) -> تفعيل المايك تلقائياً
                        wasMutedByHost = false;
                        isMicEnabled = true;
                        localStream?.getAudioTracks().forEach(t => t.enabled = true);
                        ensureTracksInPeerConnections();
                        updateMediaButtonsUI();
                        alert('🎙️ سمح لك المعلم بالتحدث الآن ومشاركة صوتك في الحصة! تم تفعيل المايكروفون.');
                    }
                } else {
                    // المعلم يظل صوته مفتوحاً دائماً ولا ينقطع
                    if (localStream && !isMicEnabled && !data.room_control.is_muted_by_host) {
                        isMicEnabled = true;
                        localStream.getAudioTracks().forEach(t => t.enabled = true);
                        ensureTracksInPeerConnections();
                        updateMediaButtonsUI();
                    }
                }

                if (data.room_control.policy !== currentRoomPolicy) {
                    currentRoomPolicy = data.room_control.policy;
                    updatePolicyUI(currentRoomPolicy);
                }

                // تحديث زر سياسة غرفة الانتظار للمعلم
                const txtAdmissionToggle = document.getElementById('txtAdmissionToggle');
                if (txtAdmissionToggle && data.room_control.requires_admission !== undefined) {
                    txtAdmissionToggle.textContent = data.room_control.requires_admission
                        ? 'غرفة الانتظار: مفعلة (بموافقة المعلم) 🔔'
                        : 'غرفة الانتظار: ملغاة (دخول مباشر للكل) 🚪';
                }
            }

            // ج) معالجة طلبات انضمام الطلاب المعلقة للمعلم (Pending Requests + Bell Chime 🔔)
            if (IS_TEACHER && data.pending_requests) {
                updatePendingAdmissionRequestsUI(data.pending_requests);
            }

            // د) تحديث قائمة الحاضرين وربط WebRTC مع المتصلين
            updateParticipantsUI(data.participants || []);

            // هـ) معالجة إشارات WebRTC الصوتية الواردة
            if (data.signals && data.signals.length > 0) {
                for (const sig of data.signals) {
                    handleIncomingSignal(sig);
                }
            }

            // و) معالجة الرسائل والتسجيلات الجديدة في المحادثة
            if (data.messages && data.messages.length > 0) {
                for (const msg of data.messages) {
                    if (msg.id > lastMessageId) {
                        lastMessageId = msg.id;
                        appendLiveChatMessage(msg);
                    }
                }
            }

            // ز) مزامنة مساحة الشرح التفاعلية والسبورة ومؤشر الليزر (Zoom Live Sync)
            if (data.whiteboard_version !== undefined) {
                syncWhiteboardFromServer(data.whiteboard_version, data.whiteboard_state);
            }
            if (data.laser_state !== undefined) {
                renderRemoteLaser(data.laser_state);
            }

        } catch (err) {
            console.error('Classroom sync error:', err);
        }
    }

    // بدء حلقة المزامنة التلقائية (تسريع المزامنة الخلفية إلى ثانية واحدة لدعم الاتصال الفوري)
    setInterval(syncClassroom, 1000);
    setTimeout(syncClassroom, 500);

    // 4. معالجة إشارات WebRTC
    async function handleIncomingSignal(sig) {
        const fromPeer = sig.from_peer_id;
        const type = sig.signal_type;
        const payload = sig.payload;

        const pc = getOrCreatePeerConnection(fromPeer, 0, 'مشارك', false);

        try {
            if (type === 'offer') {
                await pc.setRemoteDescription(new RTCSessionDescription(payload));
                const answer = await pc.createAnswer();
                await pc.setLocalDescription(answer);
                postSignal(fromPeer, 'answer', answer);
            } else if (type === 'answer') {
                await pc.setRemoteDescription(new RTCSessionDescription(payload));
            } else if (type === 'candidate' && payload) {
                await pc.addIceCandidate(new RTCIceCandidate(payload));
            }
        } catch (e) {
            console.error('Signaling processing error:', e);
        }
    }

    // ربط مبدئي مع الأقران المتصلين (Polite WebRTC Offer initiator)
    function initiatePeerOffers(participants) {
        participants.forEach(p => {
            if (p.peer_id === MY_PEER_ID) return;

            // نحدد بادئ الاتصال بشكل حتمي لتجنب تصادم الـ Offers
            if (MY_PEER_ID < p.peer_id && !peerConnections[p.peer_id]) {
                const pc = getOrCreatePeerConnection(p.peer_id, p.user_id, p.name, p.is_staff);
                pc.createOffer({ offerToReceiveAudio: true, offerToReceiveVideo: true })
                    .then(offer => pc.setLocalDescription(offer))
                    .then(() => postSignal(p.peer_id, 'offer', pc.localDescription))
                    .catch(console.error);
            }
        });
    }

    // 5. تحديث واجهة قائمة الحاضرين ورفع اليد
    function updateParticipantsUI(participants) {
        // تحديث العدادات
        const count = participants.length;
        document.getElementById('headerParticipantsCount').textContent = count;
        document.getElementById('tabCountBadge').textContent = count;
        document.getElementById('remoteCountBadge').textContent = Math.max(0, count - 1);

        // إطلاق اتصالات WebRTC مع الأعضاء الجدد
        initiatePeerOffers(participants);

        // فحص رافعي الأيدي
        const handRaisers = participants.filter(p => p.is_hand_raised && !p.is_staff);
        const handHeaderBadge = document.getElementById('headerHandBadge');
        const handContainer = document.getElementById('handRaisedContainer');
        const handList = document.getElementById('handRaisedList');

        if (handRaisers.length > 0) {
            handHeaderBadge?.classList.remove('hidden');
            handHeaderBadge.textContent = `✋ ${handRaisers.length} يطلب الكلمة`;
            
            if (handContainer && handList) {
                handContainer.classList.remove('hidden');
                handList.innerHTML = handRaisers.map(p => `
                    <div class="flex justify-between items-center p-2 bg-slate-900 rounded-xl border border-amber-500/50">
                        <span class="font-bold text-amber-200">${p.name}</span>
                        ${IS_TEACHER ? `
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="allowStudentToSpeak(${p.user_id})" class="bg-emerald-600 hover:bg-emerald-500 text-white px-2 py-1 rounded text-[10px] font-bold">
                                    السماح بالكلام 🎙️
                                </button>
                                <button type="button" onclick="lowerStudentHand(${p.user_id})" class="bg-slate-800 text-slate-300 px-2 py-1 rounded text-[10px]">
                                    خفض اليد ✕
                                </button>
                            </div>
                        ` : ''}
                    </div>
                `).join('');
            }
        } else {
            handHeaderBadge?.classList.add('hidden');
            handContainer?.classList.add('hidden');
        }

        // بناء قائمة الحاضرين في التبويب
        const listEl = document.getElementById('participantsList');
        if (!listEl) return;

        listEl.innerHTML = participants.map(p => {
            const isMe = p.user_id === CURRENT_USER_ID;
            const roleBadge = p.is_staff 
                ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">معلم/إدارة</span>'
                : '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">طالب</span>';

            const micIcon = p.has_mic && !p.is_muted_by_host 
                ? '<span class="text-emerald-400" title="المايك مفتوح">🎙️</span>' 
                : '<span class="text-rose-400" title="المايك مكتوم">🔇</span>';

            const camIcon = p.has_camera 
                ? '<span class="text-indigo-400" title="الكاميرا تعمل">📷</span>' 
                : '<span class="text-slate-600" title="الكاميرا مغلقة">⬛</span>';

            const handIcon = p.is_hand_raised ? '<span class="text-amber-400 animate-bounce" title="يطلب الكلمة">✋</span>' : '';

            return `
                <div class="p-3 bg-slate-950 rounded-2xl border ${p.is_hand_raised ? 'border-amber-500 bg-amber-950/20' : 'border-slate-800'} space-y-2">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                            <strong class="text-slate-200 text-xs">${p.name} ${isMe ? '(أنت)' : ''}</strong>
                            ${roleBadge}
                        </div>
                        <div class="flex items-center gap-1.5 text-xs">
                            ${handIcon}
                            ${micIcon}
                            ${camIcon}
                        </div>
                    </div>

                    ${IS_TEACHER && !isMe ? `
                        <div class="flex items-center gap-1 pt-1 border-t border-slate-900 text-[10px]">
                            ${p.is_muted_by_host ? `
                                <button type="button" onclick="allowStudentToSpeak(${p.user_id})" class="px-2 py-1 bg-emerald-900/60 hover:bg-emerald-800 text-emerald-300 rounded-lg font-bold">
                                    فك الكتم 🎙️
                                </button>
                            ` : `
                                <button type="button" onclick="muteStudent(${p.user_id})" class="px-2 py-1 bg-rose-950 hover:bg-rose-900 text-rose-300 rounded-lg font-bold">
                                    كتم الصوت 🔇
                                </button>
                            `}
                            <button type="button" onclick="mentionUser('${addslashes(p.name)}')" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg">
                                محادثة 💬
                            </button>
                            <button type="button" onclick="kickStudent(${p.user_id}, '${addslashes(p.name)}')" class="px-2 py-1 bg-slate-800 hover:bg-rose-900 text-slate-400 hover:text-white rounded-lg mr-auto">
                                طرد 🚫
                            </button>
                        </div>
                    ` : ''}
                </div>
            `;
        }).join('');
    }

    function updatePolicyUI(policy) {
        const iconPolicy = document.getElementById('iconPolicy');
        const txtPolicy = document.getElementById('txtPolicy');
        const dIcon = document.getElementById('drawerPolicyIcon');
        const dText = document.getElementById('drawerPolicyText');

        if (policy === 'open') {
            if (iconPolicy) iconPolicy.textContent = "📢";
            if (txtPolicy) txtPolicy.textContent = "الكلام: مسموح";
            if (dIcon) dIcon.textContent = "📢";
            if (dText) dText.textContent = "الكلام مسموح للكل";
        } else {
            if (iconPolicy) iconPolicy.textContent = "🔒";
            if (txtPolicy) txtPolicy.textContent = "الكلام: بإذن فقط";
            if (dIcon) dIcon.textContent = "🔒";
            if (dText) dText.textContent = "الكلام بإذن فقط";
        }
    }

    // 6. إجراءات المعلم الإدارية
    async function toggleRoomPolicy() {
        if (!IS_TEACHER) return;
        try {
            const res = await fetch(`/classroom/${ROOM_ID}/controls`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'toggle_policy' })
            });
            const data = await res.json();
            if (data.success) {
                currentRoomPolicy = data.policy;
                updatePolicyUI(data.policy);
            }
        } catch (e) { console.error(e); }
    }

    async function muteStudent(userId) {
        if (!IS_TEACHER) return;
        try {
            await fetch(`/classroom/${ROOM_ID}/controls`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'mute_user', user_id: userId })
            });
        } catch (e) { console.error(e); }
    }

    async function allowStudentToSpeak(userId) {
        if (!IS_TEACHER) return;
        try {
            await fetch(`/classroom/${ROOM_ID}/controls`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'allow_speak', user_id: userId })
            });
        } catch (e) { console.error(e); }
    }

    async function lowerStudentHand(userId) {
        if (!IS_TEACHER) return;
        try {
            await fetch(`/classroom/${ROOM_ID}/controls`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'lower_hand', user_id: userId })
            });
        } catch (e) { console.error(e); }
    }

    async function kickStudent(userId, name) {
        if (!IS_TEACHER) return;
        if (!confirm(`هل أنت متأكد من استبعاد الطالب [${name}] من هذه الحصة؟`)) return;
        try {
            await fetch(`/classroom/${ROOM_ID}/controls`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'kick_user', user_id: userId })
            });
        } catch (e) { console.error(e); }
    }

    function muteAllParticipants() {
        if (!IS_TEACHER) return;
        toggleRoomPolicy();
    }

    // 6.1 إدارة غرفة الانتظار والموافقة على انضمام الطلاب (Waiting Room & Admission Control)
    function updatePendingAdmissionRequestsUI(requests) {
        const count = requests.length;
        const currentIds = new Set(requests.map(r => r.user_id));

        // فحص هل يوجد طلاب جدد طلبوا الانضمام لم يكونوا مسجلين في الفحص السابق
        let hasNewRequest = false;
        let newestStudent = null;
        for (const req of requests) {
            if (!previousPendingIds.has(req.user_id)) {
                hasNewRequest = true;
                newestStudent = req;
            }
        }

        previousPendingIds = currentIds;

        // إطلاق نغمة الجرس وتفعيل نافذة الإشعار الفورية (Toast) عند وجود طالب جديد
        if (hasNewRequest && newestStudent) {
            playAdmissionChime();
            showAdmissionToast(newestStudent);
        } else if (count === 0) {
            hideAdmissionToast();
        }

        // تحديث شارة الترويسة العلوية
        const btnHeader = document.getElementById('btnHeaderAdmission');
        const countHeader = document.getElementById('headerAdmissionCount');
        if (btnHeader && countHeader) {
            if (count > 0) {
                btnHeader.classList.remove('hidden');
                countHeader.textContent = count;
            } else {
                btnHeader.classList.add('hidden');
            }
        }

        // تحديث حاوية طلبات الانضمام في اللوحة الجانبية
        const container = document.getElementById('admissionPendingContainer');
        const list = document.getElementById('admissionPendingList');
        if (container && list) {
            if (count > 0) {
                container.classList.remove('hidden');
                list.innerHTML = requests.map(r => `
                    <div class="flex justify-between items-center p-2 bg-slate-950/80 rounded-xl border border-amber-600/50 gap-2">
                        <div class="min-w-0">
                            <span class="font-bold text-amber-200 block truncate text-xs">${r.name}</span>
                            <span class="text-[10px] text-slate-400 font-mono block">${r.student_code || ''} • طلب: ${r.requested_at}</span>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" onclick="admitStudent(${r.user_id})" class="bg-emerald-600 hover:bg-emerald-500 text-white px-2 py-1 rounded-lg text-[10px] font-black shadow transition">
                                قبول ✓
                            </button>
                            <button type="button" onclick="rejectStudent(${r.user_id})" class="bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white px-2 py-1 rounded-lg text-[10px] transition">
                                رفض ✕
                            </button>
                        </div>
                    </div>
                `).join('');
            } else {
                container.classList.add('hidden');
            }
        }
    }

    function showAdmissionToast(student) {
        currentToastStudentId = student.user_id;
        const toast = document.getElementById('admissionAlertToast');
        const nameEl = document.getElementById('toastStudentName');
        if (toast && nameEl) {
            nameEl.textContent = `الطالب: ${student.name} (${student.student_code || 'كود الطالب'})`;
            toast.classList.remove('hidden');
        }
    }

    function hideAdmissionToast() {
        currentToastStudentId = null;
        document.getElementById('admissionAlertToast')?.classList.add('hidden');
    }

    async function admitStudent(userId) {
        if (!IS_TEACHER) return;
        try {
            await fetch(`/classroom/${ROOM_ID}/controls`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'admit_student', user_id: userId })
            });
            hideAdmissionToast();
            syncClassroom();
        } catch (e) { console.error('admitStudent error:', e); }
    }

    async function admitAllStudents() {
        if (!IS_TEACHER) return;
        try {
            await fetch(`/classroom/${ROOM_ID}/controls`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'admit_all' })
            });
            hideAdmissionToast();
            syncClassroom();
        } catch (e) { console.error('admitAll error:', e); }
    }

    async function rejectStudent(userId) {
        if (!IS_TEACHER) return;
        try {
            await fetch(`/classroom/${ROOM_ID}/controls`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'reject_student', user_id: userId })
            });
            hideAdmissionToast();
            syncClassroom();
        } catch (e) { console.error('rejectStudent error:', e); }
    }

    async function toggleRequiresAdmission() {
        if (!IS_TEACHER) return;
        try {
            const res = await fetch(`/classroom/${ROOM_ID}/controls`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'toggle_requires_admission' })
            });
            const data = await res.json();
            if (data.success) {
                const txt = document.getElementById('txtAdmissionToggle');
                if (txt) {
                    txt.textContent = data.requires_admission
                        ? 'غرفة الانتظار: مفعلة (بموافقة المعلم) 🔔'
                        : 'غرفة الانتظار: ملغاة (دخول مباشر للكل) 🚪';
                }
            }
        } catch (e) { console.error(e); }
    }

    function admitCurrentToastStudent() {
        if (currentToastStudentId) {
            admitStudent(currentToastStudentId);
        }
    }

    function rejectCurrentToastStudent() {
        if (currentToastStudentId) {
            rejectStudent(currentToastStudentId);
        }
    }

    function retryAdmissionRequest() {
        requestAdmissionFlag = true;
        const badge = document.getElementById('overlayWaitingBadge');
        if (badge) {
            badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span> <span>جاري إعادة إرسال الطلب...</span>';
        }
        syncClassroom();
    }

    // فتح قفل تشغيل الصوت عند أي نقرة أو لمسة على الصفحة تلقائياً
    document.addEventListener('click', unblockAudio, { passive: true });
    document.addEventListener('touchstart', unblockAudio, { passive: true });

    // 7. رفع وخفض يد الطالب
    async function toggleHandRaise() {
        try {
            const res = await fetch(`/classroom/${ROOM_ID}/hand`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.success) {
                isHandRaised = data.is_hand_raised;
                const btn = document.getElementById('btnRaiseHand');
                const txt = document.getElementById('txtRaiseHand');
                if (isHandRaised) {
                    btn.className = "bg-amber-600 hover:bg-amber-500 text-slate-950 font-black text-xs px-3 py-1.5 rounded-xl flex items-center gap-1.5 transition shadow-md animate-pulse";
                    txt.textContent = "يدك مرفوعة ✋";
                } else {
                    btn.className = "bg-slate-800 hover:bg-slate-700 text-amber-300 text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-700 flex items-center gap-1.5 transition";
                    txt.textContent = "طلب الكلمة";
                }
            }
        } catch (e) { console.error(e); }
    }

    // 8. المحادثة الحية المتزامنة وإرسال الرسائل والتسجيلات
    async function sendLiveChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('liveChatInput');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';

        try {
            const res = await fetch(`/classroom/${ROOM_ID}/messages`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ type: 'text', content: text })
            });
            const data = await res.json();
            if (data.success && data.message) {
                if (data.message.id > lastMessageId) {
                    lastMessageId = data.message.id;
                    appendLiveChatMessage(data.message);
                }
                broadcastP2P({ type: 'chat_message', message: data.message });
                syncClassroom();
            }
        } catch (err) { console.error(err); }
    }

    function appendLiveChatMessage(msg) {
        const container = document.getElementById('liveChatMessagesContainer');
        if (!container) return;

        // التحقق من عدم التكرار
        if (document.getElementById('chatMsg_' + msg.id)) return;

        const isSystem = msg.type === 'system';
        const div = document.createElement('div');
        div.id = 'chatMsg_' + msg.id;

        if (isSystem) {
            div.className = "p-2 rounded-xl bg-slate-950/80 border border-slate-800 text-[11px] text-amber-300 text-center font-bold";
            div.textContent = msg.content;
        } else {
            const isMine = msg.user_id === CURRENT_USER_ID;
            div.className = `p-3 rounded-2xl border ${msg.is_student ? 'border-emerald-800/40 bg-emerald-950/20' : 'border-indigo-800/40 bg-indigo-950/20'} space-y-1.5`;
            div.innerHTML = `
                <div class="flex justify-between items-center text-[10px]">
                    <span class="font-bold flex items-center gap-1 text-slate-300">
                        <span>${msg.is_student ? '🎒' : '👨‍🏫'}</span>
                        <span>${msg.user_name} ${isMine ? '(أنت)' : ''}</span>
                    </span>
                    <span class="text-slate-500 font-mono">${msg.time}</span>
                </div>
                ${msg.content ? `<div class="text-xs text-slate-200 leading-relaxed">${msg.content}</div>` : ''}
                ${msg.type === 'image' || msg.type === 'worksheet' ? `
                    <div class="cursor-pointer overflow-hidden rounded-xl border border-slate-700 bg-black aspect-video max-h-36 flex items-center justify-center mt-1" onclick="openLiveImageModal('${msg.file_path}', '${addslashes(msg.content || '')}')">
                        <img src="${msg.file_path}" class="w-full h-full object-cover">
                    </div>
                ` : ''}
                ${msg.type === 'audio' ? `
                    <div class="bg-purple-950/40 p-2 rounded-xl border border-purple-800/40 mt-1">
                        <audio controls src="${msg.file_path}" class="w-full h-8"></audio>
                    </div>
                ` : ''}
            `;
        }

        container.appendChild(div);
        container.scrollTop = container.scrollHeight;

        // إشعار ضوئي إذا كانت اللوحة الجانبية مغلقة
        const panel = document.getElementById('liveInteractivePanel');
        if (panel && panel.classList.contains('hidden')) {
            document.getElementById('unreadMessagesDot')?.classList.remove('hidden');
        }
    }

    function mentionUser(name) {
        toggleSidePanel('chat');
        const input = document.getElementById('liveChatInput');
        if (input) {
            input.value = `@${name} ` + input.value;
            input.focus();
        }
    }

    // 9. مسجل ومشارك الصوتيات المباشر
    let liveRecorder;
    let liveAudioChunks = [];
    let liveRecordedBlob = null;
    let liveVoiceInterval;
    let liveVoiceSeconds = 0;

    async function startLiveVoiceRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            liveRecorder = new MediaRecorder(stream);
            liveAudioChunks = [];

            liveRecorder.ondataavailable = (e) => {
                if (e.data.size > 0) liveAudioChunks.push(e.data);
            };

            liveRecorder.onstop = () => {
                liveRecordedBlob = new Blob(liveAudioChunks, { type: 'audio/webm' });
                const url = URL.createObjectURL(liveRecordedBlob);
                const prev = document.getElementById('liveVoicePreview');
                prev.src = url;
                document.getElementById('liveVoicePreviewContainer').classList.remove('hidden');
                document.getElementById('btnLiveVoiceSend').classList.remove('hidden');
                document.getElementById('liveVoiceStatus').innerText = '✅ تم إنهاء التسجيل، يمكنك الاستماع أو الإرسال.';
                document.getElementById('liveVoiceStatus').classList.remove('animate-pulse');
                stream.getTracks().forEach(t => t.stop());
            };

            liveRecorder.start();
            document.getElementById('liveVoiceBar').classList.remove('hidden');
            document.getElementById('liveVoicePreviewContainer').classList.add('hidden');
            document.getElementById('btnLiveVoiceSend').classList.add('hidden');
            document.getElementById('btnLiveVoiceStop').classList.remove('hidden');
            document.getElementById('liveVoiceStatus').innerText = '🔴 جاري التسجيل الصوتي الآن...';
            document.getElementById('liveVoiceStatus').classList.add('animate-pulse');

            liveVoiceSeconds = 0;
            liveVoiceInterval = setInterval(() => {
                liveVoiceSeconds++;
                const m = Math.floor(liveVoiceSeconds / 60);
                const s = liveVoiceSeconds % 60;
                document.getElementById('liveVoiceTimer').innerText = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
            }, 1000);

        } catch (err) {
            alert('تعذر الوصول إلى المايكروفون للتسجيل. تأكد من منح الإذن للمتصفح.');
            console.error(err);
        }
    }

    function stopLiveVoiceRecording() {
        if (liveRecorder && liveRecorder.state === 'recording') {
            liveRecorder.stop();
            clearInterval(liveVoiceInterval);
            document.getElementById('btnLiveVoiceStop').classList.add('hidden');
        }
    }

    function cancelLiveVoiceRecording() {
        if (liveRecorder && liveRecorder.state === 'recording') {
            liveRecorder.stop();
        }
        clearInterval(liveVoiceInterval);
        document.getElementById('liveVoiceBar').classList.add('hidden');
    }

    async function sendLiveVoiceRecording() {
        if (!liveRecordedBlob) return;

        const formData = new FormData();
        formData.append('_token', CSRF_TOKEN);
        formData.append('type', 'audio');
        formData.append('content', '🎙️ تسجيل صوتي من ' + CURRENT_USER_NAME);
        formData.append('attachment', liveRecordedBlob, 'voice_' + Date.now() + '.webm');

        try {
            const res = await fetch(`/classroom/${ROOM_ID}/messages`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (data.success && data.message) {
                if (data.message.id > lastMessageId) {
                    lastMessageId = data.message.id;
                    appendLiveChatMessage(data.message);
                }
                broadcastP2P({ type: 'chat_message', message: data.message });
                syncClassroom();
            }
        } catch (e) { console.error(e); }

        cancelLiveVoiceRecording();
    }

    // 10. رفع ورقة إجابة أو صورة
    function triggerLiveAnswerUpload() {
        document.getElementById('liveAnswerFileInput').click();
    }

    async function handleLiveAnswerUpload(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];

        const formData = new FormData();
        formData.append('_token', CSRF_TOKEN);
        formData.append('type', 'image');
        formData.append('content', '📸 ورقة إجابة فورية: ' + file.name);
        formData.append('attachment', file);

        try {
            const res = await fetch(`/classroom/${ROOM_ID}/messages`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (data.success && data.message) {
                if (data.message.id > lastMessageId) {
                    lastMessageId = data.message.id;
                    appendLiveChatMessage(data.message);
                }
                broadcastP2P({ type: 'chat_message', message: data.message });
                syncClassroom();
            }
        } catch (e) { console.error(e); }

        input.value = '';
    }

    // 11. التبديل والتحكم باللوحة الجانبية
    function toggleSidePanel(tab) {
        const panel = document.getElementById('liveInteractivePanel');
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            switchLiveTab(tab || 'chat');
        } else {
            switchLiveTab(tab || 'chat');
        }
        if (tab === 'chat') {
            document.getElementById('unreadMessagesDot')?.classList.add('hidden');
        }
    }

    function closeSidePanel() {
        document.getElementById('liveInteractivePanel').classList.add('hidden');
    }

    function switchLiveTab(tab) {
        const tabChat = document.getElementById('liveTabChat');
        const tabParticipants = document.getElementById('liveTabParticipants');
        const tabResources = document.getElementById('liveTabResources');

        const btnChat = document.getElementById('tabBtnChat');
        const btnParticipants = document.getElementById('tabBtnParticipants');
        const btnResources = document.getElementById('tabBtnResources');

        // إخفاء الكل أولاً
        tabChat.classList.add('hidden');
        tabParticipants.classList.add('hidden');
        tabResources.classList.add('hidden');

        btnChat.className = "px-2.5 py-1.5 rounded-lg text-slate-400 hover:text-white transition flex items-center gap-1";
        btnParticipants.className = "px-2.5 py-1.5 rounded-lg text-slate-400 hover:text-white transition flex items-center gap-1";
        btnResources.className = "px-2.5 py-1.5 rounded-lg text-slate-400 hover:text-white transition flex items-center gap-1";

        if (tab === 'chat') {
            tabChat.classList.remove('hidden');
            btnChat.className = "px-2.5 py-1.5 rounded-lg bg-indigo-600 text-white transition flex items-center gap-1 shadow-sm";
        } else if (tab === 'participants') {
            tabParticipants.classList.remove('hidden');
            btnParticipants.className = "px-2.5 py-1.5 rounded-lg bg-indigo-600 text-white transition flex items-center gap-1 shadow-sm";
        } else {
            tabResources.classList.remove('hidden');
            btnResources.className = "px-2.5 py-1.5 rounded-lg bg-indigo-600 text-white transition flex items-center gap-1 shadow-sm";
        }
    }

    // 12. العداد التنازلي ونظام النجوم والشرائح
    let sessionSeconds = 25 * 60;
    const timerEl = document.getElementById('sessionTimer');
    setInterval(() => {
        if (sessionSeconds <= 0) return;
        sessionSeconds--;
        const m = Math.floor(sessionSeconds / 60);
        const s = sessionSeconds % 60;
        timerEl.textContent = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }, 1000);

    let stars = 0;
    function rewardStar() {
        stars++;
        const counter = document.getElementById('starsCount');
        counter.textContent = stars;
        counter.classList.add('star-pop');
        setTimeout(() => counter.classList.remove('star-pop'), 400);

        if (document.getElementById('modalStarsInput')) {
            document.getElementById('modalStarsInput').value = stars;
        }

        // إرسال تنبيه في الشات لجميع الطلاب
        fetch(`/classroom/${ROOM_ID}/messages`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ type: 'system', content: `⭐ كافأ المعلم طلاب المجموعة بنجمة تشجيعية جديدة! (إجمالي النجوم: ${stars})` })
        }).catch(console.error);

        alert('🌟 أحسنت! تم منح نجمة تشجيعية.');
    }

    let currentSlide = 3;
    const totalSlides = 8;
    function nextSlide() {
        if (currentSlide < totalSlides) {
            currentSlide++;
            updateSlideUI();
        }
    }
    function prevSlide() {
        if (currentSlide > 1) {
            currentSlide--;
            updateSlideUI();
        }
    }
    function updateSlideUI() {
        const slideHeader = document.getElementById('slideNumberBadge');
        if (slideHeader) {
            slideHeader.textContent = `شريحة المنهج 0${currentSlide} من 0${totalSlides}`;
        }
    }

    function openLiveImageModal(src, title) {
        document.getElementById('liveImageModalImg').src = src;
        document.getElementById('liveImageModalTitle').innerText = title || 'ورقة إجابة الطالب';
        document.getElementById('liveImageModal').classList.remove('hidden');
    }
    function closeLiveImageModal() {
        document.getElementById('liveImageModal').classList.add('hidden');
    }

    function openReportModal() {
        document.getElementById('reportModal').classList.remove('hidden');
    }
    function closeReportModal() {
        document.getElementById('reportModal').classList.add('hidden');
    }

    // ==========================================
    // محرك مساحة الشرح والسبورة التفاعلية (Zoom Live Engine)
    // ==========================================
    let currentWbVersion = 0;
    let wbCurrentTool = 'pen'; // 'select' | 'pen' | 'brush' | 'highlighter' | 'magic' | 'laser' | 'shapes' | 'stamp' | 'note' | 'text' | 'eraser'
    let wbActiveShape = 'rect'; // 'rect' | 'rounded_rect' | 'circle' | 'triangle' | 'line' | 'arrow' | 'double_arrow' | 'star' | 'checkmark' | 'cross' | 'bubble'
    let wbShapeFilled = false;
    let wbActiveStamp = '⭐';
    let wbCurrentColor = '#ef4444';
    let wbCurrentSize = 4;
    let wbStrokes = [];
    let wbUndoStack = [];
    let wbRedoStack = [];
    let wbCurrentBg = { type: 'whiteboard', url: null, title: 'سبورة بيضاء تفاعلية', page_num: 1 };
    let wbZoom = 1.0;
    let wbIsDrawing = false;
    let wbCurrentStroke = null;
    let wbPdfDoc = null;
    let wbTotalPages = 1;
    let wbCurrentPage = 1;
    let wbMagicStrokes = [];
    let wbLastLaserSentAt = 0;
    let wbTextCoords = null;
    let wbDebounceTimer = null;
    let isStudentCollabAllowed = false;

    function initWhiteboard() {
        const drawCanvas = document.getElementById('whiteboardDrawCanvas');
        if (!drawCanvas) return;

        // تهيئة أبعاد الكانفاس الأولى
        renderBlankBackground('whiteboard');

        // ربط أحداث الماوس للرسم
        drawCanvas.addEventListener('mousedown', onWbMouseDown);
        window.addEventListener('mousemove', onWbMouseMove);
        window.addEventListener('mouseup', onWbMouseUp);

        // ربط أحداث اللمس للهواتف والأجهزة اللوحية
        drawCanvas.addEventListener('touchstart', onWbTouchStart, { passive: false });
        window.addEventListener('touchmove', onWbTouchMove, { passive: false });
        window.addEventListener('touchend', onWbTouchEnd, { passive: false });

        // إغلاق القوائم المنبثقة عند النقر في أي مكان آخر
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#wbLeftToolbar') && !e.target.closest('#btnBooksDropdown')) {
                closeAllWbFlyouts();
            }
        });

        // حلقة التلاشي للقلم السحري (Magic vanishing ink)
        requestAnimationFrame(magicStrokesAnimationLoop);

        // تحديث المظهر الافتراضي للأدوات
        selectWbTool('pen');
        setWbColor('#ef4444');
        setWbSize(4);
    }

    function getWbCoords(e) {
        const canvas = document.getElementById('whiteboardDrawCanvas');
        if (!canvas) return { x: 0, y: 0, normX: 0, normY: 0 };
        const rect = canvas.getBoundingClientRect();
        const clientX = e.touches && e.touches[0] ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches && e.touches[0] ? e.touches[0].clientY : e.clientY;

        const x = (clientX - rect.left) * (canvas.width / rect.width);
        const y = (clientY - rect.top) * (canvas.height / rect.height);
        const normX = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
        const normY = Math.max(0, Math.min(1, (clientY - rect.top) / rect.height));

        return { x, y, normX, normY };
    }

    function selectWbTool(tool) {
        wbCurrentTool = tool;
        document.querySelectorAll('.wb-tool-btn').forEach(b => {
            b.classList.remove('bg-indigo-600', 'text-white');
            b.classList.add('text-slate-400');
        });
        const targetBtnId = 'toolBtn_' + (tool === 'shapes' ? 'shapes' : (tool === 'stamp' ? 'stamp' : tool));
        const activeBtn = document.getElementById(targetBtnId);
        if (activeBtn) {
            activeBtn.classList.add('bg-indigo-600', 'text-white');
            activeBtn.classList.remove('text-slate-400');
        }
        closeAllWbFlyouts();

        const drawCanvas = document.getElementById('whiteboardDrawCanvas');
        if (drawCanvas) {
            drawCanvas.className = 'absolute inset-0 w-full h-full object-contain z-[10]';
            if (tool === 'select') {
                drawCanvas.style.pointerEvents = 'none';
                drawCanvas.classList.add('cursor-default');
            } else {
                drawCanvas.style.pointerEvents = 'auto';
                if (tool === 'pen' || tool === 'brush') drawCanvas.classList.add('cursor-pen');
                else if (tool === 'highlighter') drawCanvas.classList.add('cursor-highlighter');
                else if (tool === 'eraser') drawCanvas.classList.add('cursor-eraser');
                else if (tool === 'laser') drawCanvas.classList.add('cursor-laser');
                else if (tool === 'text') drawCanvas.classList.add('cursor-text');
                else if (tool === 'note' || tool === 'stamp') drawCanvas.classList.add('cursor-pointer');
                else drawCanvas.classList.add('cursor-crosshair');
            }
        }

        if (tool !== 'laser') {
            broadcastLaserHide();
        }
    }

    function toggleShapeFill() {
        wbShapeFilled = !wbShapeFilled;
        const stateEl = document.getElementById('shapeFillState');
        if (stateEl) {
            stateEl.textContent = wbShapeFilled ? 'ممتلئ 🎨' : 'مفرغ 🔲';
        }
    }

    function toggleStampsFlyout() {
        const m = document.getElementById('stampsFlyoutMenu');
        const isOpen = m && !m.classList.contains('hidden');
        closeAllWbFlyouts();
        if (m && !isOpen) m.classList.remove('hidden');
    }

    function selectWbStamp(stampChar) {
        wbActiveStamp = stampChar;
        selectWbTool('stamp');
        const iconEl = document.getElementById('activeStampIcon');
        if (iconEl) iconEl.textContent = stampChar;
        closeAllWbFlyouts();
    }

    function selectWbShape(shape) {
        wbActiveShape = shape;
        selectWbTool('shapes');
        const iconMap = {
            rect: '🔲',
            rounded_rect: '◽',
            circle: '⭕',
            triangle: '🔺',
            line: '➖',
            arrow: '➡️',
            double_arrow: '↔️',
            star: '⭐',
            checkmark: '✔️',
            cross: '❌',
            bubble: '💬'
        };
        const iconEl = document.getElementById('activeShapeIcon');
        if (iconEl) iconEl.textContent = iconMap[shape] || '🔲';
        closeAllWbFlyouts();
    }

    function setWbColor(color) {
        wbCurrentColor = color;
        const preview = document.getElementById('activeColorPreview');
        if (preview) preview.style.backgroundColor = color;
        const customInput = document.getElementById('wbCustomColorInput');
        if (customInput) customInput.value = color;
        closeAllWbFlyouts();
    }

    function setWbSize(size) {
        wbCurrentSize = size;
        const preview = document.getElementById('activeSizePreview');
        if (preview) preview.textContent = size + 'px';
        closeAllWbFlyouts();
    }

    function toggleShapesFlyout() {
        const m = document.getElementById('shapesFlyoutMenu');
        const isOpen = m && !m.classList.contains('hidden');
        closeAllWbFlyouts();
        if (m && !isOpen) m.classList.remove('hidden');
    }
    function toggleColorFlyout() {
        const m = document.getElementById('colorFlyoutMenu');
        const isOpen = m && !m.classList.contains('hidden');
        closeAllWbFlyouts();
        if (m && !isOpen) m.classList.remove('hidden');
    }
    function toggleSizeFlyout() {
        const m = document.getElementById('sizeFlyoutMenu');
        const isOpen = m && !m.classList.contains('hidden');
        closeAllWbFlyouts();
        if (m && !isOpen) m.classList.remove('hidden');
    }
    function toggleBooksDropdown() {
        const m = document.getElementById('booksDropdownMenu');
        if (m) m.classList.toggle('hidden');
    }
    function closeAllWbFlyouts() {
        document.getElementById('shapesFlyoutMenu')?.classList.add('hidden');
        document.getElementById('stampsFlyoutMenu')?.classList.add('hidden');
        document.getElementById('colorFlyoutMenu')?.classList.add('hidden');
        document.getElementById('sizeFlyoutMenu')?.classList.add('hidden');
        document.getElementById('booksDropdownMenu')?.classList.add('hidden');
    }

    function onWbMouseDown(e) {
        if (e.target.id !== 'whiteboardDrawCanvas') return;
        if (!IS_TEACHER && !isStudentCollabAllowed) return;

        const coords = getWbCoords(e);

        if (wbCurrentTool === 'laser') {
            renderLocalLaser(coords.normX, coords.normY);
            if (IS_TEACHER) broadcastLaserMove(coords.normX, coords.normY);
            return;
        }

        if (wbCurrentTool === 'stamp') {
            const stroke = {
                tool: 'stamp',
                stampChar: wbActiveStamp,
                color: wbCurrentColor,
                size: wbCurrentSize,
                x: coords.x,
                y: coords.y,
                createdAt: Date.now()
            };
            wbStrokes.push(stroke);
            wbUndoStack.push(JSON.parse(JSON.stringify(wbStrokes)));
            wbRedoStack = [];
            redrawWhiteboard();
            commitWhiteboardChange();
            broadcastP2P({ type: 'wb_stroke', stroke: stroke });
            return;
        }

        if (wbCurrentTool === 'note') {
            const noteText = prompt('اكتب نص الملاحظة اللاصقة (Sticky Note):', 'ملاحظة هامة 📌');
            if (noteText && noteText.trim()) {
                const stroke = {
                    tool: 'note',
                    text: noteText.trim(),
                    bgColor: wbCurrentColor === '#ffffff' ? '#1e293b' : wbCurrentColor,
                    color: '#ffffff',
                    size: wbCurrentSize,
                    x: coords.x,
                    y: coords.y,
                    width: 160,
                    height: 95,
                    createdAt: Date.now()
                };
                wbStrokes.push(stroke);
                wbUndoStack.push(JSON.parse(JSON.stringify(wbStrokes)));
                wbRedoStack = [];
                redrawWhiteboard();
                commitWhiteboardChange();
                broadcastP2P({ type: 'wb_stroke', stroke: stroke });
            }
            return;
        }

        if (wbCurrentTool === 'text') {
            openWbTextInput(coords.x, coords.y, e.clientX, e.clientY);
            return;
        }

        wbIsDrawing = true;
        wbCurrentStroke = {
            tool: wbCurrentTool,
            shape: wbActiveShape,
            filled: wbShapeFilled,
            color: wbCurrentColor,
            size: wbCurrentSize,
            points: [{ x: coords.x, y: coords.y }],
            x1: coords.x,
            y1: coords.y,
            x2: coords.x,
            y2: coords.y,
            createdAt: Date.now()
        };

        if (wbCurrentTool === 'eraser') {
            eraseNearbyStrokes(coords.x, coords.y);
        }
    }

    function onWbMouseMove(e) {
        const coords = getWbCoords(e);

        if (wbCurrentTool === 'laser') {
            renderLocalLaser(coords.normX, coords.normY);
            if (IS_TEACHER) throttleBroadcastLaser(coords.normX, coords.normY);
            return;
        }

        if (!wbIsDrawing || !wbCurrentStroke) return;

        if (wbCurrentTool === 'pen' || wbCurrentTool === 'brush' || wbCurrentTool === 'highlighter' || wbCurrentTool === 'magic') {
            wbCurrentStroke.points.push({ x: coords.x, y: coords.y });
            redrawWhiteboard(true);
        } else if (wbCurrentTool === 'shapes') {
            wbCurrentStroke.x2 = coords.x;
            wbCurrentStroke.y2 = coords.y;
            redrawWhiteboard(true);
        } else if (wbCurrentTool === 'eraser') {
            eraseNearbyStrokes(coords.x, coords.y);
        }
    }

    function onWbMouseUp(e) {
        if (!wbIsDrawing || !wbCurrentStroke) {
            wbIsDrawing = false;
            return;
        }
        wbIsDrawing = false;

        if (wbCurrentTool === 'magic') {
            wbCurrentStroke.expiresAt = Date.now() + 2500;
            wbMagicStrokes.push(wbCurrentStroke);
            broadcastP2P({ type: 'wb_magic_stroke', stroke: wbCurrentStroke });
            wbCurrentStroke = null;
            redrawWhiteboard();
            return;
        }

        if (wbCurrentTool === 'eraser') {
            wbCurrentStroke = null;
            commitWhiteboardChange();
            return;
        }

        wbStrokes.push(wbCurrentStroke);
        wbUndoStack.push(JSON.parse(JSON.stringify(wbStrokes)));
        wbRedoStack = [];
        broadcastP2P({ type: 'wb_stroke', stroke: wbCurrentStroke });
        wbCurrentStroke = null;
        redrawWhiteboard();
        commitWhiteboardChange();
    }

    function onWbTouchStart(e) {
        if (e.touches.length === 1) {
            e.preventDefault();
            onWbMouseDown(e);
        }
    }
    function onWbTouchMove(e) {
        if (e.touches.length === 1) {
            e.preventDefault();
            onWbMouseMove(e);
        }
    }
    function onWbTouchEnd(e) {
        onWbMouseUp(e);
    }

    function eraseNearbyStrokes(x, y) {
        const threshold = wbCurrentSize * 6;
        const initialCount = wbStrokes.length;
        wbStrokes = wbStrokes.filter(s => {
            if (s.points && s.points.length > 0) {
                return !s.points.some(p => Math.hypot(p.x - x, p.y - y) < threshold);
            }
            if (s.tool === 'shapes') {
                return Math.hypot(s.x1 - x, s.y1 - y) >= threshold && Math.hypot(s.x2 - x, s.y2 - y) >= threshold;
            }
            if (s.tool === 'text' || s.tool === 'stamp' || s.tool === 'note') {
                return Math.hypot(s.x - x, s.y - y) >= threshold;
            }
            return true;
        });

        if (wbStrokes.length !== initialCount) {
            redrawWhiteboard();
        }
    }

    function renderSingleStroke(ctx, stroke, opacity = 1.0) {
        ctx.save();
        ctx.globalAlpha = opacity;
        ctx.strokeStyle = stroke.color;
        ctx.fillStyle = stroke.color;
        ctx.lineWidth = stroke.size;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        if (stroke.tool === 'highlighter') {
            ctx.globalAlpha = 0.35 * opacity;
            ctx.lineWidth = stroke.size * 4;
        } else if (stroke.tool === 'magic') {
            ctx.shadowBlur = 12;
            ctx.shadowColor = stroke.color;
        }

        if (stroke.tool === 'brush') {
            const pts = stroke.points;
            if (!pts || pts.length === 0) { ctx.restore(); return; }
            const angle = Math.PI / 4;
            const w = Math.max(3, stroke.size * 2);
            const dx = (w / 2) * Math.cos(angle);
            const dy = (w / 2) * Math.sin(angle);
            ctx.fillStyle = stroke.color;
            for (let i = 0; i < pts.length; i++) {
                ctx.beginPath();
                ctx.moveTo(pts[i].x - dx, pts[i].y + dy);
                ctx.lineTo(pts[i].x + dx, pts[i].y - dy);
                if (i > 0) {
                    ctx.lineTo(pts[i - 1].x + dx, pts[i - 1].y - dy);
                    ctx.lineTo(pts[i - 1].x - dx, pts[i - 1].y + dy);
                }
                ctx.closePath();
                ctx.fill();
            }
        } else if (stroke.tool === 'stamp') {
            ctx.font = `${Math.max(32, stroke.size * 8)}px sans-serif`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(stroke.stampChar || '⭐', stroke.x, stroke.y);
        } else if (stroke.tool === 'note') {
            const x = stroke.x, y = stroke.y, w = stroke.width || 160, h = stroke.height || 95;
            ctx.save();
            ctx.shadowColor = 'rgba(0,0,0,0.45)';
            ctx.shadowBlur = 12;
            ctx.shadowOffsetY = 5;
            ctx.fillStyle = stroke.bgColor || '#1e293b';
            drawRoundRect(ctx, x, y, w, h, 10);
            ctx.fill();
            ctx.restore();

            ctx.strokeStyle = stroke.borderColor || '#6366f1';
            ctx.lineWidth = 2;
            drawRoundRect(ctx, x, y, w, h, 10);
            ctx.stroke();

            // دبوس الملاحظة العلوي
            ctx.fillStyle = '#ef4444';
            ctx.beginPath();
            ctx.arc(x + w / 2, y + 8, 4, 0, Math.PI * 2);
            ctx.fill();

            // كتابة نص الملاحظة
            ctx.fillStyle = '#ffffff';
            ctx.font = `bold 12px 'Cairo', sans-serif`;
            ctx.textAlign = 'right';
            ctx.direction = 'rtl';
            drawWrappedText(ctx, stroke.text || '', x + w - 10, y + 26, w - 20, 18);
        } else if (stroke.tool === 'pen' || stroke.tool === 'highlighter' || stroke.tool === 'magic') {
            const pts = stroke.points;
            if (!pts || pts.length === 0) { ctx.restore(); return; }
            if (pts.length === 1) {
                ctx.beginPath();
                ctx.arc(pts[0].x, pts[0].y, ctx.lineWidth / 2, 0, Math.PI * 2);
                ctx.fill();
            } else {
                ctx.beginPath();
                ctx.moveTo(pts[0].x, pts[0].y);
                for (let i = 1; i < pts.length - 1; i++) {
                    const midX = (pts[i].x + pts[i + 1].x) / 2;
                    const midY = (pts[i].y + pts[i + 1].y) / 2;
                    ctx.quadraticCurveTo(pts[i].x, pts[i].y, midX, midY);
                }
                ctx.lineTo(pts[pts.length - 1].x, pts[pts.length - 1].y);
                ctx.stroke();
            }
        } else if (stroke.tool === 'shapes') {
            const x1 = stroke.x1, y1 = stroke.y1, x2 = stroke.x2, y2 = stroke.y2;
            const rx = Math.min(x1, x2), ry = Math.min(y1, y2), rw = Math.abs(x2 - x1), rh = Math.abs(y2 - y1);

            if (stroke.shape === 'rect') {
                if (stroke.filled) {
                    ctx.fillStyle = stroke.color + '45';
                    ctx.fillRect(rx, ry, rw, rh);
                }
                ctx.strokeRect(rx, ry, rw, rh);
            } else if (stroke.shape === 'rounded_rect') {
                drawRoundRect(ctx, rx, ry, rw, rh, 12);
                if (stroke.filled) {
                    ctx.fillStyle = stroke.color + '45';
                    ctx.fill();
                }
                ctx.stroke();
            } else if (stroke.shape === 'circle') {
                const radX = rw / 2;
                const radY = rh / 2;
                const cx = rx + radX;
                const cy = ry + radY;
                ctx.beginPath();
                ctx.ellipse(cx, cy, radX, radY, 0, 0, Math.PI * 2);
                if (stroke.filled) {
                    ctx.fillStyle = stroke.color + '45';
                    ctx.fill();
                }
                ctx.stroke();
            } else if (stroke.shape === 'triangle') {
                ctx.beginPath();
                ctx.moveTo(rx + rw / 2, ry);
                ctx.lineTo(rx + rw, ry + rh);
                ctx.lineTo(rx, ry + rh);
                ctx.closePath();
                if (stroke.filled) {
                    ctx.fillStyle = stroke.color + '45';
                    ctx.fill();
                }
                ctx.stroke();
            } else if (stroke.shape === 'line') {
                ctx.beginPath();
                ctx.moveTo(x1, y1);
                ctx.lineTo(x2, y2);
                ctx.stroke();
            } else if (stroke.shape === 'arrow') {
                drawArrow(ctx, x1, y1, x2, y2, stroke.size * 3.8);
            } else if (stroke.shape === 'double_arrow') {
                drawDoubleArrow(ctx, x1, y1, x2, y2, stroke.size * 3.8);
            } else if (stroke.shape === 'star') {
                const cx = rx + rw / 2;
                const cy = ry + rh / 2;
                const outer = Math.max(rw, rh) / 2;
                drawStar(ctx, cx, cy, 5, outer, outer * 0.45, stroke.filled);
            } else if (stroke.shape === 'checkmark') {
                drawCheckmark(ctx, x1, y1, x2, y2);
            } else if (stroke.shape === 'cross') {
                drawCross(ctx, x1, y1, x2, y2);
            } else if (stroke.shape === 'bubble') {
                drawSpeechBubble(ctx, x1, y1, x2, y2, stroke.filled);
            }
        } else if (stroke.tool === 'text') {
            ctx.font = `bold ${Math.max(16, stroke.size * 4)}px 'Cairo', sans-serif`;
            ctx.fillText(stroke.text, stroke.x, stroke.y);
        }

        ctx.restore();
    }

    function drawRoundRect(ctx, x, y, width, height, radius = 8) {
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height - radius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
    }

    function drawWrappedText(ctx, text, x, y, maxWidth, lineHeight) {
        const words = text.split(' ');
        let line = '';
        for (let n = 0; n < words.length; n++) {
            const testLine = line + words[n] + ' ';
            const metrics = ctx.measureText(testLine);
            const testWidth = metrics.width;
            if (testWidth > maxWidth && n > 0) {
                ctx.fillText(line, x, y);
                line = words[n] + ' ';
                y += lineHeight;
            } else {
                line = testLine;
            }
        }
        ctx.fillText(line, x, y);
    }

    function drawArrow(ctx, fromX, fromY, toX, toY, headLength = 16) {
        const dx = toX - fromX;
        const dy = toY - fromY;
        const angle = Math.atan2(dy, dx);
        ctx.beginPath();
        ctx.moveTo(fromX, fromY);
        ctx.lineTo(toX, toY);
        ctx.stroke();

        ctx.beginPath();
        ctx.moveTo(toX, toY);
        ctx.lineTo(toX - headLength * Math.cos(angle - Math.PI / 6), toY - headLength * Math.sin(angle - Math.PI / 6));
        ctx.lineTo(toX - headLength * Math.cos(angle + Math.PI / 6), toY - headLength * Math.sin(angle + Math.PI / 6));
        ctx.closePath();
        ctx.fill();
    }

    function drawDoubleArrow(ctx, fromX, fromY, toX, toY, headLength = 16) {
        drawArrow(ctx, fromX, fromY, toX, toY, headLength);
        drawArrow(ctx, toX, toY, fromX, fromY, headLength);
    }

    function drawStar(ctx, cx, cy, spikes = 5, outerRadius = 25, innerRadius = 12, filled = false) {
        let rot = Math.PI / 2 * 3;
        let x = cx;
        let y = cy;
        const step = Math.PI / spikes;

        ctx.beginPath();
        ctx.moveTo(cx, cy - outerRadius);
        for (let i = 0; i < spikes; i++) {
            x = cx + Math.cos(rot) * outerRadius;
            y = cy + Math.sin(rot) * outerRadius;
            ctx.lineTo(x, y);
            rot += step;

            x = cx + Math.cos(rot) * innerRadius;
            y = cy + Math.sin(rot) * innerRadius;
            ctx.lineTo(x, y);
            rot += step;
        }
        ctx.lineTo(cx, cy - outerRadius);
        ctx.closePath();
        if (filled) {
            ctx.fillStyle = ctx.strokeStyle + '45';
            ctx.fill();
        }
        ctx.stroke();
    }

    function drawCheckmark(ctx, x1, y1, x2, y2) {
        const minX = Math.min(x1, x2), maxX = Math.max(x1, x2);
        const minY = Math.min(y1, y2), maxY = Math.max(y1, y2);
        const midX = minX + (maxX - minX) * 0.35;
        ctx.beginPath();
        ctx.moveTo(minX, minY + (maxY - minY) * 0.55);
        ctx.lineTo(midX, maxY);
        ctx.lineTo(maxX, minY);
        ctx.stroke();
    }

    function drawCross(ctx, x1, y1, x2, y2) {
        ctx.beginPath();
        ctx.moveTo(x1, y1);
        ctx.lineTo(x2, y2);
        ctx.moveTo(x2, y1);
        ctx.lineTo(x1, y2);
        ctx.stroke();
    }

    function drawSpeechBubble(ctx, x1, y1, x2, y2, filled = false) {
        const rx = Math.min(x1, x2), ry = Math.min(y1, y2);
        const rw = Math.abs(x2 - x1), rh = Math.abs(y2 - y1);
        const bodyH = Math.max(20, rh * 0.8);
        const radius = 12;

        ctx.beginPath();
        ctx.moveTo(rx + radius, ry);
        ctx.lineTo(rx + rw - radius, ry);
        ctx.quadraticCurveTo(rx + rw, ry, rx + rw, ry + radius);
        ctx.lineTo(rx + rw, ry + bodyH - radius);
        ctx.quadraticCurveTo(rx + rw, ry + bodyH, rx + rw - radius, ry + bodyH);
        ctx.lineTo(rx + rw * 0.5 + 15, ry + bodyH);
        ctx.lineTo(rx + rw * 0.3, ry + rh);
        ctx.lineTo(rx + rw * 0.5 - 10, ry + bodyH);
        ctx.lineTo(rx + radius, ry + bodyH);
        ctx.quadraticCurveTo(rx, ry + bodyH, rx, ry + bodyH - radius);
        ctx.lineTo(rx, ry + radius);
        ctx.quadraticCurveTo(rx, ry, rx + radius, ry);
        ctx.closePath();
        if (filled) {
            ctx.fillStyle = ctx.strokeStyle + '45';
            ctx.fill();
        }
        ctx.stroke();
    }

    function redrawWhiteboard(withCurrent = false) {
        const canvas = document.getElementById('whiteboardDrawCanvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        for (const s of wbStrokes) {
            renderSingleStroke(ctx, s);
        }

        const now = Date.now();
        wbMagicStrokes = wbMagicStrokes.filter(s => s.expiresAt > now);
        for (const s of wbMagicStrokes) {
            const remaining = Math.max(0, s.expiresAt - now);
            const opacity = Math.min(1.0, remaining / 1000);
            renderSingleStroke(ctx, s, opacity);
        }

        if (withCurrent && wbCurrentStroke) {
            renderSingleStroke(ctx, wbCurrentStroke);
        }
    }

    function magicStrokesAnimationLoop() {
        if (wbMagicStrokes.length > 0) {
            redrawWhiteboard(wbIsDrawing);
        }
        requestAnimationFrame(magicStrokesAnimationLoop);
    }

    function renderLocalLaser(normX, normY) {
        const laserEl = document.getElementById('liveLaserPointer');
        if (!laserEl) return;
        laserEl.style.left = (normX * 100) + '%';
        laserEl.style.top = (normY * 100) + '%';
        laserEl.classList.remove('hidden');
        const badge = document.getElementById('laserUserBadge');
        if (badge) badge.textContent = `🔴 ${CURRENT_USER_NAME}`;
    }

    function renderRemoteLaser(state) {
        const laserEl = document.getElementById('liveLaserPointer');
        if (!laserEl) return;
        if (state && state.active) {
            laserEl.style.left = (state.x * 100) + '%';
            laserEl.style.top = (state.y * 100) + '%';
            laserEl.classList.remove('hidden');
            const badge = document.getElementById('laserUserBadge');
            if (badge && state.user_name) {
                badge.textContent = `🔴 ${state.user_name}`;
            }
        } else {
            if (wbCurrentTool !== 'laser') {
                laserEl.classList.add('hidden');
            }
        }
    }

    function throttleBroadcastLaser(normX, normY) {
        const now = Date.now();
        if (now - wbLastLaserSentAt < 80) return;
        wbLastLaserSentAt = now;
        broadcastLaserMove(normX, normY);
    }

    function broadcastLaserMove(normX, normY) {
        broadcastP2P({ type: 'laser', x: normX, y: normY, name: CURRENT_USER_NAME });
        fetch(`/classroom/${ROOM_ID}/whiteboard`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ action: 'laser_move', x: normX, y: normY, active: true })
        }).catch(() => {});
    }

    function broadcastLaserHide() {
        broadcastP2P({ type: 'laser_hide' });
        fetch(`/classroom/${ROOM_ID}/whiteboard`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ action: 'laser_hide' })
        }).catch(() => {});
        if (wbCurrentTool !== 'laser') {
            document.getElementById('liveLaserPointer')?.classList.add('hidden');
        }
    }

    function undoStroke() {
        if (wbStrokes.length === 0) return;
        const removed = wbStrokes.pop();
        wbRedoStack.push(removed);
        redrawWhiteboard();
        commitWhiteboardChange();
        broadcastP2P({ type: 'wb_undo' });
    }
    function redoStroke() {
        if (wbRedoStack.length === 0) return;
        const restored = wbRedoStack.pop();
        wbStrokes.push(restored);
        redrawWhiteboard();
        commitWhiteboardChange();
        broadcastP2P({ type: 'wb_stroke', stroke: restored });
    }
    function clearWhiteboard() {
        if (!IS_TEACHER && !isStudentCollabAllowed) return;
        if (confirm('هل ترغب بمسح جميع الشروحات والملاحظات المكتوبة على السبورة؟')) {
            wbStrokes = [];
            wbUndoStack = [];
            wbRedoStack = [];
            redrawWhiteboard();
            broadcastP2P({ type: 'wb_clear' });
            fetch(`/classroom/${ROOM_ID}/whiteboard`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'clear' })
            }).catch(console.error);
        }
    }

    function openWbTextInput(canvasX, canvasY, clientX, clientY) {
        const wrapper = document.getElementById('wbTextInputWrapper');
        const input = document.getElementById('wbTextInput');
        const container = document.getElementById('whiteboardContainer');
        if (!wrapper || !input || !container) return;
        const rect = container.getBoundingClientRect();

        wbTextCoords = { x: canvasX, y: canvasY };
        wrapper.style.left = (clientX - rect.left) + 'px';
        wrapper.style.top = (clientY - rect.top) + 'px';
        wrapper.classList.remove('hidden');
        input.value = '';
        input.focus();
    }

    function handleWbTextKey(e) {
        if (e.key === 'Enter') {
            const input = document.getElementById('wbTextInput');
            const text = input.value.trim();
            if (text && wbTextCoords) {
                const stroke = {
                    tool: 'text',
                    color: wbCurrentColor,
                    size: wbCurrentSize,
                    text: text,
                    x: wbTextCoords.x,
                    y: wbTextCoords.y + (wbCurrentSize * 3)
                };
                wbStrokes.push(stroke);
                redrawWhiteboard();
                commitWhiteboardChange();
                broadcastP2P({ type: 'wb_stroke', stroke: stroke });
            }
            document.getElementById('wbTextInputWrapper')?.classList.add('hidden');
            wbTextCoords = null;
        } else if (e.key === 'Escape') {
            document.getElementById('wbTextInputWrapper')?.classList.add('hidden');
            wbTextCoords = null;
        }
    }

    function openBrowserModal() {
        document.getElementById('wbBrowserModal')?.classList.remove('hidden');
        document.getElementById('wbGoogleSearchInput')?.focus();
    }
    function openVideoModal() {
        document.getElementById('wbVideoModal')?.classList.remove('hidden');
        document.getElementById('wbYoutubeUrlInput')?.focus();
    }
    function closeWbMediaModals() {
        document.getElementById('wbBrowserModal')?.classList.add('hidden');
        document.getElementById('wbVideoModal')?.classList.add('hidden');
    }

    function executeBrowserSearch() {
        const input = document.getElementById('wbGoogleSearchInput');
        const query = input ? input.value.trim() : '';
        const url = query
            ? `https://www.google.com/search?igu=1&q=${encodeURIComponent(query)}`
            : 'https://www.google.com/search?igu=1';
        const title = query ? `بحث جوجل: ${query}` : 'محرك بحث جوجل التفاعلي';
        openBrowserInWorkspace(url, title);
        closeWbMediaModals();
    }

    function executeDirectUrlOpen() {
        const input = document.getElementById('wbDirectUrlInput');
        let url = input ? input.value.trim() : '';
        if (!url) return;
        if (!url.startsWith('http://') && !url.startsWith('https://')) {
            url = 'https://' + url;
        }
        openBrowserInWorkspace(url, `تصفح: ${url}`);
        closeWbMediaModals();
    }

    function openPresetGame(url, title) {
        openBrowserInWorkspace(url, title);
        closeWbMediaModals();
    }

    function reloadBrowserIframe() {
        const iframe = document.getElementById('whiteboardBrowserIframe');
        if (iframe && iframe.src) {
            iframe.src = iframe.src;
        }
    }

    function parseYoutubeId(url) {
        if (!url) return null;
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }

    function executeYoutubeOpen() {
        const input = document.getElementById('wbYoutubeUrlInput');
        const url = input ? input.value.trim() : '';
        if (!url) return;
        const ytId = parseYoutubeId(url);
        if (ytId) {
            const embedUrl = `https://www.youtube.com/embed/${ytId}?autoplay=1&rel=0`;
            openVideoInWorkspace(embedUrl, `فيديو يوتيوب (${ytId})`, true);
        } else {
            openVideoInWorkspace(url, 'فيديو تعليمي مباشر', false);
        }
        closeWbMediaModals();
    }

    function hideMediaLayers() {
        const browserWrapper = document.getElementById('whiteboardBrowserWrapper');
        const videoWrapper = document.getElementById('whiteboardVideoWrapper');
        const videoEl = document.getElementById('whiteboardHtml5Video');
        const ytFrame = document.getElementById('whiteboardYoutubeFrame');
        const browserIframe = document.getElementById('whiteboardBrowserIframe');

        if (browserWrapper) browserWrapper.classList.add('hidden');
        if (videoWrapper) videoWrapper.classList.add('hidden');
        if (videoEl) { videoEl.pause(); videoEl.src = ''; videoEl.classList.add('hidden'); }
        if (ytFrame) { ytFrame.src = ''; ytFrame.classList.add('hidden'); }
        if (browserIframe) { browserIframe.src = 'about:blank'; }
    }

    function ensureCanvasLayerDimensions() {
        const container = document.getElementById('whiteboardContainer');
        const drawCanvas = document.getElementById('whiteboardDrawCanvas');
        const bgCanvas = document.getElementById('whiteboardBgCanvas');
        if (container && drawCanvas) {
            const w = container.clientWidth || 1600;
            const h = container.clientHeight || 1000;
            drawCanvas.width = w;
            drawCanvas.height = h;
            if (bgCanvas) {
                bgCanvas.width = w;
                bgCanvas.height = h;
            }
            redrawWhiteboard();
        }
    }

    function renderBrowserBackground(url, title) {
        hideMediaLayers();
        const browserWrapper = document.getElementById('whiteboardBrowserWrapper');
        const browserIframe = document.getElementById('whiteboardBrowserIframe');
        const currentUrlInput = document.getElementById('wbBrowserCurrentUrl');
        if (browserWrapper && browserIframe) {
            browserWrapper.classList.remove('hidden');
            if (currentUrlInput) currentUrlInput.value = url;
            if (browserIframe.src !== url) {
                browserIframe.src = url;
            }
        }
        ensureCanvasLayerDimensions();
    }

    function renderVideoBackground(url, title) {
        hideMediaLayers();
        const videoWrapper = document.getElementById('whiteboardVideoWrapper');
        const videoEl = document.getElementById('whiteboardHtml5Video');
        const ytFrame = document.getElementById('whiteboardYoutubeFrame');
        const titleBadge = document.getElementById('wbVideoTitleBadge');
        if (titleBadge) titleBadge.textContent = title || 'فيديو تعليمي';

        if (videoWrapper) {
            videoWrapper.classList.remove('hidden');
            const isYoutube = url.includes('youtube.com') || url.includes('youtu.be') || url.includes('/embed/');
            if (isYoutube && ytFrame) {
                const ytId = parseYoutubeId(url);
                const embedUrl = ytId ? `https://www.youtube.com/embed/${ytId}?autoplay=1&rel=0` : url;
                ytFrame.src = embedUrl;
                ytFrame.classList.remove('hidden');
            } else if (videoEl) {
                videoEl.src = url;
                videoEl.classList.remove('hidden');
                videoEl.play().catch(() => {});
            }
        }
        ensureCanvasLayerDimensions();
    }

    function openBrowserInWorkspace(url, title) {
        if (!IS_TEACHER) return;
        closeAllWbFlyouts();
        const modeIcon = document.getElementById('wbModeIcon');
        const modeText = document.getElementById('wbModeText');
        if (modeIcon) modeIcon.textContent = '🌐';
        if (modeText) modeText.textContent = title;
        document.getElementById('wbPageNavControls')?.classList.add('hidden');

        wbCurrentBg = { type: 'browser', url: url, title: title, page_num: 1 };
        wbStrokes = [];
        renderBrowserBackground(url, title);
        selectWbTool('select');
        broadcastP2P({ type: 'wb_bg', state: wbCurrentBg });

        fetch(`/classroom/${ROOM_ID}/whiteboard`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ action: 'set_background', bg_type: 'browser', bg_url: url, bg_title: title, page_num: 1 })
        }).catch(console.error);
    }

    function openVideoInWorkspace(url, title, isYouTube = false) {
        if (!IS_TEACHER) return;
        closeAllWbFlyouts();
        const modeIcon = document.getElementById('wbModeIcon');
        const modeText = document.getElementById('wbModeText');
        if (modeIcon) modeIcon.textContent = '🎬';
        if (modeText) modeText.textContent = title;
        document.getElementById('wbPageNavControls')?.classList.add('hidden');

        wbCurrentBg = { type: 'video', url: url, title: title, page_num: 1 };
        wbStrokes = [];
        renderVideoBackground(url, title);
        selectWbTool('select');
        broadcastP2P({ type: 'wb_bg', state: wbCurrentBg });

        fetch(`/classroom/${ROOM_ID}/whiteboard`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ action: 'set_background', bg_type: 'video', bg_url: url, bg_title: title, page_num: 1 })
        }).catch(console.error);
    }

    function closeWbMediaLayer() {
        hideMediaLayers();
        setWhiteboardBg('whiteboard');
    }

    async function openResourceInWorkspace(url, title, type, pageNum = 1) {
        if (!IS_TEACHER) return;
        closeAllWbFlyouts();
        hideMediaLayers();

        const modeIcon = document.getElementById('wbModeIcon');
        const modeText = document.getElementById('wbModeText');
        if (modeIcon) modeIcon.textContent = (type === 'pdf') ? '📖' : '🖼️';
        if (modeText) modeText.textContent = title;

        wbCurrentBg = { type: type, url: url, title: title, page_num: pageNum };
        wbStrokes = [];
        redrawWhiteboard();
        broadcastP2P({ type: 'wb_bg', state: wbCurrentBg });

        if (type === 'pdf') {
            await renderPdfPage(url, pageNum);
            document.getElementById('wbPageNavControls')?.classList.remove('hidden');
        } else {
            renderImageBackground(url);
            document.getElementById('wbPageNavControls')?.classList.add('hidden');
        }

        fetch(`/classroom/${ROOM_ID}/whiteboard`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ action: 'set_background', bg_type: type, bg_url: url, bg_title: title, page_num: pageNum })
        }).catch(console.error);
    }

    function setWhiteboardBg(type) {
        if (!IS_TEACHER) return;
        closeAllWbFlyouts();
        hideMediaLayers();
        const titles = { whiteboard: 'سبورة بيضاء تفاعلية', blackboard: 'سبورة سوداء', grid: 'شبكة مربعات الشرح' };
        const icons = { whiteboard: '📋', blackboard: '⬛', grid: '📐' };

        document.getElementById('wbModeIcon').textContent = icons[type] || '📋';
        document.getElementById('wbModeText').textContent = titles[type] || 'سبورة تفاعلية';
        document.getElementById('wbPageNavControls')?.classList.add('hidden');

        wbCurrentBg = { type: type, url: null, title: titles[type], page_num: 1 };
        renderBlankBackground(type);
        broadcastP2P({ type: 'wb_bg', state: wbCurrentBg });

        fetch(`/classroom/${ROOM_ID}/whiteboard`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ action: 'set_background', bg_type: type, bg_url: null, bg_title: titles[type], page_num: 1 })
        }).catch(console.error);
    }

    async function handleWbLocalFileUpload(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const formData = new FormData();
        formData.append('action', 'upload_bg_file');
        formData.append('file', file);

        try {
            const res = await fetch(`/classroom/${ROOM_ID}/whiteboard`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                if (data.bg_type === 'video') {
                    openVideoInWorkspace(data.url, data.title, false);
                } else {
                    openResourceInWorkspace(data.url, data.title, data.bg_type, 1);
                }
            }
        } catch (e) {
            console.error('Upload whiteboard file error:', e);
        }
        input.value = '';
    }

    async function renderPdfPage(url, pageNum) {
        if (!window.pdfjsLib) {
            console.warn('pdfjsLib is not loaded');
            return;
        }
        try {
            if (!wbPdfDoc || wbPdfDoc._url !== url) {
                wbPdfDoc = await pdfjsLib.getDocument(url).promise;
                wbPdfDoc._url = url;
                wbTotalPages = wbPdfDoc.numPages;
                const totalText = document.getElementById('wbTotalPagesText');
                if (totalText) totalText.textContent = wbTotalPages;
            }

            wbCurrentPage = Math.min(Math.max(1, pageNum), wbTotalPages);
            const currentText = document.getElementById('wbCurrentPageText');
            if (currentText) currentText.textContent = wbCurrentPage;

            const page = await wbPdfDoc.getPage(wbCurrentPage);
            const viewport = page.getViewport({ scale: 1.8 });

            const bgCanvas = document.getElementById('whiteboardBgCanvas');
            const drawCanvas = document.getElementById('whiteboardDrawCanvas');
            bgCanvas.width = viewport.width;
            bgCanvas.height = viewport.height;
            drawCanvas.width = viewport.width;
            drawCanvas.height = viewport.height;

            const ctx = bgCanvas.getContext('2d');
            await page.render({ canvasContext: ctx, viewport }).promise;
            redrawWhiteboard();
        } catch (e) {
            console.error('PDF rendering error:', e);
        }
    }

    async function changeWbPage(delta) {
        if (!wbPdfDoc || !IS_TEACHER) return;
        const targetPage = wbCurrentPage + delta;
        if (targetPage >= 1 && targetPage <= wbTotalPages) {
            await openResourceInWorkspace(wbCurrentBg.url, wbCurrentBg.title, 'pdf', targetPage);
        }
    }

    function renderImageBackground(url) {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => {
            const bgCanvas = document.getElementById('whiteboardBgCanvas');
            const drawCanvas = document.getElementById('whiteboardDrawCanvas');
            const w = img.naturalWidth || 1600;
            const h = img.naturalHeight || 1000;
            bgCanvas.width = w;
            bgCanvas.height = h;
            drawCanvas.width = w;
            drawCanvas.height = h;

            const ctx = bgCanvas.getContext('2d');
            ctx.clearRect(0, 0, w, h);
            ctx.drawImage(img, 0, 0, w, h);
            redrawWhiteboard();
        };
        img.src = url;
    }

    function renderBlankBackground(type) {
        const bgCanvas = document.getElementById('whiteboardBgCanvas');
        const drawCanvas = document.getElementById('whiteboardDrawCanvas');
        if (!bgCanvas || !drawCanvas) return;
        const w = 1600;
        const h = 1000;
        bgCanvas.width = w;
        bgCanvas.height = h;
        drawCanvas.width = w;
        drawCanvas.height = h;

        const ctx = bgCanvas.getContext('2d');
        if (type === 'blackboard') {
            ctx.fillStyle = '#0b1329';
            ctx.fillRect(0, 0, w, h);
        } else if (type === 'grid') {
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, w, h);
            ctx.strokeStyle = '#e2e8f0';
            ctx.lineWidth = 1;
            for (let x = 0; x < w; x += 35) {
                ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, h); ctx.stroke();
            }
            for (let y = 0; y < h; y += 35) {
                ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(w, y); ctx.stroke();
            }
        } else {
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, w, h);
        }
        redrawWhiteboard();
    }

    function zoomWb(delta) {
        wbZoom = Math.min(2.5, Math.max(0.5, wbZoom + delta));
        applyWbZoom();
    }
    function resetWbZoom() {
        wbZoom = 1.0;
        applyWbZoom();
    }
    function applyWbZoom() {
        const bg = document.getElementById('whiteboardBgCanvas');
        const draw = document.getElementById('whiteboardDrawCanvas');
        const label = document.getElementById('wbZoomLabel');
        if (bg && draw) {
            const transform = `scale(${wbZoom})`;
            bg.style.transform = transform;
            draw.style.transform = transform;
        }
        if (label) label.textContent = Math.round(wbZoom * 100) + '%';
    }

    function downloadWhiteboardSnapshot() {
        const bgCanvas = document.getElementById('whiteboardBgCanvas');
        const drawCanvas = document.getElementById('whiteboardDrawCanvas');
        if (!bgCanvas || !drawCanvas) return;
        const exportCanvas = document.createElement('canvas');
        exportCanvas.width = bgCanvas.width;
        exportCanvas.height = bgCanvas.height;
        const ctx = exportCanvas.getContext('2d');

        ctx.drawImage(bgCanvas, 0, 0);
        ctx.drawImage(drawCanvas, 0, 0);

        const link = document.createElement('a');
        link.download = `ASOTS_Lesson_Whiteboard_${Date.now()}.png`;
        link.href = exportCanvas.toDataURL('image/png');
        link.click();
    }

    function commitWhiteboardChange() {
        if (!IS_TEACHER) return;
        clearTimeout(wbDebounceTimer);
        wbDebounceTimer = setTimeout(() => {
            fetch(`/classroom/${ROOM_ID}/whiteboard`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({
                    action: 'save_state',
                    state: {
                        bg_type: wbCurrentBg.type,
                        bg_url: wbCurrentBg.url,
                        bg_title: wbCurrentBg.title,
                        page_num: wbCurrentBg.page_num,
                        strokes: wbStrokes
                    }
                })
            }).then(r => r.json()).then(data => {
                if (data.whiteboard_version) currentWbVersion = data.whiteboard_version;
            }).catch(console.error);
        }, 80);
    }

    function syncWhiteboardFromServer(serverVersion, serverState) {
        if (serverVersion > currentWbVersion) {
            currentWbVersion = serverVersion;
            if (serverState) {
                applyServerWhiteboardState(serverState);
            }
        }
    }

    async function applyServerWhiteboardState(state) {
        if (!state) return;
        const bgChanged = (state.bg_type !== wbCurrentBg.type || state.bg_url !== wbCurrentBg.url || state.page_num !== wbCurrentBg.page_num);

        wbCurrentBg = {
            type: state.bg_type || 'whiteboard',
            url: state.bg_url || null,
            title: state.bg_title || 'سبورة الشرح',
            page_num: state.page_num || 1
        };

        const modeText = document.getElementById('wbModeText');
        if (modeText) modeText.textContent = wbCurrentBg.title;
        const modeIcon = document.getElementById('wbModeIcon');
        if (modeIcon) {
            if (wbCurrentBg.type === 'pdf') modeIcon.textContent = '📖';
            else if (wbCurrentBg.type === 'image') modeIcon.textContent = '🖼️';
            else if (wbCurrentBg.type === 'browser') modeIcon.textContent = '🌐';
            else if (wbCurrentBg.type === 'video') modeIcon.textContent = '🎬';
            else if (wbCurrentBg.type === 'blackboard') modeIcon.textContent = '⬛';
            else if (wbCurrentBg.type === 'grid') modeIcon.textContent = '📐';
            else modeIcon.textContent = '📋';
        }

        if (bgChanged) {
            if (wbCurrentBg.type === 'browser' && wbCurrentBg.url) {
                renderBrowserBackground(wbCurrentBg.url, wbCurrentBg.title);
                document.getElementById('wbPageNavControls')?.classList.add('hidden');
            } else if (wbCurrentBg.type === 'video' && wbCurrentBg.url) {
                renderVideoBackground(wbCurrentBg.url, wbCurrentBg.title);
                document.getElementById('wbPageNavControls')?.classList.add('hidden');
            } else if (wbCurrentBg.type === 'pdf' && wbCurrentBg.url) {
                hideMediaLayers();
                await renderPdfPage(wbCurrentBg.url, wbCurrentBg.page_num);
                document.getElementById('wbPageNavControls')?.classList.remove('hidden');
            } else if (wbCurrentBg.type === 'image' && wbCurrentBg.url) {
                hideMediaLayers();
                renderImageBackground(wbCurrentBg.url);
                document.getElementById('wbPageNavControls')?.classList.add('hidden');
            } else {
                hideMediaLayers();
                renderBlankBackground(wbCurrentBg.type);
                document.getElementById('wbPageNavControls')?.classList.add('hidden');
            }
        }

        wbStrokes = state.strokes || [];
        redrawWhiteboard();
    }

    // بدء تشغيل الوسائط والسبورة عند تحميل الصفحة
    window.addEventListener('DOMContentLoaded', () => {
        initLocalMedia();
        initWhiteboard();
    });
    </script>

</body>
</html>
