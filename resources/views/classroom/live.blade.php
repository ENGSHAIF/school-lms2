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
        .cursor-pen {
            cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'%3E%3Cpath d='M2 30 L3 24 L6 21 L21 6 L24 3 C26 1 28 3 29 4 C30 5 30 7 29 8 L26 11 L11 26 L8 29 Z' fill='none' stroke='%23ffffff' stroke-width='3' stroke-linejoin='round' stroke-linecap='round'/%3E%3Cpath d='M21 6 L24 3 C26 1 28 3 29 4 C30 5 30 7 29 8 L26 11 Z' fill='%23f43f5e' stroke='%230f172a' stroke-width='1'/%3E%3Cpolygon points='6,21 21,6 26,11 11,26' fill='%233b82f6' stroke='%230f172a' stroke-width='1'/%3E%3Cpolygon points='8.5,18.5 23.5,3.5 25,5 10,20' fill='%2393c5fd' opacity='0.7'/%3E%3Cpolygon points='3,24 6,21 11,26 8,29' fill='%23fbbf24' stroke='%230f172a' stroke-width='1'/%3E%3Cpolygon points='2,30 3,24 8,29' fill='%230f172a'/%3E%3Ccircle cx='2' cy='30' r='1' fill='%23ffffff'/%3E%3C/svg%3E") 2 30, auto;
        }
        .cursor-brush {
            cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'%3E%3Cpath d='M2 30 C3 27 5 24 7 22 L22 7 C24 5 27 5 29 7 C31 9 31 12 29 14 L14 29 C12 31 9 32 2 30 Z' fill='none' stroke='%23ffffff' stroke-width='3' stroke-linejoin='round'/%3E%3Cpath d='M7 22 L22 7 C24 5 27 5 29 7 C31 9 31 12 29 14 L14 29 Z' fill='%2392400e' stroke='%23451a03' stroke-width='1'/%3E%3Cpath d='M2 30 C3 27 5 24 7 22 L14 29 C12 31 9 32 2 30 Z' fill='%231e293b' stroke='%230f172a' stroke-width='1'/%3E%3Ccircle cx='2' cy='30' r='1' fill='%23ffffff'/%3E%3C/svg%3E") 2 30, auto;
        }
        .cursor-highlighter {
            cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'%3E%3Cpath d='M2 30 L6 22 L11 27 L5 31 Z' fill='none' stroke='%23ffffff' stroke-width='2.5' stroke-linejoin='round'/%3E%3Cpolygon points='6,22 20,8 25,13 11,27' fill='none' stroke='%23ffffff' stroke-width='2.5' stroke-linejoin='round'/%3E%3Cpolygon points='2,30 6,22 11,27 5,31' fill='%23eab308' stroke='%23ca8a04' stroke-width='1'/%3E%3Cpolygon points='6,22 20,8 25,13 11,27' fill='%23fef08a' stroke='%23ca8a04' stroke-width='1'/%3E%3Cpolygon points='20,8 24,4 29,9 25,13' fill='%2371717a' stroke='%233f3f46' stroke-width='1'/%3E%3C/svg%3E") 2 30, auto;
        }
        .cursor-magic {
            cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'%3E%3Cpath d='M2 30 L3 24 L6 21 L21 6 L24 3 C26 1 28 3 29 4 C30 5 30 7 29 8 L26 11 L11 26 L8 29 Z' fill='none' stroke='%23ffffff' stroke-width='3' stroke-linejoin='round'/%3E%3Cpolygon points='6,21 21,6 26,11 11,26' fill='%23a855f7' stroke='%237e22ce' stroke-width='1'/%3E%3Cpolygon points='3,24 6,21 11,26 8,29' fill='%23c084fc' stroke='%237e22ce' stroke-width='1'/%3E%3Cpolygon points='2,30 3,24 8,29' fill='%23581c87'/%3E%3Ccircle cx='2' cy='30' r='1' fill='%23ffffff'/%3E%3Ccircle cx='27' cy='5' r='2' fill='%23fbbf24'/%3E%3Cpath d='M27 1 L27 9 M23 5 L31 5' stroke='%23fbbf24' stroke-width='1'/%3E%3C/svg%3E") 2 30, auto;
        }
        .cursor-eraser {
            cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'%3E%3Cg transform='rotate(-30 16 16)'%3E%3Crect x='6' y='9' width='20' height='14' rx='3' fill='none' stroke='%23ffffff' stroke-width='3'/%3E%3Crect x='6' y='9' width='10' height='14' rx='2' fill='%23fb7185' stroke='%23e11d48' stroke-width='1'/%3E%3Crect x='16' y='9' width='10' height='14' rx='2' fill='%23f1f5f9' stroke='%2394a3b8' stroke-width='1'/%3E%3C/g%3E%3C/svg%3E") 6 26, auto;
        }
        .cursor-laser { cursor: none !important; }
        .wb-vertical-slider {
            -webkit-appearance: slider-vertical;
            writing-mode: vertical-lr;
            direction: rtl;
            width: 14px;
            height: 100%;
            min-height: 140px;
            padding: 0;
            cursor: pointer;
        }
    </style>
</head>
<body class="min-h-screen lg:h-screen flex flex-col overflow-y-auto lg:overflow-hidden select-none">

    <!-- نافذة طلب إذن الصوت والمايكروفون التفاعلية للبث الحي (User Gesture Trigger) -->
    <div id="audioPermissionModal" class="hidden fixed inset-0 z-[9999] bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border-2 border-indigo-500/70 rounded-3xl p-6 sm:p-7 max-w-sm w-full text-center shadow-2xl space-y-4">
            <div class="w-16 h-16 mx-auto bg-gradient-to-tr from-emerald-600 to-indigo-600 rounded-full flex items-center justify-center text-3xl shadow-xl animate-pulse">
                🎙️
            </div>
            <div class="space-y-1.5">
                <h3 class="text-base font-black text-white">تفعيل الصوت والمايكروفون 🔊</h3>
                <p class="text-xs text-slate-300 leading-relaxed">
                    انقر على الزر أدناه لتفعيل سماع صوت المعلم تلقائياً وتمكين المايكروفون للمشاركة في البث الحي.
                </p>
            </div>
            <button type="button" onclick="confirmAudioPermission()" class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-sm rounded-2xl shadow-xl transition active:scale-95 flex items-center justify-center gap-2">
                <span>🔊</span>
                <span>تفعيل وسماع الصوت الآن</span>
            </button>
            <div id="micHelpNotice" class="hidden text-[11px] text-amber-300/90 bg-amber-950/40 p-2.5 rounded-xl border border-amber-800/40 text-right leading-relaxed">
                💡 <strong>ملاحظة لجوال كروم:</strong> إذا لم يطلب المتصفح إذن المايك، يمكنك الاستماع للمعلم بشكل كامل، وللمشاركة بصوتك عبر الواي فاي فعل إذن الرابط في <code>chrome://flags</code>.
            </div>
        </div>
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
        $groupBooks = $levelResources->filter(fn($r) => $r->type === 'book' || str_ends_with(strtolower($r->file_path ?? ''), '.pdf'));
        if ($groupBooks->isEmpty() && isset($group) && $group && $group->level_id) {
            $groupBooks = \App\Models\LevelResource::where('level_id', $group->level_id)->where(function($q) {
                $q->where('type', 'book')->orWhere('file_path', 'like', '%.pdf');
            })->get();
        }
        $groupVideos = $levelResources->filter(fn($r) => $r->type === 'video' || in_array(strtolower(pathinfo($r->file_path ?? '', PATHINFO_EXTENSION)), ['mp4', 'webm', 'mov']));
        $groupWallPdfs = $groupActivities->filter(fn($a) => !empty($a->file_path) && (str_ends_with(strtolower($a->file_path), '.pdf') || in_array($a->type, ['worksheet', 'assignment'])));
    @endphp

    <!-- بانر عائم عالي الوضوح لتفعيل صوت المعلم فوراً عند حظر التشغيل التلقائي بالهواتف -->
    <div id="audioUnblockBanner" class="hidden fixed top-3 left-1/2 -translate-x-1/2 z-[9999] bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-black text-xs px-5 py-2.5 rounded-2xl shadow-2xl flex items-center gap-2 border-2 border-emerald-300 animate-bounce cursor-pointer" onclick="unblockAllAudio()">
        <span class="text-lg">🔊</span>
        <span>انقر هنا لتشغيل وسماع صوت المعلم فوراً!</span>
    </div>

    <!-- شريط التبديل السريع للهواتف والشاشات الصغيرة (السبورة والشرح / المحادثة والأعضاء) -->
    <div class="lg:hidden flex items-center justify-between bg-slate-950 px-2 py-1.5 border-b border-slate-800 shrink-0 z-30">
        <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-800 text-xs font-bold w-full">
            <button type="button" onclick="switchMobileView('whiteboard')" id="btnMobileWhiteboard" class="flex-1 py-2 rounded-lg bg-indigo-600 text-white transition flex items-center justify-center gap-1.5 shadow font-bold">
                <span>📋</span>
                <span>السبورة والشرح</span>
            </button>
            <button type="button" onclick="switchMobileView('panel')" id="btnMobilePanel" class="flex-1 py-2 rounded-lg text-slate-400 hover:text-white transition flex items-center justify-center gap-1.5 font-bold relative">
                <span>💬</span>
                <span>المحادثة والمنهج</span>
                <span id="mobileUnreadMessagesDot" class="hidden w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
            </button>
        </div>
    </div>

    <!-- مساحة العمل الكلية: الجانب الأيمن (محادثة وحضور ومنهج وتحكم) + الجزء الوسط الكبير (شاشة الشرح والسبورة نفس الزوم) -->
    <main class="flex-1 flex flex-col lg:flex-row overflow-hidden p-1 sm:p-1.5 lg:p-2 gap-1.5 sm:gap-2 relative">

        <!-- 1. العمود الأيمن المخصص: أدوات التحكم بالجلسة والحضور + المحادثة + المنهج -->
        <aside id="liveInteractivePanel" class="w-full lg:w-[320px] xl:w-[350px] shrink-0 bg-slate-900 border border-slate-800 rounded-2xl hidden lg:flex flex-col overflow-hidden shadow-xl z-20 transition-all duration-300 ease-in-out relative">
            
            <!-- زر طي/توسيع الشريط الجانبي لتكبير السبورة ملء الشاشة -->
            <button type="button" onclick="toggleSidebarCollapse()" id="btnToggleSidebar" class="hidden lg:flex items-center justify-center absolute -left-3.5 top-1/2 -translate-y-1/2 z-30 w-7 h-12 bg-slate-800 hover:bg-indigo-600 border border-slate-700 text-slate-300 hover:text-white rounded-r-none rounded-l-xl shadow-xl transition group" title="طي/توسيع الشريط الجانبي لتكبير مساحة السبورة ملء الشاشة">
                <span id="sidebarToggleIcon" class="text-xs transition-transform group-hover:scale-125">◀</span>
            </button>

            <!-- شريط أدوات التحكم بالجلسة والمشاركين في رأس العمود الأيمن (صفين منظمين) -->
            <div class="p-2 bg-slate-950 border-b border-slate-800 shrink-0 space-y-1.5">
                <!-- الصف الأول: لوحة التحكم + الحاضرون + سياسة الكلام + كتم الجميع + طلبات الانضمام -->
                <div class="flex items-center gap-1 justify-between flex-wrap text-xs">
                    @if($isTeacher && isset($group) && $group)
                        <a href="{{ route('admin.groups.dashboard', $group->id) }}" target="_blank" class="bg-slate-800 hover:bg-slate-700 text-amber-300 text-[11px] font-bold px-2 py-1 rounded-lg border border-slate-700 flex items-center gap-1 transition" title="فتح لوحة تحكم المجموعة">
                            <span>🎛️</span>
                            <span>لوحة التحكم</span>
                        </a>
                    @endif

                    <!-- زر الحاضرين في البث -->
                    <button type="button" onclick="switchLiveTab('participants')" id="btnHeaderParticipants" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-bold px-2 py-1 rounded-lg border border-slate-700 flex items-center gap-1 transition relative">
                        <span>👥</span>
                        <span>الحاضرون (<strong id="headerParticipantsCount" class="font-mono text-emerald-400">1</strong>)</span>
                        <span id="headerHandBadge" class="hidden px-1 py-0.2 rounded-full bg-amber-400 text-slate-950 text-[9px] font-black animate-bounce">✋</span>
                    </button>

                    <!-- زر سياسة الكلام وكتم الجميع للمعلم -->
                    @if($isTeacher)
                        <button type="button" onclick="muteAllStudentsNow()" id="btnMuteAll" class="bg-rose-950/70 hover:bg-rose-900 text-rose-300 text-[11px] font-bold px-1.5 py-1 rounded-lg border border-rose-800/80 flex items-center gap-1 transition" title="كتم صوت جميع الطلاب فوراً">
                            <span>🔇</span>
                            <span>كتم للكل</span>
                        </button>
                        <button type="button" onclick="toggleRoomPolicy()" id="btnPolicyToggle" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-bold px-1.5 py-1 rounded-lg border border-slate-700 flex items-center gap-1 transition" title="تبديل سياسة التحدث">
                            <span id="iconPolicy">📢</span>
                            <span id="txtPolicy">مسموح</span>
                        </button>
                    @endif

                    @if(auth()->user()->isStudent())
                        <button type="button" onclick="toggleHandRaise()" id="btnRaiseHand" class="bg-slate-800 hover:bg-slate-700 text-amber-300 text-[11px] font-bold px-2 py-1 rounded-lg border border-slate-700 flex items-center gap-1 transition">
                            <span id="iconRaiseHand">✋</span>
                            <span id="txtRaiseHand">طلب كلمة</span>
                        </button>
                    @endif

                    <!-- زر طلبات الانضمام للمعلم -->
                    @if($isTeacher)
                        <button type="button" onclick="switchLiveTab('participants')" id="btnHeaderAdmission" class="hidden bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-[11px] font-bold px-2 py-1 rounded-lg border border-amber-500/40 flex items-center gap-1 transition animate-pulse">
                            <span>🔔</span>
                            <span>طلبات (<strong id="headerAdmissionCount" class="font-mono text-amber-200">0</strong>)</span>
                        </button>
                    @endif
                </div>

                <!-- الصف الثاني: المايك + الكاميرا + مشاركة الشاشة + التحديث + إغلاق/فتح البث + المؤقت + مغادرة -->
                <div class="flex items-center gap-1 justify-between flex-wrap text-xs pt-1 border-t border-slate-900">
                    <!-- أزرار المايك والكاميرا -->
                    <div class="flex items-center gap-1 bg-slate-900 p-0.5 rounded-lg border border-slate-800">
                        <button type="button" onclick="toggleLocalMic()" id="btnMyMic" class="bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold px-2 py-1 rounded-md flex items-center gap-1 transition" title="كتم/تشغيل المايك">
                            <span id="iconMyMic">🎙️</span>
                            <span id="txtMyMic" class="hidden sm:inline">المايك</span>
                        </button>
                        <button type="button" onclick="toggleLocalCam()" id="btnMyCam" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-bold px-2 py-1 rounded-md flex items-center gap-1 border border-slate-700 transition" title="إيقاف/تشغيل الكاميرا">
                            <span id="iconMyCam">📷</span>
                            <span id="txtMyCam" class="hidden sm:inline">الكاميرا</span>
                        </button>
                    </div>

                    @if($isTeacher)
                        <!-- زر مشاركة الشاشة -->
                        <button type="button" onclick="toggleScreenShare()" id="btnScreenShare" class="bg-indigo-950/80 hover:bg-indigo-900 text-indigo-200 text-[11px] font-bold px-2 py-1 rounded-lg border border-indigo-700/60 flex items-center gap-1 transition" title="مشاركة الشاشة">
                            <span id="iconScreenShare">🖥️</span>
                            <span>شاشة</span>
                        </button>
                    @endif

                    <!-- زر التحديث والمزامنة F5 للجميع (معلمين وطلاب وإدارة) -->
                    <button type="button" onclick="refreshClassroom()" id="btnRefreshClassroom" class="bg-cyan-950/80 hover:bg-cyan-900 text-cyan-200 hover:text-white text-[11px] font-bold px-2.5 py-1 rounded-lg border border-cyan-700/60 flex items-center gap-1 transition shadow-sm" title="تحديث ومزامنة بيانات الفصل والشاشة والسبورة (F5)">
                        <span>🔄</span>
                        <span>تحديث</span>
                    </button>

                    <!-- زر فتح/إغلاق البث للمعلم -->
                    @if($isTeacher)
                        @if(isset($group) && $group)
                            @if($group->is_live)
                                <form method="POST" action="{{ route('groups.live.stop', $group->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-bold bg-rose-600 hover:bg-rose-500 text-white px-2 py-1 rounded-lg transition">
                                        ⏹️ إغلاق
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('groups.live.start', $group->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-black bg-emerald-500 hover:bg-emerald-400 text-slate-950 px-2 py-1 rounded-lg transition flex items-center gap-0.5">
                                        <span>📡</span>
                                        <span>فتح</span>
                                    </button>
                                </form>
                            @endif
                        @elseif(isset($booking) && $booking)
                            <form method="POST" action="{{ route('classroom.booking.toggle_live', $booking->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-[10px] font-bold {{ $booking->is_live ? 'bg-rose-600 text-white' : 'bg-emerald-500 text-slate-950' }} px-2 py-1 rounded-lg transition">
                                    {{ $booking->is_live ? '⏹️ إغلاق' : '📡 فتح' }}
                                </button>
                            </form>
                        @endif
                    @endif

                    <!-- العداد التنازلي -->
                    <div class="bg-slate-900 border border-slate-800 px-2 py-1 rounded-lg font-mono text-[11px] font-bold text-emerald-400 flex items-center gap-1">
                        <span>⏱️</span>
                        <span id="sessionTimer">25:00</span>
                    </div>

                    <!-- زر مغادرة الفصل -->
                    <a href="{{ auth()->user()->isStudent() ? route('student.portal') : (auth()->user()->isTeacher() ? route('teacher.dashboard') : route('admin.groups.index')) }}" 
                       onclick="return confirm('هل ترغب بمغادرة الفصل التفاعلي المباشر الآن؟');"
                       class="text-[11px] text-rose-400 hover:text-white bg-slate-900 hover:bg-rose-900 border border-slate-800 px-2 py-1 rounded-lg transition font-bold">
                        ✕ مغادرة
                    </a>
                </div>
            </div>
            
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
                            <div class="flex justify-between items-center">
                                <div class="font-bold flex items-center gap-1.5 text-indigo-300 text-xs">
                                    <span>👋</span>
                                    <span>محادثة الفصل التفاعلي المباشر</span>
                                </div>
                                @if($isTeacher || (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor())))
                                    <button type="button" onclick="confirmClearChat()" class="text-[10px] text-rose-300 hover:text-white bg-rose-950/80 hover:bg-rose-900 border border-rose-800/80 px-2.5 py-1 rounded-lg flex items-center gap-1 transition shadow-sm font-bold" title="حذف ومسح محتوى المحادثة بالكامل للمجموعة">
                                        <span>🗑️</span>
                                        <span>مسح المحادثة</span>
                                    </button>
                                @endif
                            </div>
                            <p class="text-[11px] leading-relaxed text-indigo-200/80">
                                الرسائل النصية، والتسجيلات الصوتية 🎙️، وأوراق الإجابة 📸 تظهر لجميع أعضاء المجموعة والمعلم لحظياً.
                            </p>
                        </div>

                        @if(isset($chatMessages) && $chatMessages->isNotEmpty())
                            @foreach($chatMessages as $msg)
                                @if($msg->type !== 'system')
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
                                @endif
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
                                    @if($isTeacher || (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor())))
                                        <button type="button" onclick="openResourceInWorkspace('{{ route('resources.stream', $bk->id) }}', '{{ addslashes($bk->title) }}', 'pdf')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[10px] rounded-lg transition flex items-center gap-1 shadow">
                                            <span>🚀</span>
                                            <span>عرض بالشرح</span>
                                        </button>
                                    @endif
                                    <a href="{{ route('resources.download', $bk->id) }}" target="_blank" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] rounded-lg transition" title="تحميل">
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

            <!-- الشريط السفلي البسيط بالجانب الأيمن -->
            @if($isTeacher || (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor())))
                <div class="p-2 bg-slate-950 border-t border-slate-800 flex items-center justify-between gap-2 shrink-0">
                    <button type="button" onclick="openRewardStarModal()" class="flex-1 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 text-xs font-bold rounded-xl border border-amber-500/30 flex items-center justify-center gap-1.5 transition" title="مكافأة بنجمة للطلاب">
                        <span>⭐ مكافأة بنجمة</span>
                        <span id="starsCount" class="font-mono bg-amber-500/30 px-1.5 py-0.2 rounded-full text-amber-200">0</span>
                    </button>
                    <button type="button" onclick="openReportModal()" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl transition shadow flex items-center gap-1" title="إنهاء وإصدار تقرير الحصة">
                        <span>📋 تقرير الحصة</span>
                    </button>
                </div>
            @endif
        </aside>

        <!-- زر استعادة الشريط الأيمن العائم (يظهر عند طي الشريط لتوسيع السبورة) -->
        <button type="button" onclick="toggleSidebarCollapse()" id="btnRestoreSidebar" class="hidden absolute right-3 top-3 z-40 bg-slate-900/95 hover:bg-indigo-600 border border-slate-700 hover:border-indigo-500 text-slate-200 hover:text-white px-3 py-1.5 rounded-xl shadow-2xl transition flex items-center gap-1.5 text-xs font-bold backdrop-blur-md" title="إظهار لوحة المحادثة والمنهج">
            <span>▶</span>
            <span>الدردشة والمنهج</span>
        </button>

        <!-- 2. الجزء الوسط واليسار الكبير: مساحة الشرح والمشاركة المباشرة (Zoom Live Presentation & Whiteboard) -->
        <section id="whiteboardSection" class="flex-1 bg-slate-900 border border-slate-800 rounded-2xl flex flex-col overflow-hidden shadow-2xl relative min-h-[400px] lg:min-h-0">
            
            <!-- الشريط العلوي لمساحة الشرح والسبورة -->
            <div class="p-2 sm:p-2.5 bg-slate-950/95 border-b border-slate-800 flex flex-wrap items-center justify-between gap-2 shrink-0 z-10 text-xs">
                
                <!-- شارة المحتوى المفتوح حالياً وعنوان المجموعة في رأس الصفحة -->
                <div class="flex items-center gap-1.5 min-w-0">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[10px] font-black shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                        <span>{{ isset($group) && $group ? 'فصل حي' : 'جلسة خاصة' }}</span>
                    </span>
                    <span class="font-bold text-slate-300 text-xs truncate max-w-[140px] sm:max-w-[240px] hidden md:inline" title="{{ isset($group) && $group ? $group->name : (isset($booking) && $booking ? ('جلسة تدريب فردية - ' . ($booking->student?->name ?? 'طالب')) : 'فصل دراسي حي') }}">
                        {{ isset($group) && $group ? $group->name : (isset($booking) && $booking ? ('جلسة فردية - ' . ($booking->student?->name ?? 'طالب')) : 'فصل دراسي حي') }}
                    </span>
                    <span class="w-px h-3 bg-slate-800 hidden md:inline"></span>
                    <span class="bg-indigo-600/30 text-indigo-300 font-bold px-2 py-0.5 rounded-lg border border-indigo-500/40 flex items-center gap-1 shrink-0">
                        <span id="wbModeIcon">📋</span>
                        <span id="wbModeText" class="truncate max-w-[120px] sm:max-w-[180px]">سبورة بيضاء</span>
                    </span>
                    <span id="wbSyncBadge" class="hidden xl:inline-flex items-center gap-1 text-[10px] text-emerald-400 font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>متزامن</span>
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

                        <!-- زر فتح مكتبة كتب ومذكرات الدورة المعتمدة -->
                        <button type="button" onclick="openBooksLibraryModal()" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[11px] px-2.5 py-1.5 rounded-xl flex items-center gap-1.5 shadow transition" title="فتح كتب المنهج ومذكرات المجموعة ورفع كتاب PDF من جهازك">
                            <span>📖</span>
                            <span>مكتبة الكتب والمذكرات</span>
                            @if($groupBooks->isNotEmpty() || $groupWallPdfs->isNotEmpty())
                                <span class="bg-indigo-950 text-indigo-200 text-[10px] px-1.5 py-0.2 rounded-full font-mono">{{ $groupBooks->count() + $groupWallPdfs->count() }}</span>
                            @endif
                        </button>

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

                    <!-- قارئ وتصفح صفحات الكتب والـ PDF (متقدم) -->
                    <div id="wbPageNavControls" class="hidden flex items-center gap-1 bg-slate-900 px-2 py-1 rounded-xl border border-slate-800 text-[11px] font-bold">
                        <button type="button" onclick="changeWbPageTo(1)" class="px-1 py-0.5 hover:bg-slate-800 text-slate-300 rounded" title="أول صفحة (Home)">⏮️</button>
                        <button type="button" onclick="changeWbPage(-1)" class="px-1.5 py-0.5 hover:bg-slate-800 text-slate-300 rounded" title="الصفحة السابقة (◀ / PageUp)">◀</button>
                        <span class="text-slate-400 flex items-center gap-1">
                            <span>صفحة</span>
                            <input type="number" id="wbPageDirectInput" min="1" value="1" onchange="onPageDirectInputChange(this.value)" class="w-12 bg-slate-950 border border-slate-700 rounded px-1 py-0.5 text-center text-white font-mono text-xs font-bold focus:border-indigo-500 focus:outline-none" title="اكتب رقم الصفحة واضغط Enter للانتقال السريع">
                            <span>من <span id="wbTotalPagesText" class="font-mono text-white">1</span></span>
                        </span>
                        <button type="button" onclick="changeWbPage(1)" class="px-1.5 py-0.5 hover:bg-slate-800 text-slate-300 rounded" title="الصفحة التالية (▶ / PageDown)">▶</button>
                        <button type="button" onclick="changeWbPageTo(wbTotalPages)" class="px-1 py-0.5 hover:bg-slate-800 text-slate-300 rounded" title="آخر صفحة (End)">⏭️</button>
                        <span class="w-px h-3 bg-slate-700 mx-0.5"></span>
                        <button type="button" onclick="fitPdfWidth()" class="px-1.5 py-0.5 hover:bg-slate-800 text-indigo-300 rounded" title="ملاءمة عرض الشاشة">📐 عرض</button>
                        <button type="button" onclick="fitPdfPage()" class="px-1.5 py-0.5 hover:bg-slate-800 text-indigo-300 rounded" title="ملاءمة كامل الصفحة">📄 صفحة</button>
                    </div>

                    <!-- أزرار التكبير والتصغير -->
                    <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-800 text-[11px]">
                        <button type="button" onclick="zoomWb(-0.1)" class="px-1.5 py-0.5 hover:bg-slate-800 text-slate-300 rounded" title="تصغير">-</button>
                        <span id="wbZoomLabel" class="font-mono text-slate-300 px-1">100%</span>
                        <button type="button" onclick="zoomWb(0.1)" class="px-1.5 py-0.5 hover:bg-slate-800 text-slate-300 rounded" title="تكبير">+</button>
                        <button type="button" onclick="resetWbZoom()" class="px-1.5 py-0.5 hover:bg-slate-800 text-slate-400 rounded" title="إعادة ضبط الحجم">⟲</button>
                    </div>

                    <!-- زر تحديث ومزامنة الصوت والشاشة والسبورة للجميع -->
                    <button type="button" onclick="refreshClassroom()" class="bg-cyan-950/90 hover:bg-cyan-900 text-cyan-200 hover:text-white border border-cyan-700 font-black text-[11px] px-2.5 py-1.5 rounded-xl flex items-center gap-1.5 transition shadow" title="تنشيط الصوت ومزامنة السبورة بدون إعادة تحميل الصفحة">
                        <span>🔄</span>
                        <span>تحديث الصوت والسبورة</span>
                    </button>

                    <!-- حفظ لقطة من الشرح والسبورة -->
                    <button type="button" onclick="downloadWhiteboardSnapshot()" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-[11px] px-2.5 py-1.5 rounded-xl flex items-center gap-1 transition shadow" title="تصدير وحفظ صورة الشرح كاملة مع الملاحظات">
                        <span>💾</span>
                        <span class="hidden md:inline">حفظ الشرح</span>
                    </button>
                </div>
            </div>

            <!-- حاوية الرسم والعرض التفاعلية (Dual Canvas + Laser + Text Input + Browser & Video Layers) -->
            <div id="whiteboardContainer" class="flex-1 relative overflow-y-auto overflow-x-hidden bg-slate-950 flex flex-col items-center select-none scrollbar-thin">
                <!-- شريط تقدم رفع ومعالجة الملفات والفيديوهات الكبيرة -->
                <div id="wbUploadProgressBar" class="hidden absolute top-3 left-1/2 -translate-x-1/2 z-40 bg-slate-900/95 border border-indigo-500/50 rounded-2xl px-4 py-2.5 shadow-2xl flex items-center gap-3 backdrop-blur-md min-w-[280px]">
                    <div class="w-5 h-5 rounded-full border-2 border-indigo-400 border-t-transparent animate-spin shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between text-[11px] font-bold text-slate-200 mb-1">
                            <span id="wbUploadStatusText" class="truncate">جاري معالجة الملف...</span>
                            <span id="wbUploadPercentText" class="font-mono text-indigo-400">0%</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                            <div id="wbUploadProgressFill" class="h-full bg-gradient-to-r from-indigo-500 to-emerald-400 w-0 transition-all duration-150"></div>
                        </div>
                    </div>
                </div>

                <!-- مسرح اللوحات والكانفاس المزدوج (Canvas Stage) -->
                <div id="wbCanvasStage" class="relative my-auto transition-all duration-75 flex items-center justify-center">
                    <!-- طبقة خلفية الكتاب أو الصورة أو السبورة النظيفة -->
                    <canvas id="whiteboardBgCanvas" class="block pointer-events-none z-[1]"></canvas>

                    <!-- طبقة الرسم والكتابة التفاعلية بالقلم والأشكال -->
                    <canvas id="whiteboardDrawCanvas" class="absolute inset-0 w-full h-full cursor-pen z-[10]"></canvas>

                    <!-- مؤشر الليزر الحي (Red Laser Pointer) -->
                    <div id="liveLaserPointer" class="hidden absolute pointer-events-none z-30 -translate-x-1/2 -translate-y-1/2 transition-all duration-75">
                        <div class="w-6 h-6 rounded-full bg-red-500 laser-glow animate-ping opacity-75 absolute -inset-0.5"></div>
                        <div class="w-4 h-4 rounded-full bg-red-600 border-2 border-white shadow-xl relative z-10"></div>
                        <span id="laserUserBadge" class="hidden absolute top-5 right-1/2 translate-x-1/2 text-[9px] bg-red-950/90 text-red-200 border border-red-500/50 px-1.5 py-0.5 rounded font-bold whitespace-nowrap shadow-md">🔴</span>
                    </div>

                    <!-- مربع إدخال النص على السبورة -->
                    <div id="wbTextInputWrapper" class="hidden absolute z-30">
                        <input type="text" id="wbTextInput" class="bg-slate-900/95 border-2 border-indigo-500 text-white px-2.5 py-1 rounded-xl text-sm outline-none shadow-2xl font-bold" placeholder="اكتب الملاحظة هنا واضغط Enter..." onkeydown="handleWbTextKey(event)">
                    </div>
                </div>

                <!-- طبقة مستعرض جوجل ومسابقات الأطفال التفاعلية داخل السبورة (Whiteboard Mini-Chrome Browser) -->
                <div id="whiteboardBrowserWrapper" class="hidden absolute inset-0 w-full h-full flex flex-col bg-slate-900 z-[5]">
                    <!-- شريط أدوات وتصفح كروم المصغر -->
                    <!-- شريط أدوات وتصفح كروم المصغر -->
                    <div class="p-1.5 sm:p-2 bg-slate-950/95 border-b border-slate-800 flex flex-wrap items-center justify-between gap-2 shrink-0 z-20 text-xs">
                        <div class="flex items-center flex-wrap gap-1.5 flex-1 min-w-[280px]">
                            @if($isTeacher || (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor())))
                                <!-- زر العودة المباشر والأبرز لشاشة السبورة الرئيسية والشرح -->
                                <button type="button" onclick="closeWbMediaLayer()" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 shrink-0 cursor-pointer border border-rose-400/40" title="إنهاء وضع التصفح والرجوع للسبورة البيضاء">
                                    <span class="text-sm">🔙</span>
                                    <span>الرجوع للسبورة الرئيسية</span>
                                </button>

                                <!-- زر قائمة المسابقات والألعاب والبحث -->
                                <button type="button" onclick="openBrowserModal()" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow transition flex items-center gap-1.5 shrink-0 cursor-pointer" title="فتح قائمة الألعاب والمسابقات والبحث السريع">
                                    <span>🎮</span>
                                    <span>المسابقات والبحث</span>
                                </button>
                            @endif

                            <!-- أزرار التنقل الأساسية للخلف وللأمام والتحديث وجوجل -->
                            <div class="flex items-center gap-1 bg-slate-900 border border-slate-800 rounded-xl p-1 shrink-0">
                                <button type="button" onclick="navigateBrowserBack()" class="px-2 py-1 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-1" title="للخلف">⬅️</button>
                                <button type="button" onclick="navigateBrowserForward()" class="px-2 py-1 rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-1" title="للأمام">➡️</button>
                                <button type="button" onclick="reloadBrowserIframe()" class="px-2 py-1 rounded-lg hover:bg-slate-800 text-cyan-300 hover:text-cyan-200 text-xs font-bold transition flex items-center gap-1" title="إعادة تحميل الصفحة">🔄</button>
                                <button type="button" onclick="navigateBrowserToGoogleHome()" class="px-2 py-1 rounded-lg hover:bg-slate-800 text-amber-300 hover:text-amber-200 text-xs font-bold transition flex items-center gap-1" title="الرجوع لصفحة جوجل الرئيسية">
                                    <span>🔍</span>
                                    <span class="hidden sm:inline">جوجل</span>
                                </button>
                            </div>

                            <!-- شريط كتابة العنوان والبحث مثل كروم -->
                            <div class="flex-1 relative flex items-center min-w-[200px]">
                                <span class="absolute right-2.5 text-slate-400 text-xs pointer-events-none">🌐</span>
                                <input type="text" id="wbBrowserCurrentUrl" class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl pr-7 pl-16 py-1.5 text-xs text-white placeholder-slate-500 font-mono focus:outline-none transition" placeholder="اكتب عنوان الموقع أو ابحث في جوجل..." onkeydown="if(event.key==='Enter') navigateBrowserToUrl()">
                                <button type="button" onclick="navigateBrowserToUrl()" class="absolute left-1 bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-lg transition shadow-sm" title="الانتقال للموقع">
                                    ذهاب ⏎
                                </button>
                            </div>

                            <!-- زر الفتح في تبويب خارجي مستقل (كروم) -->
                            <button type="button" onclick="openBrowserExternalTab()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 hover:text-amber-200 border border-slate-700 text-xs font-bold shrink-0 transition flex items-center gap-1 shadow-sm cursor-pointer" title="إذا كان الموقع يفرض حماية تمنع التضمين، افتحه هنا في نافذة كروم مباشرة">
                                <span>↗️</span>
                                <span class="hidden md:inline">فتح بتبويب مستقل</span>
                            </button>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="text-[10px] text-amber-300 bg-amber-500/10 px-2.5 py-1 rounded-xl border border-amber-500/30 hidden xl:inline font-bold">
                                💡 اختر الماوس 🖱️ للتفاعل مع الموقع أو القلم ✏️ للشرح فوقه
                            </span>
                            @if($isTeacher || (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor())))
                                <button type="button" onclick="closeWbMediaLayer()" class="px-2.5 py-1 rounded-xl bg-rose-950/80 hover:bg-rose-900 text-rose-300 hover:text-white text-xs font-bold border border-rose-800 transition" title="إنهاء التصفح">
                                    ✕ إغلاق
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- شريط إرشادي خفيف لمعالجة المواقع المحمية -->
                    <div id="wbBrowserHelpTip" class="bg-indigo-950/80 border-b border-indigo-900/60 px-3 py-1 flex items-center justify-between text-[11px] text-indigo-200">
                        <div class="flex items-center gap-1.5">
                            <span class="text-amber-400">💡</span>
                            <span>إذا صادفت موقعاً يمنع التضمين أو العرض الداخلي، يمكنك النقر على <strong>[فتح بتبويب مستقل ↗️]</strong> لفتحه فوراً ومشاركته عبر ميزة [مشاركة الشاشة 🖥️].</span>
                        </div>
                        <button type="button" onclick="document.getElementById('wbBrowserHelpTip').classList.add('hidden')" class="text-indigo-400 hover:text-white text-xs px-1">✕</button>
                    </div>

                    <div class="flex-1 relative w-full h-full bg-white overflow-hidden">
                        <iframe id="whiteboardBrowserIframe" src="about:blank" class="w-full h-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>

                <!-- طبقة مشغل الفيديو التعليمي داخل السبورة -->
                <div id="whiteboardVideoWrapper" class="hidden absolute inset-0 w-full h-full flex flex-col items-center justify-center bg-black z-[5]">
                    <div class="absolute top-2 right-2 z-20 flex items-center gap-2">
                        <span id="wbVideoTitleBadge" class="bg-black/80 backdrop-blur text-white text-xs px-2.5 py-1 rounded-lg border border-white/20 truncate max-w-xs font-bold"></span>
                        @if($isTeacher || (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor())))
                            <button type="button" onclick="closeWbMediaLayer()" class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow flex items-center gap-1.5 transition cursor-pointer" title="إغلاق الفيديو والرجوع للسبورة">
                                <span>🔙</span>
                                <span>إنهاء الفيديو والرجوع للسبورة</span>
                            </button>
                        @endif
                    </div>
                    <video id="whiteboardHtml5Video" playsinline class="hidden w-full h-full max-h-full object-contain"></video>
                    <iframe id="whiteboardYoutubeFrame" class="hidden w-full h-full max-w-full max-h-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                    <!-- شريط التحكم العائم بالفيديو فوق الكانفاس (z-30) للتحكم والشرح في نفس الوقت -->
                    <div id="wbVideoCustomControls" class="absolute bottom-4 left-1/2 -translate-x-1/2 z-30 w-11/12 max-w-2xl bg-slate-900/95 backdrop-blur-md border border-slate-700/90 rounded-2xl p-2.5 shadow-2xl flex flex-col gap-2 transition">
                        <div class="flex items-center gap-2">
                            <input type="range" id="wbVideoSeekSlider" min="0" max="100" value="0" step="0.1" oninput="onVideoSeekInput(this.value)" class="w-full accent-rose-500 h-1.5 bg-slate-800 rounded-lg cursor-pointer">
                            <span id="wbVideoTimeLabel" class="text-[10px] font-mono text-slate-300 shrink-0">00:00 / 00:00</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="toggleHtml5VideoPlay()" id="btnWbVideoPlay" class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold flex items-center gap-1 shadow">
                                    <span id="iconWbVideoPlay">▶️</span>
                                    <span id="txtWbVideoPlay">تشغيل</span>
                                </button>
                                <button type="button" onclick="rewindHtml5Video(10)" class="p-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold" title="تراجع 10 ثواني">⏪ 10ث</button>
                                <button type="button" onclick="forwardHtml5Video(10)" class="p-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold" title="تقديم 10 ثواني">10ث ⏩</button>
                                <button type="button" onclick="toggleHtml5VideoMute()" id="btnWbVideoMute" class="p-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold" title="كتم/تشغيل الصوت">🔊</button>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-amber-300 font-bold hidden sm:inline">💡 أوقف الفيديو واستخدم القلم ✏️ للشرح فوقه</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- شريط التمرير الرأسي السريع لصفحات الـ PDF (Vertical Scrubber / Quick Navigator) -->
            <div id="wbPdfSideNavigator" class="hidden absolute right-3 top-14 bottom-14 z-30 flex flex-col items-center justify-between bg-slate-900/90 backdrop-blur border border-slate-700/80 rounded-2xl py-2 px-1 shadow-2xl transition">
                <button type="button" onclick="changeWbPageTo(1)" class="text-xs text-slate-400 hover:text-white p-1 rounded hover:bg-slate-800" title="أول صفحة (Home)">⏮️</button>
                <div class="relative flex-1 flex items-center justify-center py-2 my-1">
                    <!-- فقاعة رقم الصفحة التوضيحية عند السحب -->
                    <div id="wbPdfSliderBubble" class="hidden absolute right-8 bg-indigo-600 text-white font-mono font-bold text-xs px-2.5 py-1 rounded-xl shadow-2xl pointer-events-none whitespace-nowrap border border-indigo-400/50">
                        صفحة <span id="wbPdfSliderBubbleNum">1</span>
                    </div>
                    <input type="range" id="wbPdfVerticalSlider" min="1" max="1" value="1" 
                        oninput="onPdfSliderInput(this.value)" 
                        onchange="onPdfSliderChange(this.value)" 
                        class="wb-vertical-slider accent-indigo-500 cursor-pointer"
                        title="اسحب للتنقل السريع بين صفحات الكتاب">
                </div>
                <button type="button" onclick="changeWbPageTo(wbTotalPages)" class="text-xs text-slate-400 hover:text-white p-1 rounded hover:bg-slate-800" title="آخر صفحة (End)">⏭️</button>
            </div>

            <!-- شريط أدوات الشرح باليسار نفس الزوم (Left Floating Annotation Toolbar - Zoom Style) -->
            <div id="wbLeftToolbar" class="absolute left-3 top-14 bottom-3 z-30 flex flex-col items-center justify-between bg-slate-900/95 backdrop-blur-md border border-slate-700/80 rounded-2xl p-1.5 shadow-2xl space-y-1 text-xs overflow-y-auto max-h-[96%] scrollbar-none">
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
                        <button type="button" onclick="toggleShapesFlyout()" id="toolBtn_shapes" class="wb-tool-btn p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition relative" title="الأشكال الهندسية والرسومات">
                            <span id="activeShapeIcon" class="text-base">🔲</span>
                        </button>

                        <!-- قائمة أختام التشجيع والتقييم (Stamps) -->
                        <button type="button" onclick="toggleStampsFlyout()" id="toolBtn_stamp" class="wb-tool-btn p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition relative" title="أختام التشجيع والتقييم للطلاب">
                            <span id="activeStampIcon" class="text-base">⭐</span>
                        </button>

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
                        <!-- لوحة الألوان المتنوعة (زر الفتح) -->
                        <button type="button" onclick="toggleColorFlyout()" class="w-6 h-6 rounded-full border-2 border-white shadow transition transform hover:scale-110 flex items-center justify-center shrink-0" id="activeColorPreview" style="background-color: #ef4444;" title="لوحة ألوان الشرح (42 لوناً)"></button>

                        <!-- سماكة وحجم الخط والقلم (زر الفتح) -->
                        <button type="button" onclick="toggleSizeFlyout()" id="activeSizePreviewBtn" class="p-1 rounded-lg text-slate-300 hover:bg-slate-800 transition font-mono font-bold text-[10px]" title="سماكة الخط">
                            <span id="activeSizePreview">4px</span>
                        </button>
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

                <!-- قوائم الأدوات المنبثقة خارج الشريط لتفادي القص العرضي ومشاكل التمرير (Unclipped Floating Menus) -->
                <!-- 1. قائمة الأشكال الهندسية الموسعة (13 شكلاً + تنزيل مباشر) -->
                <div id="shapesFlyoutMenu" class="wb-flyout-menu hidden absolute left-16 top-10 bg-slate-900/98 border border-slate-700/90 rounded-2xl shadow-2xl p-3 flex flex-col gap-2 z-40 w-56 backdrop-blur-md">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                        <span class="text-xs text-white font-black flex items-center gap-1.5">
                            <span>📐</span> <span>الأشكال والرسومات:</span>
                        </span>
                        <button type="button" onclick="closeAllWbFlyouts()" class="text-slate-400 hover:text-white text-xs">✕</button>
                    </div>
                    <div class="grid grid-cols-2 gap-1.5 text-xs font-bold max-h-56 overflow-y-auto scrollbar-none">
                        <button type="button" onclick="selectWbShape('rect')" class="p-1.5 rounded-xl hover:bg-indigo-600/80 text-white flex items-center gap-1.5 bg-slate-800/60 border border-slate-700/50 transition">
                            <span>🔲</span> <span>مستطيل</span>
                        </button>
                        <button type="button" onclick="selectWbShape('rounded_rect')" class="p-1.5 rounded-xl hover:bg-indigo-600/80 text-white flex items-center gap-1.5 bg-slate-800/60 border border-slate-700/50 transition">
                            <span>◽</span> <span>مستدير</span>
                        </button>
                        <button type="button" onclick="selectWbShape('circle')" class="p-1.5 rounded-xl hover:bg-indigo-600/80 text-white flex items-center gap-1.5 bg-slate-800/60 border border-slate-700/50 transition">
                            <span>⭕</span> <span>دائرة</span>
                        </button>
                        <button type="button" onclick="selectWbShape('triangle')" class="p-1.5 rounded-xl hover:bg-indigo-600/80 text-white flex items-center gap-1.5 bg-slate-800/60 border border-slate-700/50 transition">
                            <span>🔺</span> <span>مثلث</span>
                        </button>
                        <button type="button" onclick="selectWbShape('diamond')" class="p-1.5 rounded-xl hover:bg-indigo-600/80 text-white flex items-center gap-1.5 bg-slate-800/60 border border-slate-700/50 transition">
                            <span>🔶</span> <span>معين</span>
                        </button>
                        <button type="button" onclick="selectWbShape('star')" class="p-1.5 rounded-xl hover:bg-indigo-600/80 text-white flex items-center gap-1.5 bg-slate-800/60 border border-slate-700/50 transition">
                            <span>⭐</span> <span>نجمة</span>
                        </button>
                        <button type="button" onclick="selectWbShape('heart')" class="p-1.5 rounded-xl hover:bg-indigo-600/80 text-white flex items-center gap-1.5 bg-slate-800/60 border border-slate-700/50 transition">
                            <span>❤️</span> <span>قلب</span>
                        </button>
                        <button type="button" onclick="selectWbShape('line')" class="p-1.5 rounded-xl hover:bg-indigo-600/80 text-white flex items-center gap-1.5 bg-slate-800/60 border border-slate-700/50 transition">
                            <span>➖</span> <span>خط</span>
                        </button>
                        <button type="button" onclick="selectWbShape('arrow')" class="p-1.5 rounded-xl hover:bg-indigo-600/80 text-white flex items-center gap-1.5 bg-slate-800/60 border border-slate-700/50 transition">
                            <span>➡️</span> <span>سهم</span>
                        </button>
                        <button type="button" onclick="selectWbShape('double_arrow')" class="p-1.5 rounded-xl hover:bg-indigo-600/80 text-white flex items-center gap-1.5 bg-slate-800/60 border border-slate-700/50 transition">
                            <span>↔️</span> <span>سهمين</span>
                        </button>
                        <button type="button" onclick="selectWbShape('checkmark')" class="p-1.5 rounded-xl hover:bg-emerald-600/80 text-emerald-200 flex items-center gap-1.5 bg-slate-800/60 border border-slate-700/50 transition">
                            <span>✔️</span> <span>صح</span>
                        </button>
                        <button type="button" onclick="selectWbShape('cross')" class="p-1.5 rounded-xl hover:bg-rose-600/80 text-rose-200 flex items-center gap-1.5 bg-slate-800/60 border border-slate-700/50 transition">
                            <span>❌</span> <span>خطأ</span>
                        </button>
                        <button type="button" onclick="selectWbShape('bubble')" class="col-span-2 p-1.5 rounded-xl hover:bg-indigo-600/80 text-white flex items-center justify-center gap-1.5 bg-slate-800/60 border border-slate-700/50 transition">
                            <span>💬</span> <span>فقاعة كلام وشرح</span>
                        </button>
                    </div>
                    <div class="pt-1.5 border-t border-slate-800 space-y-1.5">
                        <button type="button" onclick="toggleShapeFill()" id="btnShapeFill" class="w-full py-1 px-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold flex items-center justify-between transition">
                            <span>تعبئة الشكل:</span>
                            <span id="shapeFillState" class="text-indigo-400">مفرغ 🔲</span>
                        </button>
                        <button type="button" onclick="insertWbShapeAtCenter()" class="w-full py-1.5 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-md transition" title="إدراج الشكل المختار فوراً في منتصف السبورة">
                            <span>📥</span> <span>تنزيل الشكل في وسط السبورة</span>
                        </button>
                        <p class="text-[10px] text-slate-400 text-center">أو انقر بالماوس/المس في أي مكان لتنزيل الشكل</p>
                    </div>
                </div>

                <!-- 2. قائمة أختام التشجيع والتقييم (Stamps) -->
                <div id="stampsFlyoutMenu" class="wb-flyout-menu hidden absolute left-16 top-28 bg-slate-900/98 border border-slate-700/90 rounded-2xl shadow-2xl p-2.5 grid grid-cols-3 gap-1.5 z-40 w-44 backdrop-blur-md">
                    <div class="col-span-3 flex items-center justify-between border-b border-slate-800 pb-1 mb-1">
                        <span class="text-[11px] text-slate-300 font-bold">أختام تشجيعية:</span>
                        <button type="button" onclick="closeAllWbFlyouts()" class="text-slate-400 hover:text-white text-xs">✕</button>
                    </div>
                    <button type="button" onclick="selectWbStamp('⭐')" class="p-2 rounded-xl bg-slate-800/70 hover:bg-indigo-600 text-center text-xl transition shadow" title="نجمة إبداع">⭐</button>
                    <button type="button" onclick="selectWbStamp('💯')" class="p-2 rounded-xl bg-slate-800/70 hover:bg-indigo-600 text-center text-xl transition shadow" title="ممتاز 100%">💯</button>
                    <button type="button" onclick="selectWbStamp('👍')" class="p-2 rounded-xl bg-slate-800/70 hover:bg-indigo-600 text-center text-xl transition shadow" title="إعجاب ورائع">👍</button>
                    <button type="button" onclick="selectWbStamp('👏')" class="p-2 rounded-xl bg-slate-800/70 hover:bg-indigo-600 text-center text-xl transition shadow" title="أحسنت وتصفيق">👏</button>
                    <button type="button" onclick="selectWbStamp('❤️')" class="p-2 rounded-xl bg-slate-800/70 hover:bg-indigo-600 text-center text-xl transition shadow" title="مميز ومحبوب">❤️</button>
                    <button type="button" onclick="selectWbStamp('👑')" class="p-2 rounded-xl bg-slate-800/70 hover:bg-indigo-600 text-center text-xl transition shadow" title="بطل الحصة">👑</button>
                </div>

                <!-- 3. لوحة الألوان المتكاملة (42 لوناً رائعاً + منتقي مخصص بدقة) -->
                <div id="colorFlyoutMenu" class="wb-flyout-menu hidden absolute left-16 bottom-6 bg-slate-900/98 border border-slate-700/90 rounded-2xl shadow-2xl p-3 flex flex-col gap-2 z-40 w-64 backdrop-blur-md max-h-[85vh] overflow-y-auto scrollbar-none">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                        <div class="flex items-center gap-1.5">
                            <div class="w-4 h-4 rounded-full border border-white shadow-sm" id="colorIndicatorSwatch" style="background-color: #ef4444;"></div>
                            <span class="text-xs text-white font-black">لوحة الألوان (42 لوناً):</span>
                        </div>
                        <span id="activeColorHex" class="font-mono text-[10px] text-indigo-300 font-bold">#EF4444</span>
                    </div>

                    <!-- شبكة الـ 42 لوناً (7 صفوف × 6 أعمدة) -->
                    <div class="grid grid-cols-6 gap-2 py-1">
                        <!-- صف 1: أحمر وتدرجاته -->
                        <button type="button" onclick="setWbColor('#ef4444')" data-color="#ef4444" class="wb-color-swatch w-7 h-7 rounded-full bg-red-500 border border-white/30 hover:scale-125 transition shadow-sm" title="أحمر فاتح"></button>
                        <button type="button" onclick="setWbColor('#dc2626')" data-color="#dc2626" class="wb-color-swatch w-7 h-7 rounded-full bg-red-600 border border-white/30 hover:scale-125 transition shadow-sm" title="أحمر ساطع"></button>
                        <button type="button" onclick="setWbColor('#b91c1c')" data-color="#b91c1c" class="wb-color-swatch w-7 h-7 rounded-full bg-red-700 border border-white/30 hover:scale-125 transition shadow-sm" title="أحمر كلاسيكي"></button>
                        <button type="button" onclick="setWbColor('#991b1b')" data-color="#991b1b" class="wb-color-swatch w-7 h-7 rounded-full bg-red-800 border border-white/30 hover:scale-125 transition shadow-sm" title="عنابي داكن"></button>
                        <button type="button" onclick="setWbColor('#f43f5e')" data-color="#f43f5e" class="wb-color-swatch w-7 h-7 rounded-full bg-rose-500 border border-white/30 hover:scale-125 transition shadow-sm" title="مرجاني وردي"></button>
                        <button type="button" onclick="setWbColor('#be123c')" data-color="#be123c" class="wb-color-swatch w-7 h-7 rounded-full bg-rose-700 border border-white/30 hover:scale-125 transition shadow-sm" title="قرمزي داكن"></button>

                        <!-- صف 2: وردي وبنفسجي -->
                        <button type="button" onclick="setWbColor('#ec4899')" data-color="#ec4899" class="wb-color-swatch w-7 h-7 rounded-full bg-pink-500 border border-white/30 hover:scale-125 transition shadow-sm" title="وردي زاهي"></button>
                        <button type="button" onclick="setWbColor('#db2777')" data-color="#db2777" class="wb-color-swatch w-7 h-7 rounded-full bg-pink-600 border border-white/30 hover:scale-125 transition shadow-sm" title="فوشيا"></button>
                        <button type="button" onclick="setWbColor('#d946ef')" data-color="#d946ef" class="wb-color-swatch w-7 h-7 rounded-full bg-fuchsia-500 border border-white/30 hover:scale-125 transition shadow-sm" title="ماجنتا"></button>
                        <button type="button" onclick="setWbColor('#a855f7')" data-color="#a855f7" class="wb-color-swatch w-7 h-7 rounded-full bg-purple-500 border border-white/30 hover:scale-125 transition shadow-sm" title="بنفسجي مشرق"></button>
                        <button type="button" onclick="setWbColor('#7e22ce')" data-color="#7e22ce" class="wb-color-swatch w-7 h-7 rounded-full bg-purple-700 border border-white/30 hover:scale-125 transition shadow-sm" title="أرجواني غامق"></button>
                        <button type="button" onclick="setWbColor('#581c87')" data-color="#581c87" class="wb-color-swatch w-7 h-7 rounded-full bg-purple-900 border border-white/30 hover:scale-125 transition shadow-sm" title="بنفسجي ملكي"></button>

                        <!-- صف 3: نيلي وأزرق غامق -->
                        <button type="button" onclick="setWbColor('#818cf8')" data-color="#818cf8" class="wb-color-swatch w-7 h-7 rounded-full bg-indigo-400 border border-white/30 hover:scale-125 transition shadow-sm" title="نيلي فاتح"></button>
                        <button type="button" onclick="setWbColor('#6366f1')" data-color="#6366f1" class="wb-color-swatch w-7 h-7 rounded-full bg-indigo-500 border border-white/30 hover:scale-125 transition shadow-sm" title="نيلي ساطع"></button>
                        <button type="button" onclick="setWbColor('#4338ca')" data-color="#4338ca" class="wb-color-swatch w-7 h-7 rounded-full bg-indigo-700 border border-white/30 hover:scale-125 transition shadow-sm" title="نيلي داكن"></button>
                        <button type="button" onclick="setWbColor('#3b82f6')" data-color="#3b82f6" class="wb-color-swatch w-7 h-7 rounded-full bg-blue-500 border border-white/30 hover:scale-125 transition shadow-sm" title="أزرق مشرق"></button>
                        <button type="button" onclick="setWbColor('#1d4ed8')" data-color="#1d4ed8" class="wb-color-swatch w-7 h-7 rounded-full bg-blue-700 border border-white/30 hover:scale-125 transition shadow-sm" title="أزرق ملكي"></button>
                        <button type="button" onclick="setWbColor('#1e3a8a')" data-color="#1e3a8a" class="wb-color-swatch w-7 h-7 rounded-full bg-blue-900 border border-white/30 hover:scale-125 transition shadow-sm" title="كحلي بحري"></button>

                        <!-- صف 4: سماوي وتركواز -->
                        <button type="button" onclick="setWbColor('#38bdf8')" data-color="#38bdf8" class="wb-color-swatch w-7 h-7 rounded-full bg-sky-400 border border-white/30 hover:scale-125 transition shadow-sm" title="سماوي فاتح"></button>
                        <button type="button" onclick="setWbColor('#0284c7')" data-color="#0284c7" class="wb-color-swatch w-7 h-7 rounded-full bg-sky-600 border border-white/30 hover:scale-125 transition shadow-sm" title="أزرق بحري"></button>
                        <button type="button" onclick="setWbColor('#06b6d4')" data-color="#06b6d4" class="wb-color-swatch w-7 h-7 rounded-full bg-cyan-500 border border-white/30 hover:scale-125 transition shadow-sm" title="سماوي مائي"></button>
                        <button type="button" onclick="setWbColor('#0891b2')" data-color="#0891b2" class="wb-color-swatch w-7 h-7 rounded-full bg-cyan-600 border border-white/30 hover:scale-125 transition shadow-sm" title="تركواز بترولي"></button>
                        <button type="button" onclick="setWbColor('#14b8a6')" data-color="#14b8a6" class="wb-color-swatch w-7 h-7 rounded-full bg-teal-500 border border-white/30 hover:scale-125 transition shadow-sm" title="تركواز مشرق"></button>
                        <button type="button" onclick="setWbColor('#0f766e')" data-color="#0f766e" class="wb-color-swatch w-7 h-7 rounded-full bg-teal-700 border border-white/30 hover:scale-125 transition shadow-sm" title="أخضر تركوازي"></button>

                        <!-- صف 5: زمردي وأخضر -->
                        <button type="button" onclick="setWbColor('#34d399')" data-color="#34d399" class="wb-color-swatch w-7 h-7 rounded-full bg-emerald-400 border border-white/30 hover:scale-125 transition shadow-sm" title="زمردي فاتح"></button>
                        <button type="button" onclick="setWbColor('#10b981')" data-color="#10b981" class="wb-color-swatch w-7 h-7 rounded-full bg-emerald-500 border border-white/30 hover:scale-125 transition shadow-sm" title="زمردي نضر"></button>
                        <button type="button" onclick="setWbColor('#047857')" data-color="#047857" class="wb-color-swatch w-7 h-7 rounded-full bg-emerald-700 border border-white/30 hover:scale-125 transition shadow-sm" title="أخضر زمردي داكن"></button>
                        <button type="button" onclick="setWbColor('#22c55e')" data-color="#22c55e" class="wb-color-swatch w-7 h-7 rounded-full bg-green-500 border border-white/30 hover:scale-125 transition shadow-sm" title="أخضر عشبي"></button>
                        <button type="button" onclick="setWbColor('#15803d')" data-color="#15803d" class="wb-color-swatch w-7 h-7 rounded-full bg-green-700 border border-white/30 hover:scale-125 transition shadow-sm" title="أخضر غابات"></button>
                        <button type="button" onclick="setWbColor('#84cc16')" data-color="#84cc16" class="wb-color-swatch w-7 h-7 rounded-full bg-lime-500 border border-white/30 hover:scale-125 transition shadow-sm" title="ليموني فاقع"></button>

                        <!-- صف 6: أصفر وبرتقالي -->
                        <button type="button" onclick="setWbColor('#a3e635')" data-color="#a3e635" class="wb-color-swatch w-7 h-7 rounded-full bg-lime-400 border border-white/30 hover:scale-125 transition shadow-sm" title="أخضر فسفوري"></button>
                        <button type="button" onclick="setWbColor('#facc15')" data-color="#facc15" class="wb-color-swatch w-7 h-7 rounded-full bg-yellow-400 border border-white/30 hover:scale-125 transition shadow-sm" title="أصفر ذهبي"></button>
                        <button type="button" onclick="setWbColor('#eab308')" data-color="#eab308" class="wb-color-swatch w-7 h-7 rounded-full bg-yellow-500 border border-white/30 hover:scale-125 transition shadow-sm" title="أصفر فاقع"></button>
                        <button type="button" onclick="setWbColor('#f59e0b')" data-color="#f59e0b" class="wb-color-swatch w-7 h-7 rounded-full bg-amber-500 border border-white/30 hover:scale-125 transition shadow-sm" title="كهرماني"></button>
                        <button type="button" onclick="setWbColor('#f97316')" data-color="#f97316" class="wb-color-swatch w-7 h-7 rounded-full bg-orange-500 border border-white/30 hover:scale-125 transition shadow-sm" title="برتقالي مشرق"></button>
                        <button type="button" onclick="setWbColor('#ea580c')" data-color="#ea580c" class="wb-color-swatch w-7 h-7 rounded-full bg-orange-600 border border-white/30 hover:scale-125 transition shadow-sm" title="برتقالي ناري"></button>

                        <!-- صف 7: بني، رمادي وأبيض وأسود -->
                        <button type="button" onclick="setWbColor('#c2410c')" data-color="#c2410c" class="wb-color-swatch w-7 h-7 rounded-full bg-orange-800 border border-white/30 hover:scale-125 transition shadow-sm" title="طوبي محروق"></button>
                        <button type="button" onclick="setWbColor('#9a3412')" data-color="#9a3412" class="wb-color-swatch w-7 h-7 rounded-full bg-amber-800 border border-white/30 hover:scale-125 transition shadow-sm" title="بني دافئ"></button>
                        <button type="button" onclick="setWbColor('#78350f')" data-color="#78350f" class="wb-color-swatch w-7 h-7 rounded-full bg-amber-950 border border-white/30 hover:scale-125 transition shadow-sm" title="بني غامق"></button>
                        <button type="button" onclick="setWbColor('#64748b')" data-color="#64748b" class="wb-color-swatch w-7 h-7 rounded-full bg-slate-500 border border-white/30 hover:scale-125 transition shadow-sm" title="رمادي أردوازي"></button>
                        <button type="button" onclick="setWbColor('#0f172a')" data-color="#0f172a" class="wb-color-swatch w-7 h-7 rounded-full bg-slate-900 border border-white/50 hover:scale-125 transition shadow-sm" title="أسود فاحم"></button>
                        <button type="button" onclick="setWbColor('#ffffff')" data-color="#ffffff" class="wb-color-swatch w-7 h-7 rounded-full bg-white border border-slate-400 hover:scale-125 transition shadow-sm" title="أبيض ناصع"></button>
                    </div>

                    <!-- اختيار لون مخصص إضافي -->
                    <div class="pt-2 border-t border-slate-800 flex items-center justify-between text-xs text-slate-300">
                        <span class="font-bold">لون مخصص إضافي:</span>
                        <div class="flex items-center gap-1.5">
                            <input type="color" id="wbCustomColorInput" value="#ef4444" onchange="setWbColor(this.value)" class="w-8 h-8 rounded-xl cursor-pointer border border-slate-600 bg-transparent p-0 shadow transition hover:scale-110" title="اختر أي لون مخصص بدقة">
                            <span class="text-[10px] text-slate-400">انقر للاختيار</span>
                        </div>
                    </div>
                </div>

                <!-- 4. قائمة سماكة وحجم الخط والقلم (7 أحجام) -->
                <div id="sizeFlyoutMenu" class="wb-flyout-menu hidden absolute left-16 bottom-16 bg-slate-900/98 border border-slate-700/90 rounded-2xl shadow-2xl p-2.5 flex flex-col gap-1 z-40 w-36 backdrop-blur-md">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-1 mb-1">
                        <span class="text-[11px] text-slate-300 font-bold">سماكة القلم:</span>
                        <button type="button" onclick="closeAllWbFlyouts()" class="text-slate-400 hover:text-white text-xs">✕</button>
                    </div>
                    <button type="button" onclick="setWbSize(1)" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white text-xs font-bold flex items-center justify-between transition">
                        <span>دقيق جداً</span> <span class="font-mono text-[10px] text-indigo-300">1px</span>
                    </button>
                    <button type="button" onclick="setWbSize(2)" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white text-xs font-bold flex items-center justify-between transition">
                        <span>رفيع</span> <span class="font-mono text-[10px] text-indigo-300">2px</span>
                    </button>
                    <button type="button" onclick="setWbSize(4)" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white text-xs font-bold flex items-center justify-between transition">
                        <span>متوسط</span> <span class="font-mono text-[10px] text-indigo-300">4px</span>
                    </button>
                    <button type="button" onclick="setWbSize(8)" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white text-xs font-bold flex items-center justify-between transition">
                        <span>عريض</span> <span class="font-mono text-[10px] text-indigo-300">8px</span>
                    </button>
                    <button type="button" onclick="setWbSize(14)" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white text-xs font-bold flex items-center justify-between transition">
                        <span>ماركر</span> <span class="font-mono text-[10px] text-indigo-300">14px</span>
                    </button>
                    <button type="button" onclick="setWbSize(24)" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white text-xs font-bold flex items-center justify-between transition">
                        <span>عريض جداً</span> <span class="font-mono text-[10px] text-indigo-300">24px</span>
                    </button>
                    <button type="button" onclick="setWbSize(36)" class="p-1.5 rounded-lg hover:bg-indigo-600 text-white text-xs font-bold flex items-center justify-between transition">
                        <span>جامبو</span> <span class="font-mono text-[10px] text-indigo-300">36px</span>
                    </button>
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

    <!-- نافذة مكافأة الطالب بنجمة (Reward Star Modal) -->
    <div id="rewardStarModal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4" onclick="closeRewardStarModal()">
        <div class="max-w-md w-full bg-slate-900 border border-amber-500/50 rounded-3xl shadow-2xl p-5 sm:p-6 space-y-4" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="text-3xl animate-bounce">⭐</span>
                    <div>
                        <h3 class="text-base font-black text-amber-300">منح مكافأة نجمة لطالب</h3>
                        <p class="text-[11px] text-slate-400">تُضاف النجمة مباشرة إلى رصيد تقييمات الطالب الدائم</p>
                    </div>
                </div>
                <button type="button" onclick="closeRewardStarModal()" class="text-slate-400 hover:text-white text-xl font-bold">✕</button>
            </div>

            <!-- اختيار الطالب -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-300">اختر الطالب المستحق للمكافأة:</label>
                <select id="rewardStudentSelect" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:outline-none">
                    @if(isset($students) && $students->isNotEmpty())
                        @foreach($students as $st)
                            <option value="{{ $st->id }}">{{ $st->name }} (رصيده: {{ $st->stars_balance ?? 0 }} ⭐)</option>
                        @endforeach
                        @if($students->count() > 1)
                            <option value="all">🌟 جميع الطلاب الحاضرين في الحصة (مكافأة جماعية للكل)</option>
                        @endif
                    @elseif(isset($booking) && $booking && $booking->student)
                        <option value="{{ $booking->student->id }}">{{ $booking->student->name }} (رصيده: {{ $booking->student->stars_balance ?? 0 }} ⭐)</option>
                    @endif
                </select>
            </div>

            <!-- عدد النجوم والسبب -->
            <div class="grid grid-cols-2 gap-2.5">
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-300">عدد النجوم:</label>
                    <select id="rewardStarsAmount" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 font-bold focus:border-amber-500 focus:outline-none font-mono">
                        <option value="1">⭐ نجمة واحدة (1)</option>
                        <option value="2">⭐⭐ نجمتان (2)</option>
                        <option value="3">⭐⭐⭐ 3 نجوم (3)</option>
                        <option value="5">⭐⭐⭐⭐⭐ 5 نجوم (إبداع خارق)</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-300">سبب المكافأة:</label>
                    <select id="rewardStarsReason" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:outline-none">
                        <option value="مشاركة وإجابة ممتازة">مشاركة وإجابة ممتازة 👏</option>
                        <option value="إتقان نطق الكلمات">إتقان نطق الكلمات 🗣️</option>
                        <option value="سرعة البديهة والحل">سرعة البديهة والحل ⚡</option>
                        <option value="التزام وهدوء رائع">التزام وهدوء رائع 🌟</option>
                        <option value="إنجاز الواجب والنشاط">إنجاز الواجب والنشاط 📝</option>
                    </select>
                </div>
            </div>

            <!-- زر اعتماد المكافأة -->
            <div class="pt-2 flex gap-2">
                <button type="button" onclick="submitStudentStarReward()" id="btnSubmitRewardStar" class="flex-1 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs rounded-xl shadow-lg transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <span>⭐ اعتماد وإضافة لرصيد الطالب</span>
                </button>
                <button type="button" onclick="closeRewardStarModal()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition">
                    إلغاء
                </button>
            </div>
        </div>
    </div>

    <!-- شاشة الاحتفال بنجمة التميز للطلاب (Star Celebration Overlay) -->
    <div id="starCelebrationOverlay" class="hidden fixed inset-0 z-50 pointer-events-none flex items-center justify-center bg-black/40 backdrop-blur-2xs transition-all duration-500">
        <div id="starCelebrationCard" class="bg-gradient-to-br from-amber-950/95 via-slate-900/95 to-slate-950/95 border-2 border-amber-400 rounded-3xl p-6 sm:p-8 max-w-sm w-full mx-4 text-center shadow-2xl space-y-3 transform scale-90 transition-transform duration-300 pointer-events-auto">
            <div class="text-6xl animate-bounce">⭐</div>
            <h3 class="text-lg font-black text-amber-300" id="starCelebrationTitle">أحسنت يا بطل!</h3>
            <p class="text-xs text-slate-200" id="starCelebrationDesc">منحك المعلم نجمة تميز تشجيعية تقديراً لمشاركتك الرائعة!</p>
            <div class="inline-flex items-center gap-2 bg-amber-500/20 text-amber-200 border border-amber-500/40 px-3.5 py-1.5 rounded-full text-xs font-black font-mono">
                <span>رصيدك الإجمالي الآن:</span>
                <span id="starCelebrationBalance" class="text-sm text-amber-300">0</span>
                <span>⭐</span>
            </div>
            <div class="pt-2">
                <button type="button" onclick="closeStarCelebration()" class="px-5 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs rounded-xl shadow transition cursor-pointer">
                    شكراً يا أستاذ! 🎉
                </button>
            </div>
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

            <!-- اختصارات سريعة لأشهر منصات مسابقات وألعاب الأطفال المضمونة -->
            <div class="space-y-2 pt-2 border-t border-slate-800">
                <div class="flex items-center justify-between">
                    <span class="block text-xs font-bold text-amber-400">⚡ منصات مسابقات وألعاب تعليمية سريعة:</span>
                    <button type="button" onclick="navigateBrowserToGoogleHome(); closeWbMediaModals();" class="text-[11px] text-cyan-400 hover:text-cyan-300 font-bold flex items-center gap-1">
                        <span>🔍 فتح جوجل المباشر</span>
                    </button>
                </div>

                <div class="p-2.5 bg-amber-500/10 border border-amber-500/30 rounded-xl text-[11px] text-amber-200 flex items-start gap-2 leading-relaxed">
                    <span class="text-base shrink-0">💡</span>
                    <span><strong>تلميح مهم:</strong> لفتح ألعاب <strong>Wordwall</strong>، انسخ رابط اللعبة وضعه في خانة الرابط أعلاه. وإذا صادفت أي موقع يمنع التضمين، افتحه عبر زر <strong>[فتح بتبويب مستقل ↗️]</strong> واستخدم ميزة <strong>[مشاركة الشاشة 🖥️]</strong> في البث ليشاهده الطلاب فوراً.</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <button type="button" onclick="openPresetGame('https://www.google.com/webhp?igu=1', 'محرك بحث جوجل الشامل')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-indigo-950/60 border border-slate-800 hover:border-indigo-500/50 text-right transition group">
                        <span class="text-base block mb-1">🔍</span>
                        <strong class="text-xs text-white block">Google</strong>
                        <span class="text-[10px] text-slate-400 block truncate">بحث جوجل الشامل</span>
                    </button>
                    <button type="button" onclick="openPresetGame('https://wheelofnames.com/ar', 'عجلة الحظ التفاعلية للطلاب')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-indigo-950/60 border border-slate-800 hover:border-indigo-500/50 text-right transition group">
                        <span class="text-base block mb-1">🎲</span>
                        <strong class="text-xs text-white block">عجلة الأسماء</strong>
                        <span class="text-[10px] text-slate-400 block truncate">سحب عشوائي للطلاب</span>
                    </button>
                    <button type="button" onclick="openPresetGame('https://kahoot.it/', 'لعبة كاهوت Kahoot')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-indigo-950/60 border border-slate-800 hover:border-indigo-500/50 text-right transition group">
                        <span class="text-base block mb-1">🏆</span>
                        <strong class="text-xs text-white block">Kahoot!</strong>
                        <span class="text-[10px] text-slate-400 block truncate">إدخال كود المسابقة</span>
                    </button>
                    <button type="button" onclick="openPresetGame('https://www.geogebra.org/calculator', 'حاسبة GeoGebra التفاعلية')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-indigo-950/60 border border-slate-800 hover:border-indigo-500/50 text-right transition group">
                        <span class="text-base block mb-1">🧮</span>
                        <strong class="text-xs text-white block">GeoGebra</strong>
                        <span class="text-[10px] text-slate-400 block truncate">رياضيات ورسوم بيانية</span>
                    </button>
                    <button type="button" onclick="openPresetGame('https://phet.colorado.edu/sims/html/forces-and-motion-basics/latest/forces-and-motion-basics_all.html', 'محاكاة العلوم PhET')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-indigo-950/60 border border-slate-800 hover:border-indigo-500/50 text-right transition group">
                        <span class="text-base block mb-1">🔬</span>
                        <strong class="text-xs text-white block">PhET Science</strong>
                        <span class="text-[10px] text-slate-400 block truncate">تجارب فيزياء وعلوم</span>
                    </button>
                    <button type="button" onclick="openPresetGame('https://quizizz.com/join', 'منصة Quizizz التنافسية')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-indigo-950/60 border border-slate-800 hover:border-indigo-500/50 text-right transition group">
                        <span class="text-base block mb-1">⚡</span>
                        <strong class="text-xs text-white block">Quizizz</strong>
                        <span class="text-[10px] text-slate-400 block truncate">منافسات وأسئلة تفاعلية</span>
                    </button>
                    <button type="button" onclick="openPresetGame('https://ar.wikipedia.org', 'موسوعة ويكيبيديا العربية')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-indigo-950/60 border border-slate-800 hover:border-indigo-500/50 text-right transition group">
                        <span class="text-base block mb-1">📚</span>
                        <strong class="text-xs text-white block">Wikipedia</strong>
                        <span class="text-[10px] text-slate-400 block truncate">الموسوعة الحرة</span>
                    </button>
                    <button type="button" onclick="openPresetGame('https://html.duckduckgo.com/html/', 'محرك بحث DuckDuckGo السريع')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-indigo-950/60 border border-slate-800 hover:border-indigo-500/50 text-right transition group">
                        <span class="text-base block mb-1">🦆</span>
                        <strong class="text-xs text-white block">DuckDuckGo</strong>
                        <span class="text-[10px] text-slate-400 block truncate">بحث ويب سريع</span>
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

    <!-- نافذة مكتبة كتب ومذكرات الدورة المتكاملة (PDF Books & Documents Library Modal) -->
    <div id="wbBooksLibraryModal" class="hidden fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-3 sm:p-5" onclick="closeBooksLibraryModal()">
        <div class="max-w-3xl w-full bg-slate-900 border border-slate-700/90 rounded-3xl shadow-2xl flex flex-col max-h-[90vh] overflow-hidden" onclick="event.stopPropagation()">
            
            <!-- ترويسة النافذة المنبثقة -->
            <div class="p-4 sm:p-5 border-b border-slate-800 bg-slate-950/80 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600/30 border border-indigo-500/40 flex items-center justify-center text-xl shadow-inner">
                        <span>📖</span>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-white">مكتبة كتب ومذكرات الدورة التدريبية</h3>
                        <p class="text-xs text-slate-400">استعرض كتب المنهج ومذكرات المجموعة واشرح للطلاب عليها كقارئ PDF متكامل</p>
                    </div>
                </div>
                <button type="button" onclick="closeBooksLibraryModal()" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-rose-600 hover:text-white text-slate-400 flex items-center justify-center transition font-bold text-sm">✕</button>
            </div>

            <!-- أزرار التبويبات الثلاثة -->
            <div class="p-2.5 bg-slate-950/50 border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-1.5 bg-slate-900/80 p-1 rounded-2xl border border-slate-800 text-xs font-bold">
                    <button type="button" onclick="switchBookLibTab('curriculum')" id="btnBookLibTab_curriculum" class="flex-1 py-2 rounded-xl bg-indigo-600 text-white transition flex items-center justify-center gap-1.5 shadow">
                        <span>📚</span>
                        <span>كتب المنهج المعتمدة</span>
                        <span class="bg-indigo-950 text-indigo-200 text-[10px] px-1.5 py-0.2 rounded-full font-mono">{{ $groupBooks->count() }}</span>
                    </button>
                    <button type="button" onclick="switchBookLibTab('wall')" id="btnBookLibTab_wall" class="flex-1 py-2 rounded-xl text-slate-400 hover:text-white transition flex items-center justify-center gap-1.5">
                        <span>📝</span>
                        <span>حائط وأنشطة المجموعة</span>
                        <span class="bg-slate-800 text-slate-300 text-[10px] px-1.5 py-0.2 rounded-full font-mono">{{ $groupWallPdfs->count() }}</span>
                    </button>
                    <button type="button" onclick="switchBookLibTab('upload')" id="btnBookLibTab_upload" class="flex-1 py-2 rounded-xl text-slate-400 hover:text-white transition flex items-center justify-center gap-1.5">
                        <span>📤</span>
                        <span>رفع كتاب من جهازي</span>
                    </button>
                </div>
            </div>

            <!-- المحتوى التفاعلي للتبويبات -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4">
                
                <!-- 1. تبويب كتب المنهج المعتمدة للمستوى -->
                <div id="bookLibTab_curriculum" class="space-y-3">
                    <div class="bg-indigo-950/40 border border-indigo-900/60 p-3 rounded-2xl text-xs text-indigo-200 flex items-start gap-2">
                        <span class="text-base shrink-0">💡</span>
                        <span>كتب المنهج المعتمدة تفتح داخل السبورة كقارئ PDF كامل، مع إمكانية التنقل بين جميع الصفحات بالأسهم والرسم والشرح فوقها للطلاب.</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @forelse($groupBooks as $bk)
                            <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 hover:border-indigo-500/50 flex flex-col justify-between gap-3 transition group">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-950/80 border border-indigo-800/60 flex items-center justify-center text-lg shrink-0">
                                        <span>📕</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-bold text-white text-xs truncate group-hover:text-indigo-300 transition" title="{{ $bk->title }}">{{ $bk->title }}</h4>
                                        <span class="text-[10px] text-slate-400 font-mono block mt-0.5">PDF • كتاب الدورة المعتمد</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 pt-2 border-t border-slate-900">
                                    @if($isTeacher || (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor())))
                                        <button type="button" onclick="openResourceInWorkspace('{{ route('resources.stream', $bk->id) }}', '{{ addslashes($bk->title) }}', 'pdf', 1); closeBooksLibraryModal();" class="flex-1 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow">
                                            <span>🚀</span>
                                            <span>فتح للشرح الآن</span>
                                        </button>
                                    @endif
                                    <a href="{{ route('resources.download', $bk->id) }}" target="_blank" class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition flex items-center gap-1" title="معاينة أو تنزيل">
                                        <span>⬇️</span>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 p-6 text-center text-slate-500 bg-slate-950/50 rounded-2xl border border-dashed border-slate-800 text-xs">
                                <span>لا توجد كتب PDF مسجلة لهذا المستوى حالياً. يمكنك استخدام تبويب "رفع كتاب من جهازي" لمشاركة أي كتاب فوراً.</span>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- 2. تبويب ملفات وأوراق عمل حائط المجموعة -->
                <div id="bookLibTab_wall" class="hidden space-y-3">
                    <div class="bg-indigo-950/40 border border-indigo-900/60 p-3 rounded-2xl text-xs text-indigo-200 flex items-start gap-2">
                        <span class="text-base shrink-0">📌</span>
                        <span>أوراق العمل والمذكرات المنشورة في أنشطة وحائط المجموعة قابلة للاستعراض والشرح الفوري.</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @forelse($groupWallPdfs as $act)
                            <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 hover:border-indigo-500/50 flex flex-col justify-between gap-3 transition">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-10 h-10 rounded-xl bg-purple-950/80 border border-purple-800/60 flex items-center justify-center text-lg shrink-0">
                                        <span>📄</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-bold text-white text-xs truncate" title="{{ $act->title }}">{{ $act->title }}</h4>
                                        <span class="text-[10px] text-purple-300 font-mono block mt-0.5">مذكرة / ورقة عمل</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 pt-2 border-t border-slate-900">
                                    @if($isTeacher || (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor())))
                                        <button type="button" onclick="openResourceInWorkspace('{{ route('groups.activities.stream', $act->id) }}', '{{ addslashes($act->title) }}', 'pdf', 1); closeBooksLibraryModal();" class="flex-1 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow">
                                            <span>🚀</span>
                                            <span>فتح للشرح الآن</span>
                                        </button>
                                    @endif
                                    <a href="{{ route('groups.activities.download', $act->id) }}" target="_blank" class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition flex items-center gap-1" title="معاينة">
                                        <span>⬇️</span>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 p-6 text-center text-slate-500 bg-slate-950/50 rounded-2xl border border-dashed border-slate-800 text-xs">
                                <span>لا توجد مذكرات أو ملفات PDF منشورة بحائط المجموعة بعد.</span>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- 3. تبويب رفع كتاب PDF من الجهاز فوراً -->
                <div id="bookLibTab_upload" class="hidden space-y-4">
                    <div class="border-2 border-dashed border-indigo-500/40 hover:border-indigo-400 bg-indigo-950/20 hover:bg-indigo-950/30 rounded-3xl p-6 sm:p-8 text-center transition cursor-pointer" onclick="document.getElementById('wbLocalFileInput').click(); closeBooksLibraryModal();">
                        <div class="w-16 h-16 mx-auto rounded-3xl bg-indigo-600/30 border border-indigo-500/40 flex items-center justify-center text-3xl mb-3 shadow-inner">
                            <span>📕</span>
                        </div>
                        <h4 class="text-sm font-black text-white mb-1">انقر هنا لاختيار كتاب أو مذكرة PDF من جهازك</h4>
                        <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
                            سيتم فتح الكتاب فوراً داخل مساحة الشرح بجميع صفحاته، لتبدأ التصفح والشرح للطلاب دون أي تأخير، مع رفعه ومزامنته تلقائياً.
                        </p>
                        <button type="button" class="mt-4 px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition inline-flex items-center gap-2">
                            <span>📂</span>
                            <span>استعراض ملفات جهازي</span>
                        </button>
                    </div>
                </div>

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
    const CAN_MANAGE_ROOM = {{ ($isTeacher || (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupervisor()))) ? 'true' : 'false' }};
    const clientSessionStart = Math.floor(Date.now() / 1000);
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

    const peerCandidateQueue = {}; // key: peerId => Array of queued RTCIceCandidateInit

    const ICE_SERVERS = {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' },
            { urls: 'stun:stun2.l.google.com:19302' },
            { urls: 'stun:stun3.l.google.com:19302' },
            { urls: 'stun:stun4.l.google.com:19302' },
            { urls: 'stun:stun.cloudflare.com:3478' }
        ],
        iceCandidatePoolSize: 10
    };

    function normalizeMediaUrl(url) {
        if (!url || typeof url !== 'string') return url;
        try {
            const parsed = new URL(url, window.location.href);
            if (parsed.pathname.startsWith('/resources/') || parsed.pathname.startsWith('/storage/') || parsed.pathname.startsWith('/classroom/')) {
                return window.location.origin + parsed.pathname + parsed.search;
            }
        } catch(e) {
            if (url.startsWith('/')) {
                return window.location.origin + url;
            }
        }
        return url;
    }

    function showLiveToast(msg, icon = '📢') {
        let t = document.getElementById('liveClassToast');
        if (!t) {
            t = document.createElement('div');
            t.id = 'liveClassToast';
            t.className = "fixed bottom-5 left-1/2 -translate-x-1/2 z-[9999] bg-slate-900/95 text-white border border-indigo-500 px-4 py-2.5 rounded-2xl shadow-2xl flex items-center gap-2 text-xs font-bold transition-all duration-300 opacity-0 pointer-events-none";
            document.body.appendChild(t);
        }
        t.innerHTML = `<span class="text-base">${icon}</span><span>${msg}</span>`;
        t.classList.remove('opacity-0', 'pointer-events-none');
        t.classList.add('opacity-100');
        setTimeout(() => {
            t.classList.remove('opacity-100');
            t.classList.add('opacity-0', 'pointer-events-none');
        }, 4000);
    }

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
                case 'wb_video_state':
                    const vSyncEl = document.getElementById('whiteboardHtml5Video');
                    if (vSyncEl && !IS_TEACHER) {
                        if (typeof data.time === 'number' && Math.abs(vSyncEl.currentTime - data.time) > 1.5) {
                            vSyncEl.currentTime = data.time;
                        }
                        if (data.action === 'play') {
                            vSyncEl.play().catch(() => {});
                        } else if (data.action === 'pause') {
                            vSyncEl.pause();
                        }
                    }
                case 'wb_scroll':
                    if (!IS_TEACHER) {
                        const wbCont = document.getElementById('whiteboardContainer');
                        if (wbCont) {
                            const maxScroll = wbCont.scrollHeight - wbCont.clientHeight;
                            const targetScroll = typeof data.scrollRatio === 'number' && maxScroll > 0
                                ? (data.scrollRatio * maxScroll)
                                : data.scrollTop;
                            wbCont.scrollTo({ top: targetScroll, behavior: 'smooth' });
                        }
                    }
                    break;
                case 'chat_cleared':
                    clearChatContainerUI();
                    break;
                case 'force_refresh':
                    window.location.reload();
                    break;
                case 'force_mute_all':
                    if (!IS_TEACHER) {
                        if (isMicEnabled) toggleLocalMic();
                        alert('قام المعلم بكتم صوت جميع الطلاب 🔇');
                    }
                    break;
                case 'force_mute_user':
                    if (data.target_user_id === CURRENT_USER_ID) {
                        if (isMicEnabled) toggleLocalMic();
                        alert('قام المعلم بكتم صوتك 🔇');
                    }
                    break;
                case 'allow_speak_user':
                    if (data.target_user_id === CURRENT_USER_ID) {
                        isHandRaised = false;
                        isMicEnabled = true;
                        if (!localStream) {
                            initLocalMedia();
                        } else {
                            localStream.getAudioTracks().forEach(t => t.enabled = true);
                            ensureTracksInPeerConnections();
                            updateMediaButtonsUI();
                        }
                        showLiveToast('🎙️ أذن لك المعلم بالتحدث الآن! المايكروفون مفتوح وصوتك يصل للجميع.', '🎙️');
                    }
                    break;
                case 'star_rewarded':
                    if (data.user_id === 'all' || String(data.user_id) === String(CURRENT_USER_ID)) {
                        showStarCelebration(data.student_name || CURRENT_USER_NAME, data.stars_count || 1, data.new_balance, data.reason || 'مشاركة ممتازة');
                    }
                    break;
            }
        } catch (e) {
            console.error('Error handling P2P realtime message:', e);
        }
    }

    // إدارة قفل وفك الصوت المتقدم (Web Audio API Unlocker)
    async function unlockAudioSystem() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                if (!window.globalAudioCtx) {
                    window.globalAudioCtx = new AudioContext();
                }
                if (window.globalAudioCtx.state === 'suspended') {
                    await window.globalAudioCtx.resume();
                }
            }
            unblockAllAudio();
        } catch(e) {
            console.warn('unlockAudioSystem error:', e);
        }
    }

    async function confirmAudioPermission() {
        await unlockAudioSystem();
        try {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                localStream = await navigator.mediaDevices.getUserMedia({
                    audio: { echoCancellation: true, noiseSuppression: true, autoGainControl: true },
                    video: false
                });
                isCamEnabled = false;
                isMicEnabled = IS_TEACHER ? true : (currentRoomPolicy !== 'permission');
                if (!isMicEnabled) {
                    localStream.getAudioTracks().forEach(t => t.enabled = false);
                }
                ensureTracksInPeerConnections();
                updateMediaButtonsUI();
            } else {
                document.getElementById('micHelpNotice')?.classList.remove('hidden');
            }
        } catch (err) {
            console.warn('Microphone permission in modal caught:', err);
            document.getElementById('micHelpNotice')?.classList.remove('hidden');
        }
        document.getElementById('audioPermissionModal')?.classList.add('hidden');
        document.getElementById('audioUnblockBanner')?.classList.add('hidden');
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
                        // إعادة التفاوض (Renegotiation) فورياً لكلا الطرفين لضمان وصول الصوت
                        pc.createOffer({ offerToReceiveAudio: true, offerToReceiveVideo: true })
                            .then(offer => pc.setLocalDescription(offer))
                            .then(() => postSignal(peerId, 'offer', pc.localDescription))
                            .catch(e => console.log('Renegotiate offer err:', e));
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

        // إذا كانت السياسة "بإذن" والمستخدم ليس معلماً أو مديراً ولم يُمنح الإذن بعد
        if (!CAN_MANAGE_ROOM && currentRoomPolicy === 'permission' && !isHandRaised) {
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
        // تشغيل الصوت دائماً عبر عنصر Audio
        if (!remoteAudios[remotePeerId]) {
            const audioEl = document.createElement('audio');
            audioEl.id = 'audio_' + remotePeerId;
            audioEl.autoplay = true;
            audioEl.playsInline = true;
            audioEl.setAttribute('playsinline', '');
            audioEl.setAttribute('autoplay', '');
            audioEl.volume = 1.0;
            audioEl.srcObject = stream;
            document.body.appendChild(audioEl);
            remoteAudios[remotePeerId] = audioEl;

            audioEl.play().catch(e => {
                console.log('Audio autoplay prevented, showing unblock banner:', e);
                document.getElementById('audioUnblockBanner')?.classList.remove('hidden');
            });
        } else {
            remoteAudios[remotePeerId].srcObject = stream;
            remoteAudios[remotePeerId].play().catch(() => {});
        }

        // توجيه الصوت أيضاً عبر Web Audio API لضمان وصوله بمكبر الصوت في الهواتف
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                if (!window.globalAudioCtx) window.globalAudioCtx = new AudioContext();
                if (window.globalAudioCtx.state === 'running') {
                    const src = window.globalAudioCtx.createMediaStreamSource(stream);
                    src.connect(window.globalAudioCtx.destination);
                }
            }
        } catch (e) {
            console.log('Web Audio routing err:', e);
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
                    ${CAN_MANAGE_ROOM ? `
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="openRewardStarModal(${remoteUserId}, '${addslashes(remoteName)}')" class="bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 px-2 py-0.5 rounded font-bold" title="مكافأة بنجمة">
                                ⭐ كافئ
                            </button>
                            <button type="button" onclick="muteStudent(${remoteUserId})" class="bg-rose-950 hover:bg-rose-900 text-rose-300 border border-rose-800 px-2 py-0.5 rounded font-bold">
                                كتم 🔇
                            </button>
                        </div>
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
        unblockAllAudio();
        document.getElementById('audioUnblockBanner')?.classList.add('hidden');
    }

    function unblockAllAudio() {
        if (window.globalAudioCtx && window.globalAudioCtx.state === 'suspended') {
            window.globalAudioCtx.resume().catch(() => {});
        }
        Object.values(remoteAudios).forEach(a => {
            try {
                if (a) {
                    a.volume = 1.0;
                    a.play().then(() => {
                        document.getElementById('audioUnblockBanner')?.classList.add('hidden');
                    }).catch(e => console.log('Audio autoplay resume waiting:', e));
                }
            } catch(e){}
        });
    }

    // فك حجب الصوت تلقائياً بمجرد لمس أو نقر شاشة الهاتف
    ['click', 'touchstart', 'touchend', 'pointerdown'].forEach(evt => {
        document.addEventListener(evt, () => {
            if (typeof unlockAudioSystem === 'function') unlockAudioSystem();
            unblockAllAudio();
        }, { passive: true });
    });

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
                whiteboard_version: currentWbVersion,
                session_start: clientSessionStart
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

            if (data.chat_cleared) {
                clearChatContainerUI();
            }

            if (data.force_refresh) {
                window.location.reload();
                return;
            }

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

                if (!CAN_MANAGE_ROOM) {
                    const myInfo = (data.participants || []).find(p => p.user_id === CURRENT_USER_ID);
                    if (data.room_control.is_muted_by_host) {
                        if (isMicEnabled) {
                            isMicEnabled = false;
                            localStream?.getAudioTracks().forEach(t => t.enabled = false);
                            updateMediaButtonsUI();
                            showLiveToast('🔇 قام الأستاذ بكتم صوتك في هذه الجلسة.', '🔇');
                        }
                        wasMutedByHost = true;
                    } else if (wasMutedByHost || (isHandRaised && myInfo && !myInfo.is_hand_raised)) {
                        // الأستاذ سمح للطالب بالتحدث (Unmuted or hand lowered with permission) -> تفعيل المايك تلقائياً
                        wasMutedByHost = false;
                        isHandRaised = false;
                        isMicEnabled = true;
                        if (!localStream) {
                            initLocalMedia();
                        } else {
                            localStream.getAudioTracks().forEach(t => t.enabled = true);
                            ensureTracksInPeerConnections();
                            updateMediaButtonsUI();
                        }
                        showLiveToast('🎙️ سمح لك المعلم بالتحدث الآن ومشاركة صوتك في الحصة! تم تفعيل المايكروفون.', '🎙️');
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

    // 4. معالجة إشارات WebRTC مع دعم طابور المرشحين (ICE Candidate Queue) لتفادي فشل الاتصال عبر الإنترنت
    async function handleIncomingSignal(sig) {
        const fromPeer = sig.from_peer_id;
        const type = sig.signal_type;
        const payload = sig.payload;

        const pInfo = (window.cachedParticipants || []).find(p => p.peer_id === fromPeer);
        const remoteUserId = pInfo ? pInfo.user_id : 0;
        const remoteName = pInfo ? pInfo.name : 'مشارك';
        const isRemoteStaff = pInfo ? pInfo.is_staff : false;

        const pc = getOrCreatePeerConnection(fromPeer, remoteUserId, remoteName, isRemoteStaff);

        try {
            if (type === 'offer') {
                await pc.setRemoteDescription(new RTCSessionDescription(payload));
                // تفريغ المرشحين المؤجلين لهذا النظير إن وجدوا
                if (peerCandidateQueue[fromPeer] && peerCandidateQueue[fromPeer].length > 0) {
                    for (const cand of peerCandidateQueue[fromPeer]) {
                        try { await pc.addIceCandidate(new RTCIceCandidate(cand)); } catch (ce) { console.warn('Queued ICE error:', ce); }
                    }
                    peerCandidateQueue[fromPeer] = [];
                }
                const answer = await pc.createAnswer();
                await pc.setLocalDescription(answer);
                postSignal(fromPeer, 'answer', answer);
            } else if (type === 'answer') {
                await pc.setRemoteDescription(new RTCSessionDescription(payload));
                // تفريغ المرشحين المؤجلين لهذا النظير
                if (peerCandidateQueue[fromPeer] && peerCandidateQueue[fromPeer].length > 0) {
                    for (const cand of peerCandidateQueue[fromPeer]) {
                        try { await pc.addIceCandidate(new RTCIceCandidate(cand)); } catch (ce) { console.warn('Queued ICE error:', ce); }
                    }
                    peerCandidateQueue[fromPeer] = [];
                }
            } else if (type === 'candidate' && payload) {
                // إذا لم يتم تهيئة remoteDescription بعد، يتم تأجيل المرشح حتى لا يرفضه المتصفح
                if (!pc.remoteDescription || !pc.remoteDescription.type) {
                    if (!peerCandidateQueue[fromPeer]) peerCandidateQueue[fromPeer] = [];
                    peerCandidateQueue[fromPeer].push(payload);
                } else {
                    await pc.addIceCandidate(new RTCIceCandidate(payload));
                }
            }
        } catch (e) {
            console.error('Signaling processing error:', e);
        }
    }

    // ربط مبدئي مع الأقران المتصلين (Polite WebRTC Offer initiator)
    function initiatePeerOffers(participants) {
        participants.forEach(p => {
            if (p.peer_id === MY_PEER_ID) return;

            // نحدد بادئ الاتصال بشكل حتمي لتجنب تصادم الـ Offers ونعيد الاتصال إن كان منقطعاً
            if (MY_PEER_ID < p.peer_id && (!peerConnections[p.peer_id] || peerConnections[p.peer_id].connectionState === 'failed' || peerConnections[p.peer_id].connectionState === 'disconnected')) {
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
        window.cachedParticipants = participants || [];
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

                    ${CAN_MANAGE_ROOM && !isMe ? `
                        <div class="flex items-center flex-wrap gap-1 pt-1.5 border-t border-slate-900 text-[10px]">
                            ${!p.is_staff ? `
                                <button type="button" onclick="openRewardStarModal(${p.user_id}, '${addslashes(p.name)}')" class="px-2 py-1 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 rounded-lg font-bold flex items-center gap-1 shadow-xs" title="مكافأة بنجمة">
                                    ⭐ كافئ
                                </button>
                            ` : ''}
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
            broadcastP2P({ type: 'force_mute_user', target_user_id: userId });
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
            broadcastP2P({ type: 'allow_speak_user', target_user_id: userId });
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
        muteAllStudentsNow();
    }

    // مسح سجل المحادثة بالكامل للمعلم والإدارة
    async function confirmClearChat() {
        if (!CAN_MANAGE_ROOM) return;
        if (!confirm('هل أنت متأكد من مسح جميع رسائل الدردشة في هذه المجموعة نهائياً؟ لن يمكن استرجاعها.')) return;
        
        try {
            const res = await fetch(`/classroom/${ROOM_ID}/controls`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ action: 'clear_chat' })
            });
            const data = await res.json();
            if (data.success) {
                clearChatContainerUI();
                broadcastP2P({ type: 'chat_cleared' });
            }
        } catch(e) { console.error('Clear chat error:', e); }
    }

    function clearChatContainerUI() {
        const container = document.getElementById('liveChatMessagesContainer');
        if (!container) return;
        const welcomeBox = container.firstElementChild;
        container.innerHTML = '';
        if (welcomeBox) container.appendChild(welcomeBox);
        const clearedNotice = document.createElement('div');
        clearedNotice.className = 'text-center text-[11px] text-slate-500 py-2 font-medium';
        clearedNotice.textContent = 'تم مسح سجل المحادثة 🧹';
        container.appendChild(clearedNotice);
        lastMessageId = 0;
    }

    // كتم صوت جميع الطلاب فوراً
    async function muteAllStudentsNow() {
        if (!IS_TEACHER) return;
        if (!confirm('هل تريد كتم المايك لجميع الطلاب فوراً؟')) return;

        try {
            const res = await fetch(`/classroom/${ROOM_ID}/controls`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ action: 'mute_all' })
            });
            const data = await res.json();
            if (data.success) {
                broadcastP2P({ type: 'force_mute_all' });
                currentRoomPolicy = 'permission';
                updatePolicyUI('permission');
                alert('تم كتم صوت جميع الطلاب بنجاح 🔇');
            }
        } catch(e) { console.error(e); }
    }

    // تحديث ومزامنة الفصل والشاشة تلقائياً (Universal Refresh & Sync F5)
    // تحديث ومزامنة الفصل والشاشة والصوت تلقائياً (Universal Refresh & Sync)
    async function refreshClassroom() {
        if (CAN_MANAGE_ROOM) {
            const syncEveryone = confirm(
                "تحديث ومزامنة بيانات الفصل والشاشة:\n\n" +
                "• اضغط [موافق / OK]: لإعادة تحميل وتحديث شاشة وسبورة جميع الطلاب مع شاشتك إجبارياً.\n" +
                "• اضغط [إلغاء / Cancel]: لتنشيط الصوت ومزامنة السبورة لجهازك حالياً دون إعادة تحميل الصفحة."
            );
            if (syncEveryone) {
                broadcastP2P({ type: 'force_refresh' });
                try {
                    await fetch(`/classroom/${ROOM_ID}/controls`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({ action: 'refresh_all' })
                    });
                } catch(e) { console.error(e); }
                window.location.reload();
                return;
            }
        }

        // تنشيط فوري للصوت والسبورة وإعادة تفاوض WebRTC دون الخروج من الصفحة
        if (typeof unlockAudioSystem === 'function') await unlockAudioSystem();
        currentWbVersion = -1;
        broadcastP2P({ type: 'request_wb_state' });

        // إعادة تشغيل اتصالات الصوت بالأقران لضمان فك أي تعليق بشبكة الواي فاي
        Object.entries(peerConnections).forEach(([pid, pc]) => {
            try { pc.close(); } catch(e){}
            delete peerConnections[pid];
        });
        for (let pid in peerDataChannels) delete peerDataChannels[pid];

        try {
            await syncClassroom();
        } catch(e) {}
        showLiveToast('تم تنشيط الصوت ومزامنة السبورة بنجاح ✓', '🔄');
    }

    // مشاركة الشاشة (Screen Sharing)
    let isSharingScreen = false;
    let screenStream = null;

    async function toggleScreenShare() {
        if (!IS_TEACHER) return;
        if (isSharingScreen) {
            stopScreenShare();
        } else {
            await startScreenShare();
        }
    }

    async function startScreenShare() {
        try {
            screenStream = await navigator.mediaDevices.getDisplayMedia({
                video: { cursor: 'always' },
                audio: false
            });

            const screenTrack = screenStream.getVideoTracks()[0];
            
            // استبدال مسار الفيديو في جميع اتصالات الأقران
            Object.values(peerConnections).forEach(pc => {
                const sender = pc.getSenders().find(s => s.track && s.track.kind === 'video');
                if (sender) {
                    sender.replaceTrack(screenTrack).catch(console.error);
                }
            });

            // إظهار الشاشة محلياً في بطاقة المعلم
            const myVideo = document.getElementById('mySelfVideo');
            if (myVideo) {
                myVideo.srcObject = screenStream;
                myVideo.classList.remove('hidden');
                document.getElementById('mySelfAvatar')?.classList.add('hidden');
            }

            isSharingScreen = true;
            const btn = document.getElementById('btnScreenShare');
            if (btn) {
                btn.classList.add('bg-rose-600', 'text-white');
                btn.classList.remove('bg-indigo-950/80', 'text-indigo-200');
            }

            screenTrack.onended = () => {
                stopScreenShare();
            };
        } catch (err) {
            console.error('Screen share error:', err);
            if (err.name !== 'NotAllowedError') {
                alert('تعذر مشاركة الشاشة: ' + (err.message || 'خطأ'));
            }
        }
    }

    function stopScreenShare() {
        if (screenStream) {
            screenStream.getTracks().forEach(t => t.stop());
            screenStream = null;
        }
        isSharingScreen = false;

        // استعادة مسار الكاميرا الأصلية
        const camTrack = localStream ? localStream.getVideoTracks()[0] : null;
        Object.values(peerConnections).forEach(pc => {
            const sender = pc.getSenders().find(s => s.track && s.track.kind === 'video');
            if (sender && camTrack) {
                sender.replaceTrack(camTrack).catch(console.error);
            }
        });

        const myVideo = document.getElementById('mySelfVideo');
        if (myVideo) {
            if (localStream && isCamEnabled) {
                myVideo.srcObject = localStream;
            } else {
                myVideo.classList.add('hidden');
                document.getElementById('mySelfAvatar')?.classList.remove('hidden');
            }
        }

        const btn = document.getElementById('btnScreenShare');
        if (btn) {
            btn.classList.remove('bg-rose-600', 'text-white');
            btn.classList.add('bg-indigo-950/80', 'text-indigo-200');
        }
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
        if (!msg) return;
        const container = document.getElementById('liveChatMessagesContainer');
        if (!container) return;

        // التحقق من عدم التكرار
        if (document.getElementById('chatMsg_' + msg.id)) return;

        if (msg.type === 'system') {
            const div = document.createElement('div');
            div.id = 'chatMsg_' + msg.id;
            div.className = "p-2.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-200 text-xs text-center font-bold my-1 flex items-center justify-center gap-2 shadow-xs";
            div.innerHTML = `<span>📢</span> <span>${msg.content}</span> <span class="text-[10px] text-amber-400/60 font-mono">(${msg.time || ''})</span>`;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
            return;
        }

        const isMine = msg.user_id === CURRENT_USER_ID;
        const div = document.createElement('div');
        div.id = 'chatMsg_' + msg.id;
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

        container.appendChild(div);
        container.scrollTop = container.scrollHeight;

        // إشعار ضوئي إذا كانت اللوحة الجانبية مغلقة (للكمبيوتر وللهاتف)
        const panel = document.getElementById('liveInteractivePanel');
        if (panel && (panel.classList.contains('hidden') || panel.classList.contains('lg:hidden'))) {
            document.getElementById('unreadMessagesDot')?.classList.remove('hidden');
            document.getElementById('mobileUnreadMessagesDot')?.classList.remove('hidden');
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

    // 11. التبديل والتحكم باللوحة الجانبية والتنقل في شاشات الجوال
    function switchMobileView(mode) {
        const wbSec = document.getElementById('whiteboardSection');
        const panel = document.getElementById('liveInteractivePanel');
        const btnWb = document.getElementById('btnMobileWhiteboard');
        const btnPan = document.getElementById('btnMobilePanel');

        if (mode === 'whiteboard') {
            wbSec?.classList.remove('hidden');
            wbSec?.classList.add('flex');
            panel?.classList.add('hidden');
            panel?.classList.remove('flex');

            btnWb?.classList.add('bg-indigo-600', 'text-white', 'shadow');
            btnWb?.classList.remove('text-slate-400');
            btnPan?.classList.remove('bg-indigo-600', 'text-white', 'shadow');
            btnPan?.classList.add('text-slate-400');

            setTimeout(() => {
                ensureCanvasLayerDimensions();
            }, 80);
        } else {
            wbSec?.classList.add('hidden');
            wbSec?.classList.remove('flex');
            panel?.classList.remove('hidden');
            panel?.classList.add('flex');

            btnPan?.classList.add('bg-indigo-600', 'text-white', 'shadow');
            btnPan?.classList.remove('text-slate-400');
            btnWb?.classList.remove('bg-indigo-600', 'text-white', 'shadow');
            btnWb?.classList.add('text-slate-400');

            document.getElementById('mobileUnreadMessagesDot')?.classList.add('hidden');
        }
    }

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

    // 12. العداد التنازلي ونظام النجوم التقييمية والشرائح
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

    // دوال مكافأة النجمة المخصصة للطالب مع الترحيل لرصيده
    function openRewardStarModal(targetStudentId = null, targetStudentName = null) {
        const select = document.getElementById('rewardStudentSelect');
        if (select && targetStudentId) {
            let found = false;
            for (let i = 0; i < select.options.length; i++) {
                if (String(select.options[i].value) === String(targetStudentId)) {
                    select.selectedIndex = i;
                    found = true;
                    break;
                }
            }
            if (!found) {
                const opt = document.createElement('option');
                opt.value = targetStudentId;
                opt.textContent = (targetStudentName || 'الطالب') + ' (حاضر الآن ⭐)';
                select.insertBefore(opt, select.firstChild);
                select.selectedIndex = 0;
            }
        }
        document.getElementById('rewardStarModal')?.classList.remove('hidden');
    }

    function closeRewardStarModal() {
        document.getElementById('rewardStarModal')?.classList.add('hidden');
    }

    async function submitStudentStarReward() {
        const select = document.getElementById('rewardStudentSelect');
        const amountSelect = document.getElementById('rewardStarsAmount');
        const reasonSelect = document.getElementById('rewardStarsReason');
        const btn = document.getElementById('btnSubmitRewardStar');

        if (!select) return;
        const targetUserId = select.value;
        const starsCount = amountSelect ? parseInt(amountSelect.value, 10) : 1;
        const reason = reasonSelect ? reasonSelect.value : 'مشاركة ممتازة في الحصة';

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span>جاري قيد النجمة... ⏳</span>';
        }

        try {
            const res = await fetch(`/classroom/${ROOM_ID}/reward-star`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    user_id: targetUserId,
                    stars_count: starsCount,
                    reason: reason
                })
            });

            const data = await res.json();
            if (data.success) {
                closeRewardStarModal();
                playStarSound();

                stars = (stars || 0) + starsCount;
                const counter = document.getElementById('starsCount');
                if (counter) {
                    counter.textContent = stars;
                    counter.classList.add('star-pop');
                    setTimeout(() => counter.classList.remove('star-pop'), 400);
                }

                if (document.getElementById('modalStarsInput')) {
                    document.getElementById('modalStarsInput').value = stars;
                }

                // البث الفوري عبر P2P
                broadcastP2P({
                    type: 'star_rewarded',
                    user_id: targetUserId,
                    student_name: data.student_name || 'الطالب',
                    stars_count: starsCount,
                    new_balance: data.new_balance,
                    reason: reason
                });

                // مزامنة فورية لتحديث رسائل المحادثة
                syncClassroom();

                alert(`⭐ تم منح ${starsCount} نجمة بنجاح وإضافتها لرصيد تقييمات الطالب!`);
            } else {
                alert('عذراً: ' + (data.message || 'تعذر إضافة النجمة'));
            }
        } catch (e) {
            console.error('Star reward error:', e);
            alert('حدث خطأ في الاتصال أثناء اعتماد النجمة.');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<span>⭐ اعتماد وإضافة لرصيد الطالب</span>';
            }
        }
    }

    function showStarCelebration(studentName, starsCount, newBalance, reason) {
        playStarSound();
        const overlay = document.getElementById('starCelebrationOverlay');
        const title = document.getElementById('starCelebrationTitle');
        const desc = document.getElementById('starCelebrationDesc');
        const balance = document.getElementById('starCelebrationBalance');

        if (title) title.textContent = `أحسنت يا ${studentName}! ⭐`;
        if (desc) desc.textContent = `منحك المعلم ${starsCount} نجمة تميز تقديراً لـ (${reason})!`;
        if (balance && newBalance !== undefined) balance.textContent = newBalance;

        if (overlay) {
            overlay.classList.remove('hidden');
            setTimeout(() => {
                closeStarCelebration();
            }, 6000);
        }
    }

    function closeStarCelebration() {
        document.getElementById('starCelebrationOverlay')?.classList.add('hidden');
    }

    // نغمة احتفالية لحصول الطالب على النجمة (Web Audio API Synthesizer)
    function playStarSound() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            const notes = [523.25, 659.25, 783.99, 1046.50];
            notes.forEach((freq, idx) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                const startTime = ctx.currentTime + (idx * 0.1);
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(freq, startTime);
                gain.gain.setValueAtTime(0.3, startTime);
                gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.4);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(startTime);
                osc.stop(startTime + 0.4);
            });
        } catch(e){}
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
        document.getElementById('reportModal')?.classList.remove('hidden');
    }
    function closeReportModal() {
        document.getElementById('reportModal')?.classList.add('hidden');
    }

    // طي وتوسيع الشريط الجانبي الأيمن لتوفير مساحة 100% للسبورة والشرح
    function toggleSidebarCollapse() {
        const aside = document.getElementById('liveInteractivePanel');
        const restoreBtn = document.getElementById('btnRestoreSidebar');
        const toggleIcon = document.getElementById('sidebarToggleIcon');
        if (!aside) return;

        const isCollapsed = aside.classList.contains('lg:hidden') || aside.classList.contains('hidden');
        if (isCollapsed) {
            aside.classList.remove('lg:hidden', 'hidden');
            if (restoreBtn) restoreBtn.classList.add('hidden');
            if (toggleIcon) toggleIcon.textContent = '◀';
        } else {
            aside.classList.add('lg:hidden', 'hidden');
            if (restoreBtn) restoreBtn.classList.remove('hidden');
            if (toggleIcon) toggleIcon.textContent = '▶';
        }

        // إعادة ضبط أبعاد الكانفاس فوراً لتمديد السبورة على كامل العرض الجديد
        setTimeout(() => {
            ensureCanvasLayerDimensions();
        }, 180);
    }

    // دوال نافذة مكتبة كتب ومذكرات الدورة المتكاملة (PDF Books Library)
    function openBooksLibraryModal() {
        document.getElementById('wbBooksLibraryModal')?.classList.remove('hidden');
    }
    function closeBooksLibraryModal() {
        document.getElementById('wbBooksLibraryModal')?.classList.add('hidden');
    }
    function switchBookLibTab(tab) {
        const tabs = ['curriculum', 'wall', 'upload'];
        tabs.forEach(t => {
            const pane = document.getElementById('bookLibTab_' + t);
            const btn = document.getElementById('btnBookLibTab_' + t);
            if (t === tab) {
                pane?.classList.remove('hidden');
                btn?.classList.add('bg-indigo-600', 'text-white', 'shadow');
                btn?.classList.remove('text-slate-400');
            } else {
                pane?.classList.add('hidden');
                btn?.classList.remove('bg-indigo-600', 'text-white', 'shadow');
                btn?.classList.add('text-slate-400');
            }
        });
    }

    // التنقل والتمرير المستمر بأسهم لوحة المفاتيح: التحرك فوق وتحت والتنقل التلقائي بين الصفحات عند نهايتها
    let isTransitioningWbPage = false;
    document.addEventListener('keydown', async (e) => {
        if (e.target.closest('input, textarea, select')) return;
        if (wbCurrentBg && wbCurrentBg.type === 'pdf') {
            const container = document.getElementById('whiteboardContainer');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!container || isTransitioningWbPage) return;

                const maxScroll = Math.max(0, container.scrollHeight - container.clientHeight);
                const isAtBottom = maxScroll <= 15 || container.scrollTop >= maxScroll - 25;

                if (isAtBottom) {
                    // وصل لنهاية الصفحة الحالية -> استمرار النزول بالانتقال للصفحة التالية تلقائياً!
                    if (wbCurrentPage < wbTotalPages) {
                        isTransitioningWbPage = true;
                        try {
                            container.scrollTo({ top: container.scrollTop, behavior: 'instant' });
                            await changeWbPageTo(wbCurrentPage + 1, 'top');
                            if (container) {
                                container.scrollTo({ top: 0, behavior: 'instant' });
                                container.scrollBy({ top: 70, behavior: 'smooth' });
                                syncScrollToPeersThrottled();
                            }
                        } catch (err) {
                            console.error('Error transitioning to next PDF page:', err);
                        } finally {
                            setTimeout(() => { isTransitioningWbPage = false; }, 120);
                        }
                    }
                } else {
                    // ما زال داخل الصفحة -> استمرار التمرير لأسفل بسلاسة
                    container.scrollBy({ top: 100, behavior: 'smooth' });
                    syncScrollToPeersThrottled();
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (!container || isTransitioningWbPage) return;

                const isAtTop = container.scrollTop <= 25;

                if (isAtTop) {
                    // وصل لأعلى الصفحة الحالية -> استمرار الصعود بالانتقال للصفحة السابقة والبدء من أسفلها!
                    if (wbCurrentPage > 1) {
                        isTransitioningWbPage = true;
                        try {
                            container.scrollTo({ top: 0, behavior: 'instant' });
                            await changeWbPageTo(wbCurrentPage - 1, 'bottom');
                            if (container) {
                                requestAnimationFrame(() => {
                                    const bottomPos = Math.max(0, container.scrollHeight - container.clientHeight);
                                    container.scrollTo({ top: bottomPos, behavior: 'instant' });
                                    container.scrollBy({ top: -70, behavior: 'smooth' });
                                    syncScrollToPeersThrottled();
                                });
                            }
                        } catch (err) {
                            console.error('Error transitioning to previous PDF page:', err);
                        } finally {
                            setTimeout(() => { isTransitioningWbPage = false; }, 120);
                        }
                    }
                } else {
                    // ما زال داخل الصفحة -> استمرار التمرير لأعلى بسلاسة
                    container.scrollBy({ top: -100, behavior: 'smooth' });
                    syncScrollToPeersThrottled();
                }
            } else if (e.key === 'PageDown' || e.key === 'ArrowLeft') {
                e.preventDefault();
                changeWbPage(1);
            } else if (e.key === 'PageUp' || e.key === 'ArrowRight') {
                e.preventDefault();
                changeWbPage(-1);
            } else if (e.key === 'Home') {
                e.preventDefault();
                changeWbPageTo(1, 'top');
            } else if (e.key === 'End') {
                e.preventDefault();
                changeWbPageTo(wbTotalPages, 'top');
            }
        }
    });

    // التمرير التلقائي بعجلة الفأرة (Mouse Wheel) عند الوصول لأعلى أو أسفل الصفحة
    let wheelTransitionTimeout = null;
    window.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('whiteboardContainer');
        if (container) {
            container.addEventListener('wheel', (e) => {
                if (!wbCurrentBg || wbCurrentBg.type !== 'pdf' || isTransitioningWbPage) return;
                const maxScroll = Math.max(0, container.scrollHeight - container.clientHeight);
                if (e.deltaY > 30 && (maxScroll <= 15 || container.scrollTop >= maxScroll - 25)) {
                    if (wbCurrentPage < wbTotalPages) {
                        clearTimeout(wheelTransitionTimeout);
                        wheelTransitionTimeout = setTimeout(() => {
                            changeWbPageTo(wbCurrentPage + 1, 'top');
                        }, 80);
                    }
                } else if (e.deltaY < -30 && container.scrollTop <= 25) {
                    if (wbCurrentPage > 1) {
                        clearTimeout(wheelTransitionTimeout);
                        wheelTransitionTimeout = setTimeout(() => {
                            changeWbPageTo(wbCurrentPage - 1, 'bottom');
                        }, 80);
                    }
                }
            }, { passive: true });
        }
    });

    // ==========================================
    // محرك مساحة الشرح والسبورة التفاعلية (Zoom Live Engine)
    // ==========================================
    let currentWbVersion = -1;
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
    let wbPdfScale = 1.8;
    const wbPdfPageStrokesCache = {}; // حفظ شروحات وملاحظات كل صفحة في الكتاب
    function getPdfCacheKey(url, pageNum) {
        return (url || 'current') + '_p_' + pageNum;
    }
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
            if (!e.target.closest('#wbLeftToolbar') && !e.target.closest('.wb-flyout-menu') && !e.target.closest('#btnBooksDropdown')) {
                closeAllWbFlyouts();
            }
        });

        // حلقة التلاشي للقلم السحري (Magic vanishing ink)
        requestAnimationFrame(magicStrokesAnimationLoop);

        // تهيئة مستمعات مشغل الفيديو التعليمي
        initHtml5VideoListeners();

        // مزامنة التمرير في السبورة وحاوية الكتاب للطلاب
        const wbContainer = document.getElementById('whiteboardContainer');
        if (wbContainer) {
            wbContainer.addEventListener('scroll', syncScrollToPeersThrottled, { passive: true });
        }

        // تحديث المظهر الافتراضي للأدوات
        selectWbTool('pen');
        setWbColor('#ef4444');
        setWbSize(4);
    }

    function getWbContentBox() {
        const canvas = document.getElementById('whiteboardDrawCanvas');
        const container = document.getElementById('whiteboardContainer');
        if (!canvas || !container) return null;

        const rect = canvas.getBoundingClientRect();
        const contRect = container.getBoundingClientRect();
        const cw = canvas.width || 1600;
        const ch = canvas.height || 1000;
        const rw = rect.width;
        const rh = rect.height;
        if (rw === 0 || rh === 0) return null;

        const canvasAspect = cw / ch;
        const elementAspect = rw / rh;
        let renderedWidth, renderedHeight, offsetX, offsetY;

        if (elementAspect > canvasAspect) {
            renderedHeight = rh;
            renderedWidth = rh * canvasAspect;
            offsetX = (rw - renderedWidth) / 2;
            offsetY = 0;
        } else {
            renderedWidth = rw;
            renderedHeight = rw / canvasAspect;
            offsetX = 0;
            offsetY = (rh - renderedHeight) / 2;
        }

        return {
            cw, ch, rw, rh,
            rect, contRect,
            renderedWidth, renderedHeight,
            offsetX, offsetY,
            contentLeftInContainer: (rect.left - contRect.left) + offsetX,
            contentTopInContainer: (rect.top - contRect.top) + offsetY,
        };
    }

    function getWbCoords(e) {
        const canvas = document.getElementById('whiteboardDrawCanvas');
        if (!canvas) return { x: 0, y: 0, normX: 0, normY: 0, clientX: 0, clientY: 0 };
        const rect = canvas.getBoundingClientRect();
        const clientX = (e.touches && e.touches[0]) ? e.touches[0].clientX : (e.clientX ?? 0);
        const clientY = (e.touches && e.touches[0]) ? e.touches[0].clientY : (e.clientY ?? 0);

        const cw = canvas.width;
        const ch = canvas.height;
        const rw = rect.width;
        const rh = rect.height;

        if (rw === 0 || rh === 0 || cw === 0 || ch === 0) {
            return { x: 0, y: 0, normX: 0, normY: 0, clientX, clientY };
        }

        const canvasAspect = cw / ch;
        const elementAspect = rw / rh;

        let renderedWidth, renderedHeight, offsetX, offsetY;

        if (elementAspect > canvasAspect) {
            renderedHeight = rh;
            renderedWidth = rh * canvasAspect;
            offsetX = (rw - renderedWidth) / 2;
            offsetY = 0;
        } else {
            renderedWidth = rw;
            renderedHeight = rw / canvasAspect;
            offsetX = 0;
            offsetY = (rh - renderedHeight) / 2;
        }

        const clickXInElement = clientX - rect.left;
        const clickYInElement = clientY - rect.top;

        const contentX = clickXInElement - offsetX;
        const contentY = clickYInElement - offsetY;

        const x = contentX * (cw / renderedWidth);
        const y = contentY * (ch / renderedHeight);

        const normX = Math.max(0, Math.min(1, contentX / renderedWidth));
        const normY = Math.max(0, Math.min(1, contentY / renderedHeight));

        return { x, y, normX, normY, clientX, clientY };
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
            const cursorClasses = ['cursor-default', 'cursor-pen', 'cursor-brush', 'cursor-highlighter', 'cursor-magic', 'cursor-eraser', 'cursor-laser', 'cursor-text', 'cursor-pointer', 'cursor-crosshair'];
            drawCanvas.classList.remove(...cursorClasses);
            if (tool === 'select') {
                drawCanvas.style.pointerEvents = 'none';
                drawCanvas.classList.add('cursor-default');
            } else {
                drawCanvas.style.pointerEvents = 'auto';
                if (tool === 'pen') drawCanvas.classList.add('cursor-pen');
                else if (tool === 'brush') drawCanvas.classList.add('cursor-brush');
                else if (tool === 'highlighter') drawCanvas.classList.add('cursor-highlighter');
                else if (tool === 'magic') drawCanvas.classList.add('cursor-magic');
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
            diamond: '🔶',
            heart: '❤️',
            star: '⭐',
            line: '➖',
            arrow: '➡️',
            double_arrow: '↔️',
            checkmark: '✔️',
            cross: '❌',
            bubble: '💬'
        };
        const iconEl = document.getElementById('activeShapeIcon');
        if (iconEl) iconEl.textContent = iconMap[shape] || '🔲';
        closeAllWbFlyouts();
    }

    function insertWbShapeAtCenter(shape = null) {
        if (shape) {
            selectWbShape(shape);
        }
        const canvas = document.getElementById('whiteboardDrawCanvas');
        const cw = canvas ? canvas.width : 1600;
        const ch = canvas ? canvas.height : 1000;
        const shapeToInsert = shape || wbActiveShape || 'rect';

        const stroke = {
            tool: 'shapes',
            shape: shapeToInsert,
            filled: wbShapeFilled,
            color: wbCurrentColor,
            size: wbCurrentSize,
            createdAt: Date.now()
        };

        if (shapeToInsert === 'line' || shapeToInsert === 'arrow' || shapeToInsert === 'double_arrow') {
            stroke.x1 = (cw / 2) - 100;
            stroke.y1 = ch / 2;
            stroke.x2 = (cw / 2) + 100;
            stroke.y2 = ch / 2;
        } else if (shapeToInsert === 'checkmark' || shapeToInsert === 'cross') {
            const sz = 60;
            stroke.x1 = (cw / 2) - sz;
            stroke.y1 = (ch / 2) - sz;
            stroke.x2 = (cw / 2) + sz;
            stroke.y2 = (ch / 2) + sz;
        } else {
            const w = 180;
            const h = 130;
            stroke.x1 = (cw / 2) - (w / 2);
            stroke.y1 = (ch / 2) - (h / 2);
            stroke.x2 = (cw / 2) + (w / 2);
            stroke.y2 = (ch / 2) + (h / 2);
        }

        wbStrokes.push(stroke);
        wbUndoStack.push(JSON.parse(JSON.stringify(wbStrokes)));
        wbRedoStack = [];
        redrawWhiteboard();
        commitWhiteboardChange();
        broadcastP2P({ type: 'wb_stroke', stroke: stroke });
        closeAllWbFlyouts();
    }

    function setWbColor(color) {
        wbCurrentColor = color;
        const preview = document.getElementById('activeColorPreview');
        if (preview) preview.style.backgroundColor = color;
        const swatch = document.getElementById('colorIndicatorSwatch');
        if (swatch) swatch.style.backgroundColor = color;
        const hex = document.getElementById('activeColorHex');
        if (hex) hex.textContent = color.toUpperCase();
        const customInput = document.getElementById('wbCustomColorInput');
        if (customInput) customInput.value = color;

        // تمييز اللون المختار في لوحة الألوان
        document.querySelectorAll('.wb-color-swatch').forEach(btn => {
            if (btn.getAttribute('data-color')?.toLowerCase() === color.toLowerCase()) {
                btn.classList.add('ring-2', 'ring-white', 'scale-125');
            } else {
                btn.classList.remove('ring-2', 'ring-white', 'scale-125');
            }
        });

        // إذا كان المعلم على الممحاة أو الماوس، نقله تلقائياً للقلم للكتابة باللون الجديد فوراً
        if (wbCurrentTool === 'eraser' || wbCurrentTool === 'select') {
            selectWbTool('pen');
        }

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
            openWbTextInput(coords.x, coords.y, coords.clientX, coords.clientY);
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

        if (wbCurrentTool === 'shapes') {
            const dx = Math.abs(wbCurrentStroke.x2 - wbCurrentStroke.x1);
            const dy = Math.abs(wbCurrentStroke.y2 - wbCurrentStroke.y1);
            // إذا نقر المعلم نقرة واحدة على السبورة دون سحب، نقوم بتنزيل الشكل مباشرة بحجم مناسب في موضع النقرة
            if (dx < 10 && dy < 10) {
                const clickX = wbCurrentStroke.x1;
                const clickY = wbCurrentStroke.y1;
                const shape = wbCurrentStroke.shape;
                if (shape === 'line' || shape === 'arrow' || shape === 'double_arrow') {
                    wbCurrentStroke.x1 = clickX - 70;
                    wbCurrentStroke.y1 = clickY;
                    wbCurrentStroke.x2 = clickX + 70;
                    wbCurrentStroke.y2 = clickY;
                } else if (shape === 'checkmark' || shape === 'cross') {
                    wbCurrentStroke.x1 = clickX - 35;
                    wbCurrentStroke.y1 = clickY - 35;
                    wbCurrentStroke.x2 = clickX + 35;
                    wbCurrentStroke.y2 = clickY + 35;
                } else {
                    const defW = 140;
                    const defH = 100;
                    wbCurrentStroke.x1 = clickX - (defW / 2);
                    wbCurrentStroke.y1 = clickY - (defH / 2);
                    wbCurrentStroke.x2 = clickX + (defW / 2);
                    wbCurrentStroke.y2 = clickY + (defH / 2);
                }
            }
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
            } else if (stroke.shape === 'diamond') {
                const cx = rx + rw / 2;
                const cy = ry + rh / 2;
                ctx.beginPath();
                ctx.moveTo(cx, ry);
                ctx.lineTo(rx + rw, cy);
                ctx.lineTo(cx, ry + rh);
                ctx.lineTo(rx, cy);
                ctx.closePath();
                if (stroke.filled) {
                    ctx.fillStyle = stroke.color + '45';
                    ctx.fill();
                }
                ctx.stroke();
            } else if (stroke.shape === 'heart') {
                drawHeart(ctx, x1, y1, x2, y2, stroke.filled);
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

    function drawHeart(ctx, x1, y1, x2, y2, filled = false) {
        const rx = Math.min(x1, x2), ry = Math.min(y1, y2);
        const rw = Math.abs(x2 - x1), rh = Math.abs(y2 - y1);
        ctx.beginPath();
        const topCurveHeight = rh * 0.3;
        ctx.moveTo(rx + rw / 2, ry + topCurveHeight);
        ctx.bezierCurveTo(rx + rw / 2, ry, rx, ry, rx, ry + topCurveHeight);
        ctx.bezierCurveTo(rx, ry + (rh + topCurveHeight) / 2, rx + rw / 2, ry + (rh + topCurveHeight) / 2, rx + rw / 2, ry + rh);
        ctx.bezierCurveTo(rx + rw / 2, ry + (rh + topCurveHeight) / 2, rx + rw, ry + (rh + topCurveHeight) / 2, rx + rw, ry + topCurveHeight);
        ctx.bezierCurveTo(rx + rw, ry, rx + rw / 2, ry, rx + rw / 2, ry + topCurveHeight);
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
    }

    function renderRemoteLaser(state) {
        const laserEl = document.getElementById('liveLaserPointer');
        if (!laserEl) return;
        if (state && state.active) {
            laserEl.style.left = (state.x * 100) + '%';
            laserEl.style.top = (state.y * 100) + '%';
            laserEl.classList.remove('hidden');
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

    function resolveClassroomBrowserUrl(rawUrl) {
        if (!rawUrl) return '';
        let url = rawUrl.trim();

        // 1. إذا كان استعلام بحث نصي (يحتوي مسافات أو لا يحتوي على نقطة)
        if (url.includes(' ') || (!url.includes('.') && !url.startsWith('http'))) {
            return `https://www.google.com/search?igu=1&q=${encodeURIComponent(url)}`;
        }

        if (!url.startsWith('http://') && !url.startsWith('https://')) {
            url = 'https://' + url;
        }

        // 2. معالجة موقع جوجل لتمكين العرض داخل الإطار عبر معامل igu=1 الرسمي
        try {
            const parsed = new URL(url);
            if (parsed.hostname.includes('google.') && !parsed.hostname.includes('docs.google.')) {
                if (parsed.pathname === '/' || parsed.pathname === '' || parsed.pathname.includes('/webhp')) {
                    return 'https://www.google.com/webhp?igu=1';
                }
                if (parsed.pathname.includes('/search')) {
                    parsed.searchParams.set('igu', '1');
                    return parsed.toString();
                }
            }
        } catch(e){}

        // 3. تحويل ألعاب ومسابقات Wordwall إلى صيغة التضمين الرسمية الخفيفة
        const wordwallMatch = url.match(/wordwall\.net\/(?:[a-z]{2}-[a-z]{2}\/)?(?:resource|play|embed)\/([0-9]+)/i);
        if (wordwallMatch) {
            return `https://wordwall.net/embed/${wordwallMatch[1]}`;
        }

        // 4. روابط التضمين المباشرة المدعومة للألعاب والمواقع التعليمية
        if (url.includes('wordwall.net/embed/') || url.includes('youtube.com/embed/') || url.includes('geogebra.org/calculator') || url.includes('quizizz.com/join') || url.includes('kahoot.it')) {
            return url;
        }

        // 5. توجيه المواقع الخارجية عبر البروكسي الداخلي لإزالة قيود X-Frame-Options
        return `/classroom/proxy-browse?url=${encodeURIComponent(url)}`;
    }

    function navigateBrowserBack() {
        const iframe = document.getElementById('whiteboardBrowserIframe');
        try {
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.history.back();
            }
        } catch (e) {
            console.warn('Browser back error:', e);
        }
    }

    function navigateBrowserForward() {
        const iframe = document.getElementById('whiteboardBrowserIframe');
        try {
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.history.forward();
            }
        } catch (e) {
            console.warn('Browser forward error:', e);
        }
    }

    function navigateBrowserToUrl() {
        const input = document.getElementById('wbBrowserCurrentUrl');
        if (!input) return;
        const rawVal = input.value.trim();
        if (!rawVal) return;
        const resolved = resolveClassroomBrowserUrl(rawVal);
        openBrowserInWorkspace(resolved, `تصفح: ${rawVal}`);
    }

    function openBrowserExternalTab() {
        const input = document.getElementById('wbBrowserCurrentUrl');
        let target = input ? input.value.trim() : '';
        if (!target && wbCurrentBg && wbCurrentBg.url) {
            target = wbCurrentBg.url;
        }
        if (target.includes('/classroom/proxy-browse?url=')) {
            try {
                const parsed = new URL(target, window.location.origin);
                target = parsed.searchParams.get('url') || target;
            } catch (e) {}
        }
        if (target) {
            window.open(target, '_blank', 'noopener,noreferrer');
        }
    }

    function navigateBrowserToGoogleHome() {
        const googleHome = 'https://www.google.com/webhp?igu=1';
        openBrowserInWorkspace(googleHome, 'محرك بحث جوجل');
    }

    function executeBrowserSearch() {
        const input = document.getElementById('wbGoogleSearchInput');
        const query = input ? input.value.trim() : '';
        if (!query) return;
        const googleUrl = `https://www.google.com/search?igu=1&q=${encodeURIComponent(query)}`;
        openBrowserInWorkspace(googleUrl, `بحث جوجل: ${query}`);
        closeWbMediaModals();
    }

    function executeDirectUrlOpen() {
        const input = document.getElementById('wbDirectUrlInput');
        let url = input ? input.value.trim() : '';
        if (!url) return;
        const resolved = resolveClassroomBrowserUrl(url);
        openBrowserInWorkspace(resolved, `موقع: ${url}`);
        closeWbMediaModals();
    }

    function openPresetGame(url, title) {
        const resolved = resolveClassroomBrowserUrl(url);
        openBrowserInWorkspace(resolved, title);
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
        if (!container || !drawCanvas) return;

        if (wbCurrentBg.type === 'pdf' && wbCurrentBg.url) {
            renderPdfPage(wbCurrentBg.url, wbCurrentPage);
        } else if (wbCurrentBg.type === 'image' && wbCurrentBg.url) {
            renderImageBackground(wbCurrentBg.url);
        } else if (wbCurrentBg.type === 'browser' || wbCurrentBg.type === 'video') {
            const w = container.clientWidth || 1600;
            const h = container.clientHeight || 1000;
            drawCanvas.width = w;
            drawCanvas.height = h;
            if (bgCanvas) { bgCanvas.width = w; bgCanvas.height = h; }
            redrawWhiteboard();
        } else {
            const w = container.clientWidth || 1600;
            const h = container.clientHeight || 1000;
            drawCanvas.width = w;
            drawCanvas.height = h;
            if (bgCanvas) { bgCanvas.width = w; bgCanvas.height = h; }
            renderBlankBackground(wbCurrentBg.type);
        }
    }

    function renderBrowserBackground(url, title) {
        hideMediaLayers();
        const browserWrapper = document.getElementById('whiteboardBrowserWrapper');
        const browserIframe = document.getElementById('whiteboardBrowserIframe');
        const currentUrlInput = document.getElementById('wbBrowserCurrentUrl');
        if (browserWrapper && browserIframe) {
            browserWrapper.classList.remove('hidden');
            if (currentUrlInput) {
                let displayUrl = url;
                if (url.includes('/classroom/proxy-browse?url=')) {
                    try {
                        const parsed = new URL(url, window.location.origin);
                        displayUrl = decodeURIComponent(parsed.searchParams.get('url') || url);
                    } catch (e) {}
                }
                currentUrlInput.value = displayUrl;
            }
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
        if (!CAN_MANAGE_ROOM) return;
        closeAllWbFlyouts();
        const modeIcon = document.getElementById('wbModeIcon');
        const modeText = document.getElementById('wbModeText');
        if (modeIcon) modeIcon.textContent = '🌐';
        if (modeText) modeText.textContent = title;
        document.getElementById('wbPageNavControls')?.classList.add('hidden');
        document.getElementById('wbPdfSideNavigator')?.classList.add('hidden');

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
        if (!CAN_MANAGE_ROOM) return;
        closeAllWbFlyouts();
        const modeIcon = document.getElementById('wbModeIcon');
        const modeText = document.getElementById('wbModeText');
        if (modeIcon) modeIcon.textContent = '🎬';
        if (modeText) modeText.textContent = title;
        document.getElementById('wbPageNavControls')?.classList.add('hidden');
        document.getElementById('wbPdfSideNavigator')?.classList.add('hidden');

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
        selectWbTool('pen');
        const drawCanvas = document.getElementById('whiteboardDrawCanvas');
        if (drawCanvas) {
            drawCanvas.style.pointerEvents = 'auto';
        }
    }

    async function openResourceInWorkspace(url, title, type, pageNum = 1) {
        const canManage = (typeof CAN_MANAGE_ROOM !== 'undefined' && CAN_MANAGE_ROOM) || (typeof IS_TEACHER !== 'undefined' && IS_TEACHER);
        closeAllWbFlyouts();
        hideMediaLayers();

        // 1. حفظ شروحات وملاحظات الصفحة الحالية في الكاش إن وجد كتاب PDF مفتوح
        if (wbCurrentBg && wbCurrentBg.type === 'pdf' && wbCurrentBg.url && wbCurrentPage) {
            wbPdfPageStrokesCache[getPdfCacheKey(wbCurrentBg.url, wbCurrentPage)] = JSON.parse(JSON.stringify(wbStrokes));
        }

        const modeIcon = document.getElementById('wbModeIcon');
        const modeText = document.getElementById('wbModeText');
        if (modeIcon) modeIcon.textContent = (type === 'pdf') ? '📖' : '🖼️';
        if (modeText) modeText.textContent = title;

        wbCurrentBg = { type: type, url: url, title: title, page_num: pageNum };

        // 2. تحميل شروحات وملاحظات الصفحة الجديدة إذا وُجدت
        if (type === 'pdf') {
            const cacheKey = getPdfCacheKey(url, pageNum);
            wbStrokes = wbPdfPageStrokesCache[cacheKey] ? JSON.parse(JSON.stringify(wbPdfPageStrokesCache[cacheKey])) : [];
        } else {
            wbStrokes = [];
        }
        wbUndoStack = [];
        wbRedoStack = [];
        redrawWhiteboard();

        if (canManage) {
            broadcastP2P({ type: 'wb_bg', state: wbCurrentBg });
        }

        if (type === 'pdf') {
            await renderPdfPage(url, pageNum);
            document.getElementById('wbPageNavControls')?.classList.remove('hidden');
        } else {
            renderImageBackground(url);
            document.getElementById('wbPageNavControls')?.classList.add('hidden');
            document.getElementById('wbPdfSideNavigator')?.classList.add('hidden');
        }

        if (canManage) {
            fetch(`/classroom/${ROOM_ID}/whiteboard`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ action: 'set_background', bg_type: type, bg_url: url, bg_title: title, page_num: pageNum })
            }).catch(console.error);
        }
    }

    function setWhiteboardBg(type) {
        const canManage = (typeof CAN_MANAGE_ROOM !== 'undefined' && CAN_MANAGE_ROOM) || (typeof IS_TEACHER !== 'undefined' && IS_TEACHER);
        if (!canManage) return;
        closeAllWbFlyouts();
        hideMediaLayers();
        const titles = { whiteboard: 'سبورة بيضاء تفاعلية', blackboard: 'سبورة سوداء', grid: 'شبكة مربعات الشرح' };
        const icons = { whiteboard: '📋', blackboard: '⬛', grid: '📐' };

        document.getElementById('wbModeIcon').textContent = icons[type] || '📋';
        document.getElementById('wbModeText').textContent = titles[type] || 'سبورة تفاعلية';
        document.getElementById('wbPageNavControls')?.classList.add('hidden');
        document.getElementById('wbPdfSideNavigator')?.classList.add('hidden');

        wbCurrentBg = { type: type, url: null, title: titles[type], page_num: 1 };
        renderBlankBackground(type);
        broadcastP2P({ type: 'wb_bg', state: wbCurrentBg });

        fetch(`/classroom/${ROOM_ID}/whiteboard`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ action: 'set_background', bg_type: type, bg_url: null, bg_title: titles[type], page_num: 1 })
        }).catch(console.error);
    }

    // مشاركة ورفع الملفات من الجهاز (صورة، كتاب PDF، فيديو) مع تشغيل مباشر وشريط تقدم رفع حي
    async function handleWbLocalFileUpload(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const fileExt = file.name.split('.').pop().toLowerCase();
        const isVideo = file.type.startsWith('video/') || ['mp4', 'webm', 'mov', 'm4v', 'avi', 'mkv', 'flv'].includes(fileExt);
        const isPdf = file.type === 'application/pdf' || fileExt === 'pdf';
        const isImage = file.type.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(fileExt);

        // 1. فتح فوري محلياً عبر Blob URL ليبدأ المعلم الشرح أو المشاهدة فوراً بدون أي تأخير
        const localBlobUrl = URL.createObjectURL(file);
        if (isVideo) {
            openVideoInWorkspace(localBlobUrl, file.name, false);
        } else if (isPdf) {
            openResourceInWorkspace(localBlobUrl, file.name, 'pdf', 1);
        } else if (isImage) {
            openResourceInWorkspace(localBlobUrl, file.name, 'image', 1);
        }

        // 2. إظهار شريط تقدم الرفع
        const pBar = document.getElementById('wbUploadProgressBar');
        const pFill = document.getElementById('wbUploadProgressFill');
        const pPct = document.getElementById('wbUploadPercentText');
        const pStatus = document.getElementById('wbUploadStatusText');
        if (pBar) {
            pBar.classList.remove('hidden');
            if (pStatus) pStatus.textContent = `جاري رفع ومزامنة: ${file.name}`;
            if (pPct) pPct.textContent = '0%';
            if (pFill) pFill.style.width = '0%';
        }

        // 3. إرسال عبر XMLHttpRequest مع تتبع شريط التقدم
        const formData = new FormData();
        formData.append('action', 'upload_bg_file');
        formData.append('file', file);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', `/classroom/${ROOM_ID}/whiteboard`, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', CSRF_TOKEN);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.onprogress = (e) => {
            if (e.lengthComputable) {
                const pct = Math.round((e.loaded / e.total) * 100);
                if (pPct) pPct.textContent = pct + '%';
                if (pFill) pFill.style.width = pct + '%';
            }
        };

        xhr.onload = () => {
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.success && data.url) {
                        if (pStatus) pStatus.textContent = '✅ اكتمل الرفع وجاري المزامنة للطلاب!';
                        if (pFill) pFill.style.width = '100%';
                        if (pPct) pPct.textContent = '100%';

                        // استبدال الرابط المؤقت بالرابط السيرفري المعتمد وبثه للطلاب
                        wbCurrentBg.url = data.url;
                        wbCurrentBg.title = data.title || file.name;
                        broadcastP2P({ type: 'wb_bg', state: wbCurrentBg });
                        commitWhiteboardChange();

                        setTimeout(() => {
                            pBar?.classList.add('hidden');
                        }, 1200);
                    }
                } catch (err) {
                    console.error('Error parsing upload response:', err);
                    if (pBar) pBar.classList.add('hidden');
                }
            } else {
                console.error('Upload failed with status', xhr.status);
                if (pBar) pBar.classList.add('hidden');
            }
            input.value = '';
        };

        xhr.onerror = () => {
            console.error('Upload network error');
            if (pBar) pBar.classList.add('hidden');
            input.value = '';
        };

        xhr.send(formData);
    }

    // تحويل رقم الصفحة إلى قيمة المنزلق الرأسي (الصفحة 1 في الأعلى دائماً إلى آخر صفحة بالأسفل)
    function pageToSliderVal(p) {
        if (!wbTotalPages || wbTotalPages <= 1) return 1;
        return Math.min(Math.max(1, p), wbTotalPages);
    }

    // تحويل قيمة المنزلق الرأسي إلى رقم الصفحة الحقيقي
    function sliderValToPage(val) {
        const v = parseInt(val, 10);
        if (isNaN(v) || !wbTotalPages || wbTotalPages <= 1) return 1;
        return Math.min(Math.max(1, v), wbTotalPages);
    }

    // تحميل موثوق لملفات PDF مع دعم المعالجة الثنائية المباشرة (Binary ArrayBuffer) لتخطي أي حظر للشبكة أو CORS
    async function loadPdfDocumentWithFallback(url) {
        let targetUrl = normalizeMediaUrl(url);
        try {
            if (window.location.protocol === 'https:' && targetUrl.startsWith('http://')) {
                targetUrl = targetUrl.replace(/^http:\/\//, 'https://');
            }
        } catch(e){}

        const pdfOptions = {
            cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
            cMapPacked: true,
            standardFontDataUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/standard_fonts/',
            enableXfa: true,
            disableFontFace: false
        };

        try {
            const loadingTask = pdfjsLib.getDocument({ ...pdfOptions, url: targetUrl });
            const doc = await loadingTask.promise;
            doc._url = url;
            return doc;
        } catch (directErr) {
            console.warn('Direct PDF load failed, attempting ArrayBuffer fallback fetch:', directErr);
            const res = await fetch(targetUrl, { credentials: 'same-origin' });
            if (!res.ok) throw new Error('HTTP ' + res.status + ' loading PDF');
            const arrayBuffer = await res.arrayBuffer();
            const loadingTask = pdfjsLib.getDocument({ ...pdfOptions, data: arrayBuffer });
            const doc = await loadingTask.promise;
            doc._url = url;
            return doc;
        }
    }

    // رندرة وتصفح صفحات ملفات الـ PDF مع دعم كامل ودقيق للخطوط العربية وملء العرض
    async function renderPdfPage(url, pageNum, customScale = null) {
        if (!window.pdfjsLib) {
            console.warn('pdfjsLib is not loaded');
            return;
        }
        try {
            if (!wbPdfDoc || wbPdfDoc._url !== url) {
                wbPdfDoc = await loadPdfDocumentWithFallback(url);
                wbTotalPages = wbPdfDoc.numPages;
                const totalText = document.getElementById('wbTotalPagesText');
                if (totalText) totalText.textContent = wbTotalPages;
            }

            wbCurrentPage = Math.min(Math.max(1, pageNum), wbTotalPages);
            const currentText = document.getElementById('wbCurrentPageText');
            if (currentText) currentText.textContent = wbCurrentPage;

            const directInput = document.getElementById('wbPageDirectInput');
            if (directInput) directInput.value = wbCurrentPage;

            const slider = document.getElementById('wbPdfVerticalSlider');
            if (slider) {
                slider.max = wbTotalPages;
                slider.value = pageToSliderVal(wbCurrentPage);
            }

            const sideNav = document.getElementById('wbPdfSideNavigator');
            if (sideNav) {
                if (wbTotalPages > 1) {
                    sideNav.classList.remove('hidden');
                } else {
                    sideNav.classList.add('hidden');
                }
            }

            // استرجاع شروحات الصفحة الحالية من الكاش
            const cacheKey = getPdfCacheKey(url, wbCurrentPage);
            if (wbPdfPageStrokesCache[cacheKey]) {
                wbStrokes = JSON.parse(JSON.stringify(wbPdfPageStrokesCache[cacheKey]));
            }

            const page = await wbPdfDoc.getPage(wbCurrentPage);
            const baseViewport = page.getViewport({ scale: 1.0 });

            const container = document.getElementById('whiteboardContainer');
            const stage = document.getElementById('wbCanvasStage');
            const bgCanvas = document.getElementById('whiteboardBgCanvas');
            const drawCanvas = document.getElementById('whiteboardDrawCanvas');

            // حساب العرض المناسب لملء الشاشة مع ترك مساحة مريحة للأدوات
            const contWidth = container ? container.clientWidth : 1200;
            const targetWidth = Math.max(640, contWidth - 70);
            const fitScale = targetWidth / baseViewport.width;
            wbPdfScale = customScale || fitScale;

            const displayWidth = Math.round(baseViewport.width * wbPdfScale);
            const displayHeight = Math.round(baseViewport.height * wbPdfScale);

            // دقة رندرة عالية High-DPI لضمان صفاء ووضوح الخط العربي والإنجليزي فائق الجودة
            const dpr = Math.max(window.devicePixelRatio || 1, 1.75);
            const renderViewport = page.getViewport({ scale: wbPdfScale * dpr });

            bgCanvas.width = renderViewport.width;
            bgCanvas.height = renderViewport.height;
            drawCanvas.width = renderViewport.width;
            drawCanvas.height = renderViewport.height;

            bgCanvas.style.width = displayWidth + 'px';
            bgCanvas.style.height = displayHeight + 'px';
            drawCanvas.style.width = displayWidth + 'px';
            drawCanvas.style.height = displayHeight + 'px';

            if (stage) {
                stage.style.width = displayWidth + 'px';
                stage.style.height = displayHeight + 'px';
                stage.style.marginTop = '12px';
                stage.style.marginBottom = '24px';
            }

            if (container) {
                container.classList.add('overflow-y-auto');
                container.classList.remove('overflow-hidden');
            }

            const zoomLabel = document.getElementById('wbZoomLabel');
            if (zoomLabel) {
                zoomLabel.textContent = Math.round((wbPdfScale / fitScale) * 100) + '%';
            }

            const ctx = bgCanvas.getContext('2d');
            await page.render({ canvasContext: ctx, viewport: renderViewport, intent: 'display' }).promise;
            redrawWhiteboard();
        } catch (e) {
            console.error('PDF rendering error:', e);
        }
    }

    // التنقل بين صفحات الـ PDF (خطوة للأمام أو للخلف)
    async function changeWbPage(delta) {
        if (!wbPdfDoc) return;
        changeWbPageTo(wbCurrentPage + delta);
    }

    // الانتقال المباشر لأي صفحة محددة مع تحديد موضع البداية (أعلى الصفحة أو أسفلها)
    async function changeWbPageTo(targetPage, initialScroll = 'top') {
        if (!wbPdfDoc) return;
        targetPage = Math.min(Math.max(1, targetPage), wbTotalPages);
        if (targetPage !== wbCurrentPage || (wbCurrentBg && wbCurrentBg.page_num !== targetPage)) {
            const currentUrl = (wbCurrentBg && wbCurrentBg.url) ? wbCurrentBg.url : (wbPdfDoc ? wbPdfDoc._url : null);
            const currentTitle = (wbCurrentBg && wbCurrentBg.title) ? wbCurrentBg.title : 'كتاب PDF';
            await openResourceInWorkspace(currentUrl, currentTitle, 'pdf', targetPage);
            const container = document.getElementById('whiteboardContainer');
            if (container) {
                if (initialScroll === 'bottom') {
                    requestAnimationFrame(() => {
                        const bottomPos = Math.max(0, container.scrollHeight - container.clientHeight);
                        container.scrollTo({ top: bottomPos, behavior: 'instant' });
                        syncScrollToPeersThrottled();
                    });
                } else {
                    container.scrollTo({ top: 0, behavior: 'instant' });
                    syncScrollToPeersThrottled();
                }
            }
        }
    }

    function onPageDirectInputChange(val) {
        const p = parseInt(val, 10);
        if (!isNaN(p)) {
            changeWbPageTo(p);
        }
    }

    function onPdfSliderInput(val) {
        const page = sliderValToPage(val);
        const bubble = document.getElementById('wbPdfSliderBubble');
        const bubbleNum = document.getElementById('wbPdfSliderBubbleNum');
        if (bubble && bubbleNum) {
            bubbleNum.textContent = page;
            bubble.classList.remove('hidden');
            if (wbTotalPages > 1) {
                const pct = Math.min(90, Math.max(5, ((page - 1) / (wbTotalPages - 1)) * 80 + 10));
                bubble.style.top = pct + '%';
            }
        }
    }

    function onPdfSliderChange(val) {
        document.getElementById('wbPdfSliderBubble')?.classList.add('hidden');
        const page = sliderValToPage(val);
        changeWbPageTo(page);
    }

    // ملاءمة عرض الكتاب مع الشاشة لملء العرض وإلغاء الفراغات
    async function fitPdfWidth() {
        if (!wbPdfDoc) return;
        try {
            const container = document.getElementById('whiteboardContainer');
            const page = await wbPdfDoc.getPage(wbCurrentPage);
            const baseViewport = page.getViewport({ scale: 1.0 });
            const contWidth = container ? container.clientWidth : 1200;
            const targetWidth = Math.max(640, contWidth - 70);
            wbPdfScale = targetWidth / baseViewport.width;
            await renderPdfPage(wbCurrentBg.url, wbCurrentPage, wbPdfScale);
        } catch(e) { console.error(e); }
    }

    // ملاءمة كامل صفحة الكتاب طولاً وعرضاً
    async function fitPdfPage() {
        if (!wbPdfDoc) return;
        try {
            const container = document.getElementById('whiteboardContainer');
            const page = await wbPdfDoc.getPage(wbCurrentPage);
            const baseViewport = page.getViewport({ scale: 1.0 });
            const contWidth = (container ? container.clientWidth : 1200) - 70;
            const contHeight = (container ? container.clientHeight : 800) - 40;
            wbPdfScale = Math.min(contWidth / baseViewport.width, contHeight / baseViewport.height);
            await renderPdfPage(wbCurrentBg.url, wbCurrentPage, wbPdfScale);
        } catch(e) { console.error(e); }
    }

    // مزامنة التمرير اللحظي للطلاب عند قيام المعلم بالتمرير
    let lastScrollSyncTime = 0;
    function syncScrollToPeersThrottled() {
        if (!IS_TEACHER || !wbCurrentBg || wbCurrentBg.type !== 'pdf') return;
        const now = Date.now();
        if (now - lastScrollSyncTime < 80) return;
        lastScrollSyncTime = now;
        const container = document.getElementById('whiteboardContainer');
        if (!container) return;
        const maxScroll = container.scrollHeight - container.clientHeight;
        const scrollRatio = maxScroll > 0 ? (container.scrollTop / maxScroll) : 0;
        broadcastP2P({
            type: 'wb_scroll',
            scrollTop: container.scrollTop,
            scrollRatio: scrollRatio
        });
    }

    // دوال مشغل الفيديو التعليمي وعناصر التحكم العائمة
    function initHtml5VideoListeners() {
        const video = document.getElementById('whiteboardHtml5Video');
        if (!video) return;

        video.addEventListener('timeupdate', () => {
            const slider = document.getElementById('wbVideoSeekSlider');
            const timeLabel = document.getElementById('wbVideoTimeLabel');
            if (slider && video.duration) {
                slider.value = (video.currentTime / video.duration) * 100;
            }
            if (timeLabel) {
                timeLabel.textContent = `${formatVideoTime(video.currentTime)} / ${formatVideoTime(video.duration || 0)}`;
            }
        });

        video.addEventListener('play', () => {
            const icon = document.getElementById('iconWbVideoPlay');
            const txt = document.getElementById('txtWbVideoPlay');
            if (icon) icon.textContent = '⏸️';
            if (txt) txt.textContent = 'إيقاف';
            if (IS_TEACHER) {
                broadcastP2P({ type: 'wb_video_state', action: 'play', time: video.currentTime });
            }
        });

        video.addEventListener('pause', () => {
            const icon = document.getElementById('iconWbVideoPlay');
            const txt = document.getElementById('txtWbVideoPlay');
            if (icon) icon.textContent = '▶️';
            if (txt) txt.textContent = 'تشغيل';
            if (IS_TEACHER) {
                broadcastP2P({ type: 'wb_video_state', action: 'pause', time: video.currentTime });
            }
        });
    }

    function formatVideoTime(sec) {
        if (isNaN(sec)) return '00:00';
        const m = Math.floor(sec / 60);
        const s = Math.floor(sec % 60);
        return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }

    function toggleHtml5VideoPlay() {
        const video = document.getElementById('whiteboardHtml5Video');
        if (!video) return;
        if (video.paused) {
            video.play().catch(console.error);
        } else {
            video.pause();
        }
    }

    function rewindHtml5Video(seconds = 10) {
        const video = document.getElementById('whiteboardHtml5Video');
        if (!video) return;
        video.currentTime = Math.max(0, video.currentTime - seconds);
        if (IS_TEACHER) {
            broadcastP2P({ type: 'wb_video_state', action: video.paused ? 'pause' : 'play', time: video.currentTime });
        }
    }

    function forwardHtml5Video(seconds = 10) {
        const video = document.getElementById('whiteboardHtml5Video');
        if (!video) return;
        video.currentTime = Math.min(video.duration || 0, video.currentTime + seconds);
        if (IS_TEACHER) {
            broadcastP2P({ type: 'wb_video_state', action: video.paused ? 'pause' : 'play', time: video.currentTime });
        }
    }

    function toggleHtml5VideoMute() {
        const video = document.getElementById('whiteboardHtml5Video');
        const btn = document.getElementById('btnWbVideoMute');
        if (!video) return;
        video.muted = !video.muted;
        if (btn) btn.textContent = video.muted ? '🔇' : '🔊';
    }

    function onVideoSeekInput(percent) {
        const video = document.getElementById('whiteboardHtml5Video');
        if (!video || !video.duration) return;
        video.currentTime = (percent / 100) * video.duration;
        if (IS_TEACHER) {
            broadcastP2P({ type: 'wb_video_state', action: video.paused ? 'pause' : 'play', time: video.currentTime });
        }
    }

    function renderImageBackground(url) {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => {
            const bgCanvas = document.getElementById('whiteboardBgCanvas');
            const drawCanvas = document.getElementById('whiteboardDrawCanvas');
            const stage = document.getElementById('wbCanvasStage');
            const container = document.getElementById('whiteboardContainer');
            if (!bgCanvas || !drawCanvas) return;

            if (container) {
                container.classList.remove('overflow-y-auto');
                container.classList.add('overflow-hidden');
                container.scrollTop = 0;
            }

            const w = img.naturalWidth || 1600;
            const h = img.naturalHeight || 1000;
            bgCanvas.width = w;
            bgCanvas.height = h;
            drawCanvas.width = w;
            drawCanvas.height = h;

            if (stage) {
                stage.style.width = '100%';
                stage.style.height = '100%';
                stage.style.marginTop = '0';
                stage.style.marginBottom = '0';
            }
            bgCanvas.style.width = '100%';
            bgCanvas.style.height = '100%';
            bgCanvas.style.objectFit = 'contain';
            drawCanvas.style.width = '100%';
            drawCanvas.style.height = '100%';
            drawCanvas.style.objectFit = 'contain';

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
        const stage = document.getElementById('wbCanvasStage');
        const container = document.getElementById('whiteboardContainer');
        if (!bgCanvas || !drawCanvas) return;

        if (container) {
            container.classList.remove('overflow-y-auto');
            container.classList.add('overflow-hidden');
            container.scrollTop = 0;
        }

        const w = 1600;
        const h = 1000;
        bgCanvas.width = w;
        bgCanvas.height = h;
        drawCanvas.width = w;
        drawCanvas.height = h;

        if (stage) {
            stage.style.width = '100%';
            stage.style.height = '100%';
            stage.style.marginTop = '0';
            stage.style.marginBottom = '0';
        }
        bgCanvas.style.width = '100%';
        bgCanvas.style.height = '100%';
        bgCanvas.style.objectFit = 'contain';
        drawCanvas.style.width = '100%';
        drawCanvas.style.height = '100%';
        drawCanvas.style.objectFit = 'contain';

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
        if (wbCurrentBg && wbCurrentBg.type === 'pdf') {
            wbPdfScale = Math.min(3.5, Math.max(0.4, wbPdfScale + delta));
            renderPdfPage(wbCurrentBg.url, wbCurrentPage, wbPdfScale);
            return;
        }
        wbZoom = Math.min(2.5, Math.max(0.5, wbZoom + delta));
        applyWbZoom();
    }
    function resetWbZoom() {
        if (wbCurrentBg && wbCurrentBg.type === 'pdf') {
            fitPdfWidth();
            return;
        }
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
        if (serverVersion > currentWbVersion || currentWbVersion === -1) {
            currentWbVersion = serverVersion;
            if (serverState) {
                applyServerWhiteboardState(serverState);
            }
        }
    }

    async function applyServerWhiteboardState(state) {
        if (!state) return;
        const normalizedUrl = normalizeMediaUrl(state.bg_url);
        const bgChanged = (state.bg_type !== wbCurrentBg.type || normalizedUrl !== wbCurrentBg.url || state.page_num !== wbCurrentBg.page_num);

        wbCurrentBg = {
            type: state.bg_type || 'whiteboard',
            url: normalizedUrl || null,
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
                document.getElementById('wbPdfSideNavigator')?.classList.add('hidden');
            } else if (wbCurrentBg.type === 'video' && wbCurrentBg.url) {
                renderVideoBackground(wbCurrentBg.url, wbCurrentBg.title);
                document.getElementById('wbPageNavControls')?.classList.add('hidden');
                document.getElementById('wbPdfSideNavigator')?.classList.add('hidden');
            } else if (wbCurrentBg.type === 'pdf' && wbCurrentBg.url) {
                hideMediaLayers();
                await renderPdfPage(wbCurrentBg.url, wbCurrentBg.page_num);
                document.getElementById('wbPageNavControls')?.classList.remove('hidden');
            } else if (wbCurrentBg.type === 'image' && wbCurrentBg.url) {
                hideMediaLayers();
                renderImageBackground(wbCurrentBg.url);
                document.getElementById('wbPageNavControls')?.classList.add('hidden');
                document.getElementById('wbPdfSideNavigator')?.classList.add('hidden');
            } else {
                hideMediaLayers();
                renderBlankBackground(wbCurrentBg.type);
                document.getElementById('wbPageNavControls')?.classList.add('hidden');
                document.getElementById('wbPdfSideNavigator')?.classList.add('hidden');
            }
        }

        wbStrokes = state.strokes || [];
        redrawWhiteboard();
    }

    // بدء تشغيل الوسائط والسبورة عند تحميل الصفحة
    window.addEventListener('DOMContentLoaded', () => {
        initLocalMedia();
        initWhiteboard();
        // على شاشات الجوال: التأكد من تفعيل عرض السبورة التفاعلية فورياً للطالب
        if (window.innerWidth < 1024) {
            switchMobileView('whiteboard');
        }
        // للطلاب: إظهار نافذة تفعيل الصوت والمايكروفون لمنع حظر الصوت التلقائي بالمتصفح
        if (!IS_TEACHER) {
            document.getElementById('audioPermissionModal')?.classList.remove('hidden');
        }
    });
    </script>

</body>
</html>
