@php
    $roomId = $roomId ?? ($group?->room_id ?? ($booking?->room_id ?? ''));
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>غرفة الانتظار - {{ $title ?? 'الفصل التفاعلي المباشر' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .radar-pulse {
            animation: radar 2s cubic-bezier(0, 0.2, 0.8, 1) infinite;
        }
        @keyframes radar {
            0% { transform: scale(0.8); opacity: 0.8; }
            100% { transform: scale(2.2); opacity: 0; }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between p-4 sm:p-8">

    <!-- شريط الترويسة العلوي -->
    <header class="max-w-4xl w-full mx-auto flex justify-between items-center py-4 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 bg-amber-500 rounded-full animate-ping"></span>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-800 text-amber-300 border border-slate-700">
                غرفة الانتظار المباشرة • Live Waiting Room
            </span>
        </div>

        <a href="{{ auth()->user()->isStudent() ? route('student.portal') : url()->previous() }}" class="text-xs text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded-xl transition flex items-center gap-1.5">
            <span>←</span>
            <span>العودة للبوابة</span>
        </a>
    </header>

    <!-- المحتوى الرئيسي لغرفة الانتظار -->
    <main class="max-w-xl w-full mx-auto my-auto text-center py-8 space-y-6">

        <!-- إشارة الرادار البصرية وحالة البث -->
        <div class="relative w-28 h-28 mx-auto flex items-center justify-center">
            @php
                $isPending = isset($group) && $group?->isPendingApproval();
            @endphp
            <div id="radarRing" class="absolute inset-0 rounded-full {{ $isPending ? 'bg-amber-500/20' : 'bg-rose-500/20' }} radar-pulse"></div>
            <div id="radarRing2" class="absolute inset-2 rounded-full {{ $isPending ? 'bg-amber-500/30' : 'bg-rose-500/30' }} radar-pulse" style="animation-delay: 0.6s;"></div>
            <div id="radarCenter" class="relative z-10 w-20 h-20 rounded-full {{ $isPending ? 'bg-gradient-to-tr from-amber-600 to-amber-400' : 'bg-gradient-to-tr from-rose-600 to-rose-400' }} flex items-center justify-center text-3xl shadow-2xl transition-all duration-500">
                <span id="radarIcon">{{ $isPending ? '⏳' : '📡' }}</span>
            </div>
        </div>

        <!-- نصوص الحالة والتنبيه للطالب -->
        <div class="space-y-2">
            <h1 id="waitingTitle" class="text-2xl sm:text-3xl font-black text-white">
                {{ $isPending ? 'طلب فتح البث بانتظار اعتماد الإدارة' : 'البث الحي غير مفتوح حالياً' }}
            </h1>
            <p id="waitingDesc" class="text-xs sm:text-sm text-slate-300 max-w-md mx-auto leading-relaxed">
                @if($isPending)
                    تم إرسال طلب فتح البث الحي إلى الإدارة والمعلم. سيتم إدخالك إلى الفصل التفاعلي الحي تلقائياً بمجرد الاعتماد!
                @else
                    لم يقم المعلم أو الإدارة بفتح البث الحي لهذه الجلسة بعد. يرجى الانتظار أو النقر على الزر لإشعار الإدارة بفتح البث.
                @endif
            </p>
        </div>

        <!-- بطاقة معلومات المجموعة أو الحصة -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 text-right space-y-3 shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <span class="text-xs text-slate-400 font-bold">المجموعة / الدورة:</span>
                <span class="text-xs font-black text-white">{{ $title ?? ($group->name ?? 'جلسة تدريبية') }}</span>
            </div>

            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <span class="text-xs text-slate-400 font-bold">الأستاذ المسؤول:</span>
                <span class="text-xs font-black text-indigo-400">👨‍🏫 {{ $teacher?->name ?? 'لم يُحدد بعد' }}</span>
            </div>

            @if(isset($group) && $group)
                @if($group->schedule_days || $group->schedule_time)
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <span class="text-xs text-slate-400 font-bold">الموعد المحدد:</span>
                        <span class="text-xs font-bold text-slate-300">{{ $group->schedule_days }} {{ $group->schedule_time ? "($group->schedule_time)" : '' }}</span>
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-400 font-bold">حالة البث الحي:</span>
                    <span id="badgeLiveStatus" class="text-[11px] font-bold px-3 py-1 rounded-full {{ $isPending ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                        {{ $isPending ? '⏳ بانتظار موافقة الإدارة' : '🔴 مغلق بانتظار الإطلاق' }}
                    </span>
                </div>
            @endif
        </div>

        <!-- زر إرسال طلب فتح البث الحي للإدارة والمشرفين -->
        @if(isset($group) && $group)
            <div id="requestActionContainer" class="pt-2">
                @if($isPending)
                    <div id="requestPendingNotice" class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-bold space-y-1">
                        <div class="flex items-center justify-center gap-2">
                            <span class="animate-spin text-base">⏳</span>
                            <span>طلبك قيد المراجعة لدى إدارة المنصة والمعلم</span>
                        </div>
                        <span class="text-[10px] text-amber-400/80 block">
                            (أُرسل بواسطة: {{ $group->requester?->name ?? 'طالب بالمجموعة' }} - {{ $group->live_requested_at?->diffForHumans() ?? 'الآن' }})
                        </span>
                    </div>
                @else
                    <button id="btnRequestLive" onclick="sendLiveRequest({{ $group->id }})" class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs sm:text-sm shadow-xl shadow-amber-500/20 transition transform active:scale-95 flex items-center justify-center gap-2">
                        <span class="text-base">📡</span>
                        <span>طلب فتح البث الحي الآن (إشعار الإدارة والمعلم)</span>
                    </button>
                    <div id="requestPendingNotice" class="hidden p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-bold">
                        ⏳ جاري فحص ومراجعة الطلب من الإدارة، سيتم فتح الفصل فوراً...
                    </div>
                @endif
            </div>
        @endif

        <!-- مؤشر الفحص التلقائي الحي (Auto-check) -->
        <div class="flex items-center justify-center gap-2 text-[11px] text-slate-500">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
            <span>يتم الفحص التلقائي كل 3 ثوانٍ لدخول الفصل لحظة بدء البث...</span>
        </div>

    </main>

    <!-- شريط تذييل سفلي -->
    <footer class="max-w-4xl w-full mx-auto text-center py-4 border-t border-slate-800 text-[11px] text-slate-500">
        منصة ASOTS للتعليم التفاعلي • الغرفة التفاعلية الذكية Air Class Live
    </footer>

    <!-- سكريبت الفحص المستمر وإعادة التوجيه التلقائي -->
    <script>
        const roomId = @json($roomId);
        const checkStatusUrl = "{{ route('classroom.status', ':roomId') }}".replace(':roomId', roomId);
        const csrfToken = "{{ csrf_token() }}";
        let isRedirecting = false;

        // دالة إرسال طلب فتح البث للإدارة عبر AJAX
        function sendLiveRequest(groupId) {
            const btn = document.getElementById('btnRequestLive');
            if (!btn) return;
            btn.disabled = true;
            btn.innerHTML = `<span class="animate-spin text-base">⏳</span> <span>جاري إرسال الطلب للإدارة...</span>`;

            fetch(`/groups/${groupId}/live/request`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.classList.add('hidden');
                    const notice = document.getElementById('requestPendingNotice');
                    if (notice) notice.classList.remove('hidden');

                    document.getElementById('waitingTitle').innerText = 'طلب فتح البث بانتظار اعتماد الإدارة';
                    document.getElementById('waitingDesc').innerText = 'تم إرسال طلب فتح البث الحي إلى الإدارة والمعلم. سيتم إدخالك إلى الفصل التفاعلي الحي تلقائياً بمجرد الاعتماد!';
                    
                    const badge = document.getElementById('badgeLiveStatus');
                    if (badge) {
                        badge.className = 'text-[11px] font-bold px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30';
                        badge.innerText = '⏳ بانتظار موافقة الإدارة';
                    }

                    const radarCenter = document.getElementById('radarCenter');
                    if (radarCenter) {
                        radarCenter.className = 'relative z-10 w-20 h-20 rounded-full bg-gradient-to-tr from-amber-600 to-amber-400 flex items-center justify-center text-3xl shadow-2xl transition-all duration-500';
                        document.getElementById('radarIcon').innerText = '⏳';
                    }
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = `<span class="text-base">📡</span> <span>إعادة محاولة إرسال الطلب</span>`;
            });
        }

        // دالة الفحص الدوري كل 3 ثوانٍ
        function checkLiveStatus() {
            if (isRedirecting) return;

            fetch(checkStatusUrl)
                .then(res => res.json())
                .then(data => {
                    if (data.is_live) {
                        isRedirecting = true;
                        
                        // تحديث الواجهة لحالة البدء الأخضر
                        document.getElementById('waitingTitle').innerText = '🎉 تم فتح البث الحي الآن!';
                        document.getElementById('waitingDesc').innerText = 'الأستاذ والإدارة أطلقوا البث الحي، جاري نقلك إلى الفصل فوراً...';
                        
                        const radarCenter = document.getElementById('radarCenter');
                        if (radarCenter) {
                            radarCenter.className = 'relative z-10 w-20 h-20 rounded-full bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-3xl shadow-2xl transition-all duration-500 animate-bounce';
                            document.getElementById('radarIcon').innerText = '✅';
                        }

                        const badge = document.getElementById('badgeLiveStatus');
                        if (badge) {
                            badge.className = 'text-[11px] font-bold px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
                            badge.innerText = '🟢 مباشر الآن';
                        }

                        // إعادة تحميل الصفحة للدخول التلقائي
                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    } else if (data.live_status === 'pending_approval') {
                        const badge = document.getElementById('badgeLiveStatus');
                        if (badge && !badge.innerText.includes('موافقة')) {
                            badge.className = 'text-[11px] font-bold px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30';
                            badge.innerText = '⏳ بانتظار موافقة الإدارة';
                        }
                    }
                })
                .catch(err => {
                    console.log('Waiting room poll...', err);
                });
        }

        // تشغيل الفحص الدوري التلقائي
        setInterval(checkLiveStatus, 3000);
    </script>
</body>
</html>
