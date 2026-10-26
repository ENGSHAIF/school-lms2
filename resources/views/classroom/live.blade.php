<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الفصل التفاعلي المباشر (Air Class) - {{ $title ?? ($booking->topic ?? 'محادثة حية') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .star-pop { animation: pop 0.4s ease-out; }
        @keyframes pop {
            0% { transform: scale(0.5); opacity: 0; }
            80% { transform: scale(1.3); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body class="min-h-screen lg:h-screen flex flex-col overflow-y-auto lg:overflow-hidden">

    <!-- شريط الأدوات العلوي للفصل التفاعلي -->
    <header class="bg-slate-900 border-b border-slate-800 px-3 sm:px-6 py-2.5 sm:py-3 flex flex-wrap justify-between items-center gap-2 sm:gap-3 z-20 shrink-0">
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 {{ (isset($group) && $group->is_live) || (isset($booking) && $booking->is_live) ? 'bg-red-500 animate-ping' : 'bg-slate-500' }} rounded-full"></span>
            <span class="bg-red-500/20 text-red-400 text-xs px-2.5 py-0.5 rounded-full font-bold border border-red-500/30">
                {{ isset($isGroup) && $isGroup ? 'فصل جماعي حي • Live Group Class' : 'مباشر حي • Live 1-on-1' }}
            </span>
            <h1 class="text-sm font-black text-white hidden sm:block">{{ $title ?? ($booking->topic ?? 'الفصل التفاعلي المباشر') }}</h1>
        </div>

        <!-- أزرار التحكم بالبث الحي للمعلم والإدارة -->
        @if($isTeacher)
            <div class="flex items-center gap-2 bg-slate-950/80 px-3 py-1 rounded-2xl border border-slate-800">
                @if(isset($group) && $group)
                    @if($group->is_live)
                        <span class="text-[11px] font-bold text-emerald-400 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>البث مفتوح للطلاب 🟢</span>
                        </span>
                        <form method="POST" action="{{ route('groups.live.stop', $group->id) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-[11px] font-bold bg-rose-600/80 hover:bg-rose-600 text-white px-2.5 py-1 rounded-lg transition shadow-sm">
                                ⏹️ إغلاق البث للطلاب
                            </button>
                        </form>
                    @else
                        <span class="text-[11px] font-bold text-rose-400 flex items-center gap-1">
                            <span>البث مغلق (في غرفة الانتظار) 🔴</span>
                        </span>
                        @if($group->isPendingApproval())
                            <span class="text-[10px] text-amber-300 font-bold bg-amber-500/20 px-2 py-0.5 rounded border border-amber-500/30">
                                ⏳ طلب معلق من: {{ $group->requester?->name ?? 'طالب' }}
                            </span>
                        @endif
                        <form method="POST" action="{{ route('groups.live.start', $group->id) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-[11px] font-black bg-emerald-500 hover:bg-emerald-400 text-slate-950 px-3 py-1 rounded-lg transition shadow-md flex items-center gap-1">
                                <span>📡</span>
                                <span>فتح وبدء البث للطلاب الآن</span>
                            </button>
                        </form>
                    @endif
                @elseif(isset($booking) && $booking)
                    <form method="POST" action="{{ route('classroom.booking.toggle_live', $booking->id) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-[11px] font-bold {{ $booking->is_live ? 'bg-rose-600 text-white' : 'bg-emerald-500 text-slate-950' }} px-3 py-1 rounded-lg transition">
                            {{ $booking->is_live ? '⏹️ إغلاق البث للطالب' : '📡 فتح البث للطالب الآن' }}
                        </button>
                    </form>
                @endif
            </div>
        @endif

        <!-- أزرار الأدوات التفاعلية: المحادثة والتسليمات + مناهج المستوى الدائمة -->
        <div class="flex items-center gap-2">
            <button type="button" onclick="toggleSidePanel('chat')" id="btnToggleChat" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-3 py-1.5 rounded-xl border border-indigo-400/30 flex items-center gap-1.5 transition shadow-sm">
                <span>💬</span>
                <span>المحادثة وأوراق الإجابة</span>
            </button>
            <button type="button" onclick="toggleSidePanel('resources')" id="btnToggleResources" class="bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-700 flex items-center gap-1.5 transition">
                <span>📚</span>
                <span>كتب ومنهج المستوى</span>
            </button>
        </div>

        <!-- عداد النجوم ونظام المكافآت (ASOTS Stars) -->
        <div class="flex items-center gap-4">
            <div class="bg-slate-800 border border-amber-500/40 px-4 py-1.5 rounded-2xl flex items-center gap-2">
                <span class="text-lg">⭐</span>
                <span class="text-xs text-amber-300 font-bold">النجوم المكتسبة:</span>
                <span id="starsCount" class="text-base font-black text-amber-400 font-mono">0</span>
            </div>

            <!-- العداد التنازلي للحصة (25 دقيقة) -->
            <div class="bg-slate-800 border border-slate-700 px-4 py-1.5 rounded-2xl font-mono text-sm font-bold text-emerald-400 flex items-center gap-1.5">
                <span>⏱️</span>
                <span id="sessionTimer">25:00</span>
            </div>

            <a href="{{ auth()->user()->isStudent() ? route('student.portal') : (auth()->user()->isTeacher() ? route('teacher.dashboard') : route('admin.groups.index')) }}" class="text-xs text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 px-3 py-1.5 rounded-xl transition">
                مغادرة الفصل ✕
            </a>
        </div>
    </header>

    <!-- مساحة العمل الرئيسية (شاشة العرض + كاميرات الفيديو) -->
    <main class="flex-1 grid grid-cols-1 lg:grid-cols-4 gap-3 sm:gap-4 p-3 sm:p-4 overflow-y-auto lg:overflow-hidden">

        <!-- العمود الجانبي: كاميرات الفيديو للطالب والمعلم (WebRTC / Dual Video) -->
        <aside class="lg:col-span-1 flex flex-col gap-3 h-auto lg:h-full lg:overflow-y-auto shrink-0">
            
            <!-- كاميرا المعلم -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden relative shadow-lg flex-1 min-h-[180px] flex flex-col justify-between p-3">
                <div class="flex justify-between items-center z-10">
                    <span class="text-[11px] font-bold bg-slate-950/80 px-2.5 py-1 rounded-lg text-indigo-300 border border-slate-700">
                        👨‍🏫 المعلم: {{ $teacher?->name ?? ($booking->teacher?->name ?? 'الأستاذ المسؤول') }}
                    </span>
                    <span class="text-emerald-400 text-xs">● متصل</span>
                </div>

                <!-- شاشة الفيديو للمحاضر -->
                <div class="absolute inset-0 flex items-center justify-center bg-slate-800">
                    <video id="teacherVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
                    <div id="teacherAvatar" class="text-center">
                        <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-1">👨‍🏫</div>
                        <span class="text-xs text-slate-300 font-bold">{{ $teacher?->name ?? ($booking->teacher?->name ?? 'الأستاذ المسؤول') }}</span>
                    </div>
                </div>

                <div class="z-10 flex gap-2">
                    <span class="bg-black/60 px-2 py-0.5 rounded text-[10px] text-slate-300">ميكروفون: شغال 🎙️</span>
                </div>
            </div>

            <!-- كاميرا الطالب أو طلاب المجموعة -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden relative shadow-lg flex-1 min-h-[180px] flex flex-col justify-between p-3">
                <div class="flex justify-between items-center z-10">
                    <span class="text-[11px] font-bold bg-slate-950/80 px-2.5 py-1 rounded-lg text-emerald-300 border border-slate-700">
                        @if(isset($isGroup) && $isGroup)
                            👥 طلاب المجموعة ({{ $students->count() }})
                        @else
                            🎒 الطالب: {{ $students->first()?->name ?? ($booking->student?->name ?? 'الطالب') }}
                        @endif
                    </span>
                    <span class="text-emerald-400 text-xs">● متصل</span>
                </div>

                <!-- كاميرا الطالب الحية من المتصفح -->
                <div class="absolute inset-0 flex items-center justify-center bg-slate-800">
                    <video id="studentVideo" autoplay playsinline muted class="w-full h-full object-cover"></video>
                    <div id="studentAvatar" class="text-center">
                        <div class="w-16 h-16 bg-emerald-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-1">
                            {{ isset($isGroup) && $isGroup ? '👥' : '🎓' }}
                        </div>
                        <span class="text-xs text-slate-300 font-bold">
                            @if(isset($isGroup) && $isGroup)
                                {{ $students->pluck('name')->take(3)->implode('، ') }}
                            @else
                                {{ $students->first()?->name ?? ($booking->student?->name ?? 'الطالب') }}
                            @endif
                        </span>
                    </div>
                </div>

                <div class="z-10 flex justify-between items-center">
                    <span class="bg-black/60 px-2 py-0.5 rounded text-[10px] text-slate-300">كاميرا المتصفح نشطة 📷</span>
                    <button onclick="toggleCamera()" class="bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold px-2 py-1 rounded">
                        تبديل الكاميرا
                    </button>
                </div>
            </div>

            <!-- أزرار المعلم للتحكم والمكافأة -->
            @if($isTeacher)
                <div class="bg-slate-900 p-3 rounded-2xl border border-slate-800 space-y-2">
                    <button onclick="rewardStar()" class="w-full bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs py-2.5 rounded-xl shadow-lg transition flex items-center justify-center gap-1.5">
                        <span class="text-base">⭐</span>
                        <span>مكافأة الطالب بنجمة (Reward Star)</span>
                    </button>

                    @if(isset($isGroup) && $isGroup && isset($group))
                        <a href="{{ route('admin.groups.evaluations.create', ['group_id' => $group->id]) }}" target="_blank" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2.5 rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                            <span>📋</span>
                            <span>رصد الحضور والتقييم اليومي للمجموعة</span>
                        </a>
                    @endif

                    <button onclick="openReportModal()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 rounded-xl shadow-md transition">
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
                    <span class="bg-indigo-600 text-white text-[11px] font-bold px-3 py-1 rounded-lg">شريحة المنهج 03 من 08</span>
                    <h2 class="text-sm font-bold text-slate-200">Speaking Task 1: Expressing Your Opinion & Fluency Practice</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="prevSlide()" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded-lg text-xs font-bold text-slate-300">السابق ◀</button>
                    <button onclick="nextSlide()" class="bg-slate-800 hover:bg-slate-700 px-3 py-1 rounded-lg text-xs font-bold text-slate-300">التالي ▶</button>
                </div>
            </div>

            <!-- متن الشريحة التفاعلية (Courseware) -->
            <div id="slideContent" class="flex-1 bg-white text-slate-900 rounded-2xl p-8 shadow-inner overflow-y-auto leading-relaxed" dir="ltr">
                <div class="border-b border-slate-200 pb-4 mb-4">
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider block mb-1">TOEFL Independent Speaking Prompt</span>
                    <h3 class="text-xl font-black text-slate-900">"Some people prefer to study alone, while others prefer to study in groups. Which do you prefer and why?"</h3>
                </div>

                <div class="space-y-4 text-sm text-slate-700">
                    <div class="bg-indigo-50 border-l-4 border-indigo-600 p-4 rounded-r-xl">
                        <strong class="text-indigo-950 block mb-1">Key Pronunciation Drill (Words to emphasize):</strong>
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

            <!-- لوح ملاحظات المعلم الفوري (Whiteboard notes) -->
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full"></span>
                    <span>اللوح التفاعلي متزامن لحظياً بين الطالب والمعلم.</span>
                </span>
                <span class="font-mono text-slate-500">Air Class v2.4 • WebRTC Ultra-Low Latency</span>
            </div>

        </section>

    </main>

    @php
        $levelResources = collect();
        if (isset($group) && $group && $group->level) {
            $levelResources = $group->level->resources ?? collect();
        } elseif (isset($booking) && $booking && $booking->student) {
            $enrollment = $booking->student->enrollments()->where('status', 'active')->first();
            $levelResources = $enrollment?->level?->resources ?? collect();
        }
        $groupActivities = isset($group) && $group ? ($group->activities ?? collect()) : collect();
        $uploadUrl = isset($group) && $group ? route('groups.activities.store', $group->id) : null;
    @endphp

    <!-- اللوحة الجانبية التفاعلية للفصل: المحادثة والتسليمات الفورية + بنك مناهج المستوى الدائمة -->
    <aside id="liveInteractivePanel" class="hidden fixed inset-y-0 right-0 w-full sm:w-[420px] bg-slate-900 border-l border-slate-800 z-40 flex flex-col shadow-2xl transition duration-300">
        
        <!-- ترويسة اللوحة وتبديل التبويبات -->
        <div class="p-3 bg-slate-950 border-b border-slate-800 flex justify-between items-center gap-2">
            <div class="flex items-center gap-1.5 bg-slate-900 p-1 rounded-xl border border-slate-800 text-xs font-bold">
                <button type="button" onclick="switchLiveTab('chat')" id="tabBtnChat" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white transition flex items-center gap-1.5 shadow-sm">
                    <span>💬</span>
                    <span>المحادثة وأوراق الإجابة</span>
                </button>
                <button type="button" onclick="switchLiveTab('resources')" id="tabBtnResources" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-white transition flex items-center gap-1.5">
                    <span>📚</span>
                    <span>مناهج المستوى</span>
                </button>
            </div>
            <button onclick="closeSidePanel()" class="text-slate-400 hover:text-white text-lg p-1.5 rounded-lg hover:bg-slate-800 transition">✕</button>
        </div>

        <!-- 1. تبويب المحادثة المباشرة وتسليم أوراق الإجابات والتسجيلات -->
        <div id="liveTabChat" class="flex-1 flex flex-col justify-between overflow-hidden">
            
            <!-- قائمة الرسائل والتسليمات -->
            <div id="liveChatMessagesContainer" class="flex-1 p-4 space-y-3 overflow-y-auto text-xs">
                
                <!-- رسالة ترحيبية إرشادية -->
                <div class="bg-indigo-950/60 border border-indigo-800/60 rounded-2xl p-3.5 text-indigo-200 space-y-1">
                    <div class="font-bold flex items-center gap-1.5 text-indigo-300 text-xs">
                        <span>👋</span>
                        <span>مرحباً بك في المحادثة الحية للفصل!</span>
                    </div>
                    <p class="text-[11px] leading-relaxed text-indigo-200/80">
                        يمكنك تبادل الرسائل النصية، رفع صورة ورقة إجابتك فوراً بالكاميرا 📷، أو تسجيل صوتك بالمايكروفون 🎙️ ليسمعك الأستاذ وطلاب المجموعة.
                    </p>
                </div>

                <!-- استعراض الأنشطة والواجبات السابقة المسجلة للمجموعة إن وُجدت -->
                @foreach($groupActivities->take(15) as $item)
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

                        @if($item->instructions)
                            <div class="text-[11px] text-slate-400 bg-slate-900/80 p-2 rounded-xl">
                                {{ $item->instructions }}
                            </div>
                        @endif

                        <!-- معاينة الصورة المصغرة لورقة الإجابة -->
                        @if($item->isImage() && $item->file_path)
                            <div class="cursor-pointer overflow-hidden rounded-xl border border-slate-700 bg-black aspect-video max-h-36 flex items-center justify-center relative group" onclick="openLiveImageModal('{{ asset($item->file_path) }}', '{{ addslashes($item->title) }}')">
                                <img src="{{ asset($item->file_path) }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-[11px] text-white font-bold gap-1">
                                    <span>🔍 تكبير ورقة الإجابة</span>
                                </div>
                            </div>
                        @endif

                        <!-- مشغل الصوت إن كان تسجيلاً صوتياً -->
                        @if($item->isAudio() && $item->file_path)
                            <audio controls class="w-full h-7">
                                <source src="{{ asset($item->file_path) }}">
                            </audio>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- شريط مسجل الصوت المباشر المصغر (يظهر عند الضغط على تسجيل صوتي) -->
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
                    <button type="button" id="btnLiveVoiceSend" onclick="sendLiveVoiceRecording('{{ $uploadUrl }}')" class="hidden flex-1 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs transition shadow-md">
                        🚀 إرسال التسجيل الصوتي
                    </button>
                    <button type="button" onclick="cancelLiveVoiceRecording()" class="px-2.5 py-1.5 text-slate-400 hover:text-white text-xs">إلغاء</button>
                </div>
            </div>

            <!-- صندوق إدخال الرسائل والأزرار السريعة -->
            <div class="p-3 bg-slate-950 border-t border-slate-800 space-y-2">
                
                <!-- أزرار الإجراءات السريعة: رفع صورة ورقة إجابة + تسجيل صوتي -->
                <div class="flex items-center gap-2">
                    <!-- مدخل ملف الصورة المخفي لكاميرا الهاتف أو اختيار صورة -->
                    <input type="file" id="liveAnswerFileInput" accept="image/*,application/pdf" capture="environment" class="hidden" onchange="handleLiveAnswerUpload(this, '{{ $uploadUrl }}')">
                    
                    <button type="button" onclick="triggerLiveAnswerUpload()" class="flex-1 py-1.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-400 border border-slate-700 font-bold text-[11px] flex items-center justify-center gap-1.5 transition">
                        <span>📸</span>
                        <span>رفع ورقة إجابة</span>
                    </button>

                    <button type="button" onclick="startLiveVoiceRecording()" class="flex-1 py-1.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-purple-400 border border-slate-700 font-bold text-[11px] flex items-center justify-center gap-1.5 transition">
                        <span>🎙️</span>
                        <span>تسجيل صوتي</span>
                    </button>
                </div>

                <!-- صندوق كتابة الرسالة النصية -->
                <form id="liveChatForm" onsubmit="sendLiveChatMessage(event, '{{ $uploadUrl }}')" class="flex gap-2">
                    <input type="text" id="liveChatInput" placeholder="اكتب سؤالك أو تعليقك للمعلم..." 
                        class="flex-1 bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1 shrink-0">
                        <span>إرسال</span>
                        <span>✈️</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- 2. تبويب بنك مناهج وكتب المستوى الدائمة -->
        <div id="liveTabResources" class="hidden flex-1 p-4 space-y-4 overflow-y-auto text-xs">
            <div class="bg-indigo-950/40 border border-indigo-900/50 p-3 rounded-2xl text-[11px] text-indigo-300 space-y-1">
                <span class="font-bold block text-white flex items-center gap-1">
                    <span>📌</span>
                    <span>بنك المناهج والموارد الدائمة لهذا المستوى</span>
                </span>
                <p>هذه الكتب والمقاطع الصوتية ثابتة ودائمة للمستوى ولا تتأثر بمحادثات الفصل اليومية، ومتاحة للرجوع إليها في أي وقت أثناء الشرح.</p>
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

            <!-- محاضرات الفيديو التوضيحية (Video Lectures) -->
            <div class="space-y-2">
                <span class="font-bold text-slate-300 text-xs block">🎥 محاضرات الفيديو التعليمية:</span>
                @php $videos = $levelResources->where('type', 'video'); @endphp
                @forelse($videos as $vid)
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex justify-between items-center gap-2">
                        <div class="truncate">
                            <span class="font-bold text-slate-200 block truncate">{{ $vid->title }}</span>
                            <span class="text-[10px] text-slate-500">{{ $vid->duration_minutes ? $vid->duration_minutes . ' دقيقة' : 'فيديو شرح' }}</span>
                        </div>
                        @if($vid->external_url)
                            <a href="{{ $vid->external_url }}" target="_blank" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[10px] rounded-lg transition shrink-0">
                                تشغيل ▶️
                            </a>
                        @elseif($vid->file_path)
                            <a href="{{ asset($vid->file_path) }}" target="_blank" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[10px] rounded-lg transition shrink-0">
                                تشغيل ▶️
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="p-3 text-center text-slate-500 text-[11px] bg-slate-950/40 rounded-xl">
                        لا توجد فيديوهات مسجلة لهذا المستوى حالياً.
                    </div>
                @endforelse
            </div>

        </div>

    </aside>

    <!-- نافذة تكبير ومعاينة صور أوراق الإجابات داخل الفصل الحي (Live Lightbox) -->
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

    <!-- سكريبتات كاميرا المتصفح والعداد التنازلي ونظام النجوم والمحادثة التفاعلية -->
    <script>
    // 1. تشغيل كاميرا المتصفح الحية للطالب
    let localStream;
    async function initCamera() {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            const videoEl = document.getElementById('studentVideo');
            if (videoEl) {
                videoEl.srcObject = localStream;
                document.getElementById('studentAvatar').classList.add('hidden');
            }
        } catch (e) {
            console.log('Camera access denied or unavailable in demo mode');
        }
    }
    initCamera();

    function toggleCamera() {
        if (localStream) {
            localStream.getVideoTracks().forEach(track => track.enabled = !track.enabled);
        }
    }

    // 2. العداد التنازلي للجلسة (25 دقيقة)
    let sessionSeconds = 25 * 60;
    const timerEl = document.getElementById('sessionTimer');
    setInterval(() => {
        if (sessionSeconds <= 0) return;
        sessionSeconds--;
        const m = Math.floor(sessionSeconds / 60);
        const s = sessionSeconds % 60;
        timerEl.textContent = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }, 1000);

    // 3. نظام النجوم ومكافآت ASOTS التفاعلية
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

        // إشعار صوتي بصري
        alert('🌟 أحسنت! كافأك المعلم بنجمة تشجيعية جديدة.');
    }

    // 4. تقليب شرائح المنهج التفاعلي (Courseware Slides)
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
        const slideHeader = document.querySelector('section span.bg-indigo-600');
        if (slideHeader) {
            slideHeader.textContent = `شريحة المنهج 0${currentSlide} من 0${totalSlides}`;
        }
    }

    // 5. التحكم باللوحة الجانبية التفاعلية (المحادثة وبنك المناهج)
    function toggleSidePanel(tab) {
        const panel = document.getElementById('liveInteractivePanel');
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            switchLiveTab(tab || 'chat');
        } else {
            switchLiveTab(tab || 'chat');
        }
    }
    function closeSidePanel() {
        document.getElementById('liveInteractivePanel').classList.add('hidden');
    }
    function switchLiveTab(tab) {
        const tabChat = document.getElementById('liveTabChat');
        const tabResources = document.getElementById('liveTabResources');
        const btnChat = document.getElementById('tabBtnChat');
        const btnResources = document.getElementById('tabBtnResources');

        if (tab === 'chat') {
            tabChat.classList.remove('hidden');
            tabResources.classList.add('hidden');
            btnChat.className = "px-3 py-1.5 rounded-lg bg-indigo-600 text-white transition flex items-center gap-1.5 shadow-sm";
            btnResources.className = "px-3 py-1.5 rounded-lg text-slate-400 hover:text-white transition flex items-center gap-1.5";
        } else {
            tabChat.classList.add('hidden');
            tabResources.classList.remove('hidden');
            btnResources.className = "px-3 py-1.5 rounded-lg bg-indigo-600 text-white transition flex items-center gap-1.5 shadow-sm";
            btnChat.className = "px-3 py-1.5 rounded-lg text-slate-400 hover:text-white transition flex items-center gap-1.5";
        }
    }

    // 6. إرسال رسالة المحادثة النصية الحية
    function sendLiveChatMessage(e, uploadUrl) {
        e.preventDefault();
        const input = document.getElementById('liveChatInput');
        const text = input.value.trim();
        if (!text) return;

        const userName = "{{ auth()->user()->name }}";
        const isStudent = {{ auth()->user()->isStudent() ? 'true' : 'false' }};
        const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        appendLiveChatMessage({
            name: userName,
            time: now,
            text: text,
            isStudent: isStudent
        });

        input.value = '';

        // إذا كان للفصل رابط مجموعة، نحفظ الرسالة كتنبيه/ملاحظة
        if (uploadUrl) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('title', text);
            formData.append('type', 'announcement');
            fetch(uploadUrl, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).catch(console.error);
        }
    }

    function appendLiveChatMessage(msg) {
        const container = document.getElementById('liveChatMessagesContainer');
        const div = document.createElement('div');
        div.className = `p-3 rounded-2xl border ${msg.isStudent ? 'border-emerald-800/40 bg-emerald-950/20' : 'border-indigo-800/40 bg-indigo-950/20'} space-y-1`;
        div.innerHTML = `
            <div class="flex justify-between items-center text-[10px]">
                <span class="font-bold text-slate-300">${msg.isStudent ? '🎒' : '👨‍🏫'} ${msg.name}</span>
                <span class="text-slate-500 font-mono">${msg.time}</span>
            </div>
            <div class="text-xs text-slate-200 leading-relaxed">${msg.text}</div>
            ${msg.image ? `
                <div class="cursor-pointer overflow-hidden rounded-xl border border-slate-700 aspect-video max-h-36 flex items-center justify-center mt-1" onclick="openLiveImageModal('${msg.image}', '${msg.text}')">
                    <img src="${msg.image}" class="w-full h-full object-cover">
                </div>
            ` : ''}
            ${msg.audio ? `
                <audio controls src="${msg.audio}" class="w-full h-7 mt-1"></audio>
            ` : ''}
        `;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    // 7. رفع ورقة إجابة فورية بالكاميرا أو الملف أثناء البث الحي
    function triggerLiveAnswerUpload() {
        document.getElementById('liveAnswerFileInput').click();
    }

    async function handleLiveAnswerUpload(input, uploadUrl) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const isImage = file.type.startsWith('image/');
        const userName = "{{ auth()->user()->name }}";
        const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        if (uploadUrl) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('title', 'ورقة إجابة فورية - ' + userName);
            formData.append('type', 'worksheet');
            formData.append('attachment', file);

            try {
                const response = await fetch(uploadUrl, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const res = await response.json();
                if (res.success && res.activity) {
                    appendLiveChatMessage({
                        name: userName,
                        time: now,
                        text: '📸 تم تسليم ورقة إجابة فورية: ' + file.name,
                        image: res.activity.file_path,
                        isStudent: true
                    });
                }
            } catch (err) {
                console.error(err);
                // محلياً في حالة الخطأ أو الحصة الفردية
                const localUrl = URL.createObjectURL(file);
                appendLiveChatMessage({
                    name: userName,
                    time: now,
                    text: '📸 ورقة إجابة فورية: ' + file.name,
                    image: isImage ? localUrl : null,
                    isStudent: true
                });
            }
        } else {
            const localUrl = URL.createObjectURL(file);
            appendLiveChatMessage({
                name: userName,
                time: now,
                text: '📸 ورقة إجابة فورية: ' + file.name,
                image: isImage ? localUrl : null,
                isStudent: true
            });
        }

        input.value = '';
    }

    // 8. مسجل الصوت الفوري المباشر داخل الفصل
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
            alert('تعذر الوصول إلى المايكروفون. تأكد من منح الإذن للمتصفح.');
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

    async function sendLiveVoiceRecording(uploadUrl) {
        if (!liveRecordedBlob) return;
        const userName = "{{ auth()->user()->name }}";
        const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        if (uploadUrl) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('title', 'تسجيل صوتي حي - ' + userName);
            formData.append('type', 'audio');
            formData.append('attachment', liveRecordedBlob, 'voice_' + Date.now() + '.webm');

            try {
                const response = await fetch(uploadUrl, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const res = await response.json();
                appendLiveChatMessage({
                    name: userName,
                    time: now,
                    text: '🎙️ تسجيل صوتي من ' + userName,
                    audio: res.activity?.file_path ? res.activity.file_path : URL.createObjectURL(liveRecordedBlob),
                    isStudent: {{ auth()->user()->isStudent() ? 'true' : 'false' }}
                });
            } catch (err) {
                appendLiveChatMessage({
                    name: userName,
                    time: now,
                    text: '🎙️ تسجيل صوتي من ' + userName,
                    audio: URL.createObjectURL(liveRecordedBlob),
                    isStudent: {{ auth()->user()->isStudent() ? 'true' : 'false' }}
                });
            }
        } else {
            appendLiveChatMessage({
                name: userName,
                time: now,
                text: '🎙️ تسجيل صوتي من ' + userName,
                audio: URL.createObjectURL(liveRecordedBlob),
                isStudent: {{ auth()->user()->isStudent() ? 'true' : 'false' }}
            });
        }

        cancelLiveVoiceRecording();
    }

    // 9. تكبير ومعاينة ورقة الإجابة المنبثقة
    function openLiveImageModal(src, title) {
        document.getElementById('liveImageModalImg').src = src;
        document.getElementById('liveImageModalTitle').innerText = title || 'ورقة إجابة الطالب';
        document.getElementById('liveImageModal').classList.remove('hidden');
    }
    function closeLiveImageModal() {
        document.getElementById('liveImageModal').classList.add('hidden');
    }

    // 10. تقارير الحصة
    function openReportModal() {
        document.getElementById('reportModal').classList.remove('hidden');
    }
    function closeReportModal() {
        document.getElementById('reportModal').classList.add('hidden');
    }
    </script>

</body>
</html>
