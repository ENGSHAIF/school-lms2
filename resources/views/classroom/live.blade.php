<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>الفصل التفاعلي المباشر (Air Class Live) - {{ $title ?? ($booking->topic ?? 'محادثة حية') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
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

    <!-- مساحة العمل الرئيسية (شاشة العرض + كاميرات الفيديو وشبكة المشاركين) -->
    <main class="flex-1 grid grid-cols-1 lg:grid-cols-4 gap-3 sm:gap-4 p-3 sm:p-4 overflow-y-auto lg:overflow-hidden">

        <!-- العمود الجانبي: كاميرات الفيديو وشبكة الطلاب والمعلم (WebRTC Mesh) -->
        <aside class="lg:col-span-1 flex flex-col gap-3 h-auto lg:h-full lg:overflow-y-auto shrink-0">
            
            <!-- كاميرا المعلم الأساسية -->
            <div id="teacherCard" class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden relative shadow-lg min-h-[170px] max-h-[220px] flex flex-col justify-between p-3 transition">
                <div class="flex justify-between items-center z-10">
                    <span class="text-[11px] font-bold bg-slate-950/80 px-2.5 py-1 rounded-lg text-indigo-300 border border-slate-700 flex items-center gap-1">
                        <span>👨‍🏫</span>
                        <span class="truncate max-w-[140px]">{{ $teacher?->name ?? ($booking->teacher?->name ?? 'الأستاذ المسؤول') }}</span>
                    </span>
                    <span id="teacherStatusBadge" class="text-emerald-400 text-xs font-bold">● متصل</span>
                </div>

                <!-- شاشة الفيديو للمحاضر (تعرض كاميرا المعلم إن كان هو، أو بث المعلم للطلاب) -->
                <div class="absolute inset-0 flex items-center justify-center bg-slate-950">
                    <video id="teacherVideo" autoplay playsinline class="w-full h-full object-cover hidden"></video>
                    <div id="teacherAvatar" class="text-center px-2">
                        <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-1 shadow-md">👨‍🏫</div>
                        <span class="text-xs text-slate-200 font-bold block truncate max-w-[160px] mx-auto">{{ $teacher?->name ?? ($booking->teacher?->name ?? 'المعلم') }}</span>
                    </div>
                </div>

                <div class="z-10 flex justify-between items-center text-[10px] text-slate-300">
                    <span id="teacherMicBadge" class="bg-black/60 px-2 py-0.5 rounded">🎙️ المايك شغال</span>
                    <span class="bg-black/60 px-2 py-0.5 rounded text-amber-300 font-bold">قائد الفصل 👑</span>
                </div>
            </div>

            <!-- بطاقة كاميرا المتصفح الخاصة بالمستخدم الحالي (My Self Video / Camera) -->
            <div id="mySelfCard" class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden relative shadow-lg min-h-[160px] max-h-[200px] flex flex-col justify-between p-3 transition">
                <div class="flex justify-between items-center z-10">
                    <span class="text-[11px] font-bold bg-slate-950/80 px-2.5 py-1 rounded-lg text-emerald-300 border border-slate-700 flex items-center gap-1">
                        <span>{{ auth()->user()->isStudent() ? '🎒 أنا (طالب)' : '👨‍🏫 أنا (المعلم)' }}</span>
                    </span>
                    <span id="myLiveBadge" class="text-emerald-400 text-[11px] font-bold">● حي ومباشر</span>
                </div>

                <div class="absolute inset-0 flex items-center justify-center bg-slate-950">
                    <video id="mySelfVideo" autoplay playsinline muted class="w-full h-full object-cover hidden"></video>
                    <div id="mySelfAvatar" class="text-center">
                        <div class="w-14 h-14 bg-emerald-600 rounded-full flex items-center justify-center text-xl mx-auto mb-1 shadow-md">
                            {{ auth()->user()->isStudent() ? '🎓' : '👨‍🏫' }}
                        </div>
                        <span class="text-xs text-slate-300 font-bold">{{ auth()->user()->name }}</span>
                    </div>
                </div>

                <div class="z-10 flex justify-between items-center text-[10px]">
                    <span id="mySelfMicStatus" class="bg-black/60 text-emerald-300 px-2 py-0.5 rounded font-bold">🎙️ صوتك نشط</span>
                    <button type="button" onclick="toggleLocalCam()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-0.5 rounded font-bold transition">
                        تبديل الكاميرا 📷
                    </button>
                </div>
            </div>

            <!-- شبكة فيديوهات الطلاب والمشاركين الآخرين المتصلين في الجلسة (Remote Peers Grid) -->
            <div class="space-y-2">
                <div class="flex justify-between items-center px-1">
                    <span class="text-xs font-bold text-slate-300 flex items-center gap-1">
                        <span>👥</span>
                        <span>فيديوهات الطلاب المتصلين (<span id="remoteCountBadge" class="font-mono text-emerald-400">0</span>):</span>
                    </span>
                    <span class="text-[10px] text-slate-500">صوت وصورة متزامنة</span>
                </div>

                <div id="remotePeersGrid" class="space-y-2.5">
                    <!-- تضاف كروت الطلاب صوت وصورة ديناميكياً هنا عبر WebRTC -->
                    <div id="noRemotePeersPlaceholder" class="p-4 bg-slate-900/60 border border-dashed border-slate-800 rounded-2xl text-center text-slate-500 text-xs space-y-1">
                        <span>📡 بانتظار انضمام بقية طلاب المجموعة...</span>
                        <p class="text-[10px] text-slate-600">ستظهر كاميراتهم وأصواتهم فور دخولهم للغرفة.</p>
                    </div>
                </div>
            </div>

            <!-- أزرار المعلم للتحكم والمكافأة -->
            @if($isTeacher)
                <div class="bg-slate-900 p-3 rounded-2xl border border-slate-800 space-y-2 mt-auto">
                    <button onclick="rewardStar()" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs py-2.5 rounded-xl shadow-lg transition flex items-center justify-center gap-1.5">
                        <span class="text-base">⭐</span>
                        <span>مكافأة طالب بنجمة (Reward Star)</span>
                    </button>

                    @if(isset($isGroup) && $isGroup && isset($group))
                        <a href="{{ route('admin.groups.evaluations.create', $group->id) }}" target="_blank" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2 rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                            <span>📋</span>
                            <span>رصد الحضور والتقييم اليومي للمجموعة</span>
                        </a>
                    @endif

                    <button onclick="openReportModal()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2 rounded-xl shadow-md transition">
                        إنهاء الحصة وإصدار التقرير والواتساب ✅
                    </button>
                </div>
            @endif

        </aside>

        <!-- الشاشة التفاعلية الكبرى (محتوى الدرس واللوح التفاعلي المباشر) -->
        <section class="lg:col-span-3 bg-slate-900 border border-slate-800 rounded-2xl p-4 sm:p-6 flex flex-col justify-between shadow-xl relative min-h-[420px] lg:min-h-0">
            
            <!-- رأس الشريحة التفاعلية -->
            <div class="flex justify-between items-center border-b border-slate-800 pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span id="slideNumberBadge" class="bg-indigo-600 text-white text-[11px] font-bold px-3 py-1 rounded-lg">شريحة المنهج 03 من 08</span>
                    <h2 class="text-sm font-bold text-slate-200">Speaking Task 1: Expressing Your Opinion & Fluency Practice</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="prevSlide()" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded-lg text-xs font-bold text-slate-300">السابق ◀</button>
                    <button onclick="nextSlide()" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded-lg text-xs font-bold text-slate-300">التالي ▶</button>
                </div>
            </div>

            <!-- متن الشريحة التفاعلية (Courseware) -->
            <div id="slideContent" class="flex-1 bg-white text-slate-900 rounded-2xl p-6 sm:p-8 shadow-inner overflow-y-auto leading-relaxed" dir="ltr">
                <div class="border-b border-slate-200 pb-4 mb-4">
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider block mb-1">TOEFL Independent Speaking Prompt</span>
                    <h3 class="text-xl font-black text-slate-900">"Some people prefer to study alone, while others prefer to study in groups. Which do you prefer and why?"</h3>
                </div>

                <div class="space-y-4 text-sm text-slate-700">
                    <div class="bg-indigo-50 border-l-4 border-indigo-600 p-4 rounded-r-xl">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2">
                            <strong class="text-indigo-950 block text-xs">🔤 مخارج الحروف والتدريب الصوتي (Key Pronunciation Drill):</strong>
                            <span class="text-[11px] text-indigo-600 font-semibold bg-indigo-100/70 px-2 py-0.5 rounded">رموز صوتية توضيحية لنطق الكلمات (IPA)</span>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs font-bold font-mono">
                            <span class="bg-white px-2.5 py-1 rounded shadow-sm text-indigo-700">/ˈpref.ər/ Prefer</span>
                            <span class="bg-white px-2.5 py-1 rounded shadow-sm text-indigo-700">/kənˈsɪs.tənt/ Consistent</span>
                            <span class="bg-white px-2.5 py-1 rounded shadow-sm text-indigo-700">/dɪˈstræk.ʃən/ Distraction</span>
                            <span class="bg-white px-2.5 py-1 rounded shadow-sm text-indigo-700">/kəˌlæb.əˈreɪ.ʃən/ Collaboration</span>
                        </div>
                    </div>

                    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl">
                        <strong class="text-amber-950 block mb-1">Sentence Fluency Frame:</strong>
                        <p class="italic">"Personally, I strongly prefer studying alone because it allows me to concentrate fully without unnecessary distractions..."</p>
                    </div>
                </div>
            </div>

            <!-- لوح ملاحظات المعلم الفوري -->
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    <span>البث الحي الصوتي والمرئي متزامن لحظياً بين الطلاب والمعلم.</span>
                </span>
                <span class="font-mono text-slate-500">Air Class v3.0 • WebRTC Ultra-Low Latency Mesh</span>
            </div>

        </section>

    </main>

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
    @endphp

    <!-- اللوحة الجانبية التفاعلية للفصل: المحادثة + قائمة الحاضرين + بنك المناهج -->
    <aside id="liveInteractivePanel" class="hidden fixed inset-y-0 right-0 w-full sm:w-[440px] bg-slate-900 border-l border-slate-800 z-40 flex flex-col shadow-2xl transition duration-300">
        
        <!-- ترويسة اللوحة وتبديل التبويبات الثلاثة -->
        <div class="p-3 bg-slate-950 border-b border-slate-800 flex justify-between items-center gap-2">
            <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-800 text-xs font-bold">
                <button type="button" onclick="switchLiveTab('chat')" id="tabBtnChat" class="px-2.5 py-1.5 rounded-lg bg-indigo-600 text-white transition flex items-center gap-1 shadow-sm">
                    <span>💬</span>
                    <span>المحادثة</span>
                </button>
                <button type="button" onclick="switchLiveTab('participants')" id="tabBtnParticipants" class="px-2.5 py-1.5 rounded-lg text-slate-400 hover:text-white transition flex items-center gap-1">
                    <span>👥</span>
                    <span>الحاضرون (<span id="tabCountBadge">1</span>)</span>
                </button>
                <button type="button" onclick="switchLiveTab('resources')" id="tabBtnResources" class="px-2.5 py-1.5 rounded-lg text-slate-400 hover:text-white transition flex items-center gap-1">
                    <span>📚</span>
                    <span>المنهج</span>
                </button>
            </div>
            <button onclick="closeSidePanel()" class="text-slate-400 hover:text-white text-lg p-1.5 rounded-lg hover:bg-slate-800 transition">✕</button>
        </div>

        <!-- 1. تبويب المحادثة المباشرة وتسليم أوراق الإجابات والتسجيلات الصوتية المتزامنة -->
        <div id="liveTabChat" class="flex-1 flex flex-col justify-between overflow-hidden">
            
            <!-- قائمة الرسائل والتسليمات -->
            <div id="liveChatMessagesContainer" class="flex-1 p-4 space-y-3 overflow-y-auto text-xs">
                
                <!-- رسالة ترحيبية إرشادية -->
                <div class="bg-indigo-950/60 border border-indigo-800/60 rounded-2xl p-3.5 text-indigo-200 space-y-1">
                    <div class="font-bold flex items-center gap-1.5 text-indigo-300 text-xs">
                        <span>👋</span>
                        <span>مرحباً بك في المحادثة الحية للفصل التفاعلي!</span>
                    </div>
                    <p class="text-[11px] leading-relaxed text-indigo-200/80">
                        الرسائل النصية، والتسجيلات الصوتية 🎙️، وأوراق الإجابة 📸 تظهر لجميع أعضاء المجموعة والمعلم لحظياً في نفس الوقت.
                    </p>
                </div>

                <!-- استعراض رسائل وتفريغات وتسجيلات الحصة المباشرة -->
                @if(isset($chatMessages) && $chatMessages->isNotEmpty())
                    @foreach($chatMessages as $msg)
                        @php $isMine = ($msg->user_id === auth()->id()); @endphp
                        <div id="chatMsg_{{ $msg->id }}" class="p-3 rounded-2xl border {{ $msg->user?->isStudent() ? 'border-emerald-800/40 bg-emerald-950/20' : 'border-indigo-800/40 bg-indigo-950/20' }} space-y-1.5">
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
                                <div class="cursor-pointer overflow-hidden rounded-xl border border-slate-700 bg-black aspect-video max-h-36 flex items-center justify-center mt-1" onclick="openLiveImageModal('{{ asset($msg->file_path) }}', '{{ addslashes($msg->content ?? '') }}')">
                                    <img src="{{ asset($msg->file_path) }}" class="w-full h-full object-cover">
                                </div>
                            @endif
                            @if($msg->type === 'audio' && $msg->file_path)
                                <div class="bg-purple-950/40 p-2 rounded-xl border border-purple-800/40 mt-1">
                                    <audio controls src="{{ asset($msg->file_path) }}" class="w-full h-8"></audio>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @elseif(isset($groupActivities))
                    @foreach($groupActivities->take(10) as $item)
                        <div class="p-3 rounded-2xl border {{ $item->isStudentSubmission() ? 'border-emerald-800/40 bg-emerald-950/20' : 'border-slate-800 bg-slate-950/50' }} space-y-2">
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="font-bold flex items-center gap-1 text-slate-300">
                                    <span>{{ $item->type_icon }}</span>
                                    <span>{{ $item->publisher?->name ?? 'مشارك' }}</span>
                                </span>
                                <span class="text-slate-500 font-mono">{{ $item->created_at->format('H:i') }}</span>
                            </div>
                            <div class="text-xs text-slate-200 font-medium leading-relaxed">
                                {{ $item->title }}
                            </div>
                            @if($item->isImage() && $item->file_path)
                                <div class="cursor-pointer overflow-hidden rounded-xl border border-slate-700 bg-black aspect-video max-h-36 flex items-center justify-center relative group" onclick="openLiveImageModal('{{ asset($item->file_path) }}', '{{ addslashes($item->title) }}')">
                                    <img src="{{ asset($item->file_path) }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                </div>
                            @endif
                            @if($item->isAudio() && $item->file_path)
                                <audio controls class="w-full h-8 mt-1">
                                    <source src="{{ asset($item->file_path) }}">
                                </audio>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- شريط مسجل الصوت المباشر المصغر -->
            <div id="liveVoiceBar" class="hidden p-3 bg-slate-950 border-t border-slate-800 space-y-2">
                <div class="flex items-center justify-between">
                    <span id="liveVoiceStatus" class="text-xs text-rose-400 font-bold flex items-center gap-1.5 animate-pulse">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>جاري تسجيل الصوت الآن...</span>
                    </span>
                    <span id="liveVoiceTimer" class="font-mono text-xs font-black text-amber-400">00:00</span>
                </div>
                <div id="liveVoicePreviewContainer" class="hidden">
                    <audio id="liveVoicePreview" controls class="w-full h-7 mb-2"></audio>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="btnLiveVoiceStop" onclick="stopLiveVoiceRecording()" class="flex-1 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                        ⏹️ إنهاء التسجيل
                    </button>
                    <button type="button" id="btnLiveVoiceSend" onclick="sendLiveVoiceRecording()" class="hidden flex-1 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs transition shadow-md">
                        🚀 إرسال التسجيل الصوتي للجميع
                    </button>
                    <button type="button" onclick="cancelLiveVoiceRecording()" class="px-2.5 py-1.5 text-slate-400 hover:text-white text-xs">إلغاء</button>
                </div>
            </div>

            <!-- صندوق إدخال الرسائل والأزرار السريعة -->
            <div class="p-3 bg-slate-950 border-t border-slate-800 space-y-2">
                
                <!-- أزرار الإجراءات السريعة: رفع صورة ورقة إجابة + تسجيل صوتي -->
                <div class="flex items-center gap-2">
                    <input type="file" id="liveAnswerFileInput" accept="image/*,application/pdf" capture="environment" class="hidden" onchange="handleLiveAnswerUpload(this)">
                    
                    <button type="button" onclick="triggerLiveAnswerUpload()" class="flex-1 py-1.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-400 border border-slate-700 font-bold text-[11px] flex items-center justify-center gap-1.5 transition">
                        <span>📸</span>
                        <span>رفع ورقة إجابة / صورة</span>
                    </button>

                    <button type="button" onclick="startLiveVoiceRecording()" class="flex-1 py-1.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-purple-400 border border-slate-700 font-bold text-[11px] flex items-center justify-center gap-1.5 transition">
                        <span>🎙️</span>
                        <span>تسجيل صوتي</span>
                    </button>
                </div>

                <!-- صندوق كتابة الرسالة النصية -->
                <form id="liveChatForm" onsubmit="sendLiveChatMessage(event)" class="flex gap-2">
                    <input type="text" id="liveChatInput" placeholder="اكتب رسالتك أو سؤالك ليظهر للجميع..." 
                        class="flex-1 bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1 shrink-0">
                        <span>إرسال</span>
                        <span>✈️</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- 2. تبويب الحاضرين في البث الحي وأدوات الإدارة والتحكم (Participants & Host Controls) -->
        <div id="liveTabParticipants" class="hidden flex-1 p-4 space-y-4 overflow-y-auto text-xs">
            
            <!-- لوحة التحكم الخاصة بالمعلم والإدارة -->
            @if($isTeacher)
                <div class="bg-indigo-950/60 border border-indigo-800/80 p-3.5 rounded-2xl space-y-3 shadow-inner">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-indigo-200 flex items-center gap-1.5 text-xs">
                            <span>👑</span>
                            <span>أدوات إدارة الجلسة للمعلم والمشرف:</span>
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                        <button type="button" onclick="toggleRoomPolicy()" class="p-2 bg-slate-900 hover:bg-slate-800 rounded-xl border border-indigo-700/50 font-bold text-white flex items-center justify-center gap-1.5 transition">
                            <span id="drawerPolicyIcon">📢</span>
                            <span id="drawerPolicyText">الكلام مسموح للكل</span>
                        </button>

                        <button type="button" onclick="muteAllParticipants()" class="p-2 bg-rose-950/60 hover:bg-rose-900/60 rounded-xl border border-rose-800 font-bold text-rose-300 flex items-center justify-center gap-1.5 transition">
                            <span>🔇</span>
                            <span>كتم صوت الجميع</span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- بطاقة طلبات الكلمة المعلقة (رفع اليد) -->
            <div id="handRaisedContainer" class="hidden bg-amber-950/50 border border-amber-600/70 p-3 rounded-2xl space-y-2">
                <span class="font-bold text-amber-300 text-xs flex items-center gap-1.5">
                    <span>✋</span>
                    <span>طلاب يطلبون الإذن بالحديث الآن:</span>
                </span>
                <div id="handRaisedList" class="space-y-1.5">
                    <!-- يضاف الطلاب رافعو الأيدي هنا ديناميكياً -->
                </div>
            </div>

            <!-- قائمة الحاضرين المتصلين في الوقت الفعلي -->
            <div class="space-y-2">
                <div class="flex justify-between items-center text-xs text-slate-300 font-bold">
                    <span>قائمة المتصلين في البث الحي:</span>
                    <span class="text-[10px] text-emerald-400 font-mono">● متصلون في الغرفة</span>
                </div>

                <div id="participantsList" class="space-y-2">
                    <!-- يتم تعبئة المتصلين ديناميكياً كل 2 ثانية -->
                    <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 text-center text-slate-500">
                        جاري تحميل قائمة الحاضرين...
                    </div>
                </div>
            </div>

        </div>

        <!-- 3. تبويب بنك مناهج وكتب المستوى الدائمة -->
        <div id="liveTabResources" class="hidden flex-1 p-4 space-y-4 overflow-y-auto text-xs">
            <div class="bg-indigo-950/40 border border-indigo-900/50 p-3 rounded-2xl text-[11px] text-indigo-300 space-y-1">
                <span class="font-bold block text-white flex items-center gap-1">
                    <span>📌</span>
                    <span>بنك المناهج والموارد الدائمة لهذا المستوى</span>
                </span>
                <p>هذه المقررات ثابتة ولا تتأثر بمحادثات الفصل اليومية، ويمكن للأستاذ والطلاب الرجوع إليها في أي وقت أثناء الشرح.</p>
            </div>

            <!-- كتب المنهج الرقمية (PDF) -->
            <div class="space-y-2">
                <span class="font-bold text-slate-300 text-xs block">📖 الكتب والمقررات الرسمية (PDF):</span>
                @php $books = $levelResources->where('type', 'book'); @endphp
                @forelse($books as $bk)
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex justify-between items-center gap-2">
                        <div class="truncate">
                            <span class="font-bold text-slate-200 block truncate">{{ $bk->title }}</span>
                            <span class="text-[10px] text-slate-500 font-mono">PDF • {{ $bk->file_size ?? 'كتاب معتمد' }}</span>
                        </div>
                        @if($bk->file_path)
                            <a href="{{ asset($bk->file_path) }}" target="_blank" class="px-2.5 py-1 bg-emerald-600/80 hover:bg-emerald-600 text-white font-bold text-[10px] rounded-lg transition shrink-0">
                                فتح الكتاب 📖
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="p-3 text-center text-slate-500 text-[11px] bg-slate-950/40 rounded-xl">
                        لا توجد كتب مرفوعة لهذا المستوى حالياً.
                    </div>
                @endforelse
            </div>

            <!-- تسجيلات الاستماع المنهجية (Audio Tracks) -->
            <div class="space-y-2">
                <span class="font-bold text-slate-300 text-xs block">🎧 تسجيلات الاستماع المنهجية (Listening Tracks):</span>
                @php $audios = $levelResources->where('type', 'audio'); @endphp
                @forelse($audios as $aud)
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 space-y-1.5">
                        <div class="flex justify-between items-center text-[10px] text-purple-300 font-bold">
                            <span class="truncate">{{ $aud->title }}</span>
                            <span class="font-mono text-slate-500">{{ $aud->file_size }}</span>
                        </div>
                        @if($aud->file_path)
                            <audio controls class="w-full h-7">
                                <source src="{{ asset($aud->file_path) }}">
                            </audio>
                        @endif
                    </div>
                @empty
                    <div class="p-3 text-center text-slate-500 text-[11px] bg-slate-950/40 rounded-xl">
                        لا توجد تسجيلات صوتية منهجية لهذا المستوى حالياً.
                    </div>
                @endforelse
            </div>
        </div>

    </aside>

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

    // 1. تشغيل المايكروفون والكاميرا المحلية
    async function initLocalMedia() {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({
                audio: { echoCancellation: true, noiseSuppression: true, autoGainControl: true },
                video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: "user" }
            });

            isCamEnabled = true;
            isMicEnabled = true;

            // إذا كان المستخدم معلماً، نعرض الكاميرا في مربع المعلم، وإن كان طالباً نعرضها في كرت الطالب
            if (IS_TEACHER) {
                const tVideo = document.getElementById('teacherVideo');
                if (tVideo) {
                    tVideo.srcObject = localStream;
                    tVideo.classList.remove('hidden');
                    document.getElementById('teacherAvatar')?.classList.add('hidden');
                }
            } else {
                const myVideo = document.getElementById('mySelfVideo');
                if (myVideo) {
                    myVideo.srcObject = localStream;
                    myVideo.classList.remove('hidden');
                    document.getElementById('mySelfAvatar')?.classList.add('hidden');
                }
            }

            updateMediaButtonsUI();

        } catch (err) {
            console.warn('Video not accessible, falling back to audio-only:', err);
            try {
                localStream = await navigator.mediaDevices.getUserMedia({
                    audio: { echoCancellation: true, noiseSuppression: true }
                });
                isMicEnabled = true;
                isCamEnabled = false;
                updateMediaButtonsUI();
            } catch (micErr) {
                console.warn('Microphone access denied:', micErr);
                isMicEnabled = false;
                isCamEnabled = false;
                updateMediaButtonsUI();
            }
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
        updateMediaButtonsUI();
    }

    function toggleLocalCam() {
        if (!localStream) {
            initLocalMedia();
            return;
        }
        isCamEnabled = !isCamEnabled;
        localStream.getVideoTracks().forEach(track => track.enabled = isCamEnabled);

        const videoEl = IS_TEACHER ? document.getElementById('teacherVideo') : document.getElementById('mySelfVideo');
        const avatarEl = IS_TEACHER ? document.getElementById('teacherAvatar') : document.getElementById('mySelfAvatar');

        if (isCamEnabled) {
            videoEl?.classList.remove('hidden');
            avatarEl?.classList.add('hidden');
        } else {
            videoEl?.classList.add('hidden');
            avatarEl?.classList.remove('hidden');
        }

        updateMediaButtonsUI();
    }

    function updateMediaButtonsUI() {
        const btnMic = document.getElementById('btnMyMic');
        const iconMic = document.getElementById('iconMyMic');
        const txtMic = document.getElementById('txtMyMic');
        const mySelfMicStatus = document.getElementById('mySelfMicStatus');

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

        const btnCam = document.getElementById('btnMyCam');
        const iconCam = document.getElementById('iconMyCam');
        const txtCam = document.getElementById('txtMyCam');

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

    // 2. محرك WebRTC الصوتي والمرئي (WebRTC Peer Connections Mesh)
    function getOrCreatePeerConnection(remotePeerId, remoteUserId, remoteName, isRemoteStaff) {
        if (peerConnections[remotePeerId]) {
            return peerConnections[remotePeerId];
        }

        const pc = new RTCPeerConnection(ICE_SERVERS);
        peerConnections[remotePeerId] = pc;

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

    // 3. المزامنة اللحظية (Sync Loop كل ثانيتين)
    async function syncClassroom() {
        try {
            const params = new URLSearchParams({
                peer_id: MY_PEER_ID,
                has_camera: isCamEnabled ? '1' : '0',
                has_mic: isMicEnabled ? '1' : '0',
                is_hand_raised: isHandRaised ? '1' : '0',
                last_message_id: lastMessageId
            });

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

            // أ) التعامل مع قرارات الإدارة (كتم، طرد، سياسة الفصل)
            if (data.room_control) {
                if (data.room_control.is_kicked) {
                    alert('🚫 تم إنهاء جلستك في هذا البث الحي من قِبل إدارة الفصل.');
                    window.location.href = "{{ auth()->user()->isStudent() ? route('student.portal') : route('home') }}";
                    return;
                }

                if (data.room_control.is_muted_by_host && isMicEnabled) {
                    isMicEnabled = false;
                    localStream?.getAudioTracks().forEach(t => t.enabled = false);
                    updateMediaButtonsUI();
                    alert('🔇 قام الأستاذ بكتم صوتك في هذه الجلسة.');
                }

                if (data.room_control.policy !== currentRoomPolicy) {
                    currentRoomPolicy = data.room_control.policy;
                    updatePolicyUI(currentRoomPolicy);
                }
            }

            // ب) تحديث قائمة الحاضرين وربط WebRTC مع المتصلين
            updateParticipantsUI(data.participants || []);

            // ج) معالجة إشارات WebRTC الصوتية الواردة
            if (data.signals && data.signals.length > 0) {
                for (const sig of data.signals) {
                    handleIncomingSignal(sig);
                }
            }

            // د) معالجة الرسائل والتسجيلات الجديدة في المحادثة
            if (data.messages && data.messages.length > 0) {
                for (const msg of data.messages) {
                    if (msg.id > lastMessageId) {
                        lastMessageId = msg.id;
                        appendLiveChatMessage(msg);
                    }
                }
            }

        } catch (err) {
            console.error('Classroom sync error:', err);
        }
    }

    // بدء حلقة المزامنة التلقائية
    setInterval(syncClassroom, 2000);
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

    // بدء تشغيل الوسائط عند تحميل الصفحة
    window.addEventListener('DOMContentLoaded', () => {
        initLocalMedia();
    });
    </script>

</body>
</html>
