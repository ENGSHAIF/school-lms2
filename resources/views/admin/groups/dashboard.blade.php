@extends('layouts.app', ['title' => 'لوحة تحكم المجموعة وبطاقات الطلاب - ' . $group->name])

@section('content')
<div class="space-y-6">

    <!-- ترويسة لوحة التحكم وأزرار الوصول السريع -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-800 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-2">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 flex items-center gap-1.5">
                    <span>🎛️</span>
                    <span>لوحة تحكم المجموعة المباشرة • Group Control Panel</span>
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-bold font-mono {{ $group->is_live ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 animate-pulse' : 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                    {{ $group->is_live ? '● البث الحي مفتوح الآن' : '🔴 البث الحي مغلق' }}
                </span>
                <span class="text-xs text-amber-400 font-mono font-bold bg-amber-500/10 px-2.5 py-0.5 rounded-full border border-amber-500/20">
                    غرفة: #{{ $group->room_id }}
                </span>
            </div>
            
            <h1 class="text-2xl sm:text-3xl font-black text-white flex items-center gap-2">
                <span>{{ $group->name }}</span>
            </h1>
            
            <p class="text-xs sm:text-sm text-slate-300 mt-1.5 flex items-center gap-3 flex-wrap">
                <span>📚 المسار: <strong class="text-white">{{ $group->course?->name }} - {{ $group->level?->name }}</strong></span>
                <span>👨‍🏫 الأستاذ: <strong class="text-indigo-300">{{ $group->teacher?->name ?: 'غير محدد' }}</strong></span>
                <span>🛡️ المشرف: <strong class="text-purple-300">{{ $group->supervisor?->name ?: 'إدارة المنصة' }}</strong></span>
                <span>⏰ المواعيد: <strong class="text-amber-300 font-mono">{{ $group->schedule_days ?: 'مرن' }}</strong></span>
            </p>
        </div>

        <!-- أزرار الإجراءات الرئيسية -->
        <div class="flex items-center gap-2.5 flex-wrap w-full lg:w-auto">
            <!-- زر الدخول المباشر للبث الحي -->
            <a href="{{ route('classroom.show', $group->room_id) }}" target="_blank" class="flex-1 sm:flex-initial px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-slate-950 font-black text-xs shadow-lg transition flex items-center justify-center gap-2">
                <span class="text-base">📡</span>
                <span>الدخول للفصل التفاعلي الحي</span>
            </a>

            <!-- زر رصد تقييم وحضور الحصة -->
            <a href="{{ route('admin.groups.evaluations.create', $group->id) }}" class="flex-1 sm:flex-initial px-4 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5 border border-indigo-400/30">
                <span>➕</span>
                <span>رصد تقييم الحصة</span>
            </a>

            <!-- زر سجل التقييمات -->
            <a href="{{ route('admin.groups.evaluations.index', ['group_id' => $group->id]) }}" class="px-4 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs transition border border-slate-700 flex items-center justify-center gap-1.5">
                <span>📊</span>
                <span>سجل التقييمات</span>
            </a>

            <a href="{{ route('admin.groups.index') }}" class="px-3.5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs transition border border-slate-700" title="رجوع لقائمة المجموعات">
                ✕
            </a>
        </div>
    </div>

    <!-- شريط إحصائيات الحضور والاتصال اللحظي (KPI Stats) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 text-center">
        <!-- إجمالي الطلاب -->
        <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-slate-400 block">إجمالي طلاب المجموعة</span>
            <div class="flex items-center justify-center gap-1.5">
                <span class="text-2xl font-black text-slate-900 font-mono">{{ $totalStudents }}</span>
                <span class="text-xs text-slate-400">طالب</span>
            </div>
            <span class="text-[10px] text-indigo-600 font-bold block">سعة المجموعة: {{ $group->max_students }}</span>
        </div>

        <!-- المتصلون في البث الحي الآن -->
        <div class="bg-emerald-50 p-4 sm:p-5 rounded-3xl border border-emerald-200 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-emerald-800 block flex items-center justify-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span>متصلون بالبث الحي الآن 🟢</span>
            </span>
            <div class="flex items-center justify-center gap-1.5">
                <span id="kpiOnlineCount" class="text-2xl font-black text-emerald-700 font-mono">{{ $onlineCount }}</span>
                <span class="text-xs text-emerald-600 font-bold">حاضر الآن</span>
            </div>
            <span class="text-[10px] text-emerald-700 font-mono block">تحديث فوري تلقائي</span>
        </div>

        <!-- الغائبون عن البث -->
        <div class="bg-rose-50 p-4 sm:p-5 rounded-3xl border border-rose-200 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-rose-800 block">غائبون عن البث الحي 🔴</span>
            <div class="flex items-center justify-center gap-1.5">
                <span id="kpiOfflineCount" class="text-2xl font-black text-rose-700 font-mono">{{ $offlineCount }}</span>
                <span class="text-xs text-rose-600 font-bold">غير متصل</span>
            </div>
            <span class="text-[10px] text-rose-600 font-bold block">يمكن تذكيرهم بالواتساب</span>
        </div>

        <!-- رافعو الأيدي لطلب الكلمة -->
        <div class="bg-amber-50 p-4 sm:p-5 rounded-3xl border border-amber-200 shadow-2xs space-y-1">
            <span class="text-[11px] font-bold text-amber-900 block flex items-center justify-center gap-1">
                <span>✋</span>
                <span>يطلبون الكلمة الآن</span>
            </span>
            <div class="flex items-center justify-center gap-1.5">
                <span id="kpiHandsCount" class="text-2xl font-black text-amber-700 font-mono">{{ $handsRaisedCount }}</span>
                <span class="text-xs text-amber-800 font-bold">طلب معلق</span>
            </div>
            <span class="text-[10px] text-amber-700 font-bold block">رفع اليد في البث المباشر</span>
        </div>
    </div>

    <!-- شريط أدوات تصفية وبحث بطاقات الطلاب (Visual Filter Bar) -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-2xs flex flex-col md:flex-row justify-between items-center gap-3">
        <div class="flex items-center gap-2 w-full md:w-auto">
            <span class="text-xs font-black text-slate-800 flex items-center gap-1.5 shrink-0">
                <span>👥</span>
                <span>بطاقات ومربعات أعضاء المجموعة:</span>
            </span>
            <span class="text-xs text-slate-400 font-mono">({{ $totalStudents }} طلاب)</span>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <!-- حقل البحث السريع بالاسم أو الرقم الأكاديمي -->
            <div class="relative flex-1 md:w-64">
                <input type="text" id="studentCardSearch" onkeyup="filterStudentCards()" placeholder="🔍 ابحث بالاسم أو الرقم الأكاديمي..."
                    class="w-full pl-8 pr-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <!-- أزرار الفلترة السريعة (الكل / متصل / غائب) -->
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs font-bold shrink-0">
                <button type="button" onclick="setCardFilter('all')" id="btnFilterAll" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white shadow-xs transition">الكل</button>
                <button type="button" onclick="setCardFilter('online')" id="btnFilterOnline" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition">🟢 متصل</button>
                <button type="button" onclick="setCardFilter('offline')" id="btnFilterOffline" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition">🔴 غائب</button>
            </div>
        </div>
    </div>

    <!-- شبكة بطاقات ومربعات الطلاب التفاعلية (Visual Student Cards Grid) -->
    @if($studentCards->isEmpty())
        <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center space-y-3">
            <span class="text-4xl block">👥</span>
            <h3 class="text-base font-black text-slate-800">لا يوجد طلاب مسكنين في هذه المجموعة حالياً</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                يمكنك تسكين الطلاب في المجموعة عبر شاشة إدارة المجموعات لتتمكن من متابعتهم والتحكم بحضورهم هنا.
            </p>
            <a href="{{ route('admin.groups.index') }}" class="inline-block px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-bold">
                تسكين طلاب في المجموعة
            </a>
        </div>
    @else
        <div id="studentCardsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
            @foreach($studentCards as $student)
                <div id="card_student_{{ $student->id }}" 
                     data-name="{{ mb_strtolower($student->name . ' ' . ($student->name_en ?? '') . ' ' . ($student->student_code ?? '') . ' ' . ($student->academic_number ?? '')) }}"
                     data-online="{{ $student->is_online_live ? '1' : '0' }}"
                     data-hand="{{ $student->is_hand_raised ? '1' : '0' }}"
                     class="student-card bg-white rounded-3xl border {{ $student->is_online_live ? 'border-emerald-300 shadow-md ring-1 ring-emerald-400/30' : 'border-slate-200 shadow-2xs hover:border-slate-300' }} p-5 flex flex-col justify-between transition-all duration-200 space-y-4 relative overflow-hidden">
                    
                    <!-- شريط الحالة في أعلى الكرت -->
                    <div class="flex justify-between items-start gap-2">
                        <!-- مؤشر الاتصال بالبث الحي -->
                        <span id="badge_status_{{ $student->id }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $student->is_online_live ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            <span class="w-2 h-2 rounded-full {{ $student->is_online_live ? 'bg-emerald-500 animate-ping' : 'bg-slate-400' }}"></span>
                            <span id="txt_status_{{ $student->id }}">{{ $student->is_online_live ? 'متصل بالبث 🟢' : 'غائب عن البث 🔴' }}</span>
                        </span>

                        <!-- شارات المايك والكاميرا واليد -->
                        <div class="flex items-center gap-1 text-xs">
                            <span id="badge_hand_{{ $student->id }}" class="{{ $student->is_hand_raised ? '' : 'hidden' }} px-1.5 py-0.5 rounded-lg bg-amber-100 text-amber-800 font-black animate-bounce text-[10px]" title="يطلب الكلمة في البث">
                                ✋
                            </span>
                            <span id="badge_mic_{{ $student->id }}" class="px-1.5 py-0.5 rounded-lg {{ $student->has_mic && !$student->is_muted_by_host ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}" title="{{ $student->has_mic && !$student->is_muted_by_host ? 'المايك يعمل' : 'المايك مكتوم' }}">
                                {{ $student->has_mic && !$student->is_muted_by_host ? '🎙️' : '🔇' }}
                            </span>
                            <span id="badge_cam_{{ $student->id }}" class="px-1.5 py-0.5 rounded-lg {{ $student->has_camera ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-500' }}" title="{{ $student->has_camera ? 'الكاميرا تعمل' : 'الكاميرا مغلقة' }}">
                                {{ $student->has_camera ? '📷' : '⬛' }}
                            </span>
                        </div>
                    </div>

                    <!-- الملف الشخصي والمعلومات الأكاديمية للطالب -->
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-2xl {{ $student->gender === 'female' ? 'bg-pink-100 text-pink-700' : 'bg-indigo-100 text-indigo-700' }} flex items-center justify-center text-xl font-black shrink-0 shadow-inner">
                            {{ $student->gender === 'female' ? '👩' : '👨' }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <h4 class="font-black text-sm text-slate-900 truncate leading-snug">{{ $student->name }}</h4>
                            @if($student->name_en)
                                <span class="text-[10px] text-slate-400 font-medium block truncate" dir="ltr">{{ $student->name_en }}</span>
                            @endif

                            <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                <span class="font-mono text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-200">
                                    {{ $student->student_code }}
                                </span>
                                @if($student->academic_number)
                                    <span class="font-mono text-[10px] font-black text-amber-800 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200" title="الرقم الأكاديمي اليدوي">
                                        🎓 {{ $student->academic_number }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- رصيد النجوم ومكافآت التفاعل السريعة (+1 / -1) -->
                    <div class="bg-amber-50/70 border border-amber-200/80 p-3 rounded-2xl flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <span class="text-base">⭐</span>
                            <span class="text-xs font-bold text-amber-950">رصيد النجوم:</span>
                            <span id="stars_val_{{ $student->id }}" class="font-mono font-black text-sm text-amber-600">{{ $student->stars_balance }}</span>
                        </div>

                        <!-- أزرار زيادة وخصم النجوم الفورية -->
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="updateStarsAjax({{ $group->id }}, {{ $student->id }}, 'add', 1)" class="w-7 h-7 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs flex items-center justify-center transition shadow-2xs" title="منح نجمة للطالب">
                                +1
                            </button>
                            <button type="button" onclick="updateStarsAjax({{ $group->id }}, {{ $student->id }}, 'deduct', 1)" class="w-7 h-7 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 font-bold text-xs flex items-center justify-center transition" title="خصم نجمة">
                                -1
                            </button>
                        </div>
                    </div>

                    <!-- ملخص الحضور الأكاديمي والتقييم الأخير -->
                    <div class="grid grid-cols-2 gap-2 text-center text-xs pt-1">
                        <div class="bg-slate-50 p-2 rounded-xl border border-slate-100">
                            <span class="text-[10px] text-slate-400 block font-bold">نسبة الحضور</span>
                            <span class="font-mono font-black text-slate-800 text-xs">{{ $student->attendance_rate }}%</span>
                        </div>
                        <div class="bg-slate-50 p-2 rounded-xl border border-slate-100">
                            <span class="text-[10px] text-slate-400 block font-bold">آخر تقييم</span>
                            @if($student->last_eval)
                                <span class="font-mono font-bold text-indigo-700 text-xs">{{ $student->last_eval->daily_total_score }}%</span>
                            @else
                                <span class="text-[10px] text-slate-400">لا يوجد بعد</span>
                            @endif
                        </div>
                    </div>

                    <!-- أزرار التحكم السريع بالطالب من بطاقة المربع -->
                    <div class="pt-2 border-t border-slate-100 flex items-center gap-1.5">
                        <!-- زر كتم / فك كتم الصوت في البث المباشر -->
                        <button type="button" id="btn_mute_{{ $student->id }}" onclick="toggleMuteAjax({{ $group->id }}, {{ $student->id }})" 
                            class="flex-1 py-2 px-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 {{ $student->is_muted_by_host ? 'bg-rose-100 hover:bg-rose-200 text-rose-800 border border-rose-300' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200' }}">
                            <span id="icon_mute_{{ $student->id }}">{{ $student->is_muted_by_host ? '🔇' : '🎙️' }}</span>
                            <span id="txt_mute_{{ $student->id }}">{{ $student->is_muted_by_host ? 'فك الكتم' : 'كتم المايك' }}</span>
                        </button>

                        <!-- زر مراسلة فورية عبر الواتساب -->
                        @if($student->phone_whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->phone_whatsapp) }}?text={{ urlencode('مرحباً ' . $student->name . '، بخصوص حضورك وتفاعلك في مجموعة ' . $group->name . ' في منصة ASOTS...') }}" target="_blank"
                                class="py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition flex items-center justify-center gap-1 shadow-2xs" title="مراسلة الطالب عبر الواتساب">
                                <span>💬</span>
                                <span class="hidden sm:inline">واتساب</span>
                            </a>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @endif

    <!-- شريط آخر رسائل ومشاركات المحادثة الحية في الغرفة -->
    @if($recentMessages->isNotEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-2xs space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <span class="text-xs font-black text-slate-900 flex items-center gap-2">
                    <span>💬</span>
                    <span>شريط آخر رسائل ومشاركات المحادثة في البث الحي:</span>
                </span>
                <a href="{{ route('classroom.show', $group->room_id) }}" target="_blank" class="text-[11px] text-indigo-600 font-bold hover:underline">
                    فتح المحادثة الكاملة ↗️
                </a>
            </div>

            <div class="flex items-center gap-3 overflow-x-auto py-1 scrollbar-thin">
                @foreach($recentMessages as $msg)
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 shrink-0 max-w-xs space-y-1 text-xs">
                        <div class="flex justify-between items-center gap-2 text-[10px]">
                            <strong class="text-slate-900 truncate">{{ $msg->user?->name ?? 'مشارك' }}</strong>
                            <span class="text-slate-400 font-mono">{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                        <p class="text-slate-600 text-[11px] truncate">{{ $msg->content }}</p>
                        @if($msg->type === 'audio')
                            <span class="text-[10px] text-purple-700 font-bold block">🎙️ تسجيل صوتي</span>
                        @elseif($msg->type === 'image')
                            <span class="text-[10px] text-emerald-700 font-bold block">📸 ورقة إجابة</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

<!-- نظام التحديث اللحظي لبطاقات الطلاب كل 4 ثوانٍ (Auto Polling) -->
<script>
const GROUP_ROOM_ID = "{{ $group->room_id }}";
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// 1. تحديث النجوم عبر AJAX
async function updateStarsAjax(groupId, studentId, action, amount) {
    try {
        const res = await fetch(`/admin/groups/${groupId}/students/${studentId}/stars`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ action: action, amount: amount })
        });
        const data = await res.json();
        if (data.success) {
            const el = document.getElementById('stars_val_' + studentId);
            if (el) el.textContent = data.new_balance;
        }
    } catch (e) {
        console.error(e);
    }
}

// 2. كتم / فك كتم صوت الطالب في البث الحي عبر AJAX
async function toggleMuteAjax(groupId, studentId) {
    try {
        const res = await fetch(`/admin/groups/${groupId}/students/${studentId}/toggle-mute`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();
        if (data.success) {
            const btn = document.getElementById('btn_mute_' + studentId);
            const icon = document.getElementById('icon_mute_' + studentId);
            const txt = document.getElementById('txt_mute_' + studentId);
            const badgeMic = document.getElementById('badge_mic_' + studentId);

            if (data.is_muted) {
                btn.className = "flex-1 py-2 px-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 bg-rose-100 hover:bg-rose-200 text-rose-800 border border-rose-300";
                icon.textContent = "🔇";
                txt.textContent = "فك الكتم";
                if (badgeMic) {
                    badgeMic.className = "px-1.5 py-0.5 rounded-lg bg-rose-50 text-rose-700";
                    badgeMic.textContent = "🔇";
                }
            } else {
                btn.className = "flex-1 py-2 px-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200";
                icon.textContent = "🎙️";
                txt.textContent = "كتم المايك";
                if (badgeMic) {
                    badgeMic.className = "px-1.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-700";
                    badgeMic.textContent = "🎙️";
                }
            }
        }
    } catch (e) {
        console.error(e);
    }
}

// 3. فلترة وبحث بطاقات ومربعات الطلاب
let currentFilterMode = 'all';

function setCardFilter(mode) {
    currentFilterMode = mode;
    ['btnFilterAll', 'btnFilterOnline', 'btnFilterOffline'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) btn.className = "px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 transition";
    });

    if (mode === 'all') {
        document.getElementById('btnFilterAll').className = "px-3 py-1.5 rounded-lg bg-indigo-600 text-white shadow-xs transition";
    } else if (mode === 'online') {
        document.getElementById('btnFilterOnline').className = "px-3 py-1.5 rounded-lg bg-emerald-600 text-white shadow-xs transition";
    } else if (mode === 'offline') {
        document.getElementById('btnFilterOffline').className = "px-3 py-1.5 rounded-lg bg-rose-600 text-white shadow-xs transition";
    }

    filterStudentCards();
}

function filterStudentCards() {
    const searchVal = document.getElementById('studentCardSearch').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.student-card');

    cards.forEach(card => {
        const nameData = card.getAttribute('data-name') || '';
        const isOnline = card.getAttribute('data-online') === '1';

        const matchesSearch = nameData.includes(searchVal);
        let matchesFilter = true;

        if (currentFilterMode === 'online') matchesFilter = isOnline;
        if (currentFilterMode === 'offline') matchesFilter = !isOnline;

        if (matchesSearch && matchesFilter) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}

// 4. استطلاع نبضات الحضور في البث الحي وتحديث الكروت تلقائياً كل 4 ثوانٍ
async function pollLiveAttendance() {
    try {
        const res = await fetch(`/classroom/${GROUP_ROOM_ID}/sync?peer_id=admin_dashboard`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!res.ok) return;
        const data = await res.json();
        if (!data.success) return;

        const onlineUserIds = (data.participants || []).map(p => p.user_id);
        const handRaiserUserIds = (data.participants || []).filter(p => p.is_hand_raised).map(p => p.user_id);

        let onlineCount = 0;
        let offlineCount = 0;

        document.querySelectorAll('.student-card').forEach(card => {
            const id = parseInt(card.id.replace('card_student_', ''));
            const isOnline = onlineUserIds.includes(id);
            const isHand = handRaiserUserIds.includes(id);

            card.setAttribute('data-online', isOnline ? '1' : '0');
            card.setAttribute('data-hand', isHand ? '1' : '0');

            const badgeStatus = document.getElementById('badge_status_' + id);
            const txtStatus = document.getElementById('txt_status_' + id);
            const badgeHand = document.getElementById('badge_hand_' + id);

            if (isOnline) {
                onlineCount++;
                card.className = card.className.replace('border-slate-200', 'border-emerald-300 ring-1 ring-emerald-400/30');
                if (badgeStatus) {
                    badgeStatus.className = "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300";
                    txtStatus.textContent = "متصل بالبث 🟢";
                }
            } else {
                offlineCount++;
                card.className = card.className.replace('border-emerald-300 ring-1 ring-emerald-400/30', 'border-slate-200');
                if (badgeStatus) {
                    badgeStatus.className = "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200";
                    txtStatus.textContent = "غائب عن البث 🔴";
                }
            }

            if (badgeHand) {
                if (isHand) {
                    badgeHand.classList.remove('hidden');
                } else {
                    badgeHand.classList.add('hidden');
                }
            }
        });

        // تحديث أرقام الـ KPI في الشريط العلوي
        document.getElementById('kpiOnlineCount').textContent = onlineCount;
        document.getElementById('kpiOfflineCount').textContent = offlineCount;
        document.getElementById('kpiHandsCount').textContent = handRaiserUserIds.length;

    } catch (err) {
        console.error('Polling error:', err);
    }
}

setInterval(pollLiveAttendance, 4000);
</script>
@endsection
